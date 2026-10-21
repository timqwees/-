<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

/* ============================================================
   Каталог услуг /uslugi — оформление по дизайну главной
   (ProMaintenance: тёмный герой, paper-секции, трекированные
   подписи, крупные заголовки, карточки с фото и ссылками-подчёркиваниями).
   ============================================================ */

/* Короткие названия для карточек */
$short = [
	'proizvodim_modul' => 'Модульные здания под ключ',
	'kapstroenie' => 'Капитальное строительство',
	'kapital' => 'Капитальный ремонт',
	'angar' => 'Ангары под ключ',
	'sclad' => 'Склады и логистические центры',
	'administrativnoe' => 'АБК и офисы',
	'obchegitia' => 'Общежития и вахтовые посёлки',
	'gruzoperevozki' => 'Грузоперевозки по России',
	'spectehnika' => 'Спецтехника в аренду',
	'musorbak' => 'Мусорные баки и ТБО',
	'naves' => 'Навесы и козырьки',
	'generatornae' => 'Дизель-генераторные установки',
	'compressor' => 'Газовые компрессорные станции',
	'compressor_air' => 'Компрессорные станции',
	'montach' => 'Монтаж и шеф-монтаж',
	'modultebel' => 'Модульная мебель',
	'postavka' => 'Поставка материалов и металлоконструкций',
	'proektirovanie' => 'Проектирование зданий',
	'osnashchenie' => 'Комплексное оснащение',
	'bpla' => 'БПЛА и системы КИТ',
];

/* Срок/формат работы — в подпись карточки */
$meta = [
	'proizvodim_modul' => 'от 14 дней', 'kapstroenie' => 'под ключ', 'kapital' => 'по смете',
	'angar' => 'от 14 дней', 'sclad' => 'по смете', 'administrativnoe' => 'под ключ',
	'obchegitia' => 'под ключ', 'gruzoperevozki' => 'по заявке', 'spectehnika' => 'по смене',
	'musorbak' => 'по договору', 'naves' => 'от 14 дней', 'generatornae' => 'по смете',
	'compressor' => 'по смете', 'compressor_air' => 'по смете', 'montach' => 'по договору',
	'modultebel' => 'на заказ', 'postavka' => 'по договору', 'proektirovanie' => 'по договору',
	'osnashchenie' => 'по договору', 'bpla' => 'по заявке',
];

/* Категории каталога */
$cats = [
	'modul' => 'Модульные здания',
	'sklad' => 'Склады и ангары',
	'abk' => 'АБК и офисы',
	'obch' => 'Общежития',
	'remont' => 'Капитальный ремонт',
	'proekt' => 'Проектирование',
	'dostavka' => 'Доставка и техника',
	'postavka' => 'Поставка и оснащение',
];
$catmap = [
	'proizvodim_modul' => 'modul',
	'sclad' => 'sklad', 'angar' => 'sklad', 'naves' => 'sklad',
	'administrativnoe' => 'abk', 'obchegitia' => 'obch',
	'kapital' => 'remont', 'kapstroenie' => 'remont', 'montach' => 'remont',
	'proektirovanie' => 'proekt',
	'gruzoperevozki' => 'dostavka', 'spectehnika' => 'dostavka', 'generatornae' => 'dostavka',
	'compressor' => 'dostavka', 'compressor_air' => 'dostavka',
	'postavka' => 'postavka', 'osnashchenie' => 'postavka', 'bpla' => 'postavka',
	'modultebel' => 'postavka', 'musorbak' => 'postavka',
];

/* Популярные услуги — показываем в каталоге первыми */
$featured = ['proizvodim_modul', 'angar', 'sclad', 'obchegitia', 'kapital', 'gruzoperevozki', 'spectehnika', 'proektirovanie', 'montach'];

/* Обрезка описания по границе слова */
$cut = function (string $text, int $limit = 150): string {
	if (mb_strlen($text) <= $limit) {
		return $text;
	}
	$chunk = mb_substr($text, 0, $limit);
	$pos = mb_strrpos($chunk, ' ');
	if ($pos !== false && $pos > 60) {
		$chunk = mb_substr($chunk, 0, $pos);
	}
	return rtrim($chunk, ' ,.;:—–-');
};

/* Число со знаком «+» отдельным элементом — для акцента в показателях */
$num = function (string $value): string {
	$value = trim($value);
	if (preg_match('/^(\d+)(\+)$/u', $value, $m)) {
		return htmlspecialchars($m[1], ENT_QUOTES) . '<span>+</span>';
	}
	return htmlspecialchars($value, ENT_QUOTES);
};

/* Фотографии для карточек (реальные объекты ФСС) */
$photos = [
	'proizvodim_modul' => '/public/assets/image/services/капстроение/10.JPG',
	'kapstroenie' => '/public/assets/image/services/капстроение/1.JPG',
	'kapital' => '/public/assets/image/services/капстроение/5.JPG',
	'angar' => '/public/assets/image/services/капстроение/12.JPG',
	'sclad' => '/public/assets/image/services/капстроение/3.JPG',
	'administrativnoe' => '/public/assets/image/services/АБК/04d8caee-f083-42f8-8be2-64e7e461cace.JPG',
	'obchegitia' => '/public/assets/image/services/АБК/88d88c8a-a2d2-435a-b98e-46eac4b97e6b.JPG',
	'gruzoperevozki' => '/public/assets/image/services/Грузоперевозки/2.jpg',
	'spectehnika' => '/public/assets/image/services/Спецтехника/1.jpg',
	'musorbak' => '/public/assets/image/services/МТЗ/B-EhBtJFvnnFRaO2wveNOZCClT-JPhW1zfPpCwBpbB1zf5c5eMiv04F-xdvtOP0VW4tQhgxr4EPrTjI6Hzj26rRH.jpg',
	'naves' => '/public/assets/image/services/капстроение/8.JPG',
	'generatornae' => '/public/assets/image/services/Генераторные/017C294C-9AF3-4638-ADC2-476CD4C3A7ED.JPG',
	'compressor' => '/public/assets/image/services/Генераторные/PHOTO-2020-11-05-10-03-22.jpg',
	'compressor_air' => '/public/assets/image/services/Генераторные/2BE1E005-3A0F-4D63-88CB-A369955A6A2A.JPG',
	'montach' => '/public/assets/image/services/капстроение/14.JPG',
	'modultebel' => '/public/assets/image/services/капстроение/16.JPG',
	'postavka' => '/public/assets/image/services/АБК/969bd8c5-4261-4c83-a438-25076ef6879f.JPG',
	'proektirovanie' => '/public/assets/image/services/капстроение/19.JPG',
	'osnashchenie' => '/public/assets/image/services/АБК/94fe0ce5-1e0d-46f8-bcce-2e4e48089e11.JPG',
	'bpla' => '/public/assets/image/services/Генераторные/86371565-2804-4E82-803A-46606FBAE4F8.JPG',
];
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-latin.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-cyrillic.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/fonts/WixMadeforDisplay/WixMadeforDisplay-Variable.woff2" crossorigin><link rel="stylesheet" href="/public/assets/vendor/fonts/fonts.css">
	<style> body { font-family: 'DM Sans Variable', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>

	<title>Услуги — Производство, ремонт, доставка | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Все услуги ФАВОРИТ-СТРОЙ-СНАБ: БВЗ под ключ, капитальный ремонт, грузоперевозки, спецтехника, мебель, склады, навесы.">
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
	<link rel="manifest" href="/public/assets/image/logo/favicon/site.webmanifest" />

	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">

	<!-- canonical -->
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>

	<?= seo_og_tags('Услуги — Производство, ремонт, доставка | ФАВОРИТ-СТРОЙ-СНАБ', 'Все услуги ФАВОРИТ-СТРОЙ-СНАБ: БВЗ под ключ, капитальный ремонт, грузоперевозки, спецтехника, мебель, склады, навесы.', '/public/assets/image/og/og-uslugi-1200x630.jpg') ?>

	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>

	<?= seo_analytics() ?>

	<?= seo_utm_script() ?>

	<?= seo_goals_script() ?>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/promaintenance.css?v=20261024">

<style>
	/* ============================================================
	   /uslugi — оформление по дизайну главной (ProMaintenance)
	   ============================================================ */

	/* ---------- HERO ---------- */
	.usl-hero {
		position: relative;
		background: #171310;
		color: #fff;
		overflow: hidden;
		display: flex;
		flex-direction: column;
		justify-content: flex-end;
		min-height: 620px;
	}

	.usl-hero__bg {
		position: absolute;
		inset: 0;
	}

	.usl-hero__bg img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.usl-hero__shade {
		position: absolute;
		inset: 0;
		background: linear-gradient(90deg, rgba(15, 14, 13, .93) 0%, rgba(15, 14, 13, .68) 48%, rgba(15, 14, 13, .38) 100%),
			linear-gradient(180deg, rgba(15, 14, 13, .88) 0%, rgba(15, 14, 13, 0) 42%);
	}

	.usl-hero__inner {
		position: relative;
		z-index: 1;
		width: 100%;
		padding-top: calc(var(--fss-header-h, 90px) + 72px);
		padding-bottom: 56px;
	}

	.usl-hero__eyebrow {
		color: rgba(255, 255, 255, .72);
	}

	.usl-hero h1 {
		margin: 24px 0 0;
		font-size: clamp(38px, 5.8vw, 92px);
		font-weight: 450;
		line-height: 1.02;
		letter-spacing: -.055em;
	}

	.usl-hero h1>span {
		display: block;
	}

	.usl-hero h1>span+span {
		color: rgba(240, 239, 237, .78);
	}

	.usl-hero h1 .accent-dot {
		display: inline;
	}

	.usl-hero__intro {
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: 26px 46px;
		margin-top: 34px;
	}

	.usl-hero__intro p {
		margin: 0;
		max-width: 46ch;
		color: rgba(240, 239, 237, .74);
		font-size: 14px;
		line-height: 1.75;
	}

	.usl-hero .button {
		background: #fff;
		color: var(--ink);
	}

	.usl-hero .button:hover {
		background: var(--paper);
	}

	/* Хлебные крошки на тёмном фоне */
	.usl-hero .fss-breadcrumbs {
		margin-bottom: 26px;
	}

	.usl-hero .fss-bc-list {
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

	.usl-hero .fss-bc-item {
		display: flex;
		align-items: center;
		color: rgba(255, 255, 255, .5);
	}

	.usl-hero .fss-bc-item:not(:last-child):after {
		content: "/";
		margin-left: 8px;
		color: rgba(255, 255, 255, .28);
	}

	.usl-hero .fss-bc-link {
		color: rgba(255, 255, 255, .5);
		text-decoration: none;
		transition: color .2s;
	}

	.usl-hero .fss-bc-link:hover {
		color: #fff;
	}

	.usl-hero .fss-bc-current {
		color: #fff;
	}

	/* ---------- ПОКАЗАТЕЛИ ---------- */
	.usl-facts {
		padding-top: clamp(48px, 5vw, 82px);
		padding-bottom: clamp(48px, 5vw, 82px);
	}

	.usl-facts .section-topline {
		padding-bottom: 26px;
	}

	.usl-stats {
		border-top: 1px solid var(--line);
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
	}

	.usl-stat {
		border-right: 1px solid var(--line);
		padding: 32px 32px 0 0;
		transition: color .35s;
	}

	.usl-stat:last-child {
		border-right: 0;
	}

	/* Каждый следующий блок отходит от разделительной полосы */
	@media (width>992px) {
		.usl-stat + .usl-stat {
			padding-left: 32px;
		}
	}

	.usl-stat dd {
		margin: 0;
		letter-spacing: -.055em;
		font-weight: 450;
		font-size: clamp(42px, 5.2vw, 82px);
		line-height: 1;
	}

	.usl-stat dd>span {
		color: var(--accent);
		vertical-align: top;
		font-size: .46em;
		letter-spacing: 0;
	}

	.usl-stat dt {
		color: var(--muted);
		margin-top: 20px;
		font-size: 11px;
		letter-spacing: .08em;
		text-transform: uppercase;
	}

	.usl-stat:hover dt {
		color: var(--ink);
	}

	.usl-facts__note {
		color: var(--muted);
		margin-top: 34px;
		padding-top: 18px;
		border-top: 1px solid var(--line);
		font-size: 9px;
	}

	/* ---------- КАТАЛОГ ---------- */
	.uc-catalog {
		padding-top: clamp(44px, 4vw, 64px);
		padding-bottom: clamp(56px, 6vw, 96px);
	}

	/* Заголовок раздела на светлом фоне — приглушённый текст */
	.uc-catalog .work-heading p {
		color: var(--muted);
	}

	.usl-toolbar {
		border-top: 1px solid var(--line);
		padding-top: 24px;
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		justify-content: space-between;
		gap: 22px 40px;
	}

	.uc-cats {
		display: flex;
		flex-wrap: wrap;
		gap: 10px 28px;
	}

	.uc-cat {
		background: none;
		border: 0;
		border-bottom: 1px solid transparent;
		padding: 5px 0;
		cursor: pointer;
		font: inherit;
		font-size: 11px;
		letter-spacing: .1em;
		text-transform: uppercase;
		color: var(--muted);
		transition: color .25s, border-color .25s;
	}

	.uc-cat:hover {
		color: var(--ink);
	}

	.uc-cat.is-active {
		color: var(--ink);
		border-bottom-color: var(--ink);
	}

	.usl-qsearch {
		display: inline-flex;
		align-items: center;
		gap: 12px;
		min-width: 260px;
		border-bottom: 1px solid var(--line);
		padding: 8px 2px;
		transition: border-color .25s;
	}

	.usl-qsearch:focus-within {
		border-bottom-color: var(--ink);
	}

	.usl-qsearch svg {
		width: 16px;
		height: 16px;
		flex-shrink: 0;
		color: var(--muted);
	}

	.usl-qsearch input {
		width: 100%;
		border: 0;
		outline: none;
		background: none;
		font: inherit;
		font-size: 13px;
		color: var(--ink);
	}

	.usl-qsearch input::placeholder {
		color: #918b85;
	}

	/* ---------- КАРТОЧКИ ---------- */
	.uc-grid {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 46px 30px;
		margin-top: 46px;
	}

	.uc-card {
		min-width: 0;
		display: flex;
		flex-direction: column;
	}

	.uc-card.is-extra {
		display: none;
	}

	.uc-grid.is-expanded .uc-card.is-extra,
	.uc-grid.is-searching .uc-card.is-extra,
	.uc-grid.is-filtering .uc-card.is-extra {
		display: flex;
	}

	.uc-grid.is-searching .uc-card[data-match="0"],
	.uc-grid.is-filtering .uc-card[data-match="0"] {
		display: none;
	}

	.uc-card__media {
		position: relative;
		display: block;
		aspect-ratio: 4/3;
		border-radius: var(--radius-surface);
		overflow: hidden;
		background: #dedcda;
	}

	.uc-card__media img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		transition: transform .7s var(--ease);
	}

	.uc-card:hover .uc-card__media img {
		transform: scale(1.04);
	}

	.uc-card__num {
		position: absolute;
		top: 16px;
		left: 16px;
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

	.uc-card__body {
		flex: 1;
		display: flex;
		flex-direction: column;
		padding-top: 20px;
	}

	.uc-card__cat {
		color: #876b55;
	}

	.uc-card__title {
		margin: 12px 0 0;
		font-size: clamp(21px, 1.9vw, 28px);
		line-height: 1.08;
	}

	.uc-card__title a {
		color: inherit;
		text-decoration: none;
		transition: color .25s;
	}

	.uc-card__title a:hover {
		color: var(--brand);
	}

	.uc-card__text {
		margin: 12px 0 0;
		color: var(--muted);
		font-size: 13px;
		line-height: 1.65;
	}

	.uc-card__meta {
		margin-top: 20px;
		padding-top: 16px;
		border-top: 1px solid var(--line);
		display: flex;
		justify-content: space-between;
		gap: 18px;
		font-size: 10px;
		letter-spacing: .07em;
		text-transform: uppercase;
		color: var(--muted);
	}

	.uc-card__meta span:last-child {
		text-align: right;
	}

	.uc-card .text-link {
		margin-top: 18px;
		align-self: flex-start;
		color: inherit;
		text-decoration: none;
	}

	/* ---------- ПУСТОЕ СОСТОЯНИЕ / КНОПКИ ---------- */
	.uc-empty {
		display: none;
		margin-top: 46px;
		color: var(--muted);
		font-size: 14px;
	}

	.uc-empty.show {
		display: block;
	}

	.uc-empty a {
		color: var(--brand);
	}

	.uc-foot {
		margin-top: 56px;
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		justify-content: space-between;
		gap: 24px;
	}

	.uc-more {
		background: var(--ink);
		color: #fff;
		border: 0;
		border-radius: var(--radius-control);
		min-height: 52px;
		padding: 15px 22px;
		font: inherit;
		font-size: 13px;
		cursor: pointer;
		display: inline-flex;
		align-items: center;
		justify-content: space-between;
		gap: 35px;
		transition: background .3s;
	}

	.uc-more:hover {
		background: #424244;
	}

	.uc-foot .text-link {
		color: inherit;
		text-decoration: none;
	}

	/* ---------- CTA ---------- */
	.usl-cta {
		position: relative;
		overflow: hidden;
		background: #292727;
		color: #fff;
		padding: clamp(60px, 7vw, 110px) clamp(24px, 4.4vw, 96px);
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 60px;
		align-items: center;
	}

	.usl-cta__bg {
		position: absolute;
		inset: 0;
		opacity: .16;
	}

	.usl-cta__bg img {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.usl-cta__in {
		position: relative;
		z-index: 1;
	}

	.usl-cta h2 {
		margin: 26px 0 0;
		font-size: clamp(32px, 4.2vw, 64px);
		line-height: 1.04;
	}

	.usl-cta h2 span {
		color: rgba(255, 255, 255, .5);
	}

	.usl-cta p:not(.eyebrow) {
		margin: 22px 0 0;
		max-width: 44ch;
		color: rgba(255, 255, 255, .74);
		font-size: 14px;
		line-height: 1.7;
	}

	.usl-cta__actions {
		margin-top: 32px;
		display: flex;
		flex-wrap: wrap;
		align-items: center;
		gap: 18px 32px;
	}

	.usl-cta .button {
		background: #fff;
		color: var(--ink);
	}

	.usl-cta .button:hover {
		background: var(--paper);
	}

	.usl-cta__list {
		border-top: 1px solid rgba(255, 255, 255, .18);
	}

	.usl-cta__list a {
		border-bottom: 1px solid rgba(255, 255, 255, .18);
		padding: 18px 0;
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 20px;
		color: rgba(255, 255, 255, .82);
		font-size: 13px;
		text-decoration: none;
		transition: color .25s, padding-left .25s;
	}

	.usl-cta__list a:hover {
		color: #fff;
		padding-left: 8px;
	}

	.usl-cta__list span:last-child {
		color: rgba(255, 255, 255, .4);
		font-size: 10px;
		letter-spacing: .1em;
		text-transform: uppercase;
	}

	/* ---------- АДАПТИВ ---------- */
	@media (width<=1100px) {
		.uc-grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}

		.usl-cta {
			grid-template-columns: 1fr;
			gap: 40px;
		}
	}

	@media (width<=992px) {
		.usl-stats {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}

		.usl-stat {
			border-bottom: 1px solid var(--line);
			padding: 26px 24px 26px 0;
		}

		.usl-stat:nth-child(2n) {
			border-right: 0;
			padding-left: 24px;
		}

		.usl-stat:nth-last-child(-n+2) {
			border-bottom: 0;
		}
	}

	@media (width<=720px) {
		.usl-hero {
			min-height: 0;
		}

		.usl-hero__inner {
			padding-top: calc(var(--fss-header-h, 74px) + 44px);
			padding-bottom: 40px;
		}

		.usl-hero h1 {
			font-size: 12vw;
		}

		.usl-toolbar {
			align-items: stretch;
			flex-direction: column;
		}

		.usl-qsearch {
			min-width: 0;
		}

		.uc-grid {
			grid-template-columns: 1fr;
			gap: 36px;
		}

		.uc-card__title {
			font-size: 24px;
		}

		.uc-foot {
			justify-content: flex-start;
		}
	}

	/* ---------- ГЕО-НАПРАВЛЕНИЯ (хаб → региональные страницы) ---------- */
	.uc-geo {
		padding-block: clamp(60px, 7vw, 120px);
	}

	.uc-geo__grid {
		display: grid;
		grid-template-columns: repeat(4, minmax(0, 1fr));
		gap: 1px;
		background: var(--line);
		border: 1px solid var(--line);
		margin-top: 46px;
	}

	.uc-geo__col {
		background: #fff;
		padding: 26px 24px 28px;
	}

	.uc-geo__col h3 {
		margin: 0;
		font-size: 20px;
		font-weight: 500;
		line-height: 1.25;
	}

	.uc-geo__region {
		margin: 8px 0 18px;
		color: rgba(23, 19, 16, .55);
		font-size: 12px;
		letter-spacing: .04em;
	}

	.uc-geo__col ul {
		margin: 0;
		padding: 0;
		list-style: none;
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.uc-geo__col .text-link {
		font-size: 14px;
		gap: 12px;
	}

	@media (width<=1100px) {
		.uc-geo__grid {
			grid-template-columns: repeat(3, minmax(0, 1fr));
		}
	}

	@media (width<=820px) {
		.uc-geo__grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}
	}

	@media (width<=520px) {
		.uc-geo__grid {
			grid-template-columns: 1fr;
		}
	}
</style>

</head>

<body class="font-sans text-gray-800" data-page="inner">
	<?php
	// Шапка прозрачная в начале: под ней полноэкранный hero каталога (как на главной).
	$GLOBALS['fss_nav_mode'] = 'transparent';
	include_once __DIR__ . '/../../components/header.php';
	?>

	<main>
		<!-- HERO (по дизайну главной) -->
		<section class="usl-hero" aria-labelledby="usl-title" data-nav-hero>
			<div class="usl-hero__bg" aria-hidden="true">
				<img src="/public/assets/image/Components/banner/hero.jpg.webp" alt="" loading="eager" fetchpriority="high" decoding="async" width="1672" height="941">
			</div>
			<div class="usl-hero__shade" aria-hidden="true"></div>
			<div class="usl-hero__inner section-shell">
				<?= seo_breadcrumbs_html() ?>
				<p class="eyebrow usl-hero__eyebrow">
					<span class="live-dot" aria-hidden="true"></span>
					Каталог услуг · <?= count($short) ?> направлений
				</p>
				<h1 id="usl-title">
					<span>Всё, что мы</span>
					<span>строим и обслуживаем<span class="accent-dot">.</span></span>
				</h1>
				<div class="usl-hero__intro">
					<p>Быстровозводимые здания под ключ, капитальное строительство и ремонт, логистика и техника — в одном каталоге с прозрачными сроками и ценами.</p>
					<a class="button" href="#services">Смотреть каталог
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</a>
				</div>
			</div>
		</section>

		<!-- ПОКАЗАТЕЛИ -->
		<section class="usl-facts section-shell" aria-label="ФСС в цифрах">
			<div class="section-topline">
				<p class="eyebrow">01 / ФСС в цифрах</p>
				<span class="eyebrow">С <?= htmlspecialchars((string)seo_config('founded_market')) ?> года</span>
			</div>
			<dl class="usl-stats">
				<div class="usl-stat">
					<dd><?= $num((string)count($short)) ?></dd>
					<dt>услуг в каталоге</dt>
				</div>
				<div class="usl-stat">
					<dd><?= $num((string)seo_config('market_years')) ?></dd>
					<dt>лет на рынке</dt>
				</div>
				<div class="usl-stat">
					<dd><?= $num((string)seo_config('objects')) ?></dd>
					<dt>сданных объектов</dt>
				</div>
				<div class="usl-stat">
					<dd><?= $num((string)seo_config('guarantee_years')) ?></dd>
					<dt>лет гарантии</dt>
				</div>
			</dl>
			<p class="usl-facts__note eyebrow">Собственный завод БВЗ · Сроки от <?= htmlspecialchars((string)seo_config('deadline_days')) ?> дней · Смета за <?= htmlspecialchars((string)seo_config('price_response_minutes')) ?> минут</p>
		</section>

		<!-- КАТАЛОГ УСЛУГ -->
		<section id="catalog" class="uc-catalog section-shell" aria-labelledby="catalog-title">
			<div class="section-topline">
				<p class="eyebrow">02 / Каталог</p>
				<span class="eyebrow">Москва, область и вся Россия</span>
			</div>
			<div class="work-heading">
				<h2 id="catalog-title">Все направления<br>в одном списке.</h2>
				<p>Выберите категорию или найдите услугу по названию.<br>По каждому направлению — отдельная страница с расчётом и сроками.</p>
			</div>

			<div class="usl-toolbar">
				<div class="uc-cats" role="group" aria-label="Категории услуг">
					<button class="uc-cat is-active" type="button" data-cat="all" aria-pressed="true">Все</button>
					<?php foreach ($cats as $key => $label): ?>
						<button class="uc-cat" type="button" data-cat="<?= htmlspecialchars($key) ?>" aria-pressed="false"><?= htmlspecialchars($label) ?></button>
					<?php endforeach; ?>
					<button class="uc-cat" type="button" data-cat="popular" aria-pressed="false">Популярное</button>
				</div>
				<label class="usl-qsearch">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<circle cx="11" cy="11" r="7"></circle>
						<path d="m20 20-3.5-3.5"></path>
					</svg>
					<input id="uslSearch" type="search" placeholder="Найти услугу" aria-label="Поиск услуги" autocomplete="off">
				</label>
			</div>

			<section id="services" class="uc-grid" aria-label="Список услуг">
				<?php
				$__i = 0;
				foreach (seo_services() as $slug => $svc):
					$feat = in_array($slug, $featured, true);
					$cat = $catmap[$slug] ?? 'modul';
					$title = $short[$slug] ?? $svc['title'];
					$photo = $photos[$slug] ?? '/public/assets/image/Components/banner/hero.jpg.webp';
					$price = !empty($svc['price_from'])
						? 'от ' . number_format((float)$svc['price_from'], 0, ',', ' ') . ' ₽/м²'
						: 'по смете';
				?>
					<article class="uc-card<?= ($__i >= 9) ? ' is-extra' : '' ?>" data-cat="<?= htmlspecialchars($cat) ?>"<?= $feat ? ' data-feat="1"' : '' ?>>
						<a class="uc-card__media" href="/services/<?= htmlspecialchars($slug) ?>" title="<?= htmlspecialchars($title) ?> — услуги ФСС">
							<img src="<?= htmlspecialchars($photo) ?>"<?= seo_img_attrs($photo) ?> alt="<?= htmlspecialchars($title) ?>" title="<?= htmlspecialchars($title) ?>" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/public/assets/image/logo/favicon/logo-64.png'">
							<span class="uc-card__num"><?= str_pad((string)($__i + 1), 2, '0', STR_PAD_LEFT) ?></span>
						</a>
						<div class="uc-card__body">
							<p class="eyebrow uc-card__cat"><?= htmlspecialchars($cats[$cat] ?? 'Производство') ?></p>
							<h3 class="uc-card__title"><a href="/services/<?= htmlspecialchars($slug) ?>" title="<?= htmlspecialchars($title) ?>"><?= htmlspecialchars($title) ?></a></h3>
							<p class="uc-card__text"><?= htmlspecialchars($cut((string)$svc['description'])) ?>…</p>
							<div class="uc-card__meta">
								<span><?= htmlspecialchars($price) ?></span>
								<span><?= htmlspecialchars($meta[$slug] ?? 'по договору') ?></span>
							</div>
							<a class="text-link" href="/services/<?= htmlspecialchars($slug) ?>" title="Подробнее об услуге">Подробнее
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
									<path d="M7 7h10v10"></path>
									<path d="M7 17 17 7"></path>
								</svg>
							</a>
						</div>
					</article>
				<?php $__i++; endforeach; ?>
			</section>

			<div class="uc-empty" id="uslEmpty">Ничего не найдено — измените запрос или <a href="/#contact" title="Связаться с нами для консультации">свяжитесь с нами</a>.</div>

			<div class="uc-foot">
				<button class="uc-more" id="showAll" type="button">Показать все
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</button>
				<a class="text-link" href="/portfolio" title="Посмотреть портфолио реализованных объектов">Смотреть портфолио объектов
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</a>
			</div>
		</section>

		<!-- ГЕО-НАПРАВЛЕНИЯ (хаб → региональные страницы) -->
		<section id="geo" class="uc-geo section-shell" aria-labelledby="uc-geo-title">
			<div class="section-topline">
				<p class="eyebrow">03 / География</p>
				<span class="eyebrow">Москва · МО · Регионы</span>
			</div>
			<div class="work-heading">
				<h2 id="uc-geo-title">Работаем<br>по всей России</h2>
				<p>База — Москва, объекты сдаём в регионах с подтверждёнными работами.<br>Выберите свой город — страница с местными сроками и логистикой.</p>
			</div>
			<?php
			$__geoAll = function_exists('seo_geo') ? seo_geo() : ['geo' => [], 'priority_services' => []];
			$__geoList = (array)($__geoAll['geo'] ?? []);
			$__prio = (array)($__geoAll['priority_services'] ?? []);
			$__geoRows = [];
			foreach ($__geoList as $__gk => $__gv) {
				$__links = [];
				foreach ($__prio as $__ps) {
					if (seo_geo_allows((array)$__gv, (string)$__ps)) {
						$__links[] = [
							'slug' => (string)$__ps,
							'label' => (string)($short[$__ps] ?? (seo_service((string)$__ps)['title'] ?? $__ps)),
						];
					}
				}
				if (!$__links) {
					continue;
				}
				$__geoRows[] = [
					'key' => (string)$__gk,
					'name' => (string)($__gv['name'] ?? $__gk),
					'region' => (string)($__gv['region'] ?? ''),
					'links' => $__links,
				];
			}
			?>
			<div class="uc-geo__grid">
				<?php foreach ($__geoRows as $__row): ?>
					<div class="uc-geo__col">
						<h3><?= htmlspecialchars($__row['name']) ?></h3>
						<p class="uc-geo__region"><?= htmlspecialchars($__row['region']) ?></p>
						<ul>
							<?php foreach ($__row['links'] as $__l): ?>
								<li><a class="text-link" href="/services/<?= htmlspecialchars($__l['slug']) ?>/<?= htmlspecialchars($__row['key']) ?>" title="<?= htmlspecialchars($__row['name'] . ' — ' . $__l['label']) ?>"><?= htmlspecialchars($__l['label']) ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- БЕГУЩАЯ СТРОКА НАПРАВЛЕНИЙ (как на главной) -->
		<section class="marquee-band" aria-hidden="true">
			<div class="marquee-track">
				<?php
				$__marquee = array_values($short);
				$__marquee = array_merge($__marquee, $__marquee);
				foreach ($__marquee as $__m): ?>
					<span class="marquee-item"><?= htmlspecialchars($__m) ?></span>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- CTA -->
		<section class="usl-cta" aria-labelledby="usl-cta-title">
			<div class="usl-cta__bg" aria-hidden="true">
				<img src="/public/assets/image/banner.webp" alt="" loading="lazy" decoding="async">
			</div>
			<div class="usl-cta__in">
				<p class="eyebrow">
					<span class="live-dot" aria-hidden="true"></span>
					Следующий шаг
				</p>
				<h2 id="usl-cta-title">Не знаете, какая<br><span>система нужна?</span></h2>
				<p>Оставьте заявку — инженер уточнит задачу, рассчитает смету за 15 минут и зафиксирует цену в договоре.</p>
				<div class="usl-cta__actions">
					<a class="button" href="/#contact" data-goal="uslugi_card_cta" title="Рассчитать стоимость проекта за 15 минут">Рассчитать стоимость
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</a>
					<a class="text-link" href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', (string)seo_config('phone'))) ?>" title="Позвонить: <?= htmlspecialchars((string)seo_config('phone_display')) ?>"><?= htmlspecialchars((string)seo_config('phone_display')) ?></a>
				</div>
			</div>
			<div class="usl-cta__in">
				<p class="eyebrow">Частые направления</p>
				<div class="usl-cta__list">
					<?php foreach (['proizvodim_modul' => 'Производство модульных зданий', 'sclad' => 'Склады и логистические центры', 'angar' => 'Ангары под ключ', 'kapital' => 'Капитальный ремонт'] as $__slug => $__label): ?>
						<a href="/services/<?= htmlspecialchars($__slug) ?>" title="<?= htmlspecialchars($__label) ?>">
							<span><?= htmlspecialchars($__label) ?></span>
							<span>Подробнее</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	</main>

	<script>
		(function () {
			var grid = document.getElementById('services');
			var input = document.getElementById('uslSearch');
			var btn = document.getElementById('showAll');
			var empty = document.getElementById('uslEmpty');
			if (!grid) return;
			var cards = Array.prototype.slice.call(grid.querySelectorAll('.uc-card'));
			var pills = Array.prototype.slice.call(document.querySelectorAll('.uc-cats .uc-cat'));
			var expanded = false;
			var activeCat = 'all';
			var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

			cards.forEach(function (c) {
				var t = c.querySelector('.uc-card__title'), p = c.querySelector('.uc-card__text');
				c._t = ((t ? t.textContent : '') + ' ' + (p ? p.textContent : '')).toLowerCase();
				c.setAttribute('data-match', '1');
			});

			function render() {
				var q = (input && input.value ? input.value : '').trim().toLowerCase();
				var filtering = !!q || activeCat !== 'all';
				grid.classList.toggle('is-searching', !!q);
				grid.classList.toggle('is-filtering', filtering);
				var visible = 0;
				cards.forEach(function (c) {
					var matchQ = !q || c._t.indexOf(q) > -1;
					var matchC = activeCat === 'all' || (activeCat === 'popular' ? c.hasAttribute('data-feat') : c.getAttribute('data-cat') === activeCat);
					var match = matchQ && matchC;
					c.setAttribute('data-match', match ? '1' : '0');
					if (match) visible++;
				});
				if (empty) empty.classList.toggle('show', visible === 0);
				if (btn) btn.style.display = filtering ? 'none' : '';
			}

			if (input) input.addEventListener('input', render);

			pills.forEach(function (pill) {
				pill.addEventListener('click', function () {
					var c = pill.getAttribute('data-cat');
					activeCat = (activeCat === c) ? 'all' : c;
					pills.forEach(function (x) {
						var on = x.getAttribute('data-cat') === activeCat;
						x.classList.toggle('is-active', on);
						x.setAttribute('aria-pressed', on ? 'true' : 'false');
					});
					render();
					if (activeCat !== 'all') grid.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
				});
			});

			function animateHeight(from, to, dur, ease, done) {
				grid.style.height = from + 'px';
				grid.classList.add('is-anim');
				void grid.offsetHeight;
				grid.style.transition = 'height ' + dur + 'ms ' + ease;
				grid.style.height = to + 'px';
				setTimeout(function () {
					grid.style.height = '';
					grid.style.transition = '';
					grid.classList.remove('is-anim');
					if (done) done();
				}, dur + 60);
			}

			if (btn) btn.addEventListener('click', function () {
				if (!expanded) {
					grid.classList.remove('is-collapsing');
					if (reduceMotion) {
						grid.classList.add('is-expanded');
					} else {
						var h0 = grid.offsetHeight;
						grid.classList.add('is-expanded');
						animateHeight(h0, grid.offsetHeight, 550, 'cubic-bezier(.16,1,.3,1)');
					}
					this.firstChild.nodeValue = 'Свернуть все ';
					expanded = true;
				} else {
					this.firstChild.nodeValue = 'Показать все ';
					expanded = false;
					if (reduceMotion) {
						grid.classList.remove('is-expanded', 'is-collapsing');
						return;
					}
					var h1 = grid.offsetHeight;
					grid.classList.remove('is-expanded');
					var h0c = grid.offsetHeight;
					grid.classList.add('is-expanded');
					grid.classList.add('is-collapsing');
					animateHeight(h1, h0c, 450, 'cubic-bezier(.4,0,.2,1)', function () {
						grid.classList.remove('is-expanded', 'is-collapsing');
					});
					if (btn.getBoundingClientRect().top < 0) btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
				}
			});
		})();
	</script>

	<!-- footer content -->
	<?php include_once __DIR__ . '/../../components/footer.php'; ?>

</body>

</html>
