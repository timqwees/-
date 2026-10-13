<?php
/**
 * SEO/GEO-конфигурация — единый источник правды (NAP, цифры, счётчики).
 * Меняешь здесь — обновляется везде: канониклы, schema, sitemap, rss, llms.txt.
 */

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

	'founded' => '2011',
	'objects' => '180+',
	'guarantee_years' => '50+',
	'deadline_days' => '14',
	'price_response_minutes' => '15',
	'callback_minutes' => '7',
	'area_served' => ['Москва', 'Московская область', 'Россия'],

	// --- Аналитика (заполнить перед выкатом) ---
	'ym_id' => '',            // номер счётчика Яндекс.Метрики
	'ga4_id' => '',           // G-XXXXXXXXXX
	'gtm_id' => '',           // GTM-XXXXXXX
	'gtag_id' => '',          // AW-XXXXXXXXX (Google Ads)
	'yandex_verification' => '27e0ba9980498150',
	'google_verification' => '',
	'indexnow_key' => '',

	// Прочее
	'cache_bust' => '20261009',
];
