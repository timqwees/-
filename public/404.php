<?php
require_once __DIR__ . '/../app/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Страница не найдена | ФАВОРИТ-СТРОЙ-СНАБ</title>
<meta name="robots" content="noindex, follow">
<link rel="canonical" href="<?= htmlspecialchars((string)seo_config('base_url')) ?>/404">
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;background:#fafafa;color:#171310;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:calc(var(--fss-header-h, 90px) + 20px) 20px 20px}
.container{text-align:center;max-width:480px}
.code{font-size:clamp(80px,15vw,160px);font-weight:800;color:#EA580C;line-height:1;margin:0}
.message{font-size:clamp(18px,3vw,24px);margin:20px 0 30px;color:#44403C}
.btn{display:inline-flex;align-items:center;gap:10px;background:#EA580C;color:#fff;padding:16px 32px;border-radius:12px;font-weight:700;font-size:16px;text-decoration:none;transition:background .2s,transform .15s}
.btn:hover{background:#C2410C;transform:translateY(-2px)}
</style>
</head>
<body data-page="inner">
<?php include_once __DIR__ . '/../components/header.php'; ?>
<div class="container">
<div class="code">404</div>
<p class="message">Страница не найдена или перемещена</p>
<a class="btn" href="/">Вернуться на главную</a>
</div>
</body>
</html>