<?php
/**
 * Schema.org + хлебные крошки.
 * Всё собирается из assets/PHP/config.php + public/data/services.php,
 * поэтому цифры, телефоны и адрес одинаковы во всей выдаче и фидах.
 */

$graph = seo_organization_jsonld();

// Speakable для голосовых ассистентов (Google Assistant, Алиса)
$base = rtrim((string)seo_config('base_url'), '/');
$graph[] = [
	'@type' => 'SpeakableSpecification',
	'cssSelector' => ['.article-body', '.blog-card-excerpt', '.service-description', '.faq-a'],
	'xpath' => ['//article//p', '//section[contains(@class, "faq")]//p'],
];

$bc = seo_breadcrumbs_jsonld();
if ($bc) {
	$graph[] = $bc;
}

$reviews = seo_reviews_jsonld();
foreach ($reviews as $node) {
	$graph[] = $node;
}

// Блог: BlogPosting для конкретной статьи — в блоке ниже (единый разбор URL),
// здесь только заготовка, чтобы не дублировать BlogPosting в @graph.

// Service + Offer + FAQPage + Review — на странице услуги и её гео-посадочных
if (preg_match('#^/services/([^/]+)(?:/([^/]+))?$#', seo_url(), $m)) {
	$svc = seo_service_jsonld($m[1]);
	if ($svc) {
		// Гео-посадочная: уточняем url и areaServed под конкретный город/район
		if (!empty($m[2])) {
			$gg = function_exists('seo_geo') ? seo_geo() : ['geo' => []];
			$gname = (string)($gg['geo'][$m[2]]['name'] ?? $m[2]);
			$svc['url'] = seo_canonical();
			$svc['@id'] = seo_canonical() . '#service';
			$svc['areaServed'] = ['@type' => 'City', 'name' => $gname];
			if (isset($svc['availableChannel']['serviceUrl'])) {
				$svc['availableChannel']['serviceUrl'] = seo_canonical();
			}
			if (isset($svc['offers']['url'])) {
				$svc['offers']['url'] = seo_canonical();
			}
		}
		$graph[] = $svc;
	}
	$s = seo_service($m[1]);
	// На гео-странице к общим вопросам услуги добавляем локальные вопросы города —
	// разметка FAQPage совпадает с видимым текстом (нет расхождения разметки и контента).
	$faqList = array_values((array)($s['faq'] ?? []));
	if (!empty($m[2])) {
		$gg = function_exists('seo_geo') ? seo_geo() : ['geo' => []];
		$faqList = array_merge($faqList, array_values((array)($gg['geo'][$m[2]]['faq'] ?? [])));
	}
	if ($faqList) {
		$graph[] = [
			'@type' => 'FAQPage',
			'@id' => seo_canonical() . '#faq',
			'mainEntity' => array_map(fn($qa) => [
				'@type' => 'Question',
				'name' => $qa[0],
				'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
			], $faqList),
		];
	}
	// Review для конкретной услуги (агрегатное + возможность индивидуальных)
	if (!empty($s['reviews'])) {
		$graph[] = [
			'@type' => 'Review',
			'@id' => seo_canonical() . '#reviews',
			'itemReviewed' => ['@type' => 'Service', 'name' => $s['title'], '@id' => $svc['@id'] ?? seo_canonical() . '#service'],
			'reviewRating' => ['@type' => 'Rating', 'ratingValue' => $s['rating_value'] ?? 4.9, 'bestRating' => 5, 'worstRating' => 1],
			'author' => ['@type' => 'Organization', 'name' => 'Клиенты ФСС'],
			'datePublished' => date('c'),
			'reviewBody' => 'Отзывы клиентов заказавших ' . $s['title'] . ' у ФАВОРИТ-СТРОЙ-СНАБ',
		];
	}
}

// Главная — FAQPage из тех же данных (единый источник, без расхождений)
if (seo_url() === '/') {
	$faq = [];
	foreach (seo_services() as $slug => $s) {
		foreach ($s['faq'] as $qa) {
			$faq[] = $qa;
		}
	}
	$faq = array_slice($faq, 0, 12);
	if ($faq) {
		$graph[] = [
			'@type' => 'FAQPage',
			'@id' => seo_canonical() . '#faq',
			'mainEntity' => array_map(fn($qa) => [
				'@type' => 'Question',
				'name' => $qa[0],
				'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
			], $faq),
		];
	}
}

// Legal pages — FAQPage для политики и согласия
if (in_array(seo_url(), ['/politika', '/soglasie'])) {
	$legalFaq = [
		['Какие персональные данные вы собираете?', 'Мы собираем имя, телефон, email и сообщение, которые вы freiwillно вводите в формах обратной связи.'],
		['Как вы используете мои данные?', 'Только для связи с вами, расчёта сметы и оформления договора. Не передаём третьим лицам без вашего согласия.'],
		['Сколько храните данные?', 'До выполнения цели сбора или до отзыва согласия, но не более 3 лет после последнего контакта.'],
		['Как отозвать согласие?', 'Напишите на ' . seo_config('email') . ' или позвоните ' . seo_config('phone_display') . '. Удалим данные в течение 30 дней.'],
		['Есть ли передача третьим лицам?', 'Только по требованию органов власти в рамках закона. Не продаём и не арендуем базы.'],
	];
	$graph[] = [
		'@type' => 'FAQPage',
		'@id' => seo_canonical() . '#legal-faq',
		'mainEntity' => array_map(fn($qa) => [
			'@type' => 'Question',
			'name' => $qa[0],
			'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
		], $legalFaq),
	];
}

// Speakable для legal страниц
if (in_array(seo_url(), ['/politika', '/soglasie'])) {
	$graph[] = [
		'@type' => 'SpeakableSpecification',
		'cssSelector' => ['.pg-h1', '.text-gray-700', 'ul.list-disc p'],
		'xpath' => ['//h1', '//section//p', '//ul//li'],
	];
}

// Хаб услуг — каталог услуг для AI-выдачи + Dataset
if (seo_url() === '/uslugi') {
	$services = seo_services();
	$graph[] = [
		'@type' => 'OfferCatalog',
		'@id' => seo_canonical() . '#catalog',
		'name' => 'Услуги ФАВОРИТ-СТРОЙ-СНАБ',
		'itemListElement' => array_values(array_map(fn($slug, $s) => [
			'@type' => 'Offer',
			'itemOffered' => ['@type' => 'Service', 'name' => $s['title'], 'url' => rtrim((string)seo_config('base_url'), '/') . '/services/' . $slug],
			'priceCurrency' => 'RUB',
			'availability' => 'https://schema.org/InStock',
		] + (!empty($s['price_from']) ? ['price' => $s['price_from']] : []), array_keys($services), array_values($services))),
	];
	// Dataset для каталога услуг
	$graph[] = [
		'@type' => 'Dataset',
		'@id' => seo_canonical() . '#services-dataset',
		'name' => 'Каталог услуг ФАВОРИТ-СТРОЙ-СНАБ',
		'description' => 'Полный каталог услуг по строительству БВЗ, капремонту, спецтехнике и грузоперевозкам',
		'url' => seo_canonical(),
		'sameAs' => [$base . '/sitemap.xml'],
		'keywords' => implode(', ', array_keys($services)),
		'variableMeasured' => ['Service', 'Price', 'Duration', 'Region'],
		'license' => 'https://creativecommons.org/licenses/by/4.0/',
	];
}

// Хаб блога — BlogPosting для каждого поста; статья — BlogPosting + Speakable
if (seo_url() === '/blog') {
	// Список статей: BlogPosting для каждой записи (полезно AI-краулерам)
	$blogList = seo_blog_jsonld();
	if (is_array($blogList) && $blogList !== []) {
		foreach ($blogList as $bpNode) {
			if (is_array($bpNode)) {
				$graph[] = $bpNode;
			}
		}
	}
} elseif (preg_match('#^/blog/([^/]+)$#', seo_url(), $m)) {
	$bp = seo_blog_jsonld($m[1]);
	if ($bp) {
		$graph[] = $bp;
		// Speakable для статьи
		$graph[] = [
			'@type' => 'SpeakableSpecification',
			'cssSelector' => ['.article-body', '.article-toc', '.article-expert__text'],
			'xpath' => ['//article[contains(@class, "article-body")]//p', '//ol[contains(@class, "article-toc")]//li'],
		];
	}
}

// Главная — VideoObject для hero-видео
if (seo_url() === '/') {
	$base = rtrim((string)seo_config('base_url'), '/');
	$videoRel = '/public/assets/image/banner/dostavka.MP4';
	$videoPath = dirname(__DIR__, 2) . $videoRel;
	if (is_file($videoPath)) {
		$duration = 'PT30S'; // fallback
		// Попытка получить реальную длительность через ffprobe (если есть)
		$ffprobe = @shell_exec('which ffprobe');
		if ($ffprobe) {
			$out = @shell_exec(escapeshellcmd($ffprobe) . ' -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($videoPath));
			if ($out && is_numeric(trim($out))) {
				$sec = (int)round(trim($out));
				$duration = 'PT' . $sec . 'S';
			}
		}
		$graph[] = [
			'@type' => 'VideoObject',
			'@id' => $base . '#hero-video',
			'name' => 'Быстровозводимые здания ФСС — строим то, что ведёт вас вперёд',
			'description' => 'Видео-презентация ФАВОРИТ-СТРОЙ-СНАБ: производство и монтаж БВЗ под ключ, склады, ангары, АБК, общежития, капремонт. ' . seo_config('objects') . ' объектов, с ' . seo_config('founded_market') . ' года на рынке.',
			'thumbnailUrl' => $base . '/public/assets/image/og/og-home-1200x630.jpg',
			'uploadDate' => date('c', (int)@filemtime(dirname(__DIR__, 2) . '/public/pages/home.php')),
			'contentUrl' => $base . $videoRel,
			'embedUrl' => $base . '/',
			'duration' => $duration,
			'encodingFormat' => 'video/mp4',
			'publisher' => ['@id' => $base . '#organization'],
		];
	}
}

// Speakable для главной
if (seo_url() === '/') {
	$graph[] = [
		'@type' => 'SpeakableSpecification',
		'cssSelector' => ['.hero-title', '.svh-h1', '.svh-lead', '.svh-eyebrow', '.stat-lbl', '.stat-num'],
		'xpath' => ['//h1', '//section[contains(@class, "hero")]//p', '//div[contains(@class, "stat")]'],
	];
}

// Страницы поиска — SearchResultsPage (в т.ч. для AI-краулеров)
if (in_array(seo_url(), ['/search', '/search/blog'], true)) {
	$graph[] = [
		'@type' => 'SearchResultsPage',
		'@id' => seo_canonical() . '#search',
		'name' => seo_url() === '/search' ? 'Поиск по сайту' : 'Поиск по блогу',
		'url' => seo_canonical(),
		'isPartOf' => ['@id' => $base . '#website'],
		'about' => ['@id' => $base . '#organization'],
	];
}

echo seo_jsonld($graph);
