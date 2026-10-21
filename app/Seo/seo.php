<?php
/**
 * SEO-библиотека (ОДНА точка правды для мета-тегов, schema и аналитики).
 *
 * Доступные функции:
 *   seo_canonical(int $len = 0)      — абсолютный каноникл текущей страницы
 *   seo_canonical_tags()             — <link rel=canonical> + hreflang ru/x-default
 *   seo_og_tags()                    — Open Graph + Twitter Card
 *   seo_service(string $slug)        — данные услуги из services.php
 *   seo_services()                   — все услуги
 *   seo_jsonld(array $nodes)         — JSON-LD @graph
 *   seo_breadcrumbs_jsonld()         — хлебные крошки по роуту
 *   seo_service_jsonld(string $slug) — Service + Offer + provider + areaServed
 *   seo_faq_jsonld(array $qa)        — FAQPage
 *   seo_organization_jsonld()        — Organization / LocalBusiness / WebSite
 *   seo_analytics()                  — Яндекс.Метрика + GA4 + GTM (если заданы ID)
 *   seo_utm_script()                 — авто-захват UTM/yclid в формы
 */

if (!function_exists('seo_config')) {
	function seo_config(string $key = '')
	{
		static $cfg = null;
		if ($cfg === null) {
			$cfg = require dirname(__DIR__) . '/Config/site.php';
		}
		if ($key === '') return $cfg;
		return $cfg[$key] ?? null;
	}
}

if (!function_exists('seo_ver')) {
	/** Версия статики для cache busting — одна точка правды (seo_config.php). */
	function seo_ver(): string
	{
		return (string)(seo_config('cache_bust') ?: '1');
	}
}

if (!function_exists('seo_url_path')) {
	/**
	 * Безопасное извлечение пути из URL/пути.
	 * НЕ используем parse_url(): он портит байты кириллицы (например «АБК» → «П_П_П_»),
	 * из-за чего is_file() не находил реальные файлы с не-ASCII именами.
	 */
	function seo_url_path(?string $url = null): string
	{
		$url = (string)$url;
		if (preg_match('#^https?://#i', $url)) {
			$url = (string)preg_replace('#^https?://[^/]+#i', '', $url);
		}
		$cut = strcspn($url, '?#');
		return rawurldecode(substr($url, 0, $cut));
	}
}

if (!function_exists('seo_url')) {
	/**
	 * Чистый (ЧПУ) путь текущей страницы.
	 * Приоритет — реальный файл страницы (его определил bootstrap через debug_backtrace),
	 * поэтому canonical всегда правильный, даже если страницу открыли по старому .php-адресу.
	 */
	function seo_url(): string
	{
		static $cached = null;
		if ($cached !== null) {
			return $cached;
		}
		// Явный путь (гео-посадочные и прочие виртуальные URL) приоритетнее вывода из файла
		if (!empty($GLOBALS['fss_canonical_path'])) {
			return $cached = '/' . ltrim((string)$GLOBALS['fss_canonical_path'], '/');
		}
		$file = $GLOBALS['fss_page_file'] ?? ($_SERVER['SCRIPT_FILENAME'] ?? '');
		$file = $file ? realpath($file) : false;
		$root = realpath(dirname(__DIR__, 2));
		if ($file && $root && str_starts_with($file, $root)) {
			$rel = ltrim(str_replace($root, '', $file), '/');
			if (preg_match('#^public/pages/services/([^/]+)/index\.php$#', $rel, $m)) {
				return $cached = '/services/' . $m[1];
			}
			if (preg_match('#^public/pages/blog/post\.php$#', $rel)) {
				$slug = $_GET['post'] ?? '';
				return $cached = $slug !== '' ? '/blog/' . $slug : '/blog';
			}
			switch ($rel) {
				case 'public/pages/home.php': return $cached = '/';
				case 'public/pages/uslugi/uslugi.php': return $cached = '/uslugi';
				case 'public/pages/portfolio.php': return $cached = '/portfolio';
				case 'public/pages/contacts/index.php': return $cached = '/contacts';
				case 'public/pages/about.php':
				case 'public/pages/about/index.php': return $cached = '/about';
				case 'public/pages/blog/list.php': return $cached = '/blog';
				case 'public/pages/politic/index.php': return $cached = '/politika';
				case 'public/pages/agree/index.php': return $cached = '/soglasie';
				case '404.php': return $cached = '/404';
			}
		}
		$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
		return $cached = '/' . trim(rawurldecode($uri), '/');
	}
}

if (!function_exists('seo_canonical')) {
	function seo_canonical(): string
	{
		return rtrim((string)seo_config('base_url'), '/') . seo_url();
	}
}

if (!function_exists('seo_canonical_tags')) {
 function seo_canonical_tags(): string
	{
		$c = seo_canonical();
		return "\t<link rel=\"canonical\" href=\"{$c}\" />\n"
			. "\t<link rel=\"alternate\" hreflang=\"ru\" href=\"{$c}\" />\n"
			. "\t<link rel=\"alternate\" hreflang=\"x-default\" href=\"{$c}\" />";
	}
}

if (!function_exists('seo_verify_tags')) {
	function seo_verify_tags(): string
	{
		$out = '';
		$y = (string)seo_config('yandex_verification');
		if ($y !== '') $out .= "\t<meta name=\"yandex-verification\" content=\"{$y}\" />\n";
		$g = (string)seo_config('google_verification');
		if ($g !== '') $out .= "\t<meta name=\"google-site-verification\" content=\"{$g}\" />\n";
		return $out;
	}
}

if (!function_exists('seo_og_tags')) {
	function seo_og_tags(?string $title = null, ?string $desc = null, ?string $image = null): string
	{
		$title = (string)$title;
		$desc = (string)$desc;
		$image = (string)$image;
		$base = rtrim((string)seo_config('base_url'), '/');
		$brand = (string)seo_config('brand');
		$default = '/public/assets/image/og/og-blog-1200x630.jpg';
		$img = $image !== '' ? $image : $default;
		$path = '/' . ltrim(seo_url_path($img), '/');
		if (!is_file(dirname(__DIR__, 2) . $path)) {
			$img = $default;
		}
		if (!preg_match('#^https?://#i', $img)) {
			$img = $base . $img;
		}
		$title = $title !== '' ? $title : $brand;
		$desc = $desc !== '' ? $desc : 'Быстровозводимые здания под ключ от 14 дней. Склады, ангары, АБК, общежития, капремонт. ';
		$imgAlt = $desc;
		$out = "\t<meta property=\"og:locale\" content=\"ru_RU\">\n"
			. "\t<meta property=\"og:site_name\" content=\"{$brand}\">\n"
			. "\t<meta property=\"og:type\" content=\"website\">\n"
			. "\t<meta property=\"og:title\" content=\"" . htmlspecialchars($title, ENT_QUOTES) . "\">\n"
			. "\t<meta property=\"og:description\" content=\"" . htmlspecialchars($desc, ENT_QUOTES) . "\">\n"
			. "\t<meta property=\"og:url\" content=\"" . seo_canonical() . "\">\n"
			. "\t<meta property=\"og:image\" content=\"{$img}\">\n"
			. "\t<meta property=\"og:image:width\" content=\"1200\">\n"
			. "\t<meta property=\"og:image:height\" content=\"630\">\n"
			. "\t<meta property=\"og:image:alt\" content=\"" . htmlspecialchars($imgAlt, ENT_QUOTES) . "\">\n"
		. "\t<meta name=\"twitter:image:alt\" content=\"" . htmlspecialchars($imgAlt, ENT_QUOTES) . "\">\n"
			. "\t<meta name=\"twitter:card\" content=\"summary_large_image\">\n"
			. "\t<meta name=\"twitter:title\" content=\"" . htmlspecialchars($title, ENT_QUOTES) . "\">\n"
			. "\t<meta name=\"twitter:description\" content=\"" . htmlspecialchars($desc, ENT_QUOTES) . "\">\n"
			. "\t<meta name=\"twitter:image\" content=\"{$img}\">\n"
			. "\t<meta name=\"twitter:image:alt\" content=\"" . htmlspecialchars($brand, ENT_QUOTES) . "\">";
		return $out;
	}
}

if (!function_exists('seo_clip')) {
	/** Обрезка текста до лимита meta-описания/заголовка по границе слова. */
	function seo_clip(?string $text = null, int $max = 158): string
	{
		$text = (string)$text;
		$text = trim((string)preg_replace('/\s+/u', ' ', $text));
		if (mb_strlen($text) <= $max) {
			return $text;
		}
		$cut = mb_substr($text, 0, $max);
		$sp = mb_strrpos($cut, ' ');
		if ($sp !== false && $sp > (int)($max * 0.6)) {
			$cut = mb_substr($cut, 0, $sp);
		}
		// ВАЖНО: trim только UTF-8-безопасно. Побайтовый rtrim с «—» рвал многобайтные
		// последовательности (0x80 входит в UTF-8-продолжение) → htmlspecialchars(...,'UTF-8')
		// возвращал пустую строку, и <title>/description оказывались пустыми.
		$cut = (string)preg_replace('/[\s.,;:—–-]+$/u', '', $cut);
		return $cut . '…';
	}
}

if (!function_exists('seo_img_attrs')) {
	/** width/height из реального файла (борьба с CLS). Пусто, если размер не определён. */
	function seo_img_attrs(?string $src = null): string
	{
		$src = (string)$src;
		// Размеры берём через Image::dims(): он сначала смотрит рантайм-кэш,
		// потом читает заголовок файла — без getimagesize() на каждом рендере (TTFB).
		$size = class_exists('\\App\\Models\\Image') ? \App\Models\Image::dims($src) : null;
		if (!$size || empty($size[0]) || empty($size[1])) {
			return '';
		}
		return ' width="' . (int)$size[0] . '" height="' . (int)$size[1] . '"';
	}
}

if (!function_exists('seo_jsonld')) {
	function seo_jsonld(?array $nodes = null): string
	{
		$nodes = (array)$nodes;
		$graph = ['@context' => 'https://schema.org', '@graph' => array_values($nodes)];
		return '<script type="application/ld+json">' . "\n"
			. json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS)
			. "\n</script>";
	}
}

if (!function_exists('seo_organization_jsonld')) {
	function seo_organization_jsonld(): array
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$a = (array)seo_config('address');
		return [
			[
				'@type' => ['Organization', 'LocalBusiness'],
				'@id' => $base . '#organization',
				'name' => seo_config('brand'),
				'alternateName' => array_merge([seo_config('brand_en')], (array)seo_config('brand_alt')),
				'legalName' => 'ООО "ФАВОРИТ-СТРОЙ-СНАБ"',
				'url' => $base . '/',
				'inn' => seo_config('inn'),
				'ogrn' => seo_config('ogrn'),
				'logo' => ['@type' => 'ImageObject', 'url' => $base . '/public/assets/image/logo/favicon/favicon.svg', 'width' => 512, 'height' => 512],
				'image' => ['@type' => 'ImageObject', 'url' => $base . '/public/assets/image/og/og-blog-1200x630.jpg', 'width' => 1200, 'height' => 630],
				'description' => 'ФАВОРИТ-СТРОЙ-СНАБ — производство и монтаж быстровозводимых зданий (склады, ангары, АБК, общежития), капитальный ремонт школ и социальных объектов, комплексное оснащение школ по стандартам Минпросвещения РФ, поставка БПЛА и систем КИТ, грузоперевозки и аренда спецтехники. Выполнено: 16 объектов капитального ремонта, оснащение 13 школ, комплекс общежитий 9 000+ м² в Сочи. Сотрудничество с Росгвардией (ФСВНГ РФ). Москва и Московская область, работаем по всей России.',
				'foundingDate' => seo_config('founded'),
				'slogan' => 'Быстровозводимые здания под ключ от 14 дней',
				'telephone' => seo_config('phone'),
				'email' => seo_config('email'),
				'priceRange' => '₽₽',
				'currenciesAccepted' => 'RUB',
				'paymentAccepted' => 'банковский перевод, наличные, оплата по этапам',
				'address' => [
					'@type' => 'PostalAddress',
					'postalCode' => $a['postal'],
					'addressCountry' => 'RU',
					'addressRegion' => $a['region'],
					'addressLocality' => $a['locality'],
					'streetAddress' => $a['street'],
				],
				'geo' => ['@type' => 'GeoCoordinates', 'latitude' => $a['lat'], 'longitude' => $a['lon']],
				'hasMap' => 'https://yandex.ru/maps/?text=' . rawurlencode($a['postal'] . ', ' . $a['locality'] . ', ' . $a['street']),
				'openingHoursSpecification' => [[
					'@type' => 'OpeningHoursSpecification',
					'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
					'opens' => '09:00',
					'closes' => '18:00',
				]],
				'areaServed' => array_map(fn($n) => ['@type' => 'AdministrativeArea', 'name' => $n], (array)seo_config('area_served')),
				'contactPoint' => [[
					'@type' => 'ContactPoint',
					'telephone' => seo_config('phone'),
					'contactType' => 'customer service',
					'areaServed' => 'RU',
					'availableLanguage' => ['Russian'],
				]],
				'sameAs' => array_values(array_filter([seo_config('telegram'), seo_config('vk')])),
				'knowsAbout' => [
					'быстровозводимые здания', 'модульные здания', 'капитальное строительство', 'капитальный ремонт зданий',
					'склады из сэндвич-панелей', 'ангары', 'АБК', 'общежития', 'грузоперевозки', 'аренда спецтехники',
					'дизельные генераторные установки', 'компрессорные станции', 'мусорные баки', 'навесы',
					'капитальный ремонт школ', 'оснащение школ', 'школьная мебель', 'оборудование учебных кабинетов',
					'лабораторные комплексы для школ', 'мультимедийное оборудование для школ', 'БПЛА и беспилотные летательные аппараты',
					'системы КИТ', 'поставка генераторных установок',
				],
				'hasCredential' => [
					[
						'@type' => 'EducationalOccupationalCredential',
						'credentialCategory' => 'license',
						'name' => 'Допуск СРО на проектирование и строительство',
						'description' => 'Членство в СРО, работа по 44-ФЗ и 223-ФЗ, закрывающие документы по каждому объекту.',
					],
					[
						'@type' => 'EducationalOccupationalCredential',
						'credentialCategory' => 'accreditation',
						'name' => 'Оснащение школ по стандартам Минпросвещения РФ',
						'description' => 'Комплексное оснащение 13 вновь построенных школ с учётом действующих стандартов Минпросвещения РФ и технического задания заказчиков.',
					],
				],
			],
			[
				'@type' => 'WebSite',
				'@id' => $base . '#website',
				'url' => $base . '/',
				'name' => seo_config('brand') . ' — быстровозводимые здания под ключ',
				'description' => 'Официальный сайт ' . seo_config('brand') . ': производство БВЗ, капремонт, грузоперевозки, спецтехника. Москва и МО.',
				'inLanguage' => 'ru-RU',
				'publisher' => ['@id' => $base . '#organization'],
				'potentialAction' => [
					'@type' => 'SearchAction',
					'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $base . '/search?q={search_term_string}'],
					'query-input' => 'required name=search_term_string',
				],
			],
		];
	}
}

if (!function_exists('seo_breadcrumbs_jsonld')) {
	/** Хлебные крошки из реального файла страницы — не зависят от хардкода. */
	function seo_breadcrumbs_jsonld(): ?array
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$url = seo_url();
		if ($url === '/' || $url === '/404') return null;

		$map = [
			'/uslugi' => 'Услуги',
			'/portfolio' => 'Портфолио объектов',
			'/about' => 'О компании',
			'/contacts' => 'Контакты',
			'/blog' => 'Блог',
			'/politika' => 'Политика конфиденциальности',
			'/soglasie' => 'Согласие на обработку данных',
			'/search' => 'Поиск по сайту',
			'/search/blog' => 'Поиск по блогу',
		];
		$items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $base . '/']];

		if (preg_match('#^/services/([^/]+)$#', $url)) {
			$items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'Услуги', 'item' => $base . '/uslugi'];
		}
		// Гео-посадочная /services/{slug}/{geo}: Главная → Услуги → Услуга → Гео
		if (preg_match('#^/services/([^/]+)/([^/]+)$#', $url, $mg)) {
			$sg = seo_service($mg[1]);
			$gg = function_exists('seo_geo') ? seo_geo() : ['geo' => []];
			$items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'Услуги', 'item' => $base . '/uslugi'];
			$items[] = ['@type' => 'ListItem', 'position' => 3, 'name' => (string)($sg['title'] ?? $mg[1]), 'item' => $base . '/services/' . $mg[1]];
			$items[] = ['@type' => 'ListItem', 'position' => 4, 'name' => (string)($gg['geo'][$mg[2]]['name'] ?? $mg[2]), 'item' => $base . $url];
			return ['@type' => 'BreadcrumbList', '@id' => $base . $url . '#breadcrumb', 'itemListElement' => $items];
		}
		if (preg_match('#^/blog/([^/]+)$#', $url)) {
			$items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'Блог', 'item' => $base . '/blog'];
		}
		// Детальная страница объекта: Главная → Портфолио → Объект
		if (preg_match('#^/portfolio/([^/]+)$#', $url, $mp)) {
			$projects = [];
			$projectsFile = dirname(__DIR__, 2) . '/app/Data/projects.php';
			if (is_file($projectsFile)) {
				$projects = (array)(require $projectsFile);
			}
			$items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'Портфолио объектов', 'item' => $base . '/portfolio'];
			$items[] = ['@type' => 'ListItem', 'position' => 3, 'name' => (string)($projects[$mp[1]]['title'] ?? $mp[1]), 'item' => $base . $url];
			return ['@type' => 'BreadcrumbList', '@id' => $base . $url . '#breadcrumb', 'itemListElement' => $items];
		}
		$name = $map[$url] ?? null;
		if (!$name && preg_match('#^/services/([^/]+)$#', $url, $m)) {
			$s = seo_service($m[1]);
			$name = $s['title'] ?? $m[1];
		}
		if (!$name && preg_match('#^/blog/([^/]+)$#', $url, $m)) {
			$p = seo_blog_post($m[1]);
			$name = $p['title'] ?? $m[1];
		}
		if (!$name) return null;
		$items[] = ['@type' => 'ListItem', 'position' => count($items) + 1, 'name' => $name, 'item' => $base . $url];
		return ['@type' => 'BreadcrumbList', '@id' => $base . $url . '#breadcrumb', 'itemListElement' => $items];
	}
}

if (!function_exists('seo_service')) {
	/** Услуга по slug. Данные — app/Data/services.php */
	function seo_service(string $slug): ?array
	{
		$all = seo_services();
		return $all[$slug] ?? null;
	}
}

if (!function_exists('seo_breadcrumbs_html')) {
	/** Видимые HTML-хлебные крошки — переиспользуют те же данные, что и JSON-LD. */
	function seo_breadcrumbs_html(): string
	{
		$bc = seo_breadcrumbs_jsonld();
		if (!$bc) return '';
		$base = rtrim((string)seo_config('base_url'), '/');
		$items = $bc['itemListElement'];
		$out = '<nav aria-label="Хлебные крошки" class="fss-breadcrumbs"><ol class="fss-bc-list">';
		$last = count($items) - 1;
		foreach ($items as $i => $it) {
			$out .= '<li class="fss-bc-item">';
			/* Последняя крошка — текущая страница: без ссылки, с aria-current.
			   В JSON-LD URL остаётся (там так и положено). */
			if ($i === $last) {
				$out .= '<span class="fss-bc-current" aria-current="page">' . htmlspecialchars($it['name'], ENT_QUOTES) . '</span>';
			} elseif (!empty($it['item'])) {
				$out .= '<a href="' . htmlspecialchars($it['item'], ENT_QUOTES) . '" title="' . htmlspecialchars($it['name'], ENT_QUOTES) . '" class="fss-bc-link">' . htmlspecialchars($it['name'], ENT_QUOTES) . '</a>';
			} else {
				$out .= '<span class="fss-bc-current">' . htmlspecialchars($it['name'], ENT_QUOTES) . '</span>';
			}
			$out .= '</li>';
		}
		$out .= '</ol></nav>';
		return $out;
	}
}

if (!function_exists('seo_services')) {
	function seo_services(): array
	{
		static $all = null;
		if ($all === null) {
			$all = require dirname(__DIR__, 2) . '/app/Data/services.php';
		}
		return $all;
	}
}

if (!function_exists('seo_geo_allows')) {
	/**
	 * Разрешена ли услуга для гео-направления.
	 *
	 * Если в app/Data/geo.php у города задан ключ 'services' — гео-страница
	 * создаётся только для этих услуг. Это защита от doorway-страниц:
	 * нельзя генерировать «ангары в Гудермесе», если там нет подтверждённых
	 * работ по ангарам (есть только капремонт школ).
	 */
	function seo_geo_allows(array $geo, string $slug): bool
	{
		if (!empty($geo['services']) && is_array($geo['services'])) {
			return in_array($slug, array_map('strval', $geo['services']), true);
		}
		return true;
	}
}

if (!function_exists('seo_geo')) {
	/** Гео-направления из app/Data/geo.php (кэш на запрос) */
	function seo_geo(): array
	{
		static $all = null;
		if ($all === null) {
			$path = dirname(__DIR__, 2) . '/app/Data/geo.php';
			$all = file_exists($path) ? require $path : ['geo' => [], 'priority_services' => []];
		}
		return $all;
	}
}

if (!function_exists('seo_reviews')) {
	function seo_reviews(): array
	{
		static $all = null;
		if ($all === null) {
			$path = dirname(__DIR__, 2) . '/app/Data/reviews.php';
			$all = file_exists($path) ? require $path : [];
		}
		return $all;
	}
}

if (!function_exists('seo_reviews_jsonld')) {
	/** Review[] + AggregateRating для Organization/LocalBusiness */
	function seo_reviews_jsonld(): array
	{
		$reviews = seo_reviews();
		if (!$reviews) return [];
		$count = count($reviews);
		$sum = array_sum(array_column($reviews, 'star'));
		$avg = round($sum / $count, 1);
		$base = rtrim((string)seo_config('base_url'), '/');
		$nodes = [[
			'@type' => 'AggregateRating',
			'ratingValue' => $avg,
			'bestRating' => '5',
			'worstRating' => '1',
			'reviewCount' => $count,
		]];
		foreach ($reviews as $i => $r) {
			$nodes[] = [
				'@type' => 'Review',
				'author' => ['@type' => 'Person', 'name' => $r['name']],
				'reviewBody' => $r['text'],
				'datePublished' => $r['date'],
				'reviewRating' => [
					'@type' => 'Rating',
					'ratingValue' => $r['star'],
					'bestRating' => '5',
					'worstRating' => '1',
				],
			];
		}
		return $nodes;
	}
}

if (!function_exists('seo_blog_posts')) {
	/** Все блог-посты из app/Data/blog.php */
	function seo_blog_posts(): array
	{
		static $all = null;
		if ($all === null) {
			$path = dirname(__DIR__, 2) . '/app/Data/blog.php';
			$all = file_exists($path) ? require $path : [];
		}
		return $all;
	}
}

if (!function_exists('seo_blog_post')) {
	/** Блог-пост по slug */
	function seo_blog_post(string $slug): ?array
	{
		$all = seo_blog_posts();
		return $all[$slug] ?? null;
	}
}

if (!function_exists('seo_blog_post_content')) {
	/** HTML-контент блог-поста по content_id */
	function seo_blog_post_content(array $post): string
	{
		$id = $post['content_id'] ?? 0;
		if (!$id) {
			return $post['content'] ?? '';
		}
		$path = dirname(__DIR__, 2) . '/public/pages/blog/content/' . $id . '.php';
		if (!is_file($path)) {
			return $post['content'] ?? '';
		}
		return (string)file_get_contents($path);
	}
}

if (!function_exists('seo_blog_jsonld')) {
	/** BlogPosting schema.org для текущего поста или Article для списка */
	function seo_blog_jsonld(?string $slug = null): ?array
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$posts = seo_blog_posts();
		if ($slug && isset($posts[$slug])) {
			$p = $posts[$slug];
			$author = seo_config('ceo');
			return [
				'@type' => 'BlogPosting',
				'headline' => $p['title'],
				'description' => $p['description'],
				'datePublished' => $p['date'],
				'dateModified' => $p['date'],
				'author' => ['@type' => 'Person', 'name' => $author],
				'publisher' => ['@id' => $base . '#organization'],
				'image' => ['@type' => 'ImageObject', 'url' => $base . $p['og_image']],
				'articleSection' => $p['category_name'],
				'keywords' => implode(', ', $p['tags']),
				'wordCount' => str_word_count(strip_tags(seo_blog_post_content($p))),
				'timeRequired' => 'PT' . (int)($p['read_minutes'] ?? 5) . 'M',
				'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $base . '/blog/' . $slug],
			];
		}
		if (!$slug) {
			$nodes = [];
			foreach ($posts as $slug => $p) {
				$nodes[] = [
					'@type' => 'BlogPosting',
					'headline' => $p['title'],
					'description' => $p['description'],
					'datePublished' => $p['date'],
					'author' => ['@type' => 'Person', 'name' => seo_config('ceo')],
					'publisher' => ['@id' => $base . '#organization'],
					'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $base . '/blog/' . $slug],
				];
			}
			return $nodes;
		}
		return null;
	}
}

if (!function_exists('seo_service_jsonld')) {
	function seo_service_jsonld(string $slug): ?array
	{
		$s = seo_service($slug);
		if (!$s) return null;
		$base = rtrim((string)seo_config('base_url'), '/');
		$url = $base . '/services/' . $slug;
		$node = [
			'@type' => 'Service',
			'@id' => $url . '#service',
			'name' => $s['title'],
			'serviceType' => $s['service_type'] ?? $s['title'],
			'description' => $s['description'],
			'url' => $url,
			'provider' => ['@id' => $base . '#organization'],
			'areaServed' => array_map(fn($n) => ['@type' => 'AdministrativeArea', 'name' => $n], (array)seo_config('area_served')),
			'audience' => ['@type' => 'BusinessAudience', 'name' => 'B2B / B2G'],
			'availableChannel' => [
				'@type' => 'ServiceChannel',
				'serviceUrl' => $url,
				'servicePhone' => ['@type' => 'ContactPoint', 'telephone' => seo_config('phone')],
			],
		];
		if (!empty($s['price_from'])) {
			$node['offers'] = [
				'@type' => 'Offer',
				'price' => $s['price_from'],
				'priceCurrency' => 'RUB',
				'availability' => 'https://schema.org/InStock',
				'url' => $url,
				'priceValidUntil' => date('Y-12-31'),
				'seller' => ['@id' => $base . '#organization'],
			];
		}
		return $node;
	}
}

	if (!function_exists('seo_analytics')) {
	function seo_analytics(): string
	{
		$ym = (string)seo_config('ym_id');
		$ga4 = (string)seo_config('ga4_id');
		$gtm = (string)seo_config('gtm_id');
		// Отсекаем пустые и плейсхолдерные (XXXX) значения — не выводим мусорные счётчики.
		if (!preg_match('/^\d{6,}$/', $ym)) { $ym = ''; }
		if (!preg_match('/^G-[A-Z0-9]{8,}$/i', $ga4) || str_contains(strtoupper($ga4), 'XXXX')) { $ga4 = ''; }
		if (!preg_match('/^GTM-[A-Z0-9]{6,}$/i', $gtm) || str_contains(strtoupper($gtm), 'XXXX')) { $gtm = ''; }

		$counters = '';

		if ($ym !== '') {
			// ВАЖНО: в оригинальном сниппете Метрики цикл for(j<e.length) не выполнялся
			// (e = document, document.length === undefined) — tag.js никогда не подключался,
			// счётчик молча не работал. Подключаем скрипт явно.
			$counters .= "\n<!-- Yandex.Metrika -->"
				. "\n<script>(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};"
				. "m[i].l=1*new Date();var s=document.createElement('script');s.async=1;"
				. "s.src='https://mc.yandex.ru/metrika/tag.js';document.head.appendChild(s);"
				. "})(window,document,'script','https://mc.yandex.ru/metrika/tag.js','ym');"
				. "ym({$ym},'init',{clickmap:true,trackLinks:true,"
				. "accurateTrackBounce:true,webvisor:true,ecommerce:false});</script>"
				. "\n<noscript><div><img src=\"https://mc.yandex.ru/watch/{$ym}\" title=\"Яндекс.Метрика\" "
				. "style=\"position:absolute;left:-9999px\" alt=\"\" /></div></noscript>";
		}

		if ($ga4 !== '') {
			$counters .= "\n<!-- GA4 -->"
				. "\n<script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga4}\"></script>"
				. "\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}"
				. "gtag('js',new Date());gtag('config','{$ga4}');</script>";
		}

		if ($gtm !== '') {
			$counters .= "\n<!-- GTM -->"
				. "\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),"
				. "event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),"
				. "dl=l!=='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;"
				. "f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$gtm}');</script>";
		}

		if ($counters === '') {
			return '';
		}

		// Счётчики грузим ОТЛОЖЕННО (после load, но не позже 3 секунд):
		// так они не занимают канал и не мешают отрисовке на медленном мобильном интернете.
		// Скрипты, разобранные через innerHTML, не выполняются — пересоздаём их,
		// иначе счётчики просто молча не заработают.
		// "</script>" внутри строки обрывает инлайн-скрипт — экранируем
		$countersJson = str_replace('</', '<\\/', json_encode($counters, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		$js = "(function(){var c=" . $countersJson
			. ";var done=false;function load(){if(done)return;done=true;var d=document.createElement('div');"
			. "d.innerHTML=c;var list=d.querySelectorAll('script');"
			. "for(var i=0;i<list.length;i++){var o=list[i],s=document.createElement('script');"
			. "for(var a=0;a<o.attributes.length;a++){s.setAttribute(o.attributes[a].name,o.attributes[a].value);}"
			. "if(!o.getAttribute('src')){s.text=o.textContent;}"
			. "document.head.appendChild(s);}}"
			. "if(document.readyState==='complete'){load();}else{window.addEventListener('load',load,{once:true});"
			. "setTimeout(load,3000);}})();";

		return "\n<!-- Счётчики: отложенная загрузка (не блокируют рендер) -->"
			. "\n<script>" . $js . "</script>";
	}
}

if (!function_exists('seo_utm_script')) {
	/** Захват UTM/yclid/gclid + referrer и подстановка в скрытые поля всех форм. */
	function seo_utm_script(): string
	{
		return <<<'HTML'
		<script>
		(function(){
			'use strict';
			var PARAMS = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content','yclid','gclid','fbclid'];
			var KEY = 'fss_utm', stored = {};
			function clean(url){ try { var u = new URL(url, location.origin); ['message_status','message_msg'].forEach(function(k){u.searchParams.delete(k);}); return u.toString(); } catch(e){ return url; } }
			var q = new URLSearchParams(location.search), has = false;
			PARAMS.forEach(function(p){ stored[p] = q.get(p) || ''; if (stored[p]) has = true; });
			stored.landing_page = clean(location.href); stored.referrer = document.referrer || '';
			try {
				var prev = JSON.parse(localStorage.getItem(KEY) || '{}');
				if (has || !prev.utm_source) localStorage.setItem(KEY, JSON.stringify(stored));
				else { prev.landing_page = stored.landing_page; prev.referrer = stored.referrer; stored = prev; }
			} catch(e) {}
			window.fssUtm = stored;
			function inject(){
				document.querySelectorAll('form').forEach(function(form){
					if (form.dataset.utmInjected) return;
					form.dataset.utmInjected = '1';
					PARAMS.concat(['landing_page','referrer']).forEach(function(n){
						var i = document.createElement('input');
						i.type = 'hidden'; i.name = n; i.value = stored[n] || ''; i.setAttribute('data-utm-field', n);
						form.appendChild(i);
					});
					form.addEventListener('submit', function(){
						try { var f = JSON.parse(localStorage.getItem(KEY) || '{}'); var u = new URLSearchParams(location.search);
							PARAMS.concat(['landing_page','referrer']).forEach(function(n){
								var el = form.querySelector('input[name="'+n+'"]'); if (!el) return;
								el.value = (n === 'landing_page') ? clean(location.href)
									: (n === 'referrer') ? (document.referrer || f.referrer || '')
									: (u.get(n) || f[n] || '');
							});
						} catch(e) {}
					});
				});
			}
			if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', inject); else inject();
			if (typeof MutationObserver !== 'undefined' && document.body) {
				new MutationObserver(function(){ inject(); }).observe(document.body, {childList:true, subtree:true});
			}
		})();
		</script>
		HTML;
	}
}

if (!function_exists('seo_goals_script')) {
	/** Цели: заявки, звонки, мессенджеры, FAQ, калькулятор, скролл (до 90%). */
	function seo_goals_script(): string
	{
		$ym = (string)seo_config('ym_id');
		$ga4 = (string)seo_config('ga4_id');
		$ymId = $ym !== '' ? $ym : '0';
		$ga = $ga4 !== '' ? 'true' : 'false';
		return <<<HTML
		<script>
		(function(){
			'use strict';
			var YM_ID = {$ymId}, GA = {$ga};
			window.fssGoal = function(name, params){
				params = params || {};
				try { if (window.ym && YM_ID) { var c = {form_type: params.form || '', page: location.pathname}; ym(YM_ID, 'reachGoal', name, Object.assign(c, params)); } } catch(e){}
				try { if (GA && window.gtag) gtag('event', name, Object.assign({event_category: 'engagement', event_label: location.pathname}, params)); } catch(e){}
			};
			function goal(el){
				var g = el.getAttribute('data-goal'); if (!g) return;
				window.fssGoal(g, {element: (el.textContent || '').trim().slice(0, 60)});
			}
			document.addEventListener('click', function(e){
				var el = e.target.closest ? e.target.closest('[data-goal]') : null;
				if (el) goal(el);
				var a = e.target.closest ? e.target.closest('a[href^="tel:"], a[href^="mailto:"], a[href*="t.me"], a[href*="wa.me"], a[href*="vk.com"]') : null;
				if (a) window.fssGoal('contact_click', {type: a.getAttribute('href').split(':')[0]});
			}, true);
			document.addEventListener('submit', function(e){
				var f = e.target; if (!f || f.tagName !== 'FORM') return;
				window.fssGoal('form_submit', {form: (f.getAttribute('data-form') || f.id || 'form')});
			}, true);
			var sent = {};
			if ('IntersectionObserver' in window) {
				var io = new IntersectionObserver(function(es){
					es.forEach(function(en){
						if (!en.isIntersecting) return;
						var id = en.target.id || (en.target.tagName + '_' + (en.target.className || '').split(' ')[0]);
						if (sent[id]) return; sent[id] = 1;
						window.fssGoal('block_view', {block: id.slice(0, 40)});
					});
				}, {threshold: 0.5});
				document.querySelectorAll('section[id], .faq-item, .price-table, .reviews, .portfolio-item').forEach(function(el){ io.observe(el); });
			}
			var fired = false;
			window.addEventListener('scroll', function(){
				if (fired) return;
				var h = document.documentElement.scrollHeight - window.innerHeight;
				if (h > 0 && window.scrollY / h >= 0.9) { fired = true; window.fssGoal('scroll_90'); }
			}, {passive: true});
		})();
		</script>
		HTML;
	}
}
