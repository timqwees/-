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
	'https://фсс.рус/assets/image/services/АБК/826af4bb-a922-49e9-8399-31e13493e24e.JPG',
	'https://фсс.рус/assets/image/services/АБК/88d88c8a-a2d2-435a-b98e-46eac4b97e6b.JPG',
	'https://фсс.рус/assets/image/services/АБК/94de6d9e-4d8d-4ffe-ab23-d6eb830443c7.JPG',
	'https://фсс.рус/assets/image/services/АБК/94fe0ce5-1e0d-46f8-bcce-2e4e48089e11.JPG',
	'https://фсс.рус/assets/image/services/АБК/969bd8c5-4261-4c83-a438-25076ef6879f.JPG',
	'https://фсс.рус/assets/image/services/АБК/9ae01222-4c6a-4c71-bc7b-dc7ccae05954.JPG',
	'https://фсс.рус/assets/image/services/АБК/412f4b10-4e04-4e24-a809-f4b98c451f9e.JPG',
	'https://фсс.рус/assets/image/services/АБК/4eb40ff7-527a-4f7e-9a20-d1bfeb6601b2.JPG',
	'https://фсс.рус/assets/image/services/АБК/59f1ad99-f1e5-4640-807e-ca4326725fc4.JPG',
	'https://фсс.рус/assets/image/services/АБК/5de688c3-9bf7-4e3b-b818-48b8b4f9b31a.JPG',
	'https://фсс.рус/assets/image/services/АБК/6363b4d2-80ef-4af5-afad-60838f329be3.JPG',
	'https://фсс.рус/assets/image/services/АБК/6c7d6515-85d3-4a04-b35e-8d6376122ca0.JPG',
	'https://фсс.рус/assets/image/services/АБК/04d8caee-f083-42f8-8be2-64e7e461cace.JPG',
	'https://фсс.рус/assets/image/services/АБК/05fcff5c-1d8e-4767-be29-a2e6b2bca7ab.JPG',
	'https://фсс.рус/assets/image/services/АБК/10fd99d6-066e-4962-8ce3-4e49f690dd4c.JPG',
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
	<!-- scripts modal connect+fh -->
	<script src="/local/templates/rs2023/js/fh.js"></script>
	<script src="/local/templates/rs2023/js/modal.js"></script>
	<!-- end -->

	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>FAVORIT-STROY-SNAB - Административные здания и офисы</title>
	<meta name="description"
		content="FAVORIT-STROY-SNAB — Строительство административных зданий и офисов. Современные решения для бизнеса.">
	<meta name="robots" content="index, follow">
	<meta name="keywords" content="административные здания, офисы, строительство, FAVORIT-STROY-SNAB, бизнес-центры">
	<meta name="author" content="TimQwees">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">
	<!-- Open Graph / Facebook -->
	<meta property="og:title" content="FAVORIT-STROY-SNAB - Современное домостроение">
	<meta property="og:description" content="Качественные строительные услуги и материалы.">
	<meta property="og:image" content="/assets/image/icon/2.PNG">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://фсс.рус/">
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="FAVORIT-STROY-SNAB - Современное домостроение">
	<meta name="twitter:description" content="Качественные строительные услуги и материалы.">
	<meta name="twitter:image" content="/assets/image/banner.jpeg">
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

	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer="true" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</head>

<body class="font-sans text-gray-800">

	<? include_once dirname(__DIR__, 3) . '/assets/componets/header.php'; ?>

	<section class="py-20 px-4 lg:px-20">
		<div class="container mx-auto">

			<div class="w-full p-6 bg-white flex flex-col lg:flex-row gap-10 justify-between">

				<div class="relative mb-10 ">
					<h2
						class="font-bold text-2xl mb-6 relative after:content-[''] after:block after:h-[2px] after:bg-gray-300 after:w-1/3 after:absolute after:-bottom-2 after:left-0 text-gray-900">
						Административные здания и офисы
					</h2>

					<p class="text-gray-600 tracking-wide mb-5">
						Мы специализируемся на строительстве современных административных зданий и офисных комплексов.
						Наши решения отличаются функциональностью, комфортом и современным дизайном. Предлагаем
						индивидуальные проекты для бизнеса любого масштаба - от небольших офисов до крупных
						административных центров. Гарантируем качество, сроки и соответствие всем требованиям.
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
					<form action="#" method="post" class="space-y-4">
						<input type="hidden" name="content_form">
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
							<input type="tel" id="phone" name="phone"
								class="mt-1 block w-full border border-gray-300 rounded-md p-2  focus:outline-none focus:ring-2 focus:ring-green-600"
								required>
						</div>
						<div>
							<label for="message" class="block text-sm font-medium text-gray-700">Сообщение</label>
							<textarea id="message" name="message" rows="3"
								class="mt-1 block w-full border border-gray-300 rounded-md p-2 h-[30vh]  focus:outline-none focus:ring-2 focus:ring-green-600"></textarea>
						</div>
						<div class="flex items-center">
							<input type="checkbox" id="checkbox" name="checkbox" class="mr-2">
							<label for="checkbox" class="text-sm text-gray-700 select-none cursor-pointer">
								Я согласен(а) с <a href="/privacy-policy"
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