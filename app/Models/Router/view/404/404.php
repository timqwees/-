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
	<title>Страница не найдена (404) — <?= htmlspecialchars(seo_config('brand')) ?></title>
	<meta name="robots" content="noindex, follow">
	<meta name="description" content="Запрошенная страница не найдена. Перейдите в каталог услуг быстровозводимых зданий или позвоните нам.">
	<link rel="icon" type="image/png" sizes="32x32" href="/public/assets/image/logo/favicon/favicon-32x32.png">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261009">
	<style>
		body{font-family:Manrope,Inter,system-ui,sans-serif;background:#f8fafc;color:#0f172a;margin:0}
		.wrap{max-width:900px;margin:0 auto;padding:64px 20px}
		.card{background:#fff;border:1px solid #e2e8f0;border-radius:24px;padding:40px;box-shadow:0 10px 40px rgba(15,23,42,.06)}
		h1{font-size:64px;margin:0;color:#059669;font-weight:800}
		h2{font-size:26px;margin:12px 0 16px}
		a{color:#059669;font-weight:600}
		ul{padding-left:20px;line-height:2}
		.btn{display:inline-block;margin-top:24px;background:linear-gradient(135deg,#FB923C,#EA580C);box-shadow:0 10px 26px rgba(249,115,22,.35);color:#fff;padding:14px 28px;border-radius:999px;text-decoration:none}
	</style>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261009">
</head>
<body>
	<div class="wrap">
		<div class="card">
			<h1>404</h1>
			<h2>Такой страницы нет — но нужную услугу мы найдём</h2>
			<p>Возможно, страница переехала или ссылка устарела. Вот самые популярные разделы:</p>
			<ul>
				<?php foreach ($__popular as $slug => $s): ?>
					<li><a href="/services/<?= htmlspecialchars($slug) ?>"><?= htmlspecialchars($s['title']) ?></a></li>
				<?php endforeach; ?>
				<li><a href="/uslugi">Все услуги</a> · <a href="/portfolio">Портфолио</a> · <a href="/about">О нас</a></li>
			</ul>
			<p>Или позвоните: <a href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', seo_config('phone'))) ?>"><?= htmlspecialchars(seo_config('phone_display')) ?></a> — ответим за <?= htmlspecialchars(seo_config('callback_minutes')) ?> минут и посчитаем смету.</p>
			<a class="btn" href="/">На главную</a>
		</div>
	</div>
</body>
</html>
