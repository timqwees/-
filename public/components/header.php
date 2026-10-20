<?php
// Header — точная копия ProMaintenance (стили в CSS, логика в animations.js → initHeader)
$__n = (function_exists('seo_url') ? seo_url() : '/') ?: '/';
$__act = function (bool $on): string { return $on ? ' is-active' : ''; };
$__onHome = $__n === '/';
?>
<header id="site-header" data-header="">
	<div class="site-navigation" data-header-bar="">
		<!-- Brand -->
		<a href="/" class="brand" aria-label="ФАВОРИТ-СТРОЙ-СНАБ — на главную">
			<img src="/public/assets/image/logo/favicon/favicon.svg" alt="" loading="lazy" decoding="async" width="28" height="34">
			<span>ФСС</span><span class="brand-plus">+</span>
		</a>

		<!-- Desktop Navigation -->
		<nav aria-label="Primary" class="desktop-nav">
			<a href="/portfolio" class="<?= $__act(!$__onHome && $__n === '/portfolio') ?>"<?= $__act(!$__onHome && $__n === '/portfolio') ? ' aria-current="page"' : '' ?>>Проекты</a>
			<a href="/uslugi" class="<?= $__act(!$__onHome && ($__n === '/uslugi' || str_starts_with($__n, '/services/'))) ?>"<?= (!$__onHome && ($__n === '/uslugi' || str_starts_with($__n, '/services/'))) ? ' aria-current="page"' : '' ?>>Услуги</a>
			<a href="/about" class="<?= $__act(!$__onHome && $__n === '/about') ?>"<?= (!$__onHome && $__n === '/about') ? ' aria-current="page"' : '' ?>>О нас</a>
			<a href="/#contact" class="<?= $__act(!$__onHome && $__n === '/contact') ?>"<?= (!$__onHome && $__n === '/contact') ? ' aria-current="page"' : '' ?>>Контакты</a>
		</nav>

		<!-- CTA -->
		<a href="/#contact" class="nav-enquire">Оставить заявку
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
			<a href="/portfolio"><span>01</span>Проекты<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
			<a href="/uslugi"><span>02</span>Услуги<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
			<a href="/about"><span>03</span>О нас<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
			<a href="/#contact"><span>04</span>Контакты<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
		</nav>
		<a href="/#contact" class="button">Оставить заявку <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
		<p>Строительство · Производство · Монтаж.<br>Москва и область. Работаем по России.</p>
	</div>
</header>
<style>
	/* ============================================================
	   HEADER — точная копия ProMaintenance
	   ============================================================ */
	#site-header {
		z-index: 90;
		position: fixed;
		inset: 0 0 auto;
	}

	.site-navigation {
		height: 90px;
		color: var(--paper, #f0efed);
		backdrop-filter: blur(24px);
		background: #171718db;
		border-bottom: 1px solid #0000;
		justify-content: space-between;
		align-items: center;
		gap: 30px;
		padding-inline: clamp(24px, 4.4vw, 96px);
		transition: background .5s, color .4s, border-color .4s;
		display: flex;
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
		width: auto;
		height: 30px;
	}

	.brand>span {
		letter-spacing: -.045em;
		font-size: 24px;
		font-weight: 600;
	}

	.brand-plus {
		vertical-align: super;
		margin-left: 4px;
		font-size: 10px;
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
		transition: transform .5s var(--ease, cubic-bezier(.2, .8, .2, 1));
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

	.site-navigation[data-stuck] {
		color: var(--paper, #f0efed);
		backdrop-filter: blur(24px);
		background: #171718d1;
		border-bottom-color: #ffffff29;
	}

	[data-page=home] .site-navigation {
		background: rgb(23 23 24 / var(--nav-opacity, .02));
		color: #fff;
		backdrop-filter: blur(var(--nav-blur, 1px));
		border-bottom-color: #ffffff29;
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
		--line: #ffffff29;
		--muted: #b2ada8;
		backdrop-filter: blur(28px);
		color: var(--paper, #f0efed);
		background: #181819f0;
		max-height: calc(100svh - 74px);
		padding: 12px 24px 32px;
		overflow-y: auto;
		box-shadow: 0 24px 40px #1b1c1d15;
		position: fixed;
		top: 74px;
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
		background: var(--ink, #1b1c1d);
		color: #fff;
		border-radius: var(--radius-control, 6px);
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
		transition: transform .4s var(--ease, cubic-bezier(.2, .8, .2, 1));
		flex-shrink: 0;
	}

	.button:hover svg {
		transform: translate(3px, -3px);
	}

	#nav-panel>.button {
		color: var(--ink, #1b1c1d);
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

		.site-navigation {
			height: 78px;
		}

		.nav-enquire {
			margin-left: auto;
		}
	}

	@media (width<=576px) {
		.site-navigation {
			gap: 15px;
			height: 74px;
		}

		.brand>span {
			font-size: 19px;
		}

		.brand>img {
			height: 25px;
		}

		.brand-plus {
			font-size: 8px;
		}

		.nav-enquire {
			display: none;
		}
	}
</style>
