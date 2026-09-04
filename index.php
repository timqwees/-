<?/**
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
	<!-- scripts modal connect+fh -->
	<script src="/local/templates/rs2023/js/fh.js"></script>
	<script src="/local/templates/rs2023/js/modal.js"></script>
	<!-- end -->

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>FAVORIT-STROY-SNAB - Современное домостроение</title>
	<meta name="description"
		content="FAVORIT-STROY-SNAB — Современное домостроение. Качественные строительные услуги и материалы.">
	<meta name="robots" content="index, follow">
	<meta name="keywords" content="строительство, домостроение, FAVORIT-STROY-SNAB, строительные услуги, материалы">
	<meta name="author" content="TimQwees">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">

	<!-- Open Graph / Facebook -->
	<meta property="og:title" content="FAVORIT-STROY-SNAB - Современное домостроение">
	<meta property="og:description" content="Качественные строительные услуги и материалы.">
	<meta property="og:image" content="/assets/image/logo.svg">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://www.фсс.рус/">
	<!-- Twitter -->

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="FAVORIT-STROY-SNAB - Современное домостроение">
	<meta name="twitter:description" content="Качественные строительные услуги и материалы.">
	<meta name="twitter:image" content="/assets/image/icon/2.PNG">
	<meta name="twitter:card" content="summary_large_image">
	<meta property="og:description" content="Качественные строительные услуги и материалы.">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<meta name="author" content="TimQwees">

	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="/assets/distCSS/style.css">
	<script src="/assets/distJS/main.js"></script>
	<link rel="stylesheet" href="tailwind.css">

	<!-- favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="assets/image/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">

	<!-- canonical -->
	<link rel="canonical" href="https://www.kavstal.ru/index.php" />

	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer="true" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	<!-- meta yandex -->
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
</head>

<body class="font-sans text-gray-800">

	<? include_once __DIR__ . '/assets/componets/header.php'; ?>

	<!-- VIDEO BANNER -->
	<section class="relative w-full min-h-[100dvh] flex items-center justify-start overflow-hidden view">
		<!-- <img src="/assets/image/banner.jpeg" alt="banner" class="absolute inset-0 w-full h-full object-cover z-0" /> -->

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
						<img src="https://www.фсс.рус/assets/image/services/капстроение/1.JPG" alt="Banner 1"
							class="w-full h-full object-cover" />
					</div>

					<div class="swiper-slide">
						<img src="https://www.фсс.рус/assets/image/services/капстроение/5.JPG" alt="Banner 2"
							class="w-full h-full object-cover" />
					</div>

					<div class="swiper-slide">
						<img src="https://www.фсс.рус/assets/image/services/Грузоперевозки/1.jpg" alt="Banner 3"
							class="w-full h-full object-cover" />
					</div>

					<div class="swiper-slide">
						<img src="https://img.freepik.com/free-photo/view-heavy-machinery-used-construction-industry_23-2151307694.jpg?uid=R32996255&ga=GA1.1.364390413.1754642881&semt=ais_hybrid&w=740&q=80"
							alt="Banner 3" class="w-full h-full object-cover" style="transform: scaleX(-1);" />
					</div>

					<div class="swiper-slide">
						<img src="https://truckscenter.ru/shacman/img/8x4.jpg" alt="Banner 4"
							class="w-full h-full object-cover" />
					</div>

					<div class="swiper-slide">
						<img src="https://www.фсс.рус//assets/image/swiper/1.jpeg" alt=" Banner 5"
							class="w-full h-full object-cover" />
					</div>

					<div class="swiper-slide">
						<img src="https://www.фсс.рус//assets/image/swiper/2.jpg" alt="Banner 6"
							class="w-full h-full object-cover" />
					</div>

					<div class="swiper-slide">
						<img src="https://www.фсс.рус//assets/image/swiper/3.jpeg" alt="Banner 7"
							class="w-full h-full object-cover" />
					</div>

					<div class="swiper-slide">
						<img src="https://www.фсс.рус/assets/image/services/Школа фото/2.jpeg" alt="Banner 8"
							class="w-full h-full object-cover" />
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

		<span class="absolute bottom-10 left-1/2 -translate-x-1/2 text-white">FAVORIT-STROY-SNAB</span>


		<div class="relative z-30 container mx-auto px-6 flex flex-col items-start min-h-[600px]">
			<div class="max-w-2xl viewLeft media_content_left">
				<div class="text-[1.5rem] md:text-4xl font-extrabold text-white mb-6 mt-10 md:mt-0 leading-tight">
					<span class="viewLeft">
						<span class="text-wgite">Капитальное строение</span><br>
						<span class="text-green-400">Грузоперевозки</span><br>
						<span class="text-wgite">Быстровозводимые здания под ключ</span><br>
						<span class="text-green-400">Поставка и сборка мебели</span><br>
						<span class="text-wgite">Капитальный ремонт</span>
				</div>
				<div class="text-white text-base md:text-lg mb-8"><span class="viewLeft">
						Индивидуальный
						подход к каждому
						клиенту.</span><br>

					<span class="view">Гарантия
						на все виды работ и материалов.</span><br>

					<span class="viewRight">Сопровождение
						на
						всех этапах
						строительства.</span>

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
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-home"
							title="Дом"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-cube"
							title="Кирпичи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-building"
							title="Здание"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-road"
							title="Дороги"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-bolt"
							title="Электрика"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Грузовик"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-tools"
							title="Инструменты"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Пикап"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Пикап"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-home"
							title="Дом"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-cube"
							title="Кирпичи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-building"
							title="Здание"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-road"
							title="Дороги"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-bolt"
							title="Электрика"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Грузовик"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-tools"
							title="Инструменты"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
					</div>
					<div class="marquee-content">
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-home"
							title="Дом"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-cube"
							title="Кирпичи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-building"
							title="Здание"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-road"
							title="Дороги"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-bolt"
							title="Электрика"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Грузовик"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-tools"
							title="Инструменты"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Пикап"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
							title="Чертежи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Пикап"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-home"
							title="Дом"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-cube"
							title="Кирпичи"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-building"
							title="Здание"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-road"
							title="Дороги"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-bolt"
							title="Электрика"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-truck"
							title="Грузовик"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-tools"
							title="Инструменты"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-hard-hat"
							title="Строитель"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-shield"
							title="Безопасность"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-warehouse"
							title="Склад"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 fa fa-stairs"
							title="Лестница"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 fa fa-paint-brush"
							title="Отделка"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 fa fa-door-closed"
							title="Дверь"></i>
						<i class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 fa fa-drafting-compass"
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

	<!-- Линейка брендов -->
	<section class="py-4 bg-[#deffbc]">
		<div class="overflow-x-hidden relative">
			<div class="marquee-wrapper">
				<div class="marquee-track">
					<!-- Повторим 2 раза, чтобы бесконечно двигалось -->
					<div class="marquee-content">
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Капитальный ремонт">Капитальный ремонт</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 viewLeft"
							title="Поставка мебели">Поставка мебели</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Быстровозводимые здания">Быстровозводимые здания</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Склады">Склады</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Ангары">Ангары</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Навесы">Навесы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 viewLeft"
							title="Дизель генераторные">Дизель генераторные</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Компрессорные станции">Компрессорные станции</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Блочно-модульные конструкции">Блочно-модульные конструкции</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Монтаж инженерных систем">Монтаж инженерных систем</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 viewLeft"
							title="Проектирование объектов">Проектирование объектов</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Техническое обслуживание">Техническое обслуживание</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Ремонт кровли">Ремонт кровли</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Внутренняя отделка">Внутренняя отделка</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 viewLeft"
							title="Промышленные полы">Промышленные полы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Фасадные работы">Фасадные работы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Пожарная безопасность">Пожарная безопасность</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Ландшафтные работы">Ландшафтные работы</p>
					</div>
					<!-- Дублируем для бесконечной прокрутки -->
					<div class="marquee-content">
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Капитальный ремонт">Капитальный ремонт</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 viewLeft"
							title="Поставка мебели">Поставка мебели</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Быстровозводимые здания">Быстровозводимые здания</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Склады">Склады</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Ангары">Ангары</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Навесы">Навесы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 viewLeft"
							title="Дизель генераторные">Дизель генераторные</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Компрессорные станции">Компрессорные станции</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Блочно-модульные конструкции">Блочно-модульные конструкции</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Монтаж инженерных систем">Монтаж инженерных систем</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 viewLeft"
							title="Проектирование объектов">Проектирование объектов</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Техническое обслуживание">Техническое обслуживание</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Ремонт кровли">Ремонт кровли</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
							title="Внутренняя отделка">Внутренняя отделка</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-700 rounded-lg shadow-sm p-1 viewLeft"
							title="Промышленные полы">Промышленные полы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-800 rounded-lg shadow-sm p-1 viewLeft"
							title="Фасадные работы">Фасадные работы</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-600 rounded-lg shadow-sm p-1 viewLeft"
							title="Пожарная безопасность">Пожарная безопасность</p>
						<p class="inline-block opacity-90 hover:opacity-100 transition text-green-500 rounded-lg shadow-sm p-1 viewLeft"
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


	</section>
	<!-- Process Section -->
	<section id="process" class="py-20 bg-white view">
		<div class="container mx-auto px-4">
			<div class="text-center mb-16 ">
				<h2 class="text-3xl font-bold mb-4 viewLeft">Этапы строительства БВЗ </h2>
				<p class="text-xl text-gray-600 max-w-2xl mx-auto flex items-center justify-center gap-3 ">
					<span>
						<i class="fa-solid fa-building-shield text-green-500 text-2xl"></i>
					</span>
					<span class="viewRight">Мы возводим надежные здания!</span>
				</p>
			</div>

			<div class="relative">
				<div class="grid grid-cols-1 md:grid-cols-3 gap-8">

					<!-- Step 1 -->
					<div class="timeline-item relative bg-gray-50 p-8 rounded-lg text-center openWindow viewLeft">
						<div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 ">
							<i class="fa-solid fa-pencil-ruler text-green-500 text-4xl"></i>
						</div>
						<h3 class="text-xl font-bold mb-3 openWindow_title view">Проектирование</h3>
						<p class="text-gray-600 viewLeft">Первый и самый важный этап — создание проекта.
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

						<span
							class="openWindow_button mx-auto text-white w-10 h-10 flex items-center justify-center font-bold bg-green-300 px-10 rounded-lg my-2 viewRight">Читать</span>

						<span
							class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">1</span>
					</div>

					<!-- Step 2 -->
					<div class="timeline-item relative bg-gray-50 p-8 rounded-lg text-center openWindow view">
						<div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
							<i class="fa-solid fa-drafting-compass text-green-500 text-4xl"></i>
						</div>
						<h3 class="text-xl font-bold mb-3 openWindow_title viewRight">Подготовка участка и
							фундамент
						</h3>
						<p class="text-gray-600 view">Перед началом строительства проводят:</p>

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

						<span
							class="openWindow_button mx-auto text-white w-10 h-10 flex items-center justify-center font-bold bg-green-300 px-10 rounded-lg my-2 viewLeft">Читать</span>

						<span
							class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">2</span>
					</div>

					<!-- Step 3 -->
					<div class="timeline-item relative bg-gray-50 p-8 rounded-lg text-center openWindow viewRight">
						<div
							class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 viewRight">
							<i class="fa-solid fa-industry text-green-500 text-4xl"></i>
						</div>
						<h3 class="text-xl font-bold mb-3 openWindow_title view">Изготовление
							металлоконструкций</h3>
						<p class="text-gray-600 viewRight">Основные элементы (колонны, фермы)</p>

						<input class="openWindow_description hidden"
							value="
												Основные элементы (колонны, фермы, стеновые панели) производятся на заводе по готовым чертежам. Это гарантирует точность размеров и качество материалов." />

						<span
							class="openWindow_button mx-auto text-white w-10 h-10 flex items-center justify-center font-bold bg-green-300 px-10 rounded-lg my-2 viewLeft">Читать</span>

						<span
							class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">3</span>
					</div>

					<!-- Step 4 -->
					<div class="timeline-item relative bg-gray-50 p-8 rounded-lg text-center openWindow viewLeft">
						<div
							class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 viewLeft">
							<i class="fa-solid fa-screwdriver-wrench text-green-500 text-4xl"></i>
						</div>
						<h3 class="text-xl font-bold mb-3 openWindow_title view">Монтаж каркаса</h3>
						<p class="text-gray-600 viewLeft">После доставки конструкций на объект начинается
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

						<span
							class="openWindow_button mx-auto text-white w-10 h-10 flex items-center justify-center font-bold bg-green-300 px-10 rounded-lg my-2 viewRight">Читать</span>
						<span
							class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">4</span>
					</div>

					<!-- Step 5 -->
					<div class="timeline-item relative bg-gray-50 p-8 rounded-lg text-center openWindow view">
						<div
							class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 view">
							<i class="fa-solid fa-layer-group text-green-500 text-4xl"></i>
						</div>
						<h3 class="text-xl font-bold mb-3 openWindow_title viewRight">Утепление и обшивка
						</h3>
						<p class="text-gray-600 view">Стены и кровля обшиваются сэндвич-панелями или
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

						<span
							class="openWindow_button mx-auto text-white w-10 h-10 flex items-center justify-center font-bold bg-green-300 px-10 rounded-lg my-2 viewLeft">Читать</span>
						<span
							class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">5</span>
					</div>

					<!-- Step 6 -->
					<div class="timeline-item relative bg-gray-50 p-8 rounded-lg text-center openWindow viewRight">
						<div
							class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 viewRight">
							<i class="fa-solid fa-plug text-green-500 text-4xl"></i>
						</div>
						<h3 class="text-xl font-bold mb-3 openWindow_title view">Инженерные коммуникации
						</h3>
						<p class="text-gray-600 viewRight">Проводится монтаж систем отопления,
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

						<span
							class="openWindow_button mx-auto text-white w-10 h-10 flex items-center justify-center font-bold bg-green-300 px-10 rounded-lg my-2 viewLeft">Читать</span>
						<span
							class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">6</span>
					</div>

					<!-- Step 7 -->
					<div class="timeline-item relative bg-gray-50 p-8 rounded-lg text-center openWindow view">
						<div
							class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 viewRight">
							<i class="fa-solid fa-paint-roller text-green-500 text-4xl"></i>
						</div>
						<h3 class="text-xl font-bold mb-3 openWindow_title viewRight">Внутренняя отделка и
							сдача объекта
						</h3>
						<p class="text-gray-600 view">Выполняется отделка, уборка, проверка систем и сдача
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

						<span
							class="openWindow_button mx-auto text-white w-10 h-10 flex items-center justify-center font-bold bg-green-300 px-10 rounded-lg my-2 viewLeft">Читать</span>
						<span
							class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-green-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold">7</span>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- Contact Section -->
	<section id="contact" class="py-20 bg-green-600 text-white view">
		<div class="container mx-auto px-4">
			<div class="max-w-4xl mx-auto">
				<div class="text-center mb-12">
					<h2 class="text-3xl font-bold mb-4 viewLeft">Есть вопросы?</h2>
					<p class="text-xl view">Не копите их в себе. Просто спросите наших специалистов — они
						расскажут все
						просто и понятно!</p>
				</div>

				<div class="bg-white rounded-lg shadow-xl overflow-hidden view">
					<div class="md:flex">
						<div class="md:w-1/2 p-8 viewLeft">
							<h3 class="text-2xl font-bold text-gray-800 mb-6 view">Расчет стоимости
								коттеджей</h3>
							<form method="POST">
								<div class="mb-4">
									<input type="hidden" name="mailer">
									<input name="name" type="text" placeholder="Ваше имя"
										class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-800">
								</div>
								<div class="mb-4">
									<input name="phone" type="tel" placeholder="Телефон"
										class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-800">
								</div>
								<div class="mb-4">
									<input name="email" type="email" placeholder="Email"
										class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-800">
								</div>
								<div class="mb-6">
									<textarea name="message" placeholder=" Ваш вопрос"
										class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-800 h-24"></textarea>
								</div>
								<div class="mb-4">
									<label class="flex items-center text-gray-600 text-sm">
										<input type="checkbox" class="mr-2" name="checkbox">
										<span class="view">Я согласен(а) с Политикой конфиденциальности и
											Правилами
											обработки персональных
											данных</span>
									</label>
								</div>
								<button type="submit"
									class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-lg font-medium transition viewRight">Отправить
									заявку</button>
							</form>
						</div>
						<div class="md:w-1/2 bg-gray-50 p-8 flex flex-col justify-center viewRight">
							<div class="mb-8">
								<h4 class="text-xl font-bold text-gray-800 mb-4 view">Наши гарантии</h4>
								<ul class="space-y-3">
									<li class="flex items-start">
										<i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
										<span class="text-gray-700 viewLeft">Технадзор на каждом
											этапе</span>
									</li>
									<li class="flex items-start">
										<i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
										<span class="text-gray-700 view">Еженедельный фотоотчет</span>
									</li>
									<li class="flex items-start">
										<i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
										<span class="text-gray-700 viewRight">269 лет срок
											эксплуатации</span>
									</li>
									<li class="flex items-start">
										<i class="fas fa-check-circle text-green-500 mt-1 mr-2"></i>
										<span class="text-gray-700 view">Персональный менеджер</span>
									</li>
								</ul>
							</div>
							<div>
								<img src="https://xhaus.ru/design/xhaus/img/managers.png" alt="Managers"
									class="rounded-lg">
							</div>
						</div>
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
	<section id="process" class="py-20 bg-white view">
		<div class="container mx-auto px-4">
			<div class="text-center mb-10 view">
				<h2 class="text-3xl font-bold mb-4 viewLeft">Портфолио работ</h2>
				<p class="text-xl text-gray-600 max-w-2xl mx-auto view">Посмотрите примеры наших
					реализованных модульных
					зданий. <span class="viewRight">Больше фото и видео — в полном каталоге!</span></p>
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-trash-alt text-green-500 text-xl mr-2"></i>»
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-trash-alt text-green-500 text-xl mr-2"></i>»
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-trash-alt text-green-500 text-xl mr-2"></i>» <b>Мусорные
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-school text-green-500 text-xl mr-2"></i>» <b>Капитальный
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-school text-green-500 text-xl mr-2"></i>» <b>Капитальный
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-school text-green-500 text-xl mr-2"></i>» <b>Капитальный
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-chair text-green-500 text-xl mr-2"></i>» <b>Производство
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-bus text-green-500 text-xl mr-2"></i>»
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-train text-green-500 text-xl mr-2"></i>»
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-dumpster text-green-500 text-xl mr-2"></i>»
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-tractor text-green-500 text-xl mr-2"></i>»
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
									<i class="viewLeft fas fa-bolt text-green-500 text-xl"></i>
								</div>
								<h3>ФСС - услуги</h3>
							</div>
							<div class="viewLeft items-center">
								<h3> <i class="viewLeft fas fa-truck text-green-500 text-xl mr-2"></i>»
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
						class="inline-block bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-medium text-lg shadow viewLeft mx-auto">Посмотреть
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
	<!-- uslugi -->

	<section id="advantages" class="my-24 relative mt-auto view">
		<div class="text-center my-20 view">
			<h2 class="text-3xl font-bold mb-4 viewLeft">Мы выполняем</h2>
			<p class="text-xl text-gray-600 max-w-2xl mx-auto view">
				Услуги по производству, ремонту и оснащению модульных зданий. <span class="viewRight">Подробнее — в
					каталоге!</span>
			</p>
		</div>
		<div
			class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-10 lg:gap-20  mx-auto lg:w-[85%] transition-all duration-300">

			<a href="/pages/services/kapital/index.php" class="flex flex-col items-center group hover:scale-105 view">
				<div class="mb-4">
					<i class="fa fa-tools text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">1. Капитальный ремонт</span>
			</a>
			<a href="/pages/services/obchegitia/index.php"
				class="flex flex-col items-center group hover:scale-105 viewRight">
				<div class="mb-4">
					<i class="fa fa-bed text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">2. Общежитие</span>
			</a>
			<a href="/pages/services/proizvodim_modul/index.php"
				class="flex flex-col items-center group hover:scale-105 viewLeft">
				<div class="mb-4">
					<i class="fa fa-cube text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">3. Производство модульных
					зданий</span>
			</a>
			<a href="/pages/services/modultebel/index.php"
				class="flex flex-col items-center group hover:scale-105 viewRight">
				<div class="mb-4">
					<i class="fa fa-couch text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">4. Поставка и сборка
					мебели</span>
			</a>
			<div class="flex flex-col items-center group hover:scale-105 view cursor-no-drop">
				<div class="mb-4">
					<i class="fa fa-drafting-compass text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">5. Проектирование</span>
			</div>
			<div class="flex flex-col items-center group hover:scale-105 viewRight cursor-no-drop">
				<div class="mb-4">
					<i class="fa fa-plug text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">6. Монтаж инженерных
					сетей</span>
			</div>
			<a href="/pages/services/administrativnoe/index.php"
				class="flex flex-col items-center group hover:scale-105 viewLeft">
				<div class="mb-4">
					<i class="fa fa-users-cog text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">7. Административно бытовой
					комплекс</span>
			</a>
			<a href="/pages/services/generatornae/index.php"
				class="flex flex-col items-center group hover:scale-105 view">
				<div class="mb-4">
					<i class="fa fa-bolt text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">8. Генераторная</span>
			</a>
			<a href="/pages/services/modultebel/index.php"
				class="flex flex-col items-center group hover:scale-105 viewLeft">
				<div class="mb-4">
					<i class="fa fa-building text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">9. Модульные табельные
					здания</span>
			</a>
			<a href="/pages/services/naves/index.php" class="flex flex-col items-center group hover:scale-105 view">
				<div class="mb-4">
					<i class="fa fa-umbrella text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">10. Навесы</span>
			</a>
			<a href="/pages/services/compressor/index.php"
				class="flex flex-col items-center group hover:scale-105 viewRight">
				<div class="mb-4">
					<i class="fa fa-gas-pump text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">11. Компрессоры
					газовые</span>
			</a>
			<a href="/pages/services/compressor_air/index.php"
				class="flex flex-col items-center group hover:scale-105 viewLeft">
				<div class="mb-4">
					<i class="fa fa-wind text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">12. Компрессоры
					воздушные</span>
			</a>
			<a href="/pages/services/sclad/index.php" class="flex flex-col items-center group hover:scale-105 view">
				<div class="mb-4">
					<i class="fa fa-warehouse text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">13. Склады</span>
			</a>
			<a href="/pages/services/musorbak/index.php"
				class="flex flex-col items-center group hover:scale-105 viewLeft">
				<div class="mb-4">
					<i class="fa fa-trash-alt text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">14. Производство мусорных
					баков</span>
			</a>
			<a href="/pages/services/gruzoperevozki/index.php"
				class="flex flex-col items-center group hover:scale-105 viewLeft">
				<div class="mb-4">
					<i class="fa fa-truck-moving text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">15. Грузоперевозки</span>
			</a>
			<a href="/pages/services/spectehnika/index.php"
				class="flex flex-col items-center group hover:scale-105 viewLeft">
				<div class="mb-4">
					<i class="fa fa-dumpster text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">16. Спецтехника</span>
			</a>
			<a href="/pages/services/kapstroenie/index.php"
				class="flex flex-col items-center group hover:scale-105 viewLeft">
				<div class="mb-4">
					<i class="fa fa-city text-green-600 text-6xl"></i>
				</div>
				<span class="text-lg font-semibold text-center group-hover:text-green-600">17. Капитальное
					строение</span>
			</a>
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

	<!-- footer content -->
	<? include_once __DIR__ . '/assets/componets/footer.php'; ?>
	<!-- форма обратной связи -->
	<? include_once __DIR__ . '/assets/componets/forma.php'; ?>
	<!-- Подключение Яндекс.Карт и инициализация карты -->
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
	<!-- /Яндекс.Карта -->

	<!-- <div class="viewLeft contacts__map">
		<div class="viewLeft map rounded-lg shadow-lg" id="map_kavstal"></div>
	</div> -->

</body>

</html>