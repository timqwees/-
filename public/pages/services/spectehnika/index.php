<?php
require_once __DIR__ . '/../../../../app/bootstrap.php';
// Изображения спецтехники
$modulImages = [
	'/public/assets/image/services/Спецтехника/1.jpg',
	'/public/assets/image/services/Спецтехника/2.jpg',
	'/public/assets/image/services/Спецтехника/3.jpg',
];

$componentMailPath = __DIR__ . '/../../../../app/Mail/componet_mail.php';
if (file_exists($componentMailPath)) {
	include_once $componentMailPath;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"></noscript>
	<style> body { font-family: 'Wix Madefor Display', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>

	<title>Аренда спецтехники — бульдозер, трактор, Шакман | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Аренда спецтехники с операторами: бульдозеры, экскаваторы, Шакман, самосвалы. Подача по Москве и МО, оплата по сменам.">
	<meta name="theme-color" content="#F97316">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<link rel="icon" type="image/png" sizes="32x32" href="/public/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/public/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/public/assets/image/favicon/site.webmanifest">
	<?= seo_canonical_tags() ?>

	<?= seo_og_tags('Аренда спецтехники — бульдозер, трактор, Шакман | ФАВОРИТ-СТРОЙ-СНАБ', 'Аренда спецтехники: бульдозеры, тракторы, грузовики Шакман, доставка. Москва и МО.') ?>


	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261009">
	<script defer src="/public/assets/distJS/main.js?v=20261009"></script>
	<!-- tailwind built into distCSS -->

	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer="true" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js?v=20261009"></script>

	<?php include_once __DIR__ . '/../../../components/sheoma.php'; ?>

<?= seo_analytics() ?>

<?= seo_utm_script() ?>

<?= seo_goals_script() ?>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261009">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261009">
</head>

<body class="font-sans text-gray-800">

	<?php include_once __DIR__ . '/../../../components/header.php'; ?>

	<section class="py-8 md:py-12 px-6 md:px-10 bg-[#f0fdf4]/30">
		<div class="container mx-auto">

			<div class="w-full flex flex-col lg:flex-row gap-8 items-start">

				<div class="flex-1 min-w-0 bg-white rounded-[24px] border border-gray-100 shadow-sm p-6 md:p-8">
					<nav class="flex items-center gap-1.5 text-xs text-gray-500 mb-4"><a href="/" class="hover:text-emerald-700">Главная</a><span class="opacity-40">/</span><a href="/uslugi" class="hover:text-emerald-700">Услуги</a><span class="opacity-40">/</span><span class="text-gray-900 font-medium">Аренда спецтехники</span></nav>
					<div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-full px-3 py-1 text-xs font-bold tracking-widest uppercase text-emerald-700 mb-3">С 2011 года • Под ключ • Допуск СРО</div>
					<h1 class="pg-h1">
						Аренда спецтехники
					</h1>

					<p class="text-[15px] leading-relaxed text-gray-600 mb-6">
						Наша компания предоставляет услуги аренды современной спецтехники для строительных, дорожных и
						других работ.
						В нашем автопарке — экскаваторы, бульдозеры, автокраны, самосвалы, погрузчики и другая техника.
						Все машины проходят регулярное обслуживание и управляются опытными операторами.
						Мы поможем подобрать оптимальное решение для ваших задач и обеспечим быструю подачу техники на
						объект.
					</p>
					<div class="grid grid-cols-3 gap-3 my-6">
						<div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3 text-center"><div class="text-lg font-black text-emerald-700">14 дней</div><div class="text-xs text-gray-600">от старта</div></div>
						<div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3 text-center"><div class="text-lg font-black text-emerald-700">50+ лет</div><div class="text-xs text-gray-600">службы</div></div>
						<div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-3 text-center"><div class="text-lg font-black text-emerald-700">180+</div><div class="text-xs text-gray-600">объектов</div></div>
					</div>

					<!-- ######################################USLUGI################################################## -->

					<h3 class="text-xl text-black font-bold mb-5 text-gray-900">Доступная спецтехника:</h3>

					<div
						class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
						<!-- start -->
						<?php
						foreach ($modulImages as $i => $img): ?>
							<!-- items -->
							<div
								class="group relative rounded-[20px] overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition aspect-[4/3] bg-gray-100">
								<img class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.07] transition duration-700"
										src="<?php echo $img; ?>" alt="<?php echo $techNames[$i] ?? 'Спецтехника'; ?>" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'">
									<span class="absolute top-3 left-3 bg-emerald-600 text-white text-sm font-semibold px-3 py-1.5 rounded-full shadow-lg animate-pulse">
										Фото
									</span>
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
						<input type="hidden" name="content_form" value="Аренда спецтехники">
						<?php $abPath = __DIR__ . '/../../../components/antibot_fields.php'; if(file_exists($abPath)) include_once $abPath; else { $alt=__DIR__ . '/../../../../app/Mail/Antibot.php'; if(file_exists($alt)){require_once $alt; echo Antibot::generateFields();}} ?>
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
							<label for="message" class="block text-sm font-medium text-gray-700">Какая техника
								интересует?</label>
							<textarea id="message" name="message" rows="3" required minlength="10" maxlength="2000" class="mt-1 w-full px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm min-h-[120px]"
								placeholder="Опишите задачу, вид техники, сроки и адрес объекта"></textarea>
						</div>
						<div class="flex items-center">
							<input type="checkbox" id="checkbox" name="checkbox" class="mr-2" required>
							<label for="checkbox" class="text-sm text-gray-700 select-none cursor-pointer">
								Я согласен(а) с <a href="/politika"
									class="text-green-600 underline hover:text-green-800">политикой
									конфиденциальности</a>
							</label>
						</div>
						<div class="flex flex-col sm:flex-row justify-between items-center gap-4">
							<button type="submit"
								class="w-full bg-emerald-600 hover:bg-emerald-700 text-white rounded-full py-3 font-semibold shadow-md transition flex items-center justify-center gap-2">Отправить заявку <i class="fa-solid fa-arrow-right text-xs"></i></button>
							<span class="text-[12px] text-green-700">Отправьте заявку — мы подберём спецтехнику под ваши
								задачи!</span>
						</div>
					</form>
				</div>

			</div>
		</div>
	</section>


	<!-- footer content -->
	<!-- Перелинковка PageRank Loop + FAQ (schema FAQPage) -->
	<?php include_once __DIR__ . '/../../../components/related_services.php'; ?>

	<?php include_once __DIR__ . '/../../../components/footer.php'; ?>



</body>

</html>