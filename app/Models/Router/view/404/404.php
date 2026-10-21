<?php
if (!defined('FSS_BOOT')) {
	require_once dirname(__DIR__, 5) . '/app/bootstrap.php';
}

http_response_code(404);
header('X-Robots-Tag: noindex, follow', true);

$__popular = array_slice(seo_services(), 0, 6, true);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Страница не найдена (404) — <?= htmlspecialchars((string)seo_config('brand')) ?></title>
	<meta name="robots" content="noindex, follow">
	<meta name="description" content="Запрошенная страница не найдена. Перейдите в каталог услуг быстровозводимых зданий или позвоните нам.">
	<link rel="icon" type="image/png" sizes="32x32" href="/public/assets/image/logo/favicon/favicon-32x32.png">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261009">
	<style>
		body{font-family:Manrope,Inter,system-ui,sans-serif;background:#f0efed;color:#171310;margin:0;padding:calc(var(--fss-header-h, 90px) + 20px) 20px 20px}
		.wrap{max-width:900px;margin:0 auto;padding:64px 20px}
		.card{background:#fff;border:1px solid #d3cfcb;border-radius:24px;padding:40px;box-shadow:0 10px 40px rgba(23,19,16,.06)}
		h1{font-size:64px;margin:0;color:#c7ab94;font-weight:800;letter-spacing:-.04em}
		h2{font-size:26px;margin:12px 0 16px;letter-spacing:-.03em;font-weight:500}
		p{color:#6c6864;line-height:1.6}
		a{color:#876b55;font-weight:600}
		ul{padding-left:20px;line-height:2}
		.btn{display:inline-block;margin-top:24px;background:#171310;color:#fff;box-shadow:none;padding:15px 22px;border-radius:6px;text-decoration:none;transition:background .3s}
		.btn:hover{background:#424244}
	</style>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261009">
</head>
<body data-page="inner">
	<?php include_once dirname(__DIR__, 5) . '/public/components/header.php'; ?>
	<div class="wrap">
		<div class="card fss-legal">
			<h1>404</h1>
			<h2>Такой страницы нет — но нужную услугу мы найдём</h2>
			<p>Возможно, страница переехала или ссылка устарела. Вот самые популярные разделы:</p>
			<ul>
				<?php foreach ($__popular as $slug => $s): ?>
					<li><a href="/services/<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($s['title']) ?></a></li>
				<?php endforeach; ?>
				<li><a href="/uslugi">Все услуги</a> · <a href="/portfolio">Портфолио</a> · <a href="/about">О нас</a></li>
			</ul>
			<p>Или позвоните: <a href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', (string)seo_config('phone'))) ?>"><?= htmlspecialchars((string)seo_config('phone_display')) ?></a> — ответим за <?= htmlspecialchars((string)seo_config('callback_minutes')) ?> минут и посчитаем смету.</p>
			<a class="btn" href="/">На главную</a>
		</div>
	</div>
</body>
</html>
