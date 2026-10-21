<?php
require_once __DIR__ . '/../../../app/bootstrap.php';

/* ============================================================
   /company — «О компании»: реквизиты, руководство, направления
   деятельности, выполненные объекты, стандарты и гарантии.
   Источник данных — app/Data/company.php (справка ФСС) + app/Config/site.php.
   Оформление — по дизайн-системе главной (ProMaintenance).
   ============================================================ */

$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };

$company = (array)(require dirname(__DIR__, 3) . '/app/Data/company.php');

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
$yandexMaps = (string)seo_config('yandex_maps');
$addrFull = trim((string)($addr['locality'] ?? '') . ', ' . (string)($addr['street'] ?? '') . ', ' . (string)($addr['postal'] ?? ''), ' ,');

// Сквозной номер раздела страницы («01 / О компании»).
$fssSec = function (string $label): string {
	static $n = 0;
	$n++;
	return str_pad((string)$n, 2, '0', STR_PAD_LEFT) . ' / ' . $label;
};

$seoTitle = 'О компании, реквизиты и выполненные объекты | ФАВОРИТ-СТРОЙ-СНАБ';
$seoDesc = 'ООО «Фаворит-Строй-Снаб»: ИНН 9724020865, КПП 770901001, ОГРН 1207700318583. Генеральный директор Аюбов Хасин Хамзатович. 16 капремонтов и 13 школ.';

$kapremont = (array)($company['kapremont'] ?? []);
$kapremontCount = count($kapremont);
$schoolsCount = (int)(($company['schools']['count'] ?? 13));
$faq = (array)($company['faq'] ?? []);
$principles = (array)($company['principles'] ?? []);
$standards = (array)($company['standards'] ?? []);
$activities = (array)($company['activities'] ?? []);
$objects = (array)($company['objects'] ?? []);
$customer = (array)($company['customer'] ?? []);

// Регионы капремонта — счётчики по регионам (для наглядности в блоке объектов)
$regionCount = [];
foreach ($kapremont as $k) {
	$r = (string)($k['region'] ?? '—');
	$regionCount[$r] = ($regionCount[$r] ?? 0) + 1;
}
arsort($regionCount);
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
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/manifest.json" />
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>
	<?= seo_og_tags($seoTitle, $seoDesc, '/public/assets/image/og/og-home-1200x630.jpg') ?>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>" as="style">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/promaintenance.css?v=<?= seo_ver() ?>">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<?= seo_analytics() ?>
	<?= seo_utm_script() ?>
	<?= seo_goals_script() ?>
	<style>
		/* ============================================================
		   /company — оформление по дизайн-системе главной (ProMaintenance)
		   ============================================================ */

		/* ---------- HERO ---------- */
		.cmp-hero {
			position: relative;
			background: #171310;
			color: #fff;
			overflow: hidden;
			display: flex;
			flex-direction: column;
			justify-content: flex-end;
			min-height: 520px;
			padding-bottom: 64px;
		}

		.cmp-hero__bg {
			position: absolute;
			inset: 0;
		}

		.cmp-hero__bg img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		.cmp-hero__shade {
			position: absolute;
			inset: 0;
			background: linear-gradient(90deg, rgba(15, 14, 13, .96) 0%, rgba(15, 14, 13, .86) 44%, rgba(15, 14, 13, .5) 100%),
				linear-gradient(180deg, rgba(15, 14, 13, .8) 0%, rgba(15, 14, 13, 0) 42%);
		}

		.cmp-hero__inner {
			position: relative;
			z-index: 2;
		}

		.cmp-hero h1 {
			margin: 22px 0 0;
			font-size: clamp(42px, 5.6vw, 92px);
			font-weight: 450;
			line-height: .98;
			letter-spacing: -.03em;
			text-transform: uppercase;
			max-width: 18ch;
		}

		.cmp-hero h1 span {
			color: #c7ab94;
		}

		.cmp-hero__lead {
			margin: 26px 0 0;
			max-width: 62ch;
			color: rgba(240, 239, 237, .78);
			font-size: 15px;
			line-height: 1.7;
		}

		.cmp-facts {
			display: grid;
			grid-template-columns: repeat(4, minmax(0, 1fr));
			gap: 1px;
			background: rgba(255, 255, 255, .14);
			border: 1px solid rgba(255, 255, 255, .14);
			margin-top: 46px;
		}

		.cmp-fact {
			background: rgba(23, 19, 16, .78);
			padding: 22px 24px;
		}

		.cmp-fact dt {
			color: rgba(240, 239, 237, .5);
			letter-spacing: .12em;
			text-transform: uppercase;
			font-size: 10px;
			margin: 0 0 10px;
		}

		.cmp-fact dd {
			margin: 0;
			font-size: 19px;
			font-weight: 500;
			line-height: 1.25;
		}

		.cmp-fact dd small {
			display: block;
			color: rgba(240, 239, 237, .6);
			font-size: 12px;
			font-weight: 400;
			margin-top: 6px;
		}

		/* ---------- СЕКЦИИ ---------- */
		.cmp-section {
			padding-block: clamp(60px, 7vw, 120px);
		}

		.cmp-grid {
			display: grid;
			grid-template-columns: repeat(3, minmax(0, 1fr));
			gap: 1px;
			background: var(--line);
			border: 1px solid var(--line);
			margin-top: 46px;
		}

		.cmp-card {
			background: #fff;
			padding: 30px 28px 34px;
			display: flex;
			flex-direction: column;
			gap: 14px;
		}

		.cmp-card h3 {
			margin: 0;
			font-size: 20px;
			font-weight: 500;
			line-height: 1.25;
		}

		.cmp-card p {
			margin: 0;
			color: rgba(23, 19, 16, .68);
			font-size: 14px;
			line-height: 1.68;
		}

		.cmp-card__num {
			color: #c7ab94;
			font-size: 11px;
			letter-spacing: .16em;
			text-transform: uppercase;
		}

		.cmp-card__link {
			margin-top: auto;
			padding-top: 6px;
		}

		/* ---------- РЕКВИЗИТЫ ---------- */
		.cmp-requ {
			margin: 46px 0 0;
			border-top: 1px solid var(--line);
		}

		.cmp-requ__row {
			display: grid;
			grid-template-columns: minmax(180px, .38fr) 1fr;
			gap: 24px;
			padding: 18px 0;
			border-bottom: 1px solid var(--line);
		}

		.cmp-requ dt {
			color: rgba(23, 19, 16, .55);
			font-size: 12px;
			letter-spacing: .1em;
			text-transform: uppercase;
		}

		.cmp-requ dd {
			margin: 0;
			font-size: 16px;
			line-height: 1.55;
		}

		.cmp-requ dd a {
			border-bottom: 1px solid rgba(23, 19, 16, .3);
		}

		/* ---------- СПИСОК ОБЪЕКТОВ ---------- */
		.cmp-objects {
			margin: 40px 0 0;
			padding: 0;
			list-style: none;
			border-top: 1px solid var(--line);
		}

		.cmp-objects li {
			display: grid;
			grid-template-columns: 34px minmax(0, 1fr) minmax(160px, .32fr);
			gap: 18px;
			align-items: baseline;
			padding: 15px 0;
			border-bottom: 1px solid var(--line);
		}

		.cmp-objects .n {
			color: #c7ab94;
			font-size: 12px;
			font-weight: 600;
		}

		.cmp-objects .t {
			font-size: 15px;
			line-height: 1.5;
		}

		.cmp-objects .r {
			color: rgba(23, 19, 16, .55);
			font-size: 12.5px;
			text-align: right;
		}

		/* ---------- FAQ ---------- */
		.cmp-faq {
			margin: 42px 0 0;
			border-top: 1px solid var(--line);
		}

		.cmp-faq details {
			border-bottom: 1px solid var(--line);
		}

		.cmp-faq summary {
			display: flex;
			align-items: center;
			gap: 18px;
			padding: 22px 0;
			cursor: pointer;
			list-style: none;
		}

		.cmp-faq summary::-webkit-details-marker {
			display: none;
		}

		.cmp-faq summary span {
			color: #c7ab94;
			font-size: 12px;
			font-weight: 600;
		}

		.cmp-faq summary h3 {
			margin: 0;
			font-size: 17px;
			font-weight: 500;
			line-height: 1.4;
			flex: 1;
		}

		.cmp-faq summary svg {
			flex-shrink: 0;
			transition: transform .3s var(--ease);
		}

		.cmp-faq details[open] summary svg {
			transform: rotate(45deg);
		}

		.cmp-faq__body p {
			margin: 0 0 24px;
			padding-right: 40px;
			color: rgba(23, 19, 16, .72);
			font-size: 15px;
			line-height: 1.7;
			max-width: 78ch;
		}

		/* ---------- ЗАКАЗЧИК ---------- */
		.cmp-customer {
			margin-top: 44px;
			border: 1px solid var(--line);
			padding: clamp(26px, 3vw, 44px);
			display: grid;
			grid-template-columns: minmax(0, 1fr) minmax(0, 1.4fr);
			gap: 34px;
			align-items: center;
		}

		.cmp-customer__name {
			font-size: clamp(22px, 2.4vw, 34px);
			font-weight: 500;
			line-height: 1.2;
			margin: 0;
		}

		.cmp-customer p {
			margin: 0;
			color: rgba(23, 19, 16, .7);
			font-size: 14.5px;
			line-height: 1.7;
		}

		.cmp-note {
			margin-top: 26px;
			color: rgba(23, 19, 16, .6);
			font-size: 13px;
			line-height: 1.65;
		}

		@media (width<=992px) {
			.cmp-facts {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}

			.cmp-grid {
				grid-template-columns: repeat(2, minmax(0, 1fr));
			}

			.cmp-customer {
				grid-template-columns: 1fr;
			}
		}

		@media (width<=640px) {
			.cmp-facts,
			.cmp-grid {
				grid-template-columns: 1fr;
			}

			.cmp-requ__row {
				grid-template-columns: 1fr;
				gap: 6px;
			}

			.cmp-objects li {
				grid-template-columns: 28px minmax(0, 1fr);
			}

			.cmp-objects .r {
				grid-column: 2;
				text-align: left;
			}
		}
	</style>
</head>

<body class="company-page" data-page="inner">
	<?php include_once __DIR__ . '/../../components/header.php'; ?>

	<main class="main">

		<!-- HERO -->
		<section class="cmp-hero" aria-labelledby="cmp-hero-title">
			<div class="cmp-hero__bg" aria-hidden="true">
				<img src="/public/assets/image/Components/banner/hero.jpg" alt="" loading="eager" fetchpriority="high" decoding="async" width="1920" height="1080">
				<div class="cmp-hero__shade"></div>
			</div>
			<div class="cmp-hero__inner section-shell">
				<p class="eyebrow" style="color: rgba(240,239,237,.62);"><?= $h($fssSec('О компании')) ?></p>
				<h1 id="cmp-hero-title">Фаворит-<br><span>Строй-Снаб</span></h1>
				<p class="cmp-hero__lead"><?= $h($company['about'] ?? '') ?></p>

				<dl class="cmp-facts">
					<div class="cmp-fact">
						<dt>Генеральный директор</dt>
						<dd><?= $h($ceo) ?><small><?= $h($company['ceo_position'] ?? 'Генеральный директор') ?></small></dd>
					</div>
					<div class="cmp-fact">
						<dt>Выполнено объектов</dt>
						<dd><?= $h((string)seo_config('objects')) ?><small><?= $h((string)$kapremontCount) ?> капремонтов · <?= $h((string)$schoolsCount) ?> школ оснащено</small></dd>
					</div>
					<div class="cmp-fact">
						<dt>На рынке</dt>
						<dd><?= $h((string)seo_config('founded_market')) ?> г.<small>ООО — с <?= $h($founded) ?> года</small></dd>
					</div>
					<div class="cmp-fact">
						<dt>Гарантия</dt>
						<dd><?= $h((string)seo_config('guarantee_years')) ?> лет<small>СРО · 44-ФЗ · 223-ФЗ</small></dd>
					</div>
				</dl>
			</div>
		</section>

		<!-- НАПРАВЛЕНИЯ ДЕЯТЕЛЬНОСТИ -->
		<section class="cmp-section section-shell" id="activities" aria-labelledby="activities-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Направления')) ?></p>
				<span class="eyebrow">5 направлений</span>
			</div>
			<div class="work-heading">
				<h2 id="activities-title">Чем<br>мы занимаемся</h2>
				<p>Комплексная деятельность по обеспечению нужд силовых<br>и государственных структур РФ — от производства до оснащения.</p>
			</div>

			<div class="cmp-grid">
				<?php foreach ($activities as $ai => $act): ?>
					<article class="cmp-card">
						<span class="cmp-card__num"><?= str_pad((string)($ai + 1), 2, '0', STR_PAD_LEFT) ?></span>
						<h3><?= $h($act['title']) ?></h3>
						<p><?= $h($act['text']) ?></p>
						<?php if (!empty($act['url'])): ?>
							<a class="text-link cmp-card__link" href="<?= $h($act['url']) ?>" title="<?= $h($act['title']) ?>">Подробнее
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
									<path d="M7 7h10v10"></path>
									<path d="M7 17 17 7"></path>
								</svg>
							</a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- РЕКВИЗИТЫ -->
		<section class="cmp-section section-shell" id="requisites" aria-labelledby="requisites-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Реквизиты')) ?></p>
				<span class="eyebrow">Данные из ЕГРЮЛ</span>
			</div>
			<div class="work-heading">
				<h2 id="requisites-title">Юридические<br>реквизиты</h2>
				<p>Форма собственности — <?= $h($company['legal_form_full'] ?? 'ООО') ?>.<br>Зарегистрировано <?= $h(date('d.m.Y', strtotime($regDate))) ?>.</p>
			</div>

			<dl class="cmp-requ">
				<div class="cmp-requ__row">
					<dt>Полное наименование</dt>
					<dd><?= $h($company['legal_name'] ?? '') ?></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>Форма собственности</dt>
					<dd><?= $h($company['legal_form_full'] ?? 'Общество с ограниченной ответственностью') ?> (<?= $h($company['legal_form'] ?? 'ООО') ?>)</dd>
				</div>
				<div class="cmp-requ__row">
					<dt>Руководитель</dt>
					<dd><?= $h($ceo) ?> — <?= $h(mb_strtolower((string)($company['ceo_position'] ?? 'генеральный директор'))) ?></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>ИНН / КПП</dt>
					<dd><?= $h($inn) ?> / <?= $h($kpp) ?></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>ОГРН</dt>
					<dd><?= $h($ogrn) ?></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>Дата регистрации</dt>
					<dd><?= $h(date('d.m.Y', strtotime($regDate))) ?></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>Юридический / фактический адрес</dt>
					<dd><?= $h($addrFull) ?></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>Телефон</dt>
					<dd><a href="tel:<?= $h($phoneHref) ?>" title="Позвонить: <?= $h($phone) ?>"><?= $h($phone) ?></a></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>Электронная почта</dt>
					<dd><a href="mailto:<?= $h($emailHref) ?>" title="Написать на email"><?= $h($email) ?></a></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>Соцсети и мессенджеры</dt>
					<dd>
						<a href="<?= $h($telegram) ?>" target="_blank" rel="noopener noreferrer" title="Telegram">Telegram</a> ·
						<a href="<?= $h($yandexMaps) ?>" target="_blank" rel="noopener noreferrer" title="Яндекс.Карты">Яндекс.Карты</a>
					</dd>
				</div>
				<div class="cmp-requ__row">
					<dt>Документы и договор</dt>
					<dd>Договор · Сметы · КП — <a href="/contacts#ways" title="Договор, сметы и коммерческие предложения">запросить</a></dd>
				</div>
				<div class="cmp-requ__row">
					<dt>На рынке с</dt>
					<dd><?= $h((string)seo_config('founded_market')) ?> года · ООО — с <?= $h($founded) ?> года</dd>
				</div>
			</dl>
		</section>

		<!-- ЗАКАЗЧИК -->
		<section class="cmp-section section-shell" id="customer" aria-labelledby="customer-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Заказчик')) ?></p>
				<span class="eyebrow">Госзаказчик</span>
			</div>
			<div class="work-heading">
				<h2 id="customer-title">Работаем<br>с силовыми<br>структурами</h2>
				<p>Подтверждённый опыт работы с федеральными<br>и региональными заказчиками — от школ до дворцов спорта.</p>
			</div>

			<div class="cmp-customer">
				<p class="cmp-customer__name"><?= $h($customer['name'] ?? '') ?></p>
				<p><?= $h($customer['text'] ?? '') ?></p>
			</div>
		</section>

		<!-- ВЫПОЛНЕННЫЕ ОБЪЕКТЫ -->
		<section class="cmp-section section-shell" id="objects" aria-labelledby="objects-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Выполненные объекты')) ?></p>
				<span class="eyebrow">Факт, не реклама</span>
			</div>
			<div class="work-heading">
				<h2 id="objects-title">Что<br>сдано</h2>
				<p>Каждый объект из списка выполнен компанией — с проектированием,<br>строительством, оснащением и закрывающими документами.</p>
			</div>

			<div class="cmp-grid">
				<?php foreach ($objects as $oi => $o): ?>
					<article class="cmp-card">
						<span class="cmp-card__num"><?= $h($o['meta'] ?? '') ?></span>
						<h3><?= $h($o['title']) ?></h3>
						<p><?= $h($o['text']) ?></p>
						<?php if (!empty($o['url'])): ?>
							<a class="text-link cmp-card__link" href="<?= $h($o['url']) ?>" title="<?= $h($o['title']) ?>">Смотреть
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
									<path d="M7 7h10v10"></path>
									<path d="M7 17 17 7"></path>
								</svg>
							</a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- КАПРЕМОНТ 16 ОБЪЕКТОВ -->
		<section class="cmp-section section-shell" id="kapremont" aria-labelledby="kapremont-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Капитальный ремонт')) ?></p>
				<span class="eyebrow"><?= $h((string)$kapremontCount) ?> объектов</span>
			</div>
			<div class="work-heading">
				<h2 id="kapremont-title">Школы, спортзалы<br>и дворец спорта</h2>
				<p><?= $h((string)$kapremontCount) ?> объектов социального назначения:<br>
					<?php
					$rg = [];
					foreach ($regionCount as $rn => $rc) {
						$rg[] = $rn . ' — ' . $rc;
					}
					echo $h(implode('; ', $rg));
					?>
				</p>
			</div>

			<ul class="cmp-objects">
				<?php foreach ($kapremont as $ki => $k): ?>
					<li>
						<span class="n"><?= str_pad((string)($ki + 1), 2, '0', STR_PAD_LEFT) ?></span>
						<span class="t"><?= $h($k['name']) ?></span>
						<span class="r"><?= $h($k['region']) ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<p class="cmp-note">По каждому объекту: обследование, проектно-сметная документация, техническое задание, график работ и закрывающие документы. Работы выполняются этапами — с согласованным графиком, чтобы учебный процесс объекта не останавливался.</p>
		</section>

		<!-- ОСНАЩЕНИЕ ШКОЛ -->
		<section class="cmp-section section-shell" id="schools" aria-labelledby="schools-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Оснащение')) ?></p>
				<span class="eyebrow">Стандарты Минпросвещения РФ</span>
			</div>
			<div class="work-heading">
				<h2 id="schools-title"><?= $h((string)$schoolsCount) ?> школ<br>под ключ</h2>
				<p><?= $h((string)($company['schools']['text'] ?? '')) ?></p>
			</div>

			<div class="cmp-grid">
				<?php foreach ((array)($company['schools']['items'] ?? []) as $si => $si_item): ?>
					<article class="cmp-card">
						<span class="cmp-card__num"><?= str_pad((string)($si + 1), 2, '0', STR_PAD_LEFT) ?></span>
						<h3><?= $h($si_item) ?></h3>
					</article>
				<?php endforeach; ?>
			</div>
			<p class="cmp-note">Оснащение выполняется по отдельному контракту и по стандартам Минпросвещения РФ — с паспортами на каждую позицию, спецификациями и закрывающими документами.</p>
		</section>

		<!-- СТАНДАРТЫ И ПРИНЦИПЫ -->
		<section class="cmp-section section-shell" id="standards" aria-labelledby="standards-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Стандарты')) ?></p>
				<span class="eyebrow">Допуски и гарантии</span>
			</div>
			<div class="work-heading">
				<h2 id="standards-title">Почему<br>нам доверяют</h2>
				<p>Отработанная логистика, квалифицированные специалисты<br>и организационная дисциплина в каждом объекте.</p>
			</div>

			<div class="cmp-grid">
				<?php foreach ($standards as $st): ?>
					<article class="cmp-card">
						<h3><?= $h($st['name']) ?></h3>
						<p><?= $h($st['text']) ?></p>
					</article>
				<?php endforeach; ?>
				<?php foreach ($principles as $pr): ?>
					<article class="cmp-card">
						<h3><?= $h($pr['title']) ?></h3>
						<p><?= $h($pr['text']) ?></p>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- ВОПРОСЫ -->
		<section class="cmp-section section-shell" id="faq" aria-labelledby="faq-title">
			<div class="section-topline">
				<p class="eyebrow"><?= $h($fssSec('Вопросы')) ?></p>
				<span class="eyebrow">Отвечаем по фактам</span>
			</div>
			<div class="work-heading">
				<h2 id="faq-title">Частые вопросы<br>о компании</h2>
				<p>Остальное подскажет инженер — оставьте заявку<br>и задайте вопрос напрямую.</p>
			</div>

			<div class="cmp-faq">
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
						<div class="cmp-faq__body"><p><?= $h($qa[1]) ?></p></div>
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
				<a class="enquiry-telegram" href="<?= $h($telegram) ?>" target="_blank" rel="noopener noreferrer" title="Telegram: @fss_rus">@fss_rus</a>
				<span class="enquiry-telegram-note">Самый быстрый канал: фото площадки, чертежи, вопросы по срокам и комплектации.</span>
				<a class="enquiry-docs" href="/contacts#ways" title="Договор, сметы и коммерческие предложения">Документы и договор</a>
				<span class="enquiry-docs-note">Договор · Сметы · КП</span>
				<span class="enquiry-location" style="color:#aaa49e; margin-top: 32px; font-size: 12px; display: block;">
					<?= $h($addrFull) ?><br>Работаем по Москве, Московской области и России
				</span>
			</div>
			<?php include_once __DIR__ . '/../../components/enquiry_form.php'; ?>
		</section>
	</main>

	<?php include_once __DIR__ . '/../../components/footer.php'; ?>

	<?php
	// --- Разметка страницы: AboutPage + Organization (руководитель, клиент,
	// выполненные объекты) + FAQPage. Организация с NAP уже размечена в
	// общем графе из sheoma.php — здесь только её расширение и тип страницы.
	$__org = [
		'@type' => 'Organization',
		'@id' => $base . '/company#organization',
		'name' => $company['legal_name_short'] ?? 'ФАВОРИТ-СТРОЙ-СНАБ',
		'legalName' => $company['legal_name'] ?? '',
		'url' => $base . '/company',
		'inn' => $inn,
		'ogrn' => $ogrn,
		'description' => $company['about'] ?? '',
		'founder' => [
			'@type' => 'Person',
			'name' => $ceo,
			'jobTitle' => $company['ceo_position'] ?? 'Генеральный директор',
		],
		'employee' => [
			'@type' => 'Person',
			'name' => $ceo,
			'jobTitle' => $company['ceo_position'] ?? 'Генеральный директор',
		],
		'address' => [
			'@type' => 'PostalAddress',
			'postalCode' => (string)($addr['postal'] ?? ''),
			'addressCountry' => 'RU',
			'addressRegion' => (string)($addr['region'] ?? ''),
			'addressLocality' => (string)($addr['locality'] ?? ''),
			'streetAddress' => (string)($addr['street'] ?? ''),
		],
		'contactPoint' => [[
			'@type' => 'ContactPoint',
			'telephone' => $phone,
			'email' => $email,
			'contactType' => 'customer service',
			'areaServed' => 'RU',
			'availableLanguage' => ['Russian'],
		]],
		'hasCredential' => array_map(fn($st) => [
			'@type' => 'EducationalOccupationalCredential',
			'credentialCategory' => 'accreditation',
			'name' => $st['name'],
			'description' => $st['text'],
		], $standards),
		'knowsAbout' => array_map(fn($a) => $a['title'], $activities),
		'sameAs' => array_values(array_filter([$telegram, $yandexMaps])),
	];

	$__nodes = [
		[
			'@type' => 'AboutPage',
			'@id' => seo_canonical() . '#webpage',
			'url' => seo_canonical(),
			'name' => $seoTitle,
			'description' => $seoDesc,
			'inLanguage' => 'ru-RU',
			'isPartOf' => ['@id' => $base . '/#website'],
			'about' => ['@id' => $base . '/company#organization'],
			'breadcrumb' => ['@id' => seo_canonical() . '#breadcrumb'],
			'mainEntity' => ['@id' => $base . '/company#organization'],
		],
		$__org,
		[
			'@type' => 'ItemList',
			'@id' => seo_canonical() . '#objects',
			'name' => 'Выполненные объекты капитального ремонта',
			'numberOfItems' => count($kapremont),
			'itemListOrder' => 'https://schema.org/ItemListOrderAscending',
			'itemListElement' => array_map(fn($k, $i) => [
				'@type' => 'ListItem',
				'position' => $i + 1,
				'name' => $k['name'],
				'description' => $k['type'] . ', ' . $k['region'],
			], $kapremont, array_keys($kapremont)),
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
