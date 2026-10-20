<?php
/**
 * Поиск по сайту — закрывает SearchAction (/search?q=) и opensearch.
 * Ищет по services.php: title / description / queries. Без БД.
 */
require_once __DIR__ . '/../../app/bootstrap.php';

$q = trim(mb_substr($_GET['q'] ?? '', 0, 120));
$all = seo_services();
$results = [];
if ($q !== '') {
	$needle = mb_strtolower($q);
	foreach ($all as $slug => $s) {
		$hay = mb_strtolower($s['title'] . ' ' . $s['description'] . ' ' . implode(' ', $s['queries'] ?? []));
		$score = 0;
		foreach (preg_split('/\s+/u', $needle) as $word) {
			if ($word !== '' && mb_strpos($hay, $word) !== false) $score++;
		}
		if ($score > 0) $results[] = ['slug' => $slug, 'score' => $score] + $s;
	}
	usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
	$results = array_slice($results, 0, 18);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-latin.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-cyrillic.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/fonts/WixMadeforDisplay/WixMadeforDisplay-Variable.woff2" crossorigin><link rel="stylesheet" href="/public/assets/vendor/fonts/fonts.css">
	<style> body { font-family: 'DM Sans Variable', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; }</style>
	<style>
		/* Отступ под фиксированную шапку (90 / 78 / 74) — иначе хлебные крошки
		   и первый экран уезжают под header и остаются невидимыми. */
		.page-hero {
			padding-top: max(112px, calc(var(--fss-header-h, 90px) + 22px));
		}
	</style>

	<title>Поиск по услугам<?= $q !== '' ? ' — ' . htmlspecialchars($q) : '' ?> | <?= htmlspecialchars(seo_config('brand')) ?></title>
	<meta name="description" content="Поиск по услугам <?= htmlspecialchars(seo_config('brand')) ?>: склады, ангары, АБК, общежития, капремонт, спецтехника.">
	<meta name="robots" content="noindex, follow">
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<?= seo_canonical_tags() ?>
	<?= seo_og_tags('Поиск по услугам — ' . seo_config('brand'), 'Поиск по каталогу услуг ФАВОРИТ-СТРОЙ-СНАБ: склады, ангары, АБК, общежития, капремонт, спецтехника.') ?>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css"><noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
</head>
<body class="font-sans text-gray-800" data-page="inner">
	<?php include_once __DIR__ . '/../components/header.php'; ?>

	<section class="page-hero">
		<div class="page-hero-inner">
			<nav class="page-crumb"><a href="/" title="Главная">Главная</a><span class="sep">/</span><a href="/uslugi" title="Услуги">Услуги</a><span class="sep">/</span><span>Поиск</span></nav>
			<span class="page-eyebrow"><i class="fa-solid fa-magnifying-glass"></i> Поиск по сайту</span>
			<h1 class="pg-h1">Найдите нужную услугу</h1>
			<form action="/search" method="get" role="search" class="search-form">
				<input type="search" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Например: ангар под ключ" class="search-input" autofocus>
				<button type="submit" class="btn-sun"><i class="fa-solid fa-magnifying-glass"></i> Найти</button>
			</form>
			<p class="page-lead">Популярно:
				<a href="/search?q=ангар" title="Поиск: ангар">ангар</a>, <a href="/search?q=склад" title="Поиск: склад">склад</a>, <a href="/search?q=АБК" title="Поиск: АБК">АБК</a>, <a href="/search?q=общежитие" title="Поиск: общежитие">общежитие</a>, <a href="/search?q=спецтехника" title="Поиск: спецтехника">спецтехника</a>
			</p>
		</div>
	</section>

	<main class="page-section text-[#171310]">
		<?php if ($q !== ''): ?>
			<h2 class="sec-title mb-6">Результаты: <?= count($results) ?></h2>
			<?php if ($results): ?>
				<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
					<?php foreach ($results as $r): ?>
						<a href="/services/<?= htmlspecialchars($r['slug']) ?>" title="<?= htmlspecialchars($r['title']) ?>" class="pcard">
							<span class="pcard-ico"><i class="fa-solid fa-cube"></i></span>
							<h3><?= htmlspecialchars($r['title']) ?></h3>
							<p><?= htmlspecialchars(mb_substr($r['description'], 0, 160)) ?>…</p>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<p class="sec-sub">Ничего не найдено. Позвоните <a href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', seo_config('phone'))) ?>" title="Позвонить" class="text-[#C2410C] font-semibold"><?= htmlspecialchars(seo_config('phone_display')) ?></a> — подберём решение за <?= htmlspecialchars(seo_config('callback_minutes')) ?> минут.</p>
			<?php endif; ?>
		<?php endif; ?>

		<div class="mt-8 flex gap-3">
			<a href="/uslugi" title="Все услуги" class="btn-outline"><i class="fa-solid fa-list"></i> Все услуги</a>
			<a href="/portfolio" title="Портфолио" class="btn-outline"><i class="fa-solid fa-images"></i> Портфолио</a>
			<a href="/search/blog" title="Поиск по блогу" class="btn-outline"><i class="fa-solid fa-magnifying-glass"></i> Поиск по блогу</a>
		</div>
	</main>

	<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
