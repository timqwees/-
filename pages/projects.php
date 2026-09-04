<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Каталог проектов | Быстровозводимые модульные здания</title>
	<meta name="description"
		content="Каталог реализованных проектов: школы, склады, навесы, общежития и другие модульные здания. Фото и видео объектов.">
	<meta name="keywords"
		content="модульные здания, проекты, школы, склады, навесы, общежития, компрессорные, генераторные, строительство, домостроение, FAVORIT-STROY-SNAB, строительные услуги, материалы">
	<meta name="author" content="TimQwees">
	<meta name="robots" content="index, follow">

	<!-- favicon -->
	<link rel="icon" type="image/png" sizes="32x32" href="/assets/image/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/assets/image/favicon/favicon-16x16.png">
	<link rel="apple-touch-icon" sizes="180x180" href="/assets/image/favicon/apple-touch-icon.png">
	<link rel="manifest" href="/assets/image/favicon/site.webmanifest">

	<!-- Styles and scripts -->
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="/assets/distCSS/style.css">
	<script src="/assets/distJS/main.js"></script>
	<link rel="stylesheet" href="tailwind.css">

	<!-- scripts modal connect+fh -->
	<script src="/local/templates/rs2023/js/fh.js"></script>
	<script src="/local/templates/rs2023/js/modal.js"></script>
	<!-- end -->

	<!-- Open Graph / Facebook -->
	<meta property="og:title" content="FAVORIT-STROY-SNAB - Современное домостроение">
	<meta property="og:description" content="Качественные строительные услуги и материалы.">
	<meta property="og:image" content="/assets/image/icon/2.PNG">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://www.фсс.рус/">
	<!-- Twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="FAVORIT-STROY-SNAB - Современное домостроение">
	<meta name="twitter:description" content="Качественные строительные услуги и материалы.">
	<meta name="twitter:image" content="/assets/image/icon/2.PNG">

	<!-- Swiper Slider for Banner -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
	<script defer="true" src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

	<!-- canonical -->
	<link rel="canonical" href="https://www.kavstal.ru/pages/projects.php" />
</head>

<body class="bg-gray-50 font-sans text-gray-800">
	<? include_once dirname(__DIR__, 1) . '/assets/componets/header.php'; ?>

	<section class="my-20"></section>
	<main class="container mx-auto px-4 py-12">
		<h1 class="text-4xl font-bold mb-6 text-center viewleft">Каталог <span
				class="viewright inline-block">обьектов</span></h1>
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
				<img class="view" src="/assets/image/services/Школа фото/WhatsApp Image 2025-07-06 at 13.40.00.jpeg"
					alt="Школа 1" loading="lazy">
			</div>
			<div class="project-card viewright" data-category="Мусор БАК">
				<span class="category viewLight z-20">Мусорные баки</span>
				<video class="view" src="/assets/image/services/Мусор БАК/WhatsApp Video 2025-07-07 at 17.38.28-3.mp4"
					controls loop muted playsinline></video>
			</div>
			<div class="project-card viewleft" data-category="МТЗ">
				<span class="category viewRight z-20">МТЗ</span>
				<img class="view"
					src="/assets/image/services/МТЗ/n_pdc5b2tgyjydxTAt_7ev1rBBFN26bwbq4796VCT5BHICCidmF-CnC59nv7sETn82ryDyaC4PqRG_nijl-iaTBU.jpg"
					alt="МТЗ 1" loading="lazy">
			</div>
			<div class="project-card viewright" data-category="Общежитие">
				<span class="category viewLight z-20">Общежитие</span>
				<img class="view" src="/assets/image/services/Общежитие/WhatsApp Image 2025-07-01 at 09.50.01.jpeg"
					alt="Общежитие 1" loading="lazy">
			</div>
			<div class="project-card viewleft" data-category="Навесы">
				<span class="category viewRight z-20">Навесы</span>
				<img class="view" src="/assets/image/services/Навесы/WhatsApp Image 2025-07-01 at 11.50.05.jpeg"
					alt="Навесы 1" loading="lazy">
			</div>
			<div class="project-card viewright" data-category="Компрессоры газовые">
				<span class="category viewLight z-20">Компрессоры газовые</span>
				<img class="view" src="/assets/image/services/Компрессоры газовые/PHOTO-2023-02-17-17-20-45.jpg"
					alt="Газ компрессор 1" loading="lazy">
			</div>
			<div class="project-card viewleft" data-category="Компрессоры воздушные">
				<span class="category viewRight z-20">Компрессоры воздушные</span>
				<img class="view"
					src="/assets/image/services/Компрессоры воздушные/6E6BD4AB-6EC1-425D-A012-BE82A0D36F6F (1).JPG"
					alt="Воздушный компрессор 1" loading="lazy">
			</div>
			<div class="project-card viewright" data-category="Склады">
				<span class="category viewLight z-20">Склады</span>
				<img class="view" src="/assets/image/services/Склады/WhatsApp Image 2025-07-01 at 12.15.39 (1).jpeg"
					alt="Склады 1" loading="lazy">
			</div>
			<div class="project-card viewleft" data-category="Генераторные">
				<span class="category viewRight z-20">Генераторные</span>
				<img class="view" src="/assets/image/services/Генераторные/PHOTO-2022-08-08-18-14-09 (1).jpg"
					alt="Генераторные 1" loading="lazy">
			</div>
			<div class="project-card viewright" data-category="АБК">
				<span class="category viewLight z-20">АБК</span>
				<img class="view" src="/assets/image/services/АБК/826af4bb-a922-49e9-8399-31e13493e24e.JPG" alt="АБК 1"
					loading="lazy">
			</div>
			<!-- Добавьте остальные карточки по аналогии -->
		</div>
	</main>
	<? include_once dirname(__DIR__, 1) . '/assets/componets/footer.php'; ?>

	<script src="/assets/distJS/main.js"></script>
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
	<? include_once dirname(__DIR__, 1) . '/assets/componets/forma.php'; ?>
</body>

</html>