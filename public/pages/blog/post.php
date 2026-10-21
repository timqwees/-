<?php
require_once __DIR__ . '/../../../app/bootstrap.php';
$posts = seo_blog_posts();
$slug = fss_param('post');
$post = $posts[$slug] ?? null;

if (!$post) {
	\App\Models\Router\Routes::error_404('/blog/' . $slug);
	return;
}

$h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$phone = (string)seo_config('phone_display');
$phone_tel = preg_replace('/\D/', '', (string)seo_config('phone'));
$service = seo_service($post['service_slug'] ?? '');
$articleUrl = rtrim((string)seo_config('base_url'), '/') . '/blog/' . $slug;
$today = date('c', strtotime($post['date']));
$updated = isset($post['updated']) ? date('c', strtotime($post['updated'])) : $today;
$base = rtrim((string)seo_config('base_url'), '/');
// Автор статьи — подразделение компании (реальные сотрудники не выдумываются),
// поэтому ссылка ведёт на страницу «О компании», а не на несуществующий якорь.
$authorId = $base . '/about';

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
	<title><?= $h(seo_clip($post['title'], 52)) ?> — Блог</title>
	<meta name="description" content="<?= $h(seo_clip($post['description'], 130)) ?> ☎ <?= $h($phone) ?>">
	<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
	<meta name="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
	<meta name="theme-color" content="#876b55">
	<link rel="shortcut icon" href="/public/assets/image/logo/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/public/assets/image/logo/favicon/apple-touch-icon.png" />
	<link rel="manifest" href="/manifest.json" />
	<?= seo_canonical_tags() ?>
	<?= seo_verify_tags() ?>
	<?= seo_og_tags($post['title'] . ' — Блог ФАВОРИТ-СТРОЙ-СНАБ', $post['description'], $post['og_image']) ?>
	<meta property="article:published_time" content="<?= $h($today) ?>">
	<meta property="article:modified_time" content="<?= $h($updated) ?>">
	<?php if (!empty($post['author'])): ?>
	<meta property="article:author" content="<?= $h($post['author']['name']) ?>">
	<?php endif; ?>
	<meta property="article:section" content="<?= $h($post['category_name'] ?? 'Блог') ?>">
	<?php foreach (array_slice((array)($post['tags'] ?? []), 0, 10) as $__tag): ?>
	<meta property="article:tag" content="<?= $h($__tag) ?>">
	<?php endforeach; ?>
	<link rel="preload" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>" as="style">
	<link rel="stylesheet" href="/public/assets/distCSS/style.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024">
	<noscript><link rel="stylesheet" href="/public/assets/vendor/fontawesome/all.min.css?v=20261024"></noscript>
	<link rel="stylesheet" href="/public/assets/distCSS/tailwind.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/premium.css?v=<?= seo_ver() ?>">
	<link rel="stylesheet" href="/public/assets/distCSS/article.css?v=<?= seo_ver() ?>">
	<style>
		/* Связанная услуга — блок в сайдбаре статьи (связка blog → services) */
		.article-sidebar-card--service .article-sidebar-body {
			display: flex;
			flex-direction: column;
			gap: 12px;
			align-items: flex-start;
		}

		.article-service-link {
			font-size: 16px;
			font-weight: 600;
			line-height: 1.3;
			color: inherit;
			text-decoration: none;
		}

		.article-service-link:hover {
			text-decoration: underline;
			text-underline-offset: 3px;
		}

		.article-service-note {
			margin: 0;
			font-size: 12.5px;
			line-height: 1.5;
			color: rgba(23, 19, 16, .62);
		}
	</style>
	<?php include_once __DIR__ . '/../../components/sheoma.php'; ?>
	<?= seo_analytics() ?>
	<?= seo_utm_script() ?>
	<?= seo_goals_script() ?>
</head>

<body class="blog-post-page" data-page="inner">
	<?php include_once __DIR__ . '/../../components/header.php'; ?>

	<div id="reading-bar"></div>

	<main class="fss-article-layout">
		<div class="fss-blog-container">
			<div class="article-layout">

				<!-- Article Hero + Content -->
				<div class="article-main">

					<section class="article-hero">
						<nav class="article-breadcrumbs">
							<a href="/" title="Главная">Главная</a>
							<span class="article-bc-sep">→</span>
							<a href="/blog" title="Блог">Блог</a>
							<span class="article-bc-sep">→</span>
							<span><?= $h($post['category_name']) ?></span>
						</nav>
							<div class="article-meta-top">
								<div class="article-meta-left">
									<?php if (!empty($post['author'])): ?>
									<div class="article-author-info">
										<img src="<?= $h($post['author']['photo']) ?>" alt="<?= $h($post['author']['name']) ?>" class="article-author-avatar" loading="lazy" width="36" height="36" title="<?= $h($post['author']['name']) ?> — <?= $h($post['author']['role']) ?>">
										<div class="article-author-details">
											<span class="article-author-name"><?= $h($post['author']['name']) ?></span>
											<span class="article-author-role"><?= $h($post['author']['role']) ?></span>
										</div>
									</div>
									<?php else: ?>
									<span class="article-author-label">Автор:</span>
									<span class="article-author-name">ФАВОРИТ-СТРОЙ-СНАБ</span>
									<?php endif; ?>
								</div>
								<div class="article-meta-right">
									<span class="article-cat"><?= $h($post['category_name']) ?></span>
									<time class="article-date" datetime="<?= $today ?>"><?= date('d.m.Y', strtotime($post['date'])) ?></time>
								</div>
							</div>
							<h1 class="article-title" itemprop="headline"><?= $h($post['title']) ?></h1>
							<p class="article-desc" itemprop="description"><?= $h($post['description']) ?></p>
						</section>

					<article class="article-body" itemprop="articleBody">
						<meta itemprop="inLanguage" content="ru-RU">
						<meta itemprop="datePublished" content="<?= $h($today) ?>">
						<meta itemprop="dateModified" content="<?= $h($updated) ?>">
						<?php if (!empty($post['author'])): ?>
						<meta itemprop="author" content="<?= $h($post['author']['name']) ?>">
						<meta itemprop="publisher" content="ФАВОРИТ-СТРОЙ-СНАБ">
						<link itemprop="author" href="<?= $h($authorId) ?>" />
						<?php else: ?>
						<meta itemprop="author" content="ФАВОРИТ-СТРОЙ-СНАБ">
						<?php endif; ?>
						<link itemprop="mainEntityOfPage" href="<?= $articleUrl ?>">

						<div class="article-img" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
							<img src="<?= $h((string)$post['og_image']) ?>"<?= seo_img_attrs((string)$post['og_image']) ?> alt="<?= $h($post['title']) ?>" itemprop="contentUrl" loading="eager" fetchpriority="high" decoding="async" title="<?= $h($post['title']) ?>">
						</div>

						<nav class="article-toc" id="article-toc" aria-label="Содержание статьи" hidden>
							<div class="article-toc-title"><i class="fa-solid fa-list-ul"></i> Содержание</div>
							<ol></ol>
						</nav>

						<?php
						$contentFile = __DIR__ . '/content/' . $post['content_id'] . '.php';
						file_exists($contentFile) ? include $contentFile : print('<p>' . $h($post['content'] ?? '') . '</p>');
						?>
					</article>

					<!-- Author Bio / E-E-A-T -->
					<?php if (!empty($post['author'])): ?>
					<section class="author-bio" style="margin-top:48px;padding:28px;background:#f8f4ef;border:1px solid #F5E6D3;border-radius:24px">
						<div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
							<img src="<?= $h($post['author']['photo']) ?>" alt="<?= $h($post['author']['name']) ?>" title="<?= $h($post['author']['name']) ?> — <?= $h($post['author']['role']) ?>" class="article-author-avatar" loading="lazy" width="64" height="64" style="width:64px;height:64px;border-radius:50%;object-fit:cover">
							<div>
								<div style="font-size:18px;font-weight:800;color:#171310"><?= $h($post['author']['name']) ?></div>
								<div style="font-size:14px;color:#6d5744;font-weight:600"><?= $h($post['author']['role']) ?></div>
								<div style="font-size:13px;color:#78716C;margin-top:4px"><i class="fa-solid fa-medal" style="color:#876b55;margin-right:6px"></i>Опыт: <?= $h($post['author']['experience']) ?></div>
							</div>
						</div>
						<p style="margin:0;font-size:15px;line-height:1.7;color:#44403C">Статья подготовлена экспертом ООО «Фаворит-Строй-Снаб» — компании с <?= htmlspecialchars((string)seo_config('market_years')) ?>-летним опытом строительства БВЗ, капремонта школ и поставки оборудования госзаказчикам (Росгвардия, Минпросвещение РФ). <?= htmlspecialchars((string)seo_config('objects')) ?> сданных объектов, гарантия <?= htmlspecialchars((string)seo_config('guarantee_years')) ?> лет, работа по 44-ФЗ/223-ФЗ.</p>
					</section>
					<?php endif; ?>

					<?php if ($service): ?>
					<aside class="article-expert" itemscope itemtype="https://schema.org/Service">
						<meta itemprop="name" content="Консультация по услуге <?= $h($service['title']) ?> — ФАВОРИТ-СТРОЙ-СНАБ">
						<div class="article-expert__badge">⚠️ Не рискуйте — получите правильное решение</div>
						<h3 class="article-expert__title">Сомневаетесь в выборе? Получите консультацию эксперта</h3>
						<p class="article-expert__text">Ошибки при выборе материалов и технологий стоят тысячи рублей. <strong>ФАВОРИТ-СТРОЙ-СНАБ</strong> предлагает: бесплатную консультацию, расчёт сметы и подбор оптимального решения для вашего проекта.</p>
						<ol class="article-expert__steps">
							<li><strong>Оставьте заявку</strong> — опишите ваш объект и задачи.</li>
							<li><strong>Получите расчёт</strong> — наш эксперт подготовит смету за 15 минут.</li>
							<li><strong>Реализуйте проект</strong> — мы поможем с доставкой и монтажом под ключ.</li>
						</ol>
						<div class="article-expert__btns">
							<a href="/#contact" title="Рассчитать смету" class="article-expert__btn article-expert__btn--order">Рассчитать смету →</a>
							<a href="tel:+<?= $phone_tel ?>" title="Позвонить" class="article-expert__btn article-expert__btn--phone">Позвонить: <?= $h($phone) ?></a>
						</div>
						<p class="article-expert__note">Работаем по всей России · Гарантия 50+ лет · СРО</p>
					</aside>
					<?php endif ?>

					<div class="article-tags">
						<?php foreach ((array)($post['tags'] ?? []) as $tag): ?>
						<span class="article-tag" itemprop="keywords"><?= $h($tag) ?></span>
						<?php endforeach ?>
					</div>

					<div class="article-cta">
						<h3 class="article-cta-title">Нужна помощь с проектом?</h3>
						<p class="article-cta-desc">Оставьте заявку — рассчитаем стоимость, сроки и подберём оптимальное решение для вашего объекта.</p>
						<div class="article-cta-btns">
						<a href="/#contact" title="Получить расчёт" class="article-cta-btn">Получить расчёт</a>
						<a href="tel:+<?= $phone_tel ?>" title="Позвонить: <?= $h($phone) ?>" class="article-cta-btn article-cta-btn--phone">Позвонить</a>
						<a href="/#contact" title="Написать нам" class="article-cta-btn article-cta-btn--outline">Написать нам</a>
						</div>
					</div>

				</div>

				<!-- Sidebar -->
				<aside class="article-sidebar">
					<?php if (!empty($service) && !empty($service['title'])): ?>
					<div class="article-sidebar-card article-sidebar-card--service">
						<div class="article-sidebar-header">Связанная услуга</div>
						<div class="article-sidebar-body">
							<a class="article-service-link" href="/services/<?= htmlspecialchars((string)($post['service_slug'] ?? ''), ENT_QUOTES) ?>" title="<?= $h($service['title']) ?>"><?= $h($service['title']) ?></a>
							<p class="article-service-note">Работы по этой услуге: <?= $h((string)($service['service_type'] ?? '')) ?><?php if (!empty($service['price_from'])): ?> · от <?= number_format((float)$service['price_from'], 0, ',', ' ') ?> ₽/м²<?php endif; ?></p>
							<a class="article-sidebar-card--cta-btn article-sidebar-card--cta-btn--primary" href="/services/<?= htmlspecialchars((string)($post['service_slug'] ?? ''), ENT_QUOTES) ?>" title="Перейти к услуге">Перейти к услуге</a>
						</div>
					</div>
					<?php endif ?>

					<?php if (!empty($related)): ?>
					<div class="article-sidebar-card">
						<div class="article-sidebar-header">Читайте также</div>
						<div class="article-sidebar-body">
							<?php foreach ($related as [$rSlug, $rPost]): ?>
							<a class="article-popular-item" title="<?= htmlspecialchars($rPost['title'], ENT_QUOTES) ?>" href="/blog/<?= htmlspecialchars((string)$rSlug, ENT_QUOTES) ?>">
								<div class="article-popular-img" style="background-image:url('<?= htmlspecialchars((string)$rPost['og_image'], ENT_QUOTES) ?>')"></div>
								<div class="article-popular-body">
									<span class="article-popular-title"><?= $h($rPost['title']) ?></span>
									<span class="article-popular-date"><?= date('d.m.Y', strtotime($rPost['date'])) ?></span>
								</div>
							</a>
							<?php endforeach ?>
						</div>
					</div>
					<?php endif ?>

					<div class="article-sidebar-card article-sidebar-card--cta">
						<div class="article-sidebar-body">
							<h4>Бесплатная консультация</h4>
							<p>Рассчитаем смету, подберём материалы и определим сроки для вашего проекта.</p>
							<a href="tel:+<?= $phone_tel ?>" title="Позвонить" class="article-sidebar-card--cta-btn article-sidebar-card--cta-btn--phone">Позвонить: <?= $h($phone) ?></a>
							<a href="/#contact" title="Получить расчёт" class="article-sidebar-card--cta-btn article-sidebar-card--cta-btn--primary">Получить расчёт</a>
						</div>
					</div>
				</aside>
			</div>
		</div>
	</main>

	<button id="back-top" aria-label="Наверх">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
			<path d="M18 15l-6-6-6 6" />
		</svg>
	</button>

	<?php include_once __DIR__ . '/../../components/footer.php'; ?>

	<script>
	document.addEventListener('DOMContentLoaded', function(){
		var bar = document.getElementById('reading-bar');
		var back = document.getElementById('back-top');
		if (back) {
			back.addEventListener('click', function(){ window.scrollTo({top:0,behavior:'smooth'}); });
		}
		var img = document.querySelector('.article-img img');
		if (img) { img.classList.add('loaded'); }
		var toc = document.getElementById('article-toc');
		var body = document.querySelector('.article-body');
		if (toc && body) {
			var heads = body.querySelectorAll('h2, h3');
			var list = toc.querySelector('ol');
			var slug = function(t){ return 'toc-' + t.toLowerCase().replace(/[^a-zа-я0-9\s-]/gi,'').trim().replace(/\s+/g,'-').slice(0,60); };
			var used = {};
			heads.forEach(function(h){
				var text = h.textContent.trim();
				if (!text) return;
				var id = h.id || slug(text);
				while (used[id]) { id += '-1'; }
				used[id] = true;
				h.id = id;
				var li = document.createElement('li');
				var a = document.createElement('a');
				a.href = '#' + id;
				a.textContent = text;
				if (h.tagName === 'H3') a.className = 'article-toc-h3';
				li.appendChild(a);
				list.appendChild(li);
			});
			if (list.children.length > 2) toc.hidden = false;
		}
		var ticking = false;
		window.addEventListener('scroll', function(){
			if (!ticking) {
				requestAnimationFrame(function(){
					var dh = document.documentElement.scrollHeight - window.innerHeight;
					var pct = dh > 0 ? (window.scrollY / dh) * 100 : 0;
					if (bar) bar.style.width = pct + '%';
					if (back) back.classList.toggle('show', window.scrollY > 400);
					ticking = false;
				});
			}
			ticking = true;
		});
	});
	</script>
</body>

</html>
