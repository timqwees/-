<?php
require_once __DIR__ . '/../../app/bootstrap.php';
$componentMailPath = __DIR__ . '/../../app/Mail/componet_mail.php';
if (file_exists($componentMailPath)) {
	include_once $componentMailPath;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ФАВОРИТ-СТРОЙ-СНАБ — Быстровозводимые здания под ключ | Москва</title>
	<meta name="description" content="Быстровозводимые здания под ключ в Москве: склады, ангары, АБК, общежития. Капремонт, грузоперевозки, спецтехника. От 14 дней, гарантия 50 лет. ☎ <?= htmlspecialchars((string)seo_config('phone_display')) ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<meta name="format-detection" content="telephone=yes">
	<meta name="yandex-verification" content="27e0ba9980498150" />
	<!-- Preconnect for performance -->
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
		<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
	<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/public/assets/image/logo/site.webmanifest" />
	<link rel="mask-icon" href="/public/assets/image/logo/favicon-96x96.png" color="#F97316">
	<!-- Canonical / hreflang / OG / Twitter — единый генератор (app/Seo/seo.php) -->
<?= seo_canonical_tags() ?>

<?= seo_og_tags('ФАВОРИТ-СТРОЙ-СНАБ — быстровозводимые здания под ключ в Москве', 'Строительство БВЗ от 14 дней, капитальный ремонт, грузоперевозки, спецтехника. Смета за 15 минут, фиксируем цену в договоре.') ?>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"></noscript>
	<style> body { font-family: 'Wix Madefor Display', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
	<!-- Preload critical -->
	<link rel="preload" href="/public/assets/distCSS/style.css?v=20261009" as="style">
	<link rel="preload" href="/public/assets/distJS/main.js" as="script">
	<!-- Styles -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261009">
	<!-- Swiper -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
	<script defer src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
	<script defer src="/public/assets/distJS/main.js"></script>

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

		/* Video mute/unmute control (hero) */
		.hero-video-control {
			position: absolute;
			bottom: 16px;
			right: 16px;
			z-index: 20;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 44px;
			height: 44px;
			border-radius: 50%;
			border: none;
			background: rgba(255, 255, 255, 0.15);
			backdrop-filter: blur(8px);
			color: #fff;
			cursor: pointer;
			transition: background-color 0.2s ease, transform 0.1s ease;
		}
		.hero-video-control:hover {
			background: rgba(255, 255, 255, 0.25);
		}
		.hero-video-control:active {
			transform: scale(0.92);
		}
		.hero-video-control:focus-visible {
			outline: 2px solid rgba(255, 255, 255, 0.6);
			outline-offset: 2px;
		}
		.hero-video-control__sound { display: inline-block; }
		.hero-video-control__muted { display: none; }
		.hero-video-control[aria-pressed="true"] .hero-video-control__sound { display: none; }
		.hero-video-control[aria-pressed="true"] .hero-video-control__muted { display: inline-block; }
	</style>
	<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
<?= seo_analytics() ?>

<?= seo_utm_script() ?>

<?= seo_goals_script() ?>

	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261009">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261014">
</head>

<body class="font-sans text-gray-800">

	<?php include_once __DIR__ . '/../components/header.php'; ?>

	<!-- HERO: точная копия JCREA-референса -->
	<section class="jc-hero view" id="home">
		<div class="hero-main">
			<div class="relative z-10 hero-top">
				<div class="hello-wrap"><span class="hello-pill">Привет!</span><span class="hello-burst" aria-hidden="true">〃</span></div>
				<h1 class="hero-title">Быстровозводимые <span class="text-brand font-medium">здания,</span><br>под ключ за 14 дней</h1>
			</div>
			<div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[510px] h-[255px] max-w-[80vw] rounded-t-full bg-[#ffad70]" aria-hidden="true"></div>
			<img class="portrait" src="/public/assets/image/Components/avatar/1.png" alt="Быстровозводимые здания под ключ — ФСС" fetchpriority="high" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'">
			<div class="quote">
				<div class="qmark" aria-hidden="true">❝</div>
				<p><b>180+ сданных объектов</b> — склады, ангары, АБК и общежития. Москва, МО и вся Россия</p>
			</div>
			<div class="experience">
				<div class="exp-stars" aria-hidden="true">★★★★★</div>
				<div class="exp-big">14 лет</div>
				<div class="exp-small">на рынке</div>
			</div>
			<div class="portfolio-pill">
				<a href="/portfolio" class="pp-main">Смотреть объекты <span aria-hidden="true">↗</span></a><a href="#contact" class="pp-text" data-goal="cta_hero_contact">Смета за 15 минут</a>
			</div>
			<button type="button" class="hero-video-control" id="hero-video-control" aria-label="Выключить звук" aria-pressed="false">
				<span class="hero-video-control__icon" aria-hidden="true">
					<svg class="hero-video-control__sound" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<polygon points="10 2 3 7v10a2 2 0 0 0 2 2h5a2 2 0 0 0 2-2V9a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-1"></polygon>
						<path d="M10 6v6a4 4 0 0 0 0 6h-2"></path>
					</svg>
					<svg class="hero-video-control__muted" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
						<polygon points="10 2 3 7v10a2 2 0 0 0 2 2h5a2 2 0 0 0 2-2V9a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-1"></polygon>
						<path d="M10 6v6a4 4 0 0 0 0 6h-2"></path>
						<line x1="1" y1="1" x2="23" y2="23"></line>
					</svg>
				</span>
			</button>
			<span class="hero-tilde" aria-hidden="true">〰</span>
		</div>
	</section>

	<!-- Тёмная секция услуг по референсу -->
	<section id="showcase" class="px-3 sm:px-5">
		<img src="/public/assets/image/Components/bg.png" alt="" aria-hidden="true" loading="lazy" decoding="async" class="showcase-bg">
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
						<div class="fss-art" aria-hidden="true"><img src="/public/assets/image/swiper/2.jpg" alt="" loading="lazy" decoding="async" onerror="this.style.display='none'"></div>
						<div class="fss-copy"><h3>Склады<br>под ключ</h3><p>Логистика<br>любой площади</p></div>
					</li>
					<li class="fss-card" style="--cx:750;--cy:380;--angle:-3deg" tabindex="0" aria-label="Ангары под ключ">
						<div class="fss-art" aria-hidden="true"><img src="/public/assets/image/services/капстроение/10.JPG" alt="" loading="lazy" decoding="async" onerror="this.style.display='none'"></div>
						<div class="fss-copy"><h3>Ангары<br>под ключ</h3><p>Навесы и укрытия<br>для техники</p></div>
					</li>
					<li class="fss-card" style="--cx:1250;--cy:380;--angle:3deg" tabindex="0" aria-label="АБК и модули">
						<div class="fss-art" aria-hidden="true"><img src="/public/assets/image/swiper/1.jpeg" alt="" loading="lazy" decoding="async" onerror="this.style.display='none'"></div>
						<div class="fss-copy"><h3>АБК<br>и модули</h3><p>Офисы и корпуса<br>под ключ</p></div>
					</li>
					<li class="fss-card" style="--cx:1750;--cy:430;--angle:10deg" tabindex="0" aria-label="Ваша идея">
						<div class="fss-art" aria-hidden="true"><img src="/public/assets/image/banner.jpeg" alt="" loading="lazy" decoding="async" onerror="this.style.display='none'"></div>
						<div class="fss-copy"><h3>Ваша<br>идея</h3><p>Не нашли нужное?<br>Соберём под задачу</p></div>
					</li>
				</ul>
			</div>
			<div class="show-cta view">
				<span class="show-cta-n">180+</span>
				<span class="show-cta-t">сданных объектов<br>под ключ</span>
				<a href="#contact" class="show-cta-btn" data-goal="cta_showcase_contact">Смета за 15 минут</a>
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
					<img class="step3-img" src="/public/assets/image/services/капстроение/10.JPG" alt="Заявка и смета на модульное здание" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'">
					<h3>Заявка и смета</h3>
					<p>Оставляете заявку — инженер перезвонит за 15 минут и рассчитает фиксированную смету.</p>
					<span class="step3-n">01</span>
				</article>
				<article class="step3">
					<img class="step3-img" src="/public/assets/image/swiper/2.jpg" alt="Проектирование модульного здания" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'">
					<h3>Проектирование</h3>
					<p>Готовим чертежи, рассчитываем нагрузку и согласовываем проект с вами.</p>
					<span class="step3-n">02</span>
				</article>
				<article class="step3 step3-photo">
					<img src="/public/assets/image/banner.jpeg" alt="Производство модулей на заводе" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'">
					<div class="step3-ovl">
						<h3>Фундамент</h3>
						<p>Устраиваем основание: свайное, ленточное или столбчатое под модули.</p>
						<span class="step3-n">03</span>
					</div>
				</article>
				<article class="step3">
					<img class="step3-img" src="/public/assets/image/swiper/1.jpeg" alt="Производство модулей из сэндвич-панелей" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'">
					<h3>Производство</h3>
					<p>Изготавливаем модули из сэндвич-панелей на собственном заводе БВЗ.</p>
					<span class="step3-n">04</span>
				</article>
				<article class="step3">
					<img class="step3-img" src="/public/assets/image/swiper/3.jpeg" alt="Монтаж и сдача модульного здания" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'">
					<h3>Монтаж и сдача</h3>
					<p>Собираем, подключаем коммуникации и сдаём объект под ключ.</p>
					<span class="step3-n">05</span>
				</article>
			</div>


		</div>
	</section>

	<!-- CTA-баннер: попробуйте -->
	<section class="view cta-band">
		<img src="/public/assets/image/Components/banner/hero.jpg" alt="Быстровозводимые здания ФСС — строим то, что ведёт вас вперёд" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'">
		<div class="cta-in">
			<span class="cta-eyebrow"><i></i>Ваш проект — наше дело</span>
			<h2>Строим то,<br>что ведёт вас вперёд</h2>
			<p>Проектируем, производим и монтируем здания, которые служат десятилетиями.</p>
			<a href="#contact" class="cta-pill" data-goal="cta_band_contact">Построить с нами <i class="fa-solid fa-arrow-right"></i></a>
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
			<div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
				<a href="/services/kapital" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-screwdriver-wrench text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Капитальный ремонт</div>
					<div class="text-xs text-gray-500 mt-1">Школы, АБК, фасады, кровля</div>

				</a>
				<a href="/services/obchegitia" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-building-user text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Общежития</div>
					<div class="text-xs text-gray-500 mt-1">Жилые комплексы, вахтовые</div>

				</a>
				<a href="/services/proizvodim_modul" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-cubes text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Производство модульных зданий</div>
					<div class="text-xs text-gray-500 mt-1">Завод БВЗ, блок-контейнеры</div>

				</a>
				<a href="/services/modultebel" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-chair text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Поставка и сборка мебели</div>
					<div class="text-xs text-gray-500 mt-1">Для офисов, школ, общежитий</div>

				</a>
				<div class="view flex flex-col justify-center rounded-[30px] bg-[#3d5951] p-6" style="min-height:180px">
					<div class="font-black leading-none text-[#ddf47a]" style="font-size:34px">180+</div>
					<div class="mt-2 text-xs font-semibold text-white">сданных объектов</div>
				</div>
				<a href="/services/proektirovanie" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-ruler-combined text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Проектирование</div>
					<div class="text-xs text-gray-500 mt-1">Чертежи, расчёты, согласования</div>

				</a>
				<a href="/services/montach" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-bolt text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Монтаж инженерных сетей</div>
					<div class="text-xs text-gray-500 mt-1">Отопление, электрика, вода</div>

				</a>
				<a href="/services/administrativnoe" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-building-columns text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">АБК и офисы</div>
					<div class="text-xs text-gray-500 mt-1">Административные здания</div>

				</a>
				<a href="/services/generatornae" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-plug-circle-bolt text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Генераторные</div>
					<div class="text-xs text-gray-500 mt-1">ДГУ, энергокомплексы</div>

				</a>
				<div class="view flex flex-col justify-center rounded-[30px] bg-[#3d5951] p-6" style="min-height:180px">
					<div class="font-black leading-none text-[#ddf47a]" style="font-size:34px">14 лет</div>
					<div class="mt-2 text-xs font-semibold text-white">на рынке с <?= seo_config('founded_market') ?> года</div>
				</div>
				<a href="/services/naves" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-umbrella text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Навесы</div>
					<div class="text-xs text-gray-500 mt-1">Металлоконструкции</div>

				</a>
				<a href="/services/compressor" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-gauge text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Газовые компрессоры</div>
					<div class="text-xs text-gray-500 mt-1">Поставка и монтаж</div>

				</a>
				<a href="/services/compressor_air" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-fan text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Воздушные компрессоры</div>
					<div class="text-xs text-gray-500 mt-1">Винтовые, поршневые</div>

				</a>
				<a href="/services/sclad" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-warehouse text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Склады</div>
					<div class="text-xs text-gray-500 mt-1">Логистические центры</div>

				</a>
				<div class="view flex flex-col justify-center rounded-[30px] bg-[#3d5951] p-6" style="min-height:180px">
					<div class="font-black leading-none text-[#ddf47a]" style="font-size:34px">50+ лет</div>
					<div class="mt-2 text-xs font-semibold text-white">гарантии на конструкции</div>
				</div>
				<a href="/services/musorbak" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-dumpster text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Мусорные баки</div>
					<div class="text-xs text-gray-500 mt-1">Контейнеры, оцинковка</div>

				</a>
				<a href="/services/gruzoperevozki" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-truck-ramp-box text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Грузоперевозки</div>
					<div class="text-xs text-gray-500 mt-1">Авто и ЖД</div>

				</a>
				<a href="/services/spectehnika" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-tractor text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Спецтехника</div>
					<div class="text-xs text-gray-500 mt-1">Аренда, услуги</div>

				</a>
				<a href="/services/kapstroenie" class="view group bg-white rounded-[30px] border border-[#e7e7db] p-5 hover:shadow-[0_20px_40px_rgba(15,23,42,.12)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-12 h-12 rounded-xl bg-[#f5f5f4] border border-[#e7e5e4] grid place-items-center text-[#171310]"><i class="fa-solid fa-building-shield text-lg"></i></div>
					<div class="mt-3 text-base font-bold leading-tight text-gray-900">Капитальное строение</div>
					<div class="text-xs text-gray-500 mt-1">Здания и сооружения</div>

				</a>
				<div class="view flex flex-col justify-center rounded-[30px] bg-[#3d5951] p-6" style="min-height:180px">
					<div class="font-black leading-none text-[#ddf47a]" style="font-size:34px">15 минут</div>
					<div class="mt-2 text-xs font-semibold text-white">на точную смету</div>
				</div>
			</div>
			<div class="mt-10 flex flex-col items-center sm:flex-row gap-[20px] justify-center">
				<a href="#contact" data-goal="cta_advantages_contact" class="flex h-[56px] w-[250px] items-center justify-center rounded-full border-2 border-[#3d5951] text-[18px] font-medium transition-colors hover:bg-[#3d5951] hover:text-white">Смета за 15 минут</a>
				<a href="/uslugi" class="flex h-[54px] w-[250px] items-center justify-center rounded-full bg-[#ddf47a] text-[18px] font-medium text-[#18251e] transition-colors hover:bg-[#cce968]">Все услуги</a>
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
		<img src="/public/assets/image/Components/element.png" alt="" aria-hidden="true" loading="lazy" decoding="async" class="contact-element">
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
				<a href="tel:<?= htmlspecialchars(preg_replace("/\D/", "", seo_config("phone"))) ?>" class="mt-8 inline-flex items-center gap-2 rounded-full bg-white/20 px-6 py-3 font-semibold text-white backdrop-blur transition hover:bg-white/30"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars((string)seo_config('phone_display')) ?></a>
			</div>
			<div class="w-full rounded-[28px] bg-white p-6 shadow-[0_20px_60px_rgba(130,55,0,0.08)] sm:p-8 lg:p-10 xl:p-12">
				<div class="mb-8 flex items-start justify-between gap-5">
					<h3 class="max-w-[540px] text-2xl font-medium leading-tight tracking-[-0.035em] text-[#333333] sm:text-3xl">
						Заполните форму, инженер перезвонит за 15 минут
					</h3>
					<img
						src="/public/assets/image/Components/avatar/1.png"
						alt="Ваш менеджер — ФСС"
						loading="lazy" decoding="async" onerror="this.style.display='none'"
						class="hidden h-16 w-16 shrink-0 rounded-full object-cover sm:block"
					>
				</div>
				<form method="POST" novalidate class="space-y-3" data-form="contact">
					<input type="hidden" name="mailer">
					<?php include_once __DIR__ . '/../components/antibot_fields.php'; ?>
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
								<a href="/politika" class="text-[#F97316] underline underline-offset-4 hover:text-[#D95C00]">
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
								<a href="/soglasie" class="text-[#F97316] underline underline-offset-4 hover:text-[#D95C00]">
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
						<a href="/portfolio" class="works-all">Смотреть все объекты <i class="fa-solid fa-arrow-right"></i></a>
					</div>
				</div>
				<div class="works-right">
					<div class="works-stack" id="worksStack">
						<div class="works-card back"><img id="worksBack" src="/public/assets/image/swiper/2.jpg" alt="Склад из сэндвич-панелей — объект ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></div>
						<div class="works-card mid"><img id="worksMid" src="/public/assets/image/banner.jpeg" alt="Модульные здания — производство ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></div>
						<div class="works-card front"><img id="worksFront" src="/public/assets/image/swiper/1.jpeg" alt="Модульное общежитие из сэндвич-панелей — объект ФСС" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></div>
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
							src="/public/assets/image/services/Мусор БАК/WhatsApp Video 2025-07-07 at 17.38.28-2.mp4" preload="none" poster="/public/assets/image/services/Мусор БАК/WhatsApp Image 2025-07-07 at 17.38.45.jpeg"></video>
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
							src="/public/assets/image/services/Мусор БАК/WhatsApp Video 2025-07-07 at 17.38.28-3.mp4" preload="none" poster="/public/assets/image/services/Мусор БАК/WhatsApp Image 2025-07-07 at 17.38.45.jpeg"></video>
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
							src="/public/assets/image/services/Мусор БАК/WhatsApp Video 2025-07-07 at 17.38.28.mp4" preload="none" poster="/public/assets/image/services/Мусор БАК/WhatsApp Image 2025-07-07 at 17.38.45.jpeg"></video>
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
							src="/public/assets/image/services/Школа фото/WhatsApp Video 2025-07-06 at 13.38.37.mp4" preload="none" poster="/public/assets/image/services/Школа фото/1.jpeg"></video>
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
							src="/public/assets/image/services/Школа фото/WhatsApp Video 2025-07-06 at 13.40.00-2.mp4" preload="none" poster="/public/assets/image/services/Школа фото/1.jpeg"></video>
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
							src="/public/assets/image/services/Школа фото/WhatsApp Video 2025-07-06 at 13.40.00.mp4" preload="none" poster="/public/assets/image/services/Школа фото/1.jpeg"></video>
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
							src="/public/assets/image/banner/2025-08-01 14.51.56.mp4" preload="none" poster="/public/assets/image/banner.jpeg"></video>
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
					<a href="/portfolio"
						class="inline-flex items-center gap-2 bg-gray-900 hover:bg-black text-white px-7 py-3 rounded-full font-semibold shadow viewLeft mx-auto">Посмотреть
						все проекты</a>
				</div>
			</div>
	</div>
		<script>
			document.addEventListener("DOMContentLoaded", function () {
				const swiper = new Swiper(".swipers", {
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
				document.querySelector(".swipers").classList.add("loaded");
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
			<a href="tel:<?= htmlspecialchars(preg_replace("/\D/", "", seo_config("phone"))) ?>" class="flex-1 inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-900 rounded-full py-3 font-semibold"><i class="fa-solid fa-phone text-emerald-600"></i> Позвонить</a>
			<a href="#contact" class="flex-1 inline-flex items-center justify-center gap-2 bg-emerald-600 text-white rounded-full py-3 font-semibold shadow">Рассчитать <i class="fa-solid fa-arrow-right text-xs"></i></a>
		</div>
		<div class="text-center text-[11px] text-gray-500 mt-1">Отвечаем за 7 минут • Без спама</div>
	</div>
	<script> (function(){ var bar=document.getElementById('sticky-cta'); var showAt=700; function onScroll(){ if(window.scrollY>showAt) bar.classList.add('show'); else bar.classList.remove('show'); } window.addEventListener('scroll', onScroll, {passive:true}); onScroll(); })(); </script>

	<!-- FAQ главной (FAQPage + ответы для AI Overviews) -->
	<?php include_once 'public/components/home_faq.php'; ?>

	<!-- footer content -->
	<?php include_once 'public/components/footer.php'; ?>
	<!-- форма обратной связи -->

	<?php include_once 'public/components/forma.php'; ?>

</body>

</html>