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
		$pages = [
			['/', '1.0', 'weekly'],
			['/uslugi', '0.9', 'weekly'],
			['/portfolio', '0.8', 'weekly'],
			['/about', '0.5', 'monthly'],
			['/politika', '0.3', 'yearly'],
			['/soglasie', '0.3', 'yearly'],
		];
		foreach (array_keys(is_array($services) ? $services : []) as $slug) {
			if (is_file($root . '/public/pages/services/' . $slug . '/index.php')) {
				$pages[] = ['/services/' . $slug, '0.8', 'monthly'];
			}
		}
		return $pages;
	}

	private static function fileOf(string $loc): ?string
	{
		$root = dirname(__DIR__, 3);
		$map = [
			'/' => $root . '/public/pages/home.php',
			'/uslugi' => $root . '/public/pages/uslugi/uslugi.php',
			'/portfolio' => $root . '/public/pages/portfolio.php',
			'/about' => $root . '/public/pages/about.php',
			'/politika' => $root . '/public/pages/politic/index.php',
			'/soglasie' => $root . '/public/pages/agree/index.php',
		];
		if (array_key_exists($loc, $map)) {
			return $map[$loc];
		}
		if (preg_match('#^/services/([a-z0-9_\-]+)$#', $loc, $m)) {
			return $root . '/public/pages/services/' . $m[1] . '/index.php';
		}
		return null;
	}

	private static function emit(string $body): void
	{
		SecurityHeaders::emitCached($body, 'application/xml; charset=utf-8');
	}

	public static function output(): void
	{
		$base = rtrim(seo_config('base_url'), '/');
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
		$base = rtrim(seo_config('base_url'), '/');
		$out = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$out .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		$out .= '  <sitemap><loc>' . $base . '/sitemap.xml</loc><lastmod>' . date('Y-m-d') . '</lastmod></sitemap>' . "\n";
		$out .= '</sitemapindex>';
		self::emit($out);
	}
}
