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
		$file = $GLOBALS['fss_page_file'] ?? ($_SERVER['SCRIPT_FILENAME'] ?? '');
		$file = $file ? realpath($file) : false;
		$root = realpath(dirname(__DIR__, 2));
		if ($file && $root && str_starts_with($file, $root)) {
			$rel = ltrim(str_replace($root, '', $file), '/');
			if (preg_match('#^public/pages/services/([^/]+)/index\.php$#', $rel, $m)) {
				return $cached = '/services/' . $m[1];
			}
			switch ($rel) {
				case 'public/pages/home.php': return $cached = '/';
				case 'public/pages/uslugi/uslugi.php': return $cached = '/uslugi';
				case 'public/pages/portfolio.php': return $cached = '/portfolio';
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
		return rtrim(seo_config('base_url'), '/') . seo_url();
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

if (!function_exists('seo_og_tags')) {
	function seo_og_tags(string $title = '', string $desc = '', string $image = ''): string
	{
		$base = rtrim(seo_config('base_url'), '/');
		$brand = seo_config('brand');
		$img = $image ?: $base . '/public/assets/image/og/og-home-1200x630.jpg';
		$title = $title !== '' ? $title : $brand;
		$desc = $desc !== '' ? $desc : 'Быстровозводимые здания под ключ от 14 дней. Склады, ангары, АБК, общежития, капремонт. ';
		$out = "\t<meta property=\"og:locale\" content=\"ru_RU\">\n"
			. "\t<meta property=\"og:site_name\" content=\"{$brand}\">\n"
			. "\t<meta property=\"og:type\" content=\"website\">\n"
			. "\t<meta property=\"og:title\" content=\"" . htmlspecialchars($title, ENT_QUOTES) . "\">\n"
			. "\t<meta property=\"og:description\" content=\"" . htmlspecialchars($desc, ENT_QUOTES) . "\">\n"
			. "\t<meta property=\"og:url\" content=\"" . seo_canonical() . "\">\n"
			. "\t<meta property=\"og:image\" content=\"{$img}\">\n"
			. "\t<meta property=\"og:image:width\" content=\"1200\">\n"
			. "\t<meta property=\"og:image:height\" content=\"630\">\n"
			. "\t<meta property=\"og:image:alt\" content=\"" . htmlspecialchars($brand, ENT_QUOTES) . "\">\n"
			. "\t<meta name=\"twitter:card\" content=\"summary_large_image\">\n"
			. "\t<meta name=\"twitter:title\" content=\"" . htmlspecialchars($title, ENT_QUOTES) . "\">\n"
			. "\t<meta name=\"twitter:description\" content=\"" . htmlspecialchars($desc, ENT_QUOTES) . "\">\n"
			. "\t<meta name=\"twitter:image\" content=\"{$img}\">\n"
			. "\t<meta name=\"twitter:image:alt\" content=\"" . htmlspecialchars($brand, ENT_QUOTES) . "\">";
		return $out;
	}
}

if (!function_exists('seo_jsonld')) {
	function seo_jsonld(array $nodes): string
	{
		$graph = ['@context' => 'https://schema.org', '@graph' => array_values($nodes)];
		return '<script type="application/ld+json">' . "\n"
			. json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_LINE_TERMINATORS)
			. "\n</script>";
	}
}

if (!function_exists('seo_organization_jsonld')) {
	function seo_organization_jsonld(): array
	{
		$base = rtrim(seo_config('base_url'), '/');
		$a = seo_config('address');
		return [
			[
				'@type' => ['Organization', 'LocalBusiness'],
				'@id' => $base . '#organization',
				'name' => seo_config('brand'),
				'alternateName' => array_merge([seo_config('brand_en')], (array)seo_config('brand_alt')),
				'legalName' => 'ООО "ФАВОРИТ-СТРОЙ-СНАБ"',
				'url' => $base . '/',
				'logo' => ['@type' => 'ImageObject', 'url' => $base . '/public/assets/image/logo/logo.svg', 'width' => 512, 'height' => 512],
				'image' => ['@type' => 'ImageObject', 'url' => $base . '/public/assets/image/og/og-home-1200x630.jpg', 'width' => 1200, 'height' => 630],
				'description' => 'ФАВОРИТ-СТРОЙ-СНАБ — производство и монтаж быстровозводимых зданий (склады, ангары, АБК, общежития), капитальный ремонт, грузоперевозки и аренда спецтехники. Москва и Московская область, работаем по всей России.',
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
		$base = rtrim(seo_config('base_url'), '/');
		$url = seo_url();
		if ($url === '/' || $url === '/404') return null;

		$map = [
			'/uslugi' => 'Услуги',
			'/portfolio' => 'Портфолио объектов',
			'/politika' => 'Политика конфиденциальности',
			'/soglasie' => 'Согласие на обработку данных',
			'/search' => 'Поиск по сайту',
		];
		$items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => $base . '/']];

		if (preg_match('#^/services/([^/]+)$#', $url)) {
			$items[] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'Услуги', 'item' => $base . '/uslugi'];
		}
		$name = $map[$url] ?? null;
		if (!$name && preg_match('#^/services/([^/]+)$#', $url, $m)) {
			$s = seo_service($m[1]);
			$name = $s['title'] ?? $m[1];
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

if (!function_exists('seo_service_jsonld')) {
	function seo_service_jsonld(string $slug): ?array
	{
		$s = seo_service($slug);
		if (!$s) return null;
		$base = rtrim(seo_config('base_url'), '/');
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
		$out = '';
		if ($ym !== '') {
			$out .= <<<HTML
			<!-- Yandex.Metrika -->
			<script type="text/javascript">
				(function (m, e, t, r, i, k, a) { m[i] = m[i] || function () { (m[i].a = m[i].a || []).push(arguments) };
					m[i].l = 1 * new Date(); for (var j = 0; j < e.length; j++) { k = e[j]; if (!m[i].k) { a = document.createElement("script"), a.async = 1, a.src = t + "?" + (m[i].v = 2), document.head.appendChild(a); } } })
					(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
				ym({$ym}, "init", { clickmap: true, trackLinks: true, accurateTrackBounce: true, webvisor: true, ecommerce: false });
			</script>
			<noscript><div><img src="https://mc.yandex.ru/watch/{$ym}" style="position:absolute;left:-9999px" alt="" /></div></noscript>
			HTML;
		}
		if ($ga4 !== '') {
			$out .= "\n\t\t<!-- GA4 -->\n\t\t<script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga4}\"></script>
			<script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '{$ga4}');</script>";
		}
		if ($gtm !== '') {
			$out .= "\n\t\t<!-- GTM -->\n\t\t<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{$gtm}');</script>";
		}
		return $out;
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
