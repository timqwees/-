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
	'https://фсс.рус/assets/image/services/Генераторные/PHOTO-2022-08-08-18-14-09 (1).jpg',
	'https://фсс.рус/assets/image/services/Генераторные/PHOTO-2022-08-08-18-14-09.jpg',
	'https://фсс.рус/assets/image/services/Генераторные/PHOTO-2020-11-05-10-03-22.jpg',
	'https://фсс.рус/assets/image/services/Генераторные/PHOTO-2020-11-05-10-03-54.jpg',
	'https://фсс.рус/assets/image/services/Генераторные/83934507-0570-4305-BAD7-CA9F218BFCCC.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/86371565-2804-4E82-803A-46606FBAE4F8.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/458DAF97-3386-4FE1-8CEE-B0C42AFC664A.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/475B3160-0677-4596-848A-544BEB831550.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/4F470B9C-BCB9-4B29-AB98-4C96E26ECC66.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/5CC0F37F-8EE5-4CA0-9530-AC27F5C01A0B.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/2BE1E005-3A0F-4D63-88CB-A369955A6A2A.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/2D4D055B-3A88-438B-8AB7-6388D8CB8C94.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/017C294C-9AF3-4638-ADC2-476CD4C3A7ED.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/02C32373-C168-4E51-AE88-79C9C0001058.JPG',
	'https://фсс.рус/assets/image/services/Генераторные/0463AE7A-8A91-44AB-9099-EF2ED8919223.JPG',
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
	<title>Дизель-генераторные установки — ДГУ | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Поставка дизель-генераторных установок, ДГУ, компрессорные станции.">
	<meta name="theme-color" content="#22c55e">
	<link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow">
	<meta name="keywords" content="генераторные установки, дизельные генераторы, электроснабжение, FAVORIT-STROY-SNAB">
	<meta name="author" content="TimQwees">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">
	<!-- Open Graph / Facebook -->
	<meta property="og:title" content="Дизель-генераторные установки — ДГУ">
	<meta property="og:description" content="Поставка дизель-генераторных установок, ДГУ, компрессорные станции.">
	<meta property="og:image" content="/assets/image/icon/2.PNG">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://www.xn--h1aafj.xn--p1ai/pages/services/generatornae/">
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="Дизель-генераторные установки — ДГУ">
	<meta name="twitter:description" content="Поставка дизель-генераторных установок, ДГУ, компрессорные станции.">
	<meta name="twitter:image" content="/assets/image/icon/2.PNG">
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
						Генераторные установки
					</h2>

					<p class="text-gray-600 tracking-wide mb-5">
						Мы специализируемся на поставке и монтаже генераторных установок для предприятий.
						Наши генераторы отличаются высокой надежностью, экономичностью и соответствием всем стандартам.
						Предлагаем дизельные и газовые генераторы различных мощностей для резервного и основного
						электроснабжения. Гарантируем качество, техническую поддержку и сервисное обслуживание.
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
					<form action="#" method="post" class="space-y-4" novalidate novalidate>
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