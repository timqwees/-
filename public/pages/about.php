<?php require_once __DIR__ . '/../../app/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap" media="print" onload="this.media='all'"><noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Inter:wght@400;500;600&family=Jura:wght@400;500;600;700&display=swap"></noscript>
	<style> body { font-family: 'Wix Madefor Display', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>

	<title>О компании — ФАВОРИТ-СТРОЙ-СНАБ на рынке с <?= htmlspecialchars((string)seo_config('founded_market')) ?> года</title>
<meta name="description" content="ФАВОРИТ-СТРОЙ-СНАБ на рынке с <?= htmlspecialchars((string)seo_config('founded_market')) ?> года (ООО — с <?= htmlspecialchars((string)seo_config('founded')) ?>): собственный завод БВЗ, 180+ сданных объектов, гарантия 50+ лет. Москва, МО и вся Россия. ☎ <?= htmlspecialchars((string)seo_config('phone_display')) ?>">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<meta name="robots" content="index, follow">

	<!-- Favicon unified -->
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="ФСС" />
	<link rel="manifest" href="/public/assets/image/logo/site.webmanifest" />

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<script defer src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
	<script defer src="/public/assets/distJS/main.js?v=20261018"></script>

	<!-- canonical -->
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>

	<?= seo_og_tags('О компании — ФАВОРИТ-СТРОЙ-СНАБ на рынке с ' . htmlspecialchars((string)seo_config('founded_market')) . ' года', 'С ' . htmlspecialchars((string)seo_config('founded_market')) . ' года строим быстровозводимые здания: собственный завод БВЗ, ' . seo_config('objects') . ' сданных объектов, гарантия ' . seo_config('guarantee_years') . ' лет. Москва, МО и вся Россия.', '/public/assets/image/og/og-about-1200x630.jpg') ?>

	<?php include_once __DIR__ . '/../components/sheoma.php'; ?>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
</head>

<body class="font-sans text-gray-800">
	<?php include_once __DIR__ . '/../components/header.php'; ?>
	<style>
	.bi-hero{background:#171310;border-radius:32px;margin:104px auto 0;max-width:1408px;overflow:hidden;position:relative;}
	.bi-hero-in{max-width:1280px;margin:0 auto;padding:clamp(48px,6vw,96px) 20px;display:grid;grid-template-columns:1.1fr .9fr;gap:40px;align-items:center;}
	.bi-eyebrow{display:inline-flex;align-items:center;gap:10px;color:#FDBA74;font-size:12px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;}
	.bi-eyebrow::before{content:"";width:26px;height:2px;background:#F97316;border-radius:2px;}
	.bi-h1{margin:16px 0 0;color:#fff;font-size:clamp(44px,6vw,88px);font-weight:800;letter-spacing:-.03em;line-height:1;}
	.bi-h1 .pill{display:inline-block;height:.82em;width:1.9em;border-radius:999px;overflow:hidden;vertical-align:-.08em;margin:0 .08em;}
	.bi-h1 .pill img{width:100%;height:100%;object-fit:cover;display:block;}
	.bi-h1 .acc{color:#F97316;}
	.bi-sub{margin:18px 0 0;color:#D6D3D1;font-size:16px;line-height:1.65;max-width:46ch;}
	.bi-cta{display:flex;flex-wrap:wrap;gap:12px;margin-top:28px;}
	.bi-btn{position:relative;display:inline-flex;align-items:center;gap:12px;background:#F97316;color:#171310;font-weight:800;font-size:15px;border-radius:999px;padding:16px 30px;text-decoration:none;overflow:hidden;}
	.bi-btn .clip{display:block;height:1.3em;overflow:hidden;}
	.bi-btn .roll{display:block;transition:transform .35s cubic-bezier(.16,1,.3,1);}
	.bi-btn:hover .roll{transform:translateY(-1.3em);}
	.bi-btn.ghost{background:transparent;color:#fff;border:1px solid rgba(255,255,255,.35);}
	.bi-visual{position:relative;}
	.bi-visual img.main{width:100%;height:clamp(320px,36vw,520px);object-fit:cover;border-radius:24px;display:block;}
	.bi-badge{position:absolute;left:20px;bottom:20px;background:rgba(23,19,16,.85);backdrop-filter:blur(8px);border-radius:18px;padding:16px 22px;color:#fff;}
	.bi-badge b{display:block;font-size:30px;font-weight:800;letter-spacing:-.02em;}
	.bi-badge b i{font-style:normal;color:#FDBA74;}
	.bi-badge span{font-size:13px;color:#D6D3D1;}
	.bi-stats{display:flex;gap:clamp(24px,4vw,56px);margin-top:34px;flex-wrap:wrap;}
	.bi-stats div b{display:block;color:#fff;font-size:clamp(26px,3vw,40px);font-weight:800;letter-spacing:-.02em;}
	.bi-stats div b i{font-style:normal;color:#F97316;}
	.bi-stats div span{font-size:12.5px;color:#A8A29E;}
	.bi-aud{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;}
  .fss-breadcrumbs { margin-bottom: 20px; }
  .fss-bc-list { display: flex; flex-wrap: wrap; gap: 6px; list-style: none; margin: 0; padding: 0; font-size: 13px; color: #6b7078; }
  .fss-bc-item { display: flex; align-items: center; }
  .fss-bc-item:not(:last-child)::after { content: "/"; margin-left: 6px; color: #b0b3b8; }
  .fss-bc-link { color: #6b7078; text-decoration: none; transition: color .2s; }
  .fss-bc-link:hover { color: #F97316; }
  .fss-bc-current { color: #17191d; font-weight: 600; }
	.bi-card{background:#fff;border:1px solid #F5E6D3;border-radius:24px;overflow:hidden;transition:transform .3s ease,box-shadow .3s ease;}
	.bi-card:hover{transform:translateY(-6px);box-shadow:0 24px 54px rgba(23,19,16,.12);}
	.bi-card img{width:100%;height:190px;object-fit:cover;display:block;}
	.bi-card div{padding:22px;}
	.bi-card h3{margin:0 0 8px;font-size:18px;font-weight:800;color:#171310;}
	.bi-card p{margin:0 0 12px;font-size:13.5px;color:#78716C;line-height:1.55;}
	.bi-card a{font-size:13px;font-weight:700;color:#C2410C;}
	@media(max-width:900px){.bi-hero-in{grid-template-columns:1fr;}.bi-aud{grid-template-columns:1fr 1fr;}}
	@media(max-width:560px){.bi-aud{grid-template-columns:1fr;}}
	@media(prefers-reduced-motion:reduce){.bi-btn .roll{transition:none;}}
	</style>
	<main>
	<?= seo_breadcrumbs_html() ?>
		<section class="bi-hero view">
			<div class="bi-hero-in">
				<div>
					<span class="bi-eyebrow">О компании</span>
					<h1 class="bi-h1">Строим <span class="pill"><img src="/public/assets/image/services/капстроение/10.JPG" alt="Объект ФСС" fetchpriority="high" decoding="async" onerror="this.style.display='none'"></span>на рынке с <?= htmlspecialchars((string)seo_config('founded_market')) ?> <span class="acc">года</span></h1>
					<div class="bi-cta">
						<a href="/#contact" class="bi-btn"><span class="clip"><span class="roll">Рассчитать смету<br>Рассчитать смету</span></span></a>
						<a href="/portfolio" class="bi-btn ghost"><span class="clip"><span class="roll">Наши объекты<br>Наши объекты</span></span></a>
					</div>
					<div class="bi-stats">
						<div><b>180<i>+</i></b><span>сданных объектов</span></div>
						<div><b>14 <i>лет</i></b><span>на рынке</span></div>
						<div><b>50<i>+</i></b><span>лет гарантии</span></div>
					</div>
				</div>
				<div class="bi-visual">
					<img class="main" src="/public/assets/image/services/капстроение/1.JPG"<?= seo_img_attrs('/public/assets/image/services/капстроение/1.JPG') ?> alt="Быстровозводимое здание ФСС" fetchpriority="high" decoding="async" onerror="this.style.display=.none." title="Быстровозводимое здание ФСС">
					<div class="bi-badge"><b>14 <i>дней</i></b><span>от старта до сдачи</span></div>
				</div>
			</div>
		</section>
		<div class="stats-band view"><div class="stats-grid">
			<div class="stat-cell"><div class="stat-num">14 <span class="accent">лет</span></div><div class="stat-lbl">строим с <?= htmlspecialchars((string)seo_config('founded_market')) ?> года</div></div>
			<div class="stat-cell"><div class="stat-num"><?= htmlspecialchars((string)seo_config('objects')) ?></div><div class="stat-lbl">сданных объектов</div></div>
			<div class="stat-cell"><div class="stat-num"><?= htmlspecialchars((string)seo_config('guarantee_years')) ?> <span class="accent">лет</span></div><div class="stat-lbl">гарантии по договору</div></div>
			<div class="stat-cell"><div class="stat-num"><?= htmlspecialchars((string)seo_config('deadline_days')) ?> <span class="accent">дней</span></div><div class="stat-lbl">от старта до сдачи</div></div>
		</div></div>
		<section class="page-section view">
			<div class="section-heading"><h2>Для кого строим</h2><span class="heading-divider" aria-hidden="true"></span><p>Четыре сегмента — один стандарт качества</p></div>
			<div class="bi-aud" style="margin-top:22px">
				<article class="bi-card"><img src="/public/assets/image/services/капстроение/5.JPG" alt="Производство ФСС" loading="lazy" decoding="async" onerror="this.style.display='none'"><div><h3>Производство</h3><p>Цеха, склады и ангары для бизнеса — под ключ за 14 дней.</p><a href="/services/proizvodim_modul">Подробнее →</a></div></article>
				<article class="bi-card"><img src="/public/assets/image/services/Грузоперевозки/2.jpg" alt="Логистика ФСС" loading="lazy" decoding="async" onerror="this.style.display='none'"><div><h3>Логистика</h3><p>Склады и логистика с доставкой и спецтехникой.</p><a href="/services/sclad">Подробнее →</a></div></article>
				<article class="bi-card"><img src="/public/assets/image/services/АБК/04d8caee-f083-42f8-8be2-64e7e461cace.JPG" alt="АБК ФСС" loading="lazy" decoding="async" onerror="this.style.display='none'"><div><h3>Госзаказ и юрлица</h3><p>Договор, НДС, допуск СРО и закрывающие документы.</p><a href="/services/kapstroenie">Подробнее →</a></div></article>
				<article class="bi-card"><img src="/public/assets/image/services/Генераторные/017C294C-9AF3-4638-ADC2-476CD4C3A7ED.JPG" alt="Энергетика ФСС" loading="lazy" decoding="async" onerror="this.style.display='none'"><div><h3>Энергетика</h3><p>ДГУ и компрессоры: подбор, поставка, монтаж и сервис.</p><a href="/services/generatornae">Подробнее →</a></div></article>
			</div>
		</section>
		<section class="page-section view">
			<div class="section-heading"><h2>Кто мы</h2><span class="heading-divider" aria-hidden="true"></span><p>Завод, инженеры и своя техника — без посредников</p></div>
			<div class="card-prem" style="padding:clamp(24px,4vw,44px)">
				<p style="margin:0;font-size:16px;line-height:1.75;color:#44403C;max-width:70ch">Начинали в <?= htmlspecialchars((string)seo_config('founded_market')) ?> году с металлоконструкций — сегодня это производство блок-модулей и быстровозводимых зданий, капитальное строительство и ремонт, грузоперевозки и аренда спецтехники. Работаем с бизнесом и госзаказом по договору: фиксируем цену и сроки, ведём технадзор и сдаём объекты с пакетом документов. База — Москва и Московская область, объекты — по всей России.</p>
				<div style="margin-top:18px;display:flex;flex-wrap:wrap;gap:10px">
					<a class="btn-sun" href="/uslugi">Все услуги</a>
					<a class="btn-outline" href="/portfolio">Смотреть объекты</a>
				</div>
			</div>
		</section>
		<section class="security-section view">
			<h2 class="security-title">Почему выбирают нас?</h2>
			<div class="security-grid">
				<article class="security-card security-main">
					<h3>Полный контроль 24/7</h3>
					<p>Инженер на площадке, фотоотчёт каждую неделю и технадзор на каждом этапе — вы в курсе прогресса без звонков.</p>
					<div class="security-shield" aria-hidden="true"><i class="fa-solid fa-shield-halved"></i></div>
				</article>
				<article class="security-card security-compliance">
					<h3>Фиксируем цену и сроки</h3>
					<p>Смета за 15 минут. Закрепляем стоимость и сроки в договоре — цена не растёт по ходу стройки.</p>
					<div class="document-stack" aria-hidden="true">
						<div class="document document-back"><div class="document-line"></div><div class="document-line"></div><div class="document-line"></div></div>
						<div class="document"><div class="document-line"></div><div class="document-line"></div><div class="document-check">✓</div><div class="document-check">✓</div><div class="document-check">✓</div></div>
					</div>
				</article>
				<article class="security-card security-storage">
					<h3>Сроки<br>от 14 дней</h3>
					<p>Сдаём объекты за 2–12 недель вместо 3–6 месяцев классической стройки.</p>
				</article>
				<article class="security-card security-uptime">
					<div class="gauge" aria-hidden="true"><div class="gauge-ring"></div><div class="gauge-needle"></div></div>
					<div class="uptime-number">180+</div>
					<p>сданных объектов<br>с <?= htmlspecialchars((string)seo_config('founded_market')) ?> года</p>
				</article>
				<article class="security-card security-versions">
					<span class="security-ico" aria-hidden="true"><i class="fa-solid fa-truck-fast"></i></span>
					<h3>Своя техника<br>и доставка</h3>
				</article>
				<article class="security-card security-requests">
					<h3>Собственный завод БВЗ —<br>модули без посредников</h3>
					<p>Производим сэндвич-панели и блок-контейнеры сами — контроль качества и цена ниже рынка.</p>
				</article>
				<article class="security-card security-support">
					<h3>Нужна<br>смета?</h3>
					<a href="/#contact" style="color:#171310;font-weight:700;font-size:15px;text-decoration:underline;text-underline-offset:4px;text-decoration-color:#F97316">Получить за 15 минут</a>
				</article>
			</div>
		</section>
		<section class="page-section view" id="process">
			<div class="section-heading"><h2>Как мы работаем</h2><span class="heading-divider" aria-hidden="true"></span><p>От заявки до сдачи — 5 шагов под ключ</p></div>
			<div class="steps3" style="margin-top:22px">
				<article class="step3"><h3>Заявка и смета</h3><p>Звонок или заявка — инженер считает фиксированную смету за 15 минут.</p><span class="step3-n">01</span></article>
				<article class="step3"><h3>Проект</h3><p>Замер, чертежи, расчёт нагрузок и согласование с вами.</p><span class="step3-n">02</span></article>
				<article class="step3"><h3>Договор</h3><p>Фиксируем цену, сроки и гарантию. Работаем с НДС, даём закрывающие документы.</p><span class="step3-n">03</span></article>
				<article class="step3"><h3>Производство</h3><p>Изготавливаем модули и конструкции на собственном заводе БВЗ.</p><span class="step3-n">04</span></article>
				<article class="step3"><h3>Монтаж и сдача</h3><p>Доставляем, монтируем, подключаем инженерию и сдаём под ключ.</p><span class="step3-n">05</span></article>
			</div>
		</section>
		<section class="page-section view" id="faq">
			<div class="section-heading"><h2>Частые вопросы</h2><span class="heading-divider" aria-hidden="true"></span><p>О компании и условиях работы</p></div>
			<div class="grid gap-3" style="margin-top:22px;max-width:860px">
				<details class="faq-ex" open><summary><span class="faq-mark" aria-hidden="true"></span><span>Сколько лет вы на рынке?</span></summary><div class="faq-content"><p class="faq-a">Работаем с <?= htmlspecialchars((string)seo_config('founded_market')) ?> года — 14 лет строим быстровозводимые здания, делаем капремонт и возим грузы.</p></div></details>
				<details class="faq-ex"><summary><span class="faq-mark" aria-hidden="true"></span><span>Где вы работаете?</span></summary><div class="faq-content"><p class="faq-a">База — Москва и Московская область, объекты сдаём по всей России.</p></div></details>
				<details class="faq-ex"><summary><span class="faq-mark" aria-hidden="true"></span><span>Даёте ли гарантию и договор?</span></summary><div class="faq-content"><p class="faq-a">Да: договор с фиксированной ценой и сроками, гарантия на конструкции — 50+ лет, на работы — по договору.</p></div></details>
				<details class="faq-ex"><summary><span class="faq-mark" aria-hidden="true"></span><span>Работаете с юрлицами и НДС?</span></summary><div class="faq-content"><p class="faq-a">Да, работаем с юрлицами и госзаказом: договор, счёт, закрывающие документы, допуск СРО.</p></div></details>
				<details class="faq-ex"><summary><span class="faq-mark" aria-hidden="true"></span><span>Как быстро посчитаете мой объект?</span></summary><div class="faq-content"><p class="faq-a">Смету даём за 15 минут по телефону <?= htmlspecialchars((string)seo_config('phone_display')) ?> или из заявки — инженер перезванивает за 7 минут.</p></div></details>
			</div>
		</section>
		<section class="page-section view">
			<div class="section-heading"><h2>Реквизиты</h2><span class="heading-divider" aria-hidden="true"></span><p>Как нас найти и связаться</p></div>
			<div class="card-prem" style="padding:clamp(24px,4vw,40px);display:grid;gap:14px;font-size:15px;color:#44403C">
				<div><i class="fa-solid fa-location-dot" style="color:#EA580C;margin-right:10px"></i><?= htmlspecialchars((string)seo_config('address')['postal'] . ', ' . seo_config('address')['locality'] . ', ' . seo_config('address')['street']) ?></div>
				<div><i class="fa-solid fa-phone" style="color:#EA580C;margin-right:10px"></i><a href="tel:<?= htmlspecialchars(preg_replace('/\D/', '', (string)seo_config('phone'))) ?>" style="color:#171310;font-weight:700"><?= htmlspecialchars((string)seo_config('phone_display')) ?></a></div>
				<div><i class="fa-solid fa-envelope" style="color:#EA580C;margin-right:10px"></i><?= htmlspecialchars((string)seo_config('email')) ?></div>
			</div>
		</section>
		<section class="cta-band view">
			<img src="/public/assets/image/Components/banner/hero.jpg" alt="ФАВОРИТ-СТРОЙ-СНАБ — строим то, что ведёт вас вперёд" loading="lazy" decoding="async" onerror="this.style.display='none'">
			<div class="cta-in">
				<span class="cta-eyebrow"><i></i>Ваш проект — наше дело</span>
				<h2>Строим то,<br>что ведёт вас вперёд</h2>
				<p>Оставьте заявку — рассчитаем смету за 15 минут и зафиксируем цену в договоре.</p>
				<a href="/#contact" class="cta-pill">Построить с нами <i class="fa-solid fa-arrow-right"></i></a>
			</div>
		</section>
	</main>
	<?php include_once __DIR__ . '/../components/footer.php'; ?>
</body>

</html>
