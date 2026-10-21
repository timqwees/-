<?php
$site = Setting\Route\Functions\TheFunction::site();

$seo = Setting\Route\Functions\TheFunction::seo([
    'title' => 'ФАВОРИТ-СТРОЙ-СНАБ — быстровозводимые здания под ключ',
    'description' => 'Строительство БВЗ под ключ в Москве: склады, ангары, АБК, общежития. Капремонт, спецтехника. От 14 дней, гарантия 50 лет. Смета за 15 минут.',
    'keywords' => 'ФАВОРИТ-СТРОЙ-СНАБ, БВЗ, быстровозводимые здания, ангары, склады, АБК, капитальный ремонт, Москва',
    'image' => $site['shareImageUrl'],
    'url' => $site['baseUrl'] . '/',
    'type' => 'website',
    'pageType' => 'WebPage',
    'breadcrumbs' => [
        ['name' => 'Главная', 'url' => $site['baseUrl'] . '/'],
    ],
]);
// Сквозной номер раздела главной («01 / Наш опыт»): считается в порядке вывода,
// поэтому пропусков и дублей номеров не возникает.
$fssSec = function (string $label): string {
    static $n = 0;
    $n++;
    return str_pad((string)$n, 2, '0', STR_PAD_LEFT) . ' / ' . $label;
};
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?= htmlspecialchars($seo['title']); ?> | ФСС.РУС</title>
	<meta name="description" content="<?= htmlspecialchars($seo['description']); ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#876b55">
	<meta name="format-detection" content="telephone=yes">
	<meta name="yandex-verification" content="27e0ba9980498150" />
	<link rel="canonical" href="<?= htmlspecialchars($seo['canonical']); ?>">

	<?= seo_og_tags($seo['title'], $seo['description'], $seo['og']['image']) ?>

	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
		<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
	<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/manifest.json" />
	<link rel="mask-icon" href="/public/assets/image/logo/favicon/favicon-96x96.png" color="#876b55">
	<link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-latin.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-cyrillic.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/fonts/WixMadeforDisplay/WixMadeforDisplay-Variable.woff2" crossorigin><link rel="stylesheet" href="/public/assets/vendor/fonts/fonts.css">
	<style> body { font-family: 'DM Sans Variable', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
	<!-- Preload critical -->
	<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
	<link rel="preload" href="/public/assets/distJS/main.js" as="script">
	<!-- Styles -->
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<!-- Swiper -->
	<link rel="stylesheet" href="/public/assets/vendor/swiper/swiper-bundle.min.css" />
	<script defer src="/public/assets/vendor/swiper/swiper-bundle.min.js"></script>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/promaintenance.css?v=20261023">

<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
<?= seo_analytics() ?>
<?= seo_utm_script() ?>
<?= seo_goals_script() ?>

<style>
	@keyframes floatRightIcon {
			0% {
				transform: translateY(0) scale(.9) rotateZ(0deg);
			}

			50% {
				transform: translateY(-40px) scale(1.05) rotateZ(15deg);
			}

			100% {
				transform: translateY(0) scale(.9) rotateZ(0deg);
			}
		}

		@keyframes floatLeftIcon {
			0% {
				transform: translateY(0);
			}

			50% {
				transform: translateY(40px);
			}

			100% {
				transform: translateY(0);
			}
		}

		.banner-float-right {
			animation: floatRightIcon 6s ease-in-out infinite;
		}

		.banner-float-left {
			animation: floatLeftIcon 4s ease-in-out infinite;
		}

		/* Секция «Мы выполняем» — одинаковые скругления, паддинги и высота карточек */
		#advantages .services-grid > * {
			min-height: 180px;
			padding: 20px;
			border-radius: 30px;
		}

		/* Мобильная секция «Выберите услугу»: корректный свайп и размер карточек */
		@media (max-width: 767px) {
			#showcase .fss-cards { -webkit-overflow-scrolling: touch; }
			#showcase .fss-card {
				flex: 0 0 min(320px, calc(100vw - 72px));
				width: min(320px, calc(100vw - 72px));
				touch-action: auto;
			}
		}

		/* Мобильная секция «Этапы работы» — горизонтальный слайдер */
		.steps3-nav { display: none; }

		@media (max-width: 899px) {
			section#process > .relative.mx-auto { padding-bottom: 0; }
			#process .steps3 {
				display: flex;
				gap: 14px;
				margin-top: 30px;
				margin-left: -16px;
				margin-right: -16px;
				padding: 4px 16px 12px;
				overflow-x: auto;
				scroll-snap-type: x mandatory;
				scrollbar-width: none;
				-webkit-overflow-scrolling: touch;
				overscroll-behavior-x: contain;
			}
			#process .steps3::-webkit-scrollbar { display: none; }
			#process .steps3:not(.is-ready) .step3 { opacity: 1; transform: none; }
			#process .step3 {
				flex: 0 0 82%;
				max-width: 360px;
				scroll-snap-align: center;
				display: flex;
				flex-direction: column;
				padding: 14px 14px 18px;
				border-radius: 24px;
				text-align: left;
				background: #fff;
				border: 1px solid rgba(23, 19, 16, .06);
				box-shadow: 0 14px 34px rgba(23, 19, 16, .08);
				opacity: .72;
				transform: scale(.95);
				transform-origin: center bottom;
				transition: opacity .3s ease, transform .3s ease, box-shadow .3s ease;
			}
			#process .step3.is-active {
				opacity: 1;
				transform: scale(1);
				box-shadow: 0 18px 44px rgba(23, 19, 16, .14);
			}
			#process .step3-img {
				width: 100%;
				height: 175px;
				border-radius: 16px;
				object-fit: cover;
			}
			#process .step3 h3 {
				margin: 14px 0 0;
				font-size: 19px;
				line-height: 1.25;
				text-align: left;
			}
			#process .step3 p {
				margin: 8px 0 0;
				font-size: 14px;
				line-height: 1.55;
				flex: 1;
			}
			#process .step3-n {
				position: absolute;
				top: 14px;
				left: 14px;
				z-index: 2;
				margin: 0;
				width: 36px;
				height: 36px;
				display: flex;
				align-items: center;
				justify-content: center;
				border-radius: 50%;
				background: rgba(23, 19, 16, .82);
				color: #fff;
				font-size: 12px;
				font-weight: 700;
				letter-spacing: .02em;
				box-shadow: 0 6px 16px rgba(0, 0, 0, .22);
				-webkit-backdrop-filter: blur(6px);
				backdrop-filter: blur(6px);
			}
			#process .step3-photo {
				min-height: 0;
				padding: 0;
				background: #171310;
			}
			#process .step3-photo .step3-ovl {
				padding: 56px 18px 20px;
				text-align: left;
			}
			#process .step3-photo .step3-ovl h3 { color: #fff; }
			#process .step3-photo .step3-ovl p { color: rgba(255, 255, 255, .9); }
			#process .step3-photo .step3-n {
				position: static;
				display: inline-flex;
				width: auto;
				height: auto;
				margin: 12px 0 0;
				padding: 4px 10px;
				border-radius: 999px;
				background: rgba(255, 255, 255, .18);
				box-shadow: none;
				font-size: 11px;
				letter-spacing: .08em;
			}
			#process .steps3-nav {
				display: flex;
				align-items: center;
				justify-content: center;
				gap: 12px;
				margin-top: 16px;
			}
			#process .steps3-count {
				font-size: 13px;
				font-weight: 700;
				letter-spacing: .08em;
				color: #78716c;
			}
			#process .steps3-count b { color: #171310; font-size: 15px; }
			#process .steps3-dots { display: flex; align-items: center; gap: 7px; }
			#process .steps3-dot {
				width: 7px;
				height: 7px;
				padding: 0;
				border: 0;
				border-radius: 999px;
				background: rgba(23, 19, 16, .18);
				cursor: pointer;
				transition: width .25s ease, background .25s ease;
			}
			#process .steps3-dot.is-active {
				width: 22px;
				background: #876b55;
			}
		}
		@media (prefers-reduced-motion: reduce) {
			#process .step3 { transition: none; }
		}

		/* Cinema Hero Styles — exact Protenance copy.
		   Важно: базовые свойства hero заданы классами (не inline), иначе
		   медиазапросы не могут их переопределить и вёрстка «выезжает» на мобильных. */
		.hero {
			isolation: isolate;
			color: #fff;
			background: #363636;
			height: 100svh;
			min-height: 750px;
			max-height: 1200px;
			position: relative;
			overflow: hidden;
		}

		.hero-visual {
			position: absolute;
			inset: 0;
		}

		.cinema-image {
			object-fit: cover;
			object-position: 58% 50%;
			opacity: 0;
			width: 100%;
			height: 100%;
			position: absolute;
			transition: opacity 0.8s cubic-bezier(.2, .8, .2, 1);
		}

		.cinema-image.is-active {
			opacity: 1;
		}

		.hero-shade {
			pointer-events: none;
			position: absolute;
			inset: 0;
			background: linear-gradient(90deg, #0f0e0dad, #0f0e0d14 85%), linear-gradient(#0f0e0d2e, #0000 55%, #0f0e0dcc);
		}

		.hero-content {
			position: absolute;
			top: clamp(155px, 22vh, 260px);
			left: clamp(24px, 4.4vw, 96px);
			right: clamp(24px, 4.4vw, 96px);
		}

		.hero-eyebrow {
			color: #ffffffc7;
			letter-spacing: .12em;
			text-transform: uppercase;
			font-size: 10px;
			font-weight: 500;
			line-height: 1.5;
			display: flex;
			align-items: center;
			gap: 12px;
		}

		.hero h1 {
			letter-spacing: -.066em;
			text-wrap: initial;
			margin-top: 32px;
			font-size: clamp(56px, 8vw, 130px);
			font-weight: 450;
			line-height: .98;
			font-family: 'DM Sans Variable', Arial, sans-serif;
		}

		.hero h1>span {
			display: block;
		}

		.hero-intro {
			align-items: end;
			gap: 85px;
			margin-top: 40px;
			display: flex;
		}

		.hero-intro p {
			color: #fffc;
			font-size: 15px;
			line-height: 1.5;
		}

		.hero-intro .text-link {
			border-bottom: 1px solid;
			align-items: center;
			gap: 25px;
			padding-block: 10px;
			font-size: 11px;
			line-height: 1.4;
			display: inline-flex;
			border-color: #ffffff80;
		}

		.hero-bottom {
			grid-template-columns: 1.1fr 1fr auto;
			align-items: end;
			gap: 40px;
			display: grid;
			position: absolute;
			bottom: 32px;
			left: clamp(24px, 4.4vw, 96px);
			right: clamp(24px, 4.4vw, 96px);
		}

		.hero-project {
			backdrop-filter: blur(22px);
			border-radius: 8px;
			background: #16161794;
			border: 1px solid #ffffff4d;
			flex-direction: column;
			gap: 9px;
			padding: 24px 75px 24px 24px;
			transition: background .4s;
			display: flex;
			position: relative;
		}

		.hero-project:hover { background: #24211ecc; }
		.hero-project .eyebrow { opacity: .7; font-size: 8px; gap: 2px; align-items: center; display: flex; }
		.hero-project strong { letter-spacing: -.02em; font-size: 18px; font-weight: 500; }
		.hero-project>span[data-cinema-caption] { opacity: .7; font-size: 10px; }
		.hero-project-arrow { position: absolute; top: 50%; right: 24px; transform: translateY(-50%); }

		.hero-controls {
			padding-bottom: 16px;
			display: flex;
			flex-direction: column;
			gap: 16px;
		}

		.cinema-pagination { align-items: center; gap: 16px; display: flex; }
		.cinema-pagination button { opacity: .5; transition: opacity .3s; align-items: center; gap: 12px; min-height: 44px; font-size: 10px; display: flex; background: none; border: none; color: inherit; cursor: pointer; padding: 0; }
		.cinema-pagination button span { width: 20px; height: 1px; transition: width .6s cubic-bezier(.2, .8, .2, 1); background: #fff; display: block; }
		.cinema-pagination button[aria-pressed=true] { opacity: 1; }
		.cinema-pagination button[aria-pressed=true] span { width: 65px; }
		.hero-controls>.eyebrow { opacity: .65; font-size: 8px; }

		.hero-cta { color: #1b1c1d; border-radius: 8px; background: #f0efed; align-items: center; gap: 45px; margin-bottom: 15px; padding: 22px 26px; font-size: 12px; transition: background .3s; display: flex; }
		.hero-cta:hover { background: #fff; }
		.hero-cta svg { transition: transform .4s cubic-bezier(.2, .8, .2, 1); flex-shrink: 0; }
		.hero-cta:hover svg { transform: translate(2px, -2px); }

		@media (width>=1600px) {
			.hero h1 { font-size: 155px; }
			.hero-content { top: 24%; }
		}

		@media (width<=1100px) {
			.hero { min-height: 800px; }
			.hero h1 { font-size: 11vw; }
			.hero-content { top: 195px; }
			.hero-bottom { grid-template-columns: 1fr auto; gap: 25px; }
			.hero-controls { grid-column: 2; }
			.hero-cta { display: none; }
		}

		@media (width<=992px) {
			.hero-content { top: 190px; }
			.hero h1 { font-size: 11.7vw; }
			.hero-intro { gap: 45px; }
			.hero-controls { grid-column: 1; justify-content: space-between; align-items: center; gap: 15px; padding: 0; display: flex; flex-direction: row; }
			.hero-controls>.eyebrow { text-align: right; max-width: 90px; font-size: 7px; }
			.cinema-pagination { gap: 10px; }
			.cinema-pagination button { gap: 8px; font-size: 9px; }
			.cinema-pagination button[aria-pressed=true] span { width: 40px; }
			.cinema-pagination button span { width: 15px; }
			.hero-bottom { grid-template-columns: 1fr; gap: 12px; bottom: 22px; }
			.hero-project { padding: 18px 65px 18px 18px; }
			.hero-project strong { font-size: 17px; }
			.hero-cta { display: none; }
		}

		/* Мобильная версия: hero — поток (flex), без абсолютных блоков.
		   Так контент не наезжает на карточку проекта, не перекрывается
		   липкой панелью CTA и не «выезжает» на любой высоте экрана. */
		@media (width<=576px) {
			.hero {
				height: auto;
				min-height: 100svh;
				max-height: none;
				display: flex;
				flex-direction: column;
				justify-content: space-between;
				padding: calc(var(--fss-header-h, 74px) + 16px) 24px 104px;
			}

			.hero-content {
				position: static;
				top: auto;
				left: auto;
				right: auto;
			}

			.hero-eyebrow { letter-spacing: .06em; max-width: 260px; font-size: 8px; }
			.hero h1 { margin-top: 22px; font-size: 13vw; line-height: 1.04; }
			.hero-intro { margin-top: 22px; display: block; }
			.hero-intro p { font-size: 13px; }
			.hero-intro .text-link { margin-top: 14px; }
			.hero-visual .cinema-image { object-position: 62% 50%; }
			.hero-shade { background: linear-gradient(90deg, #0f0e0d99, #0f0e0d33), linear-gradient(#0000 30%, #0f0e0dcc); }
			.hero-bottom { position: static; bottom: auto; grid-template-columns: 1fr; gap: 14px; margin-top: 26px; }
			.hero-project { padding: 16px 56px 16px 16px; }
			.hero-project strong { font-size: 16px; }
			.hero-controls { flex-direction: row; justify-content: space-between; align-items: center; gap: 12px; padding: 0; }
			.hero-controls>.eyebrow { text-align: right; max-width: 110px; font-size: 7px; }
			.cinema-pagination { gap: 10px; }
			.cinema-pagination button { gap: 8px; font-size: 9px; }
			.cinema-pagination button[aria-pressed=true] span { width: 40px; }
			.cinema-pagination button span { width: 15px; }
			.hero-cta { display: none; }
		}

		/* Низкие экраны (сериф в один ряд, iPhone SE и т.п.): компактим hero,
		   чтобы блок проекта не заезжал под липкую панель CTA. */
		@media (width<=576px) and (height<=720px) {
			.hero { padding-top: calc(var(--fss-header-h, 74px) + 10px); padding-bottom: 96px; }
			.hero h1 { margin-top: 16px; font-size: 11.5vw; }
			.hero-intro { margin-top: 16px; }
			.hero-bottom { margin-top: 18px; gap: 12px; }
			.hero-project { padding: 14px 52px 14px 14px; }
			.hero-project>span[data-cinema-caption] { display: none; }
		}

		/* Очень низкие экраны (iPhone SE 320×568 и т.п.): дополнительно
		   уплотняем hero, чтобы блок проекта и пагинация не уходили
		   под липкую панель CTA уже на первом экране. */
		@media (width<=576px) and (height<=600px) {
			.hero { padding-top: calc(var(--fss-header-h, 74px) + 6px); padding-bottom: 92px; }
			.hero h1 { margin-top: 12px; font-size: 10.5vw; }
			.hero-intro { margin-top: 12px; }
			.hero-intro .text-link { margin-top: 8px; padding-block: 6px; }
			.hero-bottom { margin-top: 14px; gap: 10px; }
			.hero-project { padding: 12px 48px 12px 12px; }
		}

		@media (hover: hover) and (pointer:fine) and (prefers-reduced-motion:no-preference) {
			.hero-cta svg { transition: transform .4s cubic-bezier(.2,.8,.2,1); flex-shrink: 0; }
			.hero-cta:hover svg { transform: translate(2px,-2px); }
		}

		@media (prefers-reduced-motion:reduce) {
			.cinema-image { transition: none; }
			.cinema-pagination button span { transition: none; }
		}
	</style>
	<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
</head>

<body class="font-sans text-gray-800" data-page="home">

	<?php include_once __DIR__ . '/../components/header.php'; ?>

	<!-- CINEMA HERO — exact Protenance copy -->
	<section id="hero" class="hero" aria-labelledby="hero-title" data-cinema data-nav-hero>
		<div class="hero-visual" data-hero-frame>
			<img src="/public/assets/image/Components/banner/hero.jpg.webp" srcset="/public/assets/image/Components/banner/hero.jpg.webp 720w, /public/assets/image/Components/banner/hero.jpg.webp 1200w, /public/assets/image/Components/banner/hero.jpg.webp 1672w" alt="Комплекс общежитий 9 000+ м² в Адлерском районе Сочи" title="Комплекс общежитий 9 000+ м² в Адлерском районе Сочи" sizes="100vw" loading="eager" fetchpriority="high" data-cinema-image="0" decoding="async" width="1672" height="941" class="cinema-image is-active">
			<img src="/public/assets/image/swiper/2.jpg.webp" srcset="/public/assets/image/swiper/2.jpg.webp 720w, /public/assets/image/swiper/2.jpg.webp 1200w, /public/assets/image/swiper/2.jpg.webp 1672w" alt="Кабинет беспилотных авиационных систем: поставка БПЛА и комплектующих" title="Поставка БПЛА, комплектующих и систем КИТ" sizes="100vw" loading="lazy" fetchpriority="auto" data-cinema-image="1" decoding="async" width="1672" height="941" class="cinema-image">
			<img src="/public/assets/image/swiper/3.jpeg.webp" srcset="/public/assets/image/swiper/3.jpeg.webp 720w, /public/assets/image/swiper/3.jpeg.webp 1200w, /public/assets/image/swiper/3.jpeg.webp 1672w" alt="Капитальный ремонт школы в Гудермесе с техническим оснащением" title="Капремонт ГБОУ «Центр образования» в Гудермесе" sizes="100vw" loading="lazy" fetchpriority="auto" data-cinema-image="2" decoding="async" width="1672" height="941" class="cinema-image">
		</div>
		<div class="hero-shade" aria-hidden="true"></div>
		<div class="hero-content">
			<p class="eyebrow hero-eyebrow">
				<span class="live-dot" aria-hidden="true"></span>
				Строительство · Производство · Монтаж
			</p>
			<h1 id="hero-title">
				<span>Модульные здания</span>
				<span>нового поколения<span class="accent-dot">.</span></span>
			</h1>
			<div class="hero-intro">
				<p>От фундамента до сдачи.<br>Сила для каждого нового этапа.</p>
				<a class="text-link" href="#about" data-goal="hero_about">Узнать о ФСС <span>&#8595;</span></a>
			</div>
		</div>
		<div class="hero-bottom">
			<a href="/portfolio/obshchegiya-adler-sochi" class="hero-project" data-cinema-link>
				<span class="eyebrow">Избранный проект / <span data-cinema-number>01</span></span>
				<strong data-cinema-name>Общежития 9 000+ м² — Сочи</strong>
				<span data-cinema-caption>Краснодарский край / Общежития</span>
				<span class="hero-project-arrow">
					<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</span>
			</a>
			<div class="hero-controls">
				<div class="cinema-pagination" role="group" aria-label="Избранные проекты">
					<button type="button" data-cinema-select="0" data-name="Общежития 9 000+ м² — Сочи" data-caption="Краснодарский край / Общежития" data-project="obshchegiya-adler-sochi" aria-label="Показать комплекс общежитий в Сочи" aria-pressed="true">01<span></span></button>
					<button type="button" data-cinema-select="1" data-name="БПЛА и системы КИТ" data-caption="Поставка оборудования / БПЛА" data-project="bpla-kit" aria-label="Показать поставку БПЛА и комплектующих" aria-pressed="false">02<span></span></button>
					<button type="button" data-cinema-select="2" data-name="Капремонт школы — Гудермес" data-caption="Чеченская Республика / Капремонт" data-project="kapremont-gudermes" aria-label="Показать капремонт школы в Гудермесе" aria-pressed="false">03<span></span></button>
				</div>
				<p class="eyebrow" data-cinema-status aria-live="polite">01 / 03 — Общежития 9 000+ м² — Сочи</p>
			</div>
			<a href="/portfolio" class="hero-cta" data-goal="hero_projects">
				Изучить проекты
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M7 7h10v10"></path>
					<path d="M7 17 17 7"></path>
				</svg>
			</a>
		</div>
	</section>
	<!-- ABOUT (ProMaintenance about-section) -->
	<section id="about" class="about-section section-shell">
		<div class="section-topline">
			<p class="eyebrow"><?= $fssSec('Наш опыт') ?></p>
			<span class="eyebrow">Москва и область</span>
		</div>
		<div class="about-grid">
			<h2 data-reveal>Будущее не всегда<br>начинается<br><span>с земли.</span></h2>
			<div class="about-copy" data-reveal>
				<span class="about-symbol" aria-hidden="true">
					<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
						<path d="M5 12h14"></path>
						<path d="M12 5v14"></path>
					</svg>
				</span>
				<p>Работаем с <?= htmlspecialchars((string)seo_config('founded_market')) ?> года. В основе — <?= htmlspecialchars((string)seo_config('market_years')) ?> лет практики в быстровозводимом строительстве.</p>
				<p>От фундаментов до реконструкции существующих зданий: мы объединяем производство БВЗ, капитальный ремонт и монтаж инженерных сетей. Три направления, единая команда, работаем по всей России.</p>
				<a href="/uslugi" class="text-link">Смотреть направления
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</a>
			</div>
		</div>
		<dl class="about-stats">
			<div><dd>15</dd><dt>лет на рынке</dt></div>
			<div><dd>03</dd><dt>основных направления</dt></div>
			<div><dd><?= htmlspecialchars((string)seo_config('objects')) ?></dd><dt>сданных объектов</dt></div>
		</dl>
	</section>

	<!-- DIVISIONS (ProMaintenance division-section) -->
	<section id="divisions" class="division-section section-shell">
		<div class="section-topline">
			<p class="eyebrow">Три направления</p>
			<span class="eyebrow">Одно ФСС</span>
		</div>
		<div class="work-heading">
			<h2>Экспертиза,<br>которая работает вместе.</h2>
			<p>От фундамента и основания.<br>Внутри конструкций и на площадке.</p>
		</div>
		<div class="division-grid">
			<a class="division-card" href="/services/proizvodim_modul" title="Производство быстровозводимых зданий">
				<div class="division-photo">
					<img src="/public/assets/image/banner.webp" alt="Быстровозводимые здания ФСС — поставка и монтаж" title="Быстровозводимые здания ФСС — поставка и монтаж" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
				</div>
				<div class="division-card-label">
					<span class="eyebrow">01</span>
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</div>
				<h3>Быстровозводимые здания</h3>
				<p>БВЗ, модульные блоки, ангары, склады, АБК и общежития под ключ — от расчёта до сдачи.</p>
			</a>
			<a class="division-card" href="/services/kapital" title="Капитальный строительство и ремонт">
				<div class="division-photo">
					<img src="/public/assets/image/services/капстроение/10.JPG" alt="Капитальный ремонт здания — объект ФСС" title="Капитальный ремонт здания — объект ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
				</div>
				<div class="division-card-label">
					<span class="eyebrow">02</span>
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</div>
				<h3>Капитальный ремонт</h3>
				<p>Школы, АБК, фасады, кровля, полы и инженерные сети — сдаём к началу учебного года.</p>
			</a>
			<a class="division-card" href="/services/spectehnika" title="Спецтехника и услуги">
				<div class="division-photo">
					<img src="/public/assets/image/swiper/3.jpeg" alt="Спецтехника и услуги ФСС" title="Спецтехника и услуги ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
				</div>
				<div class="division-card-label">
					<span class="eyebrow">03</span>
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</div>
				<h3>Спецтехника и услуги</h3>
				<p>Аренда компрессоров и генераторов, грузоперевозки, монтажные работы, техобслуживание.</p>
			</a>
		</div>
	</section>
	<section id="projects" class="selected-work section-shell">
		<div class="section-topline">
			<p class="eyebrow"><?= $fssSec('Избранные проекты') ?></p>
			<a href="/portfolio" class="text-link">Смотреть все объекты
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M7 7h10v10"></path>
					<path d="M7 17 17 7"></path>
				</svg>
			</a>
		</div>

		<div class="work-heading">
			<h2 data-reveal aria-label="Места с будущим.">Места с будущим.</h2>
			<p>За каждым объектом — причина<br>сделать его надолго.</p>
		</div>

		<div class="featured-grid">
			<article class="project-card" data-emirate="Московская область" data-search="Склад 1 200 м² из сэндвич-панелей Склад 1 200 м² из сэндвич-панелей Логистика Химки Московская область Доковые ворота, тёплый контур, освещение и вентиляция" data-slug="sklad-1200">
				<a href="/portfolio#obj-1" class="project-card__link" title="Склад 1 200 м² из сэндвич-панелей — объект ФСС">
					<div class="project-card__image">
						<img src="/public/assets/image/services/МТЗ/MNAEHRcw27qk0buSmkKQyg5y30hnigc-zXc68ylPagQIfUWmRJj2PI2ngGCso3cNr1Ar9H_JQ-Nm5IJd7VtriDPb.jpg" srcset="/public/assets/image/services/МТЗ/MNAEHRcw27qk0buSmkKQyg5y30hnigc-zXc68ylPagQIfUWmRJj2PI2ngGCso3cNr1Ar9H_JQ-Nm5IJd7VtriDPb.jpg 600w, /public/assets/image/services/МТЗ/MNAEHRcw27qk0buSmkKQyg5y30hnigc-zXc68ylPagQIfUWmRJj2PI2ngGCso3cNr1Ar9H_JQ-Nm5IJd7VtriDPb.jpg 1000w, /public/assets/image/services/МТЗ/MNAEHRcw27qk0buSmkKQyg5y30hnigc-zXc68ylPagQIfUWmRJj2PI2ngGCso3cNr1Ar9H_JQ-Nm5IJd7VtriDPb.jpg 1280w" alt="Склад 1 200 м² из сэндвич-панелей — объект ФСС" title="Склад 1 200 м² из сэндвич-панелей — объект ФСС" sizes="92vw" loading="lazy" decoding="async" width="1280" height="960" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
						<span class="project-card__number">01 — Логистика</span>
						<span class="project-card__arrow">
							<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</span>
						<span class="project-card__caption">Фото с объекта ФСС</span>
					</div>
					<div class="project-card__meta"><span>Московская область</span><span>Логистика</span></div>
					<h3>Склад 1 200 м² из сэндвич-панелей</h3>
					<p>Доковые ворота, тёплый контур, освещение и вентиляция. Каркас и обшивка за 45 дней.</p>
				</a>
			</article>

			<article class="project-card" data-emirate="Московская область" data-search="Модульное общежитие на 120 мест Модульное общежитие на 120 мест Жильё Мытищи Московская область Два этажа, тёплый контур, кондиционеры в каждой комнате" data-slug="obshchiezhie-120">
				<a href="/portfolio#obj-2" class="project-card__link" title="Модульное общежитие на 120 мест — объект ФСС">
					<div class="project-card__image">
						<img src="/public/assets/image/banner.jpeg" srcset="/public/assets/image/banner.jpeg 600w, /public/assets/image/banner.jpeg 1000w, /public/assets/image/banner.jpeg 1280w" alt="Модульное общежитие на 120 мест — объект ФСС" title="Модульное общежитие на 120 мест — объект ФСС" sizes="(min-width: 993px) 46vw, 100vw" loading="lazy" decoding="async" width="1280" height="960" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
						<span class="project-card__number">02 — Жильё</span>
						<span class="project-card__arrow">
							<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</span>
						<span class="project-card__caption">Фото с объекта ФСС</span>
					</div>
					<div class="project-card__meta"><span>Московская область</span><span>Жильё</span></div>
					<h3>Модульное общежитие на 120 мест</h3>
					<p>Два этажа, тёплый контур, кондиционеры в каждой комнате. Сдали за 30 дней — заселили сразу.</p>
				</a>
			</article>

			<article class="project-card" data-emirate="Московская область" data-search="Капремонт школы под ключ Капремонт школы под ключ Образование Подольск Московская область Полы, потолки, электрика и чистовая отделка классов" data-slug="kapremont-shkoly">
				<a href="/portfolio#obj-3" class="project-card__link" title="Капремонт школы под ключ — объект ФСС">
					<div class="project-card__image">
						<img src="/public/assets/image/services/капстроение/5.JPG" srcset="/public/assets/image/services/капстроение/5.JPG 600w, /public/assets/image/services/капстроение/5.JPG 1000w, /public/assets/image/services/капстроение/5.JPG 1170w" alt="Капремонт школы под ключ — объект ФСС" title="Капремонт школы под ключ — объект ФСС" sizes="(min-width: 993px) 46vw, 100vw" loading="lazy" decoding="async" width="1170" height="778" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
						<span class="project-card__number">03 — Образование</span>
						<span class="project-card__arrow">
							<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</span>
						<span class="project-card__caption">Фото с объекта ФСС</span>
					</div>
					<div class="project-card__meta"><span>Московская область</span><span>Образование</span></div>
					<h3>Капремонт школы под ключ</h3>
					<p>Полы, потолки, электрика и чистовая отделка классов. Сдали к 1 сентября — без срыва сроков.</p>
				</a>
			</article>
		</div>

		<p class="image-editorial-note">Фотографии — с реальных объектов ФСС. На странице проектов мы разделяем референсные здания и авторские визуализации.</p>
	</section>

	<!-- EXPERTISE (ProMaintenance expertise-section) -->
	<section id="expertise" class="expertise-section section-shell">
		<div class="section-topline">
			<p class="eyebrow"><?= $fssSec('Компетенции') ?></p>
			<span class="eyebrow">Точность снаружи и внутри</span>
		</div>
		<div class="expertise-layout">
			<div class="expertise-visual">
				<h2>Разные задачи.<br>Один сильный результат.</h2>
				<div class="expertise-image">
					<img src="/public/assets/image/swiper/1.jpeg" alt="Модульное общежитие из сэндвич-панелей — объект ФСС" title="Модульное общежитие из сэндвич-панелей — объект ФСС" loading="lazy" data-expertise-image="moduli" decoding="async" class="is-active">
					<img src="/public/assets/image/swiper/2.jpg" alt="Склад из сэндвич-панелей — объект ФСС" title="Склад из сэндвич-панелей — объект ФСС" loading="lazy" data-expertise-image="sklad" decoding="async">
					<img src="/public/assets/image/banner.webp" alt="Модульные здания ФСС — поставка и монтаж" title="Модульные здания ФСС — поставка и монтаж" loading="lazy" data-expertise-image="proizvodstvo" decoding="async">
					<img src="/public/assets/image/services/капстроение/10.JPG" alt="Капитальный ремонт здания — объект ФСС" title="Капитальный ремонт здания — объект ФСС" loading="lazy" data-expertise-image="kapremont" decoding="async">
				</div>
				<p class="eyebrow">Технологическая экспертиза / Структурная надёжность</p>
			</div>
			<div class="service-list">
				<details class="service-detail" name="expertise" id="service-moduli" data-service-detail="moduli" open>
					<summary>
						<span>01</span>
						<h3>Модульные здания под ключ</h3>
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<div class="service-detail-body">
						<span class="service-tag">Производство</span>
						<p>Модули из сэндвич-панелей изготавливаем на собственном заводе БВЗ: ангары, склады, АБК, общежития и бытовые городки. Собственное производство — это контроль качества и цена ниже рынка без посредников.</p>
						<a href="/services/proizvodim_modul" class="text-link">Обсудить систему
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				</details>
				<details class="service-detail" name="expertise" id="service-sklad" data-service-detail="sklad">
					<summary>
						<span>02</span>
						<h3>Склады и логистические центры</h3>
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<div class="service-detail-body">
						<span class="service-tag">Ёмкость</span>
						<p>Каркас и обшивка за 45 дней, доковые ворота, тёплый контур, освещение и вентиляция. Считаем несущую способность под ваши нагрузки и подъездные пути — до начала работ.</p>
						<a href="/services/sclad" class="text-link">Обсудить систему
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				</details>
				<details class="service-detail" name="expertise" id="service-proizvodstvo" data-service-detail="proizvodstvo">
					<summary>
						<span>03</span>
						<h3>Проектирование и инженерные системы</h3>
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<div class="service-detail-body">
						<span class="service-tag">Инженерия</span>
						<p>Чертежи, расчёты нагрузок, согласования, отопление, электрика, вода и вентиляция. Проект пишется так, чтобы его можно было построить на площадке, а не только показать.</p>
						<a href="/services/proektirovanie" class="text-link">Обсудить систему
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				</details>
				<details class="service-detail" name="expertise" id="service-kapremont" data-service-detail="kapremont">
					<summary>
						<span>04</span>
						<h3>Капитальный ремонт</h3>
						<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<div class="service-detail-body">
						<span class="service-tag">Долговечность</span>
						<p>Школы, АБК, фасады, кровля, полы, внутренняя отделка и пожарная безопасность. Ремонтируем без остановки работы объекта — phased по этажам и зонам.</p>
						<a href="/services/kapital" class="text-link">Обсудить систему
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				</details>
			</div>
		</div>
	</section>

	<!-- PROCESS (ProMaintenance process-section) -->
	<section id="process" class="process-section">
		<div class="process-photo">
			<img src="/public/assets/image/banner.webp" alt="Модульные здания — производство ФСС" title="Модульные здания — производство ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
		</div>
		<div class="process-content section-shell">
			<p class="eyebrow"><?= $fssSec('Как мы работаем') ?></p>
			<h2>Продуманно на каждом этапе.<br><span>Уверенно на каждом шаге.</span></h2>
			<div class="process-steps">
				<details data-phase="0" open>
					<summary>
						<span>01</span>
						<h3>Заявка и смета</h3>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<p>Оставляете заявку — инженер перезвонит за 15 минут, уточнит задачу и пришлёт смету с фиксированной ценой в договоре.</p>
				</details>
				<details data-phase="1">
					<summary>
						<span>02</span>
						<h3>Проектирование</h3>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<p>Готовим чертежи, рассчитываем нагрузки, подбираем комплектацию и согласовываем проект с вами до начала работ на площадке.</p>
				</details>
				<details data-phase="2">
					<summary>
						<span>03</span>
						<h3>Фундамент</h3>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<p>Устраиваем основание под модули: свайное, ленточное или столбчатое — по геологии участка и нагрузкам здания.</p>
				</details>
				<details data-phase="3">
					<summary>
						<span>04</span>
						<h3>Производство</h3>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<p>Изготавливаем модули из сэндвич-панелей на собственном заводе БВЗ. Контроль качества — на каждой стадии, а не на выходе.</p>
				</details>
				<details data-phase="4">
					<summary>
						<span>05</span>
						<h3>Монтаж и сдача</h3>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="M12 5v14"></path>
						</svg>
					</summary>
					<p>Собираем здание, подключаем коммуникации, сдаём объект под ключ с исполнительной документацией и гарантией 50 лет на конструкции.</p>
				</details>
			</div>
		</div>
	</section>

	<!-- WORKS PREVIEW (ProMaintenance works-preview) -->
	<section class="works-preview section-shell">
		<div class="works-preview-copy">
			<p class="eyebrow">С наших объектов</p>
			<h2>Построено в поле.<br>Увидено вблизи.</h2>
			<p>Арматура, опалубка, сваи. Смотрите оригинальные фотографии наших объектов в процессе работ.</p>
			<a href="/portfolio" class="text-link">Все наши объекты
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M7 7h10v10"></path>
					<path d="M7 17 17 7"></path>
				</svg>
			</a>
		</div>
		<a href="/portfolio" class="works-preview-photo" aria-label="Смотреть все объекты ФСС">
			<img src="/public/assets/image/swiper/2.jpg" alt="Склад из сэндвич-панелей — объект ФСС" title="Склад из сэндвич-панелей — объект ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
		</a>
	</section>

	<!-- ASSESSMENT (ProMaintenance assessment-section) -->
	<section id="triage" class="assessment-section section-shell">
		<details class="assessment-disclosure">
			<summary>
				<span class="eyebrow">Полезно начать отсюда</span>
				<span>Не знаете, какая система нужна?</span>
				<span class="assessment-toggle">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M5 12h14"></path>
						<path d="M12 5v14"></path>
					</svg>
				</span>
			</summary>
			<div class="assessment-content" data-triage>
				<p>Три наблюдения, чтобы начать разговор. Это ориентировочный guidance, а не строительная экспертиза.</p>
				<form data-triage-form class="assessment-questions">
					<label>
						<span>01 / Что вы видите?</span>
						<select name="symptom">
							<option value="">Выберите наблюдение</option>
							<option value="cracking">Трещины</option>
							<option value="spalling">Отслоение бетона</option>
							<option value="rust-staining">Ржавые потёки</option>
							<option value="water-ingress">Протечки воды</option>
							<option value="deflection">Провисание</option>
							<option value="added-load">Меняется нагрузка</option>
						</select>
					</label>
					<label>
						<span>02 / Какой элемент?</span>
						<select name="element">
							<option value="">Выберите элемент</option>
							<option value="column">Колонна</option>
							<option value="beam">Балка / перекрытие</option>
							<option value="slab">Плита</option>
							<option value="wall-core">Стена / ядро</option>
							<option value="foundation">Фундамент</option>
							<option value="water-retaining">Резервуар / ёмкость</option>
						</select>
					</label>
					<label>
						<span>03 / Это ещё движется?</span>
						<select name="movement">
							<option value="">Выберите вариант</option>
							<option value="moving">Да, движется</option>
							<option value="stopped">Нет, стабильно</option>
							<option value="unknown">Не знаем</option>
						</select>
					</label>
				</form>
				<div data-triage-empty class="assessment-empty">Ответьте на все три вопроса — покажем отправную точку.</div>
				<div data-triage-answer hidden class="assessment-answer" aria-live="polite">
					<div>
						<p class="eyebrow">Вероятная причина</p>
						<p data-triage-cause></p>
						<p class="eyebrow">Первый шаг</p>
						<p data-triage-step></p>
					</div>
					<div>
						<p class="eyebrow">Обычная система</p>
						<h3 data-triage-service></h3>
						<p class="eyebrow">Также стоит рассмотреть</p>
						<p data-triage-also></p>
						<p data-triage-caution hidden class="assessment-caution"><span data-triage-caution-text></span></p>
					</div>
					<div class="assessment-actions">
						<a href="/#contact" class="button">Получить расчёт за 15 минут
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
						<button type="button" data-triage-reset class="text-link" style="background:none;border:none;cursor:pointer;">Начать заново</button>
					</div>
				</div>
			</div>
		</details>
	</section>



	<!-- Contact — High converting -->
<div class="px-3 sm:px-5">
	<!-- ENQUIRY (ProMaintenance enquiry-section) -->
	<section id="contact" class="enquiry-section">
		<div class="enquiry-lead">
			<p class="eyebrow">
				<span class="live-dot" aria-hidden="true"></span>
				Поговорим о вашем проекте
			</p>
			<h2 id="enquiry-title">Рассчитаем стоимость<br><span>за 15 минут</span></h2>
			<p>Одна задача. Один разговор.<br>Расскажите, что у вас за проект.</p>
			<a class="enquiry-email" href="mailto:<?= htmlspecialchars((string)seo_config('email')) ?>"><?= htmlspecialchars((string)seo_config('email')) ?>
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M7 7h10v10"></path>
					<path d="M7 17 17 7"></path>
				</svg>
			</a>
			<a class="enquiry-phone" href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', (string)seo_config('phone'))) ?>"><?= htmlspecialchars((string)seo_config('phone_display')) ?></a>
			<span class="enquiry-location">Москва и область. Работаем по всей России.</span>
		</div>
		<?php include_once __DIR__ . '/../components/enquiry_form.php'; ?>
	</section>
</div><!-- /.px-3 sm:px-5 (обёртка секции заявки) -->

	<!-- 6. Подтверждённые работы: реальные заказчики и объекты (E-E-A-T) -->
	<?php
	// Отзывы публикуются только по факту (app/Data/reviews.php). Пока их нет —
	// показываем проверяемые факты: реальные заказчики и выполненные объекты.
	$__reviews = seo_reviews();
	$__projects = file_exists(dirname(__DIR__, 2) . '/app/Data/projects.php')
		? (array)(require dirname(__DIR__, 2) . '/app/Data/projects.php')
		: [];
	?>
	<section id="reviews" class="reviews-section section-shell view">
		<div class="section-topline">
			<p class="eyebrow"><?= $fssSec($__reviews ? 'Отзывы' : 'Подтверждённые работы') ?></p>
			<span class="eyebrow"><?= seo_config('objects') ?> объектов с <?= seo_config('founded_market') ?> года</span>
		</div>
		<div class="head-row">
			<h2><?= $__reviews ? 'Говорят<br><span>наши клиенты.</span>' : 'Нам доверяют<br><span>госзаказчики.</span>' ?></h2>
			<p><?= $__reviews ? 'Реальные истории с объектов: сроки, бюджеты<br>и результат, который принимают.' : 'Работы по 44-ФЗ и 223-ФЗ: заказчик, объект<br>и состав выполненных работ.' ?></p>
		</div>
		<?php if ($__reviews): ?>
		<div class="reviews-grid">
			<?php foreach ($__reviews as $i => $r): ?>
				<article class="review-card">
					<div class="review-card-top">
						<span class="review-stars" aria-label="Рейтинг <?= (int)$r['star'] ?> из 5">
							<?php for ($s = 0; $s < 5; $s++): ?>
								<i class="<?= $s < $r['star'] ? 'is-on' : '' ?>">★</i>
							<?php endfor ?>
						</span>
						<span class="review-index"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
					</div>
					<p class="review-text"><?= htmlspecialchars($r['text'], ENT_QUOTES) ?></p>
					<div class="review-author">
						<strong><?= htmlspecialchars($r['name'], ENT_QUOTES) ?></strong>
						<span><?= htmlspecialchars($r['company'], ENT_QUOTES) ?></span>
						<time datetime="<?= $r['date'] ?>"><?= $r['date'] ?></time>
					</div>
				</article>
			<?php endforeach ?>
		</div>
		<?php else: ?>
		<!-- Лента доверия: с кем работаем -->
		<ul class="trust-rail" aria-label="Заказчики компании">
			<li class="trust-rail-label">Работаем с</li>
			<li>Росгвардия России</li>
			<li>Минпросвещение РФ</li>
			<li>Муниципальные бюджетные учреждения</li>
			<li>Государственные школы и учреждения</li>
			<li>Силовые структуры</li>
		</ul>

		<!-- Кейсы: заказчик, объект, состав работ -->
		<div class="case-grid">
			<?php
			$__allSlugs = array_keys($__projects);
			foreach (array_slice($__projects, 0, 6, true) as $__slug => $__pr):
				$__photo = !empty($__pr['photos'][0]) ? \App\Models\Image::publicUrl((string)$__pr['photos'][0]) : '';
				$__alt = !empty($__pr['photo_alts'][0]) ? (string)$__pr['photo_alts'][0] : (string)($__pr['h1'] ?? $__pr['title'] ?? '');
				$__tags = array_slice((array)($__pr['features'] ?? []), 0, 3);
				$__url = '/portfolio/' . rawurlencode((string)$__slug);
				?>
				<article class="case-card">
					<?php if ($__photo !== ''): ?>
						<a class="case-media" href="<?= htmlspecialchars($__url, ENT_QUOTES) ?>" tabindex="-1" aria-hidden="true">
							<img src="<?= htmlspecialchars($__photo, ENT_QUOTES) ?>" alt="" loading="lazy" decoding="async"<?= seo_img_attrs($__photo) ?>>
						</a>
					<?php endif; ?>
					<div class="case-head">
						<span class="case-zone"><?= htmlspecialchars((string)($__pr['zone'] ?? ''), ENT_QUOTES) ?></span>
						<span class="case-index"><?= str_pad((string)((int)array_search($__slug, $__allSlugs, true) + 1), 2, '0', STR_PAD_LEFT) ?></span>
					</div>
					<h3 class="case-title"><a href="<?= htmlspecialchars($__url, ENT_QUOTES) ?>"><?= htmlspecialchars((string)($__pr['title'] ?? ''), ENT_QUOTES) ?></a></h3>
					<p class="case-region"><?= htmlspecialchars((string)($__pr['region'] ?? ''), ENT_QUOTES) ?></p>
					<?php if ($__tags): ?>
						<ul class="case-tags">
							<?php foreach ($__tags as $__t): ?>
								<li><?= htmlspecialchars((string)$__t, ENT_QUOTES) ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<a class="case-more" href="<?= htmlspecialchars($__url, ENT_QUOTES) ?>">Открыть кейс
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<path d="M5 12h14"></path>
							<path d="m13 6 6 6-6 6"></path>
						</svg>
					</a>
				</article>
			<?php endforeach; ?>
		</div>

		<!-- Цифры доверия -->
		<dl class="trust-stats">
			<div>
				<dd><?= htmlspecialchars((string)seo_config('kapremont_objects'), ENT_QUOTES) ?></dd>
				<dt>объектов капитального ремонта</dt>
			</div>
			<div>
				<dd><?= htmlspecialchars((string)seo_config('schools_equipped'), ENT_QUOTES) ?></dd>
				<dt>школ оснащено по стандартам Минпросвещения РФ</dt>
			</div>
			<div>
				<dd><?= htmlspecialchars((string)seo_config('sochi_area'), ENT_QUOTES) ?></dd>
				<dt>общежитий построено в Сочи</dt>
			</div>
			<div>
				<dd><?= htmlspecialchars((string)seo_config('guarantee_years'), ENT_QUOTES) ?> <span class="trust-stats-unit">лет</span></dd>
				<dt>гарантия на несущие конструкции</dt>
			</div>
		</dl>

		<p class="trust-note">Отзывы заказчиков публикуем по факту выполненных работ — без вымышленных имён и оценок. <?= seo_config('objects') ?> объектов в портфолио: <a href="/portfolio">все кейсы</a>.</p>
		<?php endif; ?>
	</section>

	<!-- ИЗБРАННЫЕ ПРОЕКТЫ (Protenance selected-work) -->
	<!-- Бегущая строка направлений -->
	<section class="marquee-band" aria-hidden="true">
		<div class="marquee-track"><span class="marquee-item">Капитальный ремонт</span><span class="marquee-item">Поставка мебели</span><span class="marquee-item">Быстровозводимые здания</span><span class="marquee-item">Склады</span><span class="marquee-item">Ангары</span><span class="marquee-item">Навесы</span><span class="marquee-item">Дизель-генераторы</span><span class="marquee-item">Компрессорные станции</span><span class="marquee-item">Блочно-модульные конструкции</span><span class="marquee-item">Монтаж инженерных систем</span><span class="marquee-item">Проектирование объектов</span><span class="marquee-item">Техническое обслуживание</span><span class="marquee-item">Ремонт кровли</span><span class="marquee-item">Внутренняя отделка</span><span class="marquee-item">Промышленные полы</span><span class="marquee-item">Фасадные работы</span><span class="marquee-item">Пожарная безопасность</span><span class="marquee-item">Ландшафтные работы</span><span class="marquee-item">Капитальный ремонт</span><span class="marquee-item">Поставка мебели</span><span class="marquee-item">Быстровозводимые здания</span><span class="marquee-item">Склады</span><span class="marquee-item">Ангары</span><span class="marquee-item">Навесы</span><span class="marquee-item">Дизель-генераторы</span><span class="marquee-item">Компрессорные станции</span><span class="marquee-item">Блочно-модульные конструкции</span><span class="marquee-item">Монтаж инженерных систем</span><span class="marquee-item">Проектирование объектов</span><span class="marquee-item">Техническое обслуживание</span><span class="marquee-item">Ремонт кровли</span><span class="marquee-item">Внутренняя отделка</span><span class="marquee-item">Промышленные полы</span><span class="marquee-item">Фасадные работы</span><span class="marquee-item">Пожарная безопасность</span><span class="marquee-item">Ландшафтные работы</span></div>
	</section>
	<!-- Почему выбирают: ProMaintenance (плюсы + цифры) -->
	<section id="pluses" class="pluses-section section-shell view">
		<div class="section-topline">
			<p class="eyebrow"><?= $fssSec('Почему мы') ?></p>
			<span class="eyebrow">Один подрядчик на весь цикл</span>
		</div>
		<div class="head-row">
			<h2>Причины выбрать<br><span>нас.</span></h2>
			<p>Прозрачные условия, поставка и монтаж<br>и инженер на каждом объекте.</p>
		</div>
		<div class="pluses-grid">
			<article class="plus-item">
				<span class="plus-index">01</span>
				<h3>Полный контроль 24/7</h3>
				<p>Инженер на площадке, фотоотчёт каждую неделю и технадзор на каждом этапе — вы в курсе прогресса без звонков.</p>
			</article>
			<article class="plus-item">
				<span class="plus-index">02</span>
				<h3>Фиксированная цена</h3>
				<p>Смета за 15 минут. Стоимость и сроки закрепляем в договоре — цена не растёт по ходу стройки.</p>
			</article>
			<article class="plus-item">
				<span class="plus-index">03</span>
				<h3>БВЗ и модули под ключ</h3>
				<p>Сэндвич-панели и блок-контейнеры поставляем и монтируем сами: контроль качества на объекте и цена ниже рынка.</p>
			</article>
			<article class="plus-item">
				<span class="plus-index">04</span>
				<h3>Своя техника и доставка</h3>
				<p>Собственный парк техники и доставка по России: без субподряда и перепродажи.</p>
			</article>
			<article class="plus-item">
				<span class="plus-index">05</span>
				<h3>Полный цикл работ</h3>
				<p>Проектирование, фундамент, монтаж, инженерные системы и сдача под ключ — одним договором.</p>
			</article>
			<article class="plus-item">
				<span class="plus-index">06</span>
				<h3>Работаем по всей России</h3>
				<p>Москва и область — основной регион, остальные — по отработанной логистике и договорённости.</p>
			</article>
		</div>
		<dl class="pluses-stats">
			<div>
				<dd><?= seo_config('objects') ?></dd>
				<dt>сданных объектов с <?= seo_config('founded_market') ?> года</dt>
			</div>
			<div>
				<dd>50<span>лет</span></dd>
				<dt>гарантия на несущие конструкции</dt>
			</div>
			<div>
				<dd>14<span>дней</span></dd>
				<dt>минимальный срок запуска работ</dt>
			</div>
			<div>
				<dd>15<span>минут</span></dd>
				<dt>до сметы по вашей заявке</dt>
			</div>
		</dl>
	</section>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				const el = document.querySelector(".swipers");
				if (!el || el.offsetParent === null) return;
				new Swiper(el, {
					loop: true,
					slidesPerView: 4,
					slidesPerGroup: 1,
					spaceBetween: 20,
					autoplay: {
						delay: 3000,
						disableOnInteraction: false,
					},
					pagination: {
						el: ".swiper-paginations",
						clickable: true,
					},
					breakpoints: {
						0: {
							slidesPerView: 1,
							slidesPerGroup: 1,
						},
						600: {
							slidesPerView: 2,
							slidesPerGroup: 1,
						},
						900: {
							slidesPerView: 3,
							slidesPerGroup: 2,
						},
						1200: {
							slidesPerView: 4,
							slidesPerGroup: 1,
						}
					},
				});
				el.classList.add("loaded");
			});
		</script>
	<!-- uslugi — modern bento -->



	<!-- Модальное окно для описания товара каталога -->
	<dialog id="catalog-modal" style="padding:0; border:none; background:transparent;">
		<div id="catalog-modal-backdrop"
			style="position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; display:flex; align-items:center; justify-content:center;">
			<div
				style="background:#fff; border-radius:16px; max-width:600px; width:100%; max-height:80vh; overflow-y:auto; overflow-x:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.25); position:relative;">
				<button id="catalog-modal-close" type="button"
					style="position:absolute; top:12px; right:12px; background:none; border:none; font-size:1.5rem; color:#db0d15; cursor:pointer;">&times;</button>
				<div
					style="padding:2.5rem 1.5rem 1.5rem 1.5rem; max-width:100%; word-break:break-word; overflow-wrap:break-word; box-sizing:border-box;">
					<h2 id="catalog-modal-title"
						style="font-size:1.5rem; font-weight:bold; margin-bottom:1rem; max-width:100%; word-break:break-word; overflow-wrap:break-word; box-sizing:border-box;">
					</h2>
					<p id="catalog-modal-desc"
						style="font-size:1rem; color:#333; max-width:100%; word-break:break-word; overflow-wrap:break-word; box-sizing:border-box;">
					</p>
				</div>
			</div>
		</div>
	</dialog>

	<!-- sticky lead bar (mobile) -->
	<div id="sticky-cta" class="fixed bottom-0 inset-x-0 z-40 md:hidden bg-white/95 backdrop-blur-md border-t border-gray-200 px-3 py-2 safe-pb">
		<div class="flex gap-2">
			<a href="tel:<?= htmlspecialchars(preg_replace("/\D/", "", seo_config("phone"))) ?>" title="Позвонить: +7 938 018-00-06" class="flex-1 inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-900 rounded-full py-3 font-semibold"><i class="fa-solid fa-phone text-emerald-600"></i> Позвонить</a>
			<a href="#contact" title="Рассчитать стоимость" data-goal="home_calc_cta" class="flex-1 inline-flex items-center justify-center gap-2 bg-emerald-600 text-white rounded-full py-3 font-semibold shadow">Рассчитать <i class="fa-solid fa-arrow-right text-xs"></i></a>
		</div>
		<div class="text-center text-[11px] text-gray-500 mt-1">Отвечаем за 7 минут • Без спама</div>
	</div>
	<script> (function(){ var bar=document.getElementById('sticky-cta'); var showAt=700; function onScroll(){ if(window.scrollY>showAt) bar.classList.add('show'); else bar.classList.remove('show'); } window.addEventListener('scroll', onScroll, {passive:true}); onScroll(); })(); </script>

	<!-- Этапы работы: слайдер на мобильных -->
	<script>
	(function () {
		"use strict";
		var steps = document.querySelector('#process .steps3');
		if (!steps) return;
		var items = Array.prototype.slice.call(steps.querySelectorAll('.step3'));
		var dotsWrap = document.querySelector('#process .steps3-dots');
		var countEl = document.querySelector('#process .steps3-count');
		if (!items.length || !dotsWrap) return;

		var mq = window.matchMedia('(max-width: 899px)');
		var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
		var dots = [];
		var active = -1;

		items.forEach(function (_, i) {
			var b = document.createElement('button');
			b.type = 'button';
			b.className = 'steps3-dot';
			b.setAttribute('aria-label', 'Показать этап ' + (i + 1) + ' из ' + items.length);
			b.addEventListener('click', function () { goTo(i); });
			dotsWrap.appendChild(b);
			dots.push(b);
		});

		function pad(n) { return (n < 10 ? '0' : '') + n; }

		function setActive(i) {
			if (i === active) return;
			active = i;
			items.forEach(function (el, idx) { el.classList.toggle('is-active', idx === i); });
			dots.forEach(function (d, idx) { d.classList.toggle('is-active', idx === i); });
			if (countEl) countEl.innerHTML = '<b>' + pad(i + 1) + '</b> / ' + pad(items.length);
		}

		function goTo(i) {
			if (i < 0 || i >= items.length) return;
			var sr = steps.getBoundingClientRect();
			var er = items[i].getBoundingClientRect();
			var left = steps.scrollLeft + (er.left + er.width / 2) - (sr.left + sr.width / 2);
			steps.scrollTo({ left: left, behavior: reduced.matches ? 'auto' : 'smooth' });
		}

		function nearest() {
			var sr = steps.getBoundingClientRect();
			var center = sr.left + sr.width / 2;
			var best = 0, dist = Infinity;
			items.forEach(function (el, idx) {
				var r = el.getBoundingClientRect();
				var d = Math.abs(r.left + r.width / 2 - center);
				if (d < dist) { dist = d; best = idx; }
			});
			return best;
		}

		var raf = 0;
		function onScroll() {
			if (raf) return;
			raf = requestAnimationFrame(function () {
				raf = 0;
				if (mq.matches) setActive(nearest());
			});
		}

		function sync() {
			steps.classList.add('is-ready');
			if (mq.matches) setActive(nearest());
		}

		steps.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', sync);
		window.addEventListener('load', sync);
		if (mq.addEventListener) mq.addEventListener('change', sync); else mq.addListener(sync);

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', sync);
		} else {
			sync();
		}
	})();
	</script>


	<!-- FAQ главной (FAQPage + ответы для AI Overviews) -->
	<?php include_once __DIR__ . '/../components/home_faq.php'; ?>

	<!-- footer content -->
	<?php include_once __DIR__ . '/../components/footer.php'; ?>
	<!-- форма обратной связи -->


</body>

</html>