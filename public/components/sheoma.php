<?php
/**
 * Schema.org + хлебные крошки.
 * Всё собирается из assets/PHP/config.php + public/data/services.php,
 * поэтому цифры, телефоны и адрес одинаковы во всей выдаче и фидах.
 */

$graph = seo_organization_jsonld();

$bc = seo_breadcrumbs_jsonld();
if ($bc) {
	$graph[] = $bc;
}

// Service + Offer + FAQPage — только на странице самой услуги
if (preg_match('#^/services/([^/]+)$#', seo_url(), $m)) {
	$svc = seo_service_jsonld($m[1]);
	if ($svc) {
		$graph[] = $svc;
	}
	$s = seo_service($m[1]);
	if (!empty($s['faq'])) {
		$graph[] = [
			'@type' => 'FAQPage',
			'@id' => seo_canonical() . '#faq',
			'mainEntity' => array_map(fn($qa) => [
				'@type' => 'Question',
				'name' => $qa[0],
				'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
			], $s['faq']),
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

// Хаб услуг — каталог услуг для AI-выдачи
if (seo_url() === '/uslugi') {
	$graph[] = [
		'@type' => 'OfferCatalog',
		'@id' => seo_canonical() . '#catalog',
		'name' => 'Услуги ФАВОРИТ-СТРОЙ-СНАБ',
		'itemListElement' => array_values(array_map(fn($slug, $s) => [
			'@type' => 'Offer',
			'itemOffered' => ['@type' => 'Service', 'name' => $s['title'], 'url' => rtrim(seo_config('base_url'), '/') . '/services/' . $slug],
			'priceCurrency' => 'RUB',
			'availability' => 'https://schema.org/InStock',
		] + (!empty($s['price_from']) ? ['price' => $s['price_from']] : []), array_keys(seo_services()), array_values(seo_services()))),
	];
}

echo seo_jsonld($graph);
