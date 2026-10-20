<?php require_once __DIR__ . '/../../../app/bootstrap.php'; ?>
<?php
// ======================= DATA =======================
$allPosts = seo_blog_posts();
uasort($allPosts, static fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));
$totalCount = count($allPosts);

$catCounts = [];
foreach ($allPosts as $p) {
	$catCounts[$p['category_name']] = ($catCounts[$p['category_name']] ?? 0) + 1;
}

$filterCat = isset($_GET['category']) ? trim((string)$_GET['category']) : 'all';
$isFiltered = ($filterCat !== '' && $filterCat !== 'all' && isset($catCounts[$filterCat]));

$filtered = [];
foreach ($allPosts as $slug => $p) {
	if ($isFiltered && $p['category_name'] !== $filterCat) continue;
	$filtered[$slug] = $p;
}

$featuredSlug = (!$isFiltered && $filtered) ? array_key_first($filtered) : null;
$list = $filtered;
if ($featuredSlug !== null) unset($list[$featuredSlug]);

$perPage = 6;
$total = count($list);
$maxPages = max(1, (int)ceil($total / $perPage));
$page = max(1, min($maxPages, (int)($_GET['page'] ?? 1)));
$pageItems = array_slice($list, ($page - 1) * $perPage, $perPage, true);

$popular = array_slice($allPosts, 0, 5, true);
$sidebarServices = [
	'proizvodim_modul' => ['Модульные здания', 'Производство и монтаж под ключ'],
	'angar'            => ['Ангары под ключ', 'Арочные и прямостенные'],
	'sclad'            => ['Склады и логистика', 'Из сэндвич-панелей'],
	'kapstroenie'      => ['Капитальное строительство', 'Монолит, металлокаркас'],
];

$cardImg = static fn(string $og): string => (string)preg_replace(
	'#/og/og-blog-#',
	'/og/cards/og-blog-',
	(string)preg_replace('/-1200x630\./', '-400x225.', $og)
);

$title = 'Блог — статьи о строительстве БВЗ | ФАВОРИТ-СТРОЙ-СНАБ';
$description = 'Экспертные статьи о строительстве быстровозводимых зданий: материалы, сроки, цены и рекомендации. ' . $totalCount . ' практических материалов.';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $title ?></title>
	<meta name="description" content="<?= $description ?> ☎ <?= htmlspecialchars((string)seo_config('phone_display')) ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<link rel="manifest" href="/public/assets/image/logo/favicon/site.webmanifest" />
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>
	<?= seo_og_tags($title, $description, '/public/assets/image/og/og-blog-1200x630.jpg') ?>
	<link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-latin.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/vendor/fonts/Manrope-cyrillic.woff2" crossorigin><link rel="preload" as="font" type="font/woff2" href="/public/assets/fonts/WixMadeforDisplay/WixMadeforDisplay-Variable.woff2" crossorigin><link rel="stylesheet" href="/public/assets/vendor/fonts/fonts.css">
	<style> body { font-family: 'DM Sans Variable', Manrope, Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial; } </style>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>" as="style">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=<?= seo_ver() ?>">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
</head>

<body class="blog-page" data-page="inner">
	<?php include_once __DIR__ . '/../../components/header.php'; ?>

	<main class="main">
		<!-- Blog Hero with organic background -->
		<section class="blog-hero view" aria-labelledby="blog-hero-title">
			<!-- Organic background shape -->
			<svg class="blog-hero-shape" viewBox="0 0 1920 600" preserveAspectRatio="none" aria-hidden="true">
				<defs>
					<linearGradient id="blogHeroGrad" x1="0%" y1="0%" x2="100%" y2="100%">
						<stop offset="0%" stop-color="#FFF7EF"/>
						<stop offset="50%" stop-color="#FFEDD5"/>
						<stop offset="100%" stop-color="#FDE3C9"/>
					</linearGradient>
				</defs>
				<path d="M0,0 H1920 Q1960,0 1960,40 V520 Q1960,580 1920,580 H0 Q-40,580 -40,540 V0 Q-40,-40 0,0 Z" fill="url(#blogHeroGrad)" opacity="0.6"/>
				<path d="M0,120 Q200,80 400,140 Q600,200 800,160 Q1000,120 1200,180 Q1400,240 1600,200 Q1800,160 1920,180 V580 H0 V120 Z" fill="#fff" opacity="0.4"/>
			</svg>

			<div class="container">
				<?= seo_breadcrumbs_html() ?>
				<div class="blog-hero-content">
					<span class="blog-hero-badge">Экспертиза ФСС</span>
					<h1 id="blog-hero-title" class="blog-hero-title">Блог экспертов <span class="text-brand">ФАВОРИТ-СТРОЙ-СНАБ</span></h1>
					<p class="blog-hero-desc">Практические статьи о строительстве БВЗ: материалы, сроки, цены и рекомендации. <strong><?= $totalCount ?></strong> материалов с реальными кейсами.</p>
					<div class="blog-hero-stats">
						<div class="blog-stat">
							<span class="blog-stat-value"><?= $totalCount ?>+</span>
							<span class="blog-stat-label">статей</span>
						</div>
						<div class="blog-stat-divider" aria-hidden="true"></div>
						<div class="blog-stat">
							<span class="blog-stat-value">14</span>
							<span class="blog-stat-label">лет на рынке</span>
						</div>
						<div class="blog-stat-divider" aria-hidden="true"></div>
						<div class="blog-stat">
							<span class="blog-stat-value">180+</span>
							<span class="blog-stat-label">объектов</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Bottom curve -->
			<svg class="blog-hero-curve" viewBox="0 0 1920 100" preserveAspectRatio="none" aria-hidden="true">
				<path d="M0,100 Q480,20 960,60 Q1440,100 1920,80 V100 H0 Z" fill="#fff"/>
			</svg>
		</section>

		<?php if ($featuredSlug !== null): $f = $filtered[$featuredSlug]; ?>
		<!-- Featured Article -->
		<section class="blog-featured-section" aria-labelledby="featured-title">
			<div class="container">
				<header class="section-header">
					<span class="section-pill">Рекомендуем</span>
					<h2 id="featured-title" class="section-title">Исключительный материал недели</h2>
				</header>
				<article class="blog-featured-card" itemscope itemtype="https://schema.org/BlogPosting">
					<meta itemprop="inLanguage" content="ru-RU">
					<a href="/blog/<?= htmlspecialchars($featuredSlug, ENT_QUOTES) ?>" title="<?= htmlspecialchars($f['title'], ENT_QUOTES) ?>" class="blog-featured-link" itemprop="url">
						<div class="blog-featured-media">
							<div class="blog-featured-img" style="background-image:url('<?= htmlspecialchars($cardImg($f['og_image']), ENT_QUOTES) ?>')" itemprop="image"></div>
							<div class="blog-featured-overlay"></div>
							<span class="blog-featured-badge">Рекомендуем</span>
							<span class="blog-featured-cat"><?= htmlspecialchars($f['category_name'], ENT_QUOTES) ?></span>
						</div>
						<div class="blog-featured-body">
							<h3 class="blog-featured-title" itemprop="headline"><?= htmlspecialchars($f['title'], ENT_QUOTES) ?></h3>
							<p class="blog-featured-text" itemprop="description"><?= htmlspecialchars($f['description'], ENT_QUOTES) ?></p>
							<div class="blog-featured-meta">
								<time itemprop="datePublished" datetime="<?= htmlspecialchars($f['date'], ENT_QUOTES) ?>"><?= date('d.m.Y', strtotime($f['date'])) ?></time>
								<span class="blog-meta-dot" aria-hidden="true">•</span>
								<span>~<?= (int)$f['read_minutes'] ?> мин чтения</span>
							</div>
							<?php if (!empty($f['author'])): ?>
							<div class="blog-featured-author">
								<img src="<?= htmlspecialchars($f['author']['photo'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($f['author']['name'], ENT_QUOTES) ?>" class="blog-author-avatar" loading="lazy" width="36" height="36" title="<?= htmlspecialchars($f['author']['name'], ENT_QUOTES) ?> — <?= htmlspecialchars($f['author']['role'], ENT_QUOTES) ?>">
								<div class="blog-author-info">
									<span class="blog-author-name"><?= htmlspecialchars($f['author']['name'], ENT_QUOTES) ?></span>
									<span class="blog-author-role"><?= htmlspecialchars($f['author']['role'], ENT_QUOTES) ?></span>
								</div>
							</div>
							<?php endif; ?>
							<span class="blog-featured-cta">Читать статью <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
						</div>
					</a>
				</article>
			</div>
		</section>
		<?php endif; ?>

		<!-- Articles Grid -->
		<section class="blog-content-section" aria-labelledby="articles-title">
			<div class="container">
				<header class="section-header">
					<h2 id="articles-title" class="section-title"><?= $isFiltered ? 'Статьи категории «' . htmlspecialchars($filterCat, ENT_QUOTES) . '»' : 'Все статьи блога' ?></h2>
					<?php if ($isFiltered): ?>
					<p class="section-desc">Найдено <strong><?= $total ?></strong> материалов. <a href="/blog#blog-list" title="Сбросить фильтр" class="reset-filter">Сбросить фильтр</a></p>
					<?php endif; ?>
				</header>

				<div class="blog-layout">
					<div class="blog-main">
						<!-- Filters -->
						<div class="blog-filters-bar" role="group" aria-label="Фильтр статей по категориям">
							<a href="/blog#blog-list" title="Все статьи" class="blog-filter <?= $isFiltered ? '' : 'active' ?>" data-filter="all">
								<span>Все статьи</span>
								<span class="blog-filter__count"><?= $totalCount ?></span>
							</a>
							<?php foreach ($catCounts as $cat => $cnt): ?>
							<a href="/blog?category=<?= urlencode($cat) ?>#blog-list" title="<?= htmlspecialchars($cat, ENT_QUOTES) ?>" class="blog-filter <?= ($isFiltered && $filterCat === $cat) ? 'active' : '' ?>" data-filter="<?= htmlspecialchars($cat, ENT_QUOTES) ?>">
								<span><?= htmlspecialchars($cat, ENT_QUOTES) ?></span>
								<span class="blog-filter__count"><?= (int)$cnt ?></span>
							</a>
							<?php endforeach; ?>
						</div>

						<!-- Articles Grid -->
						<div class="blog-cards-grid" id="blog-list" role="list">
							<?php if (!$pageItems): ?>
							<div class="blog-empty-state" role="status">
								<svg class="blog-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
								<h3 class="blog-empty-title">Статьи не найдены</h3>
								<p class="blog-empty-desc">В этой категории пока нет материалов. Попробуйте другую категорию или вернитесь позже.</p>
								<a href="/blog#blog-list" title="Все статьи" class="blog-empty-btn">Все статьи</a>
							</div>
							<?php else: foreach ($pageItems as $slug => $a): ?>
							<article class="blog-card" itemscope itemtype="https://schema.org/BlogPosting" role="listitem">
								<meta itemprop="inLanguage" content="ru-RU">
								<a href="/blog/<?= htmlspecialchars($slug, ENT_QUOTES) ?>" title="<?= htmlspecialchars($a['title'], ENT_QUOTES) ?>" class="blog-card-link" itemprop="url">
									<div class="blog-card-media">
										<div class="blog-card-img" style="background-image:url('<?= htmlspecialchars($cardImg($a['og_image']), ENT_QUOTES) ?>')" itemprop="image"></div>
										<div class="blog-card-img-overlay"></div>
										<?php if (!empty($a['category_name'])): ?>
										<span class="blog-card-cat"><?= htmlspecialchars($a['category_name'], ENT_QUOTES) ?></span>
										<?php endif; ?>
									</div>
									<div class="blog-card-content">
										<h3 class="blog-card-title" itemprop="headline"><?= htmlspecialchars($a['title'], ENT_QUOTES) ?></h3>
										<p class="blog-card-excerpt" itemprop="description"><?= htmlspecialchars($a['description'], ENT_QUOTES) ?></p>
										<div class="blog-card-meta">
											<time class="blog-card-date" itemprop="datePublished" datetime="<?= htmlspecialchars($a['date'], ENT_QUOTES) ?>"><?= date('d.m.Y', strtotime($a['date'])) ?></time>
											<span class="blog-meta-dot" aria-hidden="true">•</span>
											<span class="blog-card-read">~<?= (int)$a['read_minutes'] ?> мин</span>
										</div>
										<?php if (!empty($a['author'])): ?>
										<div class="blog-card-author">
											<img src="<?= htmlspecialchars($a['author']['photo'], ENT_QUOTES) ?>" alt="<?= htmlspecialchars($a['author']['name'], ENT_QUOTES) ?>" class="blog-author-avatar" loading="lazy" width="28" height="28" title="<?= htmlspecialchars($a['author']['name'], ENT_QUOTES) ?> — <?= htmlspecialchars($a['author']['role'], ENT_QUOTES) ?>">
											<div class="blog-author-info">
												<span class="blog-author-name"><?= htmlspecialchars($a['author']['name'], ENT_QUOTES) ?></span>
												<span class="blog-author-role"><?= htmlspecialchars($a['author']['role'], ENT_QUOTES) ?></span>
											</div>
										</div>
										<?php endif; ?>
									</div>
									<span class="blog-card-arrow" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></span>
								</a>
							</article>
							<?php endforeach; endif; ?>
						</div>

						<!-- Pagination -->
						<?php if ($maxPages > 1): ?>
						<nav class="blog-pagination" aria-label="Пагинация статей">
							<?php
								$qbase = $isFiltered ? '&category=' . urlencode($filterCat) : '';
								$qp = static fn(int $n): string => '?page=' . $n . $qbase . '#blog-list';
							?>
							<?php if ($page > 1): ?>
							<a href="<?= $qp($page - 1) ?>" title="Предыдущая страница" class="blog-page-btn blog-page-btn--prev" aria-label="Предыдущая страница">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 18l-6-6 6-6"/></svg>
								<span>Назад</span>
							</a>
							<?php endif; ?>
							<div class="blog-page-numbers" role="navigation" aria-label="Номера страниц">
								<?php
								$showStart = max(1, $page - 1);
								$showEnd = min($maxPages, $page + 1);
								if ($showStart > 1) {
									echo '<a href="' . $qp(1) . '" title="Страница 1" class="blog-page-btn">1</a>';
									if ($showStart > 2) echo '<span class="blog-page-ellipsis" aria-hidden="true">…</span>';
								}
								for ($i = $showStart; $i <= $showEnd; $i++):
								?>
									<?php if ($i === $page): ?>
									<span class="blog-page-btn active" aria-current="page"><?= $i ?></span>
									<?php else: ?>
									<a href="<?= $qp($i) ?>" title="Страница <?= $i ?>" class="blog-page-btn"><?= $i ?></a>
									<?php endif; ?>
								<?php endfor; ?>
								<?php if ($showEnd < $maxPages) {
									if ($showEnd < $maxPages - 1) echo '<span class="blog-page-ellipsis" aria-hidden="true">…</span>';
									echo '<a href="' . $qp($maxPages) . '" title="Страница ' . $maxPages . '" class="blog-page-btn">' . $maxPages . '</a>';
								} ?>
							</div>
							<?php if ($page < $maxPages): ?>
							<a href="<?= $qp($page + 1) ?>" title="Следующая страница" class="blog-page-btn blog-page-btn--next" aria-label="Следующая страница">
								<span>Вперёд</span>
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 6l6 6-6 6"/></svg>
							</a>
							<?php endif; ?>
						</nav>
						<?php endif; ?>
					</div>

					<!-- Sidebar -->
					<aside class="blog-sidebar" aria-label="Дополнительно">
						<!-- Search -->
						<div class="blog-sidebar-card">
							<div class="blog-sidebar-header">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
								Поиск по блогу
							</div>
							<div class="blog-sidebar-body">
								<form action="/search/blog" method="get" class="blog-search-form" role="search">
									<label for="blog-search" class="visually-hidden">Поиск по блогу</label>
									<input type="search" id="blog-search" name="q" placeholder="Например: цена БВЗ" aria-label="Поиск по блогу" value="<?= htmlspecialchars((string)($_GET['q'] ?? ''), ENT_QUOTES) ?>">
									<button type="submit" aria-label="Найти"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg></button>
								</form>
							</div>
						</div>

						<!-- Popular -->
						<div class="blog-sidebar-card">
							<div class="blog-sidebar-header">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
								Популярные статьи
							</div>
							<div class="blog-sidebar-body">
								<?php foreach ($popular as $slug => $item): ?>
								<a class="blog-popular-item" title="<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>" href="/blog/<?= htmlspecialchars($slug, ENT_QUOTES) ?>">
									<div class="blog-popular-img" style="background-image:url('<?= htmlspecialchars($cardImg($item['og_image']), ENT_QUOTES) ?>')"></div>
									<div class="blog-popular-body">
										<h4 class="blog-popular-title"><?= htmlspecialchars($item['title'], ENT_QUOTES) ?></h4>
										<time class="blog-popular-date" datetime="<?= htmlspecialchars($item['date'], ENT_QUOTES) ?>"><?= date('d.m.Y', strtotime($item['date'])) ?></time>
									</div>
								</a>
								<?php endforeach; ?>
							</div>
						</div>

						<!-- Services CTA -->
						<div class="blog-sidebar-card blog-sidebar-card--cta">
							<div class="blog-sidebar-header">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 7l-8 9-4-4"/></svg>
								Наши услуги
							</div>
							<div class="blog-sidebar-body">
								<?php foreach ($sidebarServices as $sslug => [$stitle, $sdesc]): ?>
								<a class="blog-service-item" title="<?= htmlspecialchars($service['title'] ?? '', ENT_QUOTES) ?>" href="/services/<?= htmlspecialchars($sslug, ENT_QUOTES) ?>">
									<div class="blog-service-icon">
										<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 7l-8 9-4-4"/></svg>
									</div>
									<div>
										<div class="blog-service-title"><?= htmlspecialchars($stitle, ENT_QUOTES) ?></div>
										<div class="blog-service-desc"><?= htmlspecialchars($sdesc, ENT_QUOTES) ?></div>
									</div>
								</a>
								<?php endforeach; ?>
								<a href="/uslugi" title="Все услуги" class="blog-service-all">Все услуги <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
							</div>
						</div>

						<!-- Newsletter/CTA Card -->
						<div class="blog-sidebar-card blog-sidebar-card--newsletter">
							<div class="blog-sidebar-header">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/></svg>
								Подписка на рассылку
							</div>
							<div class="blog-sidebar-body">
								<p class="blog-newsletter-desc">Получайте лучшие статьи о строительстве БВЗ раз в две недели. Без спама.</p>
								<form class="blog-newsletter-form" action="/#contact" method="post">
									<label for="nl-email" class="visually-hidden">Email для подписки</label>
									<input type="email" id="nl-email" name="email" placeholder="Ваш email" required autocomplete="email">
									<button type="submit" class="blog-newsletter-btn">Подписаться</button>
								</form>
								<p class="blog-newsletter-note">Нажимая кнопку, вы соглашаетесь с <a href="/politika" title="Политика конфиденциальности">политикой конфиденциальности</a></p>
							</div>
						</div>
					</aside>
				</div>
			</div>
		</section>

		<!-- CTA Section -->
		<section class="blog-cta-section" aria-labelledby="cta-title">
			<div class="blog-cta-bg" aria-hidden="true"></div>
			<div class="container">
				<div class="blog-cta-card">
					<div class="blog-cta-content">
						<h2 id="cta-title" class="blog-cta-title">Есть вопросы по проекту?</h2>
						<p class="blog-cta-desc">Получите бесплатную консультацию эксперта и расчёт сметы за 15 минут. Работаем по всей России.</p>
					</div>
					<div class="blog-cta-actions">
						<a href="/#contact" title="Рассчитать смету" class="blog-cta-btn blog-cta-btn--primary">Рассчитать смету <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
						<a href="tel:+<?= preg_replace('/\D/', '', (string)seo_config('phone')) ?>" title="Позвонить" class="blog-cta-btn blog-cta-btn--phone"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg> <?= htmlspecialchars((string)seo_config('phone_display')) ?></a>
					</div>
				</div>
			</div>
			<!-- Bottom curve -->
			<svg class="blog-cta-curve" viewBox="0 0 1920 100" preserveAspectRatio="none" aria-hidden="true">
				<path d="M0,20 Q480,80 960,40 Q1440,0 1920,20 V100 H0 Z" fill="#fff"/>
			</svg>
		</section>
	</main>

<?php include_once __DIR__ . '/../../components/footer.php'; ?>
	<style>
		/* ===== Blog Design System ===== */
		:root {
			--sun: #F97316;
			--sun-deep: #EA580C;
			--sun-dark: #C2410C;
			--sun-light: #FB923C;
			--cream: #FFF7EF;
			--cream-2: #FFF1E3;
			--cream-3: #FDE3C9;
			--ink: #171310;
			--ink-soft: #44403C;
			--muted: #78716c;
			--border: #efe7da;
			--white: #fff;
			--radius-sm: 8px;
			--radius-md: 12px;
			--radius-lg: 18px;
			--radius-xl: 24px;
			--radius-full: 999px;
			--shadow-sm: 0 2px 8px rgba(23,19,16,.06);
			--shadow-md: 0 8px 24px rgba(23,19,16,.08);
			--shadow-lg: 0 20px 40px rgba(23,19,16,.1);
			--shadow-sun: 0 10px 30px rgba(249,115,22,.25);
			--shadow-sun-lg: 0 20px 40px rgba(249,115,22,.3);
			--transition: .25s cubic-bezier(.4, 0, .2, 1);
			--font-heading:  'Manrope', system-ui, sans-serif;
			--font-body: 'Manrope', system-ui, sans-serif;
			--container: min(100% - 32px, 1180px);
		}

		.blog-page { background: var(--cream); color: var(--ink); font-family: var(--font-body); -webkit-font-smoothing: antialiased; }
		.blog-page .container { width: var(--container); max-width: 1180px; margin-inline: auto; }

		/* Visually hidden */
		.visually-hidden { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }

		/* Section Header */
		.section-header { text-align: center; margin-bottom: 32px; }
		.section-pill { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, var(--sun-light), var(--sun-deep)); color: #fff; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: 6px 14px; border-radius: var(--radius-full); margin-bottom: 12px; box-shadow: var(--shadow-sun); }
		.section-title { font-family: var(--font-heading); font-size: clamp(28px, 4vw, 42px); font-weight: 800; line-height: 1.15; letter-spacing: -0.02em; color: var(--ink); margin: 0; }
		.section-desc { margin-top: 10px; color: var(--muted); font-size: 15px; line-height: 1.6; }
		.section-desc a { color: var(--sun-deep); text-decoration: underline; text-underline-offset: 2px; }
		.section-desc a:hover { color: var(--sun); }

		/* ===== Hero ===== */
		.blog-hero { position: relative; padding: max(clamp(4rem, 8vw, 6rem), calc(var(--fss-header-h, 90px) + 12px)) 0 clamp(3rem, 6vw, 4rem); overflow: hidden; }
		.blog-hero-shape { position: absolute; inset: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
		.blog-hero-content { position: relative; z-index: 1; text-align: center; max-width: 800px; margin: 0 auto; }
		.blog-hero-badge { display: inline-flex; align-items: center; gap: 8px; background: var(--white); border: 1px solid var(--border); color: var(--sun-deep); font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; padding: 8px 18px; border-radius: var(--radius-full); margin-bottom: 20px; box-shadow: var(--shadow-sm); backdrop-filter: blur(8px); }
		.blog-hero-title { font-family: var(--font-heading); font-size: clamp(36px, 5.5vw, 58px); font-weight: 800; line-height: 1.08; letter-spacing: -0.025em; color: var(--ink); margin: 0 0 16px; }
		.blog-hero-title .text-brand { color: var(--sun); }
		.blog-hero-desc { font-size: clamp(16px, 2vw, 20px); line-height: 1.65; color: var(--ink-soft); margin: 0 0 32px; max-width: 680px; margin-inline: auto; }
		.blog-hero-stats { display: flex; align-items: center; justify-content: center; gap: 24px; flex-wrap: wrap; }
		.blog-stat { display: flex; flex-direction: column; align-items: center; gap: 4px; }
		.blog-stat-value { font-family: var(--font-heading); font-size: clamp(28px, 4vw, 40px); font-weight: 800; line-height: 1.1; color: var(--ink); }
		.blog-stat-label { font-size: 13px; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
		.blog-stat-divider { width: 1px; height: 40px; background: linear-gradient(180deg, transparent, var(--border), transparent); }
		.blog-hero-curve { position: absolute; bottom: 0; left: 0; width: 100%; height: 80px; pointer-events: none; z-index: 2; }

		/* ===== Featured Article ===== */
		.blog-featured-section { padding: clamp(2.5rem, 4vw, 3.5rem) 0; background: var(--white); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
		.blog-featured-card { border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-lg); background: var(--white); transition: transform var(--transition), box-shadow var(--transition); }
		.blog-featured-card:hover { transform: translateY(-4px); box-shadow: 0 30px 60px rgba(23,19,16,.12); }
		.blog-featured-link { display: grid; grid-template-columns: 1.1fr 1fr; min-height: 360px; text-decoration: none; color: inherit; }
		.blog-featured-media { position: relative; overflow: hidden; }
		.blog-featured-img { position: absolute; inset: 0; background-size: cover; background-position: center; transition: transform .6s var(--transition); }
		.blog-featured-card:hover .blog-featured-img { transform: scale(1.05); }
		.blog-featured-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(23,19,16,.6) 100%); pointer-events: none; }
		.blog-featured-badge { position: absolute; top: 20px; left: 20px; z-index: 1; background: var(--sun); color: #fff; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: 6px 14px; border-radius: var(--radius-full); box-shadow: var(--shadow-sm); }
		.blog-featured-cat { position: absolute; top: 20px; right: 20px; z-index: 1; background: rgba(255,255,255,.9); backdrop-filter: blur(8px); color: var(--ink-soft); font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; padding: 6px 14px; border-radius: var(--radius-full); border: 1px solid var(--border); }
		.blog-featured-body { position: relative; z-index: 1; padding: clamp(24px, 3vw, 40px); display: flex; flex-direction: column; justify-content: flex-end; background: linear-gradient(180deg, var(--white) 0%, var(--cream) 100%); }
		.blog-featured-title { font-family: var(--font-heading); font-size: clamp(22px, 3vw, 30px); font-weight: 800; line-height: 1.2; color: var(--ink); margin: 0 0 12px; }
		.blog-featured-text { font-size: 15px; line-height: 1.65; color: var(--ink-soft); margin: 0 0 20px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
		.blog-featured-meta { display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--muted); margin-bottom: 16px; }
		.blog-meta-dot { color: var(--border); }
		.blog-featured-author { display: flex; align-items: center; gap: 10px; padding-top: 12px; border-top: 1px solid var(--border); margin-bottom: 16px; }
		.blog-card-author { display: flex; align-items: center; gap: 8px; padding-top: 12px; border-top: 1px solid var(--border); margin-top: 8px; }
		.blog-author-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; background: var(--cream-2); }
		.blog-card-author .blog-author-avatar { width: 28px; height: 28px; }
		.blog-author-info { display: flex; flex-direction: column; min-width: 0; }
		.blog-author-name { font-size: 13px; font-weight: 600; color: var(--ink); line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
		.blog-author-role { font-size: 11.5px; color: var(--muted); line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
		.blog-featured-cta { display: inline-flex; align-items: center; gap: 8px; background: var(--sun); color: #fff; font-size: 14px; font-weight: 700; padding: 14px 24px; border-radius: var(--radius-full); align-self: flex-start; transition: background var(--transition), transform var(--transition), box-shadow var(--transition); box-shadow: var(--shadow-sun); }
		.blog-featured-cta:hover { background: var(--sun-deep); transform: translateX(4px); box-shadow: var(--shadow-sun-lg); }
		.blog-featured-cta svg { transition: transform var(--transition); }
		.blog-featured-cta:hover svg { transform: translateX(3px); }

		/* ===== Content Section ===== */
		.blog-content-section { padding: clamp(3rem, 5vw, 5rem) 0 clamp(4rem, 6vw, 6rem); }
		.blog-layout { display: grid; grid-template-columns: 1fr 340px; gap: clamp(2rem, 3vw, 3rem); align-items: start; }
		@media (max-width: 980px) { .blog-layout { grid-template-columns: 1fr; } .blog-sidebar { position: static; } }

		/* ===== Filters Bar ===== */
		.blog-filters-bar { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-bottom: 32px; padding: 16px; background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
		.blog-filter { display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 20px; background: var(--white); color: var(--ink-soft); border: 1px solid var(--border); border-radius: var(--radius-full); font-size: 13px; font-weight: 600; text-decoration: none; transition: all var(--transition); white-space: nowrap; }
		.blog-filter:hover { border-color: var(--sun); color: var(--sun); transform: translateY(-2px); box-shadow: var(--shadow-sm); }
		.blog-filter.active { background: linear-gradient(135deg, var(--sun-light), var(--sun-deep)); color: #fff; border-color: transparent; box-shadow: var(--shadow-sun); }
		.blog-filter__count { background: rgba(23,19,16,.08); color: var(--muted); font-size: 11px; font-weight: 700; min-width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; padding: 0 8px; border-radius: var(--radius-full); transition: all var(--transition); }
		.blog-filter.active .blog-filter__count { background: rgba(255,255,255,.2); color: #fff; }

		/* ===== Cards Grid ===== */
		.blog-cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px; }
		@media (max-width: 760px) { .blog-cards-grid { grid-template-columns: 1fr; } }

		.blog-card { border-radius: var(--radius-lg); overflow: hidden; background: var(--white); border: 1px solid var(--border); box-shadow: var(--shadow-sm); transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition); animation: blogCardIn .4s var(--transition) both; will-change: transform, opacity; }
		@keyframes blogCardIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
		.blog-card:nth-child(1) { animation-delay: 0ms; }
		.blog-card:nth-child(2) { animation-delay: 60ms; }
		.blog-card:nth-child(3) { animation-delay: 120ms; }
		.blog-card:nth-child(4) { animation-delay: 180ms; }
		.blog-card:nth-child(5) { animation-delay: 240ms; }
		.blog-card:nth-child(6) { animation-delay: 300ms; }
		.blog-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--sun-light); }

		.blog-card-link { display: block; text-decoration: none; color: inherit; height: 100%; }
		.blog-card-media { position: relative; aspect-ratio: 16/10; overflow: hidden; }
		.blog-card-img { position: absolute; inset: 0; background-size: cover; background-position: center; transition: transform .5s var(--transition); }
		.blog-card:hover .blog-card-img { transform: scale(1.06); }
		.blog-card-img-overlay { position: absolute; inset: 0; background: linear-gradient(180deg, transparent 50%, rgba(23,19,16,.4) 100%); pointer-events: none; }
		.blog-card-cat { position: absolute; top: 12px; left: 12px; z-index: 1; background: var(--sun); color: #fff; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; padding: 4px 10px; border-radius: var(--radius-full); box-shadow: var(--shadow-sm); }
		.blog-card-arrow { position: absolute; bottom: 16px; right: 16px; z-index: 1; width: 40px; height: 40px; display: grid; place-items: center; background: var(--white); color: var(--sun); border-radius: 50%; box-shadow: var(--shadow-md); opacity: 0; transform: translateX(10px); transition: opacity var(--transition), transform var(--transition); }
		.blog-card:hover .blog-card-arrow { opacity: 1; transform: translateX(0); }

		.blog-card-content { padding: 20px 20px 24px; display: flex; flex-direction: column; flex: 1; }
		.blog-card-title { font-family: var(--font-heading); font-size: 17px; font-weight: 700; line-height: 1.35; color: var(--ink); margin: 0 0 10px; transition: color var(--transition); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
		.blog-card:hover .blog-card-title { color: var(--sun-deep); }
		.blog-card-excerpt { font-size: 14px; line-height: 1.6; color: var(--ink-soft); flex: 1; margin: 0 0 16px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
		.blog-card-meta { display: flex; align-items: center; gap: 10px; font-size: 12px; color: var(--muted); padding-top: 12px; border-top: 1px solid var(--border); }
		.blog-card-date { font-weight: 600; }
		.blog-card-read { font-weight: 500; }

		/* ===== Empty State ===== */
		.blog-empty-state { grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 80px 24px; background: var(--white); border: 1px dashed var(--border); border-radius: var(--radius-xl); }
		.blog-empty-icon { width: 64px; height: 64px; color: var(--border); margin-bottom: 20px; }
		.blog-empty-title { font-family: var(--font-heading); font-size: 22px; font-weight: 700; color: var(--ink); margin: 0 0 8px; }
		.blog-empty-desc { color: var(--muted); margin: 0 0 24px; max-width: 360px; line-height: 1.6; }
		.blog-empty-btn { display: inline-flex; align-items: center; gap: 8px; background: var(--sun); color: #fff; font-size: 14px; font-weight: 700; padding: 12px 24px; border-radius: var(--radius-full); transition: background var(--transition), transform var(--transition), box-shadow var(--transition); box-shadow: var(--shadow-sun); }
		.blog-empty-btn:hover { background: var(--sun-deep); transform: translateY(-2px); box-shadow: var(--shadow-sun-lg); }

		/* ===== Pagination ===== */
		.blog-pagination { display: flex; align-items: center; justify-content: center; gap: 16px; margin-top: 48px; flex-wrap: wrap; }
		.blog-page-numbers { display: flex; align-items: center; gap: 8px; }
		.blog-page-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-width: 44px; height: 44px; padding: 0 16px; background: var(--white); color: var(--ink-soft); border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; font-weight: 600; text-decoration: none; transition: all var(--transition); }
		.blog-page-btn:hover { border-color: var(--sun); color: var(--sun); transform: translateY(-2px); box-shadow: var(--shadow-sm); }
		.blog-page-btn.active { background: var(--sun); color: #fff; border-color: var(--sun); box-shadow: var(--shadow-sun); }
		.blog-page-btn--prev, .blog-page-btn--next { padding: 0 20px; }
		.blog-page-ellipsis { color: var(--muted); padding: 0 4px; }
		@media (max-width: 480px) { .blog-page-btn--prev span, .blog-page-btn--next span { display: none; } }

		/* ===== Sidebar ===== */
		.blog-sidebar { position: sticky; top: 110px; }
		.blog-sidebar-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); margin-bottom: 20px; }
		.blog-sidebar-card:last-child { margin-bottom: 0; }
		.blog-sidebar-header { display: flex; align-items: center; gap: 10px; padding: 16px 20px; background: var(--cream); border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 700; color: var(--ink); text-transform: uppercase; letter-spacing: .03em; }
		.blog-sidebar-header svg { color: var(--sun); flex-shrink: 0; }
		.blog-sidebar-body { padding: 16px 20px; }

		/* Search */
		.blog-search-form { display: flex; gap: 8px; }
		.blog-search-form input { flex: 1; min-width: 0; height: 42px; padding: 0 14px; border: 1px solid var(--border); border-radius: var(--radius-md); background: var(--white); font-size: 14px; color: var(--ink); font-family: inherit; transition: border-color var(--transition), box-shadow var(--transition); }
		.blog-search-form input:focus { outline: none; border-color: var(--sun); box-shadow: 0 0 0 3px rgba(249,115,22,.12); }
		.blog-search-form input::placeholder { color: var(--muted); }
		.blog-search-form button { width: 42px; height: 42px; display: grid; place-items: center; background: var(--sun); color: #fff; border: 0; border-radius: var(--radius-md); cursor: pointer; transition: background var(--transition), transform var(--transition); }
		.blog-search-form button:hover { background: var(--sun-deep); transform: scale(1.05); }

		/* Popular */
		.blog-popular-item { display: flex; gap: 12px; text-decoration: none; color: inherit; border-radius: var(--radius-sm); padding: 8px; transition: background var(--transition); }
		.blog-popular-item:hover { background: var(--cream); }
		.blog-popular-img { flex: 0 0 64px; width: 64px; height: 48px; border-radius: var(--radius-sm); background-size: cover; background-position: center; background-color: var(--cream-2); }
		.blog-popular-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; }
		.blog-popular-title { font-size: 13.5px; font-weight: 600; line-height: 1.4; color: var(--ink); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; transition: color var(--transition); font-family: var(--font-heading); }
		.blog-popular-item:hover .blog-popular-title { color: var(--sun-deep); }
		.blog-popular-date { font-size: 11px; color: var(--muted); }

		/* Services */
		.blog-service-item { display: flex; gap: 12px; padding: 12px 0; text-decoration: none; color: inherit; border-bottom: 1px solid var(--border); transition: opacity var(--transition); align-items: flex-start; }
		.blog-service-item > div:last-child { flex: 1; min-width: 0; }
		.blog-service-item:last-child { border-bottom: none; padding-bottom: 0; }
		.blog-service-item:hover { opacity: .7; }
		.blog-service-icon { flex: 0 0 36px; width: 36px; height: 36px; display: grid; place-items: center; background: var(--cream-2); color: var(--sun); border-radius: var(--radius-sm); }
		.blog-service-title { font-size: 13px; font-weight: 600; color: var(--ink); line-height: 1.3; }
		.blog-service-desc { font-size: 11.5px; color: var(--muted); margin-top: 2px; line-height: 1.3; }
		.blog-service-all { display: inline-flex; align-items: center; gap: 6px; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border); font-size: 13px; font-weight: 700; color: var(--sun-deep); text-decoration: none; transition: color var(--transition); }
		.blog-service-all:hover { color: var(--sun); }
		.blog-service-all svg { transition: transform var(--transition); }
		.blog-service-all:hover svg { transform: translateX(3px); }

		/* CTA Card */
		.blog-sidebar-card--cta .blog-sidebar-body { text-align: center; padding: 24px 20px; }
		.blog-sidebar-card--cta h4 { margin: 0 0 8px; font-size: 16px; font-weight: 700; color: var(--ink); font-family: var(--font-heading); }
		.blog-sidebar-card--cta p { margin: 0 0 20px; font-size: 13px; color: var(--muted); line-height: 1.55; }
		.blog-sidebar-card--cta a { display: flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 10px 20px; border-radius: var(--radius-md); font-size: 13px; font-weight: 600; text-decoration: none; transition: all var(--transition); }
		.blog-sidebar-card--cta a:first-child { background: var(--ink); color: #fff; }
		.blog-sidebar-card--cta a:first-child:hover { background: #1a1a1a; transform: translateY(-1px); box-shadow: var(--shadow-sm); }
		.blog-sidebar-card--cta a:last-child { background: var(--sun); color: #fff; margin-top: 10px; }
		.blog-sidebar-card--cta a:last-child:hover { background: var(--sun-deep); transform: translateY(-1px); box-shadow: var(--shadow-sun); }

/* Newsletter Card */
		.blog-sidebar-card--newsletter { background: var(--ink); border-color: transparent; }
		.blog-sidebar-card--newsletter .blog-sidebar-header { background: transparent; border-color: rgba(255,255,255,.15); color: #fff; }
		.blog-sidebar-card--newsletter .blog-sidebar-header svg { color: var(--sun); }
		.blog-newsletter-desc { margin: 0 0 16px; font-size: 13px; color: rgba(255,255,255,.7); line-height: 1.55; }
		.blog-newsletter-form { display: flex; flex-direction: column; gap: 10px; }
		.blog-newsletter-form input { height: 42px; padding: 0 14px; border: 1px solid rgba(255,255,255,.2); border-radius: var(--radius-md); background: rgba(255,255,255,.08); color: #fff; font-size: 14px; font-family: inherit; transition: border-color var(--transition), background var(--transition); }
		.blog-newsletter-form input:focus { outline: none; border-color: var(--sun); background: rgba(255,255,255,.12); }
		.blog-newsletter-form input::placeholder { color: rgba(255,255,255,.4); }
		.blog-newsletter-btn { height: 42px; background: var(--ink); color: #fff; border: 1px solid var(--ink); border-radius: var(--radius-lg); font-size: 13px; font-weight: 700; cursor: pointer; transition: all var(--transition); }
		.blog-newsletter-btn:hover { background: #1a1a1a; border-color: #1a1a1a; transform: translateY(-1px); }
		.blog-newsletter-note { margin: 12px 0 0; font-size: 11px; color: rgba(255,255,255,.4); text-align: center; }
		.blog-newsletter-note a { color: var(--sun); text-decoration: underline; text-underline-offset: 2px; }

		/* ===== CTA Section ===== */
		.blog-cta-section { position: relative; padding: clamp(4rem, 6vw, 5rem) 0; background: var(--ink); overflow: hidden; }
		.blog-cta-bg { position: absolute; inset: 0; background: radial-gradient(ellipse at 30% 20%, rgba(249,115,22,.15) 0%, transparent 60%), radial-gradient(ellipse at 70% 80%, rgba(251,146,60,.1) 0%, transparent 50%); pointer-events: none; }
		.blog-cta-card { position: relative; z-index: 1; display: grid; grid-template-columns: 1fr auto; gap: 48px; align-items: center; background: linear-gradient(135deg, rgba(255,255,255,.03) 0%, transparent 100%); border: 1px solid rgba(255,255,255,.08); border-radius: var(--radius-xl); padding: clamp(32px, 4vw, 48px); backdrop-filter: blur(20px); }
		@media (max-width: 760px) { .blog-cta-card { grid-template-columns: 1fr; text-align: center; } .blog-cta-actions { justify-content: center; flex-wrap: wrap; } }
		.blog-cta-title { font-family: var(--font-heading); font-size: clamp(26px, 3.5vw, 36px); font-weight: 800; line-height: 1.2; color: #fff; margin: 0 0 12px; }
		.blog-cta-desc { font-size: 16px; line-height: 1.65; color: rgba(255,255,255,.7); margin: 0; max-width: 520px; }
		.blog-cta-actions { display: flex; gap: 16px; flex-wrap: wrap; }
		.blog-cta-btn { display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 14px 28px; border-radius: var(--radius-lg); font-size: 14px; font-weight: 700; text-decoration: none; transition: all var(--transition); white-space: nowrap; }
		.blog-cta-btn--primary { background: var(--ink); color: #fff; border: 1px solid var(--ink); }
		.blog-cta-btn--primary:hover { background: #1a1a1a; border-color: #1a1a1a; transform: translateY(-2px); box-shadow: var(--shadow-lg); }
		.blog-cta-btn--primary svg { transition: transform var(--transition); }
		.blog-cta-btn--primary:hover svg { transform: translateX(4px); }
		.blog-cta-btn--phone { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.25); }
		.blog-cta-btn--phone:hover { background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.4); transform: translateY(-1px); }
		.blog-cta-curve { position: absolute; bottom: 0; left: 0; width: 100%; height: 60px; pointer-events: none; z-index: 2; }

		/* ===== Breadcrumbs (shared) ===== */
		.fss-breadcrumbs { margin-bottom: 20px; }
		.fss-bc-list { display: flex; flex-wrap: wrap; gap: 6px; list-style: none; margin: 0; padding: 0; font-size: 13px; color: var(--muted); }
		.fss-bc-item { display: flex; align-items: center; }
		.fss-bc-item:not(:last-child)::after { content: "/"; margin-left: 6px; color: var(--border); }
		.fss-bc-link { color: var(--muted); text-decoration: none; transition: color var(--transition); }
		.fss-bc-link:hover { color: var(--sun); }
		.fss-bc-current { color: var(--ink); font-weight: 600; }

		/* ===== Responsive ===== */
		@media (max-width: 640px) {
			.blog-featured-link { grid-template-columns: 1fr; min-height: auto; }
			.blog-featured-media { aspect-ratio: 16/9; min-height: 220px; }
			.blog-featured-body { padding: 20px; justify-content: flex-start; }
			.blog-filters-bar { flex-direction: column; align-items: stretch; }
			.blog-filter { justify-content: center; }
			.blog-hero-stats { gap: 16px; }
			.blog-stat-divider { width: 80%; height: 1px; background: linear-gradient(90deg, transparent, var(--border), transparent); }
			.blog-cta-card { padding: 24px; }
			.blog-cta-actions { flex-direction: column; }
			.blog-cta-btn { width: 100%; }
		}
	</style>
</body>
</html>