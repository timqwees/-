<?php
/**
	* Страница политики конфиденциальности
	* FAVORIT-STROY-SNAB
	*/
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<title>Политика конфиденциальности | FAVORIT-STROY-SNAB</title>
	<meta name="description"
		content="Политика конфиденциальности сайта FAVORIT-STROY-SNAB. Узнайте, как мы обрабатываем и защищаем ваши персональные данные.">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="/assets/distCSS/style.css">
</head>

<body class="font-sans text-gray-800 bg-gray-50">
	<?php include_once dirname(__DIR__, 2) . '/assets/componets/header.php'; ?>

	<section class="py-16 px-4 lg:px-20">
		<div class="container mx-auto bg-white rounded-lg shadow-lg p-8">
			<h1 class="text-3xl font-bold mb-6 text-gray-900">Политика конфиденциальности</h1>
			<p class="mb-4 text-gray-700">
				Настоящая Политика конфиденциальности определяет порядок обработки и защиты персональных данных
				пользователей сайта FAVORIT-STROY-SNAB (далее — Сайт).
			</p>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">1. Общие положения</h2>
			<ul class="list-disc pl-6 mb-4 text-gray-700">
				<li>Использование Сайта означает согласие пользователя с настоящей Политикой и условиями обработки его
					персональных данных.</li>
				<li>В случае несогласия с условиями Политики пользователь должен прекратить использование Сайта.</li>
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
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">6. Изменение политики конфиденциальности</h2>
			<p class="mb-4 text-gray-700">
				Администрация Сайта вправе вносить изменения в настоящую Политику. Новая редакция вступает в силу с
				момента ее размещения на Сайте.
			</p>
			<h2 class="text-xl font-semibold mt-6 mb-2 text-gray-800">7. Контактная информация</h2>
			<p class="mb-4 text-gray-700">
				По вопросам, связанным с обработкой персональных данных, вы можете обратиться по электронной почте: <a
					href="mailto:info@фсс.рус" class="text-green-600 underline">fss@фсс.рус</a>
			</p>
			<p class="text-gray-600 text-sm mt-8">Последнее обновление: <?php echo date('d.m.Y'); ?></p>
		</div>
	</section>

	<?php include_once dirname(__DIR__, 2) . '/assets/componets/footer.php'; ?>
</body>

</html>