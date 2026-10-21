<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

/* ============================================================
   /contacts — отдельная страница «Контакты».
   Оформление — по дизайн-системе главной (ProMaintenance):
   тёмный герой, paper-секции, тонкие линии, трекированные подписи,
   крупная типографика, акцент — тёплый песочный.
   Все контакты и реквизиты — из app/Config/site.php (единый источник правды).
   ============================================================ */

$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };

$base = rtrim((string)seo_config('base_url'), '/');
$inn = (string)seo_config('inn');
$kpp = (string)seo_config('kpp');
$ogrn = (string)seo_config('ogrn');
$ceo = (string)seo_config('ceo');
$founded = (string)seo_config('founded');
$regDate = (string)seo_config('registered_at');
$addr = (array)seo_config('address');
$phone = (string)seo_config('phone_display');
$phoneHref = preg_replace('/\D/', '', (string)seo_config('phone'));
$email = (string)seo_config('email');
$emailHref = (string)seo_config('email_puny');
$telegram = (string)seo_config('telegram');
$vk = (string)seo_config('vk');
$addrFull = trim((string)($addr['locality'] ?? '') . ', ' . (string)($addr['street'] ?? '') . ', ' . (string)($addr['postal'] ?? ''), ' ,');

// Сквозной номер раздела страницы («01 / Способы связи»).
$fssSec = function (string $label): string {
	static $n = 0;
	$n++;
	return str_pad((string)$n, 2, '0', STR_PAD_LEFT) . ' / ' . $label;
};

$seoTitle = 'Контакты — телефон, адрес, реквизиты | ФАВОРИТ-СТРОЙ-СНАБ';
$seoDesc = 'Контакты ФАВОРИТ-СТРОЙ-СНАБ: телефон ' . $phone . ', почта ' . $email . ', офис и производство — Москва, пер. Большой Спасоглинищевский, д. 9/1 стр. 10. Реквизиты, режим работы и заявка на расчёт.';

/* Регионы, где работаем (area_served) + гео-направления с реальными объектами:
   страница работает как хаб — ведёт и в услуги, и в гео-страницы. */
$areaServed = array_values((array)seo_config('area_served'));
$geoAll = function_exists('seo_geo') ? seo_geo() : ['geo' => [], 'priority_services' => []];
$geoRows = [];
foreach ((array)($geoAll['geo'] ?? []) as $gk => $gv) {
	$slug = '';
	foreach ((array)($geoAll['priority_services'] ?? []) as $ps) {
		if (seo_geo_allows((array)$gv, (string)$ps)) {
			$slug = (string)$ps;
			break;
		}
	}
	if ($slug === '') {
		continue;
	}
	$geoRows[] = [
		'name' => (string)($gv['name'] ?? $gk),
		'region' => (string)($gv['region'] ?? ''),
		'url' => '/services/' . $slug . '/' . $gk,
	];
}

/* Короткие вопросы о работе с нами — уникальный текст страницы. */
$faq = [
	['Как быстро вы перезвоните?', 'Инженер перезванивает за ' . (string)seo_config('callback_minutes') . ' минут в рабочее время, смету по вашему ТЗ считаем за ' . (string)seo_config('price_response_minutes') . ' минут. Цену фиксируем в договоре — в процессе работ она не растёт.'],
	['Можно ли приехать на производство лично?', 'Да. Позвоните заранее — согласуем время визита и покажем цех и склад готовой продукции, чтобы вы оценили качество до подписания договора.'],
	['Работаете ли вы с организациями и по 44-ФЗ?', 'Да. Работаем с юридическими лицами, ИП, государственными и муниципальными заказчиками по 44-ФЗ и 223-ФЗ: допуск СРО, полный пакет исполнительной и закрывающей документации.'],
	['Вы работаете только в Москве?', 'Нет. База в Москве, объекты сдаём в Москве и Московской области, а также в регионах с подтверждёнными работами: Краснодарский край, Ростовская и Чеченская Республики. Конструкции везём транзитом по всей России.'],
];
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $h($seoTitle) ?></title>
	<meta name="description" content="<?= $h(seo_clip($seoDesc, 158)) ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#876b55">
	<meta name="format-detection" content="telephone=yes">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<link rel="manifest" href="/manifest.json" />
	<?= seo_canonical_tags() ?>
<?= seo_verify_tags() ?>
	<?= seo_og_tags($seoTitle, $seoDesc, '/public/assets/image/og/og-home-1200x630.jpg') ?>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/promaintenance.css?v=20261023">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<?= seo_analytics() ?>
	<?= seo_utm_script() ?>
	<?= seo_goals_script() ?>
	<style>
		/* ============================================================
		   /contacts — оформление по дизайн-системе главной (ProMaintenance)
		   ============================================================ */

		/* ---------- HERO ---------- */
		.ct-hero {
			position: relative;
			background: #171310;
			color: #fff;
			overflow: hidden;
			display: flex;
			flex-direction: column;
			justify-content: flex-end;
			min-height: 560px;
		}

		.ct-hero__bg {
			position: absolute;
			inset: 0;
		}

		.ct-hero__bg img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.ct-hero__shade {
			position: absolute;
			inset: 0;
			background: linear-gradient(90deg, rgba(15, 14, 13, .96) 0%, rgba(15, 14, 13, .88) 42%, rgba(15, 14, 13, .55) 100%),
				linear-gradient(180deg, rgba(15, 14, 13, .85) 0%, rgba(15, 14, 13, 0) 38%);
		}

		.ct-hero__inner {
			position: relative;
			z-index: 1;
			width: 100%;
			padding-top: calc(var(--fss-header-h, 90px) + 64px);
			padding-bottom: 34px;
		}

		.ct-hero__eyebrow {
			color: rgba(255, 255, 255, .72);
		}

		.ct-hero__title {
			margin: 24px 0 0;
			font-size: clamp(40px, 5.6vw, 88px);
			font-weight: 450;
			line-height: 1.02;
			letter-spacing: -.055em;
		}

		.ct-hero__title>span {
			display: block;
		}

		.ct-hero__title>span+span {
			color: rgba(240, 239, 237, .72);
		}

		.ct-hero__lead {
			margin: 26px 0 0;
			max-width: 54ch;
			color: rgba(240, 239, 237, .78);
			font-size: 15px;
			line-height: 1.7;
		}

		.ct-hero .button {
			background: #fff;
			color: var(--ink);
		}

		.ct-hero .button:hover {
			background: var(--paper);
		}

		.ct-hero .text-link {
			color: #fff;
			text-decoration: none;
			border-bottom-color: rgba(255, 255, 255, .38);
		}

		.ct-hero .text-link:hover {
			border-bottom-color: #fff;
		}

		.ct-hero__actions {
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			gap: 20px 34px;
			margin-top: 32px;
		}

		.ct-hero .fss-breadcrumbs {
			margin-bottom: 26px;
		}

		.ct-hero .fss-bc-list {
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

		.ct-hero .fss-bc-item {
			display: flex;
			align-items: center;
			color: rgba(255, 255, 255, .5);
		}

		.ct-hero .fss-bc-item:not(:last-child):after {
			content: "/";
			margin-left: 8px;
			color: rgba(255, 255, 255, .28);
		}

		.ct-hero .fss-bc-link {
			color: rgba(255, 255, 255, .5);
			text-decoration: none;
			transition: color .2s;
		}

		.ct-hero .fss-bc-link:hover {
			color: #fff;
		}

		.ct-hero .fss-bc-current {
			color: #fff;
		}

		/* Полоса быстрых контактов под героем */
		.ct-quick {
			position: relative;
			z-index: 1;
			width: 100%;
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			border-top: 1px solid rgba(255, 255, 255, .18);
		}

		.ct-quick__item {
			padding: 24px 32px 24px 0;
			border-right: 1px solid rgba(255, 255, 255, .18);
			color: #fff;
			text-decoration: none;
			display: flex;
			flex-direction: column;
			gap: 8px;
			min-width: 0;
		}

		.ct-quick__item:last-child {
			border-right: 0;
		}

		.ct-quick__label {
			color: rgba(255, 255, 255, .5);
			font-size: 10px;
			letter-spacing: .1em;
			text-transform: uppercase;
		}

		.ct-quick__value {
			font-size: 15px;
			letter-spacing: -.01em;
			overflow-wrap: anywhere;
			transition: opacity .2s;
		}

		.ct-quick__item:hover .ct-quick__value {
			opacity: .72;
		}

		/* ---------- СЕКЦИИ ---------- */
		.ct-section {
			padding-top: clamp(48px, 5.5vw, 92px);
			padding-bottom: clamp(48px, 5.5vw, 92px);
			scroll-margin-top: 100px;
		}

		.ct-section+.ct-section {
			padding-top: 0;
		}

		/* Заголовок раздела на светлом фоне: глобальный .work-heading p рассчитан
		   на тёмные секции, поэтому переопределяем цвет и ритм. */
		.ct-section .work-heading {
			padding-block: 10px 44px;
		}

		.ct-section .work-heading h2 {
			max-width: 16ch;
			letter-spacing: -.055em;
		}

		.ct-section .work-heading p {
			color: var(--muted);
		}

		.ct-grid {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 30px;
		}

		.ct-card {
			border-top: 1px solid var(--ink);
			padding-top: 24px;
			display: flex;
			flex-direction: column;
			min-width: 0;
		}

		.ct-card__ico {
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

		.ct-card h3 {
			margin: 0;
			font-size: clamp(20px, 1.8vw, 26px);
			line-height: 1.12;
			letter-spacing: -.035em;
		}

		.ct-card p {
			margin: 12px 0 0;
			color: var(--muted);
			font-size: 13.5px;
			line-height: 1.7;
		}

		.ct-card a {
			color: inherit;
			text-decoration: none;
		}

		.ct-card__value {
			margin: 14px 0 0;
			font-size: 18px;
			letter-spacing: -.02em;
			color: var(--ink);
			overflow-wrap: anywhere;
		}

		/* Карта */
		.ct-map {
			border: 1px solid var(--line);
			border-radius: var(--radius-surface);
			overflow: hidden;
			background: #fff;
		}

		.ct-map iframe {
			display: block;
			width: 100%;
			height: 420px;
			border: 0;
		}

		.ct-map__fallback {
			padding: 18px 24px;
			border-top: 1px solid var(--line);
			font-size: 12.5px;
			color: var(--muted);
			display: flex;
			flex-wrap: wrap;
			align-items: center;
			gap: 10px 20px;
		}

		.ct-map__fallback a {
			color: var(--ink);
			text-decoration: underline;
			text-underline-offset: 3px;
		}

		/* Реквизиты */
		.ct-requ {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 30px 40px;
		}

		.ct-requ__row {
			min-width: 0;
		}

		.ct-requ__row dt {
			color: var(--muted);
			font-size: 10px;
			letter-spacing: .1em;
			text-transform: uppercase;
		}

		.ct-requ__row dd {
			margin: 10px 0 0;
			font-size: 15px;
			line-height: 1.55;
			letter-spacing: -.01em;
		}

		/* География */
		.ct-geo {
			display: flex;
			flex-wrap: wrap;
			gap: 10px 26px;
		}

		.ct-geo a {
			font-size: 14px;
		}

		.ct-note {
			margin: 26px 0 0;
			font-size: 12.5px;
			color: var(--muted);
		}

		/* FAQ */
		.ct-faq {
			border-top: 1px solid var(--ink);
		}

		.ct-faq details {
			border-bottom: 1px solid var(--line);
		}

		.ct-faq summary {
			list-style: none;
			cursor: pointer;
			display: flex;
			align-items: center;
			gap: 18px;
			padding-block: 24px;
		}

		.ct-faq summary::-webkit-details-marker {
			display: none;
		}

		.ct-faq summary>span {
			color: var(--muted);
			font-size: 10px;
			padding-top: 6px;
		}

		.ct-faq summary h3 {
			margin: 0;
			font-size: clamp(19px, 1.8vw, 26px);
			line-height: 1.2;
			letter-spacing: -.035em;
		}

		.ct-faq summary svg {
			flex-shrink: 0;
			margin-left: auto;
			transition: transform .4s var(--ease);
		}

		.ct-faq details[open] summary svg {
			transform: rotate(45deg);
		}

		.ct-faq__body {
			padding: 0 44px 26px 28px;
		}

		.ct-faq__body p {
			margin: 0;
			color: var(--muted);
			font-size: 14px;
			line-height: 1.7;
		}

		/* Заявка — тёмный блок enquiry-section (дизайн-система), стили — общие */

		/* ---------- АДАПТИВ ---------- */
		@media (width<=1100px) {
			.ct-grid,
			.ct-requ {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}
		}

		@media (width<=920px) {
			.ct-quick {
				grid-template-columns: 1fr;
			}

			.ct-quick__item {
				border-right: 0;
				border-bottom: 1px solid rgba(255, 255, 255, .18);
				padding: 20px 0;
			}

			.ct-quick__item:last-child {
				border-bottom: 0;
			}
		}

		@media (width<=720px) {
			.ct-hero {
				min-height: 0;
			}

			.ct-hero__inner {
				padding-top: calc(var(--fss-header-h, 74px) + 44px);
				padding-bottom: 28px;
			}

			.ct-hero__title {
				font-size: 12vw;
			}

			.ct-grid,
			.ct-requ {
				grid-template-columns: 1fr;
				gap: 26px;
			}

			.ct-map iframe {
				height: 320px;
			}

			.ct-faq__body {
				padding-left: 0;
				padding-right: 10px;
			}
		}

		@media (prefers-reduced-motion: reduce) {
			.ct-faq summary svg {
				transition: none;
			}
		}
	</style>
</head>

<body class="font-sans text-gray-800" data-page="inner">
	<?php
	// Шапка прозрачная — под ней тёмный hero.
	$GLOBALS['fss_nav_mode'] = 'transparent';
	include_once __DIR__ . '/../../components/header.php';
	?>
	<main>
		<!-- HERO -->
		<section class="ct-hero" aria-labelledby="ct-title" data-nav-hero>
			<div class="ct-hero__bg" aria-hidden="true">
				<img src="/public/assets/image/Components/banner/hero.jpg.webp" alt="" loading="eager" fetchpriority="high" decoding="async" width="1672" height="941">
			</div>
			<div class="ct-hero__shade" aria-hidden="true"></div>
			<div class="ct-hero__inner section-shell">
				<?= seo_breadcrumbs_html() ?>
				<p class="eyebrow ct-hero__eyebrow">
					<span class="live-dot" aria-hidden="true"></span>
					Контакты · ООО «ФАВОРИТ-СТРОЙ-СНАБ» · на рынке с <?= $h((string)seo_config('founded_market')) ?> года
				</p>
				<h1 id="ct-title" class="ct-hero__title">
					<span>Свяжитесь</span>
					<span>с нами<span class="accent-dot">.</span></span>
				</h1>
				<p class="ct-hero__lead">Телефон, почта, офис и производство в Москве, реквизиты и форма заявки — на одной странице. Отвечаем за <?= $h((string)seo_config('callback_minutes')) ?> минут, смету считаем за <?= $h((string)seo_config('price_response_minutes')) ?> минут.</p>
				<div class="ct-hero__actions">
					<a class="button" href="#lead" data-goal="contacts_hero_cta" title="Оставить заявку">Оставить заявку
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</a>
					<a class="text-link" href="tel:<?= $h($phoneHref) ?>" title="Позвонить: <?= $h($phone) ?>"><?= $h($phone) ?></a>
				</div>
			</div>
			<div class="ct-quick section-shell">
				<a class="ct-quick__item" href="tel:<?= $h($phoneHref) ?>" title="Позвонить: <?= $h($phone) ?>">
					<span class="ct-quick__label">Телефон</span>
					<span class="ct-quick__value"><?= $h($phone) ?></span>
				</a>
				<a class="ct-quick__item" href="mailto:<?= $h($emailHref) ?>" title="Написать на email">
					<span class="ct-quick__label">Почта</span>
					<span class="ct-quick__value"><?= $h($email) ?></span>
				</a>
				<a class="ct-quick__item" href="#address" title="Адрес офиса и производства">
					<span class="ct-quick__label">Адрес</span>
					<span class="ct-quick__value"><?= $h((string)($addr['locality'] ?? 'Москва')) ?>, <?= $h((string)($addr['street'] ?? '')) ?></span>
				</a>
			</div>
		</section>

		<!-- СПОСОБЫ СВЯЗИ -->
		<section class="ct-section section-shell" id="ways" aria-labelledby="ways-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Способы связи')) ?></p>
				<span class="eyebrow">Пн–Пт 09:00–18:00, МСК</span>
			</div>
			<div class="work-heading">
				<h2 id="ways-title">Как связаться<br>с ФСС</h2>
				<p>Телефон и почта — для смет и вопросов, мессенджеры — для быстрых вопросов<br>и фото объектов.</p>
			</div>
			<div class="ct-grid">
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-solid fa-phone"></i></span>
					<h3>Телефон</h3>
					<p class="ct-card__value"><a href="tel:<?= $h($phoneHref) ?>" title="Позвонить: <?= $h($phone) ?>"><?= $h($phone) ?></a></p>
					<p>Инженер перезванивает за <?= $h((string)seo_config('callback_minutes')) ?> минут в рабочее время. По субботам и воскресеньям — по записи.</p>
				</article>
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
					<h3>Электронная почта</h3>
					<p class="ct-card__value"><a href="mailto:<?= $h($emailHref) ?>" title="Написать на email"><?= $h($email) ?></a></p>
					<p>Пришлите ТЗ, планировку или просто описание задачи — посчитаем смету и вернёмся с предложением и сроками.</p>
				</article>
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-solid fa-paper-plane"></i></span>
					<h3>Telegram</h3>
					<p class="ct-card__value"><a href="<?= $h($telegram) ?>" target="_blank" rel="noopener noreferrer" title="Telegram: @fss_rus">@fss_rus</a></p>
					<p>Самый быстрый канал: фото площадки, чертежи, вопросы по срокам и комплектации.</p>
				</article>
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-brands fa-vk"></i></span>
					<h3>ВКонтакте</h3>
					<p class="ct-card__value"><a href="<?= $h($vk) ?>" target="_blank" rel="noopener noreferrer" title="ВКонтакте ФСС">ФСС ВКонтакте</a></p>
					<p>Новости производства, готовые объекты и разборы решений по заказам.</p>
				</article>
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-solid fa-clock"></i></span>
					<h3>Режим работы</h3>
					<p class="ct-card__value">Пн–Пт 09:00–18:00</p>
					<p>Заявки, пришедшие в нерабочее время, обрабатываем на следующий рабочий день — о письме-ответе сообщаем заранее.</p>
				</article>
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-solid fa-file-signature"></i></span>
					<h3>Документы и договор</h3>
					<p class="ct-card__value">Договор · Сметы · КП</p>
					<p>Работаем по 44-ФЗ и 223-ФЗ, допуск СРО, полный пакет исполнительной и закрывающей документации по каждому объекту.</p>
				</article>
			</div>
		</section>

		<!-- АДРЕС И КАК ДОБРАТЬСЯ -->
		<section class="ct-section section-shell" id="address" aria-labelledby="address-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Адрес и как добраться')) ?></p>
				<span class="eyebrow">Москва, <?= $h((string)($addr['postal'] ?? '')) ?></span>
			</div>
			<div class="work-heading">
				<h2 id="address-title">Офис<br>и производство</h2>
				<p>Приезжайте заранее по договорённости — покажем цех, склад готовой продукции<br>и подготовим документы.</p>
			</div>
			<div class="ct-map">
				<iframe
					title="Карта: <?= $h($addrFull) ?>"
					src="https://yandex.ru/map-widget/v1/?text=<?= rawurlencode($addrFull) ?>&z=16"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"
					allowfullscreen></iframe>
				<div class="ct-map__fallback">
					<span><?= $h($addrFull) ?></span>
					<a href="https://yandex.ru/maps/?text=<?= rawurlencode($addrFull) ?>" target="_blank" rel="noopener noreferrer" title="Открыть в Яндекс.Картах">Открыть в Яндекс.Картах</a>
				</div>
			</div>
			<div class="ct-grid" style="margin-top:40px">
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-solid fa-building"></i></span>
					<h3>Юридический адрес</h3>
					<p class="ct-card__value"><?= $h((string)($addr['locality'] ?? '')) ?>, <?= $h((string)($addr['street'] ?? '')) ?></p>
					<p>Индекс <?= $h((string)($addr['postal'] ?? '')) ?>. Адрес для корреспонденции и получения документов.</p>
				</article>
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-solid fa-truck-fast"></i></span>
					<h3>Выезд на объект</h3>
					<p class="ct-card__value">Москва, МО и вся Россия</p>
					<p>Инженер выезжает на площадку для замера и согласования объёмов — по всей России, включая регионы с подтверждёнными объектами.</p>
				</article>
				<article class="ct-card">
					<span class="ct-card__ico" aria-hidden="true"><i class="fa-solid fa-industry"></i></span>
					<h3>Собственное производство</h3>
					<p class="ct-card__value">Завод БВЗ · <?= $h((string)seo_config('deadline_days')) ?> дней</p>
					<p>Модули, металлоконструкции и инженерные системы производим сами — поэтому срок и цена не зависят от субподрядчиков.</p>
				</article>
			</div>
		</section>

		<!-- РЕКВИЗИТЫ -->
		<section class="ct-section section-shell" id="requisites" aria-labelledby="requisites-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Реквизиты')) ?></p>
				<span class="eyebrow">Данные из ЕГРЮЛ</span>
			</div>
			<div class="work-heading">
				<h2 id="requisites-title">Юридические<br>реквизиты</h2>
				<p>ООО «ФАВОРИТ-СТРОЙ-СНАБ» зарегистрировано <?= $h(date('d.m.Y', strtotime($regDate))) ?>.<br>Данные подтверждены выпиской из ЕГРЮЛ.</p>
			</div>
			<dl class="ct-requ">
				<div class="ct-requ__row">
					<dt>Полное наименование</dt>
					<dd>Общество с ограниченной ответственностью «ФАВОРИТ-СТРОЙ-СНАБ»</dd>
				</div>
				<div class="ct-requ__row">
					<dt>Руководитель</dt>
					<dd><?= $h($ceo) ?></dd>
				</div>
				<div class="ct-requ__row">
					<dt>ИНН / КПП</dt>
					<dd><?= $h($inn) ?> / <?= $h($kpp) ?></dd>
				</div>
				<div class="ct-requ__row">
					<dt>ОГРН</dt>
					<dd><?= $h($ogrn) ?></dd>
				</div>
				<div class="ct-requ__row">
					<dt>Дата регистрации</dt>
					<dd><?= $h(date('d.m.Y', strtotime($regDate))) ?></dd>
				</div>
				<div class="ct-requ__row">
					<dt>Юридический адрес</dt>
					<dd><?= $h($addrFull) ?></dd>
				</div>
				<div class="ct-requ__row">
					<dt>Электронная почта</dt>
					<dd><a href="mailto:<?= $h($emailHref) ?>" title="Написать на email"><?= $h($email) ?></a></dd>
				</div>
				<div class="ct-requ__row">
					<dt>Телефон</dt>
					<dd><a href="tel:<?= $h($phoneHref) ?>" title="Позвонить: <?= $h($phone) ?>"><?= $h($phone) ?></a></dd>
				</div>
				<div class="ct-requ__row">
					<dt>На рынке с</dt>
					<dd><?= $h((string)seo_config('founded_market')) ?> года · ООО — с <?= $h($founded) ?> года</dd>
				</div>
			</dl>
		</section>

		<!-- ГЕОГРАФИЯ РАБОТЫ -->
		<section class="ct-section section-shell" id="geo" aria-labelledby="geo-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('География работы')) ?></p>
				<span class="eyebrow">Москва · МО · Регионы</span>
			</div>
			<div class="work-heading">
				<h2 id="geo-title">Где мы<br>работаем</h2>
				<p>База — Москва, объект сдаём в Москве и области, а также в регионах<br>с подтверждёнными работами. Ниже — направления, где есть реальные объекты.</p>
			</div>
			<nav class="ct-geo" aria-label="Гео-направления">
				<?php foreach ($geoRows as $g): ?>
					<a class="text-link" href="<?= $h($g['url']) ?>" title="<?= $h($g['name'] . ' — ' . $g['region']) ?>"><?= $h($g['name']) ?>
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</a>
				<?php endforeach; ?>
			</nav>
			<p class="ct-note">Не нашли свой город? Напишите — посчитаем смету и приедем на площадку: доставляем конструкции транзитом по всей России.</p>
		</section>

		<!-- ВОПРОСЫ -->
		<section class="ct-section section-shell" id="faq" aria-labelledby="faq-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Вопросы')) ?></p>
				<span class="eyebrow">Отвечаем честно</span>
			</div>
			<div class="work-heading">
				<h2 id="faq-title">Частые вопросы<br>о связи с нами</h2>
				<p>Остальное подскажет инженер — оставьте заявку и задайте вопрос напрямую.</p>
			</div>
			<div class="ct-faq">
				<?php foreach ($faq as $fi => $qa): ?>
					<details<?= $fi === 0 ? ' open' : '' ?>>
						<summary>
							<span><?= str_pad((string)($fi + 1), 2, '0', STR_PAD_LEFT) ?></span>
							<h3><?= $h($qa[0]) ?></h3>
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" aria-hidden="true" focusable="false">
								<path d="M12 5v14"></path>
								<path d="M5 12h14"></path>
							</svg>
						</summary>
						<div class="ct-faq__body"><p><?= $h($qa[1]) ?></p></div>
					</details>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- ЗАЯВКА -->
		<section class="enquiry-section" id="lead" aria-labelledby="lead-title">
			<div class="enquiry-lead">
				<p class="eyebrow" style="color: rgba(240,239,237,.62); display: flex; align-items: center;">
					<span class="live-dot" style="background: currentColor; border-radius: 50%; width: 6px; height: 6px; margin-right: 12px; display: inline-block;" aria-hidden="true"></span>
					<?= $h($fssSec('Заявка')) ?>
				</p>
				<h2 id="lead-title">Расскажите<br><span>о задаче.</span></h2>
				<p>Опишите объект или пришлите ТЗ — инженер перезвонит за <?= $h((string)seo_config('callback_minutes')) ?> минут, посчитает смету за <?= $h((string)seo_config('price_response_minutes')) ?> минут и зафиксирует цену в договоре.</p>
				<span class="enquiry-location" style="color:#aaa49e; margin-top: 32px; font-size: 12px; display: block;">
					<?= $h($addrFull) ?><br>Работаем по Москве, Московской области и России
				</span>
			</div>
			<?php include_once __DIR__ . '/../../components/enquiry_form.php'; ?>
		</section>
	</main>

	<?php include_once __DIR__ . '/../../components/footer.php'; ?>

	<?php
	// --- Разметка страницы: ContactPage + FAQPage.
	// Организация (адрес, телефон, режим работы) уже размечена в общем графе
	// из sheoma.php — здесь только тип страницы и её собственные вопросы,
	// чтобы не было двух определений одной сущности.
	$__nodes = [
		[
			'@type' => 'ContactPage',
			'@id' => seo_canonical() . '#webpage',
			'url' => seo_canonical(),
			'name' => $seoTitle,
			'description' => $seoDesc,
			'inLanguage' => 'ru-RU',
			'isPartOf' => ['@id' => $base . '/#website'],
			'about' => ['@id' => $base . '/#organization'],
			'breadcrumb' => ['@id' => seo_canonical() . '#breadcrumb'],
			'mainEntity' => ['@id' => $base . '/#organization'],
		],
	];
	$__faqNodes = [];
	foreach ($faq as $qa) {
		$__faqNodes[] = [
			'@type' => 'Question',
			'name' => $qa[0],
			'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
		];
	}
	if ($__faqNodes) {
		$__nodes[] = [
			'@type' => 'FAQPage',
			'@id' => seo_canonical() . '#faq',
			'mainEntity' => $__faqNodes,
		];
	}
	echo seo_jsonld($__nodes);
	?>
</body>

</html>
