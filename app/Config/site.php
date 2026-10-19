<?php
/**
 * SEO/GEO-конфигурация — единый источник правды (NAP, цифры, счётчики).
 * Меняешь здесь — обновляется везде: канониклы, schema, sitemap, rss, llms.txt.
 * Значения берутся из .env (через $_ENV, загруженное vlucas/phpdotenv в bootstrap.php)
 * с фоллбэками на плейсхолдерами.
 */

$env = fn(string $key, string $default = '') => $_ENV[$key] ?? getenv($key) ?: $default;

// Автоопределение текущего хоста
$httpHost = $_SERVER['HTTP_HOST'] ?? 'www.xn--h1aafj.xn--p1ai';
$httpHost = strtolower(trim($httpHost));
$httpHost = preg_replace('/^www\./', '', $httpHost);
$httpHost = preg_replace('/:\d+$/', '', $httpHost);

// Определяем схему (HTTP/HTTPS)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
          (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ? 'https' : 'http';

// base_host в punycode (для каноникалов и schema)
$baseHost = $httpHost;
// Если хост в IDN (кириллица), конвертируем в punycode
if (preg_match('/[^\x00-\x7F]/', $baseHost)) {
    $baseHost = idn_to_ascii($baseHost, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
}

// base_url — всегда с www в punycode
$baseUrl = $scheme . '://www.' . $baseHost;

// base_idn — IDN версия для отображения
$baseIdn = $scheme . '://www.' . (preg_match('/^xn--/', $httpHost) ? idn_to_utf8($httpHost, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46) : $httpHost);

return [
	// Домен-канон (автоопределяется от текущего хоста)
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
	'phone_8800' => '8 800 000 00 00',
	'phone_8800_tel' => '88000000000',
	'phone_8800_display' => '8 800 000 00 00',
	'email' => 'fss@' . (preg_match('/^xn--/', $baseHost) ? idn_to_utf8($baseHost, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46) : $baseHost),
	'email_puny' => 'fss@' . $baseHost,
	'telegram' => 'https://t.me/fss_rus',
	'vk' => 'https://vk.com/fss_rus',

	'address' => [
		'postal' => '101000',
		'region' => 'Москва',
		'locality' => 'Москва',
		'street' => 'ул. Большая Спасоглинищевская, д. 9/1 стр. 10, помещ. 1/2',
		'lat' => 55.7520,
		'lon' => 37.6460,
	],

	'founded' => '2020',
	'registered_at' => '2020-09-01',
	'founded_market' => '2011',
	'inn' => '9724020865',
	'ogrn' => '1207700318583',
	'kpp' => '770901001',
	'ceo' => 'Аюбов Хасин Хамзатович',
	'objects' => '180+',
	'guarantee_years' => '50+',
	'deadline_days' => '14',
	'price_response_minutes' => '15',
	'callback_minutes' => '7',
	'area_served' => ['Москва', 'Московская область', 'Россия'],

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
	'cache_bust' => '20261018',
];
