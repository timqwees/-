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
* | | \ \ \|\ \ \ \ \ \ \ \ __/|\ \ __/|\ \ \___|_ | |
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

// Изображения для капитального строения
$modulImages = [
	'/assets/image/services/капстроение/1.JPG',
	'/assets/image/services/капстроение/2.JPG',
	'/assets/image/services/капстроение/3.JPG',
	'/assets/image/services/капстроение/4.JPG',
	'/assets/image/services/капстроение/5.JPG',
	'/assets/image/services/капстроение/6.JPG',
	'/assets/image/services/капстроение/7.JPG',
	'/assets/image/services/капстроение/8.JPG',
	'/assets/image/services/капстроение/9.JPG',
	'/assets/image/services/капстроение/10.JPG',
	'/assets/image/services/капстроение/11.JPG',
	'/assets/image/services/капстроение/12.JPG',
	'/assets/image/services/капстроение/13.JPG',
	'/assets/image/services/капстроение/14.JPG',
	'/assets/image/services/капстроение/15.JPG',
	'/assets/image/services/капстроение/16.JPG',
	'/assets/image/services/капстроение/17.JPG',
	'/assets/image/services/капстроение/18.JPG',
	'/assets/image/services/капстроение/19.JPG',
];

$componentMailPath = dirname(__DIR__, 3) . '/assets/PHP/componet_mail.php';
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
	<title>Капитальное строение — строительство зданий | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Капитальное строение от ФАВОРИТ-СТРОЙ-СНАБ: строительство зданий и сооружений под ключ, гарантия, сроки, технадзор. Звоните +7 938 018-00-06">
	<meta name="theme-color" content="#22c55e">
	<link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow">
	<meta name="keywords"
		content="капитальное строение, строительство, здания, сооружения, FAVORIT-STROY-SNAB, под ключ, услуги">
	<meta name="author" content="TimQwees">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">
	<link rel="canonical" href="https://www.xn--h1aafj.xn--p1ai/pages/services/kapstroenie/" />
	<link rel="alternate" hreflang="ru" href="https://www.xn--h1aafj.xn--p1ai/pages/services/kapstroenie/" />
	<link rel="alternate" hreflang="x-default" href="https://www.xn--h1aafj.xn--p1ai/pages/services/kapstroenie/" />
	<!-- Open Graph / Facebook -->
	<meta property="og:locale" content="ru_RU">
	<meta property="og:site_name" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta property="og:title" content="Капитальное строение под ключ в Москве — строительство зданий">
	<meta property="og:description" content="Капитальное строение от ФАВОРИТ-СТРОЙ-СНАБ: строительство зданий и сооружений под ключ, гарантия, сроки, технадзор. Звоните +7 938 018-00-06">
	<meta property="og:image" content="https://www.xn--h1aafj.xn--p1ai/assets/image/icon/2.PNG">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="630">
	<meta property="og:image:alt" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://www.xn--h1aafj.xn--p1ai/pages/services/kapstroenie/">
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="Капитальное строение под ключ в Москве — строительство зданий">
	<meta name="twitter:description"
		content="Капитальное строение: строительство зданий и сооружений под ключ. Качественно, надежно, в срок.">
	<meta name="twitter:image" content="https://www.xn--h1aafj.xn--p1ai/assets/image/icon/2.PNG">
	<meta property="og:description"
		content="Капитальное строение: строительство зданий и сооружений под ключ. Качественно, надежно, в срок.">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">

	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="/assets/distCSS/style.css">
	<script defer src="/assets/distJS/main.js"></script>
	<!-- tailwind built into distCSS -->

	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer="true" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	<?php include_once dirname(__DIR__,3) . '/assets/PHP/sheoma.php'; ?>
</head>

<body class="font-sans text-gray-800">

	<?php include_once dirname(__DIR__, 3) . '/assets/componets/header.php'; ?>

	<section class="py-8 md:py-12 px-6 md:px-10 bg-[#f0fdf4]/30">
		<div class="container mx-auto">

			<div class="w-full flex flex-col lg:flex-row gap-8 items-start">

				<div class="flex-1 min-w-0 bg-white rounded-[24px] border border-gray-100 shadow-sm p-6 md:p-8">
					<nav class="flex items-center gap-1.5 text-xs text-gray-500 mb-4"><a href="/index.php" class="hover:text-emerald-700">Главная</a><span class="opacity-40">/</span><a href="/pages/uslugi/uslugi.php" class="hover:text-emerald-700">Услуги</a><span class="opacity-40">/</span><span class="text-gray-900 font-medium capitalize">kapstroenie</span></nav>
					<div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1 text-xs font-bold tracking-widest uppercase text-emerald-700 mb-3">С 2011 года • Под ключ</div>
					<h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900 mb-4">
						Капитальное строение
					</h1>

					<p class="text-[15px] leading-relaxed text-gray-600 mb-6">
						Компания FAVORIT-STROY-SNAB предоставляет услуги по капитальному строительству зданий и
						сооружений любой сложности. Мы осуществляем полный цикл работ — от проектирования до сдачи
						объекта «под ключ». Используем современные технологии и материалы, гарантируем качество,
						надежность и соблюдение сроков. Индивидуальный подход к каждому проекту, прозрачные условия и
						профессиональная команда специалистов.
					</p>
					<div class="grid grid-cols-3 gap-3 my-6">
						<div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3 text-center"><div class="text-lg font-black text-emerald-700">14 дней</div><div class="text-xs text-gray-600">от старта</div></div>
						<div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3 text-center"><div class="text-lg font-black text-emerald-700">50+ лет</div><div class="text-xs text-gray-600">службы</div></div>
						<div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3 text-center"><div class="text-lg font-black text-emerald-700">180+</div><div class="text-xs text-gray-600">объектов</div></div>
					</div>


					<!-- ######################################USLUGI################################################## -->

					<div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1 text-xs font-bold tracking-widest uppercase text-emerald-700 mb-4">Галерея объекта</div><h2 class="text-lg font-bold text-gray-900 mb-5">Фото выполненных работ</h2>


					<div
						class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
						<!-- start -->
						<?php foreach ($modulImages as $img): ?>
							<!-- items -->
							<div class="group relative rounded-[20px] overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition aspect-[4/3] bg-gray-100">
								<img class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.07] transition duration-700" src="<?php echo $img; ?>" alt="Фото объекта" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/assets/image/logo/logo.svg'">
								<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
								<span class="absolute top-3 left-3 bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">Фото</span>
								<div class="absolute bottom-0 inset-x-0 p-3 bg-gradient-to-t from-black/70 via-black/20 to-transparent">
									<div class="flex items-center gap-1.5 text-white text-xs font-semibold"><i class="fa-solid fa-shield-halved text-emerald-300"></i> Надежно • Гарантия 50+ лет</div>
								</div>
							</div>
							<!-- end items -->
						<?php endforeach; ?>

						<!-- end -->
					</div>

					<!-- ######################################################################################## -->

				</div>


				<div class="w-full lg:w-[380px] shrink-0 bg-white rounded-[24px] border border-gray-100 shadow-[0_12px_32px_rgba(15,23,42,.08)] p-6 sticky top-24">
					<form action="#" method="post" class="space-y-3" novalidate>
						<input type="hidden" name="content_form">
						<?php $abPath = dirname(__DIR__,3) . '/assets/componets/antibot_fields.php'; if(file_exists($abPath)) include_once $abPath; else { $alt=dirname(__DIR__,3).'/assets/PHP/Antibot.php'; if(file_exists($alt)){require_once $alt; echo Antibot::generateFields();}} ?>
						<input type="hidden" name="email" value="поле без почты!">
						<div class="relative">
							<div class="flex items-center gap-2 mb-4"><div class="w-8 h-8 rounded-xl bg-emerald-600 text-white grid place-items-center"><i class="fa-solid fa-paper-plane text-xs"></i></div><div><div class="font-bold text-gray-900 leading-none">Оставьте заявку</div><div class="text-xs text-gray-500">Отвечаем за 7 минут</div></div></div>
						</div>
						<div>
							<label for="name" class="block text-sm font-medium text-gray-700">Ваше имя</label>
							<input type="text" id="name" name="name"
								class="mt-1 w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
								required>
						</div>
						<div>
							<label for="phone" class="block text-sm font-medium text-gray-700">Телефон</label>
							<input type="tel" maxlength="18" placeholder="+7 (___) ___-__-__" id="phone" name="phone" pattern="^\+?[0-9\s\-\(\)]{10,20}$" autocomplete="tel" inputmode="tel" class="mt-1 w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
								required>
						</div>
						<div>
							<label for="message" class="block text-sm font-medium text-gray-700">Сообщение</label>
							<textarea id="message" name="message" rows="3" required minlength="10" maxlength="2000" class="mt-1 w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm min-h-[120px]"></textarea>
						</div>
						<div class="flex items-center">
							<input type="checkbox" id="checkbox" name="checkbox" class="mr-2" required>
							<label for="checkbox" class="text-sm text-gray-700 select-none cursor-pointer">
								Я согласен(а) с <a href="/pages/politic/index.php"
									class="text-green-600 underline hover:text-green-800">политикой
									конфиденциальности</a>
							</label>
						</div>
						<div class="flex flex-col sm:flex-row justify-between items-center gap-4">
							<button type="submit"
								class="w-full bg-emerald-600 hover:bg-emerald-700 text-white rounded-full py-3 font-semibold shadow-md transition flex items-center justify-center gap-2">Отправить заявку <i class="fa-solid fa-arrow-right text-xs"></i></button>
							<span class="text-[12px] text-green-700">Отправьте заявку и получите
								консультацию по капитальному строительству</span>
						</div>
					</form>
				</div>

			</div>
		</div>
	</section>


	<!-- footer content -->
	<?php include_once dirname(__DIR__, 3) . '/assets/componets/footer.php'; ?>

	<!-- Подключение Яндекс.Карт и инициализация карты -->
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
	<!-- /Яндекс.Карта -->

	<!-- <div class="viewLeft contacts__map">
		<div class="viewLeft map rounded-lg shadow-lg" id="map_kavstal"></div>
	</div> -->

</body>

</html>