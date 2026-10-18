<?php
/**
 * SEO/GEO-конфигурация — единый источник правды (NAP, цифры, счётчики).
 * Меняешь здесь — обновляется везде: канониклы, schema, sitemap, rss, llms.txt.
 * Значения берутся из .env (через $_ENV, загруженное vlucas/phpdotenv в bootstrap.php)
 * с фоллбэками на плейсхолдеры.
 */

$env = fn(string $key, string $default = '') => $_ENV[$key] ?? getenv($key) ?: $default;

return [
	// Домен-канон (punycode — одинаково понимается всеми парсерами)
	'base_url' => 'https://www.xn--h1aafj.xn--p1ai',
	'base_idn' => 'https://www.фсс.рус',
	'base_host' => 'www.xn--h1aafj.xn--p1ai',

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
	'email' => 'fss@фсс.рус',
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

	// --- Аналитика (из .env) ---
	'ym_id' => $env('YM_ID', 'XXXXXXXX'),
	'ga4_id' => $env('GA4_ID', 'G-XXXXXXXXXX'),
	'gtm_id' => $env('GTM_ID', 'GTM-XXXXXXX'),
	'gtag_id' => $env('GADS_ID', 'AW-XXXXXXXXX'),
	'yandex_verification' => $env('YWEB_ID', ''),
	'google_verification' => $env('GSC_ID', ''),
	'indexnow_key' => $env('INDEXNOW_KEY', 'b339c4d027c523c5aa5bed28703c94bd'),

	// Прочее
	'cache_bust' => '20261018',
];
