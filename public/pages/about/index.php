<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

/* ============================================================
   /about — страница «О компании».
   Оформление — по дизайн-системе главной (ProMaintenance):
   тёмный герой, paper-секции, тонкие линии, трекированные подписи,
   крупная типографика, акцент — тёплый песочный (#c7ab94).
   Оранжевой гаммы в проекте больше нет.
   ============================================================ */

$inn = (string)seo_config('inn');
$ogrn = (string)seo_config('ogrn');
$kpp = (string)seo_config('kpp');
$ceo = (string)seo_config('ceo');
$founded = (string)seo_config('founded');
$foundedMarket = (string)seo_config('founded_market');
$regDate = (string)seo_config('registered_at');
$address = (array)seo_config('address');
$phone = (string)seo_config('phone');
$phoneDisplay = (string)seo_config('phone_display');

$t = 'О компании — ФАВОРИТ-СТРОЙ-СНАБ';
$d = 'ООО «ФАВОРИТ-СТРОЙ-СНАБ» (ИНН ' . $inn . ', ОГРН ' . $ogrn . ') — на рынке с ' . $foundedMarket . ' года (ООО с ' . $founded . '), поставка и монтаж БВЗ, фиксированная смета за 15 минут, сроки от 14 дней, гарантия 50+ лет.';

/* Почему выбирают — короткие тезисы в стиле дизайн-системы */
$why = [
	[
		'num' => '01',
		'title' => 'Собственное производство БВЗ',
		'text' => 'Завод быстровозводимых зданий и монтажная база — очередь и сроки не зависят от подрядчиков.',
	],
	[
		'num' => '02',
		'title' => 'Один подрядчик на весь цикл',
		'text' => 'Проектирование, производство, монтаж, инженерные сети и оснащение — по договору с фиксированной сметой.',
	],
	[
		'num' => '03',
		'title' => 'Работаем с силовыми и государственными структурами',
		'text' => 'Сотрудничество с Росгвардией, школы по стандартам Минпросвещения РФ, СРО, 44-ФЗ и 223-ФЗ.',
	],
];

/* Реализованные проекты и компетенции */
$projects = [
	[
		'num' => '01',
		'title' => 'Общежития 9 000+ м² в Сочи',
		'text' => 'Комплекс зданий общежитий в Адлерском районе: проектирование, строительство, инженерное оснащение и меблировка.',
		'link' => '/services/obchegitia',
		'link_text' => 'Общежития из модулей',
	],
	[
		'num' => '02',
		'title' => 'Капремонт 16 социальных объектов',
		'text' => 'Школы, спортивные залы и дворцы спорта: Гудермес, Аргун, Чечен-Аул, Шатура, Старогладовская, Гвардейское, Балансу и другие.',
		'link' => '/services/kapital',
		'link_text' => 'Капитальный ремонт',
	],
	[
		'num' => '03',
		'title' => 'Оснащение 13 школ',
		'text' => 'Мебель всех типов, оборудование кабинетов, лабораторные комплексы, мультимедийные и инженерные системы — по стандартам Минпросвещения РФ.',
		'link' => '/services/osnashchenie',
		'link_text' => 'Оснащение школ под ключ',
	],
	[
		'num' => '04',
		'title' => 'БПЛА, КИТ и высокотех оборудование',
		'text' => 'Поставка беспилотных летательных аппаратов, комплектующих и систем КИТ от ведущих производителей, адаптация под ТЗ.',
		'link' => '/services/bpla',
		'link_text' => 'Поставка БПЛА',
	],
	[
		'num' => '05',
		'title' => 'Мебель и оснащение под заказ',
		'text' => 'Функциональная, служебная и представительская мебель, индивидуальное проектирование и визуализация, разрешения и лицензии в сжатые сроки.',
		'link' => '/services/modultebel',
		'link_text' => 'Модульная мебель',
	],
	[
		'num' => '06',
		'title' => 'СРО, 44-ФЗ и 223-ФЗ',
		'text' => 'Допуск СРО на проектирование и строительство, работа по 44-ФЗ и 223-ФЗ, закрывающие пакеты документов по каждому объекту.',
		'link' => '/services/kapstroenie',
		'link_text' => 'Капитальное строительство',
	],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($t) ?></title>
	<meta name="description" content="<?= htmlspecialchars(seo_clip($d)) ?>">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#171310">
	<meta name="format-detection" content="telephone=yes">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="yandex-verification" content="27e0ba9980498150" />
	<link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-latin.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-cyrillic.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/fonts/WixMadeforDisplay/WixMadeforDisplay-Variable.woff2" crossorigin><link rel="stylesheet" href="/public/assets/vendor/fonts/fonts.css">
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/manifest.json" />
<?= seo_canonical_tags() ?>
<?= seo_verify_tags() ?>
<?= seo_og_tags($t, seo_clip($d)) ?>
	<link rel="search" type="application/opensearchdescription+xml" title="ФАВОРИТ-СТРОЙ-СНАБ" href="/opensearch.xml">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
	<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css"></noscript>
	<style> body { font-family: 'DM Sans Variable', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
	<link rel="preload" href="/public/assets/distJS/main.js?v=20261018" as="script">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<!-- Дизайн-система главной страницы (ProMaintenance) -->
	<link rel="stylesheet" href="/public/assets/distCSS/promaintenance.css?v=20261023">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<?= seo_analytics() ?>
	<?= seo_utm_script() ?>
	<?= seo_goals_script() ?>
	<style>
		/* ============================================================
		   /about — по дизайн-системе главной (ProMaintenance)
		   ============================================================ */

		/* ---------- HERO ---------- */
		.abt-hero {
			position: relative;
			background: #171310;
			color: #fff;
			overflow: hidden;
			display: flex;
			flex-direction: column;
			justify-content: flex-end;
			min-height: 620px;
		}

		.abt-hero__bg {
			position: absolute;
			inset: 0;
		}

		.abt-hero__bg img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.abt-hero__shade {
			position: absolute;
			inset: 0;
			background: linear-gradient(90deg, rgba(15, 14, 13, .93) 0%, rgba(15, 14, 13, .68) 48%, rgba(15, 14, 13, .38) 100%),
				linear-gradient(180deg, rgba(15, 14, 13, .88) 0%, rgba(15, 14, 13, 0) 42%);
		}

		.abt-hero__inner {
			position: relative;
			z-index: 1;
			width: 100%;
			padding-top: calc(var(--fss-header-h, 90px) + 72px);
			padding-bottom: 56px;
		}

		.abt-hero__eyebrow {
			color: rgba(255, 255, 255, .72);
		}

		.abt-hero h1 {
			margin: 24px 0 0;
			font-size: clamp(38px, 5.8vw, 92px);
			font-weight: 450;
			line-height: 1.02;
			letter-spacing: -.055em;
		}

		.abt-hero h1>span {
			display: block;
		}

		.abt-hero h1>span+span {
			color: rgba(240, 239, 237, .78);
		}

		.abt-hero h1 .accent-dot {
			display: inline;
		}

		.abt-hero__intro {
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			gap: 26px 46px;
			margin-top: 34px;
		}

		.abt-hero__intro p {
			margin: 0;
			max-width: 46ch;
			color: rgba(240, 239, 237, .74);
			font-size: 14px;
			line-height: 1.6;
		}

		.abt-hero__links {
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			gap: 14px 28px;
		}

		/* Кнопка на тёмном фоне — светлая, как на остальных внутренних страницах */
		.abt-hero .button {
			background: #fff;
			color: var(--ink);
		}

		.abt-hero .button:hover {
			background: var(--paper);
		}

		/* Хлебные крошки на тёмном фоне */
		.abt-hero .fss-bc-list {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			list-style: none;
			margin: 0 0 26px;
			padding: 0;
			font-size: 10px;
			letter-spacing: .1em;
			text-transform: uppercase;
		}

		.abt-hero .fss-bc-item {
			display: flex;
			align-items: center;
			color: rgba(255, 255, 255, .5);
		}

		.abt-hero .fss-bc-item:not(:last-child)::after {
			content: "/";
			margin-left: 8px;
			color: rgba(255, 255, 255, .28);
		}

		.abt-hero .fss-bc-link {
			color: rgba(255, 255, 255, .5);
			text-decoration: none;
			transition: color .25s;
		}

		.abt-hero .fss-bc-link:hover {
			color: var(--accent);
		}

		.abt-hero .fss-bc-current {
			color: rgba(255, 255, 255, .85);
		}

		/* ---------- ЦИФРЫ ---------- */
		.abt-facts {
			padding-top: clamp(48px, 5vw, 82px);
			padding-bottom: clamp(48px, 5vw, 82px);
		}

		.abt-stats {
			border-top: 1px solid var(--line);
			display: grid;
			grid-template-columns: repeat(5, minmax(0, 1fr));
		}

		.abt-stat {
			border-right: 1px solid var(--line);
			padding: 32px 28px 0 0;
			transition: color .35s;
		}

		.abt-stat:last-child {
			border-right: 0;
		}

		.abt-stat dd {
			margin: 0;
			letter-spacing: -.055em;
			font-weight: 450;
			font-size: clamp(38px, 4.6vw, 72px);
			line-height: 1;
		}

		.abt-stat dd>span {
			color: var(--accent);
			vertical-align: top;
			font-size: .46em;
			letter-spacing: 0;
		}

		.abt-stat dt {
			color: var(--muted);
			margin-top: 20px;
			font-size: 11px;
			letter-spacing: .08em;
			text-transform: uppercase;
		}

		.abt-stat:hover dt {
			color: var(--ink);
		}

		.abt-facts__note {
			color: var(--muted);
			margin-top: 34px;
			padding-top: 18px;
			border-top: 1px solid var(--line);
			font-size: 9px;
		}

		/* ---------- О КОМПАНИИ ---------- */
		.abt-about {
			padding-top: clamp(44px, 4vw, 64px);
			padding-bottom: clamp(64px, 7vw, 120px);
		}

		/* ---------- ПОЧЕМУ МЫ ---------- */
		.abt-why {
			padding-bottom: clamp(64px, 7vw, 120px);
		}

		.abt-why-grid {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 0 clamp(28px, 3vw, 56px);
		}

		.abt-why-card {
			border-top: 1px solid var(--ink);
			padding-top: 26px;
		}

		.abt-why-card .num {
			color: var(--accent);
			font-size: 10px;
			letter-spacing: .12em;
			font-weight: 600;
		}

		.abt-why-card h3 {
			margin: 18px 0 0;
			font-size: clamp(22px, 2.1vw, 32px);
			font-weight: 500;
			line-height: 1.12;
			letter-spacing: -.04em;
		}

		.abt-why-card p {
			color: var(--muted);
			margin-top: 14px;
			font-size: 14px;
			line-height: 1.65;
		}

		/* ---------- ЭКСПЕРТИЗА ---------- */
		.abt-exp {
			padding-bottom: clamp(64px, 7vw, 120px);
		}

		.abt-exp-grid {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 0 clamp(28px, 3vw, 56px);
		}

		.abt-exp-card {
			border-top: 1px solid var(--line);
			padding-top: 24px;
			padding-bottom: 30px;
		}

		.abt-exp-card .num {
			color: var(--muted);
			font-size: 10px;
			letter-spacing: .12em;
			font-weight: 600;
		}

		.abt-exp-card h3 {
			margin: 16px 0 0;
			font-size: clamp(19px, 1.7vw, 25px);
			font-weight: 500;
			line-height: 1.16;
			letter-spacing: -.035em;
		}

		.abt-exp-card p {
			color: var(--muted);
			margin-top: 12px;
			font-size: 14px;
			line-height: 1.65;
		}

		.abt-exp-card a {
			display: inline-flex;
			align-items: center;
			gap: 10px;
			margin-top: 18px;
			color: var(--ink);
			font-size: 12px;
			text-decoration: none;
			border-bottom: 1px solid var(--accent);
			padding-bottom: 3px;
			transition: color .25s, border-color .25s;
		}

		.abt-exp-card a:hover {
			color: var(--brand);
			border-color: var(--brand);
		}

		.abt-exp-card a svg {
			transition: transform .35s var(--ease);
		}

		.abt-exp-card a:hover svg {
			transform: translate(3px, -3px);
		}

		/* ---------- ТЁМНЫЙ БЛОК ---------- */
		.abt-dark {
			background: #1c1d1f;
			color: var(--paper);
			padding-block: clamp(64px, 7vw, 120px);
		}

		.abt-dark .section-topline {
			border-color: rgba(255, 255, 255, .2);
		}

		.abt-dark .section-topline .eyebrow {
			color: rgba(240, 239, 237, .6);
		}

		.abt-dark h2 {
			font-size: clamp(32px, 4.2vw, 64px);
			font-weight: 450;
			line-height: 1.06;
			letter-spacing: -.055em;
		}

		.abt-dark h2 span {
			color: #918b85;
		}

		.abt-dark__grid {
			display: grid;
			grid-template-columns: 1.25fr .75fr;
			gap: clamp(30px, 5vw, 90px);
			align-items: start;
		}

		.abt-dark__list {
			list-style: none;
			margin: 0;
			padding: 0;
		}

		.abt-dark__list li {
			border-bottom: 1px solid rgba(255, 255, 255, .12);
			padding: 20px 0;
			font-size: 14px;
			line-height: 1.6;
			color: rgba(240, 239, 237, .8);
		}

		.abt-dark__list li:first-child {
			border-top: 1px solid rgba(255, 255, 255, .12);
		}

		.abt-dark__list b {
			display: block;
			color: var(--paper);
			font-weight: 500;
			font-size: 15px;
			margin-bottom: 6px;
		}

		.abt-dark__side p {
			color: rgba(240, 239, 237, .62);
			font-size: 14px;
			line-height: 1.7;
		}

		.abt-dark__side .text-link {
			margin-top: 26px;
		}

		/* ---------- РЕКВИЗИТЫ ---------- */
		.abt-requ {
			padding-block: clamp(64px, 7vw, 120px);
		}

		.abt-requ-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 0 clamp(32px, 6vw, 110px);
		}

		.abt-requ-list {
			margin: 0;
			border-top: 1px solid var(--line);
		}

		.abt-requ-list>div {
			display: grid;
			grid-template-columns: 180px minmax(0, 1fr);
			gap: 12px 24px;
			border-bottom: 1px solid var(--line);
			padding: 18px 0;
		}

		.abt-requ-list dt {
			color: var(--muted);
			font-size: 11px;
			letter-spacing: .08em;
			text-transform: uppercase;
			padding-top: 3px;
		}

		.abt-requ-list dd {
			margin: 0;
			font-size: 15px;
			line-height: 1.6;
		}

		.abt-requ-list dd a {
			color: var(--ink);
			text-decoration: none;
			border-bottom: 1px solid var(--accent);
			transition: color .25s, border-color .25s;
		}

		.abt-requ-list dd a:hover {
			color: var(--brand);
			border-color: var(--brand);
		}

		.abt-requ-note {
			color: var(--muted);
			margin-top: 24px;
			font-size: 12px;
			line-height: 1.6;
		}

		.abt-requ-note a {
			color: var(--brand);
			text-decoration: none;
			border-bottom: 1px solid var(--accent);
		}

		.abt-requ-note a:hover {
			border-color: var(--brand);
		}

		/* ---------- АДАПТИВ ---------- */
		@media (max-width: 1100px) {
			.abt-stats {
				grid-template-columns: repeat(3, minmax(0, 1fr));
			}

			.abt-stat:nth-child(3) {
				border-right: 0;
			}

			.abt-why-grid,
			.abt-exp-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}
		}

		@media (max-width: 900px) {
			.abt-hero {
				min-height: 560px;
			}

			.abt-dark__grid {
				grid-template-columns: 1fr;
				gap: 34px;
			}

			.abt-requ-grid {
				grid-template-columns: 1fr;
				gap: 34px;
			}
		}

		@media (max-width: 640px) {
			.abt-hero__inner {
				padding-top: calc(var(--fss-header-h, 90px) + 44px);
				padding-bottom: 40px;
			}

			.abt-stats {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}

			.abt-stat {
				border-right: 0;
				padding-right: 0;
			}

			.abt-why-grid,
			.abt-exp-grid {
				grid-template-columns: 1fr;
			}

			.abt-requ-list>div {
				grid-template-columns: 1fr;
				gap: 6px;
			}
		}
	</style>
</head>
<body data-page="inner">
	<?php
	// Шапка прозрачная в начале: под ней полноэкранный hero «О нас» (как на главной).
	$GLOBALS['fss_nav_mode'] = 'transparent';
	include_once __DIR__ . '/../../components/header.php';
	?>
	<main>
		<!-- HERO (по дизайну главной) -->
		<section class="abt-hero" aria-labelledby="abt-title" data-nav-hero>
			<div class="abt-hero__bg" aria-hidden="true">
				<img src="/public/assets/image/Components/banner/hero.jpg.webp" alt="" loading="eager" fetchpriority="high" decoding="async" width="1672" height="941">
			</div>
			<div class="abt-hero__shade" aria-hidden="true"></div>
			<div class="abt-hero__inner section-shell">
				<?= seo_breadcrumbs_html() ?>
				<p class="eyebrow abt-hero__eyebrow">
					<span class="live-dot" aria-hidden="true"></span>
					О компании · на рынке с <?= htmlspecialchars($foundedMarket) ?> года
				</p>
				<h1 id="abt-title">
					<span>Производство, строительство</span>
					<span>и оснащение БВЗ<span class="accent-dot">.</span></span>
				</h1>
				<div class="abt-hero__intro">
					<p>ООО «ФАВОРИТ-СТРОЙ-СНАБ» — производитель и генеральный подрядчик быстровозводимых зданий. Собственное производство, монтаж, капремонт и оснащение — по договору с фиксированной сметой.</p>
					<div class="abt-hero__links">
						<a class="button" href="/#contact" data-goal="about_hero_cta">Обсудить проект
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
						<a class="text-link" href="/portfolio">Смотреть объекты
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				</div>
			</div>
		</section>

		<!-- КОМПАНИЯ В ЦИФРАХ -->
		<section class="abt-facts section-shell" aria-label="ФСС в цифрах">
			<div class="section-topline">
				<p class="eyebrow">01 / Компания в цифрах</p>
				<span class="eyebrow">Москва, область и вся Россия</span>
			</div>
			<dl class="abt-stats">
				<div class="abt-stat">
					<dd><?= htmlspecialchars((string)seo_config('market_years')) ?></dd>
					<dt>лет на рынке</dt>
				</div>
				<div class="abt-stat">
					<dd><?= htmlspecialchars((string)seo_config('objects')) ?></dd>
					<dt>сданных объектов</dt>
				</div>
				<div class="abt-stat">
					<dd><?= htmlspecialchars((string)seo_config('kapremont_objects')) ?></dd>
					<dt>объектов капремонта</dt>
				</div>
				<div class="abt-stat">
					<dd><?= htmlspecialchars((string)seo_config('schools_equipped')) ?></dd>
					<dt>школ оснащено</dt>
				</div>
				<div class="abt-stat">
					<dd><?= htmlspecialchars((string)seo_config('guarantee_years')) ?><span> лет</span></dd>
					<dt>гарантия на объекты</dt>
				</div>
			</dl>
			<p class="abt-facts__note eyebrow">Собственный завод БВЗ · Сроки от <?= htmlspecialchars((string)seo_config('deadline_days')) ?> дней · Смета за <?= htmlspecialchars((string)seo_config('price_response_minutes')) ?> минут · Работаем по 44-ФЗ и 223-ФЗ</p>
		</section>

		<!-- О КОМПАНИИ -->
		<section class="abt-about section-shell" aria-labelledby="abt-about-title">
			<div class="section-topline">
				<p class="eyebrow">02 / О компании</p>
				<span class="eyebrow">С <?= htmlspecialchars($foundedMarket) ?> года</span>
			</div>
			<div class="about-grid">
				<h2 id="abt-about-title">От фундамента<br>до сдачи объекта<br><span>под ключ.</span></h2>
				<div class="about-copy">
					<span class="about-symbol" aria-hidden="true">
						<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</span>
					<p>Работаем с <?= htmlspecialchars($foundedMarket) ?> года. В основе — <?= htmlspecialchars((string)seo_config('market_years')) ?> лет практики в быстровозводимом строительстве.</p>
					<p>Мы ценим, что вы рассматриваете ФАВОРИТ-СТРОЙ-СНАБ как стратегического партнёра. Команда создаёт решения под ваши задачи и сопровождает проект на всех этапах: от проектирования и производства модулей до монтажа, инженерных сетей и оснащения объекта.</p>
					<a href="/uslugi" class="text-link">Наши услуги
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</a>
				</div>
			</div>
		</section>

		<!-- ПОЧЕМУ ВЫБИРАЮТ -->
		<section class="abt-why section-shell" aria-labelledby="abt-why-title">
			<div class="section-topline">
				<p class="eyebrow">03 / Почему выбирают</p>
				<span class="eyebrow">Ваш партнёр на всём цикле</span>
			</div>
			<div class="head-row">
				<h2 id="abt-why-title">Три причины<br>работать с нами.</h2>
				<p>Мы не перепродаём чужое производство и не отдаём объект «на подрядчика».<br>За объектом стоит своя команда и свои мощности.</p>
			</div>
			<div class="abt-why-grid">
				<?php foreach ($why as $w): ?>
					<div class="abt-why-card">
						<div class="num"><?= htmlspecialchars($w['num']) ?></div>
						<h3><?= htmlspecialchars($w['title']) ?></h3>
						<p><?= htmlspecialchars($w['text']) ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- ЭКСПЕРТИЗА И РЕАЛИЗОВАННЫЕ ПРОЕКТЫ -->
		<section class="abt-exp section-shell" aria-labelledby="abt-exp-title">
			<div class="section-topline">
				<p class="eyebrow">04 / Экспертиза</p>
				<span class="eyebrow">Реализованные объекты</span>
			</div>
			<div class="head-row">
				<h2 id="abt-exp-title">Надёжность для<br>силовых структур<br>и госзаказчиков.</h2>
				<p>В настоящее время компания сотрудничает с Федеральной службой войск национальной гвардии Российской Федерации.<br>Школы и соцобъекты — по стандартам Минпросвещения РФ.</p>
			</div>
			<div class="abt-exp-grid">
				<?php foreach ($projects as $p): ?>
					<div class="abt-exp-card">
						<div class="num"><?= htmlspecialchars($p['num']) ?></div>
						<h3><?= htmlspecialchars($p['title']) ?></h3>
						<p><?= htmlspecialchars($p['text']) ?></p>
						<a href="<?= htmlspecialchars($p['link']) ?>" title="<?= htmlspecialchars($p['link_text']) ?>"><?= htmlspecialchars($p['link_text']) ?>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- ТЁМНЫЙ БЛОК: ЗАКАЗЧИКИ И ГАРАНТИИ -->
		<section class="abt-dark" aria-labelledby="abt-partners-title">
			<div class="section-shell">
				<div class="section-topline">
					<p class="eyebrow">05 / Заказчики и гарантии</p>
					<span class="eyebrow">Москва · Россия</span>
				</div>
				<div class="abt-dark__grid">
					<div>
						<h2 id="abt-partners-title">Работаем там,<br>где важна <span>надёжность.</span></h2>
						<ul class="abt-dark__list">
							<li><b>Росгвардия</b>Поставка и монтаж быстровозводимых зданий для объектов Федеральной службы войск национальной гвардии.</li>
							<li><b>Минпросвещения РФ</b>Оснащение школ по федеральным стандартам: мебель, лабораторные комплексы, мультимедийные и инженерные системы.</li>
							<li><b>44-ФЗ и 223-ФЗ</b>Участие в государственных и корпоративных закупках, допуск СРО, полный пакет закрывающих документов.</li>
							<li><b>Гарантия <?= htmlspecialchars((string)seo_config('guarantee_years')) ?> лет</b>На конструкции, инженерные системы и смонтированное оборудование.</li>
						</ul>
					</div>
					<div class="abt-dark__side">
						<p>Данные подтверждены бухлгалтерской отчётностью ФНС и открытыми реестрами: регистрация с <?= htmlspecialchars($founded) ?> года, выручка 2025 — 521,7 млн руб., 17 контрактов на 815,4 млн руб. в системе госзакупок.</p>
						<a href="/portfolio#facts" class="text-link">Все показатели компании
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				</div>
			</div>
		</section>

		<!-- РЕКВИЗИТЫ -->
		<section class="abt-requ section-shell" aria-labelledby="abt-requ-title">
			<div class="section-topline">
				<p class="eyebrow">06 / Реквизиты</p>
				<span class="eyebrow">Юридические данные</span>
			</div>
			<div class="head-row">
				<h2 id="abt-requ-title">Юридические реквизиты<br><span>компании.</span></h2>
				<p>ООО «ФАВОРИТ-СТРОЙ-СНАБ» зарегистрировано <?= htmlspecialchars(date('d.m.Y', strtotime($regDate))) ?> г.<br>Данные актуальны и подтверждены выпиской из ЕГРЮЛ. По любым вопросам — <?= htmlspecialchars($phoneDisplay) ?>.</p>
			</div>
			<div class="abt-requ-grid">
				<dl class="abt-requ-list">
					<div>
						<dt>Полное наименование</dt>
						<dd>Общество с ограниченной ответственностью «ФАВОРИТ-СТРОЙ-СНАБ»</dd>
					</div>
					<div>
						<dt>Руководитель</dt>
						<dd><?= htmlspecialchars($ceo) ?></dd>
					</div>
					<div>
						<dt>ИНН / КПП</dt>
						<dd><?= htmlspecialchars($inn) ?> / <?= htmlspecialchars($kpp) ?></dd>
					</div>
					<div>
						<dt>ОГРН</dt>
						<dd><?= htmlspecialchars($ogrn) ?></dd>
					</div>
					<div>
						<dt>Дата регистрации</dt>
						<dd><?= htmlspecialchars(date('d.m.Y', strtotime($regDate))) ?> г.</dd>
					</div>
				</dl>
				<div>
					<dl class="abt-requ-list">
						<div>
							<dt>Адрес</dt>
							<dd><?= htmlspecialchars((string)($address['postal'] ?? '')) ?>, <?= htmlspecialchars((string)($address['locality'] ?? '')) ?>, <?= htmlspecialchars((string)($address['street'] ?? '')) ?></dd>
						</div>
						<div>
							<dt>Телефон</dt>
							<dd><a href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', $phone)) ?>" title="Позвонить"><?= htmlspecialchars($phoneDisplay) ?></a></dd>
						</div>
						<div>
							<dt>Email</dt>
							<dd><a href="mailto:<?= htmlspecialchars((string)seo_config('email_puny')) ?>" title="Написать на email"><?= htmlspecialchars((string)seo_config('email')) ?></a></dd>
						</div>
						<div>
							<dt>Регионы работы</dt>
							<dd><?= htmlspecialchars(implode(', ', array_slice((array)seo_config('area_served'), 0, 5))) ?> и вся Россия</dd>
						</div>
						<div>
							<dt>Сайт</dt>
							<dd><a href="<?= htmlspecialchars((string)seo_config('base_url')) ?>" title="ФАВОРИТ-СТРОЙ-СНАБ">фсс.рус</a></dd>
						</div>
					</dl>
					<p class="abt-requ-note">Все показатели компании — в таблице «Компания в цифрах» в разделе <a href="/portfolio#facts">Портфолио</a>.</p>
				</div>
			</div>
		</section>
	</main>

	<?php include_once __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
