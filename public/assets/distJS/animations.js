/* ============================================================
   ФСС — система анимаций (GSAP 3.15 + ScrollTrigger + SplitText + Lenis)
   Реализация по образцу https://protenance.lorolabs.ai/
   ============================================================ */
(function () {
	'use strict';

	/* --- Утилиты --- */
	var reduceMotion = function () {
		return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	};

	/* Плавный скролл к элементу (Protenance: offset -105, duration 1.05) */
	var lenisInstance = null;
	var scrollToTarget = function (target, offset) {
		offset = offset === undefined ? -105 : offset;
		if (lenisInstance) {
			lenisInstance.scrollTo(target, { offset: offset, duration: 1.05 });
		} else if (target && target.scrollIntoView) {
			target.scrollIntoView({ behavior: 'auto', block: 'start' });
		}
	};
	window.fssScrollTo = scrollToTarget;

	/* Обновить ScrollTrigger после изменения layout */
	var refreshScroll = function () {
		requestAnimationFrame(function () {
			if (window.lenisInstance) window.lenisInstance.resize();
			if (window.ScrollTrigger) window.ScrollTrigger.refresh();
		});
	};

	/* ============================================================
	   ПОЛОСА ПРОГРЕССА СКРОЛЛА (ProMaintenance site-progress)
	   ============================================================ */
	function initScrollProgress() {
		var bar = document.querySelector('.site-progress');
		if (!bar) return;

		var update = function () {
			var doc = document.documentElement;
			var max = doc.scrollHeight - doc.clientHeight;
			var progress = max > 0 ? (window.scrollY || doc.scrollTop) / max : 0;
			bar.style.setProperty('--scroll-progress', Math.min(1, Math.max(0, progress)));
		};

		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update, { passive: true });
		update();
	}

	/* ============================================================
	   1. LENIS + GSAP TICKER SYNC
	   ============================================================ */
	function initLenis() {
		if (typeof Lenis === 'undefined') return null;

		var lenis = new Lenis({
			duration: 1.12,
			smoothWheel: true,
			syncTouch: false,
			anchors: { offset: -105 },
			prevent: function (element) {
				return element.closest('#nav-panel') !== null;
			}
		});

		document.documentElement.dataset.motionEngine = 'gsap-lenis';
		window.lenisInstance = lenis;

		/* Lenis → GSAP: обновляем ScrollTrigger при скролле */
		lenis.on('scroll', function () {
			if (window.ScrollTrigger) window.ScrollTrigger.update();
		});
		/* GSAP ticker → Lenis raf (единый цикл кадров) */
		if (window.gsap) {
			window.gsap.ticker.add(function (time) {
				lenis.raf(time * 1000);
			});
			window.gsap.ticker.lagSmoothing(0);
		} else {
			var raf = function (time) {
				lenis.raf(time);
				requestAnimationFrame(raf);
			};
			requestAnimationFrame(raf);
		}

		return lenis;
	}

	/* ============================================================
	   2. HERO — появление при загрузке + параллакс
	   ============================================================ */
	function initHero() {
		if (!window.gsap) return;
		var hero = document.querySelector('.hero');
		if (!hero) return;

		var gsap = window.gsap;
		gsap.defaults({ ease: 'power3.out' });

		/* Hero Reveal — scale фона 1.07 → 1, текст снизу вверх */
		gsap.timeline({ defaults: { ease: 'power3.out' } })
			.from('.hero-visual', { scale: 1.07, duration: 2 }, 0)
			.from('.hero-content > .eyebrow', { y: 12, autoAlpha: 0, duration: 0.8 }, 0.12)
			.from('.hero h1 > span', { yPercent: 25, autoAlpha: 0, stagger: 0.12, duration: 1.2 }, 0.25)
			.from('.hero-intro > *', { y: 18, autoAlpha: 0, stagger: 0.08, duration: 0.9 }, 0.6)
			.from('.hero-bottom > *', { y: 20, autoAlpha: 0, duration: 0.9, stagger: 0.08 }, 0.85);

		/* Parallax: фон уходит вниз медленнее контента (yPercent 12) */
		if (window.matchMedia('(min-width: 993px) and (prefers-reduced-motion: no-preference)').matches && window.ScrollTrigger) {
			gsap.to('.hero-visual', {
				yPercent: 12,
				ease: 'none',
				scrollTrigger: {
					trigger: '.hero',
					start: 'top top',
					end: 'bottom top',
					scrub: 0.65
				}
			});
		}
	}

	/* ============================================================
	   3. CINEMA SLIDER — ручное переключение, без автопрокрутки
	   ============================================================ */
	function initCinema() {
		var root = document.querySelector('[data-cinema]') || document.querySelector('.hero');
		if (!root) return;

		var buttons = Array.prototype.slice.call(root.querySelectorAll('[data-cinema-select]'));
		var images = Array.prototype.slice.call(root.querySelectorAll('[data-cinema-image]'));
		if (!buttons.length || !images.length) return;

		var gsap = window.gsap;
		var current = 0;

		var setText = function (selector, value) {
			var el = root.querySelector(selector);
			if (el) el.textContent = value;
		};

		buttons.forEach(function (btn, index) {
			btn.addEventListener('click', function () {
				if (index === current) return;
				current = index;

				/* Кнопки пагинации */
				buttons.forEach(function (b, i) {
					b.setAttribute('aria-pressed', String(i === index));
				});

				/* Фон: crossfade + лёгкий zoom неактивных */
				images.forEach(function (img, i) {
					img.classList.toggle('is-active', i === index);
					if (gsap) {
						gsap.to(img, {
							opacity: i === index ? 1 : 0,
							scale: i === index ? 1 : 1.035,
							duration: reduceMotion() ? 0 : 1.2,
							ease: 'power2.inOut',
							overwrite: true
						});
					}
				});

				/* Текст проекта */
				var num = String(index + 1).padStart(2, '0');
				var name = btn.dataset.name || '';
				setText('[data-cinema-number]', num);
				setText('[data-cinema-name]', name);
				setText('[data-cinema-caption]', btn.dataset.caption || '');
				setText('[data-cinema-status]', num + ' / ' + String(images.length).padStart(2, '0') + ' — ' + name);

				var link = root.querySelector('[data-cinema-link]');
				if (link && btn.dataset.project) {
					link.href = '/portfolio/' + btn.dataset.project;
				}

				/* Плавная смена текста */
				if (gsap && !reduceMotion()) {
					gsap.fromTo(
						root.querySelectorAll('[data-cinema-name], [data-cinema-caption]'),
						{ y: 8, opacity: 0.35 },
						{ y: 0, opacity: 1, duration: 0.5, stagger: 0.05, ease: 'power2.out', overwrite: true }
					);
				}
			});
		});
	}

	/* ============================================================
	   4. ЗАГОЛОВКИ ПО СТРОКАМ (SplitText + маска)
	   ============================================================ */
	function initHeadings() {
		if (!window.gsap || !window.SplitText) return;
		var gsap = window.gsap;
		var SplitText = window.SplitText;
		gsap.registerPlugin(SplitText);

		/* Заголовки НЕ включая .hero h1 — его анимирует hero timeline */
		var selectors = [
			/* Protenance (совместимость) */
			'.about-grid h2',
			'.work-heading h2',
			'.expertise-visual h2',
			'.process-content h2',
			'.enquiry-lead :is(h1,h2)',
			'.archive-intro h1',
			'.archive-cta h2',
			'.project-detail-title h1',
			'.project-detail-info h2',
			'.works-preview h2',
			'.site-film h2',
			'.division-detail h2',
			/* ФСС */
			'.show-head h2',
			'.section-heading h2',
			'.cta-in h2',
			'.security-title',
			'.svh-h1'
		].join(',');

		document.querySelectorAll(selectors).forEach(function (el) {
			var text = ((el.textContent || el.innerText || '') + '').replace(/\s+/g, ' ').trim();
			SplitText.create(el, {
				type: 'lines',
				mask: 'lines',
				linesClass: 'motion-line',
				autoSplit: true,
				onSplit: function (self) {
					el.setAttribute('aria-label', text);
					gsap.fromTo(self.lines,
						{ yPercent: 105, opacity: 0 },
						{
							yPercent: 0,
							opacity: 1,
							duration: 1,
							stagger: 0.085,
							ease: 'power3.out',
							immediateRender: false,
							scrollTrigger: {
								trigger: el,
								start: 'top 91%',
								toggleActions: 'play none none none'
							}
						}
					);
				}
			});
		});
	}

	/* ============================================================
	   5. ПОЯВЛЕНИЕ ЭЛЕМЕНТОВ ПРИ СКРОЛЛЕ
	   ============================================================ */
	function initReveals() {
		if (!window.gsap || !window.ScrollTrigger) return;
		var gsap = window.gsap;

		var reveal = function (elements, trigger, delay) {
			if (!elements.length) return;
			/* Элементы с SplitText-строками анимирует сам SplitText — не дублируем */
			elements = elements.filter(function (el) {
				return !el.querySelector('.motion-line');
			});
			if (!elements.length) return;
			/* fromTo + immediateRender:false — элемент виден до срабатывания триггера.
			   gsap.from() применяет from-состояние сразу и без триггера оставляет
			   элемент навсегда невидимым (opacity 0). */
			gsap.fromTo(elements,
				{ y: 22, opacity: 0 },
				{
					y: 0,
					opacity: 1,
					duration: 0.85,
					stagger: 0.075,
					delay: delay || 0,
					ease: 'power3.out',
					immediateRender: false,
					scrollTrigger: {
						trigger: trigger,
						start: 'top 91%',
						toggleActions: 'play none none none'
					}
				}
			);
		};

		var revealIn = function (selector, childSelector, delay) {
			document.querySelectorAll(selector).forEach(function (section) {
				reveal(Array.prototype.slice.call(section.querySelectorAll(childSelector)), section, delay);
			});
		};

		revealIn('.section-topline', ':scope > *');
		revealIn('.about-copy', ':scope > *', 0.12);
		revealIn('.about-stats', ':scope > div');
		revealIn('.work-heading', ':scope > p', 0.2);
		revealIn('.division-grid', ':scope > a', 0.1);
		revealIn('.site-gallery-grid', ':scope > figure', 0.1);
		revealIn('.service-list', ':scope > details', 0.1);
		revealIn('.process-steps', ':scope > details', 0.1);
		revealIn('.archive-intro', '.archive-intro__top, .archive-intro__bottom', 0.1);
		revealIn('.project-archive', '.archive-controls, .archive-result-line');
		revealIn('.project-detail-title', ':scope > p');
		revealIn('.project-detail-info > div', ':scope > p, :scope > a', 0.12);
		revealIn('.project-facts', ':scope > div', 0.12);
		revealIn('.archive-cta', ':scope > p, :scope > div > a', 0.15);
		revealIn('.enquiry-lead', ':scope > p, :scope > a, :scope > .enquiry-location', 0.1);
		revealIn('.footer-top', ':scope > *');

		/* ФСС */
		revealIn('.section-heading', ':scope > p', 0.1);

		/* ProMaintenance */
		revealIn('.works-preview-copy', ':scope > *', 0.1);

		/* Элементы с data-reveal — появление снизу вверх (ProMaintenance).
		   Заголовки (h1-h3) анимирует SplitText — не дублируем. */
		document.querySelectorAll('[data-reveal]').forEach(function (el) {
			if (el.matches('h1, h2, h3')) return;
			reveal([el], el);
		});

		document.querySelectorAll('.assessment-disclosure, .enquiry-panel').forEach(function (el) {
			reveal([el], el, 0.12);
		});

		/* Изображения: раскрытие через clip-path + zoom */
		document.querySelectorAll('.project-card__image, .expertise-image, .project-detail-hero, .project-next > a').forEach(function (el) {
			var tl = gsap.timeline({
				defaults: { ease: 'power3.out' },
				scrollTrigger: {
					trigger: el,
					start: 'top 93%',
					toggleActions: 'play none none none'
				}
			});

			tl.fromTo(el,
				{ clipPath: 'inset(6% 0% 0% 0% round 8px)', opacity: 0.25 },
				{
					clipPath: 'inset(0% 0% 0% 0% round 8px)',
					opacity: 1,
					duration: 1.15,
					immediateRender: false
				}
			);

			if (!el.matches('.expertise-image')) {
				var img = el.querySelector('img');
				if (img) tl.fromTo(img,
					{ scale: 1.065 },
					{ scale: 1, duration: 1.35, immediateRender: false }, 0);
			}

			var card = el.closest('.project-card');
			if (card) {
				tl.fromTo(card.querySelectorAll('.project-card__meta, h3, p'),
					{ y: 16, opacity: 0 },
					{ y: 0, opacity: 1, duration: 0.75, stagger: 0.065, immediateRender: false }, 0.15);
			}

			if (el.closest('.project-next')) {
				tl.fromTo(el.querySelectorAll('div > *'),
					{ y: 18, opacity: 0 },
					{ y: 0, opacity: 1, duration: 0.8, stagger: 0.075, immediateRender: false }, 0.15);
			}
		});
	}

	/* ============================================================
	   6. МНОГОШАГОВАЯ ФОРМА (Protenance rl) — 2 этапа с прогрессом
	   ============================================================ */
	function initMultiStepForm() {
		var form = document.querySelector('[data-multistep]');
		if (!form) return;

		var gsap = window.gsap;
		var stages = Array.prototype.slice.call(form.querySelectorAll('[data-form-stage]'));
		/* Счётчик и индикаторы лежат ВНЕ формы (в .form-heading) — ищем глобально */
		var counter = document.querySelector('[data-form-counter]');
		var nextBtn = form.querySelector('[data-form-next]');
		var backBtn = form.querySelector('[data-form-back]');
		var errorsBox = form.querySelector('[data-error-summary]');
		var errorsList = form.querySelector('[data-error-summary-list]');
		if (stages.length < 2) return;

		var current = 0;

		/* Правила валидации (по ProMaintenance) */
		var RULES = {
			name:     { min: 2, max: 100, label: 'Имя' },
			company:  { min: 2, max: 120, label: 'Компания' },
			email:    { max: 254, label: 'Email', required: true },
			phone:    { min: 10, max: 15, label: 'Телефон', required: true },
			product:  { label: 'Услуга', required: true },
			location: { max: 120, label: 'Локация' },
			message:  { min: 10, max: 2000, label: 'Описание', required: true }
		};

		var fieldOf = function (name) {
			return form.querySelector('[name="' + name + '"]');
		};

		var errorOf = function (name) {
			return form.querySelector('[data-error-for="' + name + '"]');
		};

		/* Проверка одного поля — возвращает текст ошибки или null */
		var checkField = function (name) {
			var rule = RULES[name];
			if (!rule) return null;
			var field = fieldOf(name);
			if (!field) return null;
			var value = (field.value || '').trim();

			if (rule.required && !value) {
				return rule.label + ' — обязательное поле';
			}
			if (!value) return null;
			if (rule.min && value.length < rule.min) {
				return rule.label + ' — минимум ' + rule.min + ' символов';
			}
			if (rule.max && value.length > rule.max) {
				return rule.label + ' — максимум ' + rule.max + ' символов';
			}
			if (name === 'email' && !/^[^\s@]+@[^\s@.]+(\.[^\s@.]+)+$/.test(value)) {
				return 'Введите корректный email, например name@company.ru';
			}
			if (name === 'phone') {
				var digits = value.replace(/\D/g, '');
				if (!/^[0-9+\-().\s]+$/.test(value)) {
					return 'Телефон — только цифры, пробелы и + ( ) -';
				}
				if (digits.length < 7 || digits.length > 15) {
					return 'Телефон — введите номер полностью';
				}
			}
			return null;
		};

		/* Показать/скрыть ошибки поля */
		var setFieldError = function (name, message) {
			var field = fieldOf(name);
			var target = errorOf(name);
			if (field) {
				if (message) field.setAttribute('aria-invalid', 'true');
				else field.removeAttribute('aria-invalid');
			}
			if (target) {
				target.textContent = message || '';
				target.hidden = !message;
			}
		};

		var clearErrors = function () {
			Object.keys(RULES).forEach(function (name) { setFieldError(name, null); });
			form.querySelectorAll('[data-error-for]').forEach(function (el) {
				el.textContent = '';
				el.hidden = true;
			});
			if (errorsBox) errorsBox.hidden = true;
			if (errorsList) errorsList.replaceChildren();
		};

		/* Собрать ошибки текущего этапа */
		var collectErrors = function (index) {
			var errors = {};
			var stage = stages[index];

			/* обычные поля */
			stage.querySelectorAll('input[required], select[required], textarea[required]').forEach(function (field) {
				var name = field.getAttribute('name');
				if (!name || errors[name]) return;
				if (field.type === 'checkbox') {
					if (!field.checked) errors[name] = 'Отметьте согласие';
					return;
				}
				var message = checkField(name);
				if (message) errors[name] = message;
			});

			/* поля с minlength/maxlength/pattern, но не required */
			stage.querySelectorAll('input, select, textarea').forEach(function (field) {
				var name = field.getAttribute('name');
				if (!name || errors[name] || field.type === 'checkbox' || field.type === 'hidden') return;
				var message = checkField(name);
				if (message) errors[name] = message;
			});

			return errors;
		};

		var showErrors = function (errors) {
			var names = Object.keys(errors);
			if (!names.length) return;

			Object.keys(errors).forEach(function (name) {
				setFieldError(name, errors[name]);
			});

			if (errorsBox) errorsBox.hidden = false;
			if (errorsList) {
				errorsList.replaceChildren();
				names.forEach(function (name) {
					var li = document.createElement('li');
					var a = document.createElement('a');
					a.href = '#field-' + name;
					a.textContent = errors[name];
					a.addEventListener('click', function (e) {
						e.preventDefault();
						var field = fieldOf(name);
						if (field) {
							field.focus({ preventScroll: true });
							field.scrollIntoView({ behavior: 'smooth', block: 'center' });
						}
					});
					li.appendChild(a);
					errorsList.appendChild(li);
				});
			}
		};

		var showStage = function (index, focus) {
			current = index;
			stages.forEach(function (stage, i) {
				stage.hidden = i !== index;
			});

			form.querySelectorAll('[data-step-indicator]').forEach(function (el, i) {
				el.classList.toggle('is-current', i === index);
			});

			if (counter) counter.textContent = '0' + (index + 1) + ' / 0' + stages.length;

			if (gsap && !reduceMotion()) {
				gsap.fromTo(stages[index],
					{ opacity: 0, y: 12 },
					{ opacity: 1, y: 0, duration: 0.45, ease: 'power2.out' }
				);
			}

			if (focus) {
				var field = stages[index].querySelector('input:not([type=hidden]):not([type=checkbox]), select, textarea');
				if (field) field.focus({ preventScroll: true });
			}
			refreshScroll();
		};

		/* Валидация этапа + показ ошибок */
		var validateStage = function (index) {
			clearErrors();
			var errors = collectErrors(index);
			if (!Object.keys(errors).length) return true;
			showErrors(errors);
			var first = fieldOf(Object.keys(errors)[0]);
			if (first) first.focus({ preventScroll: true });
			return false;
		};

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				if (!validateStage(current)) return;
				if (current < stages.length - 1) showStage(current + 1, true);
			});
		}

		if (backBtn) {
			backBtn.addEventListener('click', function () {
				clearErrors();
				if (current > 0) showStage(current - 1, true);
			});
		}

		/* Снимаем ошибку при вводе */
		form.addEventListener('input', function (e) {
			var t = e.target;
			if (!t || !t.getAttribute || t.getAttribute('aria-invalid') !== 'true') return;
			var name = t.getAttribute('name');
			var message = name ? checkField(name) : null;
			if (!message) {
				setFieldError(name, null);
				if (errorsBox) {
					var remaining = form.querySelectorAll('[aria-invalid="true"]').length;
					if (!remaining) errorsBox.hidden = true;
				}
			}
		});

		form.addEventListener('change', function (e) {
			var t = e.target;
			if (!t || !t.getAttribute || t.getAttribute('aria-invalid') !== 'true') return;
			var name = t.getAttribute('name');
			if (t.type === 'checkbox' ? t.checked : !checkField(name)) {
				setFieldError(name, null);
			}
		});

		/* Отправка — только со второго этапа */
		form.addEventListener('submit', function (e) {
			if (current < stages.length - 1) {
				e.preventDefault();
				if (validateStage(current)) showStage(current + 1, true);
			}
		});

		showStage(0, false);
	}

	/* ============================================================
	   6. ЭКСПЕРТИЗА — синхронизация картинок с аккордеоном (Protenance tl)
	   ============================================================ */
	function initExpertise() {
		var details = Array.prototype.slice.call(document.querySelectorAll('[data-service-detail]'));
		var images = Array.prototype.slice.call(document.querySelectorAll('[data-expertise-image]'));
		if (!details.length) return;

		var gsap = window.gsap;

		details.forEach(function (item) {
			item.addEventListener('toggle', function () {
				if (!item.open) return;

				/* Закрываем остальные (name="expertise" делает это нативно, но подстрахуемся) */
				details.forEach(function (other) {
					if (other !== item && other.name === item.name) other.open = false;
				});

				/* Синхронизируем картинки */
				images.forEach(function (img) {
					var match = img.dataset.expertiseImage === item.dataset.serviceDetail;
					img.classList.toggle('is-active', match);
					if (gsap) {
						gsap.to(img, {
							opacity: match ? 1 : 0,
							duration: reduceMotion() ? 0 : 0.7,
							overwrite: true
						});
					}
				});

				refreshScroll();
			});
		});

		/* Открытие по хэшу */
		var openByHash = function () {
			var id = window.location.hash.slice(1);
			var item = details.find(function (d) { return d.id === id; });
			if (item) item.open = true;
		};
		window.addEventListener('hashchange', openByHash);
		openByHash();
	}

	/* ============================================================
	   7. КВИЗ «Не знаете, что нужно?» (Protenance il)
	   ============================================================ */
	function initAssessment() {
		var section = document.querySelector('[data-triage]');
		var form = section && section.querySelector('[data-triage-form]');
		if (!section || !form) return;

		var emptyBox = section.querySelector('[data-triage-empty]');
		var answerBox = section.querySelector('[data-triage-answer]');
		var state = null;

		var setText = function (sel, value) {
			var el = section.querySelector(sel);
			if (el) el.textContent = value;
		};

		/* Логика подбора: симптом + элемент + движение -> причина/шаг/система */
		var diagnose = function (s) {
			var base = {
				cause: 'Проблема чаще всего связана с перегрузкой или нарушением технологии устройства.',
				step: 'Провести обследование и зафиксировать фактическое состояние конструкции.',
				service: 'Капитальный ремонт',
				also: ['Проектирование', 'Монтаж инженерных сетей'],
				caution: ''
			};

			if (s.symptom === 'cracking') {
				if (s.movement === 'moving') {
					base.cause = 'Трещина ещё «живёт» — это структурное перемещение, а не поверхностный дефект. Нагрузка или осадка действуют иначе, чем закладывалось в проекте.';
					base.step = 'Сначала мониторинг и обследование. Заполнять движущуюся трещину нельзя — она вернётся в другом месте.';
					base.service = 'Диагностика и укрепление';
					base.also = ['Капитальный ремонт', 'Проектирование'];
					base.caution = 'Не давайте заливать «живую» трещину до выяснения причины и остановки движения.';
				} else if (s.movement === 'stopped') {
					base.cause = 'Остановившаяся трещина — обычно это historic перегрузка, усадка или температурный эффект. В сечении остаётся след, по которому она прошла.';
					base.step = 'Обследовать схему и ширину трещин, затем выполнить инъектирование для восстановления монолитности.';
					base.service = 'Инъектирование трещин';
					base.also = ['Капитальный ремонт'];
				} else {
					base.cause = 'Пока трещину не наблюдали, она может быть historic или живой — а это требует противоположных решений.';
					base.step = 'Установить мониторинг и фиксировать ширину несколько недель. Этот один шаг определит метод ремонта.';
					base.service = 'Диагностика';
					base.also = ['Инъектирование трещин', 'Капитальный ремонт'];
				}
			} else if (s.symptom === 'spalling') {
				base.cause = 'Открытая арматура означает, что сталь корродировала и расширилась — именно это разрушило защитный слой. В сечении уже потеряна площадь.';
				base.step = 'Выбить до звучащего бетона, оценить, сколько арматуры осталось, и устранить причину коррозии, а не просто зашпатлевать.';
				base.service = 'Капитальный ремонт';
				base.also = ['Усиление конструкций', 'Проектирование'];
				base.caution = 'Если потеря площади арматуры существенна, ремонт сам по себе не вернёт несущую способность — усиление обязательно.';
			} else if (s.symptom === 'rust-staining') {
				base.cause = 'Пятна при ещё целом защитном слое — коррозия началась, но ещё не вышла наружу. Это самый дешёвый момент во всей жизни проблемы.';
				base.step = 'Провести обследование толщины защиты, затем отремонтировать и защитить, пока слой ещё держит.';
				base.service = 'Капитальный ремонт';
				base.also = ['Инъектирование трещин'];
			} else if (s.symptom === 'water-ingress') {
				base.cause = 'Вода проходит через трещину, шов или дефект укладки, попутно корродируя арматуру по дороге.';
				base.step = 'Найти точку входа — она редко совпадает с местом, где видно течь, — и закрыть с правильной стороны.';
				base.service = 'Инъектирование трещин';
				base.also = ['Капитальный ремонт'];
				base.caution = s.movement === 'moving' ? 'Если трещина движется, герметизация не поможет. Сначала нужно разобраться с движением.' : '';
			} else if (s.symptom === 'deflection') {
				base.cause = 'Видимый прогиб означает перегрузку, потерю сечения или изначально недостаточную несущую способность.';
				base.step = 'Обследовать фактическую геометрию и арматуру, затем проверить несущую способность по реальным нагрузкам.';
				base.service = 'Усиление конструкций';
				base.also = ['Проектирование', 'Капитальный ремонт'];
				base.caution = 'Видимый прогиб может означать, что элемент близок к пределу. Возможно, потребуются временные подпорки.';
			} else {
				base.cause = 'С конструкцией обращаются с нагрузкой, на которую её не проектировали — это вопрос Capacity, а не дефект.';
				base.step = 'Определить новые нагрузки и фактическую несущую способность, затем выбрать систему усиления с минимальным вмешательством.';
				base.service = 'Усиление конструкций';
				base.also = ['Проектирование', 'Капитальный ремонт'];
			}
			return base;
		};

		var update = function () {
			var data = new FormData(form);
			var symptom = data.get('symptom');
			var element = data.get('element');
			var movement = data.get('movement');

			if (!symptom || !element || !movement) {
				state = null;
				if (emptyBox) emptyBox.hidden = false;
				if (answerBox) answerBox.hidden = true;
				refreshScroll();
				return;
			}

			state = { symptom: String(symptom), element: String(element), movement: String(movement) };
			var r = diagnose(state);

			setText('[data-triage-cause]', r.cause);
			setText('[data-triage-step]', r.step);
			setText('[data-triage-service]', r.service);
			setText('[data-triage-also]', r.also.join(', '));
			setText('[data-triage-caution-text]', r.caution);

			var caution = section.querySelector('[data-triage-caution]');
			if (caution) caution.hidden = !r.caution;

			if (emptyBox) emptyBox.hidden = true;
			if (answerBox) answerBox.hidden = false;
			refreshScroll();
		};

		form.addEventListener('change', update);

		var reset = section.querySelector('[data-triage-reset]');
		if (reset) {
			reset.addEventListener('click', function () {
				form.reset();
				update();
				var first = form.querySelector('select');
				if (first) first.focus();
			});
		}
	}

	/* ============================================================
	   8. HEADER — прозрачность/блюр относительно высоты hero
	   ============================================================ */
	function initHeader() {
		var toggle = document.querySelector('#nav-toggle');
		var panel = document.querySelector('#nav-panel');
		var bar = document.querySelector('[data-header-bar]');
		if (!toggle || !panel || !bar) return;

		var gsap = window.gsap;
		var hero = document.querySelector('.hero');
		var heroHeight = hero ? hero.offsetHeight : 0;

		/* Прозрачность: 0.02 (прозрачно) → 0.82 (тёмный) по мере ухода из hero */
		var update = function () {
			var progress = (!panel.hidden || !heroHeight)
				? 1
				: Math.min(1, Math.max(0, (window.scrollY - heroHeight * 0.3) / (heroHeight * 0.55)));

			bar.style.setProperty('--nav-opacity', String(0.02 + 0.8 * progress));
			bar.style.setProperty('--nav-blur', (1 + 23 * progress) + 'px');
			bar.toggleAttribute('data-stuck', progress > 0.03);
		};

		var setOpen = function (open) {
			toggle.setAttribute('aria-expanded', String(open));
			panel.hidden = !open;

			var label = toggle.querySelector('[data-nav-label]');
			if (label) label.textContent = open ? 'Close' : 'Меню';

			var openIcon = toggle.querySelector('[data-nav-open]');
			var closeIcon = toggle.querySelector('[data-nav-close]');
			if (openIcon) openIcon.classList.toggle('hidden', open);
			if (closeIcon) closeIcon.classList.toggle('hidden', !open);

			if (open) {
				bar.setAttribute('data-stuck', '');
				if (gsap && !reduceMotion()) {
					gsap.fromTo(panel.querySelectorAll('nav a'),
						{ y: 15, opacity: 0 },
						{ y: 0, opacity: 1, stagger: 0.045, duration: 0.4, ease: 'power2.out' }
					);
				}
			}
			update();
		};

		toggle.addEventListener('click', function () {
			setOpen(toggle.getAttribute('aria-expanded') !== 'true');
		});

		panel.querySelectorAll('a').forEach(function (link) {
			link.addEventListener('click', function () { setOpen(false); });
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !panel.hidden) {
				setOpen(false);
				toggle.focus();
			}
		});

		document.addEventListener('click', function (e) {
			if (e.target instanceof Node &&
				!panel.contains(e.target) && !toggle.contains(e.target) && !panel.hidden) {
				setOpen(false);
			}
		});

		window.addEventListener('resize', function () {
			heroHeight = hero ? hero.offsetHeight : 0;
			if (window.innerWidth > 992) setOpen(false);
			update();
		}, { passive: true });

		window.addEventListener('scroll', update, { passive: true });
		update();
	}

	/* ============================================================
	   7. АККОРДЕОНЫ (details) — плавное раскрытие
	   ============================================================ */
	function initDetails() {
		if (!window.gsap) return;
		var gsap = window.gsap;

		document.addEventListener('toggle', function (e) {
			var el = e.target;
			if (!(el instanceof HTMLDetailsElement) || !el.open) return;
			var body = el.querySelector('.service-detail-body, .assessment-content, .faq-content, :scope > p');
			if (!body) return;
			gsap.fromTo(body,
				{ y: 10, opacity: 0 },
				{ y: 0, opacity: 1, duration: 0.45, ease: 'power2.out', overwrite: true, onComplete: refreshScroll }
			);
		}, true);
	}

	/* ============================================================
	   8. ЯКОРЯ — плавный переход (Lenis anchors)
	   ============================================================ */
	function initAnchors() {
		document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
			anchor.addEventListener('click', function (e) {
				var hash = this.getAttribute('href');
				if (!hash || hash === '#') return;
				var target = document.querySelector(hash);
				if (target) {
					e.preventDefault();
					scrollToTarget(target);
				}
			});
		});
	}

	/* ============================================================
	   INIT
	   ============================================================ */
	function init() {
		lenisInstance = initLenis();

		if (window.gsap && window.ScrollTrigger) {
			window.gsap.registerPlugin(window.ScrollTrigger);
		}

		initHeader();
		initHero();
		initCinema();
		initHeadings();
		initReveals();
		initDetails();
		initExpertise();
		initAssessment();
		initMultiStepForm();
		initAnchors();
		initScrollProgress();

		refreshScroll();
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(refreshScroll);
		}
		window.addEventListener('load', refreshScroll, { once: true });

		/* Страховка: элементы, оказавшиеся в зоне видимости, но не проигранные
		   ScrollTrigger (layout shift, отсутствие триггера) — показываем принудительно */
		setTimeout(forceRevealVisible, 700);

		/* Страховка: если через 3 сек элементы hero всё ещё скрыты —
		   значит GSAP не отработал (нет сети/ошибка) — показываем принудительно */
		setTimeout(function () {
			document.querySelectorAll('.hero-content > .eyebrow, .hero h1 > span, .hero-intro > *, .hero-bottom > *')
				.forEach(function (el) {
					var opacity = parseFloat(getComputedStyle(el).opacity);
					if (opacity < 0.05) {
						el.style.opacity = '1';
						el.style.visibility = 'visible';
						el.style.transform = 'none';
					}
				});
		}, 3000);
	}

	function forceRevealVisible() {
		if (!window.gsap) return;
		var vh = window.innerHeight;
		var tweens = [];
		try {
			tweens = window.gsap.globalTimeline.getChildren(false, true, true) || [];
		} catch (e) {
			return;
		}
		tweens.forEach(function (tw) {
			if (!tw.scrollTrigger) return;
			var targets = tw.targets ? tw.targets() : [];
			var el = targets[0];
			if (!el || !el.getBoundingClientRect) return;
			var r = el.getBoundingClientRect();
			if (r.top < vh && r.bottom > 0 && tw.progress() < 0.05) {
				tw.progress(1);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init, { once: true });
	} else {
		init();
	}
})();
