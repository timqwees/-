<?php require_once __DIR__ . '/../../app/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Каталог проектов — Быстровозводимые здания | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Каталог типовых проектов БВЗ: склады, ангары, АБК, общежития, магазины. Цены, планировки, сроки от 2 недель.">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow">

	<!-- favicon -->
	<link rel="icon" type="image/png" sizes="32x32" href="/public/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/public/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/public/assets/image/favicon/site.webmanifest">

	<!-- Styles and scripts -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261009">
	<!-- tailwind built into distCSS -->

	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

	<!-- canonical -->
	<?= seo_canonical_tags() ?>

	<?= seo_og_tags('Каталог проектов — Быстровозводимые здания | ФАВОРИТ-СТРОЙ-СНАБ', 'Каталог типовых проектов БВЗ: склады, ангары, АБК, общежития, магазины. Цены, планировки, сроки от 2 недель.') ?>

	<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261009">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261009">
</head>

<body class="bg-gray-50 font-sans text-gray-800">
	<?php include_once __DIR__ . '/../components/header.php'; ?>
	<section class="bg-[#f0fdf4] border-b border-emerald-100">
		<div class="mx-auto max-w-[1280px] px-4 py-10 md:py-14">
			<div class="inline-flex items-center gap-2 bg-white border border-emerald-200 rounded-full px-3 py-1 text-xs font-bold tracking-widest uppercase text-emerald-700">Каталог • Фото и видео</div>
			<h1 class="mt-3 text-3xl md:text-4xl font-black tracking-tight text-gray-900">Каталог <span class="text-emerald-600">объектов</span></h1>
			<p class="mt-2 text-[15px] leading-relaxed text-gray-600 max-w-2xl">Школы, склады, навесы, компрессорные, общежития — с фото и видео. Выберите категорию — покажем смету за 15 минут.</p>
		</div>
	</section>
	<nav class="w-full px-6 md:px-10 py-3 bg-[#f0fdf4]/30 border-b border-emerald-100 text-xs text-gray-500 flex items-center gap-1.5"><a href="/" class="hover:text-emerald-700">Главная</a><span class="opacity-40">/</span><span class="text-gray-900 font-medium">Каталог проектов</span></nav>
	<nav class="w-full px-6 md:px-10 py-3 bg-[#f0fdf4]/30 border-b border-emerald-100 text-xs text-gray-500 flex items-center gap-1.5"><a href="/" class="hover:text-emerald-700">Главная</a><span class="opacity-40">/</span><span class="text-gray-900 font-medium">Каталог проектов</span></nav>

	<section class="my-20"></section>
	<main class="container mx-auto px-4 py-12">
		<h2 class="text-4xl font-bold mb-6 text-center viewleft">Каталог <span
				class="viewright inline-block">обьектов</span></h2>
		<p class="text-lg text-gray-600 text-center mb-10 view">
			Здесь вы найдёте <span class="viewleft font-semibold">фото</span> и <span
				class="viewright font-semibold">видео</span> реализованных объектов
			компании: <span class="viewleft">школы</span>, <span class="viewright">склады</span>, <span
				class="viewleft">навесы</span>, <span class="viewright">компрессорные</span>, <span
				class="viewleft">общежития</span> и многое другое.
		</p>
		<!-- Фильтры по категориям -->
		<div class="flex flex-wrap justify-center mb-10 view" id="project-filters">
			<button class="filter-btn active viewleft" data-filter="all">Все</button>
			<button class="filter-btn viewright" data-filter="Школа фото">Школа</button>
			<button class="filter-btn viewleft" data-filter="Мусор БАК">Мусорные баки</button>
			<button class="filter-btn viewright" data-filter="Общежитие">Общежитие</button>
			<button class="filter-btn viewleft" data-filter="МТЗ">МТЗ</button>
			<button class="filter-btn viewright" data-filter="Навесы">Навесы</button>
			<button class="filter-btn viewleft" data-filter="Компрессоры газовые">Компрессоры газовые</button>
			<button class="filter-btn viewright" data-filter="Компрессоры воздушные">Компрессоры воздушные</button>
			<button class="filter-btn viewleft" data-filter="Склады">Склады</button>
			<button class="filter-btn viewright" data-filter="Генераторные">Генераторные</button>
			<button class="filter-btn viewleft" data-filter="АБК">АБК</button>
		</div>
		<div class="project-grid view" id="project-grid">
			<!-- Примеры карточек, остальные можно добавить аналогично -->
			<div class="project-card viewleft" data-category="Школа фото">
				<span class="category viewright z-20">Школа</span>
				<img class="view" src="/public/assets/image/services/Школа фото/WhatsApp Image 2025-07-06 at 13.40.00.jpeg"
					alt="Школа 1" loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="project-card viewright" data-category="Мусор БАК">
				<span class="category viewLight z-20">Мусорные баки</span>
				<video class="view" src="/public/assets/image/services/Мусор БАК/WhatsApp Video 2025-07-07 at 17.38.28-3.mp4"
					controls loop muted playsinline></video>
			</div>
			<div class="project-card viewleft" data-category="МТЗ">
				<span class="category viewRight z-20">МТЗ</span>
				<img class="view"
					src="/public/assets/image/services/МТЗ/n_pdc5b2tgyjydxTAt_7ev1rBBFN26bwbq4796VCT5BHICCidmF-CnC59nv7sETn82ryDyaC4PqRG_nijl-iaTBU.jpg"
					alt="МТЗ 1" loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="project-card viewright" data-category="Общежитие">
				<span class="category viewLight z-20">Общежитие</span>
				<img class="view" src="/public/assets/image/services/Общежитие/WhatsApp Image 2025-07-01 at 09.50.01.jpeg"
					alt="Общежитие 1" loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="project-card viewleft" data-category="Навесы">
				<span class="category viewRight z-20">Навесы</span>
				<img class="view" src="/public/assets/image/services/Навесы/WhatsApp Image 2025-07-01 at 11.50.05.jpeg"
					alt="Навесы 1" loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="project-card viewright" data-category="Компрессоры газовые">
				<span class="category viewLight z-20">Компрессоры газовые</span>
				<img class="view" src="/public/assets/image/services/Компрессоры газовые/PHOTO-2023-02-17-17-20-45.jpg"
					alt="Газ компрессор 1" loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="project-card viewleft" data-category="Компрессоры воздушные">
				<span class="category viewRight z-20">Компрессоры воздушные</span>
				<img class="view"
					src="/public/assets/image/services/Компрессоры воздушные/6E6BD4AB-6EC1-425D-A012-BE82A0D36F6F (1).JPG"
					alt="Воздушный компрессор 1" loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="project-card viewright" data-category="Склады">
				<span class="category viewLight z-20">Склады</span>
				<img class="view" src="/public/assets/image/services/Склады/WhatsApp Image 2025-07-01 at 12.15.39 (1).jpeg"
					alt="Склады 1" loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="project-card viewleft" data-category="Генераторные">
				<span class="category viewRight z-20">Генераторные</span>
				<img class="view" src="/public/assets/image/services/Генераторные/PHOTO-2022-08-08-18-14-09 (1).jpg"
					alt="Генераторные 1" loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<div class="project-card viewright" data-category="АБК">
				<span class="category viewLight z-20">АБК</span>
				<img class="view" src="/public/assets/image/services/АБК/826af4bb-a922-49e9-8399-31e13493e24e.JPG" alt="АБК 1"
					loading="lazy" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'" >
			</div>
			<!-- Добавьте остальные карточки по аналогии -->
		</div>
	</main>
	<?php include_once __DIR__ . '/../components/footer.php'; ?>

	<script defer src="/public/assets/distJS/main.js?v=20261009"></script>
	<script>
		// Фильтрация по категориям
		document.querySelectorAll('.filter-btn').forEach(btn => {
			btn.addEventListener('click', function () {
				document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
				btn.classList.add('active');
				const filter = btn.getAttribute('data-filter');
				document.querySelectorAll('.project-card').forEach(card => {
					if (filter === 'all' || card.getAttribute('data-category') === filter) {
						card.style.display = '';
					} else {
						card.style.display = 'none';
					}
				});
			});
		});
		// Модальное окно для фото/видео
		document.querySelectorAll('.project-card img, .project-card video').forEach(el => {
			el.addEventListener('click', function (e) {
				e.stopPropagation();
				const modal = document.createElement('div');
				modal.className = 'modal';
				const content = document.createElement('div');
				content.className = 'modal-content';
				const close = document.createElement('div');
				close.className = 'modal-close';
				close.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>';
				close.onclick = () => document.body.removeChild(modal);
				if (el.tagName === 'IMG') {
					const img = document.createElement('img');
					img.src = el.src;
					img.alt = el.alt;
					img.className = 'max-h-[80vh] max-w-[90vw] rounded-xl';
					content.appendChild(img);
				} else if (el.tagName === 'VIDEO') {
					const video = document.createElement('video');
					video.src = el.src;
					video.controls = true;
					video.autoplay = true;
					video.className = 'max-h-[80vh] max-w-[90vw] rounded-xl';
					content.appendChild(video);
				}
				content.appendChild(close);
				modal.appendChild(content);
				modal.onclick = e => { if (e.target === modal) document.body.removeChild(modal); };
				document.body.appendChild(modal);
			});
		});
	</script>

	<!-- форма обратной связи -->
	<?php include_once __DIR__ . '/../components/forma.php'; ?>
</body>

</html>