/* ФСС runtime: scroll-reveal (.view/.viewLeft/.viewRight) + модалка каталога.
   Правится напрямую, без сборщиков. */
(function () {
	'use strict';

	/* Плавное появление блоков при скролле */
	function initViewElements() {
		var distantion = 30;
		var fadeEls = document.querySelectorAll('.View, .view');
		var leftEls = document.querySelectorAll('.viewLeft, .viewleft');
		var rightEls = document.querySelectorAll('.viewRight, .viewright');
		function check() {
			var h = window.innerHeight;
			fadeEls.forEach(function (el) {
				var top = el.getBoundingClientRect().top;
				var vis = top < h - distantion;
				el.style.opacity = vis ? '1' : '0';
				el.style.transform = vis ? 'translateY(0rem)' : 'translateY(10rem)';
			});
			leftEls.forEach(function (el) {
				var vis = el.getBoundingClientRect().top < h - distantion;
				el.style.opacity = vis ? '1' : '0';
				el.style.transform = vis ? 'translateX(0rem)' : 'translateX(-10rem)';
			});
			rightEls.forEach(function (el) {
				var vis = el.getBoundingClientRect().top < h - distantion;
				el.style.opacity = vis ? '1' : '0';
				el.style.transform = vis ? 'translateX(0rem)' : 'translateX(10rem)';
			});
		}
		check();
		var ticking = false;
		window.addEventListener('scroll', function () {
			if (!ticking) { ticking = true; requestAnimationFrame(function () { ticking = false; check(); }); }
		}, { passive: true });
	}

	/* Модалка каталога (#catalog-modal + карточки .openWindow) */
	function initModalHandlers() {
		var modal = document.getElementById('catalog-modal');
		if (!modal) return;
		var title = document.getElementById('catalog-modal-title');
		var desc = document.getElementById('catalog-modal-desc');
		var close = document.getElementById('catalog-modal-close');
		var backdrop = document.getElementById('catalog-modal-backdrop');
		document.querySelectorAll('.openWindow').forEach(function (card) {
			var btn = card.querySelector('.openWindow_button');
			if (!btn) return;
			btn.style.cursor = 'pointer';
			btn.addEventListener('click', function () {
				var t = card.querySelector('.openWindow_title');
				var d = card.querySelector('.openWindow_description');
				if (title) title.innerHTML = t ? t.innerHTML : '';
				if (desc) desc.innerHTML = d ? d.innerHTML : '';
				if (typeof modal.showModal === 'function') modal.showModal();
				else modal.setAttribute('open', '');
			});
		});
		function hide() { if (typeof modal.close === 'function') modal.close(); else modal.removeAttribute('open'); }
		if (close) close.addEventListener('click', hide);
		if (backdrop) backdrop.addEventListener('click', function (e) { if (e.target === backdrop) hide(); });
		modal.addEventListener('cancel', function (e) { e.preventDefault(); hide(); });
	}

	/* Слайдер «Наши работы» (works-band): текст + веер фото, счётчик 01/03 */
	function initWorksSlider() {
		var stack = document.getElementById('worksStack');
		if (!stack) return;
		var slides = [
			{ num: '01', name: 'Модульное общежитие на 120 мест', desc: 'Два этажа, тёплый контур, кондиционеры в каждой комнате. Сдали за 30 дней — заселили сразу, без доводок.', front: ['/public/assets/image/swiper/1.jpeg', 'Модульное общежитие из сэндвич-панелей — объект ФСС'], mid: ['/public/assets/image/banner.jpeg', 'Модульные здания — производство ФСС'], back: ['/public/assets/image/swiper/2.jpg', 'Склад из сэндвич-панелей — объект ФСС'] },
			{ num: '02', name: 'Капремонт школы под ключ', desc: 'Полы, потолки, электрика и чистовая отделка классов. Сдали к 1 сентября — без срыва сроков.', front: ['/public/assets/image/swiper/3.jpeg', 'Интерьер школы после капитального ремонта — ФСС'], mid: ['/public/assets/image/swiper/1.jpeg', 'Модульное общежитие из сэндвич-панелей — объект ФСС'], back: ['/public/assets/image/services/капстроение/10.JPG', 'Строительство быстровозводимого здания — ФСС'] },
			{ num: '03', name: 'Склад 1 200 м² из сэндвич-панелей', desc: 'Доковые ворота, тёплый контур, освещение и вентиляция. Каркас и обшивка за 45 дней.', front: ['/public/assets/image/services/капстроение/10.JPG', 'Строительство быстровозводимого здания — ФСС'], mid: ['/public/assets/image/swiper/2.jpg', 'Склад из сэндвич-панелей — объект ФСС'], back: ['/public/assets/image/banner.jpeg', 'Модульные здания — производство ФСС'] }
		];
		var i = 0;
		var txt = document.getElementById('worksText');
		var num = document.getElementById('worksNum');
		var name = document.getElementById('worksName');
		var desc = document.getElementById('worksDesc');
		var count = document.getElementById('worksCount');
		var front = document.getElementById('worksFront');
		var mid = document.getElementById('worksMid');
		var back = document.getElementById('worksBack');
		var prev = document.getElementById('worksPrev');
		var next = document.getElementById('worksNext');
		function render(s) {
			num.textContent = s.num;
			name.textContent = s.name;
			desc.textContent = s.desc;
			count.textContent = s.num + '/0' + slides.length;
			front.src = s.front[0]; front.alt = s.front[1];
			mid.src = s.mid[0]; mid.alt = s.mid[1];
			back.src = s.back[0]; back.alt = s.back[1];
		}
		function go(n) {
			i = (n + slides.length) % slides.length;
			txt.classList.add('swap'); stack.classList.add('swap');
			setTimeout(function () { render(slides[i]); txt.classList.remove('swap'); stack.classList.remove('swap'); }, 300);
		}
		if (prev) prev.addEventListener('click', function () { go(i - 1); });
		if (next) next.addEventListener('click', function () { go(i + 1); });
	}

	/* FAQ — плавное открытие/закрытие <details> */
	function initFaqAccordion() {
		document.querySelectorAll('.faq-ex').forEach(function (details) {
			var summary = details.querySelector('summary');
			var content = details.querySelector('.faq-content');
			if (!summary || !content) return;

			if (details.open) {
				content.classList.add('is-open');
				content.style.height = 'auto';
			} else {
				content.style.height = '0px';
			}

			summary.addEventListener('click', function (e) {
				e.preventDefault();

				if (details.open) {
					/* Закрытие: фиксируем высоту, затем до 0 */
					content.style.height = content.scrollHeight + 'px';
					void content.offsetHeight;
					content.classList.remove('is-open');
					content.style.height = '0px';
					var onEnd = function (ev) {
						if (ev.propertyName !== 'height') return;
						content.removeEventListener('transitionend', onEnd);
						details.open = false;
					};
					content.addEventListener('transitionend', onEnd);
				} else {
					/* Открытие: показываем, разворачиваем до auto */
					details.open = true;
					content.classList.add('is-open');
					content.style.height = '0px';
					void content.offsetHeight;
					content.style.height = content.scrollHeight + 'px';
					var onOpen = function (ev) {
						if (ev.propertyName !== 'height') return;
						content.removeEventListener('transitionend', onOpen);
						content.style.height = 'auto';
					};
					content.addEventListener('transitionend', onOpen);
				}
			});
		});
	}

	/* Навигация: уплотнение/тень при скролле */
	function initNavScroll() {
		var nav = document.querySelector('.nav-pill');
		if (!nav) return;
		var onScroll = function () {
			nav.classList.toggle('scrolled', window.scrollY > 24);
		};
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* Витрина услуг: слайдер Swiper (1/2/3 в ряд) */
	function initShowcaseSlider() {
		if (typeof Swiper === 'undefined') return;
		var el = document.querySelector('.showcase-swiper');
		if (!el || el.classList.contains('inited')) return;
		el.classList.add('inited');
		new Swiper(el, {
			loop: true,
			speed: 600,
			spaceBetween: 20,
			slidesPerView: 1,
			navigation: { nextEl: '.showcase-next', prevEl: '.showcase-prev' },
			breakpoints: { 768: { slidesPerView: 2 }, 1200: { slidesPerView: 3 } }
		});
	}

	/* Контактная форма: склеиваем продукт+компанию в скрытое message для бэкенда */
	function initContactMerge() {
		var form = document.querySelector('form[data-form="contact"]');
		if (!form) return;
		form.addEventListener('submit', function () {
			var msg = form.querySelector('#contact_message_hidden');
			var prod = form.querySelector('#contact_product');
			var comp = form.querySelector('#contact_company');
			if (!msg) return;
			var parts = [];
			if (prod && prod.value) parts.push('Услуга: ' + prod.value);
			if (comp && comp.value) parts.push('Компания: ' + comp.value);
			if (parts.length) msg.value = parts.join('; ');
		});
	}

	/* Маска телефона +7 (___) ___-__-__ */
	function initPhoneMask() {
		var phoneInput = document.getElementById('phone');
		if (!phoneInput) return;
		phoneInput.addEventListener('focus', function () {
			if (!this.value) this.value = '+7 ';
		});
		phoneInput.addEventListener('input', function () {
			var x = this.value.replace(/\D/g, '').substring(0, 11);
			if (x.length === 0) { this.value = ''; return; }
			var formatted = '+7 ';
			if (x.length > 1) formatted += '(' + x.substring(1, 4);
			if (x.length >= 4) formatted += ') ' + x.substring(4, 7);
			if (x.length >= 7) formatted += '-' + x.substring(7, 9);
			if (x.length >= 9) formatted += '-' + x.substring(9, 11);
			this.value = formatted;
		});
	}

	/* Ленты-витрины: листание стрелками (колесо отдано странице) */
	function initStripSliders() {
		function bind(stripSel, prevSel, nextSel) {
			var strip = document.querySelector(stripSel);
			if (!strip) return;
			var prev = document.querySelector(prevSel);
			var next = document.querySelector(nextSel);
			function step() {
				var first = strip.querySelector(':scope > *');
				var w = first ? first.getBoundingClientRect().width + 20 : 340;
				return w;
			}
			function go(dir) {
				strip.scrollBy({ left: dir * step(), behavior: 'smooth' });
			}
			if (prev) prev.addEventListener('click', function () { go(-1); });
			if (next) next.addEventListener('click', function () { go(1); });
		}
		bind('.show-strip', '#showPrev', '#showNext');
		bind('.pluses-strip', '#plusPrev', '#plusNext');
	}

	/* Плавный скролл (Lenis): масляное скольжение + мягкие якоря */
	function initSmoothScroll() {
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
		if (typeof Lenis === 'undefined') return;
		var lenis = new Lenis({ lerp: 0.2, smoothWheel: true });
		window.__lenis = lenis;
		function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
		requestAnimationFrame(raf);
		document.querySelectorAll('a[href^="#"]').forEach(function (a) {
			a.addEventListener('click', function (e) {
				var id = a.getAttribute('href');
				if (id && id.length > 1) {
					var t = document.querySelector(id);
					if (t) { e.preventDefault(); lenis.scrollTo(t, { offset: -70 }); }
				}
			});
		});
	}

	/* Тёмная сцена: карусель (автоплей, драг, точки, клавиши, колесо с клампом) */
	function initFssCarousel() {
		var sec = document.getElementById('showcase');
		if (!sec) return;
		var stage = sec.querySelector('#fssStage2');
		var list = sec.querySelector('.fss-cards');
		if (!stage || !list) return;
		var cards = Array.prototype.slice.call(list.children);
		var dots = Array.prototype.slice.call(sec.querySelectorAll('.fss-dot'));
		if (!cards.length) return;
		var geo = [
			{ x: 250, y: 430, angle: -10 },
			{ x: 750, y: 380, angle: -3 },
			{ x: 1250, y: 380, angle: 3 },
			{ x: 1750, y: 430, angle: 10 }
		];
		var n = cards.length;
		var mod = function (v) { return ((v % n) + n) % n; };
		var offset = 0, busy = false, hovered = false, visible = true;
		var startX = null, dragged = false, resumeT = null;
		var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
		function lenisStop() { if (window.__lenis && window.__lenis.stop) { try { window.__lenis.stop(); } catch (e) {} } }
		function lenisStart() { if (window.__lenis && window.__lenis.start) { try { window.__lenis.start(); } catch (e) {} } }
		function pose(i) { var p = geo[i % geo.length]; return 'translate(' + (p.x - 180) + 'px,' + (p.y - 312.5) + 'px) rotate(' + p.angle + 'deg)'; }
		function paint(i) { dots.forEach(function (d, k) { d.setAttribute('aria-current', String(k === mod(i))); }); }
		function advance(step, wrap) {
			if (busy || sec.clientWidth < 768) return false;
			var raw = offset + step;
			if (!wrap && (raw < 0 || raw > n - 1)) return false;
			busy = true;
			var old = offset;
			offset = mod(raw);
			var anims = cards.map(function (card, i) {
				var from = mod(i - old), to = mod(i - offset);
				var a = geo[from % geo.length], b = geo[to % geo.length], frames;
				if (Math.abs(to - from) > 2) {
					var dir = step > 0 ? -1 : 1;
					frames = [
						{ transform: pose(from), opacity: 1, offset: 0 },
						{ transform: 'translate(' + (a.x - 180 + dir * 480) + 'px,' + (a.y - 250) + 'px) rotate(' + (dir * 25) + 'deg)', opacity: 0, offset: .46 },
						{ transform: 'translate(' + (b.x - 180 - dir * 480) + 'px,' + (b.y - 250) + 'px) rotate(' + (-dir * 25) + 'deg)', opacity: 0, offset: .54 },
						{ transform: pose(to), opacity: 1, offset: 1 }
					];
				} else {
					frames = [
						{ transform: pose(from) },
						{ transform: 'translate(' + ((a.x + b.x) / 2 - 180) + 'px,' + ((a.y + b.y) / 2 - 330) + 'px) rotate(' + ((a.angle + b.angle) / 2) + 'deg)' },
						{ transform: pose(to) }
					];
				}
				card.style.transform = pose(to);
				if (card.animate) {
					return card.animate(frames, { duration: reduced.matches ? 0 : 950, easing: 'cubic-bezier(.22,.61,.36,1)' }).finished.catch(function () {});
				}
				return null;
			});
			paint(offset);
			Promise.all(anims).then(function () { busy = false; });
			return true;
		}
		function poseNow(i) { return pose(mod(i - offset)); }
		function dragTo(dx) {
			cards.forEach(function (card, i) {
				card.style.transform = poseNow(i) + ' translateX(' + dx + 'px)';
			});
		}
		list.addEventListener('pointerdown', function (e) {
			if (sec.clientWidth < 768) return;
			startX = e.clientX;
			dragged = false;
		});
		window.addEventListener('pointermove', function (e) {
			if (startX === null || sec.clientWidth < 768) return;
			var dx = e.clientX - startX;
			if (Math.abs(dx) > 8) dragged = true;
			dragTo(dx);
		}, { passive: true });
		window.addEventListener('pointerup', function (e) {
			if (startX === null) return;
			var dx = e.clientX - startX;
			startX = null;
			if (Math.abs(dx) > 80) {
				advance(dx < 0 ? 1 : -1, true);
				setTimeout(function () { dragged = false; }, 0);
			} else {
				var wasDragged = dragged;
				dragged = false;
				cards.forEach(function (card, i) {
					var from = card.style.transform || poseNow(i);
					var to = poseNow(i);
					card.style.transform = to;
					if (card.animate && from !== to) {
						try { card.animate([{ transform: from }, { transform: to }], { duration: 220, easing: 'ease-out' }); } catch (err) {}
					}
				});
				if (wasDragged) setTimeout(function () { dragged = false; }, 0);
			}
		});
		window.addEventListener('pointercancel', function () {
			startX = null;
			dragged = false;
			cards.forEach(function (card, i) { card.style.transform = poseNow(i); });
		});
		cards.forEach(function (card, i) {
			card.addEventListener('click', function () {
				if (dragged) { dragged = false; return; }
				var delta = mod(i - offset) - 2;
				if (delta) advance(delta > 0 ? 1 : -1, true);
			});
			card.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') { e.preventDefault(); advance(e.key === 'ArrowRight' ? 1 : -1, true); }
			});
		});
		dots.forEach(function (dot, i) {
			dot.addEventListener('click', function () {
				if (sec.clientWidth < 768) {
					var card = cards[i];
					if (card) list.scrollTo({ left: card.offsetLeft - (list.clientWidth - card.clientWidth) / 2, behavior: reduced.matches ? 'auto' : 'smooth' });
				} else {
					var delta = mod(i - offset) - 2;
					if (delta) advance(delta > 0 ? 1 : -1, true);
					else advance(1, true);
				}
			});
		});
		sec.addEventListener('pointerenter', function () { hovered = true; });
		sec.addEventListener('pointerleave', function () { hovered = false; lenisStart(); });
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) { visible = entries[0].isIntersecting; }).observe(sec);
		}
		setInterval(function () {
			if (!hovered && visible && !document.hidden && !reduced.matches && !sec.contains(document.activeElement)) advance(1, true);
		}, 5500);
		function resize() {
			var w = sec.clientWidth;
			var pw = stage.parentElement ? stage.parentElement.clientWidth : w;
			if (w < 768) {
				cards.forEach(function (c) {
					if (c.getAnimations) c.getAnimations().forEach(function (a) { a.cancel(); });
					c.style.removeProperty('transform');
				});
				offset = 0;
				paint(0);
				stage.style.removeProperty('height');
			} else {
				stage.style.setProperty('--fss-scale', pw / 2048);
				stage.style.height = (780 * pw / 2048) + 'px';
			}
		}
		if ('ResizeObserver' in window) new ResizeObserver(resize).observe(sec);
		resize();
		stage.classList.add('fss-ready');
		var sched = false;
		list.addEventListener('scroll', function () {
			if (sched) return;
			sched = true;
			requestAnimationFrame(function () {
				sched = false;
				var center = list.scrollLeft + list.clientWidth / 2;
				var nearest = 0, dist = Infinity;
				cards.forEach(function (card, i) {
					var d = Math.abs(card.offsetLeft + card.clientWidth / 2 - center);
					if (d < dist) { dist = d; nearest = i; }
				});
				paint(nearest);
			});
		}, { passive: true });
	}

	/* Этапы: parallax чётных карточек и центра за скроллом */
	function initStepsDrift() {
		if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
		var sec = document.getElementById('process');
		if (!sec) return;
		var grid = sec.querySelector('.steps3');
		if (!grid) return;
		var evens = [];
		var mid = null;
		Array.prototype.forEach.call(grid.children, function (el, idx) {
			if (!el.classList || !el.classList.contains('step3')) return;
			if (idx % 2 === 1) evens.push(el);
			if (idx === 2) mid = el;
		});
		if (!evens.length) return;
		function update() {
			if (window.innerWidth < 900) {
				evens.forEach(function (c) { c.style.transform = ''; });
				if (mid) mid.style.transform = '';
				return;
			}
			var r = sec.getBoundingClientRect();
			var h = window.innerHeight;
			var total = h + r.height;
			var p = (h - r.top) / total;
			p = p < 0 ? 0 : (p > 1 ? 1 : p);
			var t = 'translate3d(0,' + Math.round(p * 120) + 'px,0)';
			var t3 = 'translate3d(0,' + Math.round(p * 60) + 'px,0)';
			evens.forEach(function (c) { c.style.transform = t; });
			if (mid) mid.style.transform = t3;
		}
		var ticking = false;
		function onScroll() {
			if (ticking) return;
			ticking = true;
			requestAnimationFrame(function () { ticking = false; update(); });
		}
		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	function init() {
		initViewElements();
		initModalHandlers();
		initWorksSlider();
		initFaqAccordion();
		initNavScroll();
		initStepsDrift();
		initShowcaseSlider();
		initContactMerge();
		initPhoneMask();
		initSmoothScroll();
		initStripSliders();
		initFssCarousel();
	}
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
	else init();
})();
