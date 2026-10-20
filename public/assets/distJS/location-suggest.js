/**
 * Автодополнение поля «Где объект» (город / район).
 *
 * Работает в два слоя, чтобы отклик был мгновенным:
 *   1) локальный словарь (hot-cities.js) — подсказки появляются сразу, без сети;
 *   2) сервер /api/suggest/location (DaData → Open-Meteo → Nominatim) —
 *      полный список, приходит через ~0.3–0.5 с и заменяет локальный.
 *
 * Токен DaData остаётся на сервере и не утекает в браузер.
 *
 * Разметка-контракт:
 *   <div class="form-field form-field--location" data-location-suggest>
 *     <input id="field-location" name="location" autocomplete="off">
 *     <div class="location-suggestions" data-location-list role="listbox"></div>
 *   </div>
 */
(function () {
	'use strict';

	var ENDPOINT = '/api/suggest/location';
	var MIN_CHARS = 2;
	var DEBOUNCE_MS = 180;

	function escapeHtml(str) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str == null ? '' : String(str)));
		return div.innerHTML;
	}

	/**
	 * Локальный мгновенный подбор по словарю городов.
	 *
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

		// 1) вхождение всего запроса
		for (var i = 0; i < dict.length && out.length < 8; i++) {
			var name = dict[i][0];
			if (name.toLowerCase().indexOf(q) !== -1) {
				add(name, dict[i][1] || '');
			}
		}

		// 2) начало запроса (первое слово) — для адресных запросов
		if (head.length >= 3 && head !== q) {
			for (var j = 0; j < dict.length && out.length < 8; j++) {
				var name2 = dict[j][0];
				if (name2.toLowerCase().indexOf(head) === 0) {
					add(name2, dict[j][1] || '');
				}
			}
		}

		return out;
	}

	function initField(field) {
		var input = field.querySelector('input[name="location"], input[name="city"], input[type="text"]');
		var box = field.querySelector('[data-location-list]');
		if (!input || !box) return;

		var debounceTimer = null;
		var activeIndex = -1;
		var suggestions = [];
		var requestId = 0;
		var controller = null;

		function close() {
			box.hidden = true;
			box.innerHTML = '';
			suggestions = [];
			activeIndex = -1;
			input.setAttribute('aria-expanded', 'false');
			input.removeAttribute('aria-activedescendant');
		}

		function highlight() {
			var items = box.querySelectorAll('.location-suggestion');
			for (var i = 0; i < items.length; i++) {
				var isActive = i === activeIndex;
				items[i].classList.toggle('is-active', isActive);
				items[i].setAttribute('aria-selected', isActive ? 'true' : 'false');
			}
			if (activeIndex >= 0 && items[activeIndex]) {
				input.setAttribute('aria-activedescendant', items[activeIndex].id);
			}
		}

		function select(item) {
			input.value = item.value || item.label || '';
			input.dispatchEvent(new Event('input', { bubbles: true }));
			input.dispatchEvent(new Event('change', { bubbles: true }));
			close();
		}

		function showLoading() {
			box.innerHTML = '<div class="location-suggestion__loading">Ищем…</div>';
			box.hidden = false;
			input.setAttribute('aria-expanded', 'true');
		}

		function render() {
			if (!suggestions.length) {
				close();
				return;
			}

			var html = '';
			for (var i = 0; i < suggestions.length; i++) {
				var s = suggestions[i];
				html += '<div class="location-suggestion" id="location-suggestion-' + i + '" role="option" aria-selected="false" data-index="' + i + '">'
					+ '<span class="location-suggestion__label">' + escapeHtml(s.label) + '</span>'
					+ (s.hint ? '<span class="location-suggestion__hint">' + escapeHtml(s.hint) + '</span>' : '')
					+ '</div>';
			}
			box.innerHTML = html;
			box.hidden = false;
			input.setAttribute('aria-expanded', 'true');

			var items = box.querySelectorAll('.location-suggestion');
			for (var j = 0; j < items.length; j++) {
				items[j].addEventListener('mousedown', (function (e) {
					e.preventDefault();
					select(suggestions[parseInt(e.currentTarget.getAttribute('data-index'), 10)]);
				}));
			}
		}

		function fetchSuggestions(query) {
			var current = ++requestId;

			// Предыдущий запрос отменяем — иначе короткий запрос перебьёт более точный
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
				body: JSON.stringify({ query: query }),
				signal: controller ? controller.signal : undefined
			}).then(function (resp) {
				if (!resp.ok) return null;
				return resp.json();
			}).then(function (data) {
				if (!data || current !== requestId) return;
				var list = Array.isArray(data.suggestions) ? data.suggestions : [];
				// Пустой ответ не должен стирать локальные подсказки
				if (!list.length) {
					if (!suggestions.length) {
						box.innerHTML = '<div class="location-suggestion__loading">Ничего не найдено — уточните запрос</div>';
						box.hidden = false;
						input.setAttribute('aria-expanded', 'true');
					}
					return;
				}
				suggestions = list;
				activeIndex = -1;
				render();
			}).catch(function () {
				if (current === requestId) {
					// Сеть недоступна — оставляем локальные подсказки
					if (!suggestions.length) close();
				}
			});
		}

		input.addEventListener('input', function () {
			clearTimeout(debounceTimer);
			var query = input.value.trim();
			if (query.length < MIN_CHARS) {
				close();
				return;
			}

			// Мгновенный отклик по локальному словарю
			var local = localMatches(query);
			if (local.length) {
				suggestions = local;
				activeIndex = -1;
				render();
			}

			if (!suggestions.length) showLoading();

			debounceTimer = setTimeout(function () { fetchSuggestions(query); }, DEBOUNCE_MS);
		});

		input.addEventListener('keydown', function (e) {
			if (!suggestions.length) {
				if (e.key === 'Escape') close();
				return;
			}
			if (e.key === 'ArrowDown') {
				e.preventDefault();
				activeIndex = Math.min(activeIndex + 1, suggestions.length - 1);
				highlight();
			} else if (e.key === 'ArrowUp') {
				e.preventDefault();
				activeIndex = Math.max(activeIndex - 1, 0);
				highlight();
			} else if (e.key === 'Enter' && activeIndex >= 0) {
				e.preventDefault();
				select(suggestions[activeIndex]);
			} else if (e.key === 'Escape') {
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

	function init() {
		var fields = document.querySelectorAll('[data-location-suggest]');
		for (var i = 0; i < fields.length; i++) {
			initField(fields[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
