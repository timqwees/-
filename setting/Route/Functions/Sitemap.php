<?php

declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * Карта сайта: /sitemap.xml (urlset) + /sitemap-index.xml.
 * Источник — реально существующие шаблоны public/pages/*, lastmod — filemtime.
 */
final class Sitemap
{
	/** @return list<array{0:string,1:string,2:string}> */
	private static function pages(): array
	{
		$root = dirname(__DIR__, 3);
		$services = file_exists($root . '/app/Data/services.php')
			? (require $root . '/app/Data/services.php')
			: [];
		$blogPosts = function_exists('seo_blog_posts') ? seo_blog_posts() : [];
		$pages = [
			['/', '1.0', 'weekly'],
			['/blog', '0.7', 'weekly'],
			['/uslugi', '0.9', 'weekly'],
			['/portfolio', '0.8', 'weekly'],
			['/about', '0.5', 'monthly'],
			['/contacts', '0.6', 'monthly'],
			// /politika и /soglasie закрыты noindex — в sitemap им не место
			// (противоречие «noindex, но в карте сайта» сбивает краулер).
		];
		foreach (array_keys(is_array($services) ? $services : []) as $slug) {
			if (is_file($root . '/public/pages/services/' . $slug . '/index.php')) {
				$pages[] = ['/services/' . $slug, '0.8', 'monthly'];
			}
		}
		// Гео-посадочные НЧ (/services/{slug}/{geo}) — только приоритетные услуги
		$geoData = file_exists($root . '/app/Data/geo.php') ? (require $root . '/app/Data/geo.php') : [];
		$geoList = (array)($geoData['geo'] ?? []);
		foreach ((array)($geoData['priority_services'] ?? []) as $slug) {
			if (!is_file($root . '/public/pages/services/' . $slug . '/index.php')) {
				continue;
			}
			foreach ($geoList as $gk => $gdata) {
				// У города может быть ограничение по услугам (только те, где есть работы)
				if (!seo_geo_allows((array)$gdata, (string)$slug)) {
					continue;
				}
				$pages[] = ['/services/' . $slug . '/' . $gk, '0.6', 'monthly'];
			}
		}
		foreach (array_keys($blogPosts) as $slug) {
			$pages[] = ['/blog/' . $slug, '0.6', 'monthly'];
		}
		// Детальные страницы объектов (/portfolio/{slug})
		$projectsFile = $root . '/app/Data/projects.php';
		if (is_file($projectsFile)) {
			foreach (array_keys((array)(require $projectsFile)) as $slug) {
				$pages[] = ['/portfolio/' . $slug, '0.7', 'monthly'];
			}
		}
		return $pages;
	}

	private static function fileOf(string $loc): ?string
	{
		$root = dirname(__DIR__, 3);
		$map = [
			'/' => $root . '/public/pages/home.php',
			'/blog' => $root . '/public/pages/blog/list.php',
			'/uslugi' => $root . '/public/pages/uslugi/uslugi.php',
			'/portfolio' => $root . '/public/pages/portfolio.php',
			'/about' => $root . '/public/pages/about/index.php',
			'/contacts' => $root . '/public/pages/contacts/index.php',
		];
		if (array_key_exists($loc, $map)) {
			return $map[$loc];
		}
		if (preg_match('#^/services/([a-z0-9_\-]+)$#', $loc, $m)) {
			return $root . '/public/pages/services/' . $m[1] . '/index.php';
		}
		if (preg_match('#^/services/([a-z0-9_\-]+)/[a-z0-9_\-]+$#', $loc, $m)) {
			return $root . '/public/pages/services/' . $m[1] . '/index.php';
		}
		if (preg_match('#^/blog/([^/]+)$#', $loc, $m)) {
			return $root . '/public/pages/blog/post.php';
		}
		if (preg_match('#^/portfolio/([^/]+)$#', $loc, $m)) {
			return $root . '/public/pages/projects/project.php';
		}
		return null;
	}

	private static function emit(string $body): void
	{
		SecurityHeaders::emitCached($body, 'application/xml; charset=utf-8');
	}

	public static function output(): void
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach (self::pages() as [$loc, $priority, $freq]) {
			$f = self::fileOf($loc);
			$lm = ($f && is_file($f)) ? date('Y-m-d', (int)filemtime($f)) : date('Y-m-d');
			$out .= "  <url><loc>{$base}{$loc}</loc><lastmod>{$lm}</lastmod><changefreq>{$freq}</changefreq><priority>{$priority}</priority></url>\n";
		}
		$out .= '</urlset>';
		self::emit($out);
	}

	public static function outputIndex(): void
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$out .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		$out .= '  <sitemap><loc>' . $base . '/sitemap.xml</loc><lastmod>' . date('Y-m-d') . '</lastmod></sitemap>' . "\n";
		$out .= '</sitemapindex>';
		self::emit($out);
	}
}
