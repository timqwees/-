<?php
/**
 * SEO/GEO-конфигурация — единый источник правды (NAP, цифры, счётчики).
 * Меняешь здесь — обновляется везде: канониклы, schema, sitemap, rss, llms.txt.
 * Значения берутся из .env (через $_ENV, загруженное vlucas/phpdotenv в bootstrap.php)
 * с фоллбэками на плейсхолдерами.
 */

$env = fn(string $key, string $default = '') => $_ENV[$key] ?? getenv($key) ?: $default;

// --- Канонический домен ---
// Прод-domain сайта (punycode). Используется как fallback и как «доверенный» хост:
// любой другой Host из HTTP-Host-заголовка не попадает в canonical/OG/sitemap
// (иначе атакующий подменяет каноникл сайта одним заголовком).
const FSS_PROD_HOST = 'xn--h1aafj.xn--p1ai';

// IDN → punycode с фолбэком (intl может быть не установлен).
$toPuny = static function (string $host): string {
	$host = strtolower(trim($host));
	if ($host === '' || !preg_match('/[^\x00-\x7F]/', $host)) {
		return $host;
	}
	if (!function_exists('idn_to_ascii')) {
		return FSS_PROD_HOST;
	}
	$ascii = idn_to_ascii($host, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
	return ($ascii === false || $ascii === '') ? FSS_PROD_HOST : strtolower($ascii);
};

// Хост, которому можно верить: прод-домен или локальная разработка.
$isTrustedHost = static function (string $host): bool {
	if ($host === '' || $host === FSS_PROD_HOST) {
		return true;
	}
	if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1' || $host === '[::1]') {
		return true;
	}
	return (bool)preg_match('/^[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}$/', $host);
};

// 1) Явная настройка из .env (SITE_URL) — приоритет №1.
$configuredSite = trim((string)$env('SITE_URL', ''));
$configuredHost = '';
if ($configuredSite !== '') {
	$configuredHost = (string)preg_replace('#^https?://#i', '', $configuredSite);
	$configuredHost = (string)preg_replace('#/.*$#', '', $configuredHost);
	$configuredHost = (string)preg_replace('/:\d+$/', '', $configuredHost);
	$configuredHost = $toPuny($configuredHost);
}

// 2) Иначе — Host из запроса, но только если он доверенный.
$httpHost = (string)($_SERVER['HTTP_HOST'] ?? '');
$httpHost = strtolower(trim($httpHost));
// Порт сохраняем: в локальной разработке каноникл без порта ведёт в никуда.
$httpPort = '';
if (preg_match('/^(\[[0-9a-f:]+\]|[^:]+):(\d{1,5})$/i', $httpHost, $pm)) {
	$httpHost = $pm[1];
	$httpPort = $pm[2];
}
$httpHost = $toPuny($httpHost);
$httpHost = (string)preg_replace('/^www\./', '', $httpHost);

$baseHost = $configuredHost !== '' ? $configuredHost : $httpHost;
if (!$isTrustedHost($baseHost) || $baseHost === '') {
	$baseHost = FSS_PROD_HOST;
	$httpPort = '';
}

// Определяем схему (HTTP/HTTPS)
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$scheme = $isHttps ? 'https' : 'http';

// base_url — всегда https + www (канон .htaccess: https://www.<host>)
$baseUrl = 'https://www.' . $baseHost;
// Локальная разработка — без www и по фактической схеме, иначе каноникл уводит на прод.
if ($isTrustedHost($baseHost) && $baseHost !== FSS_PROD_HOST) {
	$baseUrl = $scheme . '://' . $baseHost . ($httpPort !== '' ? ':' . $httpPort : '');
}

// base_idn — IDN версия для отображения (пустой домен idn_to_utf8() не принимает)
$baseIdn = 'https://www.' . ($baseHost !== '' && function_exists('idn_to_utf8')
	? idn_to_utf8($baseHost, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46)
	: $baseHost);

return [
	// Домен-канон (SITE_URL из .env → иначе доверенный Host → иначе прод-домен)
	'base_url' => $baseUrl,
	'base_idn' => $baseIdn,
	'base_host' => 'www.' . $baseHost,

	'brand' => 'ФАВОРИТ-СТРОЙ-СНАБ',
	'brand_short' => 'ФСС',
	'brand_en' => 'FAVORIT-STROY-SNAB',
	'brand_alt' => ['Фаворит-Строй-Снаб', 'ФСС', 'fss.рус', 'ФАВОРИТ СТРОЙ СНАБ', 'Favorit Stroy Snab'],

	'phone' => '+7 938 018-00-06',
	'phone_tel' => '+79380180006',
	'phone_display' => '+7 938 018-00-06',
	// Номера 8-800 вынесены: в конфиге был плейсхолдер «8 800 000 00 00» —
	// несуществующий номер, который при публикации вводил бы клиентов в заблуждение.
	'email' => $env('SITE_EMAIL', 'fss@фсс.рус'),
	'email_puny' => $env('SITE_EMAIL_PUNY', 'fss@' . FSS_PROD_HOST),
	'telegram' => 'https://t.me/fss_rus',
	// ВКонтакте из контактов убран: вместо него — Яндекс.Карты (площадка, цех, проезд).
	'yandex_maps' => 'https://yandex.ru/maps/?text=' . rawurlencode('Москва, пер. Большой Спасоглинищевский, д. 9/1 стр. 10, помещ. 1/2, 101000'),

	'address' => [
		'postal' => '101000',
		'region' => 'Москва',
		'locality' => 'Москва',
		'street' => 'пер. Большой Спасоглинищевский, д. 9/1 стр. 10, помещ. 1/2',
		'lat' => 55.7520,
		'lon' => 37.6460,
	],

	'founded' => '2020',
	'registered_at' => '2020-09-01',
	'founded_market' => '2011',
	// Лет на рынке считаем от founded_market автоматически — цифра не устаревает.
	'market_years' => (string)max(1, (int)date('Y') - 2011),
	'inn' => '9724020865',
	'ogrn' => '1207700318583',
	'kpp' => '770901001',
	'ceo' => 'Аюбов Хасин Хамзатович',
	// Число объектов — по фактическому перечню выполненных работ:
	// 16 объектов капитального ремонта + 13 оснащённых школ + общежития в Сочи (9 000+ м²)
	// + административное здание, навес на 100 машино-мест, поставки БПЛА и оборудования.
	'objects' => '30+',
	'objects_count' => 30,
	'kapremont_objects' => 16,
	'schools_equipped' => 13,
	'sochi_area' => '9 000+ м²',
	'naves_capacity' => '100 машино-мест',
	'guarantee_years' => '50+',
	'deadline_days' => '14',
	'price_response_minutes' => '15',
	'callback_minutes' => '7',
	// Регионы, где есть подтверждённые выполненные объекты + общий охват
	'area_served' => ['Москва', 'Московская область', 'Краснодарский край', 'Чеченская Республика', 'Ростовская область', 'Российская Федерация'],

	// --- Аналитика (из .env). По умолчанию ПУСТО: иначе плейсхолдеры (G-XXXX/M-XXXX)
	// попадали бы в HTML как «живые» счётчики. Заполните .env реальными ID. ---
	'ym_id' => $env('YM_ID', ''),
	'ga4_id' => $env('GA4_ID', ''),
	'gtm_id' => $env('GTM_ID', ''),
	'gtag_id' => $env('GADS_ID', ''),
	'yandex_verification' => $env('YWEB_ID', ''),
	'google_verification' => $env('GSC_ID', ''),
	'indexnow_key' => $env('INDEXNOW_KEY', 'b339c4d027c523c5aa5bed28703c94bd'),

	// Прочее
	'cache_bust' => '20261024',
];
