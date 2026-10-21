/**
 * Автодополнение поля «Где объект» (город / район / адрес).
 *
 * Своя вёрстка выпадающего списка в стиле страницы (ProMaintenance:
 * тёмное «стекло», тонкие линии, акцент #c7ab94, радиус 8/6).
 * Стандартный UI DaData не используется — токен остаётся на сервере
 * и в браузер не попадает.
 *
 * Два слоя отклика:
 *   1) локальный словарь (hot-cities.js) — подсказки мгновенно, без сети;
 *   2) сервер POST /api/suggest/location (DaData → Open-Meteo → Nominatim) —
 *      полный список, заменяет локальный.
 *
 * Разметка-контракт:
 *   <div class="form-field form-field--location" data-location-suggest>
 *     <input id="field-location" name="location" autocomplete="off"
 *            role="combobox" aria-expanded="false" aria-controls="location-suggestions">
 *     <div class="location-suggestions" id="location-suggestions" role="listbox" hidden></div>
 *   </div>
 */
(function () {
	'use strict';

	var ENDPOINT = '/api/suggest/location';
	var MIN_CHARS = 2;
	var DEBOUNCE_MS = 200;
	var MAX_ITEMS = 8;

	var ICON_PIN = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
		'stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' +
		'<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"></path>' +
		'<circle cx="12" cy="10" r="2.5"></circle></svg>';

	function escapeHtml(str) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str == null ? '' : String(str)));
		return div.innerHTML;
	}

	/** Подсветка совпадения запроса внутри названия. */
	function highlight(text, query) {
		var safe = escapeHtml(text);
		var q = (query || '').trim().toLowerCase();
		if (q.length < 2) return safe;

		var lower = safe.toLowerCase();
		var pos = lower.indexOf(q);
		if (pos === -1) return safe;

		return safe.slice(0, pos) + '<em>' + safe.slice(pos, pos + q.length) + '</em>' + safe.slice(pos + q.length);
	}

	/**
	 * Локальный мгновенный подбор по словарю городов.
	 * Учитываем и начало запроса: для адреса «москва большая черемушкинская 19»
	 * сразу предлагаем город «Москва», пока сервер ищет улицу.
	 */
	function localMatches(query) {
		var dict = window.FSS_HOT_CITIES || [];
		if (!dict.length) return [];

		var q = query.trim().toLowerCase();
		if (!q) return [];

		var head = q.split(/[\s,]+/)[0] || q;
		var out = [];
		var seen = {};

		var add = function (name, region) {
			var key = name.toLowerCase();
			if (seen[key]) return;
			seen[key] = true;
			out.push({
				value: region && region !== name ? name + ', ' + region : name,
				label: name,
				hint: region && region !== name ? region : ''
			});
		};

		for (var i = 0; i < dict.length && out.length < MAX_ITEMS; i++) {
			var name = dict[i][0];
			if (name.toLowerCase().indexOf(q) !== -1) add(name, dict[i][1] || '');
		}

		if (head.length >= 3 && head !== q) {
			for (var j = 0; j < dict.length && out.length < MAX_ITEMS; j++) {
				var name2 = dict[j][0];
				if (name2.toLowerCase().indexOf(head) === 0) add(name2, dict[j][1] || '');
			}
		}

		return out;
	}

	function initField(field) {
		if (field.getAttribute('data-location-ready') === 'on') return;
		field.setAttribute('data-location-ready', 'on');

		var input = field.querySelector('input[name="location"], input[name="city"], input[type="text"]');
		var box = field.querySelector('[data-location-list], #location-suggestions, .location-suggestions');
		if (!input || !box) return;

		var debounceTimer = null;
		var activeIndex = -1;
		var suggestions = [];
		var query = '';
		var requestId = 0;
		var controller = null;

		input.setAttribute('role', 'combobox');
		input.setAttribute('aria-expanded', 'false');
		input.setAttribute('aria-autocomplete', 'list');
		if (!input.getAttribute('aria-controls')) {
			if (!box.id) box.id = 'location-suggestions';
			input.setAttribute('aria-controls', box.id);
		}
		box.setAttribute('role', 'listbox');
		if (!box.getAttribute('aria-label')) box.setAttribute('aria-label', 'Подсказки по адресу');

		function close() {
			// Сбрасываем отложенный запрос и инвалидируем «в полёте»:
			// иначе ответ сервера успевает отрисовать список уже после выбора/Esc.
			clearTimeout(debounceTimer);
			requestId++;
			box.hidden = true;
			box.innerHTML = '';
			suggestions = [];
			activeIndex = -1;
			input.setAttribute('aria-expanded', 'false');
			input.removeAttribute('aria-activedescendant');
		}

		function highlightActive() {
			var items = box.querySelectorAll('.location-suggestion');
			for (var i = 0; i < items.length; i++) {
				var isActive = i === activeIndex;
				items[i].classList.toggle('is-active', isActive);
				items[i].setAttribute('aria-selected', isActive ? 'true' : 'false');
			}
			if (activeIndex >= 0 && items[activeIndex]) {
				input.setAttribute('aria-activedescendant', items[activeIndex].id);
				if (items[activeIndex].scrollIntoView) {
					items[activeIndex].scrollIntoView({ block: 'nearest' });
				}
			}
		}

		function select(item) {
			if (!item) return;
			input.value = item.value || item.label || '';
			input.dispatchEvent(new Event('input', { bubbles: true }));
			input.dispatchEvent(new Event('change', { bubbles: true }));
			close();
		}

		function setState(text) {
			box.innerHTML = '<div class="location-suggestions__state">' + escapeHtml(text) + '</div>';
			box.hidden = false;
			input.setAttribute('aria-expanded', 'true');
		}

		function render() {
			if (!suggestions.length) {
				close();
				return;
			}

			var html = '<ul class="location-suggestions__list">';
			for (var i = 0; i < suggestions.length; i++) {
				var s = suggestions[i];
				html += '<li class="location-suggestion" id="location-suggestion-' + i + '" role="option" ' +
					'aria-selected="false" data-index="' + i + '">' +
					'<span class="location-suggestion__icon">' + ICON_PIN + '</span>' +
					'<span class="location-suggestion__body">' +
					'<span class="location-suggestion__label">' + highlight(s.label || s.value || '', query) + '</span>' +
					(s.hint ? '<span class="location-suggestion__hint">' + escapeHtml(s.hint) + '</span>' : '') +
					'</span></li>';
			}
			html += '</ul>' +
				'<p class="location-suggestions__foot">↑ ↓ — выбрать · Enter — применить · Esc — закрыть</p>';

			box.innerHTML = html;
			box.hidden = false;
			input.setAttribute('aria-expanded', 'true');
			activeIndex = -1;
			highlightActive();

			var items = box.querySelectorAll('.location-suggestion');
			for (var j = 0; j < items.length; j++) {
				items[j].addEventListener('mousedown', (function (e) {
					e.preventDefault();
					select(suggestions[parseInt(e.currentTarget.getAttribute('data-index'), 10)]);
				}));
				items[j].addEventListener('mouseenter', (function (e) {
					activeIndex = parseInt(e.currentTarget.getAttribute('data-index'), 10);
					highlightActive();
				}));
			}
		}

		function fetchSuggestions(q) {
			var current = ++requestId;

			if (controller) {
				try { controller.abort(); } catch (_) {}
			}
			controller = (typeof AbortController !== 'undefined') ? new AbortController() : null;

			fetch(ENDPOINT, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'Accept': 'application/json'
				},
				body: JSON.stringify({ query: q }),
				signal: controller ? controller.signal : undefined
			}).then(function (resp) {
				if (!resp.ok) return null;
				return resp.json();
			}).then(function (data) {
				if (!data || current !== requestId) return;
				var list = Array.isArray(data.suggestions) ? data.suggestions : [];

				if (!list.length) {
					// Пустой ответ не должен стирать локальные подсказки
					if (!suggestions.length) setState('Ничего не найдено — уточните запрос');
					return;
				}

				suggestions = list.slice(0, MAX_ITEMS);
				render();
			}).catch(function () {
				if (current === requestId && !suggestions.length) close();
			});
		}

		input.addEventListener('input', function () {
			clearTimeout(debounceTimer);
			query = input.value.trim();

			if (query.length < MIN_CHARS) {
				close();
				return;
			}

			var local = localMatches(query);
			if (local.length) {
				suggestions = local;
				render();
			} else {
				setState('Ищем…');
			}

			debounceTimer = setTimeout(function () { fetchSuggestions(query); }, DEBOUNCE_MS);
		});

		input.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				if (!box.hidden) {
					e.preventDefault();
					close();
				}
				return;
			}

			if (!suggestions.length) return;

			if (e.key === 'ArrowDown') {
				e.preventDefault();
				activeIndex = activeIndex + 1 >= suggestions.length ? 0 : activeIndex + 1;
				highlightActive();
			} else if (e.key === 'ArrowUp') {
				e.preventDefault();
				activeIndex = activeIndex <= 0 ? suggestions.length - 1 : activeIndex - 1;
				highlightActive();
			} else if (e.key === 'Home') {
				e.preventDefault();
				activeIndex = 0;
				highlightActive();
			} else if (e.key === 'End') {
				e.preventDefault();
				activeIndex = suggestions.length - 1;
				highlightActive();
			} else if (e.key === 'Enter' && activeIndex >= 0) {
				e.preventDefault();
				select(suggestions[activeIndex]);
			} else if (e.key === 'Tab') {
				close();
			}
		});

		input.addEventListener('blur', function () {
			setTimeout(function () {
				if (!field.contains(document.activeElement)) close();
			}, 120);
		});

		document.addEventListener('click', function (e) {
			if (!field.contains(e.target)) close();
		});
	}

	/** Инициализация всех полей локации на странице. */
	function enable() {
		var fields = document.querySelectorAll('[data-location-suggest]');
		for (var i = 0; i < fields.length; i++) {
			initField(fields[i]);
		}
	}

	window.FSSLocationSuggest = { enable: enable };

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', enable);
	} else {
		enable();
	}
})();
