<?/**
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

$modulImages = [
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/6E6BD4AB-6EC1-425D-A012-BE82A0D36F6F (1).JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/826B4B5B-EAA2-4AF5-9F18-295AC2ADC0AA.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/917FEF02-F2DB-47EF-9755-9FA77405E103.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/941E1DE2-3461-431D-A5F9-B5EFC58DBCC7.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/994B73F8-FFC0-4C03-B248-C06805D8DAC9.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/4EBFB1F7-874B-4F0F-9051-022E21985CE4.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/508C0D48-83AE-41EF-A0DF-2C52E5568777.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/603BA6CA-8F0B-44A9-9810-7EBBA30B90C3.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/2CEAAF1A-2D04-4402-AECC-74028D59EFF7.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/3BF11BAB-D791-4F41-A8B9-C013FD42B1C4.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/1B3E6DA9-E1DF-455A-8EFB-4944E369EFB1.JPG',
	'https://фсс.рус/assets/image/services/Компрессоры воздушные/0217DFBA-52BA-4B2F-9611-0E89157D29EB.JPG',
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
	<title>Воздушные компрессоры — поставка | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Воздушные компрессоры: промышленные, винтовые, поршневые.">
	<meta name="theme-color" content="#22c55e">
	<link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow">
	<meta name="keywords" content="воздушные компрессоры, промышленное оборудование, компрессоры, FAVORIT-STROY-SNAB">
	<meta name="author" content="TimQwees">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">
	<link rel="canonical" href="https://www.xn--h1aafj.xn--p1ai/pages/services/compressor_air/" />
	<link rel="alternate" hreflang="ru" href="https://www.xn--h1aafj.xn--p1ai/pages/services/compressor_air/" />
	<link rel="alternate" hreflang="x-default" href="https://www.xn--h1aafj.xn--p1ai/pages/services/compressor_air/" />
	<!-- Open Graph / Facebook -->
	<meta property="og:locale" content="ru_RU">
	<meta property="og:site_name" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta property="og:title" content="Воздушные компрессоры — поставка">
	<meta property="og:description" content="Воздушные компрессоры: промышленные, винтовые, поршневые.">
	<meta property="og:image" content="https://www.xn--h1aafj.xn--p1ai/assets/image/icon/2.PNG">
	<meta property="og:image:width" content="1200">
	<meta property="og:image:height" content="630">
	<meta property="og:image:alt" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://www.xn--h1aafj.xn--p1ai/pages/services/compressor_air/">
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="Воздушные компрессоры — поставка">
	<meta name="twitter:description" content="Воздушные компрессоры: промышленные, винтовые, поршневые.">
	<meta name="twitter:image" content="https://www.xn--h1aafj.xn--p1ai/assets/image/icon/2.PNG">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">

	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="/assets/distCSS/style.css">
	<script defer src="/assets/distJS/main.js"></script>
	<link rel="stylesheet" href="tailwind.css">

	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer="true" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	<?php include_once dirname(__DIR__,3) . '/assets/PHP/sheoma.php'; ?>
</head>

<body class="font-sans text-gray-800">

	<? include_once dirname(__DIR__, 3) . '/assets/componets/header.php'; ?>

	<section class="py-20 px-4 lg:px-20">
		<div class="container mx-auto">

			<div class="w-full p-6 bg-white flex flex-col lg:flex-row gap-10 justify-between">

				<div class="relative mb-10 ">
					<h2
						class="font-bold text-2xl mb-6 relative after:content-[''] after:block after:h-[2px] after:bg-gray-300 after:w-1/3 after:absolute after:-bottom-2 after:left-0 text-gray-900">
						Воздушные компрессоры
					</h2>

					<p class="text-gray-600 tracking-wide mb-5">
						Мы специализируемся на поставке и обслуживании воздушных компрессоров для промышленности.
						Наше оборудование отличается высокой производительностью, надежностью и энергоэффективностью.
						Предлагаем компрессоры различных типов и мощностей для предприятий любого масштаба.
						Гарантируем качество, техническую поддержку и сервисное обслуживание.
					</p>


					<!-- ######################################USLUGI################################################## -->

					<h3 class="text-xl text-black font-bold mb-5 text-gray-900">Наши сервисы:</h3>


					<div
						class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 px-4 pl-0 rounded-lg overflow-x-hidden overflow-y-hidden">
						<!-- start -->
						<?php foreach ($modulImages as $img): ?>
							<!-- items -->
							<div
								class="flex flex-col items-center w-full p-4 border-2 via-white rounded-lg shadow-xl transition-all duration-500 cursor-pointer hover:scale-105 hover:shadow-2xl group relative overflow-hidden view">
								<div class="w-full aspect-[4/1] relative">
									<img class="rounded-2xl object-cover bg-center w-full h-[120px] transition-transform duration-500 group-hover:scale-105"
										src="<?php echo $img; ?>" alt="Офисное модульное здание">
									<span
										class="absolute top-3 left-3 bg-green-600/90 text-white text-sm font-semibold px-3 py-1.5 rounded-full shadow-lg animate-pulse">
										Фото
									</span>
								</div>
								<div class="mt-5"></div>
								<div
									class="absolute bottom-2 mx-auto flex items-center gap-2 text-green-600 text-sm opacity-80">
									<i class="fa-solid fa-shield-halved"></i>
									<span>Надежно!</span>
								</div>
							</div>
							<!-- end items -->
						<?php endforeach; ?>

						<!-- end -->
					</div>

					<!-- ######################################################################################## -->

				</div>


				<div class="relative w-full p-6">
					<form action="#" method="post" class="space-y-4" novalidate>
						<input type="hidden" name="content_form">
						<?php $abPath = dirname(__DIR__,3) . '/assets/componets/antibot_fields.php'; if(file_exists($abPath)) include_once $abPath; else { $alt=dirname(__DIR__,3).'/assets/PHP/Antibot.php'; if(file_exists($alt)){require_once $alt; echo Antibot::generateFields();}} ?>
						<input type="hidden" name="email" value="поле без почты!">
						<div class="relative">
							<h3
								class="font-bold text-lg mb-2 after:content-[''] after:block after:h-[2px] after:bg-gray-300 after:w-1/3 after:absolute after:-bottom-2 after:left-0">
								Форма обратной связи</h3>
						</div>
						<div>
							<label for="name" class="block text-sm font-medium text-gray-700">Ваше имя</label>
							<input type="text" id="name" name="name"
								class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-600"
								required>
						</div>
						<div>
							<label for="phone" class="block text-sm font-medium text-gray-700">Телефон</label>
							<input type="tel" id="phone" name="phone" pattern="^\+?[0-9\s\-\(\)]{10,20}$" autocomplete="tel" inputmode="tel" class="mt-1 block w-full border border-gray-300 rounded-md p-2  focus:outline-none focus:ring-2 focus:ring-green-600"
								required>
						</div>
						<div>
							<label for="message" class="block text-sm font-medium text-gray-700">Сообщение</label>
							<textarea id="message" name="message" rows="3" required minlength="10" maxlength="2000" class="mt-1 block w-full border border-gray-300 rounded-md p-2 h-[30vh]  focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
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
								class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition w-full sm:w-auto">Отправить</button>
							<span class="text-[12px] text-green-700">Отправьте заявку и получите
								поддержку от специалистов</span>
						</div>
					</form>
				</div>

			</div>
		</div>
	</section>


	<!-- footer content -->
	<? include_once dirname(__DIR__, 3) . '/assets/componets/footer.php'; ?>

	<!-- Подключение Яндекс.Карт и инициализация карты -->
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
	<!-- /Яндекс.Карта -->

	<!-- <div class="viewLeft contacts__map">
		<div class="viewLeft map rounded-lg shadow-lg" id="map_kavstal"></div>
	</div> -->

</body>

</html>