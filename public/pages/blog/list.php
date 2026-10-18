<?php require_once __DIR__ . '/../../../app/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
	$posts = seo_blog_posts();
	$count = count($posts);
	$today = date('Y-m-d');
	$title = 'Блог — экспертные статьи о строительстве БВЗ | ФАВОРИТ-СТРОЙ-СНАБ';
	$description = 'Экспертные статьи о строительстве быстровозводимых зданий, материалах, сроках, ценах и рекомендациях от ФАВОРИТ-СТРОЙ-СНАБ. ' . $count . ' практических материалов.';
	?>
	<title><?= $title ?></title>
	<meta name="description" content="<?= $description ?> ☎ <?= htmlspecialchars((string)seo_config('phone_display')) ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#F97316">
	<link rel="icon" type="image/svg+xml" href="/public/assets/image/logo/favicon.svg" />
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/apple-touch-icon.png" />
	<link rel="manifest" href="/public/assets/image/logo/site.webmanifest" />
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>
	<?= seo_og_tags($title, $description, '/public/assets/image/og/og-blog-1200x630.jpg') ?>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>" as="style">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=<?= seo_ver() ?>">
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<script defer src="/public/assets/distJS/main.js?v=<?= seo_ver() ?>"></script>
</head>

<body>
	<?php include_once __DIR__ . '/../../components/header.php'; ?>
	<?= seo_breadcrumbs_html() ?>
	<main class="container">
		<div class="fss-blog-header">
			<h1 class="page-title">Блог экспертов ФАВОРИТ-СТРОЙ-СНАБ</h1>
			<p class="fss-blog-desc">Практические статьи о строительстве быстровозводимых зданий, материалах, сроках и ценах. <?= $count ?> материалов с реальными кейсами.</p>
		</div>
		<div class="fss-blog-grid">
			<?php foreach ($posts as $slug => $p): ?>
			<article class="fss-blog-card">
				<a href="/blog/<?= $slug ?>" class="fss-blog-img-link">
					<img src="<?= $p['og_image'] ?>" alt="<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>" class="fss-blog-img" loading="lazy" decoding="async" onerror="this.style.display='none'">
				</a>
				<div class="fss-blog-content">
					<div class="fss-blog-meta">
						<span class="fss-blog-cat"><?= htmlspecialchars($p['category_name'], ENT_QUOTES) ?></span>
						<time datetime="<?= $p['date'] ?>"><?= date('d.m.Y', strtotime($p['date'])) ?></time>
						<span class="fss-blog-read">~<?= $p['read_minutes'] ?> мин</span>
					</div>
					<h2 class="fss-blog-title"><a href="/blog/<?= $slug ?>"><?= htmlspecialchars($p['title'], ENT_QUOTES) ?></a></h2>
					<p class="fss-blog-desc-text"><?= htmlspecialchars($p['description'], ENT_QUOTES) ?></p>
					<div class="fss-blog-tags">
						<?php foreach ($p['tags'] as $tag): ?>
						<a href="/blog?tag=<?= rawurlencode($tag) ?>" class="fss-blog-tag">#<?= htmlspecialchars($tag, ENT_QUOTES) ?></a>
						<?php endforeach ?>
					</div>
				</div>
			</article>
			<?php endforeach ?>
		</div>
	</main>
	<?php include_once __DIR__ . '/../../components/footer.php'; ?>
</body>

</html>
