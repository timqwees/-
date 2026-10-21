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
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;background:#f0efed;color:#171310;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:calc(var(--fss-header-h, 90px) + 20px) 20px 20px}
.container{text-align:center;max-width:480px}
.code{font-size:clamp(80px,15vw,160px);font-weight:800;color:#c7ab94;line-height:1;margin:0;letter-spacing:-.05em}
.message{font-size:clamp(18px,3vw,24px);margin:20px 0 30px;color:#6c6864}
.btn{display:inline-flex;align-items:center;gap:10px;background:#171310;color:#fff;padding:15px 22px;border-radius:6px;font-weight:500;font-size:13px;text-decoration:none;transition:background .3s,transform .2s}
.btn:hover{background:#424244;transform:translateY(-2px)}
</style>
</head>
<body data-page="inner">
<?php include_once __DIR__ . '/../components/header.php'; ?>
<div class="container fss-legal">
<div class="code">404</div>
<p class="message">Страница не найдена или перемещена</p>
<a class="btn" href="/">Вернуться на главную</a>
</div>
</body>
</html>