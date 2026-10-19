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
	<meta name="theme-color" content="#F97316">
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
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/public/assets/image/logo/favicon/site.webmanifest" />
	<link rel="mask-icon" href="/public/assets/image/logo/favicon/favicon-96x96.png" color="#F97316">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"></noscript>
	<style> body { font-family: 'Wix Madefor Display', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
	<!-- Preload critical -->
	<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
	<link rel="preload" href="/public/assets/distJS/main.js" as="script">
	<!-- Styles -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<!-- Swiper -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
	<script defer src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
	<script defer>
		// Lenis Smooth Scroll — exact Protenance settings
		if (typeof Lenis !== 'undefined') {
			const lenis = new Lenis({
				duration: 1.12,
				smoothWheel: true,
				syncTouch: false,
				anchors: { offset: -105 },
				prevent: e => e.closest('#nav-panel') !== null,
				easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
				direction: 'vertical',
				gestureDirection: 'vertical',
				smooth: true,
				smoothTouch: false,
				touchMultiplier: 2,
				infinite: false,
			});
			function raf(time) {
				lenis.raf(time);
				requestAnimationFrame(raf);
			}
			requestAnimationFrame(raf);
			// Expose for debugging
			window.lenis = lenis;

			// Anchor scroll — exact Protenance: duration 1.05, offset -105
			document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
				anchor.addEventListener('click', function(e) {
					var targetId = this.getAttribute('href');
					if (targetId === '#') return;
					var target = document.querySelector(targetId);
					if (target) {
						e.preventDefault();
						lenis.scrollTo(target, { offset: -105, duration: 1.05 });
					}
				});
			});
		}
	</script>
	<script defer src="/public/assets/distJS/main.js"></script>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">

<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
<?= seo_analytics() ?>
<?= seo_utm_script() ?>
<?= seo_goals_script() ?>

</head>

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

		section {
			overflow: hidden;
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
				background: #f97316;
			}
		}
		@media (prefers-reduced-motion: reduce) {
			#process .step3 { transition: none; }
		}

		/* Cinema Hero Styles — exact Protenance copy */
		.hero { isolation: isolate; }
		.cinema-image { object-fit: cover; object-position: 58% 50%; opacity: 0; width: 100%; height: 100%; position: absolute; transition: opacity 0.8s cubic-bezier(.2,.8,.2,1); }
		.cinema-image.is-active { opacity: 1; }
		.hero-shade { pointer-events: none; }
		.cinema-pagination button { opacity: .5; transition: opacity 0.3s; }
		.cinema-pagination button[aria-pressed=true] { opacity: 1; }
		.cinema-pagination button span { transition: width 0.6s cubic-bezier(.2,.8,.2,1); }
		.hero-project { backdrop-filter: blur(22px); border-radius: 8px; background: #16161794; border: 1px solid #ffffff4d; flex-direction: column; gap: 9px; padding: 24px 75px 24px 24px; transition: background .4s; display: flex; position: relative; }
		.hero-project:hover { background: #24211ecc; }
		.hero-project .eyebrow { opacity: .7; font-size: 8px; }
		.hero-project strong { letter-spacing: -.02em; font-size: 18px; font-weight: 500; }
		.hero-project>span[data-cinema-caption] { opacity: .7; font-size: 10px; }
		.hero-project-arrow { position: absolute; top: 50%; right: 24px; transform: translateY(-50%); }
		.hero-controls { padding-bottom: 16px; }
		.cinema-pagination { align-items: center; gap: 16px; display: flex; }
		.cinema-pagination button { opacity: .5; align-items: center; gap: 12px; min-height: 44px; font-size: 10px; display: flex; background: none; border: none; color: inherit; cursor: pointer; padding: 0; }
		.cinema-pagination button span { width: 20px; height: 1px; transition: width .6s cubic-bezier(.2,.8,.2,1); background: #fff; display: block; }
		.cinema-pagination button[aria-pressed=true] { opacity: 1; }
		.cinema-pagination button[aria-pressed=true] span { width: 65px; }
		.hero-controls>.eyebrow { opacity: .65; font-size: 8px; }
		.hero-cta { color: #1b1c1d; border-radius: 8px; background: #f0efed; align-items: center; gap: 45px; margin-bottom: 15px; padding: 22px 26px; font-size: 12px; transition: background .3s; display: flex; }
		.hero-cta:hover { background: #fff; }
		.hero-cta svg { transition: transform .4s cubic-bezier(.2,.8,.2,1); flex-shrink: 0; }
		.hero-cta:hover svg { transform: translate(2px,-2px); }

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
			.hero-controls { grid-column: 1; justify-content: space-between; align-items: center; gap: 15px; padding: 0; display: flex; }
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

		@media (width<=576px) {
			.hero { height: 100svh; min-height: 735px; max-height: 1000px; }
			.hero-content { top: 150px; }
			.hero-content>.eyebrow { letter-spacing: .06em; max-width: 260px; font-size: 8px; }
			.hero h1 { margin-top: 26px; font-size: 14vw; line-height: 1.02; }
			.hero-intro { margin-top: 28px; display: block; }
			.hero-intro p { font-size: 13px; }
			.hero-intro .text-link { margin-top: 15px; }
			.hero-visual .cinema-image { object-position: 62% 50%; }
			.hero-shade { background: linear-gradient(90deg,#0f0e0d99,#0f0e0d33),linear-gradient(#0000 30%,#0f0e0dcc); }
			.hero-bottom { grid-template-columns: 1fr; gap: 12px; bottom: 22px; }
			.hero-project { padding: 18px 65px 18px 18px; }
			.hero-project strong { font-size: 17px; }
			.cinema-pagination button[aria-pressed=true] span { width: 40px; }
			.cinema-pagination button span { width: 15px; }
			.hero-cta { display: none; }
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
<?= seo_analytics() ?>

<?= seo_utm_script() ?>

<?= seo_goals_script() ?>

	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
</head>

<body class="font-sans text-gray-800">

	<?php include_once __DIR__ . '/../components/header.php'; ?>

	<!-- CINEMA HERO — exact Protenance copy -->
	<section class="hero" aria-labelledby="hero-title" style="isolation: isolate; color: #fff; background: #363636; height: 100svh; min-height: 750px; max-height: 1200px; position: relative; overflow: hidden;">
		<div class="hero-visual" data-hero-frame="" style="position: absolute; inset: 0;">
			<img src="/public/assets/image/Components/banner/hero.jpg.webp" srcset="/public/assets/image/Components/banner/hero.jpg.webp 720w, /public/assets/image/Components/banner/hero.jpg.webp 1200w, /public/assets/image/Components/banner/hero.jpg.webp 1672w" alt="" sizes="100vw" loading="eager" fetchpriority="high" data-cinema-image="0" decoding="async" width="1672" height="941" class="cinema-image" style="object-fit: cover; object-position: 58% 50%; opacity: 1; width: 100%; height: 100%; position: absolute; opacity: 1; object-position: 58% 50%;">
			<img src="/public/assets/image/swiper/2.jpg.webp" srcset="/public/assets/image/swiper/2.jpg.webp 720w, /public/assets/image/swiper/2.jpg.webp 1200w, /public/assets/image/swiper/2.jpg.webp 1672w" alt="" sizes="100vw" loading="lazy" fetchpriority="auto" data-cinema-image="1" decoding="async" width="1672" height="941" class="cinema-image" style="object-fit: cover; object-position: 58% 50%; opacity: 0; width: 100%; height: 100%; position: absolute; opacity: 0; object-position: 58% 50%;">
			<img src="/public/assets/image/swiper/3.jpeg.webp" srcset="/public/assets/image/swiper/3.jpeg.webp 720w, /public/assets/image/swiper/3.jpeg.webp 1200w, /public/assets/image/swiper/3.jpeg.webp 1672w" alt="" sizes="100vw" loading="lazy" fetchpriority="auto" data-cinema-image="2" decoding="async" width="1672" height="941" class="cinema-image" style="object-fit: cover; object-position: 58% 50%; opacity: 0; width: 100%; height: 100%; position: absolute; opacity: 0; object-position: 58% 50%;">
		</div>
		<div class="hero-shade" aria-hidden="true" style="position: absolute; inset: 0; pointer-events: none; background: linear-gradient(90deg, #0f0e0dad, #0f0e0d14 85%), linear-gradient(#0f0e0d2e, #0000 55%, #0f0e0dcc);"></div>
		<div class="hero-content" style="position: absolute; top: clamp(155px, 22vh, 260px); left: clamp(24px, 4.4vw, 96px); right: clamp(24px, 4.4vw, 96px);">
			<p class="eyebrow" style="color: #ffffffc7; letter-spacing: .12em; text-transform: uppercase; font-size: 10px; font-weight: 500; line-height: 1.5; display: flex; align-items: center; gap: 12px;">
				<span class="live-dot" style="background: currentColor; border-radius: 50%; width: 6px; height: 6px; margin-right: 12px; display: inline-block;" aria-hidden="true"></span>
				Geotechnical · Structural · Contracting
			</p>
			<h1 id="hero-title" style="letter-spacing: -.066em; text-wrap: initial; margin-top: 32px; font-size: clamp(72px, 9.6vw, 170px); font-weight: 450; line-height: .98;">
				<span style="display: block;">Strength for</span>
				<span style="display: block;">what&#8217;s next<span class="accent-dot" style="color: #c7ab94;">.</span></span>
			</h1>
			<div class="hero-intro" style="align-items: end; gap: 85px; margin-top: 40px; display: flex;">
				<p style="color: #fffc; font-size: 15px; line-height: 1.5;">From the ground up.<br>Strength for every next chapter.</p>
				<a class="text-link" href="#about" style="border-bottom: 1px solid; align-items: center; gap: 25px; padding-block: 10px; font-size: 11px; line-height: 1.4; display: inline-flex; border-color: #ffffff80; font-size: 11px; line-height: 1.4; display: inline-flex; align-items: center; gap: 25px;">Discover Protenance <span>&#8595;</span></a>
			</div>
		</div>
		<div class="hero-bottom" style="grid-template-columns: 1.1fr 1fr auto; align-items: end; gap: 40px; display: grid; position: absolute; bottom: 32px; left: clamp(24px, 4.4vw, 96px); right: clamp(24px, 4.4vw, 96px);">
			<a href="/projects/jewel-of-the-creek" class="hero-project" data-cinema-link="" style="backdrop-filter: blur(22px); border-radius: 8px; background: #16161794; border: 1px solid #ffffff4d; flex-direction: column; gap: 9px; padding: 24px 75px 24px 24px; transition: background .4s; display: flex; position: relative;">
				<span class="eyebrow" style="opacity: .7; font-size: 8px; letter-spacing: .12em; text-transform: uppercase; font-weight: 500; line-height: 1.5; display: flex; align-items: center; gap: 2px;">Featured project / <span data-cinema-number="">01</span></span>
				<strong data-cinema-name="" style="letter-spacing: -.02em; font-size: 18px; font-weight: 500;">Jewel of the Creek</strong>
				<span data-cinema-caption="" style="opacity: .7; font-size: 10px;">Dubai / Hospitality & mixed use</span>
				<span class="hero-project-arrow" style="position: absolute; top: 50%; right: 24px; transform: translateY(-50%);">
					<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</span>
			</a>
			<div class="hero-controls" style="translate: none; rotate: none; scale: none; opacity: 1; visibility: inherit; transform: translate(0px, 0px); display: flex; flex-direction: column; gap: 16px;">
				<div class="cinema-pagination" role="group" aria-label="Featured projects" style="align-items: center; gap: 16px; display: flex;">
					<button type="button" data-cinema-select="0" data-name="Jewel of the Creek" data-caption="Dubai / Hospitality & mixed use" data-project="jewel-of-the-creek" aria-label="Show Jewel of the Creek" aria-pressed="true" style="opacity: 1; align-items: center; gap: 12px; min-height: 44px; font-size: 10px; display: flex; background: none; border: none; color: inherit; cursor: pointer; padding: 0;">
						01<span style="width: 65px; height: 1px; transition: width .6s cubic-bezier(.2, .8, .2, 1); background: #fff; display: block;"></span>
					</button>
					<button type="button" data-cinema-select="1" data-name="Sheikh Khalifa Medical City" data-caption="Abu Dhabi / Healthcare" data-project="sheikh-khalifa-medical-city" aria-label="Show Sheikh Khalifa Medical City" aria-pressed="false" style="opacity: .5; align-items: center; gap: 12px; min-height: 44px; font-size: 10px; display: flex; background: none; border: none; color: inherit; cursor: pointer; padding: 0;">
						02<span style="width: 20px; height: 1px; transition: width .6s cubic-bezier(.2, .8, .2, 1); background: #fff; display: block;"></span>
					</button>
					<button type="button" data-cinema-select="2" data-name="Sunmarke School" data-caption="Dubai / Education" data-project="sunmarke-school" aria-label="Show Sunmarke School" aria-pressed="false" style="opacity: .5; align-items: center; gap: 12px; min-height: 44px; font-size: 10px; display: flex; background: none; border: none; color: inherit; cursor: pointer; padding: 0;">
						03<span style="width: 20px; height: 1px; transition: width .6s cubic-bezier(.2, .8, .2, 1); background: #fff; display: block;"></span>
					</button>
				</div>
				<p class="eyebrow" data-cinema-status="" aria-live="polite" style="opacity: .65; font-size: 8px; letter-spacing: .12em; text-transform: uppercase; font-weight: 500; line-height: 1.5;">01 / 03 — Jewel of the Creek</p>
			</div>
			<a href="/projects" class="hero-cta" style="color: #1b1c1d; border-radius: 8px; background: #f0efed; align-items: center; gap: 45px; margin-bottom: 15px; padding: 22px 26px; font-size: 12px; transition: background .3s; display: flex; align-items: center; gap: 45px;">
				Explore our work
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M7 7h10v10"></path>
					<path d="M7 17 17 7"></path>
				</svg>
			</a>
		</div>
	</section>
	<section id="showcase" class="px-3 sm:px-5">
		<img src="/public/assets/image/Components/bg.png" alt="Фон секции услуг" title="Фон секции услуг" aria-hidden="true" loading="lazy" decoding="async" class="showcase-bg">
		<div class="showcase-light view max-w-[1240px] mx-auto px-5 py-8 md:p-10 relative overflow-hidden">
			<div class="show-head view">
				<span class="show-pill">18 услуг</span>
				<h2>Выберите услугу — остальное за нами</h2>
				<p>Производство и монтаж быстровозводимых зданий, капитальный ремонт и спецтехника — с фиксированной ценой в договоре.</p>
			</div>
			<div class="fss-stage" id="fssStage2">
				<div class="fss-glow fss-glow-l" aria-hidden="true"></div>
				<div class="fss-glow fss-glow-r" aria-hidden="true"></div>
				<ul class="fss-cards" aria-label="Выберите услугу">
					<li class="fss-card" style="--cx:250;--cy:430;--angle:-10deg" tabindex="0" aria-label="Склады под ключ">
						<div class="fss-art" aria-hidden="true"><img src="/public/assets/image/swiper/2.jpg" alt="Пример склада из сэндвич-панелей" loading="lazy" decoding="async" onerror="this.style.display=.none." title="Пример склада из сэндвич-панелей"></div>
						<div class="fss-copy"><h3>Склады<br>под ключ</h3><p>Логистика<br>любой площади</p></div>
					</li>
					<li class="fss-card" style="--cx:750;--cy:380;--angle:-3deg" tabindex="0" aria-label="Ангары под ключ">
						<div class="fss-art" aria-hidden="true"><img src="/public/assets/image/services/капстроение/10.JPG" alt="Пример капитального строительства" loading="lazy" decoding="async" onerror="this.style.display=.none." title="Пример капитального строительства"></div>
						<div class="fss-copy"><h3>Ангары<br>под ключ</h3><p>Навесы и укрытия<br>для техники</p></div>
					</li>
					<li class="fss-card" style="--cx:1250;--cy:380;--angle:3deg" tabindex="0" aria-label="АБК и модули">
						<div class="fss-art" aria-hidden="true"><img src="/public/assets/image/swiper/1.jpeg" alt="Пример ангара под ключ" loading="lazy" decoding="async" onerror="this.style.display=.none." title="Пример ангара под ключ"></div>
						<div class="fss-copy"><h3>АБК<br>и модули</h3><p>Офисы и корпуса<br>под ключ</p></div>
					</li>
					<li class="fss-card" style="--cx:1750;--cy:430;--angle:10deg" tabindex="0" aria-label="Ваша идея">
						<div class="fss-art" aria-hidden="true"><img src="/public/assets/image/banner.jpeg" alt="Пример модульного здания" loading="lazy" decoding="async" onerror="this.style.display=.none." title="Пример модульного здания"></div>
						<div class="fss-copy"><h3>Ваша<br>идея</h3><p>Не нашли нужное?<br>Соберём под задачу</p></div>
					</li>
				</ul>
			</div>
			<div class="show-cta view">
				<span class="show-cta-n">180+</span>
				<span class="show-cta-t">сданных объектов<br>под ключ</span>
				<a href="#contact" class="show-cta-btn" data-goal="cta_showcase_contact" title="Рассчитать стоимость проекта за 15 минут">Смета за 15 минут</a>
				<span class="show-cta-time"><i class="fa-solid fa-bolt"></i> 7 мин</span>
			</div>
		</div>
	</section>


						<!-- Process Section -->
	<section id="process" class="view relative isolate z-[30] w-full bg-[#f3f4ef] text-[#171310]">
		<div class="relative mx-auto max-w-[1280px] px-4 pt-[72px] pb-[80px]">
			<div class="section-heading">
				<h2>Этапы работы</h2>
				<span class="heading-divider" aria-hidden="true"></span>
				<p>От заявки до сдачи — 5 шагов под ключ: проектирование, производство и монтаж</p>
			</div>

			<div class="steps3">
				<article class="step3">
					<img class="step3-img" src="/public/assets/image/services/капстроение/10.JPG" alt="Заявка и смета на модульное здание" title="Заявка и смета на модульное здание" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'">
					<h3>Заявка и смета</h3>
					<p>Оставляете заявку — инженер перезвонит за 15 минут и рассчитает фиксированную смету.</p>
					<span class="step3-n">01</span>
				</article>
				<article class="step3">
					<img class="step3-img" src="/public/assets/image/swiper/2.jpg" alt="Проектирование модульного здания" title="Проектирование модульного здания" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'">
					<h3>Проектирование</h3>
					<p>Готовим чертежи, рассчитываем нагрузку и согласовываем проект с вами.</p>
					<span class="step3-n">02</span>
				</article>
				<article class="step3 step3-photo">
					<img src="/public/assets/image/banner.jpeg" alt="Производство модулей на заводе" title="Производство модулей на заводе" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'">
					<div class="step3-ovl">
						<h3>Фундамент</h3>
						<p>Устраиваем основание: свайное, ленточное или столбчатое под модули.</p>
						<span class="step3-n">03</span>
					</div>
				</article>
				<article class="step3">
					<img class="step3-img" src="/public/assets/image/swiper/1.jpeg" alt="Производство модулей из сэндвич-панелей" title="Производство модулей из сэндвич-панелей" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'">
					<h3>Производство</h3>
					<p>Изготавливаем модули из сэндвич-панелей на собственном заводе БВЗ.</p>
					<span class="step3-n">04</span>
				</article>
				<article class="step3">
					<img class="step3-img" src="/public/assets/image/swiper/3.jpeg" alt="Монтаж и сдача модульного здания" title="Монтаж и сдача модульного здания" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'">
					<h3>Монтаж и сдача</h3>
					<p>Собираем, подключаем коммуникации и сдаём объект под ключ.</p>
					<span class="step3-n">05</span>
				</article>
			</div>

			<div class="steps3-nav" role="group" aria-label="Навигация по этапам работы">
				<span class="steps3-count" aria-hidden="true"><b>01</b> / 05</span>
				<div class="steps3-dots"></div>
			</div>

		</div>
	</section>

	<!-- CTA-баннер: попробуйте -->
	<section class="view cta-band">
		<img src="/public/assets/image/Components/banner/hero.jpg" alt="Быстровозводимые здания ФСС — строим то, что ведёт вас вперёд" title="Быстровозводимые здания ФСС — строим то, что ведёт вас вперёд" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'">
		<div class="cta-in">
			<span class="cta-eyebrow"><i></i>Ваш проект — наше дело</span>
			<h2>Строим то,<br>что ведёт вас вперёд</h2>
			<p>Проектируем, производим и монтируем здания, которые служат десятилетиями.</p>
			<a href="#contact" class="cta-pill" data-goal="cta_band_contact" title="Заказать бесплатную консультацию и расчёт сметы">Построить с нами <i class="fa-solid fa-arrow-right"></i></a>
		</div>
	</section>


	<section id="advantages" class="view relative isolate z-[10] w-full bg-[#f3f4ef] text-[#3d5951]">
		<div class="relative mx-auto max-w-[1625px] px-4 pt-[72px]">
			<div class="section-heading">
				<h2>Мы выполняем</h2>
				<span class="heading-divider" aria-hidden="true"></span>
				<p>18 услуг — от проекта и завода до монтажа и сервиса</p>
			</div>
			<div class="mx-auto max-w-[1280px]">
			<div class="services-grid mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
				<a href="/services/kapital" class="view group" title="Капитальное строительство и ремонт под ключ" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-screwdriver-wrench text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Капитальный ремонт</div>
					<div class="text-xs text-gray-500 mt-1">Школы, АБК, фасады, кровля</div>

				</a>
				<a href="/services/obchegitia" class="view group" title="Строительство общежитий и жилых комплексов" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-building-user text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Общежития</div>
					<div class="text-xs text-gray-500 mt-1">Жилые комплексы, вахтовые</div>

				</a>
				<a href="/services/proizvodim_modul" class="view group" title="Производство модульных зданий на собственном заводе" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-cubes text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Производство модульных зданий</div>
					<div class="text-xs text-gray-500 mt-1">Завод БВЗ, блок-контейнеры</div>

				</a>
				<a href="/services/modultebel" class="view group" title="Поставка и сборка мебели для офисов и общежитий" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-chair text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Поставка и сборка мебели</div>
					<div class="text-xs text-gray-500 mt-1">Для офисов, школ, общежитий</div>

				</a>
				<div class="view flex flex-col justify-center rounded-[30px] bg-[#3d5951] p-5">
					<div class="font-black leading-none text-[#ddf47a]" style="font-size:34px">180+</div>
					<div class="mt-2 text-xs font-semibold text-white">сданных объектов</div>
				</div>
				<a href="/services/proektirovanie" class="view group" title="Проектирование БВЗ и инженерных сетей" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-ruler-combined text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Проектирование</div>
					<div class="text-xs text-gray-500 mt-1">Чертежи, расчёты, согласования</div>

				</a>
				<a href="/services/montach" class="view group" title="Монтаж инженерных сетей и систем отопления" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-bolt text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Монтаж инженерных сетей</div>
					<div class="text-xs text-gray-500 mt-1">Отопление, электрика, вода</div>

				</a>
				<a href="/services/administrativnoe" class="view group" title="Строительство АБК и офисных зданий" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-building-columns text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">АБК и офисы</div>
					<div class="text-xs text-gray-500 mt-1">Административные здания</div>

				</a>
				<a href="/services/generatornae" class="view group" title="Поставка и монтаж дизель-генераторных установок" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-plug-circle-bolt text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Генераторные</div>
					<div class="text-xs text-gray-500 mt-1">ДГУ, энергокомплексы</div>

				</a>
				<div class="view flex flex-col justify-center rounded-[30px] bg-[#3d5951] p-5">
					<div class="font-black leading-none text-[#ddf47a]" style="font-size:34px">14 лет</div>
					<div class="mt-2 text-xs font-semibold text-white">на рынке с <?= seo_config('founded_market') ?> года</div>
				</div>
				<a href="/services/naves" class="view group" title="Навесы и козырьки из металлоконструкций" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-umbrella text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Навесы</div>
					<div class="text-xs text-gray-500 mt-1">Металлоконструкции</div>

				</a>
				<a href="/services/compressor" class="view group" title="Газовые и промышленные компрессоры" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-gauge text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Газовые компрессоры</div>
					<div class="text-xs text-gray-500 mt-1">Поставка и монтаж</div>

				</a>
				<a href="/services/compressor_air" class="view group" title="Винтовые и поршневые воздушные компрессоры" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-fan text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Воздушные компрессоры</div>
					<div class="text-xs text-gray-500 mt-1">Винтовые, поршневые</div>

				</a>
				<a href="/services/sclad" class="view group" title="Строительство складов из сэндвич-панелей" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-warehouse text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Склады</div>
					<div class="text-xs text-gray-500 mt-1">Логистические центры</div>

				</a>
				<div class="view flex flex-col justify-center rounded-[30px] bg-[#3d5951] p-5">
					<div class="font-black leading-none text-[#ddf47a]" style="font-size:34px">50+ лет</div>
					<div class="mt-2 text-xs font-semibold text-white">гарантии на конструкции</div>
				</div>
				<a href="/services/musorbak" class="view group" title="Мусорные баки и контейнеры для вывоза отходов" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-dumpster text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Мусорные баки</div>
					<div class="text-xs text-gray-500 mt-1">Контейнеры, оцинковка</div>

				</a>
				<a href="/services/gruzoperevozki" class="view group" title="Грузоперевозки по Москве, МО и всей России" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-truck-ramp-box text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Грузоперевозки</div>
					<div class="text-xs text-gray-500 mt-1">Авто и ЖД</div>

				</a>
				<a href="/services/spectehnika" class="view group" title="Аренда строительной спецтехники и услуги" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-tractor text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Спецтехника</div>
					<div class="text-xs text-gray-500 mt-1">Аренда, услуги</div>

				</a>
				<a href="/services/kapstroenie" class="view group" title="Капитальное строение зданий и сооружений" bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-building-shield text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Капитальное строение</div>
					<div class="text-xs text-gray-500 mt-1">Здания и сооружения</div>

				</a>
				<div class="view flex flex-col justify-center rounded-[30px] bg-[#3d5951] p-5">
					<div class="font-black leading-none text-[#ddf47a]" style="font-size:34px">15 минут</div>
					<div class="mt-2 text-xs font-semibold text-white">на точную смету</div>
				</div>
			</div>
			<div class="mt-10 flex flex-col items-center sm:flex-row gap-[20px] justify-center">
							<a href="#contact" data-goal="cta_advantages_contact" title="Смета за 15 минут" class="flex h-[56px] w-[250px] items-center justify-center rounded-full border-2 border-[#3d5951] text-[18px] font-medium transition-colors hover:bg-[#3d5951] hover:text-white">Смета за 15 минут</a>
				<a href="/uslugi" title="Все услуги ФСС: производство, монтаж, ремонт, спецтехника" class="flex h-[54px] w-[250px] items-center justify-center rounded-full bg-[#ddf47a] text-[18px] font-medium text-[#18251e] transition-colors hover:bg-[#cce968]">Все услуги</a>
			</div>
			</div>
		</div>
		<!-- Organic bottom edge -->
		<div class="pointer-events-none absolute inset-x-0 -bottom-[86px] z-20 h-[86px]">
			<div class="absolute -top-px right-0 h-[87px] w-[36%] rounded-bl-[82px] bg-[#f3f4ef]"></div>
		</div>
	</section>



	<!-- Contact — High converting -->
<div class="px-3 sm:px-5">
	<section id="contact" class="view relative isolate z-[20] overflow-hidden rounded-[28px] bg-[#FFB77D] px-5 py-12 sm:px-8 lg:min-h-[760px] lg:px-12 lg:py-16">
		<img src="/public/assets/image/Components/element.png" alt="Декоративный элемент" aria-hidden="true" loading="lazy" decoding="async" class="contact-element" title="Декоративный элемент">
		<div class="pointer-events-none absolute -bottom-40 -left-24 h-[480px] w-[480px] rounded-full bg-[#FF9A45]/70 blur-[1px]" aria-hidden="true"></div>
		<div class="pointer-events-none absolute -bottom-52 left-24 h-[440px] w-[440px] rounded-full bg-[#FFD0A7]" aria-hidden="true"></div>
		<div class="pointer-events-none absolute -bottom-64 left-0 h-[350px] w-[350px] rotate-[-35deg] rounded-[80px] bg-gradient-to-br from-[#FFE2C8] via-[#FFB77D] to-[#FF8A2A] shadow-[inset_8px_8px_18px_rgba(255,255,255,0.6),inset_-8px_-8px_20px_rgba(205,80,0,0.15)]" aria-hidden="true"></div>
		<div class="relative z-10 mx-auto grid max-w-[1440px] grid-cols-1 items-start gap-10 lg:grid-cols-[1.05fr_1fr] lg:gap-12">
			<div class="flex flex-col items-start pt-2 lg:pt-0">
				<h2 class="max-w-[650px] text-4xl font-semibold leading-[1.02] tracking-[-0.055em] text-white sm:text-5xl lg:text-6xl xl:text-7xl">
					Рассчитаем стоимость
					<br class="hidden sm:block">
					за 15 минут
				</h2>
				<p class="mt-7 max-w-[460px] text-base leading-relaxed text-white/95 sm:text-lg">
					Оставьте контакты — инженер перезвонит, уточнит задачу и пришлёт смету с фиксированной ценой
				</p>
				<a href="tel:<?= htmlspecialchars(preg_replace("/\D/", "", seo_config("phone"))) ?>" title="Позвонить: +7 938 018-00-06" class="mt-8 inline-flex items-center gap-2 rounded-full bg-white/20 px-6 py-3 font-semibold text-white backdrop-blur transition hover:bg-white/30"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars((string)seo_config('phone_display')) ?></a>
			</div>
			<div class="w-full rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(130,55,0,0.08)] sm:p-8 lg:p-10 xl:p-12">
				<div class="mb-8 flex items-start justify-between gap-5">
					<h3 class="max-w-[540px] text-2xl font-medium leading-tight tracking-[-0.035em] text-[#333333] sm:text-3xl">
						Заполните форму, инженер перезвонит за 15 минут
					</h3>
					<img
						src="/public/assets/image/Components/avatar/1.png"
						alt="Ваш менеджер — ФСС"
						title="Ваш менеджер — ФСС"
						loading="lazy" decoding="async" onerror="this.style.display='none'"
						class="hidden h-16 w-16 shrink-0 rounded-full object-cover sm:block"
					>
				</div>
				<form method="POST" action="/send/email" novalidate class="space-y-3" data-form="contact">
					<input type="hidden" name="mailer">
					<?php include __DIR__ . '/../components/antibot_fields.php'; ?>
					<input type="hidden" name="message" id="contact_message_hidden" value="Заявка с главной страницы">
					<input
						type="text"
						name="name"
						id="contact_name"
						placeholder="Ваше имя"
						aria-label="Ваше имя"
						autocomplete="name"
						required
						minlength="2"
						maxlength="100"
						pattern="^[A-Za-zА-Яа-яЁё\s\-\.\']+$"
						class="h-[76px] w-full rounded-2xl border border-transparent bg-[#FFF7F1] px-7 text-base text-[#333333] outline-none transition placeholder:text-[#B8B8B8] focus:border-[#F97316] focus:bg-white focus:ring-4 focus:ring-[#F97316]/10"
					>
					<input
						type="text"
						name="company"
						id="contact_company"
						placeholder="Ваша компания"
						aria-label="Ваша компания"
						autocomplete="organization"
						maxlength="100"
						class="h-[76px] w-full rounded-2xl border border-transparent bg-[#FFF7F1] px-7 text-base text-[#333333] outline-none transition placeholder:text-[#B8B8B8] focus:border-[#F97316] focus:bg-white focus:ring-4 focus:ring-[#F97316]/10"
					>
					<input
						type="email"
						name="email"
						id="contact_email"
						placeholder="Email"
						aria-label="Email"
						autocomplete="email"
						required
						maxlength="254"
						class="h-[76px] w-full rounded-2xl border border-transparent bg-[#FFF7F1] px-7 text-base text-[#333333] outline-none transition placeholder:text-[#B8B8B8] focus:border-[#F97316] focus:bg-white focus:ring-4 focus:ring-[#F97316]/10"
					>
					<input
						type="tel"
						name="phone"
						id="phone"
						placeholder="+7 (000) 000-00-00"
						aria-label="Телефон"
						autocomplete="tel"
						inputmode="tel"
						required
						maxlength="18"
						pattern="^\+?[0-9\s\-\(\)]{10,20}$"
						class="h-[76px] w-full rounded-2xl border border-transparent bg-[#FFF7F1] px-7 text-base text-[#333333] outline-none transition placeholder:text-[#B8B8B8] focus:border-[#F97316] focus:bg-white focus:ring-4 focus:ring-[#F97316]/10"
					>
					<div class="relative">
						<select
							name="product"
							id="contact_product"
							aria-label="Какая услуга вас интересует"
							required
							class="h-[76px] w-full appearance-none rounded-2xl border border-transparent bg-[#FFF7F1] px-7 pr-14 text-base text-[#858585] outline-none transition focus:border-[#F97316] focus:bg-white focus:ring-4 focus:ring-[#F97316]/10"
						>
							<option value="" selected disabled>Какая услуга вас интересует?</option>
							<option value="Быстровозводимые здания">Быстровозводимые здания</option>
							<option value="Капитальный ремонт">Капитальный ремонт</option>
							<option value="Модульные здания">Модульные здания</option>
							<option value="Общежития">Общежития</option>
							<option value="Спецтехника">Спецтехника</option>
							<option value="Грузоперевозки">Грузоперевозки</option>
							<option value="Другое">Другое</option>
						</select>
						<svg
							class="pointer-events-none absolute right-7 top-1/2 h-4 w-4 -translate-y-1/2 text-[#888888]"
							viewBox="0 0 20 20"
							fill="currentColor"
							aria-hidden="true"
						>
							<path d="m4 7 6 6 6-6H4Z"/>
						</svg>
					</div>
					<div class="space-y-3 pt-1 text-sm leading-relaxed text-[#858585]">
						<label class="flex cursor-pointer items-start gap-3">
							<input
								type="checkbox"
								name="checkbox"
								value="on"
								required
								class="mt-0.5 h-5 w-5 shrink-0 accent-[#F97316]"
							>
							<span>
								Я согласен
								<a href="/politika" title="Политика конфиденциальности" class="text-[#F97316] underline underline-offset-4 hover:text-[#D95C00]">
									с условиями политики конфиденциальности
								</a>
							</span>
						</label>
						<label class="flex cursor-pointer items-start gap-3">
							<input
								type="checkbox"
								name="personal_data"
								value="on"
								required
								class="mt-0.5 h-5 w-5 shrink-0 accent-[#F97316]"
							>
							<span>
								Я согласен
								<a href="/soglasie" title="Согласие на обработку данных" class="text-[#F97316] underline underline-offset-4 hover:text-[#D95C00]">
									на обработку моих персональных данных
								</a>
							</span>
						</label>
						<label class="flex cursor-pointer items-start gap-3">
							<input
								type="checkbox"
								name="marketing"
								value="on"
								class="mt-0.5 h-5 w-5 shrink-0 accent-[#F97316]"
							>
							<span>
								Я согласен на получение информационных и рекламных сообщений
							</span>
						</label>
					</div>
					<div class="pt-5">
						<button
							type="submit"
							class="inline-flex min-h-[68px] items-center justify-center rounded-full bg-[#F97316] px-10 py-4 text-base font-medium text-white shadow-[0_8px_24px_rgba(249,115,22,0.2)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#EA6508] hover:shadow-[0_12px_28px_rgba(249,115,22,0.28)] focus:outline-none focus:ring-4 focus:ring-[#F97316]/25 active:translate-y-0"
						>
							Получить расчет за 15 минут
						</button>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>


	<!-- 6. Отзывы клиентов (видимые + schema.org) -->
	<section id="reviews" class="fss-reviews-section view">
	<div class="fss-reviews-container">
		<div class="section-heading">
			<h2>Отзывы наших клиентов</h2>
			<span class="heading-divider" aria-hidden="true"></span>
			<p><?= seo_config('objects') ?> объектов за <?= seo_config('founded_market') ?> года — реальные истории</p>
		</div>
		<div class="fss-reviews-swiper swiper reviews-swiper">
			<div class="swiper-wrapper">
				<?php foreach (seo_reviews() as $r): ?>
				<div class="swiper-slide">
					<div class="fss-review-card">
						<div class="fss-review-stars" aria-label="Рейтинг <?= $r['star'] ?> из 5">
							<?php for ($i = 0; $i < 5; $i++): ?>
							<?= $i < $r['star'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>' ?>
							<?php endfor ?>
						</div>
						<p class="fss-review-text"><?= htmlspecialchars($r['text'], ENT_QUOTES) ?></p>
						<div class="fss-review-author">
							<strong><?= htmlspecialchars($r['name'], ENT_QUOTES) ?></strong>
							<span><?= htmlspecialchars($r['company'], ENT_QUOTES) ?></span>
							<time datetime="<?= $r['date'] ?>"><?= $r['date'] ?></time>
						</div>
					</div>
				</div>
				<?php endforeach ?>
			</div>
			<div class="reviews-pagination swiper-pagination"></div>
		</div>
	</div>
	</section>

	<section id="portfolio" class="works-band view relative z-[15]">
		<div class="works-inner">
			<div class="absolute right-0 top-[76px] hidden h-14 w-14 items-center justify-center rounded-2xl bg-[#f5f5f4] border border-[#e7e5e4] text-[#171310] xl:flex" aria-hidden="true">
				<i class="fa-solid fa-helmet-safety text-xl"></i>
			</div>
			<div class="section-heading">
				<h2>Наши работы</h2>
				<span class="heading-divider" aria-hidden="true"></span>
				<p>Склады, общежития и школы — смотрите, что сдаём под ключ</p>
			</div>
			<div class="works-grid">
				<div class="works-left">
					<div class="works-text" id="worksText">
						<div class="works-num" id="worksNum">01</div>
						<h3 class="works-name" id="worksName">Модульное общежитие на 120 мест</h3>
						<p class="works-desc" id="worksDesc">Два этажа, тёплый контур, кондиционеры в каждой комнате. Сдали за 30 дней — заселили сразу, без доводок.</p>
					</div>
					<div class="works-nav">
						<button type="button" class="works-btn prev" id="worksPrev" aria-label="Предыдущий объект" data-goal="works_prev"><i class="fa-solid fa-arrow-left"></i></button>
						<button type="button" class="works-btn next" id="worksNext" aria-label="Следующий объект" data-goal="works_next"><i class="fa-solid fa-arrow-right"></i></button>
					</div>
					<div class="works-foot">
						<span class="works-count" id="worksCount">01/03</span>
						<a href="/portfolio" title="Смотреть все объекты" class="works-all">Смотреть все объекты <i class="fa-solid fa-arrow-right"></i></a>
					</div>
				</div>
				<div class="works-right">
					<div class="works-stack" id="worksStack">
						<div class="works-card back"><img id="worksBack" src="/public/assets/image/swiper/2.jpg" alt="Склад из сэндвич-панелей — объект ФСС" title="Склад из сэндвич-панелей — объект ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'"></div>
						<div class="works-card mid"><img id="worksMid" src="/public/assets/image/banner.jpeg" alt="Модульные здания — производство ФСС" title="Модульные здания — производство ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'"></div>
						<div class="works-card front"><img id="worksFront" src="/public/assets/image/swiper/1.jpeg" alt="Модульное общежитие из сэндвич-панелей — объект ФСС" title="Модульное общежитие из сэндвич-панелей — объект ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/favicon.svg'"></div>
					</div>
				</div>
			</div>

		</div>
	</section>
	<!-- Бегущая строка направлений -->
	<section class="marquee-band" aria-hidden="true">
		<div class="marquee-track"><span class="marquee-item">Капитальный ремонт</span><span class="marquee-item">Поставка мебели</span><span class="marquee-item">Быстровозводимые здания</span><span class="marquee-item">Склады</span><span class="marquee-item">Ангары</span><span class="marquee-item">Навесы</span><span class="marquee-item">Дизель-генераторы</span><span class="marquee-item">Компрессорные станции</span><span class="marquee-item">Блочно-модульные конструкции</span><span class="marquee-item">Монтаж инженерных систем</span><span class="marquee-item">Проектирование объектов</span><span class="marquee-item">Техническое обслуживание</span><span class="marquee-item">Ремонт кровли</span><span class="marquee-item">Внутренняя отделка</span><span class="marquee-item">Промышленные полы</span><span class="marquee-item">Фасадные работы</span><span class="marquee-item">Пожарная безопасность</span><span class="marquee-item">Ландшафтные работы</span><span class="marquee-item">Капитальный ремонт</span><span class="marquee-item">Поставка мебели</span><span class="marquee-item">Быстровозводимые здания</span><span class="marquee-item">Склады</span><span class="marquee-item">Ангары</span><span class="marquee-item">Навесы</span><span class="marquee-item">Дизель-генераторы</span><span class="marquee-item">Компрессорные станции</span><span class="marquee-item">Блочно-модульные конструкции</span><span class="marquee-item">Монтаж инженерных систем</span><span class="marquee-item">Проектирование объектов</span><span class="marquee-item">Техническое обслуживание</span><span class="marquee-item">Ремонт кровли</span><span class="marquee-item">Внутренняя отделка</span><span class="marquee-item">Промышленные полы</span><span class="marquee-item">Фасадные работы</span><span class="marquee-item">Пожарная безопасность</span><span class="marquee-item">Ландшафтные работы</span></div>
	</section>
	<!-- Почему выбирают: секция «Бесперебойная работа и безопасность» (точная копия референса) -->
	<section id="pluses" class="view pluses-band">
		<section class="security-section">
			<h2 class="security-title">Почему выбирают нас?</h2>

			<div class="security-grid">

				<!-- 1. Большая карточка -->
				<article class="security-card security-main">
					<h3>Полный контроль 24/7</h3>
					<p>Инженер на площадке, фотоотчёт каждую неделю и технадзор на каждом этапе — вы в курсе прогресса без звонков.</p>
					<div class="security-shield" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></div>
				</article>

				<!-- 2. Соблюдение / договор -->
				<article class="security-card security-compliance">
					<h3>Фиксируем цену и сроки</h3>
					<p>Смета за 15 минут. Закрепляем стоимость и сроки в договоре — цена не растёт по ходу стройки.</p>
					<div class="document-stack" aria-hidden="true">
						<div class="document document-back">
							<div class="document-line"></div>
							<div class="document-line"></div>
							<div class="document-line"></div>
						</div>
						<div class="document">
							<div class="document-line"></div>
							<div class="document-line"></div>
							<div class="document-check">✓</div>
							<div class="document-check">✓</div>
							<div class="document-check">✓</div>
						</div>
					</div>
				</article>

				<!-- 3. Сроки -->
				<article class="security-card security-storage">
					<h3>Сроки<br />от 14 дней</h3>
					<p>Сдаём модуль за 2–12 недель вместо 3–6 месяцев классической стройки.</p>
				</article>

				<!-- 4. Стата -->
				<article class="security-card security-uptime">
					<div class="gauge" aria-hidden="true">
						<div class="gauge-ring"></div>
						<div class="gauge-needle"></div>
					</div>
					<div class="uptime-number">180+</div>
					<p>сданных объектов<br />на рынке с <?= seo_config('founded_market') ?> года</p>
				</article>

				<!-- 5. Техника -->
				<article class="security-card security-versions">
					<span class="security-ico" aria-hidden="true"><i class="fa-solid fa-truck-fast"></i></span>
					<h3>Своя техника<br />и доставка</h3>
				</article>

				<!-- 6. Производство -->
				<article class="security-card security-requests">
					<h3>Собственный завод БВЗ —<br />модули без посредников</h3>
					<p>Производим сэндвич-панели и блок-контейнеры сами — контроль качества и цена ниже рынка.</p>
				</article>

				<!-- 7. Гарантия -->
				<article class="security-card security-support">
					<span class="security-ico" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
					<h3>Гарантия<br class="hidden sm:block" />50+ лет</h3>
				</article>

			</div>
		</section>
	</section>
	<div hidden aria-hidden="true">
			<div class="swipers overflow-hidden">
				<div class="swiper-wrapper" style="height: 500px">


					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/dostavka.MP4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-trash-alt text-emerald-600 text-xl mr-2"></i>»
									<b>Мусорные
										баки</b>
								</h3>
								</h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>

					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/dostavkaKD.MP4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-trash-alt text-emerald-600 text-xl mr-2"></i>»
									<b>Мусорные
										баки</b>
								</h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>

					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/schakman.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-trash-alt text-emerald-600 text-xl mr-2"></i>» <b>Мусорные
										баки</b></h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>

					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/tracktor.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-school text-emerald-600 text-xl mr-2"></i>» <b>Капитальный
										ремонт школ</b>
								</h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>

					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/buldozer.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-school text-emerald-600 text-xl mr-2"></i>» <b>Капитальный
										ремонт школ</b></h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>

					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/buldozer.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-school text-emerald-600 text-xl mr-2"></i>» <b>Капитальный
										ремонт школ</b></h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>

					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/schakman.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-chair text-emerald-600 text-xl mr-2"></i>» <b>Производство
										мебели</b></h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>

					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/dostavka.MP4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-bus text-emerald-600 text-xl mr-2"></i>»
									<b>Автотранспортные доставки</b>
								</h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>


					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/dostavkaKD.MP4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-train text-emerald-600 text-xl mr-2"></i>»
									<b>Железнодорожные доставки</b>
								</h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>


					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/buldozer.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-dumpster text-emerald-600 text-xl mr-2"></i>»
									<b>Спецтехника - Бульдозер</b>
								</h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>


					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/tracktor.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-tractor text-emerald-600 text-xl mr-2"></i>»
									<b>Спецтехника - Трактор</b>
								</h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>

					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" muted loop playsinline
							src="/public/assets/image/banner/schakman.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
						<!-- темное затемнение -->
						<div class="bg-black w-full h-full opacity-50 absolute"></div>
						<div class="viewLeft slide-content">
							<!-- содержание -->
							<div class="viewLeft flex slide-had-content justify-center items-center gap-2 font-bold">
								<div
									class="viewLeft  w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
									<i class="viewLeft fas fa-bolt text-emerald-600 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-truck text-emerald-600 text-xl mr-2"></i>»
									<b>Спецтехника - Шакман</b>
								</h3>
							</div>
						</div>
						<!-- конец содержание-->
					</div>


				</div>
				<!-- Кнопки навигации и пагинация -->
				<div class="relative w-full flex items-center justify-between mt-6">
					<div class="swiper-paginations flex-1 flex justify-center"></div>
				</div>
				<div class="relative w-full flex items-center justify-between mt-6">
					<a href="/portfolio" title="Посмотреть все проекты"
						class="inline-flex items-center gap-2 bg-gray-900 hover:bg-black text-white px-7 py-3 rounded-full font-semibold shadow viewLeft mx-auto">Посмотреть
						все проекты</a>
				</div>
			</div>
	</div>
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
			<a href="#contact" title="Рассчитать стоимость" class="flex-1 inline-flex items-center justify-center gap-2 bg-emerald-600 text-white rounded-full py-3 font-semibold shadow">Рассчитать <i class="fa-solid fa-arrow-right text-xs"></i></a>
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
		}
	</script>

<!-- Cinema Hero JS — exact Protenance copy -->
	<script>
	(function () {
		"use strict";
		var hero = document.querySelector('.hero');
		if (!hero) return;

		var images = Array.prototype.slice.call(hero.querySelectorAll('.cinema-image'));
		var buttons = Array.prototype.slice.call(hero.querySelectorAll('[data-cinema-select]'));
		var statusEl = hero.querySelector('[data-cinema-status]');
		var projectLink = hero.querySelector('[data-cinema-link]');
		var projectName = hero.querySelector('[data-cinema-name]');
		var projectCaption = hero.querySelector('[data-cinema-caption]');
		var projectNumber = hero.querySelector('[data-cinema-number]');

		if (!images.length || !buttons.length) return;

		var activeIndex = 0;
		var timer = null;
		var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

		function setActive(index) {
			if (index === activeIndex) return;
			activeIndex = index;
			images.forEach(function (img, i) {
				img.classList.toggle('is-active', i === index);
			});
			buttons.forEach(function (btn, i) {
				btn.setAttribute('aria-pressed', i === index ? 'true' : 'false');
			});
			if (projectName) projectName.textContent = buttons[index].getAttribute('data-name') || '';
			if (projectCaption) projectCaption.textContent = buttons[index].getAttribute('data-caption') || '';
			if (projectNumber) projectNumber.textContent = String(index + 1).padStart(2, '0');
			if (projectLink) projectLink.setAttribute('href', '/' + (buttons[index].getAttribute('data-project') || 'projects'));
			if (statusEl) statusEl.textContent = String(index + 1).padStart(2, '0') + ' / ' + String(buttons.length).padStart(2, '0') + ' \u2014 ' + (buttons[index].getAttribute('data-name') || '');
		}

		function next() {
			setActive((activeIndex + 1) % images.length);
		}

		function startTimer() {
			if (reduced.matches) return;
			timer = setInterval(next, 6000);
		}

		function stopTimer() {
			if (timer) clearInterval(timer);
			timer = null;
		}

		buttons.forEach(function (btn, i) {
			btn.addEventListener('click', function () {
				setActive(i);
				stopTimer();
				startTimer();
			});
		});

		hero.addEventListener('mouseenter', stopTimer);
		hero.addEventListener('mouseleave', startTimer);
		hero.addEventListener('focusin', stopTimer);
		hero.addEventListener('focusout', startTimer);

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () {
				setActive(0);
				startTimer();
			});
		} else {
			setActive(0);
			startTimer();
		}
	})();
	</script>

	<!-- FAQ главной (FAQPage + ответы для AI Overviews) -->
	<?php include_once 'public/components/home_faq.php'; ?>

	<!-- footer content -->
	<?php include_once 'public/components/footer.php'; ?>
	<!-- форма обратной связи -->


</body>

</html>