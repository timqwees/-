<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
$posts = seo_blog_posts();
$slug = $_GET['post'] ?? '';
$post = $posts[$slug] ?? null;

if (!$post) {
	\App\Models\Router\Routes::error_404('/blog/' . $slug);
	return;
}

$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$phone = (string)seo_config('phone_display');
$service = seo_service($post['service_slug'] ?? '');
$articleUrl = rtrim(seo_config('base_url'), '/') . '/blog/' . $slug;
$today = date('c', strtotime($post['date']));

// Related posts (same category, excluding current)
$related = [];
foreach ($posts as $s => $p) {
	if ($s !== $slug && ($p['category_slug'] ?? '') === ($post['category_slug'] ?? '')) {
		$related[] = [$s, $p];
		if (count($related) >= 4) break;
	}
}
if (count($related) < 4) {
	foreach ($posts as $s => $p) {
		if ($s !== $slug && !in_array([$s, $p], $related)) {
			$related[] = [$s, $p];
			if (count($related) >= 4) break;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $h($post['title']) ?> — Блог ФАВОРИТ-СТРОЙ-СНАБ</title>
	<meta name="description" content="<?= $h($post['description']) ?> ☎ <?= $h($phone) ?>">
	<meta name="keywords" content="<?= $h(implode(', ', $post['tags'])) ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/apple-touch-icon.png" />
	<link rel="manifest" href="/public/assets/image/logo/site.webmanifest" />
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>
	<?= seo_og_tags($post['title'] . ' — Блог ФАВОРИТ-СТРОЙ-СНАБ', $post['description'], $post['og_image']) ?>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>" as="style">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=<?= seo_ver() ?>">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<script defer src="/public/assets/distJS/main.js?v=<?= seo_ver() ?>"></script>
</head>

<body class="blog-post-page">
	<?php include_once __DIR__ . '/../../components/header.php'; ?>
	<?= seo_breadcrumbs_html() ?>

	<div id="reading-bar"></div>

	<main class="fss-article-body-wrap">
		<div class="fss-blog-container">
			<article class="fss-article" itemscope itemtype="https://schema.org/BlogPosting">

				<header class="fss-article-head">
					<div class="fss-article-meta">
						<span class="fss-blog-cat"><?= $h($post['category_name']) ?></span>
						<time datetime="<?= $today ?>"><?= date('d.m.Y', strtotime($post['date'])) ?></time>
						<span class="fss-article-read">~<?= $post['read_minutes'] ?> мин чтения</span>
					</div>
					<h1 class="fss-article-title" itemprop="headline"><?= $h($post['title']) ?></h1>
					<p class="fss-article-desc" itemprop="description"><?= $h($post['description']) ?></p>
					<div class="fss-article-tags">
						<?php foreach ($post['tags'] as $tag): ?>
						<span class="fss-article-tag" itemprop="keywords"><?= $h($tag) ?></span>
						<?php endforeach ?>
					</div>
					<meta itemprop="datePublished" content="<?= $today ?>">
					<meta itemprop="dateModified" content="<?= $today ?>">
					<meta itemprop="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
					<meta itemprop="inLanguage" content="ru-RU">
					<link itemprop="mainEntityOfPage" href="<?= $articleUrl ?>">
					<meta itemprop="image" content="<?= $post['og_image'] ?>">
				</header>

				<div class="fss-article-img" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
					<img src="<?= $post['og_image'] ?>" alt="<?= $h($post['title']) ?>" itemprop="contentUrl" loading="eager" decoding="async">
				</div>

				<div class="fss-article-content" itemprop="articleBody">
					<?php
					$contentFile = __DIR__ . '/content/' . $post['content_id'] . '.php';
					file_exists($contentFile) ? include $contentFile : print('<p>' . $h($post['content'] ?? '') . '</p>');
					?>
				</div>

				<?php if ($service): ?>
				<aside class="fss-article-aside" itemscope itemtype="https://schema.org/Service">
					<meta itemprop="serviceType" content="Строительство БВЗ">
					<meta itemprop="provider" content="<?= htmlspecialchars(seo_config('brand'), ENT_QUOTES) ?>">
					<h3>Связанная услуга</h3>
					<a href="/services/<?= $post['service_slug'] ?>" class="fss-service-link">
						<strong itemprop="name"><?= $h($service['title']) ?></strong>
						<span itemprop="description"><?= $h($service['description']) ?></span>
					</a>
				</aside>
				<?php endif ?>

				<div class="fss-article-tags-bottom">
					<span>Теги:</span>
					<?php foreach ($post['tags'] as $tag): ?>
					<a href="/blog?tag=<?= rawurlencode($tag) ?>" class="fss-article-tag-link">#<?= $h($tag) ?></a>
					<?php endforeach ?>
				</div>

				<?php if (!empty($related)): ?>
				<aside class="fss-article-related">
					<h3>Читайте также</h3>
					<div class="fss-related-grid">
						<?php foreach ($related as [$rSlug, $rPost]): ?>
						<article class="fss-related-item">
							<a href="/blog/<?= $rSlug ?>">
								<img src="<?= $rPost['og_image'] ?>" alt="<?= $h($rPost['title']) ?>" loading="lazy" decoding="async">
								<div class="fss-related-info">
									<span class="fss-related-cat"><?= $h($rPost['category_name']) ?></span>
									<h4><?= $h($rPost['title']) ?></h4>
									<time datetime="<?= $rPost['date'] ?>"><?= date('d.m.Y', strtotime($rPost['date'])) ?></time>
								</div>
							</a>
						</article>
						<?php endforeach ?>
					</div>
				</aside>
				<?php endif ?>

				<div class="fss-article-cta">
					<h3>Нужен объект под ключ?</h3>
					<p>Оставьте заявку — рассчитаем смету за 15 минут и фиксируем цену в договоре. Работаем по всей России.</p>
					<div class="fss-cta-buttons">
						<a href="/#contact" class="btn btn-brand">Рассчитать смету</a>
						<a href="tel:<?= seo_config('phone_tel') ?>" class="btn btn-phone"><?= $h($phone) ?></a>
					</div>
				</div>

			</article>
		</div>
	</main>

	<?php include_once __DIR__ . '/../../components/footer.php'; ?>

	<script>
	document.addEventListener('DOMContentLoaded', function(){
		var bar = document.getElementById('reading-bar');
		if (!bar) return;
		var ticking = false;
		window.addEventListener('scroll', function(){
			if (!ticking) {
				requestAnimationFrame(function(){
					var dh = document.documentElement.scrollHeight - window.innerHeight;
					var pct = dh > 0 ? (window.scrollY / dh) * 100 : 0;
					bar.style.width = pct + '%';
					ticking = false;
				});
			}
			ticking = true;
		});
		var imgs = document.querySelectorAll('.fss-article-img img, .fss-article-content img');
		imgs.forEach(function(im){
			if (im.complete) return;
			im.addEventListener('load', function(){ im.style.opacity='1'; });
		});
	});
	</script>
</body>

</html>
