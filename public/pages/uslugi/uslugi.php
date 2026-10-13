<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Услуги — Производство, ремонт, доставка | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Все услуги ФАВОРИТ-СТРОЙ-СНАБ: БВЗ под ключ, капитальный ремонт, грузоперевозки, спецтехника, мебель, склады, навесы.">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow">
	<link rel="icon" type="image/png" sizes="32x32" href="/public/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/public/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/public/assets/image/favicon/site.webmanifest">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261009">
	<script defer src="/public/assets/distJS/main.js"></script>
	<!-- tailwind built into distCSS -->

	<!-- canonical -->
	<?= seo_canonical_tags() ?>

	<?= seo_og_tags('Услуги — Производство, ремонт, доставка | ФАВОРИТ-СТРОЙ-СНАБ', 'Все услуги ФАВОРИТ-СТРОЙ-СНАБ: БВЗ под ключ, капитальный ремонт, грузоперевозки, спецтехника, мебель, склады, навесы.') ?>


	<!-- scripts modal connect+fh -->

<!-- end -->



	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer="true" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	<style>
		.bx-green {
			box-shadow: 0 8px 32px 0 rgba(34, 197, 94, 0.45), 0 1.5px 8px 0 rgba(34, 197, 94, 0.15);
			border: 2.5px solid #F97316;
			transition: ease-in-out .1s all;
			will-change: transform, box-shadow, filter;
			position: relative;
			overflow: hidden;
			background: #fff;
			cursor: pointer;
		}

		.bx-green:hover {
			box-shadow: 0 30px 48px 0 rgba(34, 197, 94, 0.60), 0 3px 16px 0 rgba(34, 197, 94, 0.20), 0 0 0 8px rgba(34, 197, 94, 0.08);
			border-color: #EA580C;
			transform: scale(1.045) rotate(-1.5deg) translateY(-6px);
			filter: brightness(1.08) saturate(1.15) drop-shadow(0 0 16px #F9731633);
			z-index: 10;
		}
	</style>
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>

<?= seo_analytics() ?>

<?= seo_utm_script() ?>

<?= seo_goals_script() ?>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261009">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261009">
</head>

	<!-- Main Content -->
	<main class="bg-gray-50 pb-16">


		<!-- footer content -->
		<?php
// Last-Modified для SEO
include_once __DIR__ . '/../../components/header.php'; ?>
	<section class="bg-[#f0fdf4] border-b border-emerald-100">
		<div class="mx-auto max-w-[1280px] px-4 py-10 md:py-14">
			<div class="inline-flex items-center gap-2 bg-white border border-emerald-200 rounded-full px-3 py-1 text-xs font-bold tracking-widest uppercase text-emerald-700">Услуги • Полный цикл</div>
			<h1 class="mt-3 text-3xl md:text-4xl font-black tracking-tight text-gray-900">Строим, ремонтируем, поставляем</h1>
			<p class="mt-2 text-[15px] leading-relaxed text-gray-600 max-w-2xl">От быстровозводимых зданий до мебели и спецтехники — всё с гарантией и технадзором. Выберите направление.</p>
			<div class="mt-4 flex gap-2">
				<a href="#services-list" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-full px-5 py-2.5 font-semibold text-sm">Выбрать услугу <i class="fa-solid fa-arrow-down text-xs"></i></a>
				<a href="/#contact" class="inline-flex items-center gap-2 bg-white border border-emerald-200 text-emerald-800 rounded-full px-5 py-2.5 font-semibold text-sm">Получить консультацию</a>
			</div>
		</div>
	</section>
	<nav class="w-full px-6 md:px-10 py-3 bg-[#f0fdf4]/30 border-b border-emerald-100 text-xs text-gray-500 flex items-center gap-1.5"><a href="/" class="hover:text-emerald-700">Главная</a><span class="opacity-40">/</span><span class="text-gray-900 font-medium">Услуги</span></nav>

		<section class="my-10"></section>

		<!-- SEO-оптимизированные услуги компании -->
		<section id="services-list" class="container mx-auto px-4 py-12 flex flex-col md:flex-row items-center gap-8">
			<div class="md:w-1/2">
				<h2 class="text-3xl md:text-4xl font-bold mb-4 viewleft">Строительство модульных зданий </h2>
				<p class="mb-6 text-gray-700 font-medium view">Компания <span
						class="text-green-500 font-semibold">Фаворит-Строй-Снаб</span> осуществляет <strong
						class="text-green-500">строительство модульных зданий под ключ</strong> с применением
					<strong class="text-green-500">сборных элементов</strong>,
					изготовленных на собственном заводе. Такой подход позволяет <strong class="text-green-500">сократить
						сроки возведения</strong> и снизить затраты по сравнению
					с традиционными технологиями. <strong class="text-green-500">Модульные здания</strong> широко
					используются для <strong class="text-green-500">складов</strong>,
					<strong class="text-green-500">торговых</strong> и <strong class="text-green-500">производственных
						помещений</strong>,
					а также <strong class="text-green-500">жилых объектов</strong>. Мы гарантируем качество,
					надежность и соблюдение всех строительных норм.
				</p>
				<ul class="pl-5 mb-4 text-gray-700 space-y-2 overflow-hidden">
					<li class="view flex items-start"><i
							class="fas fa-industry text-green-500 mt-1 mr-2"></i><strong>Собственное
							производство модулей</strong></li>
					<li class="view flex items-start"><i
							class="fas fa-bolt text-green-500 mt-1 mr-2"></i><strong>Быстрый
							монтаж на
							объекте</strong></li>
					<li class="view flex items-start"><i
							class="fas fa-drafting-compass text-green-500 mt-1 mr-2"></i><strong>Индивидуальные
							проекты под заказ</strong></li>
				</ul>
			</div>
			<div class="md:w-1/2 flex justify-center"><img src="/public/pages/uslugi/img/banner.jpeg"
					alt="Строительство модульных зданий"
					class="rounded-xl shadow-lg max-w-md w-full object-cover viewRight bx-green transition-all duration-500">
			</div>
		</section>

		<section class="container mx-auto px-4 py-12 flex flex-col md:flex-row items-center gap-8 relative">
			<div class="md:w-1/2 flex justify-center z-10"><img src="/public/pages/uslugi/img/2.jpg"
					alt="Доставка и сборка мебели"
					class="rounded-xl shadow-lg max-w-md w-full object-cover viewRight bx-green transition-all duration-500">
			</div>
			<div class="absolute inset-0 z-0 opacity-20 pointer-events-none"><img src="/public/pages/uslugi/img/2.jpg"
					alt="Фон для мебели" class="w-full h-full object-cover rounded-xl bx-green"></div>
			<div class="md:w-1/2 z-10">
				<h2 class="text-3xl md:text-4xl font-bold mb-4 viewleft">Доставка и сборка мебели</h2>
				<p class="mb-6 text-gray-700 font-medium view">Оказываем <strong class="text-green-500">комплексные
						услуги по доставке и сборке мебели</strong> любого
					класса и назначения. Наши специалисты обеспечивают <strong class="text-green-500">аккуратную
						транспортировку</strong> и <strong class="text-green-500">профессиональную сборку
						мебели</strong> на объекте заказчика. Это позволяет <strong class="text-green-500">избежать
						хлопот</strong> с самостоятельной перевозкой и сборкой,
					а также гарантирует <strong class="text-green-500">экономию времени</strong> и <strong
						class="text-green-500">безопасность мебели</strong>. </p>
				<ul class="pl-5 mb-4 text-gray-700 space-y-2 overflow-hidden">
					<li class="viewRight flex items-start"><i
							class="fas fa-truck-moving text-green-500 mt-1 mr-2"></i><strong>Доставка мебели по
							Москве и области</strong></li>
					<li class="viewRight flex items-start"><i
							class="fas fa-tools text-green-500 mt-1 mr-2"></i><strong>Сборка
							мебели любой
							сложности</strong></li>
					<li class="viewRight flex items-start"><i
							class="fas fa-shield-alt text-green-500 mt-1 mr-2"></i><strong>Гарантия на
							выполненные работы</strong></li>
				</ul>
			</div>
		</section>

		<section class="container mx-auto px-4 py-12 flex md:flex-row items-center gap-8 flex-col">
			<div class="md:w-1/2">
				<h2 class="text-3xl md:text-4xl font-bold mb-4 view">Капитальный ремонт зданий</h2>
				<p class="mb-6 text-gray-700 font-medium viewLeft">Выполняем <strong class="text-green-500">капитальный
						ремонт зданий и сооружений</strong> любой сложности.
					<strong class="text-green-500">Комплекс строительных работ</strong> включает <strong
						class="text-green-500">восстановление</strong> и <strong class="text-green-500">замену
						изношенных элементов</strong>,
					<strong class="text-green-500">ремонт инженерных систем</strong>,
					<strong class="text-green-500">фасадов</strong> и <strong class="text-green-500">кровли</strong>.
					Все работы проводятся в соответствии с <strong class="text-green-500">нормативными
						требованиями</strong> и с применением современных
					технологий,
					что позволяет <strong class="text-green-500">улучшить эксплуатационные
						характеристики</strong> объекта.
				</p>
				<ul class="pl-5 mb-4 text-gray-700 space-y-2 overflow-hidden">
					<li class="viewLeft flex items-start"><i
							class="fas fa-exchange-alt text-green-500 mt-1 mr-2"></i><strong>Замена инженерных
							коммуникаций</strong></li>
					<li class="viewLeft flex items-start"><i
							class="fas fa-hard-hat text-green-500 mt-1 mr-2"></i><strong>Ремонт
							и усиление
							несущих конструкций</strong></li>
					<li class="viewLeft flex items-start"><i
							class="fas fa-building text-green-500 mt-1 mr-2"></i><strong>Восстановление фасадов
							и кровли</strong></li>
				</ul>
			</div>
			<div class="md:w-1/2 flex justify-center"><img src="/public/pages/uslugi/img/3.jpeg"
					alt="Капитальный ремонт зданий"
					class="rounded-xl shadow-lg max-w-md w-full object-cover viewRight bx-green transition-all duration-500">
			</div>
		</section>

		<section class="container mx-auto px-4 py-12 flex md:flex-row items-center gap-8 flex-col">
			<div class="md:w-1/2 flex justify-center"><img
					src="/public/assets/image/services/МТЗ/TBiT7i6PKC59P2fly3WALvJkWWYPWJbSXWj13AyXfSRvSB2btvao6G4HxpIzQMtWxlCM_ZzG5sFpdzqKpkFVVUjP.jpg"
					alt="Изготовление мебели"
					class="rounded-xl shadow-lg max-w-md w-full object-cover viewRight bx-green transition-all duration-500" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="md:w-1/2">
				<h2 class="text-3xl md:text-4xl font-bold mb-4 view">Изготовление мебели</h2>
				<p class="mb-6 text-gray-700 font-medium viewRight">Мы предлагаем <strong
						class="text-green-500">изготовление
						мебели на заказ</strong> для офисов,
					учебных заведений,
					общежитий и других объектов. Вся мебель производится с учетом <strong
						class="text-green-500">индивидуальных требований</strong> заказчика,
					используются только <strong class="text-green-500">качественные материалы</strong> и
					современное оборудование. Наши специалисты помогут подобрать оптимальные решения по <strong
						class="text-green-500">дизайну</strong> и <strong
						class="text-green-500">функциональности</strong>. </p>
				<ul class="pl-5 mb-4 text-gray-700 space-y-2 overflow-hidden">
					<li class="viewRight flex items-start"><i
							class="fas fa-couch text-green-500 mt-1 mr-2"></i><strong>Изготовление корпусной и
							мягкой мебели</strong></li>
					<li class="viewRight flex items-start"><i
							class="fas fa-pencil-ruler text-green-500 mt-1 mr-2"></i><strong>Индивидуальный
							дизайн и проектирование</strong></li>
					<li class="viewRight flex items-start"><i
							class="fas fa-cube text-green-500 mt-1 mr-2"></i><strong>Использование современных и
							экологичных материалов</strong></li>
					<li class="viewRight flex items-start"><i
							class="fas fa-check-circle text-green-500 mt-1 mr-2"></i><strong>Гарантия качества и
							долговечности</strong></li>
				</ul>
			</div>
		</section>
	<!-- Каталог услуг — хаб перелинковки (PageRank Loop). Генерируется из public/data/services.php -->
	<section class="container mx-auto px-4 py-14">
		<h2 class="text-3xl md:text-4xl font-black tracking-tight text-gray-900 mb-3">Все услуги компании</h2>
		<p class="text-gray-600 mb-8 max-w-3xl">18 направлений — от производства модульных зданий до аренды техники. Работаем по договору, фиксируем цену в смете, выполняем объекты в Москве, Московской области и по всей России.</p>
		<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach (seo_services() as $slug => $svc): ?>
				<a href="/services/<?= htmlspecialchars($slug) ?>"
					class="group bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition" data-goal="service_card_click">
					<div class="flex items-start justify-between gap-3">
						<h3 class="text-[16px] font-bold text-gray-900 group-hover:text-emerald-700"><?= htmlspecialchars($svc['title']) ?></h3>
						<i class="fa-solid fa-arrow-right text-emerald-600 text-xs mt-1.5"></i>
					</div>
					<p class="mt-2.5 text-sm leading-relaxed text-gray-600"><?= htmlspecialchars(mb_substr($svc['description'], 0, 130)) ?>…</p>
					<div class="mt-3 flex flex-wrap gap-1.5">
						<?php foreach (array_slice($svc['queries'], 0, 3) as $q): ?>
							<span class="text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full px-2 py-0.5"><?= htmlspecialchars($q) ?></span>
						<?php endforeach; ?>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
	</main>

	<!-- footer content -->
	<?php include_once __DIR__ . '/../../components/footer.php'; ?>
	<?php include_once __DIR__ . '/../../components/forma.php'; ?>
	</body>

</html>