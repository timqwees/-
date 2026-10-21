<?php
/**
 * ЕДИНЫЙ шаблон страниц услуг (1 услуга = 1 кластер).
 * Используется всеми public/pages/services/<slug>/index.php через тонкий загрузчик:
 *   <?php $fss_service_slug = 'angar'; require __DIR__ . '/../../../components/service_template.php';
 * Данные: app/Data/services.php (title/service_type/description/price_from/queries/faq),
 * цифры и контакты: app/Config/site.php. Фото — только существующие файлы.
 */
require_once __DIR__ . '/../../app/bootstrap.php';

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
// Локальные вопросы города — делают гео-страницу самостоятельной, а не копией услуги
$geoFaq = $geo ? array_values((array)($geo['faq'] ?? [])) : [];
$geoZones = $geo ? array_values((array)($geo['zones'] ?? [])) : [];
$svcType = (string)($svc['service_type'] ?? $svc['title']);
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$phone = (string)seo_config('phone_display');
$phoneHref = preg_replace('/\D/', '', (string)seo_config('phone'));
$deadline = (string)seo_config('deadline_days');
$guarantee = (string)seo_config('guarantee_years');
$objects = (string)seo_config('objects');

// --- реальные объекты по этому направлению (внутренняя перелинковка + доказательства) ---
$svcProjects = [];
$projectsFile = dirname(__DIR__, 2) . '/app/Data/projects.php';
if (is_file($projectsFile)) {
	foreach ((array)(require $projectsFile) as $pslug => $pr) {
		$prServices = array_map('strval', (array)($pr['services'] ?? [$pr['service'] ?? '']));
		if (in_array((string)$slug, $prServices, true)) {
			$svcProjects[$pslug] = $pr;
		}
	}
}

// --- объекты в городе/регионе гео-страницы (app/Data/projects.php) ---
// Считаем заранее: от этого зависят и блок «Объекты в регионе», и честная
// подпись галереи (не обещаем «фото из региона», если объекта в регионе нет).
$__geoProjects = [];
if ($geo) {
	$__needle = mb_strtolower($geoName);
	$__region = mb_strtolower((string)($geo['region'] ?? ''));
	$__geoCity = [];
	$__geoRegion = [];
	foreach ($svcProjects as $__pslug => $__pr) {
		$__hay = mb_strtolower(((string)($__pr['region'] ?? '')) . ' ' . ((string)($__pr['title'] ?? '')));
		if (str_contains($__hay, $__needle)) {
			// Точное совпадение по городу — приоритетнее регионального
			$__geoCity[$__pslug] = $__pr;
		} elseif ($__region !== '' && str_contains($__hay, $__region)) {
			$__geoRegion[$__pslug] = $__pr;
		}
	}
	// Если в городе есть свой объект — показываем только его; иначе — объекты региона
	$__geoProjects = $__geoCity ?: $__geoRegion;
}
$__geoHasLocal = $__geoProjects !== [];

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
// Услуги с поставкой комплектов (оснащение школ, БПЛА) не показываем галерею стройки —
// вместо неё блок «Что входит в поставку»: так фото не врут и страница отвечает на интент.
$supply = array_values((array)($svc['supply'] ?? []));
$isSupply = $supply !== [];
$pn = count($pool);
$off = abs(crc32($slug)) % $pn;
$pick = function ($k) use ($pool, $pn, $off) { return '/public/assets/image/services/' . $pool[($off + $k) % $pn]; };
$heroImg = $isSupply ? '/public/assets/image/og/og-home-1200x630.jpg' : $pick(0);

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
// Локальная деталь (маршрут, срок доставки, расстояние от базы) — только в шаге
// «Договор и поставка»: этап выезда на площадку относится именно к поставке.
$geoStep = '';
if ($geo) {
	$geoStep = ' В ' . $geoIn . ' этап выполняем с выездом на площадку: '
		. (string)($geo['eta'] ?? '') . ' на доставку по маршруту '
		. (string)($geo['route'] ?? '') . ', ' . (int)($geo['distance'] ?? 0) . ' км от базы.';
}
$steps = [
	['Заявка и смета за 15 минут', 'Позвоните ' . $phone . ' или оставьте заявку — рассчитаем стоимость и зафиксируем цену в договоре.'],
	['Договор и поставка', $svcType . ': поставка и монтаж по графику, технадзор на каждом этапе.' . $geoStep],
	['Сдача и гарантия', 'Сдаём объект с пакетом документов. Гарантия ' . $guarantee . ' лет, поддержка после сдачи.'],
];
$faq = (array)($svc['faq'] ?? []);
$qr = array_values((array)($svc['queries'] ?? []));
while (count($qr) < 7) { $qr[] = $svcType; }
$intent = trim((string)preg_replace('/^(Коммерческий|Транзакционный)[^:]*:\s*/iu', '', (string)($svc['intent'] ?? '')));
if ($intent === '') { $intent = mb_strtolower($svcType); }
$priceNum = !empty($svc['price_from']) ? number_format((int)$svc['price_from'], 0, ',', ' ') . ' ₽' : '';
$priceDesc = $priceNum !== '' ? 'Ориентир — от ' . $priceNum . ', точную смету даём за 15 минут. Цена в договоре не растёт.' : 'Смету считаем за 15 минут по вашей задаче. Цена в договоре не растёт.';

/**
 * Сквозной номер раздела страницы («07 / Цены»).
 * Нумерация считается в порядке вывода: условные секции (цены, объекты,
 * локальный блок) не оставляют пропусков и не дублируют номера между
 * шаблоном услуги и подключаемыми компонентами (отзывы, связанные услуги).
 */
$fssSec = function (string $label): string {
	static $n = 0;
	$n++;
	return str_pad((string)$n, 2, '0', STR_PAD_LEFT) . ' / ' . $label;
};

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
	// Локальная деталь в description: уникализирует сниппет и повышает CTR по гео-запросу
	if (!empty($geo['distance']) && !empty($geo['eta'])) {
		$seoDesc .= ' Доставка ' . $geo['eta'] . ' ' . $geo['route'] . ', ' . $geo['distance'] . ' км от базы.';
	}
}
$basePrice = !empty($svc['price_from']) ? (int)$svc['price_from'] : 0;
// Таблица цен «за м²» и калькулятор площади — только для услуг с площадной ценой
$areaBased = in_array($slug, ['angar', 'sclad', 'proizvodim_modul', 'kapstroenie', 'kapital', 'administrativnoe', 'obchegitia', 'naves', 'montach', 'proektirovanie', 'postavka'], true);

// --- Оглавление страницы (порядок = порядок секций на странице) ---
// Собираем из фактически выводимых блоков: у услуг без цены за м² нет блока
// «Цены», и ссылка в оглавлении была бы битой.
$toc = [
	['#aud', 'Кому подойдёт'],
	['#lead', 'Смета'],
	['#steps', 'Этапы'],
	['#works', $isSupply ? 'Состав поставки' : 'Наши работы'],
];
if ($basePrice > 0 && $areaBased) {
	$toc[] = ['#ceny', 'Цены'];
}
$toc[] = ['#adv', 'Преимущества'];
if ($faq) {
	$toc[] = ['#faq', 'Вопросы'];
}
if ($geo) {
	$toc[] = ['#local', 'Локально'];
	if ($__geoHasLocal) {
		$toc[] = ['#geo-objects', 'Объекты в регионе'];
	}
	$toc[] = ['#geo', 'География'];
}
$specIcons = ['generatornae' => 'fa-bolt', 'compressor' => 'fa-fan', 'compressor_air' => 'fa-fan', 'gruzoperevozki' => 'fa-truck-fast', 'spectehnika' => 'fa-tractor', 'postavka' => 'fa-boxes-stacked', 'musorbak' => 'fa-trash-can', 'montach' => 'fa-screwdriver-wrench', 'proektirovanie' => 'fa-compass-drafting', 'sclad' => 'fa-warehouse', 'angar' => 'fa-warehouse', 'administrativnoe' => 'fa-building', 'obchegitia' => 'fa-bed', 'modultebel' => 'fa-couch', 'naves' => 'fa-umbrella', 'osnashchenie' => 'fa-school', 'bpla' => 'fa-satellite'];
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
<meta name="theme-color" content="#876b55">
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/public/assets/image/logo/favicon/site.webmanifest" />
<?= seo_canonical_tags() ?>
<?= seo_verify_tags() ?>
<?= seo_og_tags($seoTitle, $seoDesc, $pick(0)) ?>
<link rel="search" type="application/opensearchdescription+xml" title="ФАВОРИТ-СТРОЙ-СНАБ" href="/opensearch.xml">
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
<style> body { font-family: 'DM Sans Variable', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
<link rel="preload" href="/public/assets/distJS/main.js?v=20261018" as="script">
<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css">
<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css"></noscript>
<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
<link rel="stylesheet" href="/public/assets/distCSS/promaintenance.css?v=20261023">
<meta name="yandex-verification" content="27e0ba9980498150" />
<?php include_once __DIR__ . '/sheoma.php'; ?>
<?= seo_analytics() ?>
<?= seo_utm_script() ?>
<?= seo_goals_script() ?>
<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
<style>
/* ============================================================
   Страница услуги — оформление по дизайн-системе главной
   (ProMaintenance: тёмный герой, paper-секции, трекированные
   подписи, крупные заголовки, карточки с фото, ссылки-подчёркивания)
   ============================================================ */

/* ---------- HERO ---------- */
.svc-hero {
	position: relative;
	background: #171310;
	color: #fff;
	overflow: hidden;
	display: flex;
	flex-direction: column;
	justify-content: flex-end;
	min-height: 640px;
}

.svc-hero__bg {
	position: absolute;
	inset: 0;
}

.svc-hero__bg img {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.svc-hero__shade {
	position: absolute;
	inset: 0;
	background: linear-gradient(90deg, rgba(15, 14, 13, .96) 0%, rgba(15, 14, 13, .88) 42%, rgba(15, 14, 13, .58) 100%),
		linear-gradient(180deg, rgba(15, 14, 13, .85) 0%, rgba(15, 14, 13, 0) 38%);
}

.svc-hero__inner {
	position: relative;
	z-index: 1;
	width: 100%;
	padding-top: calc(var(--fss-header-h, 90px) + 64px);
	padding-bottom: 34px;
}

.svc-hero__eyebrow {
	color: rgba(255, 255, 255, .72);
}

.svc-hero__title {
	margin: 24px 0 0;
	font-size: clamp(38px, 5.6vw, 88px);
	font-weight: 450;
	line-height: 1.02;
	letter-spacing: -.055em;
}

.svc-hero__title>span {
	display: block;
}

.svc-hero__title>span+span {
	color: rgba(240, 239, 237, .72);
}

.svc-hero__title .accent-dot {
	display: inline;
}

.svc-hero__lead {
	margin: 26px 0 0;
	max-width: 52ch;
	color: rgba(240, 239, 237, .78);
	font-size: 15px;
	line-height: 1.7;
}

.svc-hero__geo {
	margin: 14px 0 0;
	max-width: 52ch;
	color: rgba(240, 239, 237, .58);
	font-size: 13px;
	line-height: 1.65;
}

.svc-hero__actions {
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	gap: 20px 34px;
	margin-top: 32px;
}

.svc-hero .button {
	background: #fff;
	color: var(--ink);
}

.svc-hero .button:hover {
	background: var(--paper);
}

.svc-hero .text-link {
	color: #fff;
	text-decoration: none;
	border-bottom-color: rgba(255, 255, 255, .38);
}

.svc-hero .text-link:hover {
	border-bottom-color: #fff;
}

/* Хлебные крошки на тёмном фоне */
.svc-hero .fss-breadcrumbs {
	margin-bottom: 26px;
}

.svc-hero .fss-bc-list {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	list-style: none;
	margin: 0;
	padding: 0;
	font-size: 10px;
	letter-spacing: .1em;
	text-transform: uppercase;
}

.svc-hero .fss-bc-item {
	display: flex;
	align-items: center;
	color: rgba(255, 255, 255, .5);
}

.svc-hero .fss-bc-item:not(:last-child):after {
	content: "/";
	margin-left: 8px;
	color: rgba(255, 255, 255, .28);
}

.svc-hero .fss-bc-link {
	color: rgba(255, 255, 255, .5);
	text-decoration: none;
	transition: color .2s;
}

.svc-hero .fss-bc-link:hover {
	color: #fff;
}

.svc-hero .fss-bc-current {
	color: #fff;
}

/* Навигация по разделам */
.svc-toc {
	display: flex;
	flex-wrap: wrap;
	gap: 10px 26px;
	margin-top: 36px;
}

.svc-toc a {
	font-size: 11px;
	letter-spacing: .1em;
	text-transform: uppercase;
	color: rgba(255, 255, 255, .6);
	text-decoration: none;
	border-bottom: 1px solid rgba(255, 255, 255, .2);
	padding-bottom: 6px;
	transition: color .25s, border-color .25s;
}

.svc-toc a:hover {
	color: #fff;
	border-bottom-color: #fff;
}

.svc-geo {
	margin-top: 18px;
	font-size: 12px;
	line-height: 1.9;
	color: rgba(255, 255, 255, .6);
}

.svc-geo a {
	color: #fff;
	text-decoration: underline;
	text-underline-offset: 3px;
}

.svc-hero__foot {
	position: relative;
	z-index: 1;
	width: 100%;
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	gap: 12px 30px;
	padding-top: 22px;
	padding-bottom: 28px;
	border-top: 1px solid rgba(255, 255, 255, .18);
}

.svc-hero__price {
	font-size: 16px;
	font-weight: 500;
	letter-spacing: -.01em;
}

.svc-hero__note {
	font-size: 10px;
	letter-spacing: .1em;
	text-transform: uppercase;
	color: rgba(255, 255, 255, .55);
}

/* ---------- ПОКАЗАТЕЛИ ---------- */
.svc-stats {
	border-top: 1px solid var(--line);
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
}

.svc-stat {
	border-right: 1px solid var(--line);
	padding: 32px 32px 0 0;
	transition: color .35s;
}

.svc-stat:last-child {
	border-right: 0;
}

.svc-stat dd {
	margin: 0;
	letter-spacing: -.055em;
	font-weight: 450;
	font-size: clamp(40px, 4.8vw, 76px);
	line-height: 1;
}

.svc-stat dd>span {
	color: var(--muted);
	vertical-align: top;
	font-size: .3em;
	letter-spacing: .02em;
}

.svc-stat dd .svc-stat__plus {
	color: var(--accent);
	font-size: .46em;
	letter-spacing: 0;
}

.svc-stat dd.svc-stat__text {
	font-size: clamp(28px, 3.2vw, 52px);
	color: var(--muted);
}

.svc-stat dt {
	color: var(--muted);
	margin-top: 20px;
	font-size: 11px;
	letter-spacing: .08em;
	text-transform: uppercase;
}

.svc-stat:hover dt {
	color: var(--ink);
}

/* ---------- СЕКЦИИ ---------- */
.svc-section {
	padding-top: clamp(48px, 5.5vw, 92px);
	padding-bottom: clamp(48px, 5.5vw, 92px);
	scroll-margin-top: 100px;
}

.svc-section+.svc-section {
	padding-top: 0;
}

/* Заголовок раздела на светлом фоне */
.svc-section .work-heading p {
	color: var(--muted);
}

.svc-section .work-heading {
	padding-block: 10px 44px;
}

.svc-section .work-heading h2 {
	max-width: 16ch;
}

/* ---------- КАРТОЧКИ «КОМУ ПОДОЙДЁТ» ---------- */
.svc-aud {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 40px 30px;
	align-items: start;
}

.svc-aud__flat {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 34px 30px;
}

.svc-aud__card {
	min-width: 0;
	display: flex;
	flex-direction: column;
}

.svc-aud__media {
	position: relative;
	display: block;
	aspect-ratio: 4/3;
	border-radius: var(--radius-surface);
	overflow: hidden;
	background: #dedcda;
}

.svc-aud__media img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform .7s var(--ease);
}

.svc-aud__card:hover .svc-aud__media img {
	transform: scale(1.04);
}

.svc-aud__media span {
	position: absolute;
	left: 16px;
	bottom: 16px;
	right: 16px;
	color: #fff;
	font-size: 12px;
	letter-spacing: .04em;
	text-shadow: 0 2px 12px rgba(0, 0, 0, .6);
}

.svc-aud__body {
	padding-top: 20px;
	display: flex;
	flex-direction: column;
	flex: 1;
}

.svc-aud__body h3 {
	margin: 12px 0 0;
	font-size: clamp(21px, 1.9vw, 28px);
	line-height: 1.08;
	letter-spacing: -.04em;
}

.svc-aud__body p {
	margin: 12px 0 0;
	color: var(--muted);
	font-size: 13px;
	line-height: 1.65;
}

.svc-aud__body .text-link {
	margin-top: 18px;
	align-self: flex-start;
	color: inherit;
	text-decoration: none;
}

/* Карточка без фото — как блок преимуществ */
.svc-aud__card--flat {
	border-top: 1px solid var(--ink);
	padding-top: 24px;
}

.svc-aud__card--flat .svc-aud__body {
	padding-top: 0;
}

/* ---------- ЭТАПЫ ---------- */
.svc-steps {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	border-top: 1px solid var(--line);
}

.svc-step {
	padding: 34px 34px 0 0;
	border-right: 1px solid var(--line);
}

/* одинаковый отступ от разделительной полосы с обеих сторон:
   блоки 02 и 03 не должны примыкать к границе вплотную */
.svc-step + .svc-step {
	padding-left: 34px;
}

.svc-step:last-child {
	border-right: 0;
}

.svc-step__n {
	color: var(--muted);
	font-size: 10px;
	letter-spacing: .12em;
	text-transform: uppercase;
}

.svc-step h3 {
	margin: 18px 0 0;
	font-size: clamp(22px, 2.1vw, 32px);
	line-height: 1.08;
	letter-spacing: -.04em;
}

.svc-step p {
	margin: 14px 0 0;
	color: var(--muted);
	font-size: 14px;
	line-height: 1.7;
}

.svc-step__bar {
	margin-top: 26px;
	height: 3px;
	background: var(--line);
	border-radius: 99px;
	overflow: hidden;
}

.svc-step__bar i {
	display: block;
	height: 100%;
	background: var(--brand);
	border-radius: 99px;
}

/* ---------- РАБОТЫ / ОБЪЕКТЫ ---------- */
.svc-works {
	display: grid;
	grid-auto-flow: column;
	grid-auto-columns: min(340px, 82vw);
	gap: 16px;
	overflow-x: auto;
	overflow-y: hidden;
	scroll-snap-type: x mandatory;
	padding: 4px 2px 10px;
	scrollbar-width: none;
}

.svc-works::-webkit-scrollbar {
	display: none;
}

.svc-work {
	margin: 0;
	scroll-snap-align: start;
}

.svc-work__media {
	position: relative;
	display: block;
	aspect-ratio: 1.3;
	border-radius: var(--radius-surface);
	overflow: hidden;
	background: #dedcda;
}

.svc-work__media img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform .7s var(--ease);
}

.svc-work:hover .svc-work__media img {
	transform: scale(1.04);
}

.svc-work__media span {
	position: absolute;
	top: 14px;
	left: 14px;
	padding: 7px 10px;
	border-radius: var(--radius-control);
	background: rgba(20, 19, 18, .42);
	border: 1px solid rgba(255, 255, 255, .3);
	backdrop-filter: blur(16px);
	color: #fff;
	font-size: 9px;
	letter-spacing: .06em;
	text-transform: uppercase;
}

.svc-work__cap {
	margin: 16px 0 0;
	display: flex;
	justify-content: space-between;
	gap: 18px;
	font-size: 10px;
	letter-spacing: .07em;
	text-transform: uppercase;
	color: var(--muted);
}

.svc-wnav {
	display: flex;
	gap: 10px;
	margin-top: 22px;
}

.svc-wnav button {
	width: 52px;
	height: 44px;
	border-radius: var(--radius-control);
	border: 1px solid var(--line);
	background: none;
	color: var(--ink);
	font-size: 15px;
	cursor: pointer;
	transition: background .25s, color .25s, border-color .25s;
	font-family: inherit;
}

.svc-wnav button:hover {
	background: var(--ink);
	color: #fff;
	border-color: var(--ink);
}

/* Сетка объектов / поставок */
.svc-grid {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 30px;
}

/* Плоские панели — как блоки на главной: тонкая линия сверху, без карточки */
.svc-panel {
	border: 0;
	border-top: 1px solid var(--ink);
	border-radius: 0;
	padding: 26px 0 0;
	background: none;
	display: flex;
	flex-direction: column;
}

.svc-panel h3 {
	margin: 14px 0 0;
	font-size: 20px;
	line-height: 1.15;
	letter-spacing: -.03em;
}

.svc-panel p {
	margin: 12px 0 0;
	color: var(--muted);
	font-size: 13.5px;
	line-height: 1.65;
}

.svc-panel__n {
	color: var(--muted);
	font-size: 10px;
	letter-spacing: .1em;
	display: block;
}

/* Локальные условия (гео-страница): список фактов и зоны работ */
.svc-local__list {
	margin: 16px 0 0;
	padding: 0;
	list-style: none;
	border-top: 1px solid var(--line);
}

.svc-local__list li {
	display: flex;
	align-items: flex-start;
	gap: 14px;
	padding: 12px 0;
	border-bottom: 1px solid var(--line);
	color: var(--muted);
	font-size: 13.5px;
	line-height: 1.6;
}

.svc-local__list li::before {
	content: "";
	flex: 0 0 auto;
	width: 5px;
	height: 5px;
	border-radius: 50%;
	background: var(--accent);
	margin-top: 8px;
}

.svc-local__zones {
	display: flex;
	flex-wrap: wrap;
	gap: 8px;
	margin-top: 18px;
}

.svc-local__zones .eyebrow {
	border: 1px solid var(--line);
	border-radius: var(--radius-control);
	padding: 7px 12px;
	color: var(--muted);
}

/* Карточка проекта (портфолио) */
.svc-project {
	display: block;
	text-decoration: none;
	color: inherit;
	min-width: 0;
}

.svc-project__media {
	position: relative;
	display: block;
	aspect-ratio: 1.3;
	border-radius: var(--radius-surface);
	overflow: hidden;
	background: #dedcda;
}

.svc-project__media img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	transition: transform .7s var(--ease);
}

.svc-project:hover .svc-project__media img {
	transform: scale(1.04);
}

.svc-project__zone {
	position: absolute;
	top: 14px;
	left: 14px;
	padding: 7px 10px;
	border-radius: var(--radius-control);
	background: rgba(20, 19, 18, .42);
	border: 1px solid rgba(255, 255, 255, .3);
	backdrop-filter: blur(16px);
	color: #fff;
	font-size: 9px;
	letter-spacing: .06em;
	text-transform: uppercase;
}

.svc-project h3 {
	margin: 16px 0 0;
	font-size: 20px;
	line-height: 1.15;
	letter-spacing: -.03em;
}

.svc-project p {
	margin: 8px 0 0;
	color: var(--muted);
	font-size: 13px;
	line-height: 1.6;
}

.svc-project .text-link {
	margin-top: 14px;
	color: inherit;
	text-decoration: none;
}

/* ---------- ФОРМА СМЕТЫ ---------- */
.svc-lead {
	display: grid;
	grid-template-columns: 1fr 1.05fr;
	gap: clamp(28px, 4vw, 64px);
	align-items: start;
	border: 1px solid var(--line);
	border-radius: var(--radius-surface);
	background: #fff;
	padding: clamp(28px, 4vw, 56px);
}

.svc-lead h3 {
	margin: 0;
	font-size: clamp(28px, 3.2vw, 46px);
	line-height: 1.06;
	letter-spacing: -.045em;
}

.svc-lead p {
	margin: 20px 0 0;
	color: var(--muted);
	font-size: 14.5px;
	line-height: 1.7;
}

.svc-lead__points {
	margin: 26px 0 0;
	padding: 0;
	list-style: none;
	display: grid;
	gap: 12px;
	font-size: 13.5px;
	color: var(--ink-soft);
}

.svc-lead__points li {
	display: flex;
	gap: 12px;
	align-items: flex-start;
}

.svc-lead__points li::before {
	content: "";
	flex: 0 0 6px;
	width: 6px;
	height: 6px;
	margin-top: 8px;
	border-radius: 50%;
	background: var(--brand);
}

.svc-form {
	display: grid;
	gap: 12px;
}

.svc-form .row2 {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 12px;
}

.svc-form input,
.svc-form select {
	width: 100%;
	box-sizing: border-box;
	border: 0;
	border-bottom: 1px solid var(--line);
	background: none;
	border-radius: 0;
	padding: 0 2px;
	height: 52px;
	font-size: 15px;
	font-family: inherit;
	color: var(--ink);
	transition: border-color .25s;
}

.svc-form input:focus,
.svc-form select:focus {
	outline: none;
	border-bottom-color: var(--ink);
}

.svc-form input::placeholder {
	color: #918b85;
}

.svc-send {
	justify-self: start;
	margin-top: 8px;
	background: var(--ink);
	color: #fff;
	border: 0;
	border-radius: var(--radius-control);
	min-height: 52px;
	padding: 15px 22px;
	font: inherit;
	font-size: 13px;
	font-weight: 500;
	cursor: pointer;
	display: inline-flex;
	align-items: center;
	justify-content: space-between;
	gap: 35px;
	transition: background .3s;
}

.svc-send:hover {
	background: #424244;
}

.svc-check {
	display: flex;
	align-items: center;
	gap: 10px;
	font-size: 12.5px;
	color: var(--muted);
	cursor: pointer;
}

.svc-check input {
	width: 18px;
	height: 18px;
	margin: 0;
	accent-color: var(--brand);
	flex: 0 0 18px;
}

.svc-consent {
	font-size: 11.5px;
	color: var(--muted);
}

.svc-consent a {
	text-decoration: underline;
	text-underline-offset: 3px;
}

/* ---------- ЦЕНЫ / КАЛЬКУЛЯТОР ---------- */
/* Таблица цен — тонкие линии, как на главной (без «шапки»-плашки) */
.svc-tablewrap {
	overflow-x: auto;
	border-top: 1px solid var(--line);
	scrollbar-width: none;
	-ms-overflow-style: none;
}

/* Полоса прокрутки под таблицей не нужна — таблица вписывается в экран */
.svc-tablewrap::-webkit-scrollbar {
	display: none;
}

.svc-table {
	width: 100%;
	border-collapse: collapse;
	font-size: 14px;
	min-width: 560px;
}

.svc-table th {
	text-align: left;
	padding: 0 24px 14px 0;
	border-bottom: 1px solid var(--line);
	background: none;
	color: var(--muted);
	font-size: 9px;
	letter-spacing: .12em;
	text-transform: uppercase;
	font-weight: 500;
	white-space: nowrap;
}

.svc-table td {
	padding: 16px 24px 16px 0;
	border-bottom: 1px solid var(--line);
	color: var(--ink-soft);
}

.svc-table tbody tr:last-child td {
	border-bottom: 0;
}

.svc-table td:first-child {
	color: var(--ink);
	font-weight: 500;
}

.svc-note {
	margin: 16px 0 0;
	font-size: 12.5px;
	color: var(--muted);
}

.svc-calc {
	border: 1px solid var(--line);
	border-radius: var(--radius-surface);
	background: #fff;
	padding: clamp(24px, 3vw, 40px);
}

.svc-calc__row {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
	gap: 16px;
	margin-top: 26px;
}

.svc-calc__row label {
	display: flex;
	flex-direction: column;
	gap: 8px;
	font-size: 11px;
	letter-spacing: .08em;
	text-transform: uppercase;
	color: var(--muted);
}

.svc-calc__row input,
.svc-calc__row select {
	width: 100%;
	box-sizing: border-box;
	border: 0;
	border-bottom: 1px solid var(--line);
	background: none;
	padding: 8px 2px;
	height: 46px;
	font-size: 15px;
	font-family: inherit;
	color: var(--ink);
}

.svc-calc__row input:focus,
.svc-calc__row select:focus {
	outline: none;
	border-bottom-color: var(--ink);
}

.svc-calc__out {
	margin-top: 26px;
	padding-top: 22px;
	border-top: 1px solid var(--line);
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: space-between;
	gap: 18px;
	font-size: 14px;
}

.svc-calc__out b {
	font-size: 26px;
	letter-spacing: -.03em;
	color: var(--brand);
}

/* ---------- ПРЕИМУЩЕСТВА ---------- */
.svc-adv {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 30px;
}

.svc-adv__card {
	border-top: 1px solid var(--ink);
	padding-top: 24px;
}

.svc-adv__card h3 {
	margin: 0;
	font-size: clamp(20px, 1.8vw, 26px);
	line-height: 1.12;
	letter-spacing: -.035em;
}

.svc-adv__card p {
	margin: 12px 0 0;
	color: var(--muted);
	font-size: 13.5px;
	line-height: 1.7;
}

.svc-adv__ico {
	width: 42px;
	height: 42px;
	border-radius: var(--radius-control);
	background: var(--ink);
	color: var(--paper);
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 15px;
	margin-bottom: 20px;
}

/* ---------- FAQ ---------- */
.svc-faq {
	border-top: 1px solid var(--ink);
}

.svc-faq details {
	border-bottom: 1px solid var(--line);
}

.svc-faq summary {
	list-style: none;
	cursor: pointer;
	display: flex;
	align-items: center;
	gap: 18px;
	padding-block: 24px;
}

.svc-faq summary::-webkit-details-marker {
	display: none;
}

.svc-faq summary>span {
	color: var(--muted);
	font-size: 10px;
	padding-top: 6px;
}

.svc-faq summary h3 {
	margin: 0;
	font-size: clamp(19px, 1.8vw, 26px);
	line-height: 1.2;
	letter-spacing: -.035em;
}

.svc-faq summary svg {
	flex-shrink: 0;
	margin-left: auto;
	transition: transform .4s var(--ease);
}

.svc-faq details[open] summary svg {
	transform: rotate(45deg);
}

.svc-faq__body {
	padding: 0 44px 26px 28px;
}

.svc-faq__body p {
	margin: 0;
	color: var(--muted);
	font-size: 14px;
	line-height: 1.7;
}

/* ---------- ПРОЧИЕ БЛОКИ (общие компоненты) ---------- */
main {
	overflow-x: clip;
}

main img {
	-webkit-user-drag: none;
	user-select: none;
	-moz-user-select: none;
}

.svc-section #reviews .card-prem,
.svc-section .card-prem {
	border: 1px solid var(--line);
	border-radius: var(--radius-surface);
	box-shadow: none;
	background: #fff;
}

.svc-section .section-heading h2,
#reviews .section-heading h2 {
	font-size: clamp(30px, 3.4vw, 52px);
	letter-spacing: -.05em;
	font-weight: 450;
}

/* ---------- АДАПТИВ ---------- */
@media (width<=1100px) {
	.svc-grid,
	.svc-adv {
		grid-template-columns: repeat(2, minmax(0, 1fr));
	}
}

@media (width<=992px) {
	.svc-stats {
		grid-template-columns: repeat(2, minmax(0, 1fr));
	}

	.svc-stat {
		border-bottom: 1px solid var(--line);
		padding: 26px 24px 26px 0;
	}

	.svc-stat:nth-child(2n) {
		border-right: 0;
		padding-left: 24px;
	}

	.svc-stat:nth-last-child(-n+2) {
		border-bottom: 0;
	}

	.svc-steps {
		grid-template-columns: 1fr;
	}

	.svc-step {
		border-right: 0;
		border-bottom: 1px solid var(--line);
		padding: 28px 0;
	}

	.svc-step + .svc-step {
		padding-left: 0;
	}

	.svc-step:last-child {
		border-bottom: 0;
	}

	.svc-lead {
		grid-template-columns: 1fr;
	}
}

@media (width<=720px) {
	/* Таблица цен на узком экране: шапка переносится по словам, колонки
	   сжимаются — таблица помещается в ширину экрана, без обрезки
	   и без горизонтальной полосы прокрутки под таблицей */
	.svc-table {
		min-width: 0;
		font-size: 12.5px;
		table-layout: fixed;
	}

	.svc-table th {
		padding: 0 12px 10px 0;
		white-space: normal;
		line-height: 1.25;
		letter-spacing: .06em;
		overflow-wrap: anywhere;
	}

	.svc-table td {
		padding: 12px 12px 12px 0;
		word-break: break-word;
	}
}

@media (width<=560px) {
	/* На телефоне таблица цен становится списком карточек:
	   подпись колонки переносится к значению — ничего не обрезается
	   и под таблицей нет полосы прокрутки */
	.svc-table,
	.svc-table tbody,
	.svc-table tr,
	.svc-table td {
		display: block;
		width: 100%;
	}

	.svc-table thead {
		display: none;
	}

	.svc-table tr {
		border-bottom: 1px solid var(--line);
		padding: 14px 0;
	}

	.svc-table tbody tr:last-child {
		border-bottom: 0;
	}

	.svc-table td {
		display: flex;
		align-items: baseline;
		justify-content: space-between;
		gap: 16px;
		padding: 3px 0;
		border: 0;
	}

	.svc-table td::before {
		content: attr(data-label);
		flex: 0 0 auto;
		color: var(--muted);
		font-size: 9px;
		letter-spacing: .1em;
		text-transform: uppercase;
	}

	.svc-table td:first-child {
		font-size: 16px;
		font-weight: 700;
		margin-bottom: 4px;
	}

	.svc-table td:first-child::before {
		content: none;
	}
}

	.svc-hero {
		min-height: 0;
	}

	.svc-hero__inner {
		padding-top: calc(var(--fss-header-h, 74px) + 44px);
		padding-bottom: 28px;
	}

	.svc-hero__title {
		font-size: 12vw;
	}

	.svc-aud {
		grid-template-columns: 1fr;
		gap: 26px;
	}

	.svc-aud__flat {
		grid-template-columns: 1fr;
		gap: 26px;
	}

	.svc-grid,
	.svc-adv {
		grid-template-columns: 1fr;
		gap: 26px;
	}

	.svc-faq__body {
		padding-left: 0;
		padding-right: 10px;
	}
}

@media (prefers-reduced-motion: reduce) {
	.svc-toc a,
	.svc-faq summary svg,
	.svc-step__bar i {
		transition: none;
	}
}
</style>

</head>

<body class="font-sans text-gray-800" data-page="inner">
	<?php
	// Шапка прозрачная в начале: под ней полноэкранный hero услуги (как на главной).
	$GLOBALS['fss_nav_mode'] = 'transparent';
	include_once __DIR__ . '/header.php';
	?>
	<?php $isPriority = in_array($slug, (array)($geoAll['priority_services'] ?? []), true); ?>
	<main>
		<!-- HERO -->
		<section class="svc-hero" aria-labelledby="svc-title" data-nav-hero>
			<div class="svc-hero__bg" aria-hidden="true">
				<img src="<?= $h($heroImg) ?>"<?= seo_img_attrs((string)$heroImg) ?> alt="" fetchpriority="high" decoding="async" onerror="this.style.display='none'">
			</div>
			<div class="svc-hero__shade" aria-hidden="true"></div>
			<div class="svc-hero__inner section-shell">
				<?= seo_breadcrumbs_html() ?>
				<p class="eyebrow svc-hero__eyebrow">
					<span class="live-dot" aria-hidden="true"></span>
					<?= $h($svcType) ?> · С <?= $h((string)seo_config('founded_market')) ?> года · Допуск СРО
				</p>
				<h1 id="svc-title" class="svc-hero__title">
					<span><?= $h($h1a) ?></span>
					<span><?= $h1b !== '' ? $h($h1b) . '<span class="accent-dot">.</span>' : '' ?></span>
				</h1>
				<p class="svc-hero__lead"><?= $h($svc['description']) ?></p>
				<?php if ($geo): ?>
					<p class="svc-hero__geo"><?= $h($svcType) ?> в <?= $h($geoIn) ?> (<?= $h((string)$geo['region']) ?>): доставка конструкций <?= $h((string)$geo['route']) ?> — <?= $h((string)$geo['eta']) ?>. Работаем с юрлицами по договору, цена фиксируется.</p>
				<?php endif; ?>
				<div class="svc-hero__actions">
					<a class="button" href="#lead" data-goal="svc_hero_cta" title="Рассчитать стоимость">Рассчитать стоимость
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</a>
					<a class="text-link" href="tel:<?= $h($phoneHref) ?>" title="Позвонить: <?= $h($phone) ?>"><?= $h($phone) ?></a>
				</div>
				<nav class="svc-toc" aria-label="Разделы страницы">
					<?php foreach ($toc as $__t): ?>
						<a href="<?= $h($__t[0]) ?>" title="<?= $h($__t[1]) ?>"><?= $h($__t[1]) ?></a>
					<?php endforeach; ?>
				</nav>
				<?php if (!$geo && $isPriority && !empty($geoAll['geo'])): ?>
					<nav class="svc-geo" aria-label="Гео-направления"><span>Строим в городах:</span>
						<?php $gi = 0; foreach ($geoAll['geo'] as $gk => $gv): if (!seo_geo_allows((array)$gv, (string)$slug)) { continue; } echo $gi++ ? ' · ' : ''; ?><a href="/services/<?= $h($slug) ?>/<?= $h($gk) ?>" title="<?= $h($gv['name']) ?>"><?= $h($gv['name']) ?></a><?php endforeach; ?>
					</nav>
				<?php endif; ?>
			</div>
			<div class="svc-hero__foot section-shell">
				<span class="svc-hero__price"><?= $h($price) ?></span>
				<span class="svc-hero__note">смета за <?= $h((string)seo_config('price_response_minutes')) ?> минут</span>
				<span class="svc-hero__note">Гарантия <?= $h($guarantee) ?> лет</span>
			</div>
		</section>

		<!-- ПОКАЗАТЕЛИ -->
		<dl class="svc-stats section-shell">
			<div class="svc-stat">
				<dd><?= (int)$deadline ?><span class="svc-stat__unit">дней</span></dd>
				<dt>от старта до сдачи</dt>
			</div>
			<div class="svc-stat">
				<dd><?= (int)preg_replace('/\D/', '', $guarantee) ?><span class="svc-stat__plus">+</span><span class="svc-stat__unit">лет</span></dd>
				<dt>гарантия на конструкции</dt>
			</div>
			<div class="svc-stat">
				<dd><?= (int)preg_replace('/\D/', '', $objects) ?><span class="svc-stat__plus">+</span></dd>
				<dt>сданных объектов</dt>
			</div>
			<div class="svc-stat">
				<?php if ($basePrice > 0): ?>
					<dd><?= htmlspecialchars(number_format($basePrice, 0, ',', ' '), ENT_QUOTES) ?><span class="svc-stat__unit">₽/м²</span></dd>
				<?php else: ?>
					<dd class="svc-stat__text">по смете</dd>
				<?php endif; ?>
				<dt>ориентир по цене</dt>
			</div>
		</dl>

		<?php if (!$geo && $isPriority && !empty($geoAll['geo'])): ?>
			<!-- ДОВЕРИЕ -->
			<section class="svc-section section-shell" id="credentials" aria-labelledby="credentials-title">
				<div class="section-topline">
					<p class="eyebrow"><?= $h($fssSec('Доверие')) ?></p>
					<span class="eyebrow">Росгвардия · Минпросвещение · СРО</span>
				</div>
				<div class="work-heading">
					<h2 id="credentials-title">Почему доверяют<br>госзаказчики и бизнес</h2>
					<p>Экспертиза, подтверждённая Росгвардией, Минпросвещением<br>и выполненными объектами ФСС.</p>
				</div>
				<div class="svc-grid">
					<article class="svc-panel">
						<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
						<h3>Партнёр Росгвардии</h3>
						<p>Официальный поставщик Федеральной службы войск национальной гвардии РФ. Обеспечиваем нужды силовых структур РФ.</p>
					</article>
					<article class="svc-panel">
						<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-school"></i></span>
						<h3>Стандарты Минпросвещения</h3>
						<p>Комплексное оснащение <?= (int)seo_config('schools_equipped') ?> школ по стандартам Минпросвещения РФ: мебель, оборудование, лаборатории, мультимедиа.</p>
					</article>
					<article class="svc-panel">
						<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-certificate"></i></span>
						<h3>СРО и допуски</h3>
						<p>Член СРО проектирования и строительства. Работаем по 44-ФЗ и 223-ФЗ. Полный пакет закрывающих документов.</p>
					</article>
					<article class="svc-panel">
						<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-building"></i></span>
						<h3><?= $h($objects) ?> сданных объектов</h3>
						<p>С <?= $h((string)seo_config('founded_market')) ?> года сдаём объекты под ключ: общежития, школы, ангары, АБК, склады, БВЗ под ключ.</p>
					</article>
					<article class="svc-panel">
						<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
						<h3>Гарантия <?= $h($guarantee) ?> лет</h3>
						<p>Официальная гарантия на несущие конструкции. Фиксированная цена и сроки в договоре.</p>
					</article>
					<article class="svc-panel">
						<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-truck-fast"></i></span>
						<h3>Своя логистика и техника</h3>
						<p>Свой автопарк, манипуляторы, спецтехника. Доставка и монтаж по Москве, МО и всей России.</p>
					</article>
				</div>
			</section>
		<?php endif; ?>

		<!-- КОМУ ПОДОЙДЁТ -->
		<section class="svc-section section-shell" id="aud" aria-labelledby="aud-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Кому подойдёт')) ?></p>
				<span class="eyebrow"><?= $h($svcType) ?></span>
			</div>
			<div class="work-heading">
				<h2 id="aud-title">Кому подойдёт<br><?= $geo ? 'работа в ' . $h($geoIn) : 'эта услуга' ?></h2>
				<p>Если узнали свою задачу — рассчитаем точную смету за 15 минут<br>и зафиксируем цену в договоре.</p>
			</div>
			<?php $audDesc = ['Замер, проект и точная смета за 15 минут', 'Фиксированная цена в договоре', 'Сдача с документами и гарантией', 'Работаем по Москве, МО и всей России']; ?>
			<div class="svc-aud">
				<article class="svc-aud__card svc-aud__card--photo">
					<a class="svc-aud__media" href="/uslugi" title="Все услуги ФСС">
						<img src="<?= $h($isSupply ? $heroImg : $pick(4)) ?>" alt="<?= $h($aud[0]) ?>" title="<?= $h($aud[0]) ?>" loading="lazy" decoding="async" onerror="this.style.display='none'">
						<span><?= $h($aud[0]) ?></span>
					</a>
					<div class="svc-aud__body">
						<p class="eyebrow" style="color:#876b55">Задача</p>
						<h3><?= $h($aud[0]) ?></h3>
						<p><?= $h($svcType) ?> под ключ — от замера площадки до сдачи объекта.</p>
						<a class="text-link" href="/portfolio" title="Посмотреть портфолио объектов">Примеры работ
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				</article>
				<div class="svc-aud__flat">
					<?php for ($i = 1; $i <= 4; $i++): ?>
						<article class="svc-aud__card svc-aud__card--flat">
							<div class="svc-aud__body">
								<p class="eyebrow" style="color:#876b55">0<?= $i ?></p>
								<h3><?= $h($aud[$i]) ?></h3>
								<p><?= $h($audDesc[($i - 1) % 4]) ?></p>
							</div>
						</article>
					<?php endfor; ?>
				</div>
			</div>
		</section>

		<!-- СМЕТА -->
		<section class="svc-section section-shell" id="lead" aria-labelledby="lead-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Смета')) ?></p>
				<span class="eyebrow">Ответ за 7 минут</span>
			</div>
			<div class="svc-lead">
				<div>
					<h3 id="lead-title">Оцените стоимость<br>за пару минут</h3>
					<p>Оставьте заявку — <b>инженер перезвонит за <?= (int)seo_config('callback_minutes') ?> минут</b>, рассчитает смету за <?= (int)seo_config('price_response_minutes') ?> минут и зафиксирует цену в договоре.</p>
					<ul class="svc-lead__points">
						<li>Смета по вашему ТЗ — без «плавающих» доплат</li>
						<li>Фиксированная цена: в договоре она не растёт</li>
						<li>Полный пакет закрывающих документов и гарантия <?= $h($guarantee) ?> лет</li>
					</ul>
				</div>
				<form class="svc-form js-svcalc" method="POST" action="/send/email" data-goal="lead_<?= $h($slug) ?>" novalidate>
					<input type="hidden" name="content_form">
					<?php $abPath = __DIR__ . '/antibot_fields.php'; if (file_exists($abPath)) include $abPath; ?>
					<input type="text" name="name" placeholder="Ваше имя" autocomplete="name" required>
					<div class="row2">
						<input type="tel" name="phone" placeholder="Телефон" autocomplete="tel" inputmode="tel" required>
						<input type="text" name="area" inputmode="decimal" placeholder="Площадь, м²">
					</div>
					<input type="email" name="email" placeholder="Email (необязательно)" autocomplete="email" maxlength="254">
					<select name="otype" aria-label="Что нужно">
						<option value="">Что нужно</option>
						<?php foreach (array_slice($aud, 0, 4) as $o): ?><option><?= $h($o) ?></option><?php endforeach; ?>
					</select>
					<input type="hidden" name="message" value="<?= $h($title) ?> — нужны сроки и цена">
					<label class="svc-check"><input type="checkbox" name="checkbox" required> <span>Согласен(а) на обработку персональных данных</span></label>
					<button type="submit" class="svc-send">Отправить заявку
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</button>
					<div class="svc-consent">Нажимая на кнопку, я соглашаюсь на <a href="/politika" title="Политика конфиденциальности">обработку персональных данных</a></div>
				</form>
			</div>
			<script>
				(function () {
					document.querySelectorAll('main img').forEach(function (img) {
						img.draggable = false;
						img.addEventListener('dragstart', function (e) { e.preventDefault(); });
						img.addEventListener('contextmenu', function (e) { e.preventDefault(); });
					});
				})();
				(function () {
					document.querySelectorAll('.js-svcalc').forEach(function (frm) {
						frm.addEventListener('submit', function () {
							var m = frm.querySelector('input[name="message"]');
							var a = frm.querySelector('input[name="area"]');
							var o = frm.querySelector('select[name="otype"]');
							var add = '';
							if (a && a.value.trim()) add += ' Площадь: ' + a.value.trim() + ' м².';
							if (o && o.value) add += ' Нужно: ' + o.value + '.';
							if (add && m) m.value += add;
						});
					});
				})();
			</script>
		</section>

		<!-- ЭТАПЫ РАБОТЫ -->
		<section class="svc-section section-shell" id="steps" aria-labelledby="steps-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Этапы работы')) ?></p>
				<span class="eyebrow">3 шага до сдачи</span>
			</div>
			<div class="work-heading">
				<h2 id="steps-title">Как мы<br>работаем</h2>
				<p>Три шага от заявки до сдачи — сроки фиксируем в договоре<?= $geo ? ' и работаем с выездом в ' . $h($geoIn) : '' ?>.</p>
			</div>
			<div class="svc-steps">
				<?php $pct = [33, 66, 100]; foreach ($steps as $k => $st): ?>
					<article class="svc-step">
						<p class="svc-step__n">0<?= $k + 1 ?> / 03</p>
						<h3><?= $h($st[0]) ?></h3>
						<p><?= $h($st[1]) ?></p>
						<div class="svc-step__bar" aria-hidden="true"><i style="width:<?= $pct[$k] ?>%"></i></div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- РАБОТЫ / ПОСТАВКА -->
		<section class="svc-section section-shell" id="works" aria-labelledby="works-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec($isSupply ? 'Состав поставки' : 'Примеры работ')) ?></p>
				<span class="eyebrow"><?= $h($svcType) ?></span>
			</div>
			<?php if ($isSupply): ?>
				<div class="work-heading">
					<h2 id="works-title">Что входит<br>в поставку</h2>
					<p><?= $h($svcType) ?> — состав комплекта под ключ.</p>
				</div>
				<div class="svc-grid">
					<?php foreach ($supply as $si => $sp): ?>
						<article class="svc-panel">
							<span class="svc-panel__n"><?= $si + 1 ?></span>
							<h3><?= $h($sp[0]) ?></h3>
							<p><?= $h($sp[1]) ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="work-heading">
					<h2 id="works-title">Примеры<br>наших работ</h2>
					<p><?= $h($svcType) ?> — реальные фото с объектов ФСС<?= $geo ? ($__geoHasLocal ? ' в ' . $h($geoName) : ' в Москве и Подмосковье') : '' ?>.</p>
				</div>
				<div class="svc-works" id="svWorks">
					<?php foreach ($works as $wi => $wf): ?>
						<figure class="svc-work">
							<div class="svc-work__media">
								<img src="/public/assets/image/services/<?= $h($wf) ?>" alt="<?= $h($svcType) ?> — пример работ <?= $wi + 1 ?>" title="<?= $h($svcType) ?> — пример работ <?= $wi + 1 ?>" loading="lazy" decoding="async" onerror="this.closest('.svc-work').style.display='none'">
								<span>Фото <?= $wi + 1 ?></span>
							</div>
							<figcaption class="svc-work__cap"><span><?= $h($svcType) ?></span><span>ФСС</span></figcaption>
						</figure>
					<?php endforeach; ?>
				</div>
				<div class="svc-wnav">
					<button type="button" data-wv="-1" aria-label="Назад"><i class="fa-solid fa-arrow-left"></i></button>
					<button type="button" data-wv="1" aria-label="Вперёд"><i class="fa-solid fa-arrow-right"></i></button>
				</div>
				<script>
					(function () {
						var box = document.getElementById('svWorks');
						if (!box) return;
						var btns = document.querySelectorAll('[data-wv]');
						btns.forEach(function (b) {
							b.addEventListener('click', function () {
								var card = box.querySelector('.svc-work');
								var w = card ? card.getBoundingClientRect().width + 16 : 360;
								box.scrollBy({ left: parseInt(b.getAttribute('data-wv'), 10) * w, behavior: 'smooth' });
							});
						});
					})();
				</script>
			<?php endif; ?>
		</section>

		<?php if ($svcProjects && !$geo): ?>
			<!-- ОБЪЕКТЫ ПО НАПРАВЛЕНИЮ -->
			<section class="svc-section section-shell" id="objects" aria-labelledby="objects-title">
				<div class="section-topline">
					<p class="eyebrow"><?= $h($fssSec('Объекты')) ?></p>
					<span class="eyebrow">Реальные кейсы ФСС</span>
				</div>
				<div class="work-heading">
					<h2 id="objects-title">Выполненные объекты<br>по направлению</h2>
					<p>Кейсы компании по услуге «<?= $h($svcType) ?>» — с задачей, решением и результатом.</p>
				</div>
				<div class="svc-grid">
					<?php foreach ($svcProjects as $pslug => $pr):
						$prPhoto = !empty($pr['photos'][0]) ? \App\Models\Image::publicUrl((string)$pr['photos'][0]) : '';
						$prAlt = !empty($pr['photo_alts'][0]) ? (string)$pr['photo_alts'][0] : (string)($pr['h1'] ?? $pslug);
					?>
						<a class="svc-project" href="/portfolio/<?= $h((string)$pslug) ?>" title="<?= $h((string)($pr['h1'] ?? $pslug)) ?>">
							<span class="svc-project__media">
								<?php if ($prPhoto !== ''): ?><img src="<?= $h($prPhoto) ?>"<?= seo_img_attrs($prPhoto) ?> alt="<?= $h($prAlt) ?>" loading="lazy" decoding="async"><?php endif; ?>
								<?php if (!empty($pr['zone'])): ?><span class="svc-project__zone"><?= $h((string)$pr['zone']) ?></span><?php endif; ?>
							</span>
							<h3><?= $h((string)($pr['h1'] ?? $pslug)) ?></h3>
							<p><?= $h((string)($pr['region'] ?? '')) ?></p>
							<span class="text-link">Открыть кейс
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
									<path d="M7 7h10v10"></path>
									<path d="M7 17 17 7"></path>
								</svg>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
				<p class="svc-note">Всего по компании выполнено <?= $h($objects) ?> объектов. <a href="/portfolio" title="Портфолио объектов" style="text-decoration:underline;text-underline-offset:3px">Смотреть все проекты</a>.</p>
			</section>
		<?php endif; ?>

		<?php if ($basePrice > 0 && $areaBased): ?>
			<!-- ЦЕНЫ -->
			<section class="svc-section section-shell" id="ceny" aria-labelledby="ceny-title">
				<div class="section-topline">
					<p class="eyebrow"><?= $h($fssSec('Цены')) ?></p>
					<span class="eyebrow">Ориентир по площади</span>
				</div>
				<div class="work-heading">
					<h2 id="ceny-title">Цены<br>по площади</h2>
					<p>Ориентировочные цены «под ключ»<?= $geo ? ' в ' . $h($geoIn) : '' ?> — точную смету считаем за 15 минут.</p>
				</div>
				<div class="svc-tablewrap">
					<table class="svc-table">
						<thead>
							<tr>
								<th>Площадь</th>
								<th>Базовая комплектация</th>
								<th>Утеплённая / под ключ</th>
								<th>Срок</th>
							</tr>
						</thead>
					<tbody>
						<?php
						// Пробелы в числах — неразрывные, чтобы сумма не рвалась на узких экранах
						$nb = static fn(string $s): string => str_replace(' ', "\u{00A0}", $s);
						foreach ([200, 300, 500, 1000] as $a): $cold = $a * $basePrice; $warm = (int)round($cold * 1.28); ?>
							<tr>
								<td data-label="Площадь"><?= $nb(number_format($a, 0, ',', ' ')) ?> м²</td>
								<td data-label="Базовая комплектация">от <?= $nb(number_format($cold, 0, ',', ' ')) ?> ₽</td>
								<td data-label="Утеплённая / под ключ">от <?= $nb(number_format($warm, 0, ',', ' ')) ?> ₽</td>
								<td data-label="Срок">от <?= $h($deadline) ?> дней</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					</table>
				</div>
				<p class="svc-note">* Ориентир «от <?= number_format($basePrice, 0, ',', ' ') ?> ₽/м²». Итог зависит от пролёта, нагрузок и инженерных систем — фиксируем в договоре.</p>
			</section>

			<!-- КАЛЬКУЛЯТОР -->
			<section class="svc-section section-shell" id="calc" aria-labelledby="calc-title">
				<div class="section-topline">
					<p class="eyebrow"><?= $h($fssSec('Калькулятор')) ?></p>
					<span class="eyebrow">Прикидка за 10 секунд</span>
				</div>
				<div class="work-heading">
					<h2 id="calc-title">Калькулятор<br>стоимости</h2>
					<p>Прикидка за 10 секунд — точный расчёт инженер даёт за 15 минут.</p>
				</div>
				<div class="svc-calc">
					<div class="svc-calc__row" data-base="<?= $basePrice ?>">
						<label>Площадь, м²
							<input type="number" min="20" step="10" value="300" data-calc-area>
						</label>
						<label>Комплектация
							<select data-calc-type>
								<option value="1">Холодная (без утепления)</option>
								<option value="1.28">Утеплённая</option>
								<option value="1.35">Тёплая с инженерией и отделкой</option>
							</select>
						</label>
					</div>
					<div class="svc-calc__out">
						<span>Ориентировочная стоимость: <b data-calc-out>—</b></span>
						<a class="button" href="#lead" data-goal="calc_send" data-calc-send title="Отправить расчёт инженеру">Отправить расчёт инженеру
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					</div>
				</div>
				<script>
					(function () {
						var box = document.querySelector('#calc [data-base]');
						if (!box) return;
						var base = parseInt(box.getAttribute('data-base'), 10) || 0;
						var area = box.querySelector('[data-calc-area]');
						var type = box.querySelector('[data-calc-type]');
						var out = document.querySelector('#calc [data-calc-out]');
						function fmt(n) { return n.toLocaleString('ru-RU') + ' ₽'; }
						function calc() {
							var a = Math.max(0, parseFloat(area.value) || 0);
							var k = parseFloat(type.value) || 1;
							var lo = a * base * k, hi = lo * 1.15;
							out.textContent = lo ? '≈ ' + fmt(Math.round(lo)) + ' – ' + fmt(Math.round(hi)) : '—';
						}
						area.addEventListener('input', calc);
						type.addEventListener('change', calc);
						calc();
						var send = document.querySelector('#calc [data-calc-send]');
						if (send) send.addEventListener('click', function () {
							var lf = document.querySelector('#lead input[name="area"]');
							if (lf) lf.value = area.value;
						});
					})();
				</script>
			</section>
		<?php endif; ?>

		<!-- ПРЕИМУЩЕСТВА -->
		<section class="svc-section section-shell" id="adv" aria-labelledby="adv-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Преимущества')) ?></p>
				<span class="eyebrow">Почему выбирают нас</span>
			</div>
			<div class="work-heading">
				<h2 id="adv-title">Почему выбирают<br><?= $geo ? 'ФСС в ' . $h($geoIn) : 'ФСС' ?></h2>
				<p>Фиксированная цена, сроки в договоре и гарантия<br>на несущие конструкции.</p>
			</div>
			<div class="svc-adv">
				<article class="svc-adv__card">
					<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></span>
					<h3>Фиксированная цена</h3>
					<p><?= $h($priceDesc) ?></p>
				</article>
				<article class="svc-adv__card">
					<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-drafting-compass"></i></span>
					<h3>Сроки от <?= $h($deadline) ?> дней</h3>
					<p><?= $h($intent) ?> — от заявки до сдачи, график фиксируем в договоре.</p>
				</article>
				<article class="svc-adv__card">
					<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-medal"></i></span>
					<h3>Гарантия <?= $h($guarantee) ?> лет</h3>
					<p>Несущие конструкции и узлы — официальная гарантия по договору.</p>
				</article>
				<article class="svc-adv__card">
					<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid <?= $h($specIcon) ?>"></i></span>
					<h3><?= $h($pretty($qr[5])) ?></h3>
					<p>Полный цикл: замер, проект, производство, доставка, монтаж.</p>
				</article>
				<article class="svc-adv__card">
					<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-truck-fast"></i></span>
					<h3><?= $h($pretty($qr[6])) ?></h3>
					<p>Рассчитаем за 15 минут — <?= $h($phone) ?>, инженер на связи.</p>
				</article>
				<article class="svc-adv__card">
					<span class="svc-adv__ico" aria-hidden="true"><i class="fa-solid fa-comment-dots"></i></span>
					<h3>Нужна смета?</h3>
					<p><a class="text-link" href="#lead" title="Получить смету за 15 минут">Получить за 15 минут</a></p>
				</article>
			</div>
		</section>

		<?php if ($faq): ?>
			<!-- FAQ -->
			<section class="svc-section section-shell" id="faq" aria-labelledby="faq-title">
				<div class="section-topline">
					<p class="eyebrow"><?= $h($fssSec('Вопросы')) ?></p>
					<span class="eyebrow">Отвечаем честно</span>
				</div>
				<div class="work-heading">
					<h2 id="faq-title">Частые вопросы<?= $geo ? ' о работах в ' . $h($geoIn) : '' ?></h2>
					<p>Остальное подскажет инженер — оставьте заявку и задайте вопрос напрямую.</p>
				</div>
				<?php
				// На гео-странице сокращаем общие вопросы услуги: локальных вопросов города
				// больше, а полный блок вопросов услуги дублируется на всех гео-страницах
				// одного направления (лишний дубль контента).
				$__faqList = $geo ? array_merge(array_slice($faq, 0, 3), $geoFaq) : array_merge($faq, $geoFaq);
				?>
				<div class="svc-faq">
					<?php foreach ($__faqList as $fi => $f): ?>
						<details<?= $fi === 0 ? ' open' : '' ?>>
							<summary>
								<span><?= str_pad((string)($fi + 1), 2, '0', STR_PAD_LEFT) ?></span>
								<h3><?= $h($f[0]) ?></h3>
								<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true" focusable="false">
									<path d="M12 5v14"></path>
									<path d="M5 12h14"></path>
								</svg>
							</summary>
							<div class="svc-faq__body"><p><?= $h($f[1]) ?></p></div>
						</details>
					<?php endforeach; ?>
				</div>
				<div style="margin-top:26px"><a class="text-link" href="#lead" title="Получить консультацию">Получить консультацию по другим вопросам
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</a></div>
			</section>
		<?php endif; ?>

		<?php if ($geo && !empty($geoAll['geo'])): ?>
			<!-- ЛОКАЛЬНЫЕ УСЛОВИЯ -->
			<section class="svc-section section-shell" id="local" aria-labelledby="local-title">
				<div class="section-topline">
					<p class="eyebrow"><?= $h($fssSec('Локально')) ?></p>
					<span class="eyebrow"><?= $h($geoName) ?></span>
				</div>
				<div class="work-heading">
					<h2 id="local-title"><?= $h($svcType) ?><br>в <?= $h($geoIn) ?></h2>
					<p>Локальные условия: маршрут, срок доставки, зоны работ.</p>
				</div>
				<div class="svc-grid">
					<article class="svc-panel">
						<h3>Доставка и монтаж</h3>
						<p><?= $h((string)($geo['delivery'] ?? '')) ?></p>
						<ul class="svc-local__list">
							<li>Расстояние от базы: <?= (int)($geo['distance'] ?? 0) ?> км</li>
							<li>Маршрут: <?= $h((string)($geo['route'] ?? '')) ?></li>
							<li>Срок доставки на объект: <?= $h((string)($geo['eta'] ?? '')) ?></li>
						</ul>
					</article>
					<article class="svc-panel">
						<h3>Зоны работ</h3>
						<p><?= $h((string)($geo['demand'] ?? '')) ?></p>
						<?php if ($geoZones): ?>
						<div class="svc-local__zones">
							<?php foreach ($geoZones as $zn): ?><span class="eyebrow"><?= $h($zn) ?></span><?php endforeach; ?>
						</div>
						<?php endif; ?>
					</article>
					<article class="svc-panel">
						<h3>Что строим в <?= $h($geoName) ?></h3>
						<p><?= $h((string)($svc['local'] ?? $svc['description'])) ?></p>
						<p><?= $h((string)($geo['local_text'] ?? '')) ?></p>
						<p>В <?= $h($geoIn) ?> (<?= $h((string)($geo['region'] ?? '')) ?>) <?= $h(mb_strtolower((string)$svcType)) ?> ведём <?= $h((string)($geo['within'] ?? 'районе')) ?>: <?= $h((string)($geo['route'] ?? '')) ?>, до объекта — <?= $h((string)($geo['eta'] ?? '')) ?>. Смету считаем по вашему ТЗ, цену фиксируем в договоре.</p>
						<?php if ($basePrice > 0): ?>
							<p>Ориентир по <?= $h($geoName) ?>: от <?= number_format($basePrice, 0, ',', ' ') ?> ₽/м². Логистику и монтаж считаем по маршруту, объёму поставки и условиям площадки — точная цифра в смете за 15 минут.</p>
						<?php endif; ?>
					</article>
				</div>
			</section>
		<?php endif; ?>

		<?php if ($geo && !empty($geoAll['geo'])): ?>
			<?php
			// Реальные объекты в этом городе/регионе — из app/Data/projects.php (посчитано выше).
			// Это уникальный для гео-страницы контент (плюс перелинковка «услуга → кейс»).
			?>
			<?php if ($__geoProjects): ?>
				<section class="svc-section section-shell" id="geo-objects" aria-labelledby="geo-objects-title">
					<div class="section-topline">
						<p class="eyebrow"><?= $h($fssSec('Объекты в регионе')) ?></p>
						<span class="eyebrow"><?= $h($geoName) ?></span>
					</div>
					<div class="work-heading">
						<h2 id="geo-objects-title">Выполненные объекты<br>в <?= $h($geoName) ?></h2>
						<p>Кейсы компании в этом городе и регионе — с задачей, решением и результатом.</p>
					</div>
					<div class="svc-grid">
						<?php foreach ($__geoProjects as $__pslug => $__pr):
							$__photo = !empty($__pr['photos'][0]) ? \App\Models\Image::publicUrl((string)$__pr['photos'][0]) : '';
							$__alt = !empty($__pr['photo_alts'][0]) ? (string)$__pr['photo_alts'][0] : (string)($__pr['h1'] ?? $__pslug);
						?>
							<a class="svc-project" href="/portfolio/<?= $h((string)$__pslug) ?>" title="<?= $h((string)($__pr['h1'] ?? $__pslug)) ?>">
								<span class="svc-project__media">
									<?php if ($__photo !== ''): ?><img src="<?= $h($__photo) ?>"<?= seo_img_attrs($__photo) ?> alt="<?= $h($__alt) ?>" loading="lazy" decoding="async"><?php endif; ?>
									<?php if (!empty($__pr['zone'])): ?><span class="svc-project__zone"><?= $h((string)$__pr['zone']) ?></span><?php endif; ?>
								</span>
								<h3><?= $h((string)($__pr['h1'] ?? $__pslug)) ?></h3>
								<p><?= $h((string)($__pr['region'] ?? '')) ?></p>
								<span class="text-link">Открыть кейс
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
										<path d="M7 7h10v10"></path>
										<path d="M7 17 17 7"></path>
									</svg>
								</span>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<section class="svc-section section-shell" id="geo" aria-labelledby="geo-title">
				<div class="section-topline">
					<p class="eyebrow"><?= $h($fssSec('География')) ?></p>
					<span class="eyebrow">Москва · МО · Регионы</span>
				</div>
				<div class="work-heading">
					<h2 id="geo-title">Другие направления<br><?= $h(trim($h1a)) ?></h2>
					<p>Строим и доставляем по Москве, Московской области и регионам с выполненными объектами.</p>
				</div>
				<nav aria-label="Гео-направления" style="display:flex;flex-wrap:wrap;gap:10px 26px">
					<?php foreach ($geoAll['geo'] as $gk => $gv):
						if ($gk === $geoKey) {
							continue;
						}
						// Ссылаемся только на те города, где эта услуга реально выполнялась
						if (!seo_geo_allows((array)$gv, (string)$slug)) {
							continue;
						}
					?>
						<a class="text-link" href="/services/<?= $h($slug) ?>/<?= $h($gk) ?>" title="<?= $h($gv['name']) ?>"><?= $h($gv['name']) ?>
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
								<path d="M7 7h10v10"></path>
								<path d="M7 17 17 7"></path>
							</svg>
						</a>
					<?php endforeach; ?>
				</nav>
			</section>
		<?php endif; ?>

		<!-- CTA (баннер-CTA сайта — сохраняем исходный блок) -->
		<section class="cta-band view">
			<img src="/public/assets/image/Components/banner/hero.jpg" alt="Быстровозводимые здания ФСС — строим то, что ведёт вас вперёд" title="Быстровозводимые здания ФСС" loading="lazy" decoding="async" onerror="this.style.display='none'">
			<div class="cta-in">
				<span class="cta-eyebrow"><i></i>Ваш проект — наше дело</span>
				<h2>Строим то,<br>что ведёт вас вперёд</h2>
				<p><?= $h($svcType) ?> под ключ: проектируем, производим и монтируем — с фиксированной ценой в договоре.</p>
				<a href="#lead" class="cta-pill" data-goal="svc_band_cta" title="Построить с нами">Построить с нами <i class="fa-solid fa-arrow-right"></i></a>
			</div>
		</section>

		<?php include_once __DIR__ . '/reviews_services.php'; ?>
	</main>

	<?php include_once __DIR__ . '/related_services.php'; ?>
	<?php include_once __DIR__ . '/footer.php'; ?>
</body>

</html>
