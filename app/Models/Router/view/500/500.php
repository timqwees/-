<?php
/**
 * Страница 500 — единая точка для необработанных ошибок.
 * Детали (сообщение/файл/строка) показываются только в DEV-режиме.
 *
 * @var array{message:string,where:string,dev:bool}|null $fss_error
 */
$__err = $fss_error ?? null;
$__dev = (bool)($__err['dev'] ?? false);
http_response_code(500);
header('X-Robots-Tag: noindex, nofollow', true);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Внутренняя ошибка (500) — ФСС.РУС</title>
	<meta name="robots" content="noindex, nofollow">
	<link rel="icon" type="image/png" sizes="32x32" href="/public/assets/image/logo/favicon/favicon-32x32.png">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261009">
	<style>
		body{font-family:Manrope,Inter,system-ui,sans-serif;background:#f8fafc;color:#0f172a;margin:0;padding:calc(var(--fss-header-h, 90px) + 20px) 20px 20px}
		.wrap{max-width:900px;margin:0 auto;padding:64px 20px}
		.card{background:#fff;border:1px solid #e2e8f0;border-radius:24px;padding:40px;box-shadow:0 10px 40px rgba(15,23,42,.06)}
		h1{font-size:64px;margin:0;color:#dc2626;font-weight:800}
		h2{font-size:26px;margin:12px 0 16px}
		a{color:#059669;font-weight:600}
		.btn{display:inline-block;margin-top:24px;background:linear-gradient(135deg,#FB923C,#EA580C);box-shadow:0 10px 26px rgba(249,115,22,.35);color:#fff;padding:14px 28px;border-radius:999px;text-decoration:none}
		pre{background:#0f172a;color:#e2e8f0;padding:16px 18px;border-radius:14px;overflow:auto;font-size:13px;line-height:1.6;white-space:pre-wrap;word-break:break-word}
	</style>
</head>

<body data-page="inner">
	<?php include_once dirname(__DIR__, 5) . '/public/components/header.php'; ?>
	<div class="wrap">
		<div class="card">
			<h1>500</h1>
			<h2>Что-то пошло не так на сервере</h2>
			<p>Страница временно недоступна. Мы уже знаем о проблеме — обновите страницу через минуту или позвоните нам, поможем сразу.</p>
			<?php if ($__dev && $__err !== null): ?>
				<h2 style="font-size:18px">DEV: детали ошибки</h2>
				<pre><?= htmlspecialchars($__err['message'], ENT_QUOTES) . "\n\n" . htmlspecialchars($__err['where'], ENT_QUOTES) ?></pre>
			<?php endif; ?>
			<p>Или позвоните: <a href="tel:+79380180006">+7 938 018-00-06</a> — ответим за 7 минут и посчитаем смету.</p>
			<a class="btn" href="/">На главную</a>
		</div>
	</div>
</body>

</html>
