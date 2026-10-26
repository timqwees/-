<?php require_once __DIR__ . '/../../app/bootstrap.php'; ?>
<?php
// Реальные объекты — единый источник для разметки, карточек и текстовых блоков
$projects = file_exists(dirname(__DIR__, 2) . '/app/Data/projects.php')
	? (array)(require dirname(__DIR__, 2) . '/app/Data/projects.php')
	: [];
$base = rtrim((string)seo_config('base_url'), '/');
$portfolioTitle = 'Реализованные объекты — портфолио ФАВОРИТ-СТРОЙ-СНАБ';
$portfolioDesc = 'Портфолио ФАВОРИТ-СТРОЙ-СНАБ: общежития 9 000+ м² в Сочи, капремонт школ и спортивных объектов, оснащение 13 школ, навес на 100 машино-мест, поставка БПЛА.';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-latin.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-cyrillic.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/fonts/WixMadeforDisplay/WixMadeforDisplay-Variable.woff2" crossorigin><link rel="stylesheet" href="/public/assets/vendor/fonts/fonts.css">
	<style>
.esp-wrap{max-width:1440px;margin:0 auto;padding:0 clamp(16px,3vw,44px);}
.esp-top{padding-top:104px;}
.esp-over{margin:0;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#171310;}
.esp-h1{margin:18px 0 0;font-weight:600;text-transform:uppercase;letter-spacing:-.03em;line-height:.95;color:#111;font-size:clamp(52px,8.5vw,148px);}
.esp-h1 .it{display:block;font-family:Georgia,'Times New Roman',serif;font-style:italic;font-weight:400;text-transform:uppercase;padding-left:16%;}
.esp-filters{display:flex;flex-wrap:wrap;justify-content:center;gap:8px 30px;margin:36px 0 6px;}
.esp-fbtn{background:none;border:0;cursor:pointer;font-family:inherit;font-size:11.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#A8A29E;padding:6px 2px;text-underline-offset:7px;transition:color .28s ease,opacity .28s ease;}
.esp-fbtn:hover{color:#171310;}
.esp-fbtn.active{color:#171310;text-decoration:underline;text-decoration-thickness:2px;}
.esp-scroll{text-align:center;font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;margin:28px 0 34px;color:#171310;}
.esp-grid{columns:5 260px;column-gap:18px;max-width:2200px;margin:0 auto;padding:0 clamp(12px,2.5vw,40px);}
.esp-item{break-inside:avoid;margin:0 0 26px;cursor:zoom-in;}
.esp-item[hidden]{display:none;}
.esp-item .ph{display:block;overflow:hidden;background:#F5F5F4;position:relative;}
.esp-item img{width:100%;height:auto;display:block;transition:transform .5s ease;}
.esp-item video{width:100%;aspect-ratio:16/9;object-fit:cover;background:#F5F5F4;display:block;transition:transform .5s ease;}
.esp-item:hover img{transform:scale(1.035);}
.esp-item{content-visibility:auto;contain-intrinsic-size:320px;contain-intrinsic-size:auto 320px;}
.esp-item img{opacity:0;transition:opacity .45s ease,transform .5s ease;}
.esp-item img.done{opacity:1;}
.esp-item img.ph-load{background:linear-gradient(100deg,#F5F5F4 30%,#EDEBE7 50%,#F5F5F4 70%);background-size:200% 100%;animation:espShim 1.4s linear infinite;}
.esp-item img.ph-load.done{animation:none;background:none;}
@keyframes espShim{from{background-position:200% 0}to{background-position:-200% 0}}
.esp-cap{margin:9px 0 0;font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#111;}
.esp-play{position:absolute;right:12px;bottom:12px;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;font-size:13px;color:#111;}
@keyframes espIn{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}
.esp-item.flash{animation:espIn .5s cubic-bezier(.16,1,.3,1) both;}
@media(prefers-reduced-motion:reduce){.esp-item img{transition:none;}.esp-item.flash{animation:none;}}
</style>

	<title><?= htmlspecialchars($portfolioTitle, ENT_QUOTES) ?></title>
	<meta name="description" content="<?= htmlspecialchars($portfolioDesc, ENT_QUOTES) ?>">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#876b55">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/manifest.json" />

	<!-- Swiper CSS -->
		<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<!-- tailwind built into distCSS -->

	<!-- canonical -->
	<?= seo_canonical_tags() ?>
<?= seo_verify_tags() ?>

	<?= seo_og_tags($portfolioTitle, $portfolioDesc, '/public/assets/image/og/og-portfolio-1200x630.jpg') ?>

	<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
	<script type="application/ld+json">
	<?= json_encode([
		'@context' => 'https://schema.org',
		'@graph' => [
			[
				'@type' => 'CollectionPage',
				'@id' => $base . '/portfolio#webpage',
				'url' => $base . '/portfolio',
				'name' => $portfolioTitle,
				'description' => $portfolioDesc,
				'inLanguage' => 'ru-RU',
				'isPartOf' => ['@id' => $base . '/#website'],
				'about' => ['@id' => $base . '/#organization'],
				'breadcrumb' => ['@id' => $base . '/portfolio#breadcrumb'],
				'mainEntity' => ['@id' => $base . '/portfolio#itemlist'],
			],
			[
				'@type' => 'BreadcrumbList',
				'@id' => $base . '/portfolio#breadcrumb',
				'itemListElement' => [
					['@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $base . '/'],
					['@type' => 'ListItem', 'position' => 2, 'name' => 'Проекты', 'item' => $base . '/portfolio'],
				],
			],
			[
				'@type' => 'ItemList',
				'@id' => $base . '/portfolio#itemlist',
				'name' => 'Реализованные объекты ООО «Фаворит-Строй-Снаб»',
				'description' => 'Выполненные объекты: общежития в Сочи, капитальный ремонт школ и спортивных объектов, оснащение школ, навесы, административные здания, поставка БПЛА и оборудования.',
				'numberOfItems' => count($projects),
				'itemListOrder' => 'https://schema.org/ItemListUnordered',
				'itemListElement' => (static function (array $prs) use ($base): array {
					$items = [];
					$i = 0;
					foreach ($prs as $slug => $pr) {
						$i++;
						$items[] = [
							'@type' => 'ListItem',
							'position' => $i,
							'name' => (string)($pr['title'] ?? ''),
							'url' => $base . '/portfolio/' . (string)$slug,
						];
					}
					return $items;
				})($projects),
			],
		],
	], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS) ?>
	</script>
	<?= seo_analytics() ?>
	<?= seo_utm_script() ?>
	<?= seo_goals_script() ?>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<!-- tailwind built into distCSS -->
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<!-- Дизайн-система главной страницы (ProMaintenance) -->
	<link rel="stylesheet" href="/public/assets/distCSS/promaintenance.css?v=20261024">
	<style>
	.esp-wrap{max-width:1440px;margin:0 auto;padding:0 clamp(16px,3vw,44px);}
.esp-top{padding-top:104px;}
.esp-over{margin:0;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#171310;}
.esp-h1{margin:18px 0 0;font-weight:600;text-transform:uppercase;letter-spacing:-.03em;line-height:.95;color:#111;font-size:clamp(52px,8.5vw,148px);}
.esp-h1 .it{display:block;font-family:Georgia,'Times New Roman',serif;font-style:italic;font-weight:400;text-transform:uppercase;padding-left:16%;}
.esp-filters{display:flex;flex-wrap:wrap;justify-content:center;gap:8px 30px;margin:36px 0 6px;}
.esp-fbtn{background:none;border:0;cursor:pointer;font-family:inherit;font-size:11.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#A8A29E;padding:6px 2px;text-underline-offset:7px;transition:color .28s ease,opacity .28s ease;}
.esp-fbtn:hover{color:#171310;}
.esp-fbtn.active{color:#171310;text-decoration:underline;text-decoration-thickness:2px;}
.esp-scroll{text-align:center;font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;margin:28px 0 34px;color:#171310;}
.esp-grid{columns:5 260px;column-gap:18px;max-width:2200px;margin:0 auto;padding:0 clamp(12px,2.5vw,40px);}
.esp-item{break-inside:avoid;margin:0 0 26px;cursor:zoom-in;}
.esp-item[hidden]{display:none;}
.esp-item .ph{display:block;overflow:hidden;background:#F5F5F4;position:relative;}
.esp-item img{width:100%;height:auto;display:block;transition:transform .5s ease;}
.esp-item video{width:100%;aspect-ratio:16/9;object-fit:cover;background:#F5F5F4;display:block;transition:transform .5s ease;}
.esp-item:hover img{transform:scale(1.035);}
.esp-item{content-visibility:auto;contain-intrinsic-size:320px;contain-intrinsic-size:auto 320px;}
.esp-item img{opacity:0;transition:opacity .45s ease,transform .5s ease;}
.esp-item img.done{opacity:1;}
.esp-item img.ph-load{background:linear-gradient(100deg,#F5F5F4 30%,#EDEBE7 50%,#F5F5F4 70%);background-size:200% 100%;animation:espShim 1.4s linear infinite;}
.esp-item img.ph-load.done{animation:none;background:none;}
@keyframes espShim{from{background-position:200% 0}to{background-position:-200% 0}}
.esp-cap{margin:9px 0 0;font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#111;}
.esp-play{position:absolute;right:12px;bottom:12px;width:38px;height:38px;border-radius:50%;background:rgba(255,255,255,.92);display:grid;place-items:center;font-size:13px;color:#111;}
@keyframes espIn{from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);}}
.esp-item.flash{animation:espIn .5s cubic-bezier(.16,1,.3,1) both;}
@media(prefers-reduced-motion:reduce){.esp-item img{transition:none;}.esp-item.flash{animation:none;}}
/* ---------- Реализованные объекты по видам работ и «Компания в цифрах» ----------
   Оформление — по дизайн-системе главной страницы (ProMaintenance):
   бумажный фон, тонкие линии, крупная типографика, никаких «оранжевых» карточек. */
.pf-sec{scroll-margin-top:110px;}

/* Секция «Реализованные объекты по видам работ» */
.pf-works{padding-block:clamp(64px,7vw,120px) clamp(56px,6vw,100px);}
.pf-works .work-heading{display:grid;grid-template-columns:1.35fr 1fr;gap:clamp(30px,5vw,90px);align-items:end;}
.pf-works .work-heading h2{font-size:clamp(32px,4.2vw,64px);font-weight:450;}
.pf-works .work-heading p{color:var(--muted);max-width:420px;}
@media(max-width:900px){.pf-works .work-heading{grid-template-columns:1fr;gap:22px;}}
.pf-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:clamp(38px,4.5vw,72px) clamp(28px,3.5vw,64px);align-items:start;}
.pf-card-wide{grid-column:1/-1;}
@media(max-width:900px){.pf-grid{grid-template-columns:1fr;}.pf-card-wide{grid-column:auto;}}
.pf-card{min-width:0;border-top:1px solid var(--ink);padding-top:26px;}
.pf-card-head{display:grid;grid-template-columns:auto minmax(0,1fr);gap:6px 22px;align-items:start;padding-bottom:24px;border-bottom:1px solid var(--line);}
.pf-card-head>.eyebrow{color:var(--muted);padding-top:7px;}
.pf-card h3{margin:0;letter-spacing:-.04em;font-size:clamp(22px,2.2vw,34px);font-weight:500;line-height:1.1;color:var(--ink);}
.pf-count{display:block;color:#876b55;text-transform:uppercase;letter-spacing:.1em;font-size:9px;margin-top:12px;}
.pf-list{list-style:none;margin:0;padding:0;}
.pf-list.pf-list-3{columns:3;column-gap:40px;}
.pf-list.pf-list-3 li{break-inside:avoid;}
@media(max-width:1000px){.pf-list.pf-list-3{columns:2;}}
@media(max-width:640px){.pf-list.pf-list-3{columns:1;}}
.pf-list li{border-bottom:1px solid var(--line);font-size:14px;line-height:1.55;}
.pf-list li:last-child{border-bottom:0;}
.pf-list a,.pf-list span{display:flex;align-items:flex-start;gap:14px;padding:14px 0;text-decoration:none;color:var(--ink);transition:color .3s ease;}
.pf-list a::before,.pf-list span::before{content:"";flex:0 0 auto;width:5px;height:5px;border-radius:50%;background:var(--accent);margin-top:8px;transition:background .3s ease;}
.pf-list a:hover{color:var(--brand);}
.pf-list a:hover::before{background:var(--brand);}
.pf-list a svg{margin-left:auto;flex:0 0 auto;opacity:0;transform:translateX(-6px);transition:opacity .3s ease,transform .3s ease;margin-top:3px;}
.pf-list a:hover svg{opacity:1;transform:translateX(0);}

/* Секция «Компания в цифрах» — тёмная, как «Избранные проекты» на главной */
.pf-facts-sec{color:var(--paper);background:#1c1d1f;padding-block:clamp(64px,7vw,120px);}
.pf-facts-sec .section-topline{border-color:rgba(255,255,255,.2);}
.pf-facts-sec .work-heading h2{font-size:clamp(34px,4.4vw,68px);font-weight:450;}
.pf-facts-sec .work-heading p{color:rgba(240,239,237,.62);}
.pf-facts-scroll{overflow-x:auto;margin-top:4px;}
.pf-facts{width:100%;min-width:680px;border-collapse:collapse;}
.pf-facts thead th{text-align:left;padding:0 24px 16px 0;border-bottom:1px solid rgba(255,255,255,.28);color:rgba(240,239,237,.55);font-size:9px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;white-space:nowrap;}
.pf-facts tbody th,.pf-facts tbody td{padding:20px 24px 20px 0;border-bottom:1px solid rgba(255,255,255,.12);vertical-align:top;text-align:left;font-size:14px;line-height:1.6;font-weight:400;color:rgba(240,239,237,.8);}
.pf-facts tbody th{width:32%;}
.pf-facts .pf-fact-val{width:30%;color:var(--paper);font-size:15px;font-weight:500;}
.pf-facts .pf-fact-src{color:rgba(240,239,237,.45);font-size:12px;line-height:1.55;}
.pf-facts tbody tr:last-child th,.pf-facts tbody tr:last-child td{border-bottom:0;}
/* На узких экранах таблица превращается в список карточек — без горизонтальной прокрутки */
@media(max-width:640px){
.pf-facts{min-width:0;}
.pf-facts thead{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;}
.pf-facts tbody tr{display:block;padding:20px 0;border-bottom:1px solid rgba(255,255,255,.12);}
.pf-facts tbody th,.pf-facts tbody td{display:block;width:auto;padding:0;border:0;}
.pf-facts tbody th{font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:rgba(240,239,237,.5);margin-bottom:8px;}
.pf-facts .pf-fact-val{font-size:15px;margin-bottom:6px;}
.pf-facts .pf-fact-src{font-size:11px;}
.pf-facts tbody tr:last-child{border-bottom:0;}
}

/* Блок о географии работ и заказчике */
.pf-note-wrap{padding-block:clamp(48px,5vw,90px);}
.pf-note{display:grid;grid-template-columns:auto minmax(0,1fr) auto;align-items:center;gap:clamp(18px,2.4vw,34px);border-top:1px solid var(--line);padding-top:clamp(26px,3vw,40px);}
.pf-note-ico{flex:0 0 auto;width:44px;height:44px;border:1px solid var(--line);border-radius:var(--radius-control);color:var(--ink);display:grid;place-items:center;}
.pf-note p{margin:0;max-width:64ch;font-size:16px;line-height:1.6;letter-spacing:-.015em;color:var(--ink);}
.pf-note .button{white-space:nowrap;}
@media(max-width:860px){.pf-note{grid-template-columns:auto minmax(0,1fr);align-items:start;}.pf-note p{font-size:15px;}.pf-note .button{grid-column:1/-1;justify-content:space-between;}}
  /* ---------- Превью объекта (лайтбокс) ---------- */
  .esp-lb{position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;padding:clamp(16px,3vw,48px);background:rgba(16,16,18,.94);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);animation:espLbFade .22s ease both;}
  .esp-lb[hidden]{display:none;}
  @keyframes espLbFade{from{opacity:0}to{opacity:1}}
  .esp-lb-figure{margin:0;max-width:min(1280px,94vw);display:flex;flex-direction:column;gap:14px;align-items:center;}
  .esp-lb-stage{display:flex;max-width:100%;max-height:80vh;}
  .esp-lb-stage img,.esp-lb-stage video{display:block;max-width:100%;max-height:80vh;width:auto;height:auto;object-fit:contain;border-radius:10px;background:#101012;box-shadow:0 30px 90px rgba(0,0,0,.6);}
  .esp-lb-stage video{background:#000;}
  .esp-lb-cap{color:rgba(240,239,237,.55);letter-spacing:.12em;text-transform:uppercase;font-size:10px;display:flex;align-items:center;gap:14px;}
  .esp-lb-cap b{color:#f0efed;font-weight:500;}
  .esp-lb-cap i{width:22px;height:1px;background:rgba(240,239,237,.35);font-style:normal;}
  .esp-lb-btn{position:fixed;display:grid;place-items:center;border-radius:50%;border:1px solid rgba(240,239,237,.28);background:rgba(240,239,237,.06);color:#f0efed;width:46px;height:46px;cursor:pointer;transition:background .25s ease,border-color .25s ease,transform .25s ease;}
  .esp-lb-btn:hover{background:rgba(240,239,237,.18);border-color:rgba(240,239,237,.55);}
  .esp-lb-btn:focus-visible{outline:none;background:rgba(240,239,237,.18);border-color:rgba(240,239,237,.75);}
  .esp-lb-stage img,.esp-lb-stage video{animation:espLbImg .28s ease both;}
  @keyframes espLbImg{from{opacity:0;transform:scale(.995)}to{opacity:1;transform:none}}
  .esp-lb-close{top:clamp(14px,2.2vw,30px);right:clamp(14px,2.2vw,30px);}
  .esp-lb-prev{left:clamp(10px,2vw,28px);top:50%;transform:translateY(-50%);}
  .esp-lb-next{right:clamp(10px,2vw,28px);top:50%;transform:translateY(-50%);}
  .esp-lb-prev:hover{transform:translateY(-50%) translateX(-2px);}
  .esp-lb-next:hover{transform:translateY(-50%) translateX(2px);}
  body.esp-lb-open{overflow:hidden;}
  @media (width <= 700px){.esp-lb-prev,.esp-lb-next{display:none;}.esp-lb-stage img,.esp-lb-stage video{max-height:72vh;}}
  .fss-breadcrumbs { margin-bottom: 20px; }
  .fss-bc-list { display: flex; flex-wrap: wrap; gap: 6px; list-style: none; margin: 0; padding: 0; font-size: 13px; color: #6b7078; }
  .fss-bc-item { display: flex; align-items: center; }
  .fss-bc-item:not(:last-child)::after { content: "/"; margin-left: 6px; color: #b0b3b8; }
  .fss-bc-link { color: #6b7078; text-decoration: none; transition: color .2s; }
  .fss-bc-link:hover { color: #876b55; }
  .fss-bc-current { color: #17191d; font-weight: 600; }
</style>
</head>

<body class="font-sans text-gray-800" data-page="inner">
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
		<button class="esp-fbtn" type="button" data-f="video" aria-pressed="false">Видео</button>
		</div>
		<p class="esp-scroll">Листайте вниз</p>
	</div>
		<main class="esp-grid" id="espGrid">
			<figure class="esp-item" data-cat="abk" data-best="1" id="obj-1"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/04d8caee-f083-42f8-8be2-64e7e461cace.JPG" width="960" height="1280" alt="АБК — объект 1" title="АБК — объект 1" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-2"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/05fcff5c-1d8e-4767-be29-a2e6b2bca7ab.JPG" width="960" height="1280" alt="АБК — объект 2" title="АБК — объект 2" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" data-best="1" id="obj-3"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/10fd99d6-066e-4962-8ce3-4e49f690dd4c.JPG" width="1024" height="768" alt="АБК — объект 3" title="АБК — объект 3" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-4"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/412f4b10-4e04-4e24-a809-f4b98c451f9e.JPG" width="1024" height="768" alt="АБК — объект 4" title="АБК — объект 4" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-5"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/4eb40ff7-527a-4f7e-9a20-d1bfeb6601b2.JPG" width="1024" height="768" alt="АБК — объект 5" title="АБК — объект 5" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" data-best="1" id="obj-6"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/59f1ad99-f1e5-4640-807e-ca4326725fc4.JPG" width="960" height="1280" alt="АБК — объект 6" title="АБК — объект 6" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-7"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/5de688c3-9bf7-4e3b-b818-48b8b4f9b31a.JPG" width="768" height="1024" alt="АБК — объект 7" title="АБК — объект 7" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-8"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/6363b4d2-80ef-4af5-afad-60838f329be3.JPG" width="768" height="1024" alt="АБК — объект 8" title="АБК — объект 8" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-9"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/6c7d6515-85d3-4a04-b35e-8d6376122ca0.JPG" width="1024" height="768" alt="АБК — объект 9" title="АБК — объект 9" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" data-best="1" id="obj-10"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/826af4bb-a922-49e9-8399-31e13493e24e.JPG" width="1024" height="768" alt="АБК — объект 10" title="АБК — объект 10" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-11"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/88d88c8a-a2d2-435a-b98e-46eac4b97e6b.JPG" width="1024" height="768" alt="АБК — объект 11" title="АБК — объект 11" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-12"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/94de6d9e-4d8d-4ffe-ab23-d6eb830443c7.JPG" width="1024" height="768" alt="АБК — объект 12" title="АБК — объект 12" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-13"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/94fe0ce5-1e0d-46f8-bcce-2e4e48089e11.JPG" width="1280" height="960" alt="АБК — объект 13" title="АБК — объект 13" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-14"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/969bd8c5-4261-4c83-a438-25076ef6879f.JPG" width="960" height="1280" alt="АБК — объект 14" title="АБК — объект 14" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="abk" id="obj-15"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/АБК/9ae01222-4c6a-4c71-bc7b-dc7ccae05954.JPG" width="1024" height="768" alt="АБК — объект 15" title="АБК — объект 15" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">АБК</figcaption></figure>
			<figure class="esp-item" data-cat="gen" data-best="1" id="obj-16"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/017C294C-9AF3-4638-ADC2-476CD4C3A7ED.JPG" width="900" height="1600" alt="ДГУ — объект 1" title="ДГУ — объект 1" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" data-best="1" id="obj-17"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/02C32373-C168-4E51-AE88-79C9C0001058.JPG" width="900" height="1600" alt="ДГУ — объект 2" title="ДГУ — объект 2" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-18"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/0463AE7A-8A91-44AB-9099-EF2ED8919223.JPG" width="900" height="1600" alt="ДГУ — объект 3" title="ДГУ — объект 3" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-19"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/2BE1E005-3A0F-4D63-88CB-A369955A6A2A.JPG" width="900" height="1600" alt="ДГУ — объект 4" title="ДГУ — объект 4" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-20"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/2D4D055B-3A88-438B-8AB7-6388D8CB8C94.JPG" width="900" height="1600" alt="ДГУ — объект 5" title="ДГУ — объект 5" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-21"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/458DAF97-3386-4FE1-8CEE-B0C42AFC664A.JPG" width="900" height="1600" alt="ДГУ — объект 6" title="ДГУ — объект 6" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-22"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/475B3160-0677-4596-848A-544BEB831550.JPG" width="900" height="1600" alt="ДГУ — объект 7" title="ДГУ — объект 7" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-23"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/4F470B9C-BCB9-4B29-AB98-4C96E26ECC66.JPG" width="900" height="1600" alt="ДГУ — объект 8" title="ДГУ — объект 8" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-24"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/5CC0F37F-8EE5-4CA0-9530-AC27F5C01A0B.JPG" width="900" height="1600" alt="ДГУ — объект 9" title="ДГУ — объект 9" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-25"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/83934507-0570-4305-BAD7-CA9F218BFCCC.JPG" width="900" height="1600" alt="ДГУ — объект 10" title="ДГУ — объект 10" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-26"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/86371565-2804-4E82-803A-46606FBAE4F8.JPG" width="900" height="1600" alt="ДГУ — объект 11" title="ДГУ — объект 11" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" data-best="1" id="obj-27"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/PHOTO-2020-11-05-10-03-22.jpg" width="720" height="1280" alt="ДГУ — объект 12" title="ДГУ — объект 12" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-28"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/PHOTO-2020-11-05-10-03-54.jpg" width="720" height="1280" alt="ДГУ — объект 13" title="ДГУ — объект 13" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="gen" id="obj-29"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/Генераторные/PHOTO-2022-08-08-18-14-09.jpg" width="1280" height="960" alt="ДГУ — объект 14" title="ДГУ — объект 14" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">ДГУ</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1" id="obj-30"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/1.JPG" width="820" height="461" alt="Капстроение — объект 1" title="Капстроение — объект 1" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1" id="obj-31"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/10.JPG" width="1170" height="778" alt="Капстроение — объект 2" title="Капстроение — объект 2" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-32"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/11.JPG" width="1170" height="778" alt="Капстроение — объект 3" title="Капстроение — объект 3" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1" id="obj-33"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/12.JPG" width="820" height="461" alt="Капстроение — объект 4" title="Капстроение — объект 4" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-34"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/13.JPG" width="1170" height="778" alt="Капстроение — объект 5" title="Капстроение — объект 5" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-35"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/14.JPG" width="820" height="461" alt="Капстроение — объект 6" title="Капстроение — объект 6" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-36"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/15.JPG" width="1170" height="778" alt="Капстроение — объект 7" title="Капстроение — объект 7" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-37"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/16.JPG" width="820" height="461" alt="Капстроение — объект 8" title="Капстроение — объект 8" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-38"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/17.JPG" width="1170" height="778" alt="Капстроение — объект 9" title="Капстроение — объект 9" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-39"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/18.JPG" width="820" height="461" alt="Капстроение — объект 10" title="Капстроение — объект 10" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-40"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/19.JPG" width="1170" height="778" alt="Капстроение — объект 11" title="Капстроение — объект 11" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-41"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/2.JPG" width="1170" height="778" alt="Капстроение — объект 12" title="Капстроение — объект 12" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1" id="obj-42"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/3.JPG" width="820" height="461" alt="Капстроение — объект 13" title="Капстроение — объект 13" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-43"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/4.JPG" width="820" height="461" alt="Капстроение — объект 14" title="Капстроение — объект 14" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" data-best="1" id="obj-44"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/5.JPG" width="820" height="461" alt="Капстроение — объект 15" title="Капстроение — объект 15" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-45"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/6.JPG" width="820" height="461" alt="Капстроение — объект 16" title="Капстроение — объект 16" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-46"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/7.JPG" width="1170" height="778" alt="Капстроение — объект 17" title="Капстроение — объект 17" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-47"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/8.JPG" width="1170" height="778" alt="Капстроение — объект 18" title="Капстроение — объект 18" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="kap" id="obj-48"><span class="ph"><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-src="/public/assets/image/services/капстроение/9.JPG" width="1170" height="778" alt="Капстроение — объект 19" title="Капстроение — объект 19" loading="lazy" decoding="async" onerror="this.onerror=null;this.classList.add('done');this.src='/public/assets/image/logo/favicon/logo-64.png'"></span><figcaption class="esp-cap">Капстроение</figcaption></figure>
			<figure class="esp-item" data-cat="video" id="obj-66"><span class="ph vid"><video data-src="/public/assets/image/banner/buldozer.mp4" preload="none" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
			<figure class="esp-item" data-cat="video" id="obj-67"><span class="ph vid"><video data-src="/public/assets/image/banner/tracktor.mp4" preload="none" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
			<figure class="esp-item" data-cat="video" id="obj-68"><span class="ph vid"><video data-src="/public/assets/image/banner/schakman.mp4" preload="none" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
			<figure class="esp-item" data-cat="video" id="obj-69"><span class="ph vid"><video data-src="/public/assets/image/banner/dostavka.MP4" preload="none" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
			<figure class="esp-item" data-cat="video" id="obj-70"><span class="ph vid"><video data-src="/public/assets/image/banner/dostavkaKD.MP4" preload="none" muted playsinline></video><span class="esp-play" aria-hidden="true">▶</span></span><figcaption class="esp-cap">Видео</figcaption></figure>
		</main>
	<div class="esp-wrap">
		<div style="height:70px"></div>
	</div>

	<!-- РЕАЛИЗОВАННЫЕ ОБЪЕКТЫ ПО ВИДАМ РАБОТ -->
	<section id="works" class="pf-sec pf-works section-shell">
		<div class="section-topline">
			<p class="eyebrow">01 / Реализованные объекты</p>
			<span class="eyebrow">Москва, область и вся Россия</span>
		</div>
		<div class="work-heading">
			<h2 data-reveal>Реализованные объекты<br>по видам работ</h2>
			<p>Перечень фактически выполненных работ ООО «Фаворит-Строй-Снаб»: капитальный ремонт социальных объектов, быстровозводимые здания, поставка оборудования и техники.</p>
		</div>

		<div class="pf-grid">
			<?php
			// Группы работ. У части объектов есть детальные страницы /portfolio/{slug} —
			// по ним ставим ссылку, остальные — обычные строки перечня.
			$worksGroups = [
				[
					'title' => 'Капитальный ремонт социальных объектов',
					'count' => '16 объектов',
					'items' => [
						['ГБОУ «Центр образования г. Гудермес»', 'kapremont-gudermes'],
						['МБОУ «СОШ №2» г. Аргун', 'kapremont-argun'],
						['МБОУ «Илсхан-Юртовская ОШ»', null],
						['МБОУ «СОШ №1 с. Чечен-Аул»', 'kapremont-chechen-aul'],
						['Спортивный зал МБОУ «СОШ с. Балансу им. Р. Байхаджиева»', null],
						['Спортивный зал МБОУ «СОШ с. Айти-Мохк»', null],
						['Спортивный зал МБОУ «ООШ с. Мескеты»', null],
						['Дворец спорта «Шахтёр» МБУ г. Гуково «Спортивная школа Прометей»', 'dvorets-sporta-shahter'],
						['МБОУ «СОШ №2 с. Ачхой-Мартан»', 'kapremont-achhoy-martan'],
						['МБОУ «Старогладовская СОШ»', null],
						['МБОУ «СОШ №1 с.п. Гвардейское»', null],
						['МБОУ «СОШ с.п. Комарова»', null],
						['МБОУ ООШ «с. Нохчи-Келой»', null],
						['МБОУ НОШ с. Ярыш-Марды', null],
						['МБОУ «СОШ №1 г. Шатура»', 'kapremont-shatura'],
						['МБОУ «СОШ с. Лаха-Варанды Шатойского муниципального района»', null],
					],
				],
				[
					'title' => 'Быстровозводимые здания и инженерное оснащение',
					'count' => '4 объекта',
					'items' => [
						['Комплекс общежитий 9 000+ м², Адлерский район г. Сочи', 'obshchegiya-adler-sochi'],
						['Комплексное оснащение 13 вновь построенных школ', 'osnashchenie-shkol'],
						['Административное здание с инженерными системами и мебелью', 'administrativnoe-zdanie'],
						['Навесы для хранения и обслуживания автомобильной техники на 100 машино-мест', 'navesy-100-mest'],
					],
				],
				[
					'title' => 'Поставка оборудования и техники',
					'count' => '3 поставки',
					'items' => [
						['Беспилотные летательные аппараты, комплектующие и системы КИТ', 'bpla-kit'],
						['Ноутбуки для ГБОУ «Центр дистанционного обучения»', 'noutbuki-cdo'],
						['Мебель всех типов: ученическая, офисная, лабораторная', null],
					],
				],
			];
			foreach ($worksGroups as $gi => $g): ?>
			<article class="pf-card<?= count($g['items']) > 8 ? ' pf-card-wide' : '' ?>">
				<header class="pf-card-head">
					<span class="eyebrow"><?= str_pad((string)($gi + 1), 2, '0', STR_PAD_LEFT) ?></span>
					<div>
						<h3><?= htmlspecialchars($g['title'], ENT_QUOTES) ?></h3>
						<span class="pf-count"><?= htmlspecialchars($g['count'], ENT_QUOTES) ?></span>
					</div>
				</header>
				<ul class="pf-list<?= count($g['items']) > 8 ? ' pf-list-3' : '' ?>">
					<?php foreach ($g['items'] as [$label, $slug]): ?>
					<li>
						<?php if ($slug !== null && isset($projects[$slug])): ?>
						<a href="/portfolio/<?= htmlspecialchars($slug, ENT_QUOTES) ?>" title="<?= htmlspecialchars($label, ENT_QUOTES) ?>"><?= htmlspecialchars($label, ENT_QUOTES) ?>
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
						<?php else: ?>
						<span><?= htmlspecialchars($label, ENT_QUOTES) ?></span>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</article>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- КОМПАНИЯ В ЦИФРАХ — ПРОВЕРЯЕМЫЕ ФАКТЫ -->
	<section class="pf-sec pf-facts-sec section-shell" id="facts">
		<div class="section-topline">
			<p class="eyebrow">02 / Проверяемые факты</p>
			<span class="eyebrow">Каждая цифра с указанием источника</span>
		</div>
		<div class="work-heading">
			<h2 data-reveal>Компания в цифрах</h2>
			<p>Показатели подтверждены бухгалтерской отчётностью ФНС, открытыми реестрами контрактов и портфолио компании. Каждая строка — с источником.</p>
		</div>
		<div class="pf-facts-scroll">
			<table class="pf-facts">
				<caption class="sr-only">Проверяемые факты о компании ООО «Фаворит-Строй-Снаб»</caption>
				<thead>
					<tr>
						<th scope="col">Показатель</th>
						<th scope="col">Значение</th>
						<th scope="col">Источник</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$facts = [
						['Объектов капитального ремонта', seo_config('kapremont_objects') . ' (школы, спортивные залы, дворец спорта)', 'Портфолио, раздел «Капитальный ремонт»'],
						['Школ оснащено по стандартам Минпросвещения РФ', seo_config('schools_equipped'), 'Портфолио, кейс «Комплексное оснащение»'],
						['Общежития в Сочи (Адлерский район)', seo_config('sochi_area'), 'Портфолио, кейс «Комплекс общежитий»'],
						['Навес для хранения автотехники', seo_config('naves_capacity'), 'Портфолио, кейс «Навесы»'],
						['Выручка за 2025 год', '521,7 млн ₽', 'Бухгалтерская отчётность ФНС (ЕГРЮЛ)'],
						['Госзакупки', '17 контрактов на 815,4 млн ₽', 'Открытые реестры контрактов'],
						['На рынке', 'с ' . seo_config('founded_market') . ' года (ООО — с ' . seo_config('founded') . ' года)', 'ЕГРЮЛ, ИНН ' . seo_config('inn') . ' / ОГРН ' . seo_config('ogrn')],
						['Гарантия на несущие конструкции', seo_config('guarantee_years') . ' лет', 'Договор и гарантийные обязательства'],
						['Срок изготовления и монтажа БВЗ', 'от ' . seo_config('deadline_days') . ' дней', 'Нормативы компании'],
						['Расчёт сметы', seo_config('price_response_minutes') . ' минут', 'Нормативы компании'],
						['Текущий заказчик', 'Федеральная служба войск национальной гвардии РФ (Росгвардия)', 'Договорные отношения'],
					];
					foreach ($facts as $f): ?>
					<tr>
						<th scope="row"><?= htmlspecialchars($f[0], ENT_QUOTES) ?></th>
						<td class="pf-fact-val"><?= htmlspecialchars($f[1], ENT_QUOTES) ?></td>
						<td class="pf-fact-src"><?= htmlspecialchars($f[2], ENT_QUOTES) ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>

	<!-- ГЕОГРАФИЯ РАБОТ И ЗАКАЗЧИК -->
	<div class="pf-note-wrap section-shell">
		<div class="pf-note">
			<span class="pf-note-ico" aria-hidden="true">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" focusable="false">
					<path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6l7-3z"></path>
					<path d="M9 12l2 2 4-4"></path>
				</svg>
			</span>
			<p>Работы выполняются в Москве, Московской области и по всей России — в том числе для силовых и государственных структур. Компания сотрудничает с Федеральной службой войск национальной гвардии Российской Федерации.</p>
			<a class="button" href="/#contact" title="Получить смету за 15 минут" data-goal="portfolio_estimate">Получить смету за 15 минут
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M7 7h10v10"></path>
					<path d="M7 17 17 7"></path>
				</svg>
			</a>
		</div>
	</div>
	<script>
	(function(){
		var btns = Array.prototype.slice.call(document.querySelectorAll('.esp-fbtn'));
		var cards = Array.prototype.slice.call(document.querySelectorAll('.esp-item'));
		var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var hasIO = 'IntersectionObserver' in window;

		// Ленивая загрузка: реальный src подставляется только когда карточка близко к вьюпорту
		function loadCard(card){
			var imgs = card.querySelectorAll('img[data-src]');
			Array.prototype.forEach.call(imgs, function(img){
				var src = img.getAttribute('data-src');
				if (!src) return;
				img.removeAttribute('data-src');
				img.classList.add('ph-load');
				var done = function(){ img.classList.add('done'); };
				img.addEventListener('load', done);
				img.addEventListener('error', done);
				img.src = src;
				if (img.complete) done();
			});
			// видео весит 30–66 МБ — подключаем только когда карточка дошла до экрана
			var vids = card.querySelectorAll('video[data-src]');
			Array.prototype.forEach.call(vids, function(v){
				var src = v.getAttribute('data-src');
				if (!src) return;
				v.removeAttribute('data-src');
				v.preload = 'metadata';
				v.src = src;
			});
		}

		function reveal(card, delay){
			if (card.getAttribute('data-revealed') === '1') return;
			card.setAttribute('data-revealed','1');
			loadCard(card);
			if (reduce) return;
			card.classList.remove('flash');
			card.style.animationDelay = (delay || 0) + 'ms';
			void card.offsetWidth;
			card.classList.add('flash');
		}

		function inView(card, pad){
			var r = card.getBoundingClientRect();
			return r.top < (window.innerHeight + pad) && r.bottom > -pad;
		}

		if (hasIO) {
			var io = new IntersectionObserver(function(entries){
				entries.forEach(function(en){
					if (en.isIntersecting) reveal(en.target, 0);
				});
			}, {rootMargin:'400px 0px', threshold:0.01});
			cards.forEach(function(c){ io.observe(c); });
		}

		function apply(f){
			cards.forEach(function(c){
				var show = (f === 'all') ? c.hasAttribute('data-best') : c.getAttribute('data-cat') === f;
				c.hidden = !show;
				if (show) c.removeAttribute('data-revealed');
			});
			// кадры в зоне видимости появляются сразу, остальные — по мере прокрутки
			requestAnimationFrame(function(){
				var vis = cards.filter(function(c){ return !c.hidden; });
				var i = 0;
				vis.forEach(function(c){
					if (!hasIO || inView(c, 400)) reveal(c, Math.min(i * 40, 480));
					i++;
				});
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
	(function () {
		"use strict";
		var grid = document.getElementById('espGrid');
		if (!grid) return;

		var lb = null, stage = null, cap = null, list = [], index = 0, lastFocus = null;

		function icon(path, size) {
			return '<svg viewBox="0 0 24 24" width="' + size + '" height="' + size + '" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' + path + '"></path></svg>';
		}

		function build() {
			if (lb) return;
			lb = document.createElement('div');
			lb.className = 'esp-lb';
			lb.setAttribute('role', 'dialog');
			lb.setAttribute('aria-modal', 'true');
			lb.hidden = true;
			lb.innerHTML =
				'<button class="esp-lb-btn esp-lb-close" type="button" aria-label="Закрыть">' + icon('M6 6l12 12M18 6L6 18', 18) + '</button>' +
				'<button class="esp-lb-btn esp-lb-prev" type="button" aria-label="Предыдущий объект">' + icon('M15 5l-7 7 7 7', 20) + '</button>' +
				'<button class="esp-lb-btn esp-lb-next" type="button" aria-label="Следующий объект">' + icon('M9 5l7 7-7 7', 20) + '</button>' +
				'<figure class="esp-lb-figure"><div class="esp-lb-stage"></div><figcaption class="esp-lb-cap"></figcaption></figure>';
			document.body.appendChild(lb);
			stage = lb.querySelector('.esp-lb-stage');
			cap = lb.querySelector('.esp-lb-cap');
			lb.querySelector('.esp-lb-close').addEventListener('click', close);
			lb.querySelector('.esp-lb-prev').addEventListener('click', function () { step(-1); });
			lb.querySelector('.esp-lb-next').addEventListener('click', function () { step(1); });
			lb.addEventListener('click', function (e) { if (e.target === lb) close(); });
			document.addEventListener('keydown', function (e) {
				if (!lb || lb.hidden) return;
				if (e.key === 'Escape') close();
				else if (e.key === 'ArrowLeft') step(-1);
				else if (e.key === 'ArrowRight') step(1);
			});
		}

		function pad(n) { return (n < 10 ? '0' : '') + n; }

		function visible() {
			return [].slice.call(grid.querySelectorAll('.esp-item')).filter(function (it) { return !it.hidden; });
		}

		function show(i) {
			if (!list.length) return;
			index = (i + list.length) % list.length;
			var item = list[index];
			var media = item.querySelector('img, video');
			if (!media) return;
			// при lazy-загрузке в src лежит плейсхолдер — берём оригинал из data-src
			var src = media.getAttribute('data-src') || media.getAttribute('src') || '';
			stage.innerHTML = '';
			if (media.tagName === 'VIDEO') {
				var v = document.createElement('video');
				v.src = src;
				v.controls = true;
				v.autoplay = true;
				v.playsInline = true;
				stage.appendChild(v);
			} else {
				var im = document.createElement('img');
				im.src = src;
				im.alt = media.getAttribute('alt') || '';
				stage.appendChild(im);
			}
			var label = item.querySelector('.esp-cap');
			cap.innerHTML = '<b>' + (label ? label.textContent.trim() : '') + '</b><i></i>' + pad(index + 1) + ' / ' + pad(list.length);
		}

		function open(item) {
			build();
			list = visible();
			lastFocus = document.activeElement;
			lb.hidden = false;
			document.body.classList.add('esp-lb-open');
			var i = list.indexOf(item);
			show(i < 0 ? 0 : i);
			lb.querySelector('.esp-lb-close').focus();
		}

		function close() {
			if (!lb) return;
			lb.hidden = true;
			document.body.classList.remove('esp-lb-open');
			stage.innerHTML = '';
			if (lastFocus && lastFocus.focus) lastFocus.focus();
		}

		function step(dir) { show(index + dir); }

		grid.addEventListener('click', function (e) {
			var media = e.target.closest ? e.target.closest('.esp-item img, .esp-item video') : null;
			if (!media) return;
			var item = media.closest('.esp-item');
			if (!item) return;
			e.preventDefault();
			open(item);
		});
	})();
	</script>

	<?php include_once __DIR__ . '/../components/footer.php'; ?>

</body>

</html>
