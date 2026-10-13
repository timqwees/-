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
	<title>Поиск<?= $q !== '' ? ' — ' . htmlspecialchars($q) : '' ?> | <?= htmlspecialchars(seo_config('brand')) ?></title>
	<meta name="description" content="Поиск по услугам <?= htmlspecialchars(seo_config('brand')) ?>: склады, ангары, АБК, общежития, капремонт, спецтехника.">
	<meta name="robots" content="noindex, follow">
<?= seo_canonical_tags() ?>
<?= seo_og_tags('Поиск по сайту — ' . seo_config('brand'), 'Быстрый поиск по 18 услугам: БВЗ, капремонт, грузоперевозки, спецтехника.') ?>
	<link rel="icon" type="image/png" sizes="32x32" href="/public/assets/image/favicon/favicon-32x32.png">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= htmlspecialchars(seo_ver()) ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261009">
<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261009">
</head>
<body>
	<main style="max-width:900px;margin:0 auto;padding:48px 20px;font-family:'Wix Madefor Display',Manrope,system-ui,sans-serif">
		<p><a href="/">Главная</a> / <a href="/uslugi">Услуги</a> / Поиск</p>
		<h1>Поиск по сайту</h1>
		<form action="/search" method="get" role="search">
			<input type="search" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Например: ангар под ключ" style="width:70%;padding:12px;border:1px solid #ccc;border-radius:8px">
			<button type="submit" style="padding:12px 24px;border-radius:8px;background:#059669;color:#fff;border:0">Найти</button>
		</form>
		<?php if ($q !== ''): ?>
			<h2>Результаты: <?= count($results) ?></h2>
			<?php if ($results): ?>
				<ul>
					<?php foreach ($results as $r): ?>
						<li style="margin:12px 0">
							<a href="/services/<?= htmlspecialchars($r['slug']) ?>"><strong><?= htmlspecialchars($r['title']) ?></strong></a>
							<br><small><?= htmlspecialchars(mb_substr($r['description'], 0, 160)) ?>…</small>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p>Ничего не найдено. Позвоните <a href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', seo_config('phone'))) ?>"><?= htmlspecialchars(seo_config('phone_display')) ?></a> — подберём решение за <?= htmlspecialchars(seo_config('callback_minutes')) ?> минут.</p>
			<?php endif; ?>
		<?php else: ?>
			<p>Популярно: <a href="/search?q=ангар">ангар</a>, <a href="/search?q=склад">склад</a>, <a href="/search?q=АБК">АБК</a>, <a href="/search?q=общежитие">общежитие</a>, <a href="/search?q=спецтехника">спецтехника</a></p>
		<?php endif; ?>
		<p><a href="/uslugi">Все услуги</a> · <a href="/portfolio">Портфолио</a> · <a href="/projects">Проекты</a></p>
	</main>
</body>
</html>
