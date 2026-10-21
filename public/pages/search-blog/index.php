<?php
/**
 * Поиск по блогу — современная страница поиска статей.
 * Единый дизайн с основным блогом.
 */
require_once __DIR__ . '/../../../app/bootstrap.php';

$q = mb_substr(fss_param('q'), 0, 120);
$tag = mb_substr(fss_param('tag'), 0, 60);
$cat = mb_substr(fss_param('cat'), 0, 60);

$posts = seo_blog_posts();

$results = [];
if ($q !== '' || $tag !== '' || $cat !== '') {
	$needleQ = $q !== '' ? mb_strtolower($q) : '';
	$needleTag = $tag !== '' ? mb_strtolower($tag) : '';
	$needleCat = $cat !== '' ? mb_strtolower($cat) : '';

	foreach ($posts as $slug => $p) {
		$hay = mb_strtolower($p['title'] . ' ' . $p['description'] . ' ' . implode(' ', $p['tags']) . ' ' . $p['category_name']);
		$score = 0;
		if ($q !== '' && mb_strpos($hay, $needleQ) !== false) {
			foreach (preg_split('/\s+/u', $needleQ) as $word) {
				if ($word !== '' && mb_strpos($hay, $word) !== false) $score++;
			}
		}
		if ($tag !== '' && in_array($tag, $p['tags'])) $score += 3;
		if ($cat !== '' && $p['category_slug'] === $cat) $score += 2;

		if (($q === '' || mb_strpos($hay, $needleQ) !== false) &&
			($tag === '' || in_array($tag, $p['tags'])) &&
			($cat === '' || $p['category_slug'] === $cat)) {
			$results[] = ['slug' => $slug, 'score' => $score] + $p;
		}
	}
	usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
	$results = array_slice($results, 0, 20);
}

$count = count($results);
$activeFilters = ($q !== '' || $tag !== '' || $cat !== '');
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Поиск статей — Блог | ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="Поиск по блогу: статьи о строительстве БВЗ, материалах, ценах и рекомендациях. Найдите нужную информацию быстро.">
	<meta name="robots" content="noindex, follow">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#876b55">
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<link rel="manifest" href="/manifest.json" />
	<?= seo_canonical_tags() ?>
	<?= seo_og_tags('Поиск статей — Блог | ' . seo_config('brand'), 'Поиск по блогу ФАВОРИТ-СТРОЙ-СНАБ: статьи о строительстве БВЗ, материалах, ценах и рекомендациях.') ?>
	<?= seo_verify_tags() ?>
	<link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-latin.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-cyrillic.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/fonts/WixMadeforDisplay/WixMadeforDisplay-Variable.woff2" crossorigin><link rel="stylesheet" href="/public/assets/vendor/fonts/fonts.css">
	<style> body { font-family: 'DM Sans Variable', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>" as="style">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<script defer src="/public/assets/distJS/main.js?v=<?= seo_ver() ?>"></script>
</head>

<body class="blog-page blog-search-page" data-page="inner">
	<?php include_once __DIR__ . '/../../components/header.php'; ?>

	<main class="main">
		<!-- Search Hero -->
		<section class="blog-hero blog-search-hero view" aria-labelledby="search-hero-title">
			<svg class="blog-hero-shape" viewBox="0 0 1920 500" preserveAspectRatio="none" aria-hidden="true">
				<defs>
					<linearGradient id="searchHeroGrad" x1="0%" y1="0%" x2="100%" y2="100%">
						<stop offset="0%" stop-color="#f8f4ef"/>
						<stop offset="50%" stop-color="#f0e6da"/>
						<stop offset="100%" stop-color="#f4eade"/>
					</linearGradient>
				</defs>
				<path d="M0,0 H1920 Q1960,0 1960,40 V440 Q1960,500 1920,500 H0 Q-40,500 -40,460 V0 Q-40,-40 0,0 Z" fill="url(#searchHeroGrad)" opacity="0.6"/>
				<path d="M0,100 Q200,60 400,120 Q600,180 800,140 Q1000,100 1200,160 Q1400,220 1600,180 Q1800,140 1920,160 V500 H0 V100 Z" fill="#fff" opacity="0.4"/>
			</svg>

			<div class="container">
				<?= seo_breadcrumbs_html() ?>
				<div class="blog-hero-content">
					<span class="blog-hero-badge">Поиск по блогу</span>
					<h1 id="search-hero-title" class="blog-hero-title">Найдите нужную статью</h1>
					<p class="blog-hero-desc">Поиск по темам, тегам и категориям — <strong><?= count($posts) ?></strong> практических материалов</p>

					<form action="/search/blog" method="get" class="blog-search-form blog-search-form--hero" role="search">
						<label for="hero-search" class="visually-hidden">Поиск по статьям</label>
						<input type="search" id="hero-search" name="q" placeholder="Например: цена БВЗ, фундамент, сэндвич-панели, монтаж ГЖ" aria-label="Поиск по статьям" value="<?= htmlspecialchars((string)$q, ENT_QUOTES) ?>">
						<button type="submit" aria-label="Найти"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></button>
					</form>

					<?php if ($activeFilters): ?>
					<div class="blog-search-filters-info">
						<?php if ($q): ?>
						<span class="blog-filter-tag">
							Запрос: <strong><?= htmlspecialchars($q, ENT_QUOTES) ?></strong>
							<a href="/search/blog?cat=<?= urlencode($cat) ?>&tag=<?= urlencode($tag) ?>" title="Убрать фильтр запроса" class="blog-filter-tag-remove" aria-label="Убрать фильтр запроса">×</a>
						</span>
						<?php endif; ?>
						<?php if ($cat): ?>
						<span class="blog-filter-tag">
							Категория: <strong><?= htmlspecialchars($cat, ENT_QUOTES) ?></strong>
							<a href="/search/blog?q=<?= urlencode($q) ?>&tag=<?= urlencode($tag) ?>" title="Убрать фильтр категории" class="blog-filter-tag-remove" aria-label="Убрать фильтр категории">×</a>
						</span>
						<?php endif; ?>
						<?php if ($tag): ?>
						<span class="blog-filter-tag">
							Тег: <strong>#<?= htmlspecialchars($tag, ENT_QUOTES) ?></strong>
							<a href="/search/blog?q=<?= urlencode($q) ?>&cat=<?= urlencode($cat) ?>" title="Убрать фильтр тега" class="blog-filter-tag-remove" aria-label="Убрать фильтр тега">×</a>
						</span>
						<?php endif; ?>
						<?php if ($activeFilters): ?>
						<a href="/search/blog" title="Сбросить все фильтры" class="blog-filters-clear">Сбросить все</a>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<svg class="blog-hero-curve" viewBox="0 0 1920 100" preserveAspectRatio="none" aria-hidden="true">
				<path d="M0,100 Q480,20 960,60 Q1440,100 1920,80 V100 H0 Z" fill="#fff"/>
			</svg>
		</section>

		<!-- Results -->
		<section class="blog-content-section" aria-labelledby="results-title">
			<div class="container">
				<header class="section-header">
					<h2 id="results-title" class="section-title">
						<?php if ($activeFilters): ?>
							<?php if ($count > 0): ?>
								Найдено <span class="text-brand"><?= $count ?></span> статей
							<?php else: ?>
								Ничего не найдено
							<?php endif; ?>
						<?php else: ?>
							Введите запрос для поиска
						<?php endif; ?>
					</h2>
				</header>

				<?php if ($activeFilters && $count > 0): ?>
				<div class="blog-cards-grid" id="blog-list" role="list">
					<?php foreach ($results as $p):
						$cardImg = preg_replace('#/og/og-blog-#', '/og/cards/og-blog-', preg_replace('/-1200x630\./', '-400x225.', $p['og_image']));
					?>
					<article class="blog-card" itemscope itemtype="https://schema.org/BlogPosting" role="listitem">
						<meta itemprop="inLanguage" content="ru-RU">
						<a href="/blog/<?= $p['slug'] ?>" title="<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>" class="blog-card-link" itemprop="url">
							<div class="blog-card-media">
								<div class="blog-card-img" style="background-image:url('<?= $cardImg ?>')" itemprop="image"></div>
								<div class="blog-card-img-overlay"></div>
								<span class="blog-card-cat"><?= htmlspecialchars($p['category_name'], ENT_QUOTES) ?></span>
							</div>
							<div class="blog-card-content">
								<h3 class="blog-card-title" itemprop="headline"><?= htmlspecialchars($p['title'], ENT_QUOTES) ?></h3>
								<p class="blog-card-excerpt" itemprop="description"><?= htmlspecialchars($p['description'], ENT_QUOTES) ?></p>
								<div class="blog-card-meta">
									<time class="blog-card-date" itemprop="datePublished" datetime="<?= $p['date'] ?>"><?= date('d.m.Y', strtotime($p['date'])) ?></time>
									<span class="blog-meta-dot" aria-hidden="true">•</span>
									<span class="blog-card-read">~<?= $p['read_minutes'] ?> мин</span>
								</div>
							</div>
							<span class="blog-card-arrow" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
						</a>
					</article>
					<?php endforeach; ?>
				</div>
				<?php elseif ($activeFilters && $count === 0): ?>
				<div class="blog-empty-state" role="status">
					<svg class="blog-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
					<h3 class="blog-empty-title">Ничего не найдено</h3>
					<p class="blog-empty-desc">По вашему запросу «<strong><?= htmlspecialchars($q, ENT_QUOTES) ?></strong>» материалов нет. Попробуйте изменить запрос или очистить фильтры.</p>
					<a href="/blog" title="Все статьи блога" class="blog-empty-btn">Все статьи блога</a>
				</div>
				<?php else: ?>
				<div class="blog-empty-state" role="status">
					<svg class="blog-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
					<h3 class="blog-empty-title">Начните поиск</h3>
					<p class="blog-empty-desc">Введите запрос в строке выше или выберите категорию/тег — найдём нужную статью за секунды.</p>
					<a href="/blog" title="Все статьи блога" class="blog-empty-btn">Все статьи блога</a>
				</div>
				<?php endif; ?>
			</div>
		</section>

		<!-- CTA -->
		<section class="blog-cta-section" aria-labelledby="search-cta-title">
			<div class="blog-cta-bg" aria-hidden="true"></div>
			<div class="container">
				<div class="blog-cta-card">
					<div class="blog-cta-content">
						<h2 id="search-cta-title" class="blog-cta-title">Не нашли нужное?</h2>
						<p class="blog-cta-desc">Задайте вопрос экспертам ФСС — получите консультацию и расчёт сметы за 15 минут.</p>
					</div>
					<div class="blog-cta-actions">
						<a href="/#contact" title="Получить консультацию" class="blog-cta-btn blog-cta-btn--primary">Получить консультацию <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
						<a href="tel:+<?= preg_replace('/\D/', '', (string)seo_config('phone')) ?>" title="Позвонить" class="blog-cta-btn blog-cta-btn--phone"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg> <?= htmlspecialchars((string)seo_config('phone_display')) ?></a>
					</div>
				</div>
			</div>
			<svg class="blog-cta-curve" viewBox="0 0 1920 100" preserveAspectRatio="none" aria-hidden="true">
				<path d="M0,20 Q480,80 960,40 Q1440,0 1920,20 V100 H0 Z" fill="#fff"/>
			</svg>
		</section>
	</main>

	<?php include_once __DIR__ . '/../../components/footer.php'; ?>
	<style>
		:root {
			--brand-50: #f8f4ef;
			--brand-100: #f0e6da;
			--brand-200: #e3d1bd;
			--brand-300: #d2b89d;
			--brand-400: #c7ab94;
			--brand-500: #b1907a;
			--brand-600: #9a7c66;
			--brand-700: #876b55;
			--brand: #876b55;
			--brand-800: #6d5744;
			--brand-900: #554433;
			--brand-950: #3f3227;
			--brand-soft: #f0e6da;
			--brand-tint: #f8f4ef;
			--brand-tint-2: #f1e9df;
			--brand-tint-3: #f4eade;
			--ink: #171310;
			--ink-soft: #44403C;
			--border: #efe7da;
			--white: #fff;
			--radius-full: 999px;
			--shadow-sm: 0 2px 8px rgba(23,19,16,.06);
			--shadow-brand: 0 10px 30px rgba(135,107,85,.25);
			--shadow-brand-lg: 0 20px 40px rgba(135,107,85,.3);
			--transition: .25s cubic-bezier(.4, 0, .2, 1);
		}
		/* Search page specific styles */
		.blog-search-page .blog-hero { padding: max(clamp(3rem, 5vw, 4rem), calc(var(--fss-header-h, 90px) + 12px)) 0 0; }
		.blog-search-hero .blog-hero-content { max-width: 720px; }
		.blog-search-form--hero { margin-top: 24px; max-width: 640px; margin-inline: auto; }
		.blog-search-form--hero input { height: 52px; padding: 0 20px; font-size: 16px; border-radius: var(--radius-full); border: 1px solid var(--border); background: var(--white); box-shadow: var(--shadow-sm); transition: all var(--transition); }
		.blog-search-form--hero input:focus { border-color: var(--brand); box-shadow: 0 0 0 4px rgba(135,107,85,.15); }
		.blog-search-form--hero button { width: 52px; height: 52px; border-radius: 50%; background: var(--brand); color: #fff; box-shadow: var(--shadow-brand); transition: all var(--transition); }
		.blog-search-form--hero button:hover { background: var(--brand-800); transform: scale(1.05); box-shadow: var(--shadow-brand-lg); }

		.blog-search-filters-info { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 8px; margin-top: 20px; }
		.blog-filter-tag { display: inline-flex; align-items: center; gap: 6px; background: var(--white); border: 1px solid var(--border); padding: 6px 14px; border-radius: var(--radius-full); font-size: 13px; color: var(--ink-soft); box-shadow: var(--shadow-sm); }
		.blog-filter-tag strong { color: var(--ink); }
		.blog-filter-tag-remove { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; margin-left: 8px; border-radius: 50%; background: var(--brand-tint-2); color: var(--ink); font-size: 14px; font-weight: 700; line-height: 1; text-decoration: none; transition: all var(--transition); }
		.blog-filter-tag-remove:hover { background: var(--brand); color: #fff; }
		.blog-filters-clear { color: var(--brand-800); font-size: 13px; font-weight: 600; text-decoration: underline; text-underline-offset: 2px; transition: color var(--transition); }
		.blog-filters-clear:hover { color: var(--brand); }

		@media (max-width: 640px) {
			.blog-search-form--hero { flex-direction: column; }
			.blog-search-form--hero button { width: 100%; border-radius: var(--radius-full); }
		}
	</style>
</body>
</html>