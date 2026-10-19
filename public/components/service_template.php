<?php
/**
 * ЕДИНЫЙ шаблон страниц услуг (1 услуга = 1 кластер).
 * Используется всеми public/pages/services/<slug>/index.php через тонкий загрузчик:
 *   <?php $fss_service_slug = 'angar'; require __DIR__ . '/../../../components/service_template.php';
 * Данные: app/Data/services.php (title/service_type/description/price_from/queries/faq),
 * цифры и контакты: app/Config/site.php. Фото — только существующие файлы.
 */
require_once __DIR__ . '/../../app/bootstrap.php';
$componentMailPath = __DIR__ . '/../../app/Mail/component_mail.php';
if (file_exists($componentMailPath)) {
	include_once $componentMailPath;
}

$slug = $fss_service_slug ?? ($_GET['service'] ?? '');
$services = seo_services();
if (!isset($services[$slug])) {
	\App\Models\Router\Routes::error_404('/services/' . $slug);
	return;
}
$svc = $services[$slug];
$geoKey = (string)($GLOBALS['fss_service_geo'] ?? '');
$geoAll = function_exists('seo_geo') ? seo_geo() : ['geo' => [], 'priority_services' => []];
$geo = ($geoKey !== '' && isset($geoAll['geo'][$geoKey])) ? $geoAll['geo'][$geoKey] : null;
$geoName = (string)($geo['name'] ?? '');
$geoIn = (string)($geo['in'] ?? $geoName);
$svcType = (string)($svc['service_type'] ?? $svc['title']);
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$phone = (string)seo_config('phone_display');
$phoneHref = preg_replace('/\D/', '', (string)seo_config('phone'));
$deadline = (string)seo_config('deadline_days');
$guarantee = (string)seo_config('guarantee_years');
$objects = (string)seo_config('objects');

// --- фотопулы (только файлы, существующие на диске) ---
$KAP = array_map(function ($i) { return 'капстроение/' . $i . '.JPG'; }, range(1, 19));
$ABK = ['АБК/04d8caee-f083-42f8-8be2-64e7e461cace.JPG','АБК/05fcff5c-1d8e-4767-be29-a2e6b2bca7ab.JPG','АБК/10fd99d6-066e-4962-8ce3-4e49f690dd4c.JPG','АБК/412f4b10-4e04-4e24-a809-f4b98c451f9e.JPG','АБК/4eb40ff7-527a-4f7e-9a20-d1bfeb6601b2.JPG','АБК/59f1ad99-f1e5-4640-807e-ca4326725fc4.JPG','АБК/5de688c3-9bf7-4e3b-b818-48b8b4f9b31a.JPG','АБК/6363b4d2-80ef-4af5-afad-60838f329be3.JPG','АБК/6c7d6515-85d3-4a04-b35e-8d6376122ca0.JPG','АБК/826af4bb-a922-49e9-8399-31e13493e24e.JPG','АБК/88d88c8a-a2d2-435a-b98e-46eac4b97e6b.JPG','АБК/94de6d9e-4d8d-4ffe-ab23-d6eb830443c7.JPG','АБК/94fe0ce5-1e0d-46f8-bcce-2e4e48089e11.JPG','АБК/969bd8c5-4261-4c83-a438-25076ef6879f.JPG','АБК/9ae01222-4c6a-4c71-bc7b-dc7ccae05954.JPG'];
$GEN = ['Генераторные/017C294C-9AF3-4638-ADC2-476CD4C3A7ED.JPG','Генераторные/02C32373-C168-4E51-AE88-79C9C0001058.JPG','Генераторные/0463AE7A-8A91-44AB-9099-EF2ED8919223.JPG','Генераторные/2BE1E005-3A0F-4D63-88CB-A369955A6A2A.JPG','Генераторные/2D4D055B-3A88-438B-8AB7-6388D8CB8C94.JPG','Генераторные/458DAF97-3386-4FE1-8CEE-B0C42AFC664A.JPG','Генераторные/475B3160-0677-4596-848A-544BEB831550.JPG','Генераторные/4F470B9C-BCB9-4B29-AB98-4C96E26ECC66.JPG','Генераторные/5CC0F37F-8EE5-4CA0-9530-AC27F5C01A0B.JPG','Генераторные/83934507-0570-4305-BAD7-CA9F218BFCCC.JPG','Генераторные/86371565-2804-4E82-803A-46606FBAE4F8.JPG','Генераторные/PHOTO-2020-11-05-10-03-22.jpg','Генераторные/PHOTO-2020-11-05-10-03-54.jpg','Генераторные/PHOTO-2022-08-08-18-14-09.jpg'];
$GRUZ = ['Грузоперевозки/1.jpg','Грузоперевозки/2.jpg','Грузоперевозки/3.jpg','Грузоперевозки/4.jpg','Грузоперевозки/5.avif'];
$MTZ = ['МТЗ/B-EhBtJFvnnFRaO2wveNOZCClT-JPhW1zfPpCwBpbB1zf5c5eMiv04F-xdvtOP0VW4tQhgxr4EPrTjI6Hzj26rRH.jpg','МТЗ/MAlDfjGfSf1Vs39sMgOgZT3FIVlZy2V3WKSzwzje3TjX1zoWTC-wApgBQ_8wp1iBUWxCBsuWkR9tMePU8A1TAVVg.jpg','МТЗ/MNAEHRcw27qk0buSmkKQyg5y30hnigc-zXc68ylPagQIfUWmRJj2PI2ngGCso3cNr1Ar9H_JQ-Nm5IJd7VtriDPb.jpg','МТЗ/TBiT7i6PKC59P2fly3WALvJkWWYPWJbSXWj13AyXfSRvSB2btvao6G4HxpIzQMtWxlCM_ZzG5sFpdzqKpkFVVUjP.jpg','МТЗ/WaFjTiAvWrSSDsQfVMmZP8f_4Ypmss9_SmOCEwERQsPFGqv8usViLdYzhYVfY45UOX3bjGEDr8lXHiVZam0Gf4w1.jpg','МТЗ/XK_gv3g8LacSwhlUCa7rOPn9wbbuX3mt104w9q_E5aEv7gYG4ZZfRvCI0aSGn7Bkm3X5FNnoX7Vh9GSfauPzynYX.jpg','МТЗ/fnKAAgLhhTHPwjHV7dUN2l5tz9HIj-Iqi72N3unydGdhEnKq6gEQmazhbEtFUELLEOdBJlPaJztw7BzrsyncSlt5.jpg','МТЗ/n_pdc5b2tgyjydxTAt_7ev1rBBFN26bwbq4796VCT5BHICCidmF-CnC59nv7sETn82ryDyaC4PqRG_nijl-iaTBU.jpg','МТЗ/vXxOynm7ZGLVJl41pflAYJ5V7pl8CoXnvGetQUIa3whJyGIAZggV4Tedcd_NydckYB2Y2emsEorcNLBGna_Zjphp.jpg'];
$SPEC = ['Спецтехника/1.jpg','Спецтехника/2.jpg','Спецтехника/3.jpg'];
$POOL = [
	'proizvodim_modul' => $KAP, 'kapstroenie' => $KAP, 'kapital' => $KAP, 'angar' => $KAP,
	'sclad' => $KAP, 'obchegitia' => $KAP, 'naves' => $KAP, 'montach' => $KAP, 'proektirovanie' => $KAP,
	'administrativnoe' => $ABK, 'modultebel' => $ABK,
	'generatornae' => $GEN, 'compressor' => $GEN, 'compressor_air' => $GEN,
	'gruzoperevozki' => $GRUZ, 'postavka' => $GRUZ,
	'spectehnika' => array_merge($SPEC, $MTZ), 'musorbak' => array_merge($MTZ, $SPEC),
];
$pool = $POOL[$slug] ?? $KAP;
$pn = count($pool);
$off = abs(crc32($slug)) % $pn;
$pick = function ($k) use ($pool, $pn, $off) { return '/public/assets/image/services/' . $pool[($off + $k) % $pn]; };

// --- заголовок с акцентом: часть после «—» оранжевая ---
$title = (string)$svc['title'];
$h1a = $title; $h1b = '';
if (!empty($svc['short'])) {
	$h1a = (string)$svc['short'];
} elseif (mb_strpos($title, '—') !== false) {
	[$h1a, $h1b] = array_map('trim', explode('—', $title, 2));
}
// --- цена ---
$price = !empty($svc['price_from']) ? 'от ' . number_format((int)$svc['price_from'], 0, ',', ' ') . ' ₽' : 'цена по смете';
// --- «кому подойдёт» из запросов СЯ ---
$pretty = function ($q) { $q = trim((string)$q); return mb_strtoupper(mb_substr($q, 0, 1)) . mb_substr($q, 1); };
$auraw = array_values((array)($svc['queries'] ?? []));
$aud = array_values(array_filter(array_map($pretty, $auraw)));
while (count($aud) < 5) { $aud[] = $pretty($svcType); }
$aud = array_slice($aud, 0, 5);
$audIcons = ['fa-building', 'fa-warehouse', 'fa-bolt', 'fa-truck-fast', 'fa-compass-drafting'];
// --- этапы ---
$steps = [
	['Заявка и смета за 15 минут', 'Позвоните ' . $phone . ' или оставьте заявку — рассчитаем стоимость и зафиксируем цену в договоре.'],
	['Договор и производство', $svcType . ': собственное производство, монтаж по графику и технадзор на каждом этапе.'],
	['Сдача и гарантия', 'Сдаём объект с пакетом документов. Гарантия ' . $guarantee . ' лет, поддержка после сдачи.'],
];
$faq = (array)($svc['faq'] ?? []);
$qr = array_values((array)($svc['queries'] ?? []));
while (count($qr) < 7) { $qr[] = $svcType; }
$intent = trim((string)preg_replace('/^(Коммерческий|Транзакционный)[^:]*:\s*/iu', '', (string)($svc['intent'] ?? '')));
if ($intent === '') { $intent = mb_strtolower($svcType); }
$priceNum = !empty($svc['price_from']) ? number_format((int)$svc['price_from'], 0, ',', ' ') . ' ₽' : '';
$priceDesc = $priceNum !== '' ? 'Ориентир — от ' . $priceNum . ', точную смету даём за 15 минут. Цена в договоре не растёт.' : 'Смету считаем за 15 минут по вашей задаче. Цена в договоре не растёт.';

// --- SEO-поля с учётом гео-посадочной (/services/{slug}/{geo}) ---
// Заголовок вкладки: краткий заголовок услуги (до «—») + бренд, укладываемся в 60 символов.
// Для гео-страниц город ставим в конец и НЕ даём ему обрезаться (иначе titles дублируются).
$titleSuffix = ' | ФАВОРИТ-СТРОЙ-СНАБ';
$seoTitle = seo_clip($h1a, 60 - mb_strlen($titleSuffix)) . $titleSuffix;
$seoDesc = (string)$svc['description'];
if ($geo) {
	$h1b = 'в ' . $geoIn;
	// Гео-посадочная: полное название услуги + город; бренд добавляем, только если укладываемся в 60 символов.
	$seoTitle = seo_clip($h1a . ' в ' . $geoIn, 60);
	if (mb_strlen($seoTitle) + mb_strlen($titleSuffix) <= 60) {
		$seoTitle .= $titleSuffix;
	}
	$seoDesc = $geoName . ' — ' . (string)$svc['description'];
}
$basePrice = !empty($svc['price_from']) ? (int)$svc['price_from'] : 0;
// Таблица цен «за м²» и калькулятор площади — только для услуг с площадной ценой
$areaBased = in_array($slug, ['angar', 'sclad', 'proizvodim_modul', 'kapstroenie', 'kapital', 'administrativnoe', 'obchegitia', 'naves', 'montach', 'proektirovanie', 'postavka'], true);
$specIcons = ['generatornae' => 'fa-bolt', 'compressor' => 'fa-fan', 'compressor_air' => 'fa-fan', 'gruzoperevozki' => 'fa-truck-fast', 'spectehnika' => 'fa-tractor', 'postavka' => 'fa-boxes-stacked', 'musorbak' => 'fa-trash-can', 'montach' => 'fa-screwdriver-wrench', 'proektirovanie' => 'fa-compass-drafting', 'sclad' => 'fa-warehouse', 'angar' => 'fa-warehouse', 'administrativnoe' => 'fa-building', 'obchegitia' => 'fa-bed', 'modultebel' => 'fa-couch', 'naves' => 'fa-umbrella'];
$specIcon = $specIcons[$slug] ?? 'fa-helmet-safety';
$wn = min($pn, 8);
$works = [];
for ($k = 0; $k < $wn; $k++) { $works[] = $pool[($off + 5 + $k) % $pn]; }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $h($seoTitle) ?></title>
<meta name="description" content="<?= $h(seo_clip($seoDesc, 130)) ?> ☎ <?= $h($phone) ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
<meta name="theme-color" content="#F97316">
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/public/assets/image/logo/site.webmanifest" />
<?= seo_canonical_tags() ?>
<?= seo_verify_tags() ?>
<?= seo_og_tags($seoTitle, $seoDesc, $pick(0)) ?>
<link rel="search" type="application/opensearchdescription+xml" title="ФАВОРИТ-СТРОЙ-СНАБ" href="/opensearch.xml">
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style> body { font-family: 'Wix Madefor Display', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
<link rel="preload" href="/public/assets/distJS/main.js?v=20261018" as="script">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
<script defer src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
<script defer src="/public/assets/distJS/main.js?v=20261018"></script>
<meta name="yandex-verification" content="27e0ba9980498150" />
<?php include_once __DIR__ . '/sheoma.php'; ?>
<?= seo_analytics() ?>
<?= seo_utm_script() ?>
<?= seo_goals_script() ?>
<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
<style>
.svh-wrap{max-width:1440px;margin:0 auto;padding:104px 16px 0;}
.svh-panel{position:relative;overflow:hidden;border-radius:28px;padding:clamp(26px,4vw,54px) clamp(26px,4vw,54px) clamp(64px,5vw,84px) calc(clamp(26px,4vw,54px) + 7%);}
.svh-surface{position:absolute;inset:0;width:100%;height:100%;fill:#171310;pointer-events:none;}
.svh-circles{position:absolute;left:1.7%;top:118px;z-index:2;}
.svh-crumb{display:flex;align-items:center;gap:6px;font-size:12px;color:#A8A29E;margin-bottom:14px;flex-wrap:wrap;position:relative;z-index:2;}
.svh-crumb a{color:#FCD9B6;font-weight:600;}
.svh-grid{position:relative;z-index:2;display:grid;grid-template-columns:1.05fr .95fr;gap:clamp(20px,3vw,44px);align-items:center;}
.svh-eyebrow{display:inline-flex;align-items:center;gap:10px;color:#FDBA74;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;}
.svh-eyebrow::before{content:"";width:26px;height:2px;background:#F97316;border-radius:2px;}
.svh-h1{margin:16px 0 0;color:#fff;font-size:clamp(36px,4.4vw,64px);font-weight:700;letter-spacing:-.02em;line-height:1.02;}
.svh-h1 .acc{color:#F97316;}
.svh-lead{margin:14px 0 0;color:#D6D3D1;font-size:15px;line-height:1.65;max-width:54ch;}
.svh-pricetab{position:absolute;top:17px;right:2.2%;z-index:2;font-size:14px;font-weight:700;color:#171310;white-space:nowrap;line-height:1.4;}
.svh-pricetab small{font-size:12px;font-weight:600;color:#78716C;white-space:nowrap;}
.svh-cta{display:flex;align-items:center;flex-wrap:wrap;gap:22px;margin-top:20px;}
.svh-pill{display:inline-flex;align-items:center;flex-shrink:0;color:#171310;font-size:15px;font-weight:700;text-decoration:none;}
.svh-pill-label{background:#fff;height:52px;display:flex;align-items:center;padding:0 26px;border-radius:999px;}
.svh-pill-arrow{display:grid;place-items:center;width:52px;height:52px;border-radius:50%;background:#fff;color:#171310;margin-left:-7px;position:relative;transition:transform .25s;}
.svh-pill-arrow::before{content:"";position:absolute;left:-5px;width:12px;height:36px;background:#fff;z-index:-1;}
.svh-pill-arrow svg{width:17px;height:17px;fill:currentColor;transition:transform .25s;transform-origin:center;}
.svh-pill:hover .svh-pill-arrow{transform:translateX(8px);}
.svh-pill:hover .svh-pill-arrow svg{transform:rotate(45deg);}
.svh-phone{color:#fff;font-weight:700;font-size:17px;text-decoration:none;}
.svh-phone:hover{color:#FDBA74;}
.svh-circles{display:flex;flex-direction:column;gap:10px;}
.svh-circles a{display:grid;place-items:center;width:46px;height:46px;background:#F97316;color:#171310;border-radius:50%;font-size:18px;transition:background .2s,transform .2s;}
.svh-circles a:hover{background:#EA580C;transform:translateX(3px);}
.svh-toc{display:flex;flex-wrap:wrap;gap:8px;margin-top:22px;}
.svh-media{position:relative;}
.svh-media::before{content:"";position:absolute;left:0;right:0;bottom:0;height:60%;background:linear-gradient(180deg,transparent 0%,rgba(23,19,16,.7) 100%);border-radius:0 0 22px 22px;z-index:1;pointer-events:none;}
.svh-media::after{content:"";position:absolute;right:0;bottom:0;width:78px;height:78px;background:#171310;border-radius:24px 0 22px 0;z-index:2;}
a.svh-mphoto{position:absolute;right:13px;bottom:13px;width:52px;height:52px;border-radius:50%;background:#F97316;color:#171310;display:grid;place-items:center;font-size:18px;z-index:2;transition:transform .25s,background .25s;}
a.svh-mphoto:hover{transform:translateY(3px);background:#EA580C;}
.svh-media img{width:100%;height:clamp(280px,32vw,460px);object-fit:cover;border-radius:22px;display:block;}
.svh-tag{position:absolute;left:16px;bottom:16px;background:#F97316;color:#171310;border-radius:999px;padding:10px 18px;font-size:13px;font-weight:800;}
.svh-tag b{color:#fff;}
.svh-toc{display:flex;flex-wrap:wrap;gap:8px;margin-top:22px;}
.svh-toc{--line:rgba(255,255,255,.35);}
.svh-toc a{font-size:14px;font-weight:600;color:#fff;background:#ff8038;border:1px solid rgba(255,255,255,.6);border-radius:999px;padding:10px 20px;white-space:nowrap;text-decoration:none;transition:transform .25s,background .25s,border-color .25s,box-shadow .25s;}
.svh-toc a:hover{color:#fff;box-shadow:rgba(0,0,0,.12) 0px 8px 18px;border-color:var(--line);background:rgba(255,255,255,.1);transform:none;}
.svh-giant{position:absolute;left:0;right:0;bottom:-.24em;margin:0;text-align:center;font-size:clamp(90px,13vw,200px);font-weight:800;letter-spacing:-.02em;line-height:1;color:transparent;-webkit-text-stroke:1px rgba(255,255,255,.13);pointer-events:none;white-space:nowrap;}
.svh-under{margin-top:14px;font-size:11px;font-weight:700;letter-spacing:.14em;color:#A8A29E;text-transform:uppercase;}
#lead{scroll-margin-top:90px;}
main img{-webkit-user-drag:none;user-select:none;-moz-user-select:none;}
#works{scroll-margin-top:90px;}
.page-section{padding-top:40px!important;padding-bottom:40px!important;}
.security-section{margin-top:40px!important;margin-bottom:40px!important;}
.cta-band{margin-block:20px!important;}
#works .section-heading{margin-top:24px;}
.svh-h1,.section-heading h2{text-wrap:balance;}
.sv-aud{display:grid;grid-template-columns:1.15fr 1fr 1fr;gap:18px;}
.sv-tall{position:relative;border-radius:20px;overflow:hidden;min-height:380px;grid-row:span 2;}
.sv-tall img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
.sv-tall span{position:absolute;left:18px;right:18px;bottom:18px;color:#fff;font-weight:800;font-size:18px;text-shadow:0 2px 12px rgba(0,0,0,.65);}
.sv-tall span small{display:block;font-weight:500;font-size:13px;opacity:.85;margin-top:4px;}
.sv-sgrid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;}
.sv-works{display:grid;grid-auto-flow:column;grid-auto-columns:min(340px,82vw);gap:14px;overflow-x:auto;overflow-y:hidden;scroll-snap-type:x mandatory;padding:4px 2px 10px;scrollbar-width:none;}
.sv-works::-webkit-scrollbar{display:none;}
.sv-work{position:relative;scroll-snap-align:start;border-radius:20px;overflow:hidden;background:#F4F1EC;}
.sv-work img{width:100%;height:250px;object-fit:cover;display:block;}
.sv-work span{position:absolute;left:12px;bottom:12px;font-size:12px;font-weight:700;color:#fff;background:rgba(0,0,0,.55);border-radius:8px;padding:5px 11px;}
.sv-wnav{display:flex;gap:10px;margin-top:16px;}
.sv-wnav button{width:56px;height:44px;border-radius:999px;border:1px solid rgba(255,255,255,.6);background:#ff8038;color:#fff;font-size:15px;cursor:pointer;transition:transform .25s,background .25s,border-color .25s;font-family:inherit;}
.sv-wnav button:hover{background:transparent;border-color:#EA580C;color:#EA580C;transform:translateY(-2px);}
.sv-whyname{color:#EA580C;font-family:Georgia,'Times New Roman',serif;font-style:italic;font-weight:400;white-space:nowrap;}
@media(min-width:1200px){.security-title{white-space:nowrap;}}
.sv-sup{color:#171310;font-weight:700;font-size:15px;text-decoration:underline;text-underline-offset:4px;text-decoration-color:#F97316;}
.sv-sup:hover{color:#C2410C;}
.sv-flat{background:#F4F1EC;border-radius:25px;padding:27px 29px;}
.sv-flat.cream{background:#FFF1E3;}
.sv-flat h3{margin:0 0 10px;font-size:clamp(20px,1.6vw,24px);font-weight:500;line-height:1.15;letter-spacing:-.5px;color:#171310;}
.sv-flat p{margin:0;font-size:14.5px;line-height:1.55;color:#44403C;}
.sv-flowhead{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;flex-wrap:wrap;}
.sv-flowtitle{margin:0;font-size:clamp(56px,8vw,120px);font-weight:800;letter-spacing:-.04em;line-height:.95;color:#171310;text-transform:uppercase;}
.sv-flowdesc{margin:0;max-width:300px;font-size:15px;font-weight:600;line-height:1.5;color:#171310;text-align:right;}
main{overflow-x:clip;}
.sv-flowmeta{display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px 16px;margin-top:34px;font-size:12.5px;font-weight:600;letter-spacing:.06em;color:#78716C;text-transform:uppercase;}
.sv-flowmeta b{color:#171310;font-weight:800;}
.sv-flow{display:grid;grid-template-columns:repeat(3,1fr);margin-top:26px;}
.sv-fcol{position:relative;padding:0 34px 90px 30px;border-left:1px solid #E4E0D8;}
.sv-fcol:last-child{border-right:1px solid #E4E0D8;}
.sv-fcol:nth-child(2){padding-top:64px;}
.sv-fcol:nth-child(3){padding-top:128px;}
.sv-fcol img{width:132px;height:150px;object-fit:cover;border-radius:14px;display:block;margin-bottom:26px;background:#F4F5F7;}
.sv-fcol h3{margin:0;font-size:clamp(21px,2vw,27px);font-weight:800;letter-spacing:-.01em;text-transform:uppercase;color:#171310;}
.sv-fcol p{margin:12px 0 0;font-size:14.5px;line-height:1.6;color:#57534E;}
.sv-fbar{position:absolute;left:30px;right:34px;bottom:34px;}
.sv-fbar span{display:block;font-size:13px;font-weight:700;color:#171310;margin-bottom:8px;}
.sv-fbar i{display:block;height:7px;border-radius:99px;background:#EDE9E3;overflow:hidden;}
.sv-fbar i b{display:block;height:100%;background:#EA580C;border-radius:99px;}
@media(max-width:860px){.sv-flow{grid-template-columns:1fr;}.sv-fcol{border-left:0;border-top:1px solid #E4E0D8;padding:26px 0 80px;}.sv-fcol:nth-child(2),.sv-fcol:nth-child(3){padding-top:26px;}.sv-fbar{left:0;right:0;bottom:26px;}.sv-flowdesc{text-align:left;}}
.sv-leadgrid{display:grid;grid-template-columns:1fr 1.1fr;gap:clamp(24px,4vw,60px);align-items:start;}
.sv-leadh{margin:0;font-size:clamp(30px,3.4vw,44px);font-weight:500;letter-spacing:-.02em;line-height:1.1;color:#171310;}
.sv-leadp{margin:22px 0 0;font-size:17px;line-height:1.6;color:#333333;max-width:46ch;}
.sv-leadp b{color:#171310;}
.sv-send{border:0;cursor:pointer;font-family:inherit;background:#EA580C;color:#fff;font-weight:600;font-size:19px;border-radius:16px;padding:18px 54px;justify-self:start;margin-top:16px;transition:background .2s;}
.sv-send:hover{background:#C2410C;}
.sv-consent{font-size:14px;line-height:1.65;color:#78716C;margin-top:4px;}
.sv-leadcard{position:relative;overflow:hidden;}
.sv-leadcard::after{content:"";position:absolute;left:-128px;bottom:-255px;width:min(560px,95%);aspect-ratio:1;background:url(/public/assets/image/Components/element-nobg.png) center/contain no-repeat;opacity:.92;pointer-events:none;}
.sv-leadcard .sv-leadgrid{position:relative;z-index:1;}
.sv-consent a{color:#57534E;text-decoration:underline;text-underline-offset:3px;}
.sv-points{margin:18px 0 0;padding:0;list-style:none;display:grid;gap:12px;font-size:14.5px;color:var(--ink-soft);}
.sv-points i{color:var(--sun);margin-right:10px;}
.sv-form{display:grid;gap:12px;}
.sv-form .row2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.sv-form input,.sv-form select{width:100%;box-sizing:border-box;border:1px solid #DFDCD6;background:#fff;border-radius:14px;padding:0 22px;height:62px;font-size:16.5px;font-family:inherit;color:#171310;}
.sv-form input::placeholder{color:#8A8A8A;}
.sv-form input:focus,.sv-form select:focus{outline:none;border-color:#F97316;background:#fff;box-shadow:0 0 0 3px rgba(249,115,22,.15);}
.sv-form .btn-sun{border:0;width:100%;cursor:pointer;font-family:inherit;}
.sv-nolift:hover{transform:none!important;box-shadow:0 12px 32px rgba(23,19,16,.06)!important;}
.sv-check{display:flex;align-items:center;gap:10px;font-size:14px;color:#57534E;cursor:pointer;}
.sv-check input{width:19px;height:19px;margin:0;accent-color:#EA580C;flex:0 0 19px;}
.sv-agree{font-size:12px;color:#78716C;}
.sv-agree a{color:#EA580C;}
.sv-marq{margin:0;overflow:clip;}
html.lenis,html.lenis body{height:auto;}
.lenis.lenis-smooth{scroll-behavior:auto!important;}
.lenis.lenis-stopped{overflow:hidden;}
.sv-marq .marquee-band{transform:rotate(-1.2deg);margin:26px -2vw 10px;}
.btn-sun{background:#EA580C!important;box-shadow:0 10px 24px rgba(234,88,12,.35)!important;border:0;}
.btn-sun:hover{background:#C2410C!important;}
.scard-ico{background:#171310!important;color:#FDBA74!important;box-shadow:none!important;}
.scard::after{background:#EA580C!important;}
.sv-marq .marquee-band{background:#EA580C;}
.step3-n{color:#C2410C!important;}
.sv-steps3 .step3{transition:transform .3s ease,box-shadow .3s ease;}
.sv-steps3 .step3:hover{transform:translateY(-6px);box-shadow:0 22px 48px rgba(23,19,16,.12);}
.sv-steps3 .step3-img{transition:transform .5s ease;}
.sv-steps3 .step3:hover .step3-img{transform:scale(1.03);}
@media(prefers-reduced-motion:reduce){.marquee-track{animation:none;}.svh-pill-arrow,.svh-pill-arrow svg{transition:none;}}
@media(max-width:1100px){.svh-pricetab{position:static;margin-top:16px;text-align:left;}}
@media(max-width:700px){.svh-surface{display:none;}.svh-panel{background:#171310;border-radius:24px;padding:26px 20px 56px;}.svh-circles{position:static;flex-direction:row;margin-top:18px;}.svh-pricetab{position:static;margin-top:16px;text-align:left;}}
@media(max-width:900px){.svh-grid{grid-template-columns:1fr;}.sv-aud{grid-template-columns:1fr 1fr;}.sv-tall{grid-column:1/-1;grid-row:auto;min-height:250px;}.sv-leadgrid{grid-template-columns:1fr;}.sv-sgrid{grid-template-columns:1fr 1fr;}.sv-works{grid-auto-columns:min(300px,84vw);}}
@media(max-width:560px){.sv-aud{grid-template-columns:1fr;}.sv-sgrid{grid-template-columns:1fr;}}
@media(prefers-reduced-motion:reduce){.sv-tall,.scard{transition:none;}}
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
<?php include_once __DIR__ . '/header.php'; ?>
<main>
<?= seo_breadcrumbs_html() ?>
	<div class="svh-wrap">
	<section class="svh-panel view">
		<svg class="svh-surface" viewBox="0 0 1600 760" preserveAspectRatio="none" aria-hidden="true"><path d="M150 0H1290Q1318 0 1323 28Q1328 56 1356 56H1560Q1600 56 1600 96V720Q1600 760 1560 760H350Q325 760 325 735Q325 710 300 710H170Q145 710 145 735Q145 760 120 760H40Q0 760 0 720V430Q0 400 30 400H45Q115 400 115 370V40Q115 0 150 0Z"/>
		<div class="svh-pricetab"><?= $h($price) ?> <small>смета за 15 минут</small></div></svg>
		<div class="svh-circles">
			<a href="tel:<?= $h($phoneHref) ?>" title="Позвонить" aria-label="Позвонить"><i class="fa-solid fa-phone"></i></a>
			<a href="https://t.me/fss_rus" target="_blank" rel="noopener" title="Telegram" aria-label="Telegram"><i class="fa-brands fa-telegram"></i></a>
			<a href="https://vk.com/fss_rus" target="_blank" rel="noopener" title="ВКонтакте" aria-label="ВКонтакте"><i class="fa-brands fa-vk"></i></a>
		</div>
		<div class="svh-grid">
			<div>
				<div class="svh-eyebrow">С <?= seo_config('founded_market') ?> года • Под ключ • Допуск СРО</div>
				<h1 class="svh-h1"><?= $h($h1a) ?><?= $h1b !== '' ? ' <span class="acc">' . $h($h1b) . '</span>' : '' ?></h1>
				<p class="svh-lead"><?= $h($svc['description']) ?></p>
				<?php if ($geo): ?><p style="margin-top:10px"><?= $h($svcType) ?> в <?= $h($geoIn) ?> (<?= $h($geo['region']) ?>): доставка конструкций <?= $h($geo['route']) ?> — <?= $h($geo['eta']) ?>. Работаем с юрлицами по договору, цена фиксируется.</p><?php endif; ?>
				<div class="svh-cta">
					<a class="svh-pill" href="#lead" data-goal="svc_hero_cta" title="Рассчитать стоимость"><span class="svh-pill-label">Рассчитать стоимость</span><span class="svh-pill-arrow"><svg viewBox="0 0 384 512" aria-hidden="true"><path d="M328 96h24v288h-48V177.9L81 401l-17 17-33.9-34 17-17 223-223H64V96h264z"/></svg></span></a>
					<a class="svh-phone" href="tel:<?= $h($phoneHref) ?>" title="Позвонить: <?= $h($phone) ?>"><?= $h($phone) ?></a>
				</div>
				<nav class="svh-toc" aria-label="Разделы страницы">
					<a href="#aud" title="Кому подойдёт">Кому подойдёт</a><a href="#ceny" title="Цены">Цены</a><a href="#lead" title="Смета">Смета</a><a href="#steps" title="Этапы работы">Этапы</a><a href="#works" title="Наши работы">Работы</a><a href="#adv" title="Преимущества">Преимущества</a><a href="#faq" title="Частые вопросы">Вопросы</a>
				</nav>
				<?php $isPriority = in_array($slug, (array)($geoAll['priority_services'] ?? []), true); if (!$geo && $isPriority && !empty($geoAll['geo'])): ?>
				<nav aria-label="Гео-направления" style="margin-top:12px;font-size:13px;line-height:1.9;color:#fff"><span style="opacity:.7">Строим в городах:</span> <?php $gi = 0; foreach ($geoAll['geo'] as $gk => $gv): echo $gi++ ? ' · ' : ''; ?><a style="color:#fff;text-decoration:underline;text-underline-offset:2px" href="/services/<?= $h($slug) ?>/<?= $h($gk) ?>" title="<?= $h($gv['name']) ?>"><?= $h($gv['name']) ?></a><?php endforeach; ?></nav>
</div>
	</section>
	<!-- E-E-A-T Credentials Section -->
	<section class="page-section view" id="credentials" style="background:#FFF7EF;border-top:1px solid #F5E6D3;border-bottom:1px solid #F5E6D3">
		<div class="container">
			<div class="section-heading"><h2>Почему доверяют нам госзаказчики и бизнес</h2><span class="heading-divider" aria-hidden="true"></span><p>Экспертиза, подтверждённая Росгвардией, Минпросвещением и 180+ сданными объектами</p></div>
			<div class="credentials-grid" style="margin-top:22px;display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px">
				<article class="credential-card" style="background:#fff;border:1px solid #F5E6D3;border-radius:20px;padding:24px;text-align:center;transition:transform .3s ease,box-shadow .3s ease;">
					<div style="width:56px;height:56px;background:#F97316;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px"><i class="fa-solid fa-shield-halved" style="color:#fff;font-size:24px"></i></div>
					<h3 style="margin:0 0 8px;font-size:18px;font-weight:800;color:#171310">Партнёр Росгвардии</h3>
					<p style="margin:0;font-size:14px;color:#44403C;line-height:1.5">Официальный поставщик Федеральной службы войск национальной гвардии РФ. Обеспечиваем нужды силовых структур РФ.</p>
				</article>
				<article class="credential-card" style="background:#fff;border:1px solid #F5E6D3;border-radius:20px;padding:24px;text-align:center;transition:transform .3s ease,box-shadow .3s ease;">
					<div style="width:56px;height:56px;background:#F97316;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px"><i class="fa-solid fa-school" style="color:#fff;font-size:24px"></i></div>
					<h3 style="margin:0 0 8px;font-size:18px;font-weight:800;color:#171310">Стандарты Минпросвещения</h3>
					<p style="margin:0;font-size:14px;color:#44403C;line-height:1.5">Комплексное оснащение 13+ школ по стандартам Минпросвещения РФ: мебель, оборудование, лаборатории, мультимедиа.</p>
				</article>
				<article class="credential-card" style="background:#fff;border:1px solid #F5E6D3;border-radius:20px;padding:24px;text-align:center;transition:transform .3s ease,box-shadow .3s ease;">
					<div style="width:56px;height:56px;background:#F97316;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px"><i class="fa-solid fa-certificate" style="color:#fff;font-size:24px"></i></div>
					<h3 style="margin:0 0 8px;font-size:18px;font-weight:800;color:#171310">СРО и допуски</h3>
					<p style="margin:0;font-size:14px;color:#44403C;line-height:1.5">Член СРО проектирования и строительства. Работаем по 44-ФЗ и 223-ФЗ. Полный пакет закрывающих документов.</p>
				</article>
				<article class="credential-card" style="background:#fff;border:1px solid #F5E6D3;border-radius:20px;padding:24px;text-align:center;transition:transform .3s ease,box-shadow .3s ease;">
					<div style="width:56px;height:56px;background:#F97316;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px"><i class="fa-solid fa-building" style="color:#fff;font-size:24px"></i></div>
					<h3 style="margin:0 0 8px;font-size:18px;font-weight:800;color:#171310">180+ сданных объектов</h3>
					<p style="margin:0;font-size:14px;color:#44403C;line-height:1.5">С 2011 года сдаём объекты под ключ: общежития, школы, ангары, АБК, склады, БВЗ под ключ.</p>
				</article>
				<article class="credential-card" style="background:#fff;border:1px solid #F5E6D3;border-radius:20px;padding:24px;text-align:center;transition:transform .3s ease,box-shadow .3s ease;">
					<div style="width:56px;height:56px;background:#F97316;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px"><i class="fa-solid fa-shield-halved" style="color:#fff;font-size:24px"></i></div>
					<h3 style="margin:0 0 8px;font-size:18px;font-weight:800;color:#171310">Гарантия 50+ лет</h3>
					<p style="margin:0;font-size:14px;color:#44403C;line-height:1.5">Официальная гарантия на несущие конструкции 50+ лет. Фиксированная цена и сроки в договоре.</p>
				</article>
				<article class="credential-card" style="background:#fff;border:1px solid #F5E6D3;border-radius:20px;padding:24px;text-align:center;transition:transform .3s ease,box-shadow .3s ease;">
					<div style="width:56px;height:56px;background:#F97316;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px"><i class="fa-solid fa-truck-fast" style="color:#fff;font-size:24px"></i></div>
					<h3 style="margin:0 0 8px;font-size:18px;font-weight:800;color:#171310">Своя логистика и техника</h3>
					<p style="margin:0;font-size:14px;color:#44403C;line-height:1.5">Свой автопарк, манипуляторы, спецтехника. Доставка и монтаж по Москве, МО и всей России.</p>
				</article>
			</div>
		</div>
	</section>
	<?php endif; ?>
			</div>
			<div class="svh-media">
				<img src="<?= $h($pick(0)) ?>"<?= seo_img_attrs((string)$pick(0)) ?> alt="<?= $h($title) ?> — фото объекта" title="<?= $h($title) ?> — фото объекта" fetchpriority="high" decoding="async" onerror="this.style.display='none'">
				<span class="svh-tag">Смета за <b>15 минут</b></span>
				<a class="svh-mphoto" href="#aud" title="Смотреть дальше" aria-label="Смотреть дальше"><i class="fa-solid fa-arrow-down"></i></a>
			</div>
		</div>
		<p class="svh-under">EST. <?= seo_config('founded_market') ?> — Москва · работаем по России</p>
		<p class="svh-giant" aria-hidden="true">ФСС</p>
	</section>
	</div>
	<div class="sv-marq"><div class="marquee-band" aria-hidden="true"><div class="marquee-track"><?php $mq = array_slice($auraw, 0, 8); foreach (array_merge($mq, $mq) as $mqi): ?><span class="marquee-item"><?= $h($mqi) ?></span><?php endforeach; ?></div></div></div>
	<div class="stats-band"><div class="stats-grid">
		<div class="stat-cell"><div class="stat-num" data-n="<?= (int)$deadline ?>">0 <span class="accent">дней</span></div><div class="stat-lbl">от старта до сдачи</div></div>
		<div class="stat-cell"><div class="stat-num" data-n="<?= (int)$guarantee ?>">0 <span class="accent">лет</span></div><div class="stat-lbl">службы конструкции</div></div>
		<div class="stat-cell"><div class="stat-num" data-n="<?= (int)preg_replace("/\D/", "", $objects) ?>"><?= $h(preg_replace("/\d+/", "", $objects)) ?></div><div class="stat-lbl">сданных объектов</div></div>
		<div class="stat-cell"><div class="stat-num"><?= $h($price) ?></div><div class="stat-lbl">смета за 15 минут</div></div>
	</div></div>
	<section class="page-section view" id="aud">
		<div class="section-heading"><h2>Кому подойдёт</h2><span class="heading-divider" aria-hidden="true"></span><p>Если узнали свою задачу — рассчитаем точную смету за 15 минут</p></div>
		<div class="sv-aud" style="margin-top:22px">
			<div class="sv-tall"><img src="<?= $h($pick(4)) ?>" alt="<?= $h($aud[0]) ?>" title="<?= $h($aud[0]) ?>" loading="lazy" decoding="async" onerror="this.style.display='none'"><span><?= $h($aud[0]) ?><small><?= $h($svcType) ?> под ключ</small></span></div>
			<?php $audDesc = ['Замер, проект и точная смета за 15 минут', 'Фиксированная цена в договоре', 'Сдача с документами и гарантией', 'Работаем по Москве, МО и всей России']; ?>
			<?php for ($i = 1; $i <= 4; $i++): ?>
			<div class="sv-flat<?= in_array($i, [2, 3]) ? ' cream' : '' ?>"><h3><?= $h($aud[$i]) ?></h3><p><?= $h($audDesc[($i - 1) % 4]) ?></p></div>
			<?php endfor; ?>
		</div>
	</section>
	<section class="page-section view" id="lead">
		<div class="card-prem sv-nolift sv-leadcard" style="margin-top:22px;padding:clamp(30px,5vw,72px);border-radius:28px;">
			<div class="sv-leadgrid">
				<div>
					<h3 class="sv-leadh">Оцените стоимость<br>за пару минут</h3>
					<p class="sv-leadp">Оставьте заявку — <b>инженер перезвонит за 7 минут</b>, рассчитает смету за 15 минут и зафиксирует цену в договоре.</p>
				</div>
				<form class="sv-form js-svcalc" method="POST" action="#" data-goal="lead_<?= $h($slug) ?>" novalidate>
					<input type="hidden" name="content_form">
					<?php $abPath = __DIR__ . '/antibot_fields.php'; if (file_exists($abPath)) include_once $abPath; ?>
					<input type="text" name="name" placeholder="Ваше имя" autocomplete="name" required>
					<div class="row2">
						<input type="tel" name="phone" placeholder="Телефон" autocomplete="tel" inputmode="tel" required>
						<input type="text" name="area" inputmode="decimal" placeholder="Площадь, м²">
					</div>
					<select name="otype" aria-label="Что нужно">
						<option value="">Что нужно</option>
						<?php foreach (array_slice($aud, 0, 4) as $o): ?><option><?= $h($o) ?></option><?php endforeach; ?>
					</select>
					<input type="hidden" name="message" value="<?= $h($title) ?> — нужны сроки и цена">
					<label class="sv-check"><input type="checkbox" name="checkbox" required> <span>Согласен(а) на обработку персональных данных</span></label>
					<button type="submit" class="sv-send">Отправить</button>
					<div class="sv-consent">Нажимая на кнопку, я соглашаюсь на <a href="/politika" title="Политика конфиденциальности">обработку персональных данных</a></div>
				</form>
			</div>
		</div>
		<script>(function(){
		if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
			var nums = document.querySelectorAll('.stat-num[data-n]');
			var io = new IntersectionObserver(function(es){ es.forEach(function(e){ if (!e.isIntersecting) return; io.unobserve(e.target);
				var el = e.target, target = parseInt(el.getAttribute('data-n'), 10) || 0, tail = el.textContent.replace(/^[\d\s]+/, ''), t0 = null;
				function fr(ts){ if (!t0) t0 = ts; var p = Math.min((ts - t0) / 1200, 1), ez = 1 - Math.pow(1 - p, 3), v = Math.round(target * ez);
					el.innerHTML = v.toLocaleString('ru-RU') + tail; if (p < 1) requestAnimationFrame(fr); }
				requestAnimationFrame(fr); }); }, {threshold: .4});
			nums.forEach(function(n){ io.observe(n); });
		} else { document.querySelectorAll('.stat-num[data-n]').forEach(function(el){ var target = parseInt(el.getAttribute('data-n'), 10) || 0, tail = el.textContent.replace(/^[\d\s]+/, ''); el.innerHTML = target.toLocaleString('ru-RU') + tail; }); }
	})();
	(function(){document.querySelectorAll('main img').forEach(function(img){img.draggable=false;img.addEventListener('dragstart',function(e){e.preventDefault();});img.addEventListener('contextmenu',function(e){e.preventDefault();});});})();</script>
	<script>
	(function(){document.querySelectorAll('.js-svcalc').forEach(function(frm){frm.addEventListener('submit',function(){var m=frm.querySelector('input[name="message"]');var a=frm.querySelector('input[name="area"]');var o=frm.querySelector('select[name="otype"]');var add='';if(a&&a.value.trim())add+=' Площадь: '+a.value.trim()+' м².';if(o&&o.value)add+=' Нужно: '+o.value+'.';if(add)m.value+=add;});});})();</script>
	</section>
	<section class="page-section view" id="steps">
		<div class="section-heading"><h2>Как мы работаем</h2><span class="heading-divider" aria-hidden="true"></span><p>Три шага от заявки до сдачи — сроки фиксируем в договоре</p></div>
		<div class="sv-flowmeta"><span>Процесс: <b>3 этапа</b></span><span>Срок: <b>от <?= $h($deadline) ?> дней</b></span></div>
		<div class="sv-flow">
			<?php $pct = [33, 66, 100]; foreach ($steps as $k => $st): ?>
			<div class="sv-fcol">
				<img src="<?= $h($pick($k + 1)) ?>" alt="<?= $h($st[0]) ?> — ФСС" title="<?= $h($st[0]) ?> — ФСС" loading="lazy" decoding="async" onerror="this.style.display='none'">
				<h3><?= $h($st[0]) ?></h3>
				<p><?= $h($st[1]) ?></p>
				<div class="sv-fbar"><span><?= $pct[$k] ?>%</span><i><b style="width:<?= $pct[$k] ?>%"></b></i></div>
			</div>
			<?php endforeach; ?>
		</div>
	</section>
	<section class="page-section view" id="works">
		<div class="section-heading"><h2>Примеры работ</h2><span class="heading-divider" aria-hidden="true"></span><p><?= $h($svcType) ?> — реальные фото с объектов ФСС</p></div>
		<div class="sv-works" id="svWorks">
			<?php foreach ($works as $wi => $wf): ?>
			<figure class="sv-work" style="margin:0"><img src="/public/assets/image/services/<?= $h($wf) ?>" alt="<?= $h($svcType) ?> — пример работ <?= $wi + 1 ?>" title="<?= $h($svcType) ?> — пример работ <?= $wi + 1 ?>" loading="lazy" decoding="async" onerror="this.closest('.sv-work').style.display='none'"><span>Фото <?= $wi + 1 ?></span></figure>
			<?php endforeach; ?>
		</div>
		<div class="sv-wnav"><button type="button" data-wv="-1" aria-label="Назад"><i class="fa-solid fa-arrow-left"></i></button><button type="button" data-wv="1" aria-label="Вперёд"><i class="fa-solid fa-arrow-right"></i></button></div>
		<script>(function(){var box=document.getElementById('svWorks');if(!box)return;var btns=document.querySelectorAll('[data-wv]');btns.forEach(function(b){b.addEventListener('click',function(){var card=box.querySelector('.sv-work');var w=card?card.getBoundingClientRect().width+14:360;box.scrollBy({left:parseInt(b.getAttribute('data-wv'),10)*w,behavior:'smooth'});});});})();</script>
	</section>
	<?php if ($basePrice > 0 && $areaBased): ?>
	<section class="page-section view" id="ceny">
		<div class="section-heading"><h2>Цены<?= $geo ? ' в ' . $h($geoIn) : '' ?></h2><span class="heading-divider" aria-hidden="true"></span><p>Ориентировочные цены «под ключ» по площади<?= $geo ? ' в ' . $h($geoIn) : '' ?> — точную смету считаем за 15 минут</p></div>
		<div style="overflow-x:auto;margin-top:22px">
			<table style="width:100%;border-collapse:collapse;font-size:15px;background:#fff;border-radius:16px;overflow:hidden">
				<thead><tr style="background:#171310;color:#fff;text-align:left">
					<th style="padding:12px 16px">Площадь</th>
					<th style="padding:12px 16px">Базовая комплектация</th>
					<th style="padding:12px 16px">Утеплённая / под ключ</th>
					<th style="padding:12px 16px">Срок</th>
				</tr></thead>
				<tbody>
				<?php foreach ([200, 300, 500, 1000] as $a): $cold = $a * $basePrice; $warm = (int)round($cold * 1.28); ?>
				<tr style="border-bottom:1px solid #eee">
					<td style="padding:12px 16px;font-weight:700"><?= number_format($a, 0, ',', ' ') ?> м²</td>
					<td style="padding:12px 16px">от <?= number_format($cold, 0, ',', ' ') ?> ₽</td>
					<td style="padding:12px 16px">от <?= number_format($warm, 0, ',', ' ') ?> ₽</td>
					<td style="padding:12px 16px">от <?= $h($deadline) ?> дней</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<p style="margin-top:10px;font-size:13px;opacity:.75">* Ориентир «от <?= number_format($basePrice, 0, ',', ' ') ?> ₽/м²». Итог зависит от пролёта, нагрузок и инженерных систем — фиксируем в договоре.</p>
	</section>

	<section class="page-section view" id="calc">
		<div class="card-prem sv-nolift" style="padding:clamp(24px,4vw,48px);border-radius:24px">
			<div class="section-heading"><h2>Калькулятор стоимости</h2><span class="heading-divider" aria-hidden="true"></span><p>Прикидка за 10 секунд — точный расчёт инженер даёт за 15 минут</p></div>
			<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-top:20px" data-base="<?= $basePrice ?>">
				<label style="display:flex;flex-direction:column;gap:6px;font-size:13px;font-weight:600">Площадь, м²
					<input type="number" min="20" step="10" value="300" data-calc-area style="padding:12px;border:1px solid #e7e7db;border-radius:12px;font-size:16px">
				</label>
				<label style="display:flex;flex-direction:column;gap:6px;font-size:13px;font-weight:600">Комплектация
					<select data-calc-type style="padding:12px;border:1px solid #e7e7db;border-radius:12px;font-size:16px">
						<option value="1">Холодная (без утепления)</option>
						<option value="1.28">Утеплённая</option>
						<option value="1.35">Тёплая с инженерией и отделкой</option>
					</select>
				</label>
			</div>
			<div style="margin-top:20px;display:flex;flex-wrap:wrap;gap:16px;align-items:center">
				<div style="font-size:15px">Ориентировочная стоимость: <b data-calc-out style="font-size:26px;color:#F97316">—</b></div>
				<a href="#lead" class="btn-outline" data-calc-send title="Отправить расчёт инженеру">Отправить расчёт инженеру</a>
			</div>
		</div>
		<script>(function(){var box=document.querySelector('#calc [data-base]');if(!box)return;var base=parseInt(box.getAttribute('data-base'),10)||0;var area=box.querySelector('[data-calc-area]'),type=box.querySelector('[data-calc-type]'),out=document.querySelector('#calc [data-calc-out]');function fmt(n){return n.toLocaleString('ru-RU')+' ₽';}function calc(){var a=Math.max(0,parseFloat(area.value)||0),k=parseFloat(type.value)||1,lo=a*base*k,hi=lo*1.15;out.textContent=lo?'≈ '+fmt(Math.round(lo))+' – '+fmt(Math.round(hi)):'—';}area.addEventListener('input',calc);type.addEventListener('change',calc);calc();var send=document.querySelector('#calc [data-calc-send]');if(send)send.addEventListener('click',function(){var lf=document.querySelector('#lead input[name="area"]');if(lf)lf.value=area.value;});})();</script>
	</section>
	<?php endif; ?>
	<?php if ($faq): ?>
	<section class="cta-band view">
		<img src="/public/assets/image/Components/banner/hero.jpg" alt="Быстровозводимые здания ФСС — строим то, что ведёт вас вперёд" title="Быстровозводимые здания ФСС" loading="lazy" decoding="async" onerror="this.style.display='none'">
		<div class="cta-in">
			<span class="cta-eyebrow"><i></i>Ваш проект — наше дело</span>
			<h2>Строим то,<br>что ведёт вас вперёд</h2>
			<p><?= $h($svcType) ?> под ключ: проектируем, производим и монтируем — с фиксированной ценой в договоре.</p>
			<a href="#lead" class="cta-pill" data-goal="svc_band_cta" title="Построить с нами">Построить с нами <i class="fa-solid fa-arrow-right"></i></a>
		</div>
	</section>
	<section class="security-section view" id="adv">
		<h2 class="security-title">Почему выбирают нас: <span class="sv-whyname"><?= $h($h1a) ?></span></h2>
		<div class="security-grid">
			<article class="security-card security-main">
				<h3>Фиксированная цена</h3>
				<p><?= $h($priceDesc) ?></p>
				<div class="security-shield" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></div>
			</article>
			<article class="security-card security-compliance">
				<h3>Сроки от <?= $h($deadline) ?> дней</h3>
				<p><?= $h($intent) ?> — от заявки до сдачи, график фиксируем в договоре.</p>
				<div class="document-stack" aria-hidden="true">
					<div class="document document-back"><div class="document-line"></div><div class="document-line"></div><div class="document-line"></div></div>
					<div class="document"><div class="document-line"></div><div class="document-line"></div><div class="document-check">✓</div><div class="document-check">✓</div><div class="document-check">✓</div></div>
				</div>
			</article>
			<article class="security-card security-storage">
				<h3>Гарантия<br><?= $h($guarantee) ?></h3>
				<p>Несущие конструкции и узлы — официальная гарантия по договору.</p>
			</article>
			<article class="security-card security-uptime">
				<div class="gauge" aria-hidden="true"><div class="gauge-ring"></div><div class="gauge-needle"></div></div>
				<div class="uptime-number"><?= $h($deadline) ?></div>
				<p>дней —<br>от старта</p>
			</article>
			<article class="security-card security-versions">
				<span class="security-ico" aria-hidden="true"><i class="fa-solid <?= $h($specIcon) ?>"></i></span>
				<h3><?= $h($pretty($qr[5])) ?></h3>
			</article>
			<article class="security-card security-requests">
				<h3><?= $h($pretty($qr[6])) ?></h3>
				<p>Рассчитаем за 15 минут — <?= $h($phone) ?>, инженер на связи.</p>
			</article>
			<article class="security-card security-support">
				<h3>Нужна<br>смета?</h3>
				<a class="sv-sup" href="#lead" title="Получить за 15 минут">Получить за 15 минут</a>
			</article>
		</div>
	</section>
	<section class="page-section view" id="faq">
		<div class="section-heading"><h2>Частые вопросы</h2><span class="heading-divider" aria-hidden="true"></span><p>Отвечаем честно — остальное подскажет инженер</p></div>
		<div class="grid gap-3" style="margin-top:22px">
			<?php foreach ($faq as $fi => $f): ?>
			<details class="faq-ex"<?= $fi === 0 ? ' open' : '' ?>>
				<summary><span class="faq-mark" aria-hidden="true"></span><span><?= $h($f[0]) ?></span></summary>
				<div class="faq-content"><p class="faq-a"><?= $h($f[1]) ?></p></div>
			</details>
			<?php endforeach; ?>
		</div>
		<div style="margin-top:14px"><a class="btn-outline" href="#lead" title="Получить консультацию">Получить консультацию по другим вопросам</a></div>
	</section>
	<?php endif; ?>

	<?php if ($geo && !empty($geoAll['geo'])): ?>
	<section class="page-section view" id="geo">
		<div class="section-heading"><h2>Другие направления <?= $h(trim($h1a)) ?></h2><span class="heading-divider" aria-hidden="true"></span><p>Строим и доставляем по Москве и Московской области</p></div>
		<nav aria-label="Гео-направления" style="margin-top:18px;display:flex;flex-wrap:wrap;gap:10px;font-size:14px">
			<a class="btn-outline" href="/services/<?= $h($slug) ?>" title="Все направления">Все направления</a>
			<?php foreach ($geoAll['geo'] as $gk => $gv): if ($gk === $geoKey) continue; ?>
			<a class="btn-outline" href="/services/<?= $h($slug) ?>/<?= $h($gk) ?>" title="<?= $h($gv['name']) ?>"><?= $h($gv['name']) ?></a>
			<?php endforeach; ?>
		</nav>
	</section>
	<?php endif; ?>

	<?php include_once __DIR__ . '/reviews_services.php'; ?>
</main>

 <?php include_once __DIR__ . '/related_services.php'; ?>
 <?php include_once __DIR__ . '/footer.php'; ?>
</body>
</html>
