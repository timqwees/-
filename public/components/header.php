<?php
// Header — единый компонент для ВСЕХ страниц (стили в CSS, логика в animations.js → initHeader).
//
// Контракт шапки:
//   • прозрачная (поверх hero) — на страницах с полноэкранным тёмным hero:
//     data-nav-mode="transparent" (главная, объект /portfolio/{slug}, услуга /services/{slug},
//     каталог услуг /uslugi, о нас /about);
//   • тёмный фон — на страницах со светлым фоном: data-nav-mode="solid";
//   • внутренние цвета заданы локальными переменными --h-*, поэтому шапка
//     выглядит одинаково на любой странице (не зависит от подключённых CSS).
$__hasUrl = function_exists('seo_url');
$__n = ($__hasUrl ? (string)seo_url() : '/') ?: '/';
$__act = function (bool $on): string { return $on ? ' is-active' : ''; };
// Главная определяется по реальному файлу страницы (а не только по URL):
// так прозрачная шапка не «включится» на 404/500 и любых служебных страницах.
$__pf = str_replace('\\', '/', (string)($GLOBALS['fss_page_file'] ?? ''));
$__isHomeFile = $__pf !== '' && str_ends_with($__pf, '/home.php');
$__onHome = $__isHomeFile || ($__pf === '' && $__hasUrl && $__n === '/');
// Режим шапки. Страница с полноэкранным hero переключает его явно —
// до подключения шапки: $GLOBALS['fss_nav_mode'] = 'transparent'.
// Главная работает без флага (определяется по файлу страницы).
$__forced = strtolower(trim((string)($GLOBALS['fss_nav_mode'] ?? '')));
$__navMode = in_array($__forced, ['transparent', 'solid'], true)
	? $__forced
	: ($__onHome ? 'transparent' : 'solid');
?>
<header id="site-header" data-header="" data-nav-mode="<?= $__navMode ?>">
	<!-- Полоса прогресса скролла (ProMaintenance site-progress) -->
	<div class="site-progress" aria-hidden="true"></div>

	<div class="site-navigation" data-header-bar="">
		<!-- Brand -->
		<a href="/" class="brand" aria-label="ФАВОРИТ-СТРОЙ-СНАБ — на главную">
			<img src="/public/assets/image/logo/favicon/logo-64.png" alt="" loading="lazy" decoding="async" width="28" height="34">
			<span>ФСС</span>
		</a>

		<!-- Desktop Navigation -->
		<nav aria-label="Primary" class="desktop-nav">
			<a href="/" class="<?= $__act($__onHome) ?>"<?= $__onHome ? ' aria-current="page"' : '' ?>>Главная</a>
			<a href="/portfolio" class="<?= $__act(!$__onHome && $__n === '/portfolio') ?>"<?= $__act(!$__onHome && $__n === '/portfolio') ? ' aria-current="page"' : '' ?>>Проекты</a>
			<a href="/uslugi" class="<?= $__act(!$__onHome && ($__n === '/uslugi' || str_starts_with($__n, '/services/'))) ?>"<?= (!$__onHome && ($__n === '/uslugi' || str_starts_with($__n, '/services/'))) ? ' aria-current="page"' : '' ?>>Услуги</a>
			<a href="/about" class="<?= $__act(!$__onHome && $__n === '/about') ?>"<?= (!$__onHome && $__n === '/about') ? ' aria-current="page"' : '' ?>>О нас</a>
			<a href="/contacts" class="<?= $__act(!$__onHome && $__n === '/contacts') ?>"<?= (!$__onHome && $__n === '/contacts') ? ' aria-current="page"' : '' ?>>Контакты</a>
		</nav>

		<!-- CTA -->
		<a href="/#contact" class="nav-enquire" data-goal="header_enquiry">Оставить заявку
			<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
				<path d="M7 7h10v10"></path>
				<path d="M7 17 17 7"></path>
			</svg>
		</a>

		<!-- Mobile Burger -->
		<button id="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-panel">
			<span data-nav-label>Меню</span>
			<span data-nav-open>
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M4 5h16"></path>
					<path d="M4 12h16"></path>
					<path d="M4 19h16"></path>
				</svg>
			</span>
			<span data-nav-close class="hidden">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M18 6 6 18"></path>
					<path d="m6 6 12 12"></path>
				</svg>
			</span>
		</button>
	</div>

	<!-- Mobile Panel -->
	<div id="nav-panel" hidden>
		<nav aria-label="Primary, mobile">
			<a href="/"><span>01</span>Главная<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
			<a href="/portfolio"><span>02</span>Проекты<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
			<a href="/uslugi"><span>03</span>Услуги<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
			<a href="/about"><span>04</span>О нас<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
			<a href="/contacts"><span>05</span>Контакты<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
		</nav>
		<a href="/#contact" class="button">Оставить заявку <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
		<p>Строительство · Производство · Монтаж.<br>Москва и область. Работаем по России.</p>
	</div>
</header>
<style>
	/* ============================================================
	   HEADER — единый компонент для всех страниц (ProMaintenance)
	   ============================================================ */
	:root {
		/* Высота шапки (90 / 78 / 74) — общий контракт с контентом страниц */
		--fss-header-h: 90px;
		/* Локальная палитра шапки: одинакова на всех страницах */
		--h-paper: #f0efed;
		--h-ink: #171310;
		--h-muted: #b2ada8;
		--h-line: #ffffff29;
		--h-accent: #c7ab94;
		--h-ease: cubic-bezier(.2, .8, .2, 1);
		--h-radius: 6px;
	}

	@media (width<=992px) {
		:root {
			--fss-header-h: 78px;
		}
	}

	@media (width<=576px) {
		:root {
			--fss-header-h: 74px;
		}
	}

	#site-header {
		z-index: 90;
		position: fixed;
		inset: 0 0 auto;
	}

	/* Полоса прогресса скролла (ProMaintenance site-progress) */
	.site-progress {
		z-index: 101;
		background: var(--h-accent);
		height: 2px;
		transform: scaleX(var(--scroll-progress, 0));
		transform-origin: 0;
		pointer-events: none;
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
	}

	/* Базовая (тёмная) шапка — все страницы, кроме главной */
	.site-navigation {
		height: var(--fss-header-h);
		color: var(--h-paper);
		backdrop-filter: blur(24px);
		background: #171718db;
		border-bottom: 1px solid var(--h-line);
		justify-content: space-between;
		align-items: center;
		gap: 30px;
		padding-inline: clamp(24px, 4.4vw, 96px);
		transition: background .3s, color .3s, border-color .3s, backdrop-filter .3s;
		display: flex;
	}

	/* Прозрачная шапка — страницы с полноэкранным hero (hero просвечивает).
	   Режим ставит PHP: data-nav-mode="transparent" (главная, объект, услуга, /uslugi, /about).
	   Значения --nav-opacity/--nav-blur заданы явно: пока скрипт не отработал
	   (или упал), шапка остаётся прозрачной, а не «тёмной плашкой» из-за
	   fallback'а .82 в правиле для data-stuck. */
	#site-header[data-nav-mode="transparent"] .site-navigation,
	[data-page=home] .site-navigation {
		--nav-opacity: .02;
		--nav-blur: 1px;
		background: rgb(23 23 24 / var(--nav-opacity, .02));
		color: #fff;
		backdrop-filter: blur(var(--nav-blur, 1px));
		border-bottom-color: var(--h-line);
	}

	/* Прокрутили главную — шапка «прилипает».
	   Фон и блюр берутся из тех же переменных (--nav-opacity/--nav-blur),
   иначе при возврате вверх шапка оставалась серой «плашкой» до самого верха. */
	#site-header[data-nav-mode="transparent"] .site-navigation[data-stuck],
	[data-page=home] .site-navigation[data-stuck] {
		--nav-opacity: .82;
		--nav-blur: 24px;
		color: #fff;
		background: rgb(23 23 24 / var(--nav-opacity, .82));
		backdrop-filter: blur(var(--nav-blur, 24px));
		border-bottom-color: var(--h-line);
	}

	.brand {
		flex-shrink: 0;
		align-items: center;
		gap: 10px;
		display: flex;
	}

	.brand>img {
		filter: invert();
		mix-blend-mode: screen;
		border-radius: 8px;
		width: auto;
		height: 30px;
	}

	.brand>span {
		letter-spacing: -.045em;
		font-size: 24px;
		font-weight: 600;
	}

	.desktop-nav {
		gap: 34px;
		display: flex;
	}

	.desktop-nav a {
		padding-block: 8px;
		font-size: 12px;
		position: relative;
	}

	.desktop-nav a:after {
		content: "";
		transform-origin: 100%;
		height: 1px;
		transition: transform .5s var(--h-ease);
		background: currentColor;
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		transform: scaleX(0);
	}

	.desktop-nav a:hover:after,
	.desktop-nav a[aria-current]:after {
		transform-origin: 0;
		transform: scaleX(1);
	}

	.nav-enquire {
		border-bottom: 1px solid;
		align-items: center;
		gap: 28px;
		padding-block: 8px;
		font-size: 12px;
		display: flex;
	}

	#nav-toggle {
		align-items: center;
		gap: 14px;
		font-size: 12px;
		display: none;
		background: none;
		border: none;
		color: inherit;
		cursor: pointer;
	}

	#nav-panel {
		--line: var(--h-line);
		--muted: var(--h-muted);
		backdrop-filter: blur(28px);
		color: var(--h-paper);
		background: #181819f0;
		max-height: calc(100svh - var(--fss-header-h));
		padding: 12px 24px 32px;
		overflow-y: auto;
		box-shadow: 0 24px 40px #1b1c1d15;
		position: fixed;
		top: var(--fss-header-h);
		left: 0;
		right: 0;
		z-index: 99;
	}

	#nav-panel[hidden] {
		display: none;
	}

	#nav-panel nav a {
		letter-spacing: -.04em;
		border-bottom: 1px solid var(--line);
		align-items: center;
		gap: 22px;
		padding: 16px 0;
		font-size: 30px;
		display: flex;
	}

	#nav-panel nav a>span {
		letter-spacing: 0;
		color: var(--muted);
		font-size: 10px;
	}

	#nav-panel nav a svg {
		margin-left: auto;
	}

	#nav-panel>.button {
		width: 100%;
		margin-top: 24px;
	}

	#nav-panel>p {
		color: var(--muted);
		margin-top: 24px;
		font-size: 11px;
	}

	/* Кнопки (ProMaintenance .button / .pill) */
	.button {
		background: var(--h-ink, #171310);
		color: #fff;
		border-radius: var(--h-radius, 6px);
		border: 1px solid transparent;
		justify-content: space-between;
		align-items: center;
		gap: 35px;
		min-height: 52px;
		padding: 15px 22px;
		font-size: 13px;
		font-weight: 500;
		line-height: 1.2;
		transition: background .3s, color .3s;
		display: inline-flex;
		text-decoration: none;
	}

	.button:hover {
		background: #424244;
	}

	.button svg {
		transition: transform .4s var(--h-ease);
		flex-shrink: 0;
	}

	.button:hover svg {
		transform: translate(3px, -3px);
	}

	#nav-panel>.button {
		color: var(--h-ink, #171310);
		background: #f0efed;
	}

	#nav-panel>.button:hover {
		background: #fff;
	}

	@media (width<=992px) {
		.desktop-nav {
			display: none;
		}

		#nav-toggle {
			display: flex;
		}

		.nav-enquire {
			margin-left: auto;
		}
	}

	@media (width<=576px) {
		.brand>span {
			font-size: 19px;
		}

		.brand>img {
			height: 25px;
		}

		.nav-enquire {
			display: none;
		}
	}
</style>

<!-- Fallback ResizeObserver (нужен GSAP ScrollTrigger/SplitText) — ДО загрузки библиотек -->
<script>
	if (typeof window.ResizeObserver === 'undefined') {
		window.ResizeObserver = function () {
			this.observe = function () {};
			this.unobserve = function () {};
			this.disconnect = function () {};
		};
	}
</script>

<!-- Стек анимаций (ProMaintenance): Lenis + GSAP 3.15 + ScrollTrigger + SplitText -->
<script defer src="/public/assets/vendor/lenis/lenis.min.js"></script>
<script defer src="/public/assets/vendor/gsap/gsap.min.js"></script>
<script defer src="/public/assets/vendor/gsap/ScrollTrigger.min.js"></script>
<script defer src="/public/assets/vendor/gsap/SplitText.min.js"></script>
<script defer src="/public/assets/distJS/main.js"></script>
<script defer src="/public/assets/distJS/animations.js?v=<?= seo_ver() ?>"></script>
<!-- Подсказки по адресу «Где объект» — свой выпадающий список (ProMaintenance).
     Токен DaData не отдаём в браузер: подсказки запрашивает сервер
     POST /api/suggest/location, где ключ остаётся на стороне сервера. -->
<script defer src="/public/assets/distJS/hot-cities.js"></script>
<script defer src="/public/assets/distJS/location-suggest.js"></script>
