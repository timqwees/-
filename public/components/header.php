<?php $__n = function_exists('seo_url') ? seo_url() : '/'; ?>
		<header class="px-5 md:px-10 pt-5 relative z-[999] max-w-7xl">
			<nav class="nav-pill" aria-label="Быстрая навигация">
				<a href="/" class="nav-link<?= ($__n ?? '') === '/' ? ' active-link' : '' ?>">Главная</a><a href="/uslugi" class="nav-link hide-small<?= ($__n ?? '') === '/uslugi' || str_starts_with(($__n ?? ''), '/services/') ? ' active-link' : '' ?>">Услуги</a><a href="/#process" class="nav-link hide-small">Этапы</a>
				<a href="/" class="jc-logo" aria-label="ФАВОРИТ-СТРОЙ-СНАБ — на главную"><span class="jc-logo-badge">Ф</span><span class="logo-word">ФСС</span></a>
				<a href="/projects" class="nav-link hide-small<?= ($__n ?? '') === '/projects' ? ' active-link' : '' ?>">Каталог</a><a href="/portfolio" class="nav-link<?= ($__n ?? '') === '/portfolio' ? ' active-link' : '' ?>">Портфолио</a><a href="/#contact" class="nav-link">Контакты</a>
			</nav>
		</header>