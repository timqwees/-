<?php
/**
 * Детальная страница объекта: /portfolio/{slug}
 * Данные — app/Data/projects.php (только те объекты, что уже заявлены на сайте).
 * Оформление — по дизайн-системе главной страницы (ProMaintenance).
 */
require_once __DIR__ . '/../../../app/bootstrap.php';

$slug = (string)($GLOBALS['fss_project_slug'] ?? '');
$all = require dirname(__DIR__, 3) . '/app/Data/projects.php';
$p = $all[$slug] ?? null;
if (!$p) {
	\App\Models\Router\Routes::error_404('/portfolio/' . $slug);
	return;
}
$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$base = rtrim((string)seo_config('base_url'), '/');
$phone = (string)seo_config('phone_display');
$phoneHref = preg_replace('/\D/', '', (string)seo_config('phone'));
$svc = seo_service((string)$p['service']);
$title = (string)$p['title'];
// Title вкладки — до 60 символов: название объекта + бренд, без обрезки в сниппете
$titleSuffix = ' — ФАВОРИТ-СТРОЙ-СНАБ';
$seoTitle = seo_clip($title, 60 - mb_strlen($titleSuffix)) . $titleSuffix;
$photos = array_values((array)$p['photos']);
// Публичные URL фото: файлы могут лежать в services/ или в корне image/
$photoUrls = array_map(static fn(string $f): string => \App\Models\Image::publicUrl($f), $photos);
$photoAlts = array_values((array)($p['photo_alts'] ?? []));
$features = array_values((array)($p['features'] ?? []));
$faq = array_values((array)($p['faq'] ?? []));

// Заголовок объекта — в две строки (как крупные заголовки на главной)
$h1 = trim((string)($p['h1'] ?? $p['title']));
$h1Words = preg_split('/\s+/u', $h1) ?: [$h1];
$h1Half = (int)ceil(count($h1Words) / 2);
$h1a = implode(' ', array_slice($h1Words, 0, $h1Half));
$h1b = implode(' ', array_slice($h1Words, $h1Half));
// Фотографии для фона героя (до 5) и для галереи
$heroPhotos = array_slice($photoUrls, 0, 5);
$galleryPhotos = $photoUrls;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $h($seoTitle) ?></title>
	<meta name="description" content="<?= $h(seo_clip((string)$p['lead'], 120) . ' Гарантия 50+ лет, смета за 15 минут.') ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#876b55">
	<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
	<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
	<link rel="icon" type="image/png" href="/public/assets/image/logo/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<link rel="manifest" href="/manifest.json" />
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>
	<?= seo_og_tags($title . ' — ФАВОРИТ-СТРОЙ-СНАБ', (string)$p['lead'], $photoUrls ? $photoUrls[0] : '') ?>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=20261018" as="style">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=20261018">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=20261018">
	<!-- Дизайн-система главной страницы (ProMaintenance) -->
	<link rel="stylesheet" href="/public/assets/distCSS/promaintenance.css?v=20261023">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<style>
	/* ============================================================
	   ГЕРОЙ ОБЪЕКТА — по образцу hero главной страницы
	   ============================================================ */
	.prj-hero{isolation:isolate;position:relative;overflow:hidden;color:#fff;background:#363636;min-height:min(86svh,820px);display:flex;flex-direction:column;justify-content:flex-end;}
	.prj-hero__visual{position:absolute;inset:0;}
	.prj-hero__visual img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:58% 50%;opacity:0;transition:opacity .8s cubic-bezier(.2,.8,.2,1);}
	.prj-hero__visual img.is-active{opacity:1;}
	.prj-hero__shade{position:absolute;inset:0;pointer-events:none;background:linear-gradient(90deg,#0f0e0dad,#0f0e0d14 85%),linear-gradient(#0f0e0d33,#0000 45%,#0f0e0dcc);}
	.prj-hero__inner{position:relative;z-index:2;padding-top:clamp(150px,20vh,240px);padding-bottom:36px;}
	.prj-hero__eyebrow{color:#ffffffc7;display:flex;align-items:center;gap:12px;font-size:10px;line-height:1.5;}
	.prj-hero__title{letter-spacing:-.066em;text-wrap:initial;margin:30px 0 0;font-family:'DM Sans Variable',Arial,sans-serif;font-size:clamp(40px,5.6vw,86px);font-weight:450;line-height:1;}
	.prj-hero__title>span{display:block;}
	.prj-hero__lead{max-width:52ch;margin:26px 0 0;color:#fffc;font-size:15px;line-height:1.7;}
	.prj-hero__actions{display:flex;flex-wrap:wrap;align-items:center;gap:20px 34px;margin-top:32px;}
	.prj-hero .button{background:#fff;color:var(--ink);}
	.prj-hero .button:hover{background:var(--paper);}
	.prj-hero .text-link{color:#fff;border-bottom-color:#ffffff80;text-decoration:none;}
	.prj-hero .text-link:hover{border-bottom-color:#fff;}
	.prj-hero .fss-breadcrumbs{margin-bottom:26px;}
	.prj-hero .fss-bc-list{display:flex;flex-wrap:wrap;gap:8px;list-style:none;margin:0;padding:0;font-size:10px;letter-spacing:.1em;text-transform:uppercase;}
	.prj-hero .fss-bc-item{display:flex;align-items:center;color:#ffffff80;}
	.prj-hero .fss-bc-item:not(:last-child):after{content:"/";margin-left:8px;color:#ffffff47;}
	.prj-hero .fss-bc-link{color:#ffffff80;text-decoration:none;transition:color .2s;}
	.prj-hero .fss-bc-link:hover{color:#fff;}
	.prj-hero .fss-bc-current{color:#fff;}
	.prj-hero__foot{position:relative;z-index:2;display:grid;grid-template-columns:1.1fr 1fr auto;align-items:end;gap:40px;padding-bottom:30px;}
	.prj-hero__card{backdrop-filter:blur(22px);border-radius:8px;background:#16161794;border:1px solid #ffffff4d;display:flex;flex-direction:column;gap:9px;padding:24px 75px 24px 24px;position:relative;}
	.prj-hero__card .eyebrow{opacity:.7;font-size:8px;display:flex;align-items:center;gap:2px;}
	.prj-hero__card strong{letter-spacing:-.02em;font-size:18px;font-weight:500;}
	.prj-hero__card>span:last-of-type{opacity:.7;font-size:10px;}
	.prj-hero__arrow{position:absolute;top:50%;right:24px;transform:translateY(-50%);}
	.prj-hero__controls{display:flex;flex-direction:column;gap:10px;}
	.cinema-pagination{align-items:center;gap:16px;display:flex;}
	.cinema-pagination button{opacity:.5;transition:opacity .3s;align-items:center;gap:12px;min-height:44px;font-size:10px;display:flex;background:none;border:none;color:inherit;cursor:pointer;padding:0;}
	.cinema-pagination button span{width:20px;height:1px;transition:width .6s cubic-bezier(.2,.8,.2,1);background:#fff;display:block;}
	.cinema-pagination button[aria-pressed=true]{opacity:1;}
	.cinema-pagination button[aria-pressed=true] span{width:65px;}
	.prj-hero__controls>.eyebrow{opacity:.65;font-size:8px;}
	.prj-hero__cta{border-radius:8px;background:#f0efed;color:#1b1c1d;align-items:center;gap:45px;padding:22px 26px;font-size:12px;transition:background .3s;display:flex;margin-bottom:15px;}
	.prj-hero__cta:hover{background:#fff;}
	.prj-hero__cta svg{transition:transform .4s cubic-bezier(.2,.8,.2,1);flex-shrink:0;}
	.prj-hero__cta:hover svg{transform:translate(2px,-2px);}

	/* ============================================================
	   ПЛОЩАДКА ОБЪЕКТА — показатели
	   ============================================================ */
	.prj-facts{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));border-top:1px solid var(--line);padding-top:28px;padding-bottom:clamp(48px,6vw,90px);}
	.prj-facts>div{border-right:1px solid var(--line);padding-inline:38px;}
	.prj-facts>div:first-child{padding-left:0;}
	.prj-facts>div:last-child{border-right:0;padding-right:0;}
	.prj-facts dt{color:var(--muted);font-size:10px;letter-spacing:.12em;text-transform:uppercase;}
	.prj-facts dd{margin:14px 0 0;font-size:clamp(16px,1.4vw,21px);letter-spacing:-.03em;line-height:1.3;}

	/* ============================================================
	   ЗАДАЧА / РЕШЕНИЕ / РЕЗУЛЬТАТ
	   ============================================================ */
	.prj-section{padding-block:clamp(64px,7vw,120px);border-top:1px solid var(--line);}
	.prj-section .work-heading h2{font-size:clamp(34px,4.4vw,68px);font-weight:450;}
	.prj-section .work-heading p{color:var(--muted);}
	.prj-steps{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:clamp(28px,3vw,54px);}
	.prj-step{border-top:1px solid var(--ink);padding-top:24px;}
	.prj-step .eyebrow{color:var(--muted);}
	.prj-step h3{margin:16px 0 0;font-size:clamp(20px,1.8vw,26px);letter-spacing:-.04em;font-weight:500;line-height:1.15;}
	.prj-step p{margin:12px 0 0;font-size:14.5px;line-height:1.75;color:var(--muted);}
	.prj-features{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:0 clamp(24px,3vw,48px);margin-top:clamp(44px,5vw,76px);border-top:1px solid var(--line);}
	.prj-features li{list-style:none;display:flex;align-items:flex-start;gap:14px;padding:18px 0;border-bottom:1px solid var(--line);font-size:14px;line-height:1.5;}
	.prj-features li::before{content:"";flex:0 0 auto;width:5px;height:5px;border-radius:50%;background:var(--accent);margin-top:8px;}

	/* ============================================================
	   ГАЛЕРЕЯ
	   ============================================================ */
	.prj-gallery{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:clamp(22px,2.6vw,44px);}
	.prj-gallery figure{margin:0;min-width:0;}
	.prj-gallery figure:first-child{grid-column:1/-1;}
	.prj-gallery img{display:block;width:100%;aspect-ratio:4/3;object-fit:cover;background:#f4f1ec;transition:transform .6s cubic-bezier(.2,.8,.2,1);}
	.prj-gallery figure:first-child img{aspect-ratio:21/9;}
	.prj-gallery figure:hover img{transform:scale(1.03);}
	.prj-gallery figcaption{margin-top:12px;color:var(--muted);font-size:9px;letter-spacing:.07em;text-transform:uppercase;}

	/* ============================================================
	   CTA — тёмный блок в конце
	   ============================================================ */
	.prj-cta{background:#1c1d1f;color:var(--paper);}
	.prj-cta__inner{display:grid;grid-template-columns:1.25fr auto;align-items:end;gap:clamp(28px,4vw,60px);padding-block:clamp(64px,7vw,120px);}
	.prj-cta h2{margin:18px 0 0;font-size:clamp(34px,4.4vw,68px);font-weight:450;letter-spacing:-.055em;line-height:1.03;}
	.prj-cta p{margin:16px 0 0;max-width:52ch;color:rgba(240,239,237,.62);font-size:14px;line-height:1.7;}
	.prj-cta__actions{display:flex;flex-direction:column;align-items:flex-start;gap:18px;}
	.prj-cta .button{background:#fff;color:var(--ink);}
	.prj-cta .button:hover{background:var(--paper);}
	.prj-cta .text-link{color:#fff;border-bottom-color:#ffffff80;text-decoration:none;}
	.prj-cta .text-link:hover{border-bottom-color:#fff;}

	/* ============================================================
	   АДАПТИВ
	   ============================================================ */
	@media(max-width:1100px){
		.prj-hero__foot{grid-template-columns:1fr auto;}
		.prj-hero__controls{grid-column:2;grid-row:1;}
		.prj-hero__cta{display:none;}
	}
	@media(max-width:900px){
		.prj-facts{grid-template-columns:repeat(2,minmax(0,1fr));gap:26px 0;}
		.prj-facts>div{border-right:0;padding-inline:0;}
		.prj-facts>div:nth-child(odd){padding-right:24px;}
		.prj-facts>div:nth-child(even){padding-left:24px;border-left:1px solid var(--line);}
		.prj-facts>div:nth-child(n+3){padding-top:26px;border-top:1px solid var(--line);}
		.prj-steps{grid-template-columns:1fr;}
		.prj-gallery{grid-template-columns:1fr;}
		.prj-gallery figure:first-child{grid-column:auto;}
		.prj-gallery figure:first-child img{aspect-ratio:4/3;}
		.prj-cta__inner{grid-template-columns:1fr;align-items:start;}
	}
	@media(max-width:640px){
		.prj-hero{min-height:min(92svh,760px);}
		.prj-hero__title{font-size:clamp(32px,8.4vw,48px);}
		/* На мобильных заголовок не режем принудительно — текст идёт естественным потоком */
		.prj-hero__title>span{display:inline;}
		.prj-hero__title>span+span::before{content:" ";}
		/* Плотнее затемняем фон под текстом */
		.prj-hero__shade{background:linear-gradient(90deg,#0f0e0dd6 0%,#0f0e0da8 58%,#0f0e0d70 100%),linear-gradient(180deg,#0f0e0d80 0%,#0f0e0d00 28%,#0f0e0db8 100%);}
		.prj-hero__foot{grid-template-columns:1fr;gap:24px;}
		.prj-hero__controls{grid-column:1;grid-row:auto;}
		.prj-hero__card{padding:20px 60px 20px 20px;}
		.prj-facts{grid-template-columns:1fr;}
		.prj-facts>div:nth-child(odd),.prj-facts>div:nth-child(even){padding-inline:0;border-left:0;}
		.prj-facts>div:nth-child(n+2){padding-top:22px;border-top:1px solid var(--line);}
	}
	</style>
</head>
<body class="font-sans text-gray-800" data-page="inner">
	<?php
	// Шапка прозрачная в начале: под ней полноэкранный hero объекта (как на главной).
	$GLOBALS['fss_nav_mode'] = 'transparent';
	include_once __DIR__ . '/../../components/header.php';
	?>
	<main>

		<!-- ГЕРОЙ ОБЪЕКТА -->
		<section class="prj-hero" aria-labelledby="prj-title" data-cinema data-nav-hero>
			<div class="prj-hero__visual" data-hero-frame>
				<?php foreach ($heroPhotos as $i => $src): ?>
				<img src="<?= $h($src) ?>"<?= seo_img_attrs($src) ?> alt="<?= $h((string)($photoAlts[$i] ?? $p['h1'])) ?>" title="<?= $h((string)$p['h1']) ?>"<?= $i === 0 ? ' fetchpriority="high"' : ' loading="lazy"' ?> decoding="async" data-cinema-image="<?= $i ?>" class="<?= $i === 0 ? 'is-active' : '' ?>" onerror="this.style.display='none'">
				<?php endforeach; ?>
			</div>
			<div class="prj-hero__shade" aria-hidden="true"></div>

			<div class="prj-hero__inner section-shell">
				<?= seo_breadcrumbs_html() ?>
				<p class="eyebrow prj-hero__eyebrow">
					<span class="live-dot" aria-hidden="true"></span>
					Объект ФСС · <?= $h((string)$p['zone']) ?>
				</p>
				<h1 id="prj-title" class="prj-hero__title">
					<span><?= $h($h1a) ?></span>
					<span><?= $h($h1b) ?><span class="accent-dot">.</span></span>
				</h1>
				<p class="prj-hero__lead"><?= $h((string)$p['lead']) ?></p>
				<div class="prj-hero__actions">
					<a class="button" href="#prj-cta" data-goal="project_estimate" title="Рассчитать стоимость">Рассчитать стоимость
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</a>
					<a class="text-link" href="tel:<?= $h($phoneHref) ?>" title="Позвонить: <?= $h($phone) ?>"><?= $h($phone) ?></a>
				</div>
			</div>

			<div class="prj-hero__foot section-shell">
				<a href="#prj-gallery" class="prj-hero__card" title="Смотреть фотографии объекта">
					<span class="eyebrow">Фото объекта / <span data-cinema-number>01</span></span>
					<strong data-cinema-name><?= $h($title) ?></strong>
					<span data-cinema-caption><?= $h((string)$p['region']) ?></span>
					<span class="prj-hero__arrow" aria-hidden="true">
						<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</span>
				</a>
				<?php if (count($heroPhotos) > 1): ?>
				<div class="prj-hero__controls">
					<div class="cinema-pagination" role="group" aria-label="Фотографии объекта">
						<?php foreach ($heroPhotos as $i => $src): ?>
						<button type="button" data-cinema-select="<?= $i ?>" data-name="<?= $h($title) ?>" data-caption="<?= $h((string)$p['region']) ?>" aria-label="Показать фото <?= $i + 1 ?> из <?= count($heroPhotos) ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?><span></span></button>
						<?php endforeach; ?>
					</div>
					<p class="eyebrow" data-cinema-status aria-live="polite">01 / <?= str_pad((string)count($heroPhotos), 2, '0', STR_PAD_LEFT) ?> — <?= $h($title) ?></p>
				</div>
				<?php endif; ?>
				<a href="/portfolio" class="prj-hero__cta" data-goal="project_all">
					Все объекты
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
						<path d="M7 7h10v10"></path>
						<path d="M7 17 17 7"></path>
					</svg>
				</a>
			</div>
		</section>

		<!-- ПОКАЗАТЕЛИ ОБЪЕКТА -->
		<dl class="prj-facts section-shell">
			<div>
				<dt>Тип объекта</dt>
				<dd><?= $h((string)$p['type']) ?></dd>
			</div>
			<div>
				<dt>Регион</dt>
				<dd><?= $h((string)$p['region']) ?></dd>
			</div>
			<div>
				<dt>Направление</dt>
				<dd><?= $h((string)($svc['title'] ?? '')) ?></dd>
			</div>
			<div>
				<dt>Гарантия</dt>
				<dd><?= $h((string)seo_config('guarantee_years')) ?> лет на несущие конструкции</dd>
			</div>
		</dl>

		<!-- ЗАДАЧА / РЕШЕНИЕ / РЕЗУЛЬТАТ -->
		<section class="prj-section section-shell" id="prj-task" aria-labelledby="prj-task-title">
			<div class="section-topline">
				<p class="eyebrow">01 / Задача и решение</p>
				<span class="eyebrow"><?= $h((string)$p['region']) ?></span>
			</div>
			<div class="work-heading">
				<h2 id="prj-task-title">Как строился<br>этот объект.</h2>
				<p>Разбор кейса: с чего начали,<br>что сделали и что получили.</p>
			</div>
			<div class="prj-steps">
				<article class="prj-step">
					<span class="eyebrow">01</span>
					<h3>Задача</h3>
					<p><?= $h((string)$p['task']) ?></p>
				</article>
				<article class="prj-step">
					<span class="eyebrow">02</span>
					<h3>Решение</h3>
					<p><?= $h((string)$p['solution']) ?></p>
				</article>
				<article class="prj-step">
					<span class="eyebrow">03</span>
					<h3>Результат</h3>
					<p><?= $h((string)$p['result']) ?></p>
				</article>
			</div>
			<?php if ($features): ?>
			<ul class="prj-features">
				<?php foreach ($features as $ft): ?>
				<li><?= $h((string)$ft) ?></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</section>

		<!-- ФОТОГРАФИИ ОБЪЕКТА -->
		<section class="prj-section section-shell" id="prj-gallery" aria-labelledby="prj-gallery-title">
			<div class="section-topline">
				<p class="eyebrow">02 / Фотографии объекта</p>
				<span class="eyebrow"><?= count($galleryPhotos) ?> кадра с объекта</span>
			</div>
			<div class="work-heading">
				<h2 id="prj-gallery-title">Объект<br>в деталях.</h2>
				<p>Съёмка с площадки: конструкция,<br>инженерия и сдача.</p>
			</div>
			<div class="prj-gallery site-gallery-grid">
				<?php foreach ($galleryPhotos as $i => $src): ?>
				<figure>
					<img src="<?= $h($src) ?>"<?= seo_img_attrs($src) ?> alt="<?= $h((string)($photoAlts[$i] ?? $p['h1'])) ?>" title="<?= $h((string)$p['h1']) ?>" loading="lazy" decoding="async" onerror="this.closest('figure').style.display='none'">
					<figcaption><?= $h((string)$p['title']) ?> · <?= $i + 1 ?></figcaption>
				</figure>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- ЧАСТЫЕ ВОПРОСЫ -->
		<?php if ($faq): ?>
		<section class="prj-section section-shell" id="prj-faq" aria-labelledby="prj-faq-title">
			<div class="section-topline">
				<p class="eyebrow">03 / Вопросы</p>
				<span class="eyebrow">Ответы по объекту</span>
			</div>
			<div class="work-heading">
				<h2 id="prj-faq-title">Частые<br>вопросы.</h2>
				<p>Коротко о том, что спрашивают<br>по этому объекту.</p>
			</div>
			<div class="faq-grid">
				<?php foreach ($faq as $fi => $qa): ?>
				<details class="faq-ex"<?= $fi === 0 ? ' open' : '' ?>>
					<summary><span><?= $h((string)$qa[0]) ?></span><span class="faq-mark" aria-hidden="true"></span></summary>
					<div class="faq-content">
						<p class="faq-a"><?= $h((string)$qa[1]) ?></p>
					</div>
				</details>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<!-- CTA -->
		<section class="prj-cta" id="prj-cta">
			<div class="prj-cta__inner section-shell">
				<div>
					<p class="eyebrow">Следующий шаг</p>
					<h2>Нужен такой<br>же объект?</h2>
					<p>Рассчитаем смету за <?= $h((string)seo_config('price_response_minutes')) ?> минут и зафиксируем цену в договоре. Работаем по Москве, области и всей России.</p>
				</div>
				<div class="prj-cta__actions">
					<a class="button" href="/#contact" data-goal="project_enquiry" title="Получить смету">Получить смету
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
							<path d="M7 7h10v10"></path>
							<path d="M7 17 17 7"></path>
						</svg>
					</a>
					<?php if (!empty($p['service'])): ?>
					<a class="text-link" href="/services/<?= $h((string)$p['service']) ?>" title="Услуга: <?= $h((string)($svc['title'] ?? '')) ?>">Услуга: <?= $h((string)($svc['title'] ?? '')) ?></a>
					<?php endif; ?>
					<a class="text-link" href="/portfolio" title="Все объекты портфолио">Все объекты портфолио</a>
				</div>
			</div>
		</section>

	</main>
	<?php include_once __DIR__ . '/../../components/footer.php'; ?>
	<?php
	// Разметка объекта: CreativeWork + связь с организацией и услугой
	$nodes = [];
	$nodes[] = [
		'@type' => 'CreativeWork',
		'@id' => seo_canonical() . '#project',
		'name' => (string)$p['title'],
		'headline' => (string)$p['h1'],
		'description' => (string)$p['lead'],
		'url' => seo_canonical(),
		'creator' => ['@id' => $base . '#organization'],
		'about' => !empty($p['service']) ? ['@id' => $base . '/services/' . $p['service'] . '#service'] : null,
		'contentLocation' => ['@type' => 'Place', 'name' => (string)$p['region']],
		'image' => array_map(static fn(string $u): string => $base . $u, $photoUrls),
		'keywords' => implode(', ', (array)$p['features']),
	];
	if (!empty($p['faq'])) {
		$nodes[] = [
			'@type' => 'FAQPage',
			'@id' => seo_canonical() . '#faq',
			'mainEntity' => array_map(fn($qa) => [
				'@type' => 'Question',
				'name' => $qa[0],
				'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
			], (array)$p['faq']),
		];
	}
	echo seo_jsonld($nodes);
	?>
</body>
</html>
