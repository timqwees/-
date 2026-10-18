<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
$inn = (string)seo_config('inn');
$ogrn = (string)seo_config('ogrn');
$ceo = (string)seo_config('ceo');
$founded = (string)seo_config('founded');
$foundedMarket = (string)seo_config('founded_market');
$regDate = (string)seo_config('registered_at');
$t = 'О компании — ФАВОРИТ-СТРОЙ-СНАБ';
$d = 'ООО «ФАВОРИТ-СТРОЙ-СНАБ» (ИНН ' . $inn . ', ОГРН ' . $ogrn . ') — на рынке с ' . $foundedMarket . ' года (ООО с ' . $founded . '), собственное производство БВЗ, фиксированная смета за 15 минут, сроки от 14 дней, гарантия 50+ лет.';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($t) ?></title>
	<meta name="description" content="<?= htmlspecialchars(seo_clip($d)) ?>">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<meta name="format-detection" content="telephone=yes">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="yandex-verification" content="27e0ba9980498150" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"></noscript>
	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/public/assets/image/logo/site.webmanifest" />
<?= seo_canonical_tags() ?>
<?= seo_verify_tags() ?>
<?= seo_og_tags($t, seo_clip($d)) ?>
	<link rel="search" type="application/opensearchdescription+xml" title="ФАВОРИТ-СТРОЙ-СНАБ" href="/opensearch.xml">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
	<link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<style> body { font-family: 'Wix Madefor Display', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
	<link rel="preload" href="/public/assets/distJS/main.js?v=20261018" as="script">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<script defer src="/public/assets/distJS/main.js?v=20261018"></script>
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<?= seo_analytics() ?>
	<?= seo_utm_script() ?>
	<?= seo_goals_script() ?>
	<style>
		body{background:#fff;color:#171310;font-family:"Wix Madefor Display",Manrope,Inter,Arial,sans-serif;margin:0}
		.xl{max-width:1280px;margin:0 auto;padding:0 24px}
		.xl-eyebrow{font-size:11px;font-weight:600;letter-spacing:.24em;text-transform:uppercase;color:#A1A1AA;margin-bottom:20px}
		.xl-btn{display:inline-flex;align-items:center;gap:10px;background:#171310;color:#fff;border-radius:999px;border:0;padding:13px 24px;font-size:14px;font-weight:600;cursor:pointer;transition:background .25s,transform .25s;text-decoration:none}
		.xl-btn:hover{background:#F97316;transform:translateY(-2px)}
		.xl-btn i{font-size:11px}
		/* HERO */
		.xl-hero{padding:40px 0 24px}
		.xl-hero-grid{display:grid;grid-template-columns:1fr .78fr;gap:32px;align-items:center;min-height:480px}
		.xl-hero h1{font-size:clamp(26px,3.2vw,42px);font-weight:700;line-height:1.12;letter-spacing:-1.2px;margin:0 0 32px;max-width:620px}
		.xl-scroll{display:flex;gap:10px;align-items:center;margin-top:56px}
		.xl-scroll.ar{width:30px;height:30px;border:1px solid #E4E4E7;border-radius:50%;display:grid;place-items:center;color:#71717A;font-size:10px}
		.xl-scroll .t{font-size:11px;color:#A1A1AA;letter-spacing:.06em;text-transform:uppercase;font-weight:600}
		.xl-auto{margin-top:48px;text-align:right;max-width:220px;margin-left:auto}
		.xl-auto .n1{font-size:13px;font-weight:600;color:#171310}
		.xl-auto .n2{font-size:12px;color:#A1A1AA;margin-top:4px}
		/* 3D куб (CSS) */
		.xl3{position:relative;width:100%;max-width:460px;aspect-ratio:1/.92;margin:0 auto}
		.xl3 .cube{position:absolute;top:46%;left:52%;transform:translate(-50%,-50%) rotateX(-16deg) rotateY(34deg);width:210px;height:210px;transform-style:preserve-3d}
		.xl3 .fc{position:absolute;width:210px;height:210px;border-radius:26px;background:linear-gradient(135deg,#fff,#fafafa);border:1px solid #ececef;box-shadow:inset 0 0 40px rgba(23,19,16,.03)}
		.xl3 .a{transform:translateZ(105px)}
		.xl3 .b{transform:rotateX(90deg) translateZ(105px);background:linear-gradient(135deg,#f6f6f7,#fff)}
		.xl3 .c{transform:rotateY(90deg) translateZ(105px);background:linear-gradient(135deg,#fdf4ee,#fbe9da)}
		.xl3 .sphere{position:absolute;top:16%;right:6%;width:82px;height:82px;border-radius:50%;background:radial-gradient(circle at 34% 28%,#fff 0%,#f0f0f2 45%,#d7d7db 100%);box-shadow:-14px 20px 34px rgba(23,19,16,.14)}
		.xl3 .sq{position:absolute;top:40%;left:2%;width:54px;height:54px;border-radius:12px;background:radial-gradient(circle at 34% 28%,#fff,#e6e6ea);box-shadow:10px 16px 26px rgba(23,19,16,.1)}
		.xl3 .dot{position:absolute;border-radius:50%;background:radial-gradient(circle at 34% 28%,#fff,#dcdce0);box-shadow:6px 10px 18px rgba(23,19,16,.08)}
		.xl3 .d1{width:34px;height:34px;bottom:12%;left:12%}
		.xl3 .d2{width:22px;height:22px;top:8%;left:34%}
		/* MARQUEE заголовок */
		.xl-marq{overflow:hidden;padding:36px 0;white-space:nowrap;margin:24px 0}
		.xl-marq-in{display:inline-block;animation:xlscroll 26s linear infinite}
		.xl-marq .big{display:inline-block;font-size:clamp(56px,8vw,116px);font-weight:800;letter-spacing:-3.5px;line-height:1}
		.xl-marq .dim{color:#EAEAEA}
		@keyframes xlscroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
		/* ABOUT */
		.xl-about{padding:56px 0}
		.xl-about-grid{display:grid;grid-template-columns:auto 1fr;gap:48px;align-items:start}
		.xl-about-lbl{font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:#A1A1AA;writing-mode:vertical-rl;transform:rotate(180deg);position:sticky;top:120px}
		.xl-about h2{font-size:clamp(20px,2.4vw,30px);font-weight:600;line-height:1.3;letter-spacing:-.4px;margin:0 0 28px;max-width:720px}
		.xl-about h2 .dim{color:#D4D4D8}
		/* WHY CHOOSE (2 колонки) */
		.xl-why{padding:56px 0}
		.xl-why-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start}
		.xl-why-l h2{font-size:clamp(38px,5vw,68px);font-weight:800;line-height:1.03;letter-spacing:-2.2px;margin:0}
		.xl-why-l h2 .dim{color:#D4D4D8}
		.xl-why-r .txt{font-size:14px;line-height:1.7;color:#71717A;margin:0 0 28px}
		.xl-why-tag{font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:#A1A1AA;margin-bottom:16px}
		/* FEATURE CARDS */
		.xl-cards{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:48px}
		.xl-card{background:#FAFAFA;border-radius:32px;padding:24px;min-height:340px;display:flex;flex-direction:column;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s,background .3s}
		.xl-card:hover{transform:translateY(-6px);background:#fff;box-shadow:0 28px 56px rgba(23,19,16,.11)}
		.xl-card .num{width:40px;height:40px;border-radius:50%;background:#fff;border:1px solid #E4E4E7;display:grid;place-items:center;font-size:12px;font-weight:700;color:#71717A;flex-shrink:0;transition:background .3s,border-color .3s,color .3s}
		.xl-card:hover .num{background:#F97316;border-color:#F97316;color:#fff}
		.xl-card h3{font-size:clamp(20px,2.2vw,26px);font-weight:700;line-height:1.14;letter-spacing:-.5px;color:#171310;margin:20px 0 0}
		.xl-card:first-child h3{margin:0 0 16px;margin-top:auto;order:3}
		.xl-card:first-child .x3{order:2;margin:auto 0}
		.xl-card:first-child .num{order:1}
		.xl-card .x3{margin-top:auto;width:100%;aspect-ratio:2/1;position:relative;flex-shrink:0}
		/* STATS */
		.xl-stats{padding:24px 0 64px}
		.xl-stats-h{font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:#A1A1AA;margin-bottom:28px}
		.xl-stats-row{display:grid;grid-template-columns:repeat(5,1fr);gap:0;border-top:1px solid #EDEDEF;border-bottom:1px solid #EDEDEF}
		.xl-stat{padding:28px 20px;border-right:1px solid #EDEDEF}
		.xl-stat:first-child{padding-left:0}
		.xl-stat:last-child{border-right:0}
		.xl-stat .num{font-size:clamp(32px,4vw,52px);font-weight:800;letter-spacing:-2px;line-height:1;color:#171310}
		.xl-stat .lbl{font-size:12px;color:#71717A;margin-top:10px;line-height:1.4}
		/* Фоновое изображение */
		.xl-band{position:relative;height:440px;overflow:hidden;border-radius:32px;margin-bottom:24px}
		.xl-band img{width:100%;height:100%;object-fit:cover}
		.xl-band .ov{position:absolute;inset:0;background:linear-gradient(120deg,rgba(23,19,16,.42),rgba(23,19,16,.08))}
		.xl-band .cap{position:absolute;bottom:36px;left:36px;right:36px;color:#fff}
		.xl-band .cap .who{font-size:11px;font-weight:700;letter-spacing:.2em;text-transform:uppercase;opacity:.85;margin-bottom:10px}
		.xl-band .cap .what{font-size:clamp(20px,2.6vw,34px);font-weight:800;letter-spacing:-1px;line-height:1.06}
		/* 3D фигуры для карточек */
		.f3{position:absolute;inset:0}
		.f3 .octa{position:absolute;top:20%;left:50%;transform:translate(-50%,0) rotate(45deg);width:110px;height:110px;background:radial-gradient(circle at 30% 30%,#fff,#e2e2e6);border-radius:16px;box-shadow:18px 24px 40px rgba(23,19,16,.12)}
		.f3 .gem{position:absolute;bottom:18%;right:18%;width:66px;height:66px;background:conic-gradient(from 30deg,#f6f6f7,#e6e6ea,#f6f6f7);transform:rotate(24deg);clip-path:polygon(50% 0%,100% 38%,82% 100%,18% 100%,0% 38%);box-shadow:10px 14px 24px rgba(23,19,16,.1)}
		@media(max-width:900px){
			.xl-hero-grid{grid-template-columns:1fr}
			.xl3{max-width:320px;margin:24px auto}
			.xl-auto{text-align:left;margin-left:0}
			.xl-about-grid{grid-template-columns:1fr}
			.xl-about-lbl{writing-mode:horizontal-tb;transform:none;position:static}
			.xl-why-grid{grid-template-columns:1fr;gap:24px}
			.xl-why-l{order:-1}
			.xl-cards{grid-template-columns:1fr}
			.xl-stats-row{grid-template-columns:repeat(2,1fr)}
			.xl-stat{border-right:0}
.xl-band{height:300px;border-radius:24px}
		}
  .fss-breadcrumbs { margin-bottom: 20px; }
  .fss-bc-list { display: flex; flex-wrap: wrap; gap: 6px; list-style: none; margin: 0; padding: 0; font-size: 13px; color: #6b7078; }
  .fss-bc-item { display: flex; align-items: center; }
  .fss-bc-item:not(:last-child)::after { content: "/"; margin-left: 6px; color: #b0b3b8; }
  .fss-bc-link { color: #6b7078; text-decoration: none; transition: color .2s; }
  .fss-bc-link:hover { color: #F97316; }
  .fss-bc-current { color: #17191d; font-weight: 600; }
</style>
</head>
<body>
	<?php include_once __DIR__ . '/../../components/header.php'; ?>
	<main>
	<?= seo_breadcrumbs_html() ?>
		<!-- HERO -->
		<section class="xl xl-hero">
			<div class="xl-eyebrow">Инновации на каждом этапе</div>
			<div class="xl-hero-grid">
				<div>
					<h1>Реализуем успешную стратегию строительства и инновационные решения для любых задач</h1>
					<a href="/#contact" class="xl-btn">Обсудить проект <i class="fa-solid fa-arrow-right"></i></a>
					<div class="xl-scroll">
						<span class="xl-scroll ar"><i class="fa-solid fa-arrow-down"></i></span>
						<span class="t">Смотрите дальше</span>
					</div>
					<div class="xl-auto">
						<div class="n1">Автоматизация производства</div>
						<div class="n2">Модули из сэндвич-панелей за считанные дни</div>
					</div>
				</div>
				<div class="xl3" aria-hidden="true">
					<div class="cube">
						<div class="fc a"></div>
						<div class="fc b"></div>
						<div class="fc c"></div>
					</div>
					<span class="sphere"></span>
					<span class="sq"></span>
					<span class="dot d1"></span>
					<span class="dot d2"></span>
				</div>
			</div>
		</section>

		<!-- MARQUEE -->
		<section class="xl-marq" aria-hidden="true">
			<div class="xl-marq-in">
				<span class="big">Инновации — на каждом этапе</span> 			<span class="big dim">Строим с 2020</span>
				<span class="big">Инновации — на каждом этапе</span> 			<span class="big dim">Строим с 2020</span>
			</div>
		</section>

		<!-- ABOUT -->
		<section class="xl xl-about">
			<div class="xl-about-grid">
				<div class="xl-about-lbl">О нас</div>
				<div>
					<h2>ФАВОРИТ-СТРОЙ-СНАБ — команда профессионалов на рынке с <?= $foundedMarket ?> года, <span class="dim">сфокусирована на интегрированных, надёжных и экономичных решениях, где 100% удовлетворённость клиента — привилегия.</span></h2>
					<a href="/uslugi" class="xl-btn">Наши услуги <i class="fa-solid fa-arrow-right"></i></a>
				</div>
			</div>
		</section>

		<!-- WHY CHOOSE -->
		<section class="xl xl-why">
			<div class="xl-why-grid">
				<div class="xl-why-r">
					<p class="txt">Мы ценим, что вы рассматриваете ФАВОРИТ-СТРОЙ-СНАБ как стратегического партнёра. Наша команда создаёт уникальные решения под ваши задачи и поддерживает на всех этапах реализации. Мы внедряем новые возможности, модернизируем подход и обеспечиваем эффективность ваших объектов.</p>
					<a href="/#contact" class="xl-btn">Обсудить проект <i class="fa-solid fa-arrow-right"></i></a>
				</div>
				<div class="xl-why-l">
					<div class="xl-why-tag">Ваш партнёр</div>
					<h2>Почему <span class="dim">выбирают</span><br>ФАВОРИТ-СТРОЙ-СНАБ?</h2>
				</div>
			</div>

			<div class="xl-cards">
				<div class="xl-card">
					<div class="num">01</div>
					<div class="x3 f3" aria-hidden="true"><span class="octa"></span><span class="gem"></span></div>
					<h3>Внутренний контроль качества</h3>
				</div>
				<div class="xl-card">
					<div class="num">02</div>
					<h3>Поддержка 24/7 и бесперебойная эксплуатация</h3>
					<div class="x3 f3" aria-hidden="true"><span class="octa"></span><span class="gem"></span></div>
				</div>
				<div class="xl-card">
					<div class="num">03</div>
					<h3>Сертифицированный опыт во многих технологиях</h3>
					<div class="x3 f3" aria-hidden="true"><span class="octa"></span><span class="gem"></span></div>
				</div>
			</div>
		</section>

		<!-- СТАТИСТИКА -->
		<section class="xl xl-stats">
			<div class="xl-stats-h">Компания в цифрах</div>
			<div class="xl-stats-row">
				<div class="xl-stat"><div class="num">6+</div><div class="lbl">лет на рынке</div></div>
				<div class="xl-stat"><div class="num">14</div><div class="lbl">дней до сдачи модуля</div></div>
				<div class="xl-stat"><div class="num">180+</div><div class="lbl">проектов сдано</div></div>
				<div class="xl-stat"><div class="num">50+</div><div class="lbl">лет гарантии</div></div>
				<div class="xl-stat"><div class="num">100%</div><div class="lbl">фиксированная цена</div></div>
			</div>
		</section>

		<!-- ФОНОВОЕ ИЗОБРАЖЕНИЕ -->
		<section class="xl">
			<div class="xl-band">
				<img src="/public/assets/image/banner.jpeg"<?= seo_img_attrs('/public/assets/image/banner.jpeg') ?> alt="Модульное строительство — ФСС" loading="lazy" onerror="this.style.display=.none." title="Модульное строительство — ФСС">
				<div class="ov"></div>
				<div class="cap">
					<div class="who">ФАВОРИТ-СТРОЙ-СНАБ</div>
					<div class="what">Строим быстровозводимые здания по всей России на рынке с <?= $foundedMarket ?> года</div>
				</div>
			</div>
		</section>

		<!-- РЕКВИЗИТЫ -->
		<section class="xl">
			<div class="xl-about">
				<div class="xl-about-grid">
					<div class="xl-about-lbl">Реквизиты</div>
					<div>
						<h2>Юридические реквизиты ООО «ФАВОРИТ-СТРОЙ-СНАБ»</h2>
						<p class="txt">Руководитель: <b><?= htmlspecialchars($ceo) ?></b></p>
						<p class="txt">ИНН <?= htmlspecialchars($inn) ?> / КПП <?= htmlspecialchars((string)seo_config('kpp')) ?> / ОГРН <?= htmlspecialchars($ogrn) ?></p>
						<p class="txt">Дата регистрации: <?= htmlspecialchars(date('d.m.Y', strtotime($regDate))) ?> г.</p>
						<p class="txt">Адрес: <?= htmlspecialchars(seo_config('address')['street']) ?>, <?= htmlspecialchars(seo_config('address')['locality']) ?>, <?= htmlspecialchars(seo_config('address')['postal']) ?></p>
						<p class="txt">Телефон: <a href="tel:<?= preg_replace('/\D/', '', seo_config('phone')) ?>"><?= htmlspecialchars(seo_config('phone_display')) ?></a></p>
						<p class="txt">Email: <a href="mailto:<?= htmlspecialchars(seo_config('email')) ?>"><?= htmlspecialchars(seo_config('email')) ?></a></p>
						<p class="txt" style="font-size:12px;color:#71717A;margin-top:16px">Данные подтверждены выпиской из ЕГРЮЛ / rusprofile.ru (регистрация с <?= $founded ?> года, выручка 2025 — 522 млн руб., участник госзакупок на 829 млн руб.).</p>
					</div>
				</div>
			</div>
		</section>
	</main>

	<?php include_once __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
