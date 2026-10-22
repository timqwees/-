/* ============================================================
   ФСС — система анимаций (GSAP 3.15 + ScrollTrigger + SplitText + Lenis)
   Реализация по образцу https://protenance.lorolabs.ai/
   ============================================================ */
(function () {
	'use strict';

	/* Fallback для браузеров без ResizeObserver (нужен GSAP ScrollTrigger/SplitText) */
	if (typeof window.ResizeObserver === 'undefined') {
		window.ResizeObserver = function () {
			this.observe = function () {};
			this.unobserve = function () {};
			this.disconnect = function () {};
		};
	}

	/* --- Утилиты --- */
	var reduceMotion = function () {
		if (!window.matchMedia) return false;
		return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	};

	/* Элемент реально отображается (не внутри display:none).
	   Проверяем только display: visibility:hidden не учитываем — этим
	   состоянием GSAP сам помечает элементы перед появлением (autoAlpha),
	   и такой элемент анимировать как раз нужно. */
	var isVisible = function (el) {
		if (!el) return false;
		if (typeof el.checkVisibility === 'function') {
			return el.checkVisibility({ checkOpacity: false, checkVisibilityCSS: false });
		}
		return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
	};

	/* Радиус скругления элемента для clip-path (проценты недопустимы) */
	var clipRadius = function (el) {
		var radius = window.getComputedStyle(el).borderTopLeftRadius || '';
		var value = parseFloat(radius);
		if (!radius || isNaN(value) || value < 1) return '8px';
		return value + 'px';
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

	/* Выполнить колбэк, когда шрифты готовы (или по таймауту).
	   Нужен там, где действие меняет высоту блока: пока шрифт не пришёл,
	   метрики другие → после подмены шрифта высота меняется → «уезжает» секция. */
	var whenFontsReady = function (cb) {
		if (!document.fonts || !document.fonts.ready) {
			cb();
			return;
		}
		var timer = setTimeout(cb, 2000);
		document.fonts.ready.then(function () {
			clearTimeout(timer);
			cb();
		});
	};

	/* ============================================================
	   ПОЛОСА ПРОГРЕССА СКРОЛЛА (ProMaintenance site-progress)
	   ============================================================ */
	function initScrollProgress() {
		var bar = document.querySelector('.site-progress');
		if (!bar) return;

		/* Высота документа кэшируется: чтение scrollHeight на каждом кадре
		   вызывает forced reflow (принудительный пересчёт layout) — тормозит скролл */
		var maxScroll = 0;

		var measure = function () {
			var doc = document.documentElement;
			maxScroll = doc.scrollHeight - doc.clientHeight;
		};

		var update = function () {
			if (!maxScroll) {
				measure();
				if (!maxScroll) return;
			}
			var progress = (window.scrollY || document.documentElement.scrollTop) / maxScroll;
			bar.style.setProperty('--scroll-progress', Math.min(1, Math.max(0, progress)));
		};

		/* Обновляем кэш при изменении размеров/содержимого */
		var refresh = function () {
			measure();
			update();
		};

		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', refresh, { passive: true });
		window.addEventListener('load', refresh, { once: true });

		measure();
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
		/* Единственный экземпляр Lenis на странице. main.js тоже обращается к нему
		   (window.__lenis.start()), поэтому публикуем под обоими именами. */
		window.__lenis = lenis;

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
	   ============================================================
	   Верхние экраны всех страниц описаны единой таблицей:
	     root  — корневой блок экрана
	     bg    — фоновая картинка: мягко приближается (scale 1.07 → 1)
	     parts — части, которые поднимаются снизу вверх со stagger
	   Список дополняется новыми страницами без изменения кода анимации. */
	var HERO_PRESETS = [
		/* Главная */
		{
			root: '.hero',
			bg: '.hero-visual',
			parts: [
				{ sel: '.hero-content > .eyebrow', from: { y: 12 }, dur: 0.8, at: 0.12 },
				{ sel: '.hero h1 > span', from: { yPercent: 25 }, dur: 1.2, stagger: 0.12, at: 0.25 },
				{ sel: '.hero-intro > *', from: { y: 18 }, dur: 0.9, stagger: 0.08, at: 0.6 },
				{ sel: '.hero-bottom > *', from: { y: 20 }, dur: 0.9, stagger: 0.08, at: 0.85 }
			]
		},
		/* Услуги (20 страниц services/*) */
		{
			root: '.svc-hero',
			bg: '.svc-hero__bg',
			parts: [
				{ sel: '.svc-hero__inner .fss-breadcrumbs', from: { y: 10 }, dur: 0.7, at: 0.1 },
				{ sel: '.svc-hero__eyebrow', from: { y: 12 }, dur: 0.8, at: 0.18 },
				{ sel: '.svc-hero__title > span', from: { yPercent: 25 }, dur: 1.15, stagger: 0.12, at: 0.28 },
				{ sel: '.svc-hero__lead', from: { y: 18 }, dur: 0.9, at: 0.55 },
				{ sel: '.svc-hero__geo', from: { y: 18 }, dur: 0.9, at: 0.63 },
				{ sel: '.svc-hero__actions > *', from: { y: 18 }, dur: 0.9, stagger: 0.09, at: 0.72 },
				{ sel: '.svc-hero__inner .svc-toc', from: { y: 14 }, dur: 0.8, at: 0.84 },
				{ sel: '.svc-hero__inner .svc-geo', from: { y: 14 }, dur: 0.8, at: 0.9 },
				{ sel: '.svc-hero__foot > *', from: { y: 14 }, dur: 0.8, stagger: 0.08, at: 0.94 }
			]
		},
		/* Каталог услуг /uslugi */
		{
			root: '.usl-hero',
			bg: '.usl-hero__bg',
			parts: [
				{ sel: '.usl-hero__inner .fss-breadcrumbs', from: { y: 10 }, dur: 0.7, at: 0.1 },
				{ sel: '.usl-hero__eyebrow', from: { y: 12 }, dur: 0.8, at: 0.18 },
				{ sel: '.usl-hero h1 > span', from: { yPercent: 25 }, dur: 1.15, stagger: 0.12, at: 0.28 },
				{ sel: '.usl-hero__intro > *', from: { y: 18 }, dur: 0.9, stagger: 0.08, at: 0.58 }
			]
		},
		/* Карточка объекта /portfolio/{slug} */
		{
			root: '.prj-hero',
			bg: '.prj-hero__visual',
			parts: [
				{ sel: '.prj-hero__inner .fss-breadcrumbs', from: { y: 10 }, dur: 0.7, at: 0.1 },
				{ sel: '.prj-hero__eyebrow', from: { y: 12 }, dur: 0.8, at: 0.18 },
				{ sel: '.prj-hero__title > span', from: { yPercent: 25 }, dur: 1.15, stagger: 0.12, at: 0.28 },
				{ sel: '.prj-hero__lead', from: { y: 18 }, dur: 0.9, at: 0.55 },
				{ sel: '.prj-hero__actions > *', from: { y: 18 }, dur: 0.9, stagger: 0.09, at: 0.66 },
				{ sel: '.prj-hero__foot > *', from: { y: 20 }, dur: 0.9, stagger: 0.08, at: 0.8 }
			]
		},
		/* Статья блога /blog/{slug} */
		{
			root: '.article-hero',
			parts: [
				{ sel: '.article-breadcrumbs', from: { y: 10 }, dur: 0.7, at: 0.1 },
				{ sel: '.article-meta-top > *', from: { y: 12 }, dur: 0.8, stagger: 0.08, at: 0.2 },
				{ sel: '.article-title', from: { yPercent: 20 }, dur: 1.05, at: 0.3 },
				{ sel: '.article-desc', from: { y: 16 }, dur: 0.9, at: 0.52 }
			]
		},
		/* Список блога /blog */
		{
			root: '.blog-hero',
			parts: [
				{ sel: '.blog-hero-badge', from: { y: 12, autoAlpha: 0 }, dur: 0.7, at: 0.1 },
				{ sel: '.blog-hero-title', from: { yPercent: 20, autoAlpha: 0 }, dur: 1.05, at: 0.2 },
				{ sel: '.blog-hero-desc', from: { y: 16, autoAlpha: 0 }, dur: 0.9, at: 0.45 },
				{ sel: '.blog-hero-stats > *', from: { y: 14, autoAlpha: 0 }, dur: 0.8, stagger: 0.07, at: 0.58 }
			]
		},
		/* О компании (бирюзовая версия) */
		{
			root: '.bi-hero',
			parts: [
				{ sel: '.bi-eyebrow', from: { y: 12 }, dur: 0.8, at: 0.12 },
				{ sel: '.bi-h1', from: { yPercent: 22 }, dur: 1.1, at: 0.22 },
				{ sel: '.bi-sub', from: { y: 18 }, dur: 0.9, at: 0.45 },
				{ sel: '.bi-cta > *', from: { y: 18 }, dur: 0.8, stagger: 0.08, at: 0.55 },
				{ sel: '.bi-visual', from: { y: 26, autoAlpha: 0 }, dur: 1.1, at: 0.4 }
			]
		},
		/* Поиск /search */
		{
			root: '.page-hero',
			parts: [
				{ sel: '.page-crumb', from: { y: 10 }, dur: 0.7, at: 0.1 },
				{ sel: '.page-eyebrow', from: { y: 12 }, dur: 0.8, at: 0.18 },
				{ sel: '.page-hero-inner .pg-h1', from: { yPercent: 22 }, dur: 1.05, at: 0.26 },
				{ sel: '.page-hero-inner .search-form', from: { y: 18 }, dur: 0.9, at: 0.46 },
				{ sel: '.page-hero-inner .page-lead', from: { y: 14 }, dur: 0.8, at: 0.58 }
			]
		},
		/* Контакты /contact — верхний экран заявки */
		{
			root: '.enquiry-section--standalone',
			parts: [
				{ sel: '.enquiry-lead > .eyebrow', from: { y: 12 }, dur: 0.8, at: 0.12 },
				{ sel: '.enquiry-lead > p', from: { y: 18 }, dur: 0.9, at: 0.55 },
				{ sel: '.enquiry-lead > .enquiry-email', from: { y: 18 }, dur: 0.9, at: 0.66 },
				{ sel: '.enquiry-lead > .enquiry-phone', from: { y: 16 }, dur: 0.85, at: 0.74 },
				{ sel: '.enquiry-lead > .enquiry-location', from: { y: 14 }, dur: 0.8, at: 0.82 }
			]
		}
	];

	function initHeroes() {
		if (!window.gsap) return;
		var gsap = window.gsap;
		var reduced = reduceMotion();
		/* Заголовки, которые разобьёт SplitText — их анимирует он сам.
		   Если часть hero-таймлайна попадает внутрь такого заголовка,
		   пропускаем её: иначе текст получает две анимации и остаётся
	  	   невидимым (SplitText переносит inline-стили в свои строки). */
		var headings = getSplitHeadings();

		HERO_PRESETS.forEach(function (preset) {
			var root = document.querySelector(preset.root);
			if (!root) return;

			var parts = preset.parts.filter(function (part) {
				var targets = root.querySelectorAll(part.sel);
				if (!targets.length) return false;
				return !Array.prototype.some.call(targets, function (el) {
					var heading = el.closest ? el.closest('h1, h2, h3') : null;
					return headings.has(el) || (heading && headings.has(heading));
				});
			});

			/* При «уменьшить движение» — показываем сразу, без анимации */
			if (reduced) {
				parts.forEach(function (part) {
					var targets = root.querySelectorAll(part.sel);
					if (!targets.length) return;
					gsap.set(targets, { opacity: 1, y: 0, yPercent: 0, visibility: 'visible' });
				});
				return;
			}

			/* Hero Reveal (ProMaintenance): фон scale 1.07 → 1 (2s),
			   затем текст снизу вверх со stagger */
			var tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

			if (preset.bg) {
				var bg = root.querySelector(preset.bg);
				if (bg) tl.fromTo(bg, { scale: 1.07 }, { scale: 1, duration: 2 }, 0);
			}

			parts.forEach(function (part) {
				var targets = root.querySelectorAll(part.sel);
				if (!targets.length) return;

				/* fromTo, а не from: конечные значения задаются явно. Иначе GSAP
				   берёт их из текущего состояния элемента, а если у элемента есть
				   CSS-transition по opacity — получает промежуточное значение и
				   элемент «замерзает» наполовину прозрачным. */
				var fromVars = { autoAlpha: 0 };
				for (var key in part.from) {
					if (Object.prototype.hasOwnProperty.call(part.from, key)) fromVars[key] = part.from[key];
				}

				var toVars = { autoAlpha: 1, y: 0, yPercent: 0, duration: part.dur || 0.9, ease: 'power3.out' };
				if (part.stagger) toVars.stagger = part.stagger;

				tl.fromTo(targets, fromVars, toVars, part.at || 0);
			});
		});
	}

	/* ============================================================
	   ПАРАЛЛАКС (ProMaintenance) — фон уходит вниз медленнее контента
	   ============================================================ */
	function initParallax() {
		if (!window.gsap || !window.ScrollTrigger) return;

		var gsap = window.gsap;
		var mq = window.matchMedia
			? window.matchMedia('(min-width: 993px) and (prefers-reduced-motion: no-preference)')
			: { matches: true };
		if (!mq.matches) return;

		[['.hero', '.hero-visual', 12], ['.process-section', '.process-photo', 7], ['.svc-hero', '.svc-hero__bg', 10], ['.usl-hero', '.usl-hero__bg', 10]].forEach(function (row) {
			var trigger = document.querySelector(row[0]);
			var target = document.querySelector(row[1]);
			if (!trigger || !target) return;
			gsap.to(target, {
				yPercent: row[2],
				ease: 'none',
				scrollTrigger: {
					trigger: trigger,
					start: 'top top',
					end: 'bottom top',
					scrub: 0.65
				}
			});
		});
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
	/* Список заголовков, которые разбиваются на строки.
	   Используется и в initReveals: блок, внутри которого есть такой
	   заголовок, не анимируется целиком — иначе текст «дёргается» дважды. */
	var HEADING_SELECTORS = [
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
		'.svh-h1',
		/* Портфолио */
		'.esp-h1',
		/* Блог */
		'.section-title',
		'.blog-cta-title',
		/* О компании */
		'.xl-hero-grid h1',
		'.xl-about-grid h2',
		'.xl-why-l h2',
		/* Услуги (шаблон service_template) */
		'.svc-section h2',
		/* Каталог услуг */
		'.usl-cta h2',
		/* Объект */
		'.prj-h1',
		'.prj-section h2',
		/* .prj-hero__title не включаем — её анимирует hero-таймлайн (.prj-hero) */
		'.prj-cta h2',
		/* Статья блога */
		'.article-body h2',
		'.article-body h3',
		/* Служебные страницы */
		'.fss-legal h2'
	].join(',');

		/* Заголовки, которые будут разбиты на строки (SplitText).
		   Список вычисляется один раз — по нему же reveals исключает сами
		   заголовки, чтобы текст не анимировался дважды. */
	var splitHeadings = null;
	var getSplitHeadings = function () {
		if (!splitHeadings) {
			splitHeadings = new Set();
			if (document.querySelectorAll) {
				document.querySelectorAll(HEADING_SELECTORS).forEach(function (el) {
					splitHeadings.add(el);
				});
			}
		}
		return splitHeadings;
	};

	function initHeadings() {
		if (!window.gsap || !window.SplitText) return;
		if (reduceMotion()) return;
		var gsap = window.gsap;
		var SplitText = window.SplitText;
		gsap.registerPlugin(SplitText);

		/* Заголовки НЕ включая .hero h1 — его анимирует hero timeline */
		var selectors = HEADING_SELECTORS;

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
		if (reduceMotion()) return;
		var gsap = window.gsap;

		var reveal = function (elements, trigger, delay) {
			if (!elements.length) return;
			/* Элементы с SplitText-строками анимирует сам SplitText — не дублируем.
		   Проверяем и будущие строки (по списку заголовков), и уже готовые. */
			var headings = getSplitHeadings();
			elements = elements.filter(function (el) {
				if (el.querySelector('.motion-line')) return false;
				if (headings.has(el)) return false;
				return true;
			});
			/* Скрытые контейнеры (display:none) не трогаем: GSAP ставит inline
			   opacity:0, который перебил бы CSS-правило показа элемента. */
			elements = elements.filter(function (el) {
				return isVisible(el);
			});
			if (!elements.length) return;
			/* Помечаем — чтобы общий проход не анимировал элемент второй раз */
			elements.forEach(function (el) { el.setAttribute('data-fss-reveal', ''); });
			/* gsap.fromTo() применяет начальное состояние сразу — элемент ждёт
			   своего момента в зоне видимости и плавно появляется (как в
			   ProMaintenance). Если ScrollTrigger не сработает — элемент
			   покажет forceRevealVisible(). */
			gsap.fromTo(elements,
				{ y: 22, opacity: 0 },
				{
					y: 0,
					opacity: 1,
					duration: 0.85,
					stagger: 0.075,
					delay: delay || 0,
					ease: 'power3.out',
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

		/* ============================================================
		   РЕЕСТР СЕКЦИЙ
		   Формат: [контейнер, что внутри показывать, задержка]
		   ============================================================ */
		var REVEAL_RULES = [
			/* Главная / общие (ProMaintenance) */
			['.section-topline', ':scope > *', 0],
			['.about-copy', ':scope > *', 0.12],
			['.about-stats', ':scope > div', 0],
			['.work-heading', ':scope > p', 0.2],
			['.division-grid', ':scope > a', 0.1],
			['.site-gallery-grid', ':scope > figure', 0.1],
			['.service-list', ':scope > details', 0.1],
			['.process-steps', ':scope > details', 0.1],
			['.archive-intro', '.archive-intro__top, .archive-intro__bottom', 0.1],
			['.project-archive', '.archive-controls, .archive-result-line', 0],
			['.project-detail-title', ':scope > p', 0],
			['.project-detail-info > div', ':scope > p, :scope > a', 0.12],
			['.project-facts', ':scope > div', 0.12],
			['.archive-cta', ':scope > p, :scope > div > a', 0.15],
			['.enquiry-section:not(.enquiry-section--standalone) .enquiry-lead', ':scope > p, :scope > a, :scope > .enquiry-location', 0.1],
			['.footer-top', ':scope > *', 0],
			['.form-heading', ':scope > *', 0.1],

			/* ФСС */
			['.show-head', ':scope > *', 0.1],
			['.section-heading', ':scope > p', 0.1],
			['.cta-in', ':scope > *', 0.15],
			['.reviews-grid', ':scope > .review-card', 0.07],
			['.trust-rail', ':scope > li', 0.05],
			['.case-grid', ':scope > .case-card', 0.07],
			['.pluses-grid', ':scope > *', 0.07],
			['.pluses-stats', ':scope > *', 0.07],

			/* ProMaintenance */
			['.works-preview-copy', ':scope > *', 0.1],

			/* Портфолио / Блог / О компании */
			['.esp-top', ':scope > *', 0.1],
			['.esp-filters', ':scope > *', 0.06],
			['.esp-item', ':scope > figcaption', 0.05],
			['.pf-grid', ':scope > *', 0.07],
			['.pf-facts', ':scope > *', 0.07],
			['.xl-hero-grid', ':scope > *', 0.1],
			['.xl-about-grid', ':scope > *', 0.1],
			['.xl-why-l', ':scope > *', 0.1],
			['.xl-cards', ':scope > .xl-card', 0.08],
			['.xl-stats-row', ':scope > *', 0.08],
			['.blog-card', ':scope > *', 0.05],
			['.blog-feature', ':scope > *', 0.1],

			/* Каталог услуг /uslugi */
			['.usl-stats', ':scope > .usl-stat', 0.08],
			['.usl-facts', ':scope > .usl-facts__note', 0.12],
			['.uc-grid', ':scope > .uc-card', 0.07],
			['.uc-cats', ':scope > .uc-cat', 0.06],
			['.usl-cta__in', ':scope > *', 0.12],
			['.usl-toolbar', ':scope > *', 0.08],

			/* Услуги (service_template) */
			['.svc-stats', ':scope > .svc-stat', 0.08],
			['.svc-section', ':scope > .svc-panel, :scope > .svc-lead, :scope > .svc-grid, :scope > .svc-step, :scope > .svc-aud__card, :scope > .svc-adv__card, :scope > .svc-work, :scope > .svc-tablewrap, :scope > .svc-note, :scope > .svc-project, :scope > .svc-calc, :scope > .svc-form, :scope > .svc-faq__item, :scope > details', 0.08],
			['.svc-grid', ':scope > .svc-panel', 0.07],
			['.svc-steps', ':scope > .svc-step', 0.08],
			['.svc-faq', ':scope > details', 0.06],
			['.svc-adv', ':scope > .svc-adv__card', 0.07],
			['.svc-works', ':scope > .svc-work', 0.07],
			['.svc-tablewrap', ':scope > tr', 0.02],
			['.svc-aud', ':scope > *', 0.08],
			['.cta-band', ':scope > .cta-in', 0.1],

			/* Объект /portfolio/{slug} */
			['.prj-facts', ':scope > div', 0.07],
			['.prj-steps', ':scope > .prj-step', 0.08],
			['.prj-section', ':scope > p, :scope > .prj-features, :scope > .prj-gallery', 0.08],
			['.prj-gallery', ':scope > figure', 0.07],
			['.faq-grid', ':scope > details', 0.06],
			['.prj-features', ':scope > li', 0.05],
			['.prj-cta__inner', ':scope > *', 0.12],

			/* Статья блога /blog/{slug} */
			['.article-body', ':scope > p, :scope > ul, :scope > ol, :scope > div[data-type], :scope > .article-img', 0.05],
			['.article-sidebar-card', ':scope > *', 0.08],
			['.author-bio', ':scope > *', 0.08],
			['.article-expert', ':scope > *', 0.08],
			['.article-expert__steps', ':scope > li', 0.06],
			['.article-cta', ':scope > *', 0.08],
			['.article-tags', ':scope > .article-tag', 0.04],
			['.article-popular-item', ':scope > *', 0.05],

			/* Список блога /blog */
			['.blog-hero-content', ':scope > *', 0.1],
			['.blog-cards-grid', ':scope > .blog-card', 0.06],
			['.blog-content-section', ':scope > .container > *', 0.08],
			['.blog-featured-card', ':scope > *', 0.08],
			['.blog-sidebar-card', ':scope > *', 0.08],
			['.blog-service-item', ':scope > *', 0.05],
			['.blog-cta-card', ':scope > *', 0.1],
			['.blog-empty-state', ':scope > *', 0.08],
			['.blog-pagination', ':scope > *', 0.05],

			/* Поиск */
			['.page-hero-inner', ':scope > *', 0.1],
			['.page-section', '.pcard', 0.07],

			/* Служебные страницы */
			['.fss-legal', ':scope > h1, :scope > h2, :scope > .code, :scope > p, :scope > ul, :scope > ol, :scope > .message, :scope > .btn, :scope > .fss-legal-note', 0.05],

			/* О компании (бирюзовая версия) */
			['.bi-hero-in', ':scope > *', 0.1],
			['.bi-stats', ':scope > *', 0.08],
			['.bi-aud', ':scope > .bi-card', 0.07],
			['.expertise-grid', ':scope > .expertise-card', 0.07],
			['.stats-grid', ':scope > .stat-cell', 0.07],
			['.security-grid', ':scope > .security-card', 0.07]
		];

		REVEAL_RULES.forEach(function (rule) {
			revealIn(rule[0], rule[1], rule[2]);
		});

		/* Шаги процесса: на мобильных это горизонтальный слайдер со своим
		   CSS-показом (opacity:1 в media-запросе) — там GSAP не трогает
		   элементы, иначе inline-стиль перебьёт правило слайдера. */
		if (window.matchMedia('(min-width: 900px)').matches) {
			revealIn('.steps3', ':scope > .step3', 0.07);
		}

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
		document.querySelectorAll('.project-card__image, .expertise-image, .project-detail-hero, .project-next > a, .esp-item .ph, .blog-card-img, .blog-featured-img, .prj-gallery figure, .svc-aud__media, .svc-work__media, .svc-project__media, .article-img, .blog-popular-img').forEach(function (el) {
			/* Радиус берём из стилей элемента: в clip-path нельзя проценты,
			   а фиксированный 8px срезал бы скругления в 18–32px. */
			var radius = clipRadius(el);
			var tl = gsap.timeline({
				defaults: { ease: 'power3.out' },
				scrollTrigger: {
					trigger: el,
					start: 'top 93%',
					toggleActions: 'play none none none'
				}
			});

			tl.fromTo(el,
				{ clipPath: 'inset(6% 0% 0% 0% round ' + radius + ')', opacity: 0.25 },
				{
					clipPath: 'inset(0% 0% 0% 0% round ' + radius + ')',
					opacity: 1,
					duration: 1.15
				}
			);

			if (!el.matches('.expertise-image')) {
				var img = el.querySelector('img');
				if (img) tl.fromTo(img, { scale: 1.065 }, { scale: 1, duration: 1.35 }, 0);
			}

			var card = el.closest('.project-card');
			if (card) {
				tl.fromTo(card.querySelectorAll('.project-card__meta, h3, p'),
					{ y: 16, opacity: 0 },
					{ y: 0, opacity: 1, duration: 0.75, stagger: 0.065 }, 0.15);
			}

			if (el.closest('.project-next')) {
				tl.fromTo(el.querySelectorAll('div > *'),
					{ y: 18, opacity: 0 },
					{ y: 0, opacity: 1, duration: 0.8, stagger: 0.075 }, 0.15);
			}
		});

		initGenericReveals();
	}

	/* ============================================================
	   5.1 ОБЩИЙ ПРОХОД — всё, что не описано в реестре, тоже появляется
	   Заголовки (h1-h3) пропускаем: их анимирует SplitText.
	   Элементы, уже помеченные data-fss-reveal, повторно не трогаем.
	   ============================================================ */
	function initGenericReveals() {
		if (!window.gsap || !window.ScrollTrigger) return;
		if (reduceMotion()) return;
		var gsap = window.gsap;
		var headings = getSplitHeadings();

		var sections = document.querySelectorAll(
			'main section, main > div > section, .fss-article-layout > section, .fss-article-layout section'
		);

		sections.forEach(function (section) {
			var children = Array.prototype.slice.call(section.children).filter(function (el) {
				if (el.matches('h1, h2, h3')) return false;
				if (headings.has(el)) return false;
				if (el.hasAttribute('data-fss-reveal')) return false;
				if (el.querySelector('.motion-line')) return false;
				return isVisible(el);
			});

			if (children.length < 2) return;

			children.forEach(function (el) { el.setAttribute('data-fss-reveal', ''); });

			gsap.fromTo(children,
				{ y: 20, opacity: 0 },
				{
					y: 0,
					opacity: 1,
					duration: 0.8,
					stagger: 0.07,
					ease: 'power3.out',
					scrollTrigger: {
						trigger: section,
						start: 'top 91%',
						toggleActions: 'play none none none'
					}
				}
			);
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
	   6.1 ЭКРАН «ЗАЯВКА ПРИНЯТА» (ProMaintenance enquiry-success)
	   Кнопка «Отправить ещё одну заявку» возвращает чистую форму
	   ============================================================ */
	function initEnquiryReset() {
		var reset = document.querySelector('[data-enquiry-reset]');
		if (!reset) return;
		reset.addEventListener('click', function () {
			window.location.reload();
		});
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
		/* Hero-блок страницы: [data-nav-hero] помечает полноэкранные тёмные hero
		   (главная, объект /portfolio/{slug}, услуга /services/{slug}, /uslugi, /about).
		   По его высоте считаем момент «прилипания» шапки. */
		var hero = document.querySelector('[data-nav-hero]') || document.querySelector('.hero');
		var heroHeight = hero ? hero.offsetHeight : 0;

		/* Прозрачность: 0.02 (прозрачно) → 0.82 (тёмный) по мере ухода из hero.
		   Фон шапки читает --nav-opacity/--nav-blur, поэтому значение должно
		   обновляться строго по текущей позиции скролла — иначе при возврате
		   вверх шапка «залипает» серой плашкой до следующего движения.
		   Если hero не найден (страница без заставки или устаревший кэш скрипта) —
		   ориентируемся на высоту самой шапки: вверху прозрачно, дальше «прилипает».

		   ВАЖНО: в самом верху страницы (scrollY <= 4) шапка ВСЕГДА прозрачна.
		   Раньше состояние считалось только от scrollY, и при восстановлении
		   прокрутки браузером (перезагрузка/«назад») шапка оставалась тёмной
		   там, где должна быть прозрачной. */
		var update = function () {
			var y = window.scrollY || window.pageYOffset || 0;

			/* Hero мог не найтись при инициализации (ленивая отрисовка) —
			   ищем ещё раз, пока не нашли. */
			if (!heroHeight) {
				var found = document.querySelector('[data-nav-hero]') || document.querySelector('.hero');
				if (found && found !== hero) {
					hero = found;
					heroHeight = hero.offsetHeight;
					observeHero();
				}
			}

			var progress;
			if (!panel.hidden) {
				progress = 1;                          // открытое мобильное меню
			} else if (y <= 4) {
				progress = 0;                          // верх страницы — прозрачно
			} else if (heroHeight) {
				progress = Math.min(1, Math.max(0, (y - heroHeight * 0.3) / (heroHeight * 0.55)));
			} else {
				progress = y > (bar.offsetHeight || 90) ? 1 : 0;
			}

			bar.style.setProperty('--nav-opacity', String(0.02 + 0.8 * progress));
			bar.style.setProperty('--nav-blur', (1 + 23 * progress) + 'px');
			bar.toggleAttribute('data-stuck', progress > 0.03);
		};

		var heroObserver = null;
		var observeHero = function () {
			if (heroObserver || !hero || typeof ResizeObserver === 'undefined') return;
			heroObserver = new ResizeObserver(function () {
				heroHeight = hero.offsetHeight;
				update();
			});
			heroObserver.observe(hero);
		};

		/* Высота hero может измениться после загрузки (картинки, шрифты) —
		   иначе порог прозрачности считался бы от устаревшего значения. */
		observeHero();

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

		var ticking = false;
		var onScroll = function () {
			if (ticking) return;
			ticking = true;
			requestAnimationFrame(function () {
				ticking = false;
				update();
			});
		};

		window.addEventListener('scroll', onScroll, { passive: true });

		/* Возврат из bfcache (кнопка «назад») и догрузка: страница может
		   восстановиться с прокруткой — пересчитываем состояние шапки. */
		window.addEventListener('pageshow', update);
		window.addEventListener('load', update);

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
			var body = el.querySelector('.service-detail-body, .assessment-content, .faq-content, .svc-faq__body, .svc-step__body, :scope > p, :scope > div');
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
	   MOTION — анимационная система.
	   ВНИМАНИЕ: не оборачиваем в matchMedia('no-preference') —
	   при включённом «уменьшить движение» отключалось ВСЁ,
	   включая Lenis (плавный скролл) и hero-анимацию.
	   Каждый модуль сам проверяет reduceMotion().
	   ============================================================ */
	function initMotion() {
		lenisInstance = initLenis();

		if (window.gsap && window.ScrollTrigger) {
			window.gsap.registerPlugin(window.ScrollTrigger);

			/* ProMaintenance: игнорировать resize на мобильных (появление адресной
			   строки вызывало пересчёт триггеров и «прыжок» секций) */
			window.ScrollTrigger.config({
				limitCallbacks: true,
				ignoreMobileResize: true,
				autoRefreshEvents: 'visibilitychange,DOMContentLoaded,load,resize'
			});
		}

		initHeroes();
		/* SplitText — только после готовности шрифтов: иначе autoSplit пересобирает
		   строки после подмены шрифта и высота заголовка меняется на ходу (CLS). */
		whenFontsReady(function () {
			try {
				initHeadings();
			} catch (e) {
				console.error('[FSS] Ошибка инициализации модуля headings:', e);
			}
			refreshScroll();
		});
		initReveals();
		initParallax();

		refreshScroll();
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(refreshScroll);
		}
		window.addEventListener('load', refreshScroll, { once: true });

		setTimeout(forceRevealVisible, 2500);
	}

	/* ============================================================
	   INIT
	   ============================================================ */
	function init() {
		/* Каждый модуль изолирован: ошибка в одном не ломает остальные */
		var safe = function (name, fn) {
			try {
				fn();
			} catch (e) {
				console.error('[FSS] Ошибка инициализации модуля ' + name + ':', e);
			}
		};

		/* Функциональные модули — работают всегда */
		safe('header', initHeader);
		safe('cinema', initCinema);
		safe('details', initDetails);
		safe('expertise', initExpertise);
		safe('assessment', initAssessment);
		safe('form', initMultiStepForm);
		safe('enquiry-reset', initEnquiryReset);
		safe('anchors', initAnchors);
		safe('progress', initScrollProgress);

		/* Анимационная система */
		safe('motion', initMotion);

		/* Страховка: если через 3 сек элементы hero всё ещё скрыты —
		   значит GSAP не отработал (нет сети/ошибка) — показываем принудительно */
		setTimeout(function () {
			HERO_PRESETS.forEach(function (preset) {
				var root = document.querySelector(preset.root);
				if (!root) return;
				preset.parts.forEach(function (part) {
					root.querySelectorAll(part.sel).forEach(function (el) {
						var opacity = parseFloat(window.getComputedStyle(el).opacity);
						if (opacity < 0.05) {
							el.style.opacity = '1';
							el.style.visibility = 'visible';
							el.style.transform = 'none';
						}
					});
				});
			});
		}, 3000);
	}

	/* Страховка: элементы, оказавшиеся в зоне видимости, но не проигранные
	   ScrollTrigger (layout shift, отсутствие триггера) — показываем.
	   Проверяем только элементы В viewport, иначе сломаем эффект появления
	   для секций ниже экрана. */
	function forceRevealVisible() {
		if (!window.gsap || !window.ScrollTrigger) return;
		var triggers = window.ScrollTrigger.getAll ? window.ScrollTrigger.getAll() : [];
		var vh = window.innerHeight;
		triggers.forEach(function (st) {
			var anim = st.animation;
			if (!anim || typeof anim.progress !== 'function') return;
			var el = st.trigger;
			if (!el || !el.getBoundingClientRect) return;
			var r = el.getBoundingClientRect();
			if (r.top < vh && r.bottom > 0 && anim.progress() < 0.05) {
				anim.progress(1);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init, { once: true });
	} else {
		init();
	}
})();
