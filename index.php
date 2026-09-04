<?php
// Last-Modified для SEO
if (!headers_sent()) header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime(__FILE__)) . " GMT");
/**
*
* _____ _____
* ( ___ ) ( ___ )
* | |~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~| |
* | | | |
* | | | |
* | | ________ ___ __ _______ _______ ________ | |
* | | |\ __ \|\ \ |\ \|\ ___ \ |\ ___ \ |\ ____\ | |
* | | \ \ \|\ \ \ \ \ \ \ \ \ __/|\ \ __/|\ \ \___|_ | |
* | | \ \ \\\ \ \ \ __\ \ \ \ \_|/_\ \ \_|/_\ \_____ \ | |
* | | \ \ \\\ \ \ \|\__\_\ \ \ \_|\ \ \ \_|\ \|____|\ \ | |
* | | \ \_____ \ \____________\ \_______\ \_______\____\_\ \ | |
* | | \|___| \__\|____________|\|_______|\|_______|\_________\ | |
* | | \|__| \|_________| | |
* | | ________ ________ ________ _______ ________ ________ ________ | |
* | | |\ ____\|\ __ \|\ __ \|\ ___ \ |\ __ \|\ __ \|\ __ \ | |
* | | \ \ \___|\ \ \|\ \ \ \|\ \ \ __/|\ \ \|\ \ \ \|\ \ \ \|\ \ | |
* | | \ \ \ \ \ \\\ \ \ _ _\ \ \_|/_\ \ ____\ \ _ _\ \ \\\ \ | |
* | | \ \ \____\ \ \\\ \ \ \\ \\ \ \_|\ \ \ \___|\ \ \\ \\ \ \\\ \ | |
* | | \ \_______\ \_______\ \__\\ _\\ \_______\ \__\ \ \__\\ _\\ \_______\ | |
* | | \|_______|\|_______|\|__|\|__|\|_______|\|__| \|__|\|__|\|_______| | |
* | | | |
* | | | |
* |___|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|___|
* (_____) (_____)
*
* Эта программа является свободным программным обеспечением: вы можете распространять ее и/или модифицировать
* в соответствии с условиями GNU General Public License, опубликованными
* Фондом свободного программного обеспечения (Free Software Foundation), либо в версии 3 Лицензии, либо (по вашему
выбору) в любой более поздней версии.
*
* @author TimQwees
* @link https://github.com/TimQwees/Qwees_CorePro
*
*/
$componentMailPath = __DIR__ . '/assets/PHP/componet_mail.php';
if (file_exists($componentMailPath)) {
	include_once $componentMailPath;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ФАВОРИТ-СТРОЙ-СНАБ — Быстровозводимые здания под ключ | Москва</title>
	<meta name="description" content="Быстровозводимые здания под ключ в Москве: склады, ангары, АБК, общежития. Капремонт, грузоперевозки, спецтехника. От 2 недель, гарантия 50 лет. ☎ +7 938 018-00-06">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="keywords" content="быстровозводимые здания БВЗ, модульные здания, капитальный ремонт зданий, грузоперевозки Москва, аренда спецтехники, производство мусорных баков, навесы, склады, общежития, Фаворит-Строй-Снаб">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#22c55e">
	<meta name="format-detection" content="telephone=yes">
	<meta name="yandex-verification" content="27e0ba9980498150" />
	<!-- Preconnect for performance -->
	<link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
	<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
	<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
	<link rel="dns-prefetch" href="https://api-maps.yandex.ru">
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">
	<link rel="mask-icon" href="/assets/image/favicon/favicon-32x32.png" color="#22c55e">
	<!-- Canonical - punycode for SEO compatibility, IDN for humans -->
	<link rel="canonical" href="https://www.xn--h1aafj.xn--p1ai/" />
	<link rel="alternate" hreflang="ru" href="https://www.xn--h1aafj.xn--p1ai/" />
	<link rel="alternate" hreflang="x-default" href="https://www.xn--h1aafj.xn--p1ai/" />
	<!-- Open Graph -->
	<meta property="og:locale" content="ru_RU">
	<meta property="og:site_name" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta property="og:title" content="ФАВОРИТ-СТРОЙ-СНАБ — Быстровозводимые здания под ключ в Москве">
	<meta property="og:description" content="Строительство БВЗ от 2 недель, капитальный ремонт, грузоперевозки, поставка мебели и металлоконструкций. Гарантия, технадзор, фотоотчёты.">
	<meta property="og:image" content="https://www.xn--h1aafj.xn--p1ai/assets/image/logo/logo.svg">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="630">
	<meta property="og:image:alt" content="ФАВОРИТ-СТРОЙ-СНАБ логотип">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://www.xn--h1aafj.xn--p1ai/">
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="ФАВОРИТ-СТРОЙ-СНАБ — Быстровозводимые здания под ключ">
	<meta name="twitter:description" content="Быстровозводимые здания, капремонт, грузоперевозки, мебель, склады и ангары. Москва и МО.">
	<meta name="twitter:image" content="https://www.xn--h1aafj.xn--p1ai/assets/image/icon/2.PNG">
	<meta name="twitter:site" content="@fss_rus">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
	<style> body { font-family: Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
	<!-- Preload critical -->
	<link rel="preload" href="/assets/distCSS/style.css" as="style">
	<link rel="preload" href="/assets/distJS/main.js" as="script">
	<!-- Styles -->
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/assets/distCSS/style.css">
	<!-- Swiper -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
	<script defer src="/assets/distJS/main.js"></script>

	<!--verefy-->
	<meta name="yandex-verification" content="27e0ba9980498150" />

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
	</style>
	<?php include_once __DIR__ . '/assets/PHP/sheoma.php'; ?>
</head>

<body class="font-sans text-gray-800">

	<?php include_once __DIR__ . '/assets/componets/header.php'; ?>

	<!-- VIDEO BANNER -->
	<section class="relative w-full min-h-[100dvh] flex items-center justify-start overflow-hidden view">
		<!-- <img src="/assets/image/banner.jpeg" alt="banner" class="absolute inset-0 w-full h-full object-cover z-0"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" /> -->

		<!-- swiper -->
		<div class="absolute inset-0 w-full h-full z-0">
			<div class="swiper banner-swiper w-full h-full">
				<div class="swiper-wrapper">

					<!-- icon png with animation -->
					<!-- <img src="assets/image/icon/1.png" alt="icon"
						class="absolute right-[5%] z-20 top-[20%] max-w-[600px] banner-float-right hidden-1200" /> -->

					<img src="assets/image/icon/2.PNG" alt="icon" class="absolute z-20 banner-float-left"
						style="left: 3.5rem; bottom:1rem; max-width:400px; width:100%; max-width:400px;"
						media="(max-width: 600px)" />
					<style>
						@media (max-width: 600px) {
							.banner-float-left {
								max-width: 250px !important;
							}
						}
					</style>
					<!-- темное затемнение -->
					<div class="bg-black w-full h-full opacity-50 z-10 absolute"></div>

					<div class="swiper-slide">
						<img src="/assets/image/services/капстроение/1.JPG" alt="Капитальное строительство — ФАВОРИТ-СТРОЙ-СНАБ" loading="eager" fetchpriority="high" decoding="async"
							class="w-full h-full object-cover"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>

					<div class="swiper-slide">
						<img src="/assets/image/services/капстроение/5.JPG" alt="Быстровозводимые здания — производство" loading="lazy" decoding="async"
							class="w-full h-full object-cover"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>

					<div class="swiper-slide">
						<img src="/assets/image/services/Грузоперевозки/1.jpg" alt="Грузоперевозки — автотранспорт" loading="lazy" decoding="async"
							class="w-full h-full object-cover"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>

					<div class="swiper-slide">
						<img src="/assets/image/services/Грузоперевозки/2.jpg" alt="Строительная техника — спецтехника" loading="lazy" decoding="async" referrerpolicy="no-referrer" class="w-full h-full object-cover" style="transform: scaleX(-1);"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>

					<div class="swiper-slide">
						<img src="/assets/image/banner.jpeg" alt="Грузовик Шакман — доставка" loading="lazy" decoding="async" referrerpolicy="no-referrer"
							class="w-full h-full object-cover"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>

					<div class="swiper-slide">
						<img src="/assets/image/swiper/1.jpeg" alt="Модульные здания — портфолио" loading="lazy" decoding="async"
							class="w-full h-full object-cover"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>

					<div class="swiper-slide">
						<img src="/assets/image/swiper/2.jpg" alt="Складские комплексы — БВЗ" loading="lazy" decoding="async"
							class="w-full h-full object-cover"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>

					<div class="swiper-slide">
						<img src="/assets/image/swiper/3.jpeg" alt="Монтаж быстровозводимых зданий" loading="lazy" decoding="async"
							class="w-full h-full object-cover"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>

					<div class="swiper-slide">
						<img src="/assets/image/services/Школа фото/2.jpeg" alt="Капитальный ремонт школ — до и после" loading="lazy" decoding="async"
							class="w-full h-full object-cover"  onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'" />
					</div>


				</div>

				<!-- Пагинация и стрелки вынесены из absolute и flex классов для корректного отображения -->
				<div class="swiper-pagination absolute z-20"></div>
				<!-- Прогресс-бар для слайдера -->

				<!-- Центрированная белая линия с зелёным прогресс-баром слева от текста, вертикально -->
				<div class="absolute inset-0 flex items-center justify-center z-30 pointer-events-none">
					<div class="relative flex flex-col items-center w-full h-full">
						<!-- Линия 40% высоты шапки, белая, ширина 8px, по центру -->
						<div class="absolute left-10 top-[22.5%] -translate-y-1/2 flex flex-col items-center h-2/5 media_content_NON_left"
							style="height:10%;">
							<div class="relative h-full flex items-start">
								<!-- Белая линия -->
								<div class="absolute left-0 top-0 w-2 h-full bg-white rounded-full"></div>
								<!-- Зелёный прогресс-бар (идёт вниз) -->
								<div class="swiper-progress-bar-vertical absolute left-0 top-0 w-2 rounded-full" style="background-color: rgb(74 222 128);  height:0%;
									transition:height 5s linear;"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="absolute inset-0 bg-black/10 z-10 pointer-events-none"></div>
		</div>

		<!-- modern hero overlay gradient -->
		<div class="absolute inset-0 z-10 bg-gradient-to-r from-black/70 via-black/45 to-black/10 pointer-events-none"></div>
		<div class="absolute inset-0 z-10 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>

		<div class="relative z-20 w-full min-h-[100dvh] flex items-center">
			<div class="mx-auto w-full max-w-[1280px] px-4 py-10 md:py-14 grid lg:grid-cols-[1.15fr_.85fr] gap-8 items-center">
				<!-- left -->
				<div class="text-white">
					<div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/15 rounded-full px-3 py-1.5 text-xs font-medium">
						<span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
						<span>С 2011 года • 180+ объектов • Допуск СРО</span>
						<span class="hidden sm:inline-flex items-center gap-1 ml-1 bg-emerald-500 text-white rounded-full px-2 py-0.5 text-[11px] font-bold">3 слота на июнь</span>
					</div>
					<h1 class="mt-5 text-[32px] sm:text-[42px] lg:text-[48px] font-black leading-[0.95] tracking-tight">
						Быстровозводимые<br>
						<span class="text-emerald-400">здания под ключ</span><br>
						от 14 дней
					</h1>
					<p class="mt-4 text-[16px] sm:text-[18px] leading-relaxed text-white/85 max-w-[560px]">
						Склады, ангары, АБК, общежития, капремонт и грузоперевозки. Смету рассчитаем за 15 минут, выезд инженера — бесплатно. Фиксируем цену в договоре.
					</p>
					<div class="mt-7 flex flex-col sm:flex-row gap-3">
						<a href="#contact" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full px-7 py-3.5 font-semibold text-[15px] shadow-[0_12px_28px_rgba(16,185,129,.35)] transition">
							Рассчитать стоимость за 15 минут <i class="fa-solid fa-arrow-right text-xs"></i>
						</a>
						<a href="/pages/portfolio.php" class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 hover:bg-white hover:text-gray-900 text-white rounded-full px-6 py-3.5 font-semibold text-[15px] transition">
							<i class="fa-solid fa-images"></i> Показать объекты
						</a>
					</div>
					<div class="mt-3 flex items-center gap-2 text-xs text-white/70">
						<i class="fa-solid fa-lock text-emerald-400"></i> Без предоплаты • Цена фиксируется • Оплата по этапам
					</div>
					<div class="mt-8 grid grid-cols-3 gap-3 max-w-[520px]">
						<div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-3">
							<div class="text-[11px] uppercase tracking-widest text-white/60">Срок</div>
							<div class="text-lg font-extrabold">от 14 дней</div>
							<div class="text-xs text-white/70">вместо 3–6 мес.</div>
						</div>
						<div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-3">
							<div class="text-[11px] uppercase tracking-widest text-white/60">Экономия</div>
							<div class="text-lg font-extrabold">до −30%</div>
							<div class="text-xs text-white/70">к классике</div>
						</div>
						<div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-3">
							<div class="text-[11px] uppercase tracking-widest text-white/60">Гарантия</div>
							<div class="text-lg font-extrabold">50+ лет</div>
							<div class="text-xs text-white/70">службы</div>
						</div>
					</div>
					<div class="mt-6 flex flex-wrap items-center gap-3 text-xs">
						<span class="inline-flex items-center gap-1.5 bg-white text-gray-900 rounded-full px-3 py-1.5 font-medium"><i class="fa-solid fa-check text-emerald-600"></i> Технадзор на каждом этапе</span>
						<span class="inline-flex items-center gap-1.5 bg-white text-gray-900 rounded-full px-3 py-1.5 font-medium"><i class="fa-solid fa-check text-emerald-600"></i> Фотоотчет каждую неделю</span>
						<span class="inline-flex items-center gap-1.5 bg-white text-gray-900 rounded-full px-3 py-1.5 font-medium"><i class="fa-solid fa-check text-emerald-600"></i> Персональный менеджер</span>
					</div>
				</div>
				<!-- right mock card - lead magnet -->
				<div class="hidden lg:block">
					<div class="bg-white rounded-[28px] p-6 shadow-[0_24px_64px_rgba(0,0,0,.28)] border border-black/5">
						<div class="flex items-center justify-between">
							<div class="text-xs font-semibold tracking-widest uppercase text-gray-500">Онлайн-расчет</div>
							<div class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-2.5 py-1 font-medium">Ответим за 7 минут</div>
						</div>
						<div class="mt-4">
							<div class="text-xl font-extrabold tracking-tight text-gray-900 leading-tight">Узнайте стоимость<br>вашего здания сегодня</div>
							<div class="text-sm text-gray-500 mt-1">Оставьте контакты — пришлем смету + 3 типовых проекта в WhatsApp</div>
						</div>
						<form method="POST" class="mt-5 space-y-3" novalidate>
							<input type="hidden" name="mini-forma">
							<?php include_once __DIR__ . '/assets/componets/antibot_fields.php'; ?>
							<input name="name" type="text" placeholder="Ваше имя" required minlength="2" maxlength="100" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50">
							<input name="phone" type="tel" maxlength="18" placeholder="+7 (___) ___-__-__" required pattern="^\+?[0-9\s\-\(\)]{10,20}$" class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-gray-50">
							<button type="submit" class="w-full bg-gray-900 hover:bg-black text-white rounded-full py-3.5 font-semibold transition flex items-center justify-center gap-2">Получить расчет и проекты <i class="fa-solid fa-arrow-right text-xs"></i></button>
							<div class="text-[11px] leading-tight text-gray-500 text-center">Нажимая, вы соглашаетесь с <a href="/pages/politic/index.php" class="underline">политикой</a>. Защита от спама. Реклама не принимается.</div>
						</form>
						<div class="mt-4 grid grid-cols-3 gap-2 text-center">
							<div class="bg-gray-50 rounded-2xl p-2.5 border border-gray-100">
								<div class="text-[11px] text-gray-500">Звонок</div>
								<div class="text-sm font-bold">7 минут</div>
							</div>
							<div class="bg-gray-50 rounded-2xl p-2.5 border border-gray-100">
								<div class="text-[11px] text-gray-500">Смета</div>
								<div class="text-sm font-bold">15 минут</div>
							</div>
							<div class="bg-gray-50 rounded-2xl p-2.5 border border-gray-100">
								<div class="text-[11px] text-gray-500">Выезд</div>
								<div class="text-sm font-bold">Бесплатно</div>
							</div>
						</div>
						<div class="mt-4 flex items-center gap-2 text-xs text-gray-500">
							<img src="/assets/image/logo/logo.svg" alt="logo" class="w-6 h-6" onerror="this.style.display='none'">
							<span>Работаем по договору • Без скрытых платежей</span>
						</div>
					</div>
					<div class="mt-3 flex items-center justify-center gap-2 text-xs text-white/80">
						<i class="fa-solid fa-shield-halved text-emerald-400"></i> Данные защищены • 256-bit
					</div>
				</div>
			</div>
		</div>


		<script>
			document.addEventListener('DOMContentLoaded', function () {
				const bannerSwiper = new Swiper('.banner-swiper', {
					loop: true,
					autoplay: {
						delay: 5000,
						disableOnInteraction: false,
					},
					effect: 'fade',
					fadeEffect: { crossFade: true },
					pagination: {
						el: '.banner-swiper .swiper-pagination',
						clickable: true,
					},
					navigation: {
						nextEl: '.banner-swiper .swiper-button-next',
						prevEl: '.banner-swiper .swiper-button-prev',
					},
					allowTouchMove: true,
				});

				// Вертикальный прогресс-бар
				const progressBar = document.querySelector('.swiper-progress-bar-vertical');
				function startProgressBar() {
					if (!progressBar) return;
					progressBar.style.transition = 'none';
					progressBar.style.height = '0%';
					// force reflow
					void progressBar.offsetHeight;
					progressBar.style.transition = 'height 5s linear';
					progressBar.style.height = '100%';
				}

				function resetProgressBar() {
					if (!progressBar) return;
					progressBar.style.transition = 'none';
					progressBar.style.height = '0%';
				}

				bannerSwiper.on('slideChangeTransitionStart', () => {
					resetProgressBar();
				});

				bannerSwiper.on('slideChangeTransitionEnd', () => {
					startProgressBar();
				});

				bannerSwiper.on('autoplayStart', () => {
					startProgressBar();
				});

				bannerSwiper.on('autoplayStop', () => {
					resetProgressBar();
				});

				// Инициализация прогресс-бара при загрузке
				startProgressBar();
			});

		</script>
		<style>
			/* Swiper z-index fix and controls overlay */
			.banner-swiper {
				position: relative;
				z-index: 1;
			}

			.banner-swiper .swiper-pagination {
				z-index: 30 !important;
			}

			.banner-swiper .swiper-button-prev,
			.banner-swiper .swiper-button-next {
				z-index: 30 !important;
			}

			.banner-swiper .swiper-slide {
				z-index: 1;
			}

			.swiper-slide img,
			.swiper-slide video {
				height: 100%;
			}

			.swiper-pagination-bullet-active {
				background: rgb(74 222 128) !important;
				width: 2rem;
				border-radius: 30px;
			}

			.swiper-pagination-bullet {
				background: white;
				width: 2rem;
				border-radius: 30px;
			}

			/* Для вертикального прогресс-бара */
			.swiper-progress-bar-vertical {
				transition: height 5s linear;
			}

			@media screen and (max-width:1200px) {

				.hidden-1200 {
					display: none;
				}

				.media_content_left {
					margin-left: 2rem;
				}

				.media_content_NON_left {
					left: 15px !important;
					/* transform: rotate(-90deg) translate(-2rem, 14rem); */
				}
			}

			@media screen and (max-width: 720px) {
				.media_content_left {
					margin-left: 1rem;
				}
			}
		</style>
	</section>

	<!-- Линейка брендов -->
	<!-- <section class="py-4 bg-[#deffbc]">
		<div class="overflow-x-hidden relative">
			<div class="marquee-wrapper">
				<div class="marquee-track-ltr">
					<div class="marquee-content">
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-home"
							title="Дом"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-cube"
							title="Кирпичи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-building"
							title="Здание"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-road"
							title="Дороги"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-bolt"
							title="Электрика"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Грузовик"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-tools"
							title="Инструменты"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Пикап"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Пикап"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-home"
							title="Дом"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-cube"
							title="Кирпичи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-building"
							title="Здание"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-road"
							title="Дороги"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-bolt"
							title="Электрика"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Грузовик"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-tools"
							title="Инструменты"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
					</div>
					<div class="marquee-content">
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-home"
							title="Дом"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-cube"
							title="Кирпичи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-building"
							title="Здание"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-road"
							title="Дороги"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-bolt"
							title="Электрика"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Грузовик"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-tools"
							title="Инструменты"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Пикап"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Пикап"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-home"
							title="Дом"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-cube"
							title="Кирпичи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-building"
							title="Здание"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-road"
							title="Дороги"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-bolt"
							title="Электрика"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Грузовик"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-tools"
							title="Инструменты"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-emerald-600 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
					</div>
				</div>
			</div>
		</div>

		<style>
			.marquee-wrapper {
				overflow: hidden;
				position: relative;
			}

			.marquee-track-ltr {
				display: flex;
				animation: scroll-marque-ltr 40s linear infinite;
				will-change: transform;
			}

			.marquee-content {
				display: flex;
				white-space: nowrap;
			}

			.marquee-content i {
				font-size: 2rem;
			}

			.marquee-content i::after {
				content: '•';
				margin-inline: 1rem;
			}

			.marquee-content i:last-child::after;

				{
				content: '';
				margin-inline: 1rem;
			}

			@keyframes scroll-marque-ltr {
				from {
					transform: translateX(0%);
				}

				to {
					transform: translateX(-100%);
				}
			}

			@media (max-width: 640px) {
				.marquee-content i {
					font-size: 1rem;
				}
			}
		</style>
	</section> -->

	<!-- Линейка — зелёная как было, но современнее -->
	<section class="py-3 bg-[#deffbc] border-y border-[#bbf7d0]">
		<div class="overflow-x-hidden relative">
			<div class="marquee-wrapper">
				<div class="marquee-track">
					<!-- Повторим 2 раза, чтобы бесконечно двигалось -->
					<div class="marquee-content">
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Капитальный ремонт">Капитальный ремонт</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Поставка мебели">Поставка мебели</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Быстровозводимые здания">Быстровозводимые здания</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Склады">Склады</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Ангары">Ангары</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Навесы">Навесы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Дизель генераторные">Дизель генераторные</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Компрессорные станции">Компрессорные станции</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Блочно-модульные конструкции">Блочно-модульные конструкции</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Монтаж инженерных систем">Монтаж инженерных систем</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Проектирование объектов">Проектирование объектов</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Техническое обслуживание">Техническое обслуживание</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Ремонт кровли">Ремонт кровли</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Внутренняя отделка">Внутренняя отделка</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Промышленные полы">Промышленные полы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Фасадные работы">Фасадные работы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Пожарная безопасность">Пожарная безопасность</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Ландшафтные работы">Ландшафтные работы</p>
					</div>
					<!-- Дублируем для бесконечной прокрутки -->
					<div class="marquee-content">
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Капитальный ремонт">Капитальный ремонт</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Поставка мебели">Поставка мебели</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Быстровозводимые здания">Быстровозводимые здания</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Склады">Склады</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Ангары">Ангары</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Навесы">Навесы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Дизель генераторные">Дизель генераторные</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Компрессорные станции">Компрессорные станции</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Блочно-модульные конструкции">Блочно-модульные конструкции</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Монтаж инженерных систем">Монтаж инженерных систем</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Проектирование объектов">Проектирование объектов</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Техническое обслуживание">Техническое обслуживание</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Ремонт кровли">Ремонт кровли</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Внутренняя отделка">Внутренняя отделка</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Промышленные полы">Промышленные полы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Фасадные работы">Фасадные работы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Пожарная безопасность">Пожарная безопасность</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-emerald-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Ландшафтные работы">Ландшафтные работы</p>
					</div>
				</div>
			</div>
		</div>

		<style>
			.marquee-wrapper {
				overflow: hidden;
				position: relative;
			}

			.marquee-track {
				display: flex;
				animation: scroll-marquee 40s linear infinite;
				will-change: transform;
			}

			.marquee-content {
				display: flex;
				white-space: nowrap;
			}

			.marquee-content p {
				font-size: 1.5rem;
			}

			.marquee-content p::after {
				content: '•';
				margin-inline: 1rem;
			}

			.marquee-content p:last-child::after;

				{
				content: '';
				margin-inline: 1rem;
			}

			.marquee-content p:hover {
				opacity: 1;
			}

			@keyframes scroll-marquee {
				0% {
					transform: translateX(-240%);
				}

				100% {
					transform: translateX(0%);
				}
			}

			@media (max-width: 640px) {
				.marquee-content p {
					font-size: 1rem;
				}
			}
		</style>
	</section>
	<!-- Process Section -->
	<section id="process" class="py-16 md:py-20 bg-[#f0fdf4]/60 view">
		<div class="container mx-auto px-4">
					<div class="text-center mb-12 max-w-3xl mx-auto">
				<div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1 text-xs font-semibold tracking-widest uppercase text-emerald-700">Процесс • Прозрачно</div>
				<h2 class="mt-4 text-3xl md:text-4xl font-black tracking-tight text-gray-900">Этапы строительства БВЗ</h2>
				<p class="mt-3 text-[15px] leading-relaxed text-gray-600">От идеи до сдачи — 7 понятных шагов. Каждый этап — под технадзором, с фотоотчетом и фиксированной ценой.</p>
			</div>

			<div class="relative">
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

					<!-- Step 1 -->
					<div class="timeline-item relative bg-white rounded-[24px] border border-gray-100 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition text-left openWindow viewLeft">
						<div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-5">
							<i class="fa-solid fa-pencil-ruler text-emerald-600 text-xl"></i>
						</div>
						<h3 class="text-[17px] font-bold leading-tight text-gray-900 mb-2 openWindow_title">Проектирование</h3>
						<p class="text-sm leading-relaxed text-gray-600">Первый и самый важный этап — создание проекта.
						</p>

						<input class="openWindow_description hidden" value="Первый и самый важный этап — создание проекта. На
						этом
						этапе учитываются:
						<br>· Назначение здания (склад, производство, торговый центр и т.
						д.).<br>
						· Климатические условия (ветровые и снеговые нагрузки). <br>· Требования к теплоизоляции и
						пожарной безопасности. <br>
						· Архитектурные особенности. <br>
						Проектирование включает
						разработку
						чертежей, расчёты несущих конструкций и подбор материалов." />

						<span class="openWindow_button inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800 mt-4">Подробнее <i class="fa-solid fa-arrow-right text-xs"></i></span>

						<span class="absolute -top-3 left-6 bg-gray-900 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold border-4 border-white">1</span>
					</div>

					<!-- Step 2 -->
					<div class="timeline-item relative bg-white rounded-[24px] border border-gray-100 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition text-left openWindow view">
						<div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-5">
							<i class="fa-solid fa-drafting-compass text-emerald-600 text-xl"></i>
						</div>
						<h3 class="text-[17px] font-bold leading-tight text-gray-900 mb-2 openWindow_title">Подготовка участка и
							фундамент
						</h3>
						<p class="text-sm leading-relaxed text-gray-600">Перед началом строительства проводят:</p>

						<input class="openWindow_description hidden" value="
						Перед началом строительства проводят:<br>
						· Геодезические изыскания (анализ грунта, уровня
						грунтовых вод).
						<br>
						· Расчистку и выравнивание территории.
						<br>
						· Устройство фундамента (ленточный, свайный или плитный). 
						
						<br>
						Для БВЗ часто используют
						облегчённые фундаменты, что сокращает сроки и стоимость работ." />

						<span class="openWindow_button inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800 mt-4">Подробнее <i class="fa-solid fa-arrow-right text-xs"></i></span>

						<span class="absolute -top-3 left-6 bg-gray-900 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold border-4 border-white">2</span>
					</div>

					<!-- Step 3 -->
					<div class="timeline-item relative bg-white rounded-[24px] border border-gray-100 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition text-left openWindow viewRight">
						<div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-5">
							<i class="fa-solid fa-industry text-emerald-600 text-xl"></i>
						</div>
						<h3 class="text-[17px] font-bold leading-tight text-gray-900 mb-2 openWindow_title">Изготовление
							металлоконструкций</h3>
						<p class="text-sm leading-relaxed text-gray-600">Основные элементы (колонны, фермы)</p>

						<input class="openWindow_description hidden"
							value="
												Основные элементы (колонны, фермы, стеновые панели) производятся на заводе по готовым чертежам. Это гарантирует точность размеров и качество материалов." />

						<span class="openWindow_button inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800 mt-4">Подробнее <i class="fa-solid fa-arrow-right text-xs"></i></span>

						<span class="absolute -top-3 left-6 bg-gray-900 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold border-4 border-white">3</span>
					</div>

					<!-- Step 4 -->
					<div class="timeline-item relative bg-white rounded-[24px] border border-gray-100 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition text-left openWindow viewLeft">
						<div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-5">
							<i class="fa-solid fa-screwdriver-wrench text-emerald-600 text-xl"></i>
						</div>
						<h3 class="text-[17px] font-bold leading-tight text-gray-900 mb-2 openWindow_title">Монтаж каркаса</h3>
						<p class="text-sm leading-relaxed text-gray-600">После доставки конструкций на объект начинается
							сборка:</p>

						<input class="openWindow_description hidden" value="
												После доставки конструкций на объект начинается сборка:
<br>
· Установка вертикальных колонн.
<br>
· Монтаж кровельных ферм.
<br>
· Крепление связей и прогонов.
<br>
Каркас собирается с помощью болтовых соединений или сварки." />

						<span class="openWindow_button inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800 mt-4">Подробнее <i class="fa-solid fa-arrow-right text-xs"></i></span>
						<span class="absolute -top-3 left-6 bg-gray-900 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold border-4 border-white">4</span>
					</div>

					<!-- Step 5 -->
					<div class="timeline-item relative bg-white rounded-[24px] border border-gray-100 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition text-left openWindow view">
						<div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-5">
							<i class="fa-solid fa-layer-group text-emerald-600 text-xl"></i>
						</div>
						<h3 class="text-[17px] font-bold leading-tight text-gray-900 mb-2 openWindow_title">Утепление и обшивка
						</h3>
						<p class="text-sm leading-relaxed text-gray-600">Стены и кровля обшиваются сэндвич-панелями или
							профлистом с
							утеплителем. Это обеспечивает:</p>

						<input class="openWindow_description hidden" value="
												Стены и кровля обшиваются сэндвич-панелями или профлистом с утеплителем. Это обеспечивает:
<br>
· Тепло- и звукоизоляцию.
<br>
· Защиту от влаги и коррозии.
<br>
· Эстетичный внешний вид." />

						<span class="openWindow_button inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800 mt-4">Подробнее <i class="fa-solid fa-arrow-right text-xs"></i></span>
						<span class="absolute -top-3 left-6 bg-gray-900 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold border-4 border-white">5</span>
					</div>

					<!-- Step 6 -->
					<div class="timeline-item relative bg-white rounded-[24px] border border-gray-100 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition text-left openWindow viewRight">
						<div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-5">
							<i class="fa-solid fa-plug text-emerald-600 text-xl"></i>
						</div>
						<h3 class="text-[17px] font-bold leading-tight text-gray-900 mb-2 openWindow_title">Инженерные коммуникации
						</h3>
						<p class="text-sm leading-relaxed text-gray-600">Проводится монтаж систем отопления,
							водоснабжения, вентиляции
							и
							электрики.</p>

						<input class="openWindow_description hidden" value="
												Проводятся:
<br>
· Электромонтажные работы.
<br>
· Установка вентиляции и отопления.
<br>
· Прокладка водоснабжения и канализации (при необходимости)." />

						<span class="openWindow_button inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800 mt-4">Подробнее <i class="fa-solid fa-arrow-right text-xs"></i></span>
						<span class="absolute -top-3 left-6 bg-gray-900 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold border-4 border-white">6</span>
					</div>

					<!-- Step 7 -->
					<div class="timeline-item relative bg-white rounded-[24px] border border-gray-100 p-7 shadow-sm hover:shadow-xl hover:-translate-y-1 transition text-left openWindow view">
						<div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-5">
							<i class="fa-solid fa-paint-roller text-emerald-600 text-xl"></i>
						</div>
						<h3 class="text-[17px] font-bold leading-tight text-gray-900 mb-2 openWindow_title">Внутренняя отделка и
							сдача объекта
						</h3>
						<p class="text-sm leading-relaxed text-gray-600">Выполняется отделка, уборка, проверка систем и сдача
							заказчику.
						</p>

						<input class="openWindow_description hidden" value=" Завершающий этап включает: <br>
						· Устройство полов (бетонная стяжка, наливные полы).
						<br>
						· Монтаж перегородок (если требуется).
						<br>
						· Финишную отделку помещений.
						<br>
						После проверки качества и соответствия проекту здание вводится в эксплуатацию.
						<br>
						Преимущества быстровозводимых зданий
						<br>
						✅ Скорость строительства – от 2 недель до 3 месяцев.
						<br>
						✅ Экономия бюджета – на 20-30% дешевле традиционных методов.
						<br>
						✅ Долговечность – срок службы 50+ лет.
						<br>
						✅ Гибкость – возможность расширения и модернизации." />

						<span class="openWindow_button inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-700 hover:text-emerald-800 mt-4">Подробнее <i class="fa-solid fa-arrow-right text-xs"></i></span>
						<span class="absolute -top-3 left-6 bg-gray-900 text-white rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold border-4 border-white">7</span>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Contact — High converting -->
	<section id="contact" class="py-16 md:py-20 bg-[#f0fdf4]/40 relative overflow-hidden">
		<div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(600px 400px at 20% 10%, rgba(16,185,129,.08), transparent 60%), radial-gradient(800px 600px at 90% 90%, rgba(15,23,42,.04), transparent 60%);"></div>
		<div class="container mx-auto px-4 relative">
			<div class="max-w-[1120px] mx-auto">
				<div class="grid lg:grid-cols-[1.05fr_.95fr] gap-8 items-start">
					<!-- left: benefits -->
					<div class="lg:sticky lg:top-24">
						<div class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-3 py-1 text-xs font-semibold tracking-widest uppercase text-gray-600">
							<span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span> Свяжитесь с нами
						</div>
						<h2 class="mt-4 text-3xl md:text-[40px] font-black tracking-tight leading-[0.95] text-gray-900">Рассчитаем стоимость<br><span class="text-emerald-600">за 15 минут</span></h2>
						<p class="mt-3 text-[16px] leading-relaxed text-gray-600 max-w-[520px]">Оставьте контакты — инженер перезвонит, уточнит задачу и пришлет смету с фиксированной ценой. Выезд на объект — бесплатно.</p>
						<div class="mt-6 grid sm:grid-cols-2 gap-3">
							<div class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-3">
								<div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600 shrink-0"><i class="fa-solid fa-stopwatch"></i></div>
								<div><div class="text-sm font-bold text-gray-900">Ответ за 7 минут</div><div class="text-xs text-gray-500">в рабочее время</div></div>
							</div>
							<div class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-3">
								<div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600 shrink-0"><i class="fa-solid fa-file-invoice"></i></div>
								<div><div class="text-sm font-bold text-gray-900">Смета за 15 минут</div><div class="text-xs text-gray-500">с фиксацией цены</div></div>
							</div>
							<div class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-3">
								<div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600 shrink-0"><i class="fa-solid fa-truck-fast"></i></div>
								<div><div class="text-sm font-bold text-gray-900">Выезд бесплатно</div><div class="text-xs text-gray-500">по Москве и МО</div></div>
							</div>
							<div class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-3">
								<div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600 shrink-0"><i class="fa-solid fa-shield-halved"></i></div>
								<div><div class="text-sm font-bold text-gray-900">Без предоплаты</div><div class="text-xs text-gray-500">оплата по этапам</div></div>
							</div>
						</div>
						<div class="mt-6 bg-white rounded-2xl border border-gray-100 p-4">
							<div class="text-xs font-semibold tracking-widest uppercase text-gray-500">Наши гарантии</div>
							<ul class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
								<li class="flex gap-2"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> Технадзор на каждом этапе</li>
								<li class="flex gap-2"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> Фотоотчет каждую неделю</li>
								<li class="flex gap-2"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> Срок службы 50+ лет</li>
								<li class="flex gap-2"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> Персональный менеджер</li>
							</ul>
						</div>
						<div class="mt-4 flex items-center gap-3 text-sm">
							<a href="tel:+79380180006" class="inline-flex items-center gap-2 bg-gray-900 text-white rounded-full px-5 py-2.5 font-semibold"><i class="fa-solid fa-phone"></i> +7 938 018-00-06</a>
							<span class="text-gray-500">или напишите на <a href="mailto:fss@фсс.рус" class="underline decoration-emerald-300">fss@фсс.рус</a></span>
						</div>
					</div>
					<!-- right: form card -->
					<div class="bg-white rounded-[28px] border border-gray-100 shadow-[0_16px_48px_rgba(15,23,42,.08)] p-6 md:p-7">
						<div class="flex items-center justify-between gap-3">
							<h3 class="text-xl font-extrabold tracking-tight text-gray-900">Бесплатный расчет</h3>
							<span class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-2.5 py-1 font-medium">Без спама</span>
						</div>
						<p class="text-sm text-gray-500 mt-1">Заполните — пришлем смету + 3 проекта в WhatsApp</p>
						<form method="POST" novalidate class="mt-6 space-y-3">
							<input type="hidden" name="mailer">
							<?php include_once __DIR__ . '/assets/componets/antibot_fields.php'; ?>
							<div>
								<label for="contact_name" class="text-xs font-semibold text-gray-700">Ваше имя *</label>
								<input id="contact_name" name="name" type="text" placeholder="Иван Петров" required autocomplete="name" minlength="2" maxlength="100" pattern="^[A-Za-zА-Яа-яЁё\s\-\.\']+$" class="mt-1 w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 placeholder:text-gray-500">
							</div>
							<div>
								<label for="contact_phone" class="text-xs font-semibold text-gray-700">Телефон *</label>
								<input id="contact_phone" name="phone" type="tel" maxlength="18" placeholder="+7 (___) ___-__-__" required autocomplete="tel" inputmode="tel" pattern="^\+?[0-9\s\-\(\)]{10,20}$" class="mt-1 w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 placeholder:text-gray-500">
							</div>
							<div>
								<label for="contact_email" class="text-xs font-semibold text-gray-700">Email *</label>
								<input id="contact_email" name="email" type="email" placeholder="name@company.ru" required autocomplete="email" maxlength="254" class="mt-1 w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 placeholder:text-gray-500">
							</div>
							<div>
								<label for="contact_message" class="text-xs font-semibold text-gray-700">Задача *</label>
								<textarea id="contact_message" name="message" placeholder="Опишите объект: назначение, размеры, адрес, сроки" required minlength="10" maxlength="2000" rows="4" class="mt-1 w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-gray-900 placeholder:text-gray-500"></textarea>
							</div>
							<label class="flex gap-2.5 items-start text-xs leading-tight text-gray-600 cursor-pointer">
								<input type="checkbox" name="checkbox" required class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
								<span>Я согласен(а) с <a href="/pages/politic/index.php" class="underline decoration-gray-300 hover:decoration-emerald-400">Политикой конфиденциальности</a> и <a href="/pages/agree/index.php" class="underline decoration-gray-300 hover:decoration-emerald-400">Правилами</a></span>
							</label>
							<button type="submit" class="w-full bg-gray-900 hover:bg-black text-white rounded-full py-3.5 font-semibold transition flex items-center justify-center gap-2 shadow-[0_10px_24px_rgba(15,23,42,.18)]">Получить расчет за 15 минут <i class="fa-solid fa-arrow-right text-xs"></i></button>
							<div class="text-center">
								<a href="tel:+79380180006" class="text-sm font-medium text-gray-700 hover:text-gray-900">Или позвоните: +7 938 018-00-06</a>
							</div>
							<p class="text-[11px] leading-tight text-gray-500 text-center">Защита от спама. Реклама не принимается. Данные защищены 256-bit.</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- FAQ Section -->
	<!--  include_once __DIR__ . ' /assets/componets/faq.php'; ?>	-->
	<style>
		.swipers.loaded {
			opacity: 1;
			transform: scale(1);
		}

		.swiper-pointer-events {
			touch-action: pan-y;
		}

		.swiper-slide::after {
			content: '';
			width: 100%;
			height: 100%;
			position: absolute;
			background-color: black;
			opacity: 0.25;
		}

		.slide-bg {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			z-index: 0;
			object-fit: cover;
		}

		.slide-content {
			position: relative;
			z-index: 2;
			color: #fff;
			padding: 2rem 1rem;
			width: 100%;
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			flex-direction: column;
			height: 100%;
		}

		.swipers .swiper-pagination-bullet {
			background: black;
			width: 12px;
			height: 12px;
			transition: .5s ease all;
		}

		.swipers .swiper-pagination-bullet-active {
			width: 25px;
			border-radius: 25px;
			transition: .5s ease all;
		}
	</style>
	<!-- About Section -->
	<section id="portfolio" class="py-16 md:py-20 bg-white view">
		<div class="container mx-auto px-4">
			<div class="text-center mb-8 max-w-3xl mx-auto">
				<div class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-full px-3 py-1 text-xs font-semibold tracking-widest uppercase text-gray-600">Портфолио • 50+ объектов</div>
				<h2 class="mt-3 text-3xl md:text-4xl font-black tracking-tight text-gray-900">Реализованные объекты</h2>
				<p class="mt-2 text-[15px] leading-relaxed text-gray-600">Склады, школы, АБК, общежития, навесы — с фото, видео и сроками. Нажмите, чтобы увидеть детали.</p>
			</div>
			<div class="swipers overflow-hidden">
				<div class="swiper-wrapper" style="height: 500px">


					<!-- slide 1 -->
					<div class="viewLeft swiper-slide overflow-hidden rounded-[25px] relative">
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/services/Мусор БАК/WhatsApp Video 2025-07-07 at 17.38.28-2.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/services/Мусор БАК/WhatsApp Video 2025-07-07 at 17.38.28-3.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/services/Мусор БАК/WhatsApp Video 2025-07-07 at 17.38.28.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/services/Школа фото/WhatsApp Video 2025-07-06 at 13.38.37.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/services/Школа фото/WhatsApp Video 2025-07-06 at 13.40.00-2.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/services/Школа фото/WhatsApp Video 2025-07-06 at 13.40.00.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/banner/2025-08-01 14.51.56.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/banner/dostavka.MP4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/banner/dostavkaKD.MP4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/banner/buldozer.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/banner/tracktor.mp4"></video>
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
						<video class="slide-bg" autoplay muted loop playsinline
							src="/assets/image/banner/schakman.mp4"></video>
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
					<a href="pages/portfolio.php"
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
	</section>
	<!-- uslugi — modern bento -->
	<section id="advantages" class="py-16 md:py-20 bg-white view">
		<div class="mx-auto max-w-[1280px] px-4">
			<div class="max-w-3xl mx-auto text-center">
				<div class="inline-flex items-center gap-2 bg-slate-900 text-white rounded-full px-3 py-1 text-xs font-semibold tracking-widest uppercase">Направления • 17 услуг</div>
				<h2 class="mt-4 text-3xl md:text-4xl font-black tracking-tight text-gray-900">Мы выполняем</h2>
				<p class="mt-3 text-[15px] leading-relaxed text-gray-600">Полный цикл — от проектирования и производства на заводе до монтажа, отделки и сервиса. Выберите направление — покажем смету и сроки за 15 минут.</p>
			</div>
			<div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
				<a href="/pages/services/kapital/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-hammer"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Капитальный ремонт</div>
					<div class="text-xs text-gray-500 mt-1">Школы, АБК, фасады, кровля</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/obchegitia/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-bed"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Общежития</div>
					<div class="text-xs text-gray-500 mt-1">Жилые комплексы, вахтовые</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/proizvodim_modul/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-cube"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Производство модульных зданий</div>
					<div class="text-xs text-gray-500 mt-1">Завод БВЗ, блок-контейнеры</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/modultebel/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-couch"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Поставка и сборка мебели</div>
					<div class="text-xs text-gray-500 mt-1">Для офисов, школ, общежитий</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<div class="group bg-slate-50 rounded-[20px] border border-dashed border-gray-200 p-5 text-left opacity-80">
					<div class="w-11 h-11 rounded-xl bg-white border border-gray-200 grid place-items-center text-gray-500"><i class="fa-solid fa-compass-drafting"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Проектирование</div>
					<div class="text-xs text-gray-500 mt-1">Скоро — оставите заявку</div>
					<div class="mt-3 text-xs font-semibold text-gray-400">Чертежи • Расчеты • Согл.</div>
				</div>
				<div class="group bg-slate-50 rounded-[20px] border border-dashed border-gray-200 p-5 text-left opacity-80">
					<div class="w-11 h-11 rounded-xl bg-white border border-gray-200 grid place-items-center text-gray-500"><i class="fa-solid fa-plug"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Монтаж инженерных сетей</div>
					<div class="text-xs text-gray-500 mt-1">Скоро</div>
					<div class="mt-3 text-xs font-semibold text-gray-400">Отопление • Электрика</div>
				</div>
				<a href="/pages/services/administrativnoe/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-building"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">АБК и офисы</div>
					<div class="text-xs text-gray-500 mt-1">Административные здания</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/generatornae/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-bolt"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Генераторные</div>
					<div class="text-xs text-gray-500 mt-1">ДГУ, энергокомплексы</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/naves/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-umbrella"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Навесы</div>
					<div class="text-xs text-gray-500 mt-1">Металлоконструкции</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/compressor/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-gauge-high"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Газовые компрессоры</div>
					<div class="text-xs text-gray-500 mt-1">Поставка и монтаж</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/compressor_air/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-wind"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Воздушные компрессоры</div>
					<div class="text-xs text-gray-500 mt-1">Винтовые, поршневые</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/sclad/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-warehouse"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Склады</div>
					<div class="text-xs text-gray-500 mt-1">Логистические центры</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/musorbak/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-trash-can"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Мусорные баки</div>
					<div class="text-xs text-gray-500 mt-1">Контейнеры, оцинковка</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/gruzoperevozki/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-truck"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Грузоперевозки</div>
					<div class="text-xs text-gray-500 mt-1">Авто и ЖД</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/spectehnika/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-tractor"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Спецтехника</div>
					<div class="text-xs text-gray-500 mt-1">Аренда, услуги</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
				<a href="/pages/services/kapstroenie/index.php" class="group bg-white rounded-[20px] border border-gray-100 p-5 hover:shadow-[0_12px_32px_rgba(15,23,42,.08)] hover:border-gray-200 hover:-translate-y-1 transition text-left">
					<div class="w-11 h-11 rounded-xl bg-emerald-50 border border-emerald-100 grid place-items-center text-emerald-600"><i class="fa-solid fa-city"></i></div>
					<div class="mt-3 text-[15px] font-bold leading-tight text-gray-900">Капитальное строение</div>
					<div class="text-xs text-gray-500 mt-1">Здания и сооружения</div>
					<div class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">Подробнее <i class="fa-solid fa-arrow-right text-[10px]"></i></div>
				</a>
			</div>
			<div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
				<a href="#contact" class="inline-flex items-center justify-center gap-2 bg-gray-900 text-white rounded-full px-7 py-3 font-semibold hover:bg-black transition">Получить смету за 15 минут <i class="fa-solid fa-arrow-right text-xs"></i></a>
				<a href="/pages/uslugi/uslugi.php" class="inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-900 rounded-full px-7 py-3 font-semibold hover:bg-gray-50 transition">Все услуги</a>
			</div>
		</div>
	</section>


	<!-- Модальное окно для описания товара каталога -->
	<dialog id="catalog-modal" style="padding:0; border:none; background:transparent;">
		<div id="catalog-modal-backdrop"
			style="position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; display:flex; align-items:center; justify-content:center;">
			<div
				style="background:#fff; border-radius:16px; max-width:600px; width:100%; max-height:80vh; overflow-y:auto; overflow-x:hidden; box-shadow:0 8px 32px rgba(0,0,0,0.25); position:relative;">
				<button id="catalog-modal-close"
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
			<a href="tel:+79380180006" class="flex-1 inline-flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-900 rounded-full py-3 font-semibold"><i class="fa-solid fa-phone text-emerald-600"></i> Позвонить</a>
			<a href="#contact" class="flex-1 inline-flex items-center justify-center gap-2 bg-emerald-600 text-white rounded-full py-3 font-semibold shadow">Рассчитать <i class="fa-solid fa-arrow-right text-xs"></i></a>
		</div>
		<div class="text-center text-[11px] text-gray-500 mt-1">Отвечаем за 7 минут • Без спама</div>
	</div>
	<script> (function(){ var bar=document.getElementById('sticky-cta'); var showAt=700; function onScroll(){ if(window.scrollY>showAt) bar.classList.add('show'); else bar.classList.remove('show'); } window.addEventListener('scroll', onScroll, {passive:true}); onScroll(); })(); </script>

	<!-- footer content -->
	<?php include_once __DIR__ . '/assets/componets/footer.php'; ?>
	<!-- форма обратной связи -->
	<?php include_once __DIR__ . '/assets/componets/forma.php'; ?>
	<!-- Подключение Яндекс.Карт и инициализация карты -->
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
	<!-- /Яндекс.Карта -->

	<!-- <div class="viewLeft contacts__map">
		<div class="viewLeft map rounded-lg shadow-lg" id="map_kavstal"></div>
	</div> -->

</body>

</html>