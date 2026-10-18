<?php require_once __DIR__ . '/../../app/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"></noscript>
	<style>
.esp-wrap{max-width:1440px;margin:0 auto;padding:0 clamp(16px,3vw,44px);}
.esp-top{padding-top:104px;}
.esp-over{margin:0;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#171310;}
.esp-h1{margin:18px 0 0;font-weight:600;text-transform:uppercase;letter-spacing:-.03em;line-height:.95;color:#111;font-size:clamp(52px,8.5vw,148px);}
.esp-h1 .it{display:block;font-family:Georgia,'Times New Roman',serif;font-style:italic;font-weight:400;text-transform:uppercase;padding-left:16%;}
.esp-filters{display:flex;flex-wrap:wrap;justify-content:center;gap:8px 30px;margin:36px 0 6px;}
.esp-fbtn{background:none;border:0;cursor:pointer;font-family:inherit;font-size:11.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#A8A29E;padding:6px 2px;text-underline-offset:7px;}
.esp-fbtn:hover{color:#171310;}
.esp-fbtn.active{color:#171310;text-decoration:underline;text-decoration-thickness:2px;}
.esp-scroll{text-align:center;font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;margin:28px 0 34px;color:#171310;}
.esp-grid{columns:5 260px;column-gap:18px;max-width:2200px;margin:0 auto;padding:0 clamp(12px,2.5vw,40px);}
.esp-item{break-inside:avoid;margin:0 0 26px;cursor:zoom-in;}
.esp-item[hidden]{display:none;}
.esp-item .ph{display:block;overflow:hidden;background:#F5F5F4;position:relative;}
.esp-item img,.esp-item video{width:100%;display:block;transition:transform .5s ease;}
.esp-item:hover img{transform:scale(1.035);}
.esp-item{content-visibility:auto;contain-intrinsic-size:320px;}
.esp-item img{opacity:0;transition:opacity .45s ease,transform .5s ease;}
.esp-item img.done{opacity:1;}
.esp-item img[data-src]{min-height:220px;}
.esp-cap{margin:9px 0 0;font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#111;}
.esp-play{position:absolute;right:12px;bottom:12px;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;font-size:13px;color:#111;}
@keyframes espIn{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}
.esp-item.flash{animation:espIn .5s cubic-bezier(.16,1,.3,1) both;}
@media(prefers-reduced-motion:reduce){.esp-item img{transition:none;}.esp-item.flash{animation:none;}}
</style>

	<title>Портфолио — Реализованные объекты | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Портфолио выполненных объектов: фото и видео школ, складов, АБК, общежитий, навесов. 180+ сданных объектов.">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/public/assets/image/logo/site.webmanifest" />

	<!-- Swiper CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<!-- tailwind built into distCSS -->

	<!-- canonical -->
	<?= seo_canonical_tags() ?>
<?= seo_verify_tags() ?>

	<?= seo_og_tags('Портфолио — Реализованные объекты | ФАВОРИТ-СТРОЙ-СНАБ', 'Портфолио выполненных объектов: фото и видео школ, складов, АБК, общежитий, навесов. 180+ сданных объектов.', '/public/assets/image/og/og-portfolio-1200x630.jpg') ?>

	<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
	<script type="application/ld+json">
	<?= json_encode([
		'@context' => 'https://schema.org',
		'@graph' => [
			[
				'@type' => 'ItemList',
				'name' => 'Портфолио реализованных объектов ФАВОРИТ-СТРОЙ-СНАБ',
				'description' => 'Фото и видео сданных объектов: склады, ангары, АБК, общежития, школы, навесы. 180+ объектов в Москве, МО и России.',
				'numberOfItems' => 180,
				'itemListOrder' => 'https://schema.org/Unordered',
				'itemListElement' => array_map(function ($i) {
					return [
						'@type' => 'ListItem',
						'position' => $i,
						'name' => 'Объект ' . $i,
						'url' => (string)seo_config('base_url') . '/portfolio#obj-' . $i,
					];
				}, range(1, 20)),
			],
		],
	], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS) ?>
	</script>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<!-- tailwind built into distCSS -->
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<style>
	.esp-wrap{max-width:1440px;margin:0 auto;padding:0 clamp(16px,3vw,44px);}
.esp-top{padding-top:104px;}
.esp-over{margin:0;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#171310;}
.esp-h1{margin:18px 0 0;font-weight:600;text-transform:uppercase;letter-spacing:-.03em;line-height:.95;color:#111;font-size:clamp(52px,8.5vw,148px);}
.esp-h1 .it{display:block;font-family:Georgia,'Times New Roman',serif;font-style:italic;font-weight:400;text-transform:uppercase;padding-left:16%;}
.esp-filters{display:flex;flex-wrap:wrap;justify-content:center;gap:8px 30px;margin:36px 0 6px;}
.esp-fbtn{background:none;border:0;cursor:pointer;font-family:inherit;font-size:11.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#A8A29E;padding:6px 2px;text-underline-offset:7px;}
.esp-fbtn:hover{color:#171310;}
.esp-fbtn.active{color:#171310;text-decoration:underline;text-decoration-thickness:2px;}
.esp-scroll{text-align:center;font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;margin:28px 0 34px;color:#171310;}
.esp-grid{columns:5 260px;column-gap:18px;max-width:2200px;margin:0 auto;padding:0 clamp(12px,2.5vw,40px);}
.esp-item{break-inside:avoid;margin:0 0 26px;cursor:zoom-in;}
.esp-item[hidden]{display:none;}
.esp-item .ph{display:block;overflow:hidden;background:#F5F5F4;position:relative;}
.esp-item img,.esp-item video{width:100%;display:block;transition:transform .5s ease;}
.esp-item:hover img{transform:scale(1.035);}
.esp-item{content-visibility:auto;contain-intrinsic-size:320px;}
.esp-item img{opacity:0;transition:opacity .45s ease,transform .5s ease;}
.esp-item img.done{opacity:1;}
.esp-item img[data-src]{min-height:220px;}
.esp-cap{margin:9px 0 0;font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#111;}
.esp-play{position:absolute;right:12px;bottom:12px;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;font-size:13px;color:#111;}
@keyframes espIn{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}
.esp-item.flash{animation:espIn .5s cubic-bezier(.16,1,.3,1) both;}
@media(prefers-reduced-motion:reduce){.esp-item img{transition:none;}.esp-item.flash{animation:none;}}
  .fss-breadcrumbs { margin-bottom: 20px; }
  .fss-bc-list { display: flex; flex-wrap: wrap; gap: 6px; list-style: none; margin: 0; padding: 0; font-size: 13px; color: #6b7078; }
  .fss-bc-item { display: flex; align-items: center; }
  .fss-bc-item:not(:last-child)::after { content: "/"; margin-left: 6px; color: #b0b3b8; }
  .fss-bc-link { color: #6b7078; text-decoration: none; transition: color .2s; }
  .fss-bc-link:hover { color: #F97316; }
  .fss-bc-current { color: #17191d; font-weight: 600; }
</style>
</head>

<body class="font-sans text-gray-800">
	<?php include_once __DIR__ . '/../components/header.php'; ?>
	<div class="esp-wrap">
	<?= seo_breadcrumbs_html() ?>
		<div class="esp-top">
			<p class="esp-over">Наши реализованные объекты</p>
			<h1 class="esp-h1">Проекты <span class="it">что вдохновляют</span></h1>
		</div>
		<div class="esp-filters" role="group" aria-label="Фильтр объектов">
			<button class="esp-fbtn active" type="button" data-f="all" aria-pressed="true">Смотреть все</button>
			<button class="esp-fbtn" type="button" data-f="abk" aria-pressed="false">АБК</button>
			<button class="esp-fbtn" type="button" data-f="gen" aria-pressed="false">ДГУ</button>
			<button class="esp-fbtn" type="button" data-f="kap" aria-pressed="false">Капстроение</button>
			<button class="esp-fbtn" type="button" data-f="gruz" aria-pressed="false">Перевозки</button>
			<button class="esp-fbtn" type="button" data-f="spec" aria-pressed="false">Спецтехника</button>
			<button class="esp-fbtn" type="button" data-f="video" aria-pressed="false">Видео</button>
		</div>
		<p class="esp-scroll">Листайте вниз</p>
	</div>
		<main class="esp-grid" id="espGrid">
			<figure class="esp-item" data-cat="abk" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/АБК/04d8caee-f083-42f8-8be2-64e7e461cace.JPG" alt="АБК — объект 1" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/05fcff5c-1d8e-4767-be29-a2e6b2bca7ab.JPG" alt="АБК — объект 2" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/АБК/10fd99d6-066e-4962-8ce3-4e49f690dd4c.JPG" alt="АБК — объект 3" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/412f4b10-4e04-4e24-a809-f4b98c451f9e.JPG" alt="АБК — объект 4" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/4eb40ff7-527a-4f7e-9a20-d1bfeb6601b2.JPG" alt="АБК — объект 5" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/АБК/59f1ad99-f1e5-4640-807e-ca4326725fc4.JPG" alt="АБК — объект 6" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/5de688c3-9bf7-4e3b-b818-48b8b4f9b31a.JPG" alt="АБК — объект 7" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/6363b4d2-80ef-4af5-afad-60838f329be3.JPG" alt="АБК — объект 8" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/6c7d6515-85d3-4a04-b35e-8d6376122ca0.JPG" alt="АБК — объект 9" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/АБК/826af4bb-a922-49e9-8399-31e13493e24e.JPG" alt="АБК — объект 10" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/88d88c8a-a2d2-435a-b98e-46eac4b97e6b.JPG" alt="АБК — объект 11" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/94de6d9e-4d8d-4ffe-ab23-d6eb830443c7.JPG" alt="АБК — объект 12" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/94fe0ce5-1e0d-46f8-bcce-2e4e48089e11.JPG" alt="АБК — объект 13" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/969bd8c5-4261-4c83-a438-25076ef6879f.JPG" alt="АБК — объект 14" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/9ae01222-4c6a-4c71-bc7b-dc7ccae05954.JPG" alt="АБК — объект 15" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="gen" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/Генераторные/017C294C-9AF3-4638-ADC2-476CD4C3A7ED.JPG" alt="ДГУ — объект 1" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/Генераторные/02C32373-C168-4E51-AE88-79C9C0001058.JPG" alt="ДГУ — объект 2" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/0463AE7A-8A91-44AB-9099-EF2ED8919223.JPG" alt="ДГУ — объект 3" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/2BE1E005-3A0F-4D63-88CB-A369955A6A2A.JPG" alt="ДГУ — объект 4" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/2D4D055B-3A88-438B-8AB7-6388D8CB8C94.JPG" alt="ДГУ — объект 5" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/458DAF97-3386-4FE1-8CEE-B0C42AFC664A.JPG" alt="ДГУ — объект 6" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/475B3160-0677-4596-848A-544BEB831550.JPG" alt="ДГУ — объект 7" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/4F470B9C-BCB9-4B29-AB98-4C96E26ECC66.JPG" alt="ДГУ — объект 8" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/5CC0F37F-8EE5-4CA0-9530-AC27F5C01A0B.JPG" alt="ДГУ — объект 9" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/83934507-0570-4305-BAD7-CA9F218BFCCC.JPG" alt="ДГУ — объект 10" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/86371565-2804-4E82-803A-46606FBAE4F8.JPG" alt="ДГУ — объект 11" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/Генераторные/PHOTO-2020-11-05-10-03-22.jpg" alt="ДГУ — объект 12" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/PHOTO-2020-11-05-10-03-54.jpg" alt="ДГУ — объект 13" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/PHOTO-2022-08-08-18-14-09.jpg" alt="ДГУ — объект 14" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/капстроение/1.JPG" alt="Капстроение — объект 1" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/капстроение/10.JPG" alt="Капстроение — объект 2" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/11.JPG" alt="Капстроение — объект 3" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/капстроение/12.JPG" alt="Капстроение — объект 4" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/13.JPG" alt="Капстроение — объект 5" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/14.JPG" alt="Капстроение — объект 6" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/15.JPG" alt="Капстроение — объект 7" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/16.JPG" alt="Капстроение — объект 8" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/17.JPG" alt="Капстроение — объект 9" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/18.JPG" alt="Капстроение — объект 10" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/19.JPG" alt="Капстроение — объект 11" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/2.JPG" alt="Капстроение — объект 12" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/капстроение/3.JPG" alt="Капстроение — объект 13" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/4.JPG" alt="Капстроение — объект 14" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/капстроение/5.JPG" alt="Капстроение — объект 15" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/6.JPG" alt="Капстроение — объект 16" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/7.JPG" alt="Капстроение — объект 17" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/8.JPG" alt="Капстроение — объект 18" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/9.JPG" alt="Капстроение — объект 19" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="gruz" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/Грузоперевозки/1.jpg" alt="Грузоперевозки — объект 1" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Грузоперевозки</figcaption></figure>
			<figure class="esp-item" data-cat="gruz" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/Грузоперевозки/2.jpg" alt="Грузоперевозки — объект 2" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Грузоперевозки</figcaption></figure>
			<figure class="esp-item" data-cat="gruz" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/Грузоперевозки/3.jpg" alt="Грузоперевозки — объект 3" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Грузоперевозки</figcaption></figure>
			<figure class="esp-item" data-cat="gruz"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Грузоперевозки/4.jpg" alt="Грузоперевозки — объект 4" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Грузоперевозки</figcaption></figure>
			<figure class="esp-item" data-cat="gruz"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Грузоперевозки/5.avif" alt="Грузоперевозки — объект 5" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Грузоперевозки</figcaption></figure>
			<figure class="esp-item" data-cat="spec" data-best="1"><span class="ph"><img onload="this.classList.add('done')" src="/public/assets/image/services/Спецтехника/1.jpg" alt="Спецтехника — объект 1" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Спецтехника/2.jpg" alt="Спецтехника — объект 2" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Спецтехника/3.jpg" alt="Спецтехника — объект 3" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/B-EhBtJFvnnFRaO2wveNOZCClT-JPhW1zfPpCwBpbB1zf5c5eMiv04F-xdvtOP0VW4tQhgxr4EPrTjI6Hzj26rRH.jpg" alt="Спецтехника — объект 4" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/MAlDfjGfSf1Vs39sMgOgZT3FIVlZy2V3WKSzwzje3TjX1zoWTC-wApgBQ_8wp1iBUWxCBsuWkR9tMePU8A1TAVVg.jpg" alt="Спецтехника — объект 5" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/MNAEHRcw27qk0buSmkKQyg5y30hnigc-zXc68ylPagQIfUWmRJj2PI2ngGCso3cNr1Ar9H_JQ-Nm5IJd7VtriDPb.jpg" alt="Спецтехника — объект 6" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/TBiT7i6PKC59P2fly3WALvJkWWYPWJbSXWj13AyXfSRvSB2btvao6G4HxpIzQMtWxlCM_ZzG5sFpdzqKpkFVVUjP.jpg" alt="Спецтехника — объект 7" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/WaFjTiAvWrSSDsQfVMmZP8f_4Ypmss9_SmOCEwERQsPFGqv8usViLdYzhYVfY45UOX3bjGEDr8lXHiVZam0Gf4w1.jpg" alt="Спецтехника — объект 8" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/XK_gv3g8LacSwhlUCa7rOPn9wbbuX3mt104w9q_E5aEv7gYG4ZZfRvCI0aSGn7Bkm3X5FNnoX7Vh9GSfauPzynYX.jpg" alt="Спецтехника — объект 9" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/fnKAAgLhhTHPwjHV7dUN2l5tz9HIj-Iqi72N3unydGdhEnKq6gEQmazhbEtFUELLEOdBJlPaJztw7BzrsyncSlt5.jpg" alt="Спецтехника — объект 10" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/n_pdc5b2tgyjydxTAt_7ev1rBBFN26bwbq4796VCT5BHICCidmF-CnC59nv7sETn82ryDyaC4PqRG_nijl-iaTBU.jpg" alt="Спецтехника — объект 11" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="spec"><span class="ph"><img onload="this.classList.add('done')" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/МТЗ/vXxOynm7ZGLVJl41pflAYJ5V7pl8CoXnvGetQUIa3whJyGIAZggV4Tedcd_NydckYB2Y2emsEorcNLBGna_Zjphp.jpg" alt="Спецтехника — объект 12" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/logo.svg'"></span><figcaption class="esp-cap">Спецтехника</figcaption></figure>
			<figure class="esp-item" data-cat="video"><span class="ph vid"><video src="/public/assets/image/banner/buldozer.mp4" preload="metadata" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
			<figure class="esp-item" data-cat="video"><span class="ph vid"><video src="/public/assets/image/banner/tracktor.mp4" preload="metadata" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
			<figure class="esp-item" data-cat="video"><span class="ph vid"><video src="/public/assets/image/banner/schakman.mp4" preload="metadata" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
			<figure class="esp-item" data-cat="video"><span class="ph vid"><video src="/public/assets/image/banner/dostavka.MP4" preload="metadata" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
			<figure class="esp-item" data-cat="video"><span class="ph vid"><video src="/public/assets/image/banner/dostavkaKD.MP4" preload="metadata" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
		</main>
	<div class="esp-wrap">
		<div style="height:70px"></div>
	</div>
	<script>
	(function(){
		var btns = Array.prototype.slice.call(document.querySelectorAll('.esp-fbtn'));
		var cards = Array.prototype.slice.call(document.querySelectorAll('.esp-item'));
		var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		function apply(f){
			var i = 0;
			cards.forEach(function(c){
				var show = (f === 'all') ? c.hasAttribute('data-best') : c.getAttribute('data-cat') === f;
				c.hidden = !show;
				if (show) c.querySelectorAll('img[data-src]').forEach(function(img){ img.src = img.getAttribute('data-src'); img.removeAttribute('data-src'); });
				c.classList.remove('flash');
				if (show && !reduce){ c.style.animationDelay = Math.min(i * 35, 600) + 'ms'; i++; void c.offsetWidth; c.classList.add('flash'); }
			});
		}
		apply('all');
		btns.forEach(function(b){ b.addEventListener('click', function(){
			btns.forEach(function(x){ var on = x === b; x.classList.toggle('active', on); x.setAttribute('aria-pressed', on ? 'true' : 'false'); });
			apply(b.getAttribute('data-f'));
			document.getElementById('espGrid').scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
		});});
	})();
	</script>
	<script>
		document.querySelectorAll('.esp-item img, .esp-item video').forEach(el => {
			el.addEventListener('click', function () {
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

	<?php include_once __DIR__ . '/../components/footer.php'; ?>

</body>

</html>
