<?php
// Шапка v6 (Loro): стеклянная пилюля по центру, underline-ссылки, чёрные круги, бургер.
$__n = function_exists('seo_url') ? seo_url() : '/';
$__act = function (bool $on): string { return $on ? ' is-active' : ''; };
$__tel = htmlspecialchars(preg_replace('/\D/', '', (string)seo_config('phone')));
?>
<header class="hd">
	<nav class="hd-bar hd-glass" aria-label="Быстрая навигация">
		<a href="/" class="hd-avatar" aria-label="ФАВОРИТ-СТРОЙ-СНАБ — на главную"><span>Ф</span></a>
		<div class="hd-mid" aria-hidden="true"><span>Смета за 15 минут</span></div>
		<div class="hd-links">
			<a href="/" class="hd-link<?= $__act(($__n ?? '') === '/') ?>">Главная</a><a href="/uslugi" class="hd-link<?= $__act(($__n ?? '') === '/uslugi' || str_starts_with(($__n ?? ''), '/services/')) ?>">Услуги</a><a href="/#process" class="hd-link">Этапы</a><a href="/portfolio" class="hd-link<?= $__act(($__n ?? '') === '/portfolio') ?>">Портфолио</a><a href="/about" class="hd-link<?= $__act(($__n ?? '') === '/about') ?>">О нас</a><a href="/#contact" class="hd-link">Обратится</a>
		</div>
		<a href="/#contact" class="hd-round" aria-label="Оставить заявку"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
		<a href="tel:<?= $__tel ?>" class="hd-round" aria-label="Позвонить"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg></a>
		<button class="hd-burger" type="button" aria-label="Открыть меню" aria-expanded="false"><span></span><span></span></button>
		<div class="hd-drop" id="hdDrop">
			<a href="/">Главная</a><a href="/uslugi">Услуги</a><a href="/#process">Этапы</a><a href="/portfolio">Портфолио</a><a href="/about">О нас</a><a href="/#contact">Обратится</a><a href="/#contact" class="hd-drop-cta">Оставить заявку</a>
		</div>
	</nav>
	<script>(function(){var b=document.querySelector('.hd-burger'),n=document.querySelector('.hd-bar');if(!b||!n)return;b.addEventListener('click',function(){var open=n.classList.toggle('hd-open');b.setAttribute('aria-expanded',open?'true':'false');});n.querySelectorAll('.hd-drop a').forEach(function(a){a.addEventListener('click',function(){n.classList.remove('hd-open');b.setAttribute('aria-expanded','false');});});})();</script>
</header>
