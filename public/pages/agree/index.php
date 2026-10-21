<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
/**
	* Страница правил обработки персональных данных
	* FAVORIT-STROY-SNAB
	*/
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-latin.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-cyrillic.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/fonts/WixMadeforDisplay/WixMadeforDisplay-Variable.woff2" crossorigin><link rel="stylesheet" href="/public/assets/vendor/fonts/fonts.css">
	<style> body { font-family: 'DM Sans Variable', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>

	<title>Согласие на обработку данных — ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Согласие на обработку персональных данных ФАВОРИТ-СТРОЙ-СНАБ: порядок сбора, хранения и защиты данных клиентов сайта. Права субъектов данных.">
	<meta name="robots" content="noindex, follow">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<?= seo_verify_tags() ?>
	<?= seo_canonical_tags() ?>
	<?= seo_og_tags('Согласие на обработку персональных данных — ФАВОРИТ-СТРОЙ-СНАБ', 'Согласие на обработку ПДн ФАВОРИТ-СТРОЙ-СНАБ: какие данные собираем, как защищаем, ваши права. Актуально для заказчиков БВЗ, капремонта, спецтехники.', '/public/assets/image/og/og-blog-1200x630.jpg') ?>
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/manifest.json" />
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>

<?= seo_analytics() ?>

<?= seo_utm_script() ?>

<?= seo_goals_script() ?>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=<?= seo_ver() ?>">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=<?= seo_ver() ?>">
</head>

<body class="font-sans text-gray-800 bg-gray-50" data-page="inner">
	<?php include_once __DIR__ . '/../../components/header.php'; ?>
	<nav class="w-full px-6 md:px-10 py-3 bg-[#f0fdf4]/30 border-b border-emerald-100 text-xs text-gray-500 flex items-center gap-1.5" style="padding-top:calc(var(--fss-header-h, 90px) + 8px)"><a href="/" title="Главная" class="hover:text-emerald-700">Главная</a><span class="opacity-40">/</span><span class="text-gray-900 font-medium">Согласие на обработку данных</span></nav>

	<section class="py-16 px-4 lg:px-20">
		<div class="container mx-auto bg-white rounded-lg shadow-lg p-8 fss-legal">
			<h1 class="pg-h1">Согласие на обработку персональных данных ФАВОРИТ-СТРОЙ-СНАБ</h1>
			<p class="mb-4 text-gray-700">
				Настоящие Правила определяют порядок обработки и защиты персональных данных пользователей сайта
				FAVORIT-STROY-SNAB (далее — Сайт).
			</p>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">1. Общие положения</h2>
			<ul class="list-disc pl-6 mb-4 text-gray-700">
				<li>Использование Сайта означает согласие пользователя с настоящими Правилами и условиями обработки его
					персональных данных.</li>
				<li>В случае несогласия с условиями Правил пользователь должен прекратить использование Сайта.</li>
			</ul>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">2. Персональные данные пользователей</h2>
			<p class="mb-4 text-gray-700">
				Сайт может собирать следующие персональные данные:
			</p>
			<ul class="list-disc pl-6 mb-4 text-gray-700">
				<li>Имя</li>
				<li>Телефон</li>
				<li>Адрес электронной почты (если предоставлен)</li>
				<li>Иные сведения, которые пользователь сообщает добровольно через формы обратной связи</li>
			</ul>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">3. Цели обработки персональных данных</h2>
			<ul class="list-disc pl-6 mb-4 text-gray-700">
				<li>Обработка заявок и обращений пользователей</li>
				<li>Обратная связь с пользователем</li>
				<li>Улучшение качества предоставляемых услуг</li>
				<li>Выполнение требований законодательства РФ</li>
			</ul>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">4. Защита персональных данных</h2>
			<p class="mb-4 text-gray-700">
				Сайт принимает необходимые организационные и технические меры для защиты персональных данных от
				неправомерного или случайного доступа, уничтожения, изменения, блокирования, копирования,
				распространения, а также от иных неправомерных действий третьих лиц.
			</p>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">5. Передача данных третьим лицам</h2>
			<p class="mb-4 text-gray-700">
				Персональные данные пользователей не передаются третьим лицам, за исключением случаев, предусмотренных
				законодательством РФ.
			</p>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">6. Изменение правил обработки персональных данных
			</h2>
			<p class="mb-4 text-gray-700">
				Администрация Сайта вправе вносить изменения в настоящие Правила. Новая редакция вступает в силу с
				момента ее размещения на Сайте.
			</p>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">7. Контактная информация</h2>
			<p class="mb-4 text-gray-700">
				По вопросам, связанным с обработкой персональных данных, вы можете обратиться по электронной почте: 				<a
					href="mailto:<?= htmlspecialchars((string)seo_config('email_puny')) ?>" title="Написать на email" class="text-green-600 underline"><?= htmlspecialchars((string)seo_config('email')) ?></a>
			</p>
			<p class="text-gray-600 text-sm mt-8">Последнее обновление: <?php echo date('d.m.Y'); ?></p>
		</div>
	</section>

	<?php include_once __DIR__ . '/../../components/footer.php'; ?>
</body>

</html>