<?php
// Шапка v6 (Loro): стеклянная пилюля по центру, underline-ссылки, чёрные круги, бургер.
// Активный пункт: определяется по текущему URL (seo_url).
// На главной странице PHP-активность временно отключается для НЕ-hash пунктов,
// чтобы JS мог управлять активностью "Этапы"/"Обращение" без конфликтов.
$__n = (function_exists('seo_url') ? seo_url() : '/') ?: '/';
$__act = function (bool $on): string { return $on ? ' is-active' : ''; };
$__onHome = $__n === '/';
$__tel = htmlspecialchars((string) preg_replace( '/\D/', '', (string)seo_config('phone')));
?><header class="hd">
	<nav class="hd-bar hd-glass" aria-label="Быстрая навигация">
		<a href="/" class="hd-avatar" aria-label="ФАВОРИТ-СТРОЙ-СНАБ — на главную"><img src="/public/assets/image/logo/favicon.svg" alt="ФАВОРИТ-СТРОЙ-СНАБ" width="40" height="40" loading="eager" decoding="async"></a>
		<div class="hd-mid" aria-hidden="true"><span>СТРОЙ ФИРМА</span></div>
	<div class="hd-links" data-menu-root="<?= $__onHome ? 'home' : 'other' ?>">
		<a href="/" class="hd-link" data-menu-hash="home" data-menu-nav>Главная</a>
		<a href="/uslugi" class="hd-link<?= $__act(!$__onHome && ($__n === '/uslugi' || str_starts_with($__n, '/services/'))) ?>">Услуги</a>
		<a href="/#process" class="hd-link" data-menu-hash="process" data-menu-nav>Этапы</a>
		<a href="/portfolio" class="hd-link<?= $__act(!$__onHome && $__n === '/portfolio') ?>">Портфолио</a>
	<a href="/blog" class="hd-link<?= $__act(!$__onHome && str_starts_with($__n, '/blog')) ?>">Статьи</a>
		<a href="/about" class="hd-link<?= $__act(!$__onHome && $__n === '/about') ?>">О нас</a>
		<a href="/#contact" class="hd-link" data-menu-hash="contact" data-menu-nav>Связаться</a>
	</div>
		<a href="/#contact" class="hd-round" aria-label="Оставить заявку"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
		<a href="tel:<?= $__tel ?>" class="hd-round" aria-label="Позвонить"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></a>
		<button class="hd-burger" type="button" aria-label="Открыть меню" aria-expanded="false"><span></span><span></span></button>
	<div class="hd-drop" id="hdDrop">
		<a href="/" class="hd-drop-link" data-menu-hash="home" data-menu-nav>Главная</a>
		<a href="/uslugi" class="hd-drop-link<?= $__act(!$__onHome && ($__n === '/uslugi' || str_starts_with($__n, '/services/'))) ?>">Услуги</a>
		<a href="/#process" class="hd-drop-link" data-menu-hash="process" data-menu-nav>Этапы</a>
		<a href="/portfolio" class="hd-drop-link<?= $__act(!$__onHome && $__n === '/portfolio') ?>">Портфолио</a>
		<a href="/blog" class="hd-drop-link<?= $__act(!$__onHome && str_starts_with($__n, '/blog')) ?>">Статьи</a>
		<a href="/about" class="hd-drop-link<?= $__act(!$__onHome && $__n === '/about') ?>">О нас</a>
		<a href="/#contact" class="hd-drop-link" data-menu-nav data-menu-hash="contact">Обращение</a>
		<a href="/#contact" class="hd-drop-cta">Оставить заявку</a>
	</div>
	</nav>
	<script>
	(function(){
		"use strict";
		var root = document.querySelector('.hd-links');
		var isHomePage = root ? root.getAttribute('data-menu-root') === 'home' : false;

		function setActive(hash) {
			document.querySelectorAll('a[data-menu-nav]').forEach(function(a){
				if (a.getAttribute('data-menu-hash') === hash) {
					a.classList.add('is-active');
				} else {
					a.classList.remove('is-active');
				}
			});
		}

		// При загрузке: на главной если URL содержит #process или #contact — подсвечиваем
		if (isHomePage) {
			var hash = window.location.hash.replace('#', '');
			if (hash === 'process' || hash === 'contact') {
				setActive(hash);
			} else {
				// На главной без хеша — "Главная" active по умолчанию
				setActive('home');
			}
		}

		// При клике на hash-ссылку: активируем
		document.querySelectorAll('a[data-menu-nav]').forEach(function(a){
			a.addEventListener('click', function(){
				if (isHomePage) {
					setActive(a.getAttribute('data-menu-hash'));
				}
			});
		});

		// При клике на обычную ссылку (не hash): сбрасываем hash-пункты
		document.querySelectorAll('a[data-menu-nav]').forEach(function(a){
			a.addEventListener('click', function(e){
				// Не сбрасываем при клике на самих hash-пунктах
			});
		});
		document.querySelectorAll('.hd-links a:not([data-menu-nav]), .hd-drop a:not([data-menu-nav])').forEach(function(a){
			a.addEventListener('click', function(){
				setActive(null);
			});
		});

		// Бургер: мобильное меню
		var bar = document.querySelector('.hd-bar');
		var burger = document.querySelector('.hd-burger');
		if (bar && burger) {
			var setOpen = function(open){
				bar.classList.toggle('hd-open', open);
				burger.setAttribute('aria-expanded', open ? 'true' : 'false');
			};
			burger.addEventListener('click', function(e){
				e.stopPropagation();
				setOpen(!bar.classList.contains('hd-open'));
			});
			document.addEventListener('click', function(e){
				if (bar.classList.contains('hd-open') && !bar.contains(e.target)) setOpen(false);
			});
			document.addEventListener('keydown', function(e){
				if (e.key === 'Escape') setOpen(false);
			});
			document.querySelectorAll('.hd-drop a').forEach(function(a){
				a.addEventListener('click', function(){ setOpen(false); });
			});
		}
	})();
	</script>
</header>
