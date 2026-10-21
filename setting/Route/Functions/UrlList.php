<?php

declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * /pages — plain-text список всех URL сайта (для Screaming Frog и ручных проверок).
 */
final class UrlList
{
	public static function output(): void
	{
		$base = rtrim((string)seo_config('base_url'), '/');
		$root = dirname(__DIR__, 3);
		header('Content-Type: text/plain; charset=utf-8');
		if (SecurityHeaders::isDev()) {
			SecurityHeaders::sendNoCache();
		} else {
			header('Cache-Control: public, max-age=3600');
		}
		$urls = ['/', '/uslugi', '/portfolio', '/about', '/company', '/contacts', '/politika', '/soglasie', '/search', '/search/blog'];
		foreach (glob($root . '/public/pages/services/*/index.php') ?: [] as $file) {
			$urls[] = '/services/' . basename(dirname((string)$file));
		}
		// Блог: хаб и все статьи
		$urls[] = '/blog';
		$blogPosts = function_exists('seo_blog_posts') ? seo_blog_posts() : [];
		foreach (array_keys((array)$blogPosts) as $postSlug) {
			$urls[] = '/blog/' . $postSlug;
		}
		// Детальные страницы объектов
		$projectsFile = $root . '/app/Data/projects.php';
		if (is_file($projectsFile)) {
			foreach (array_keys((array)(require $projectsFile)) as $slug) {
				$urls[] = '/portfolio/' . $slug;
			}
		}
		// Гео-посадочные
		$geoFile = $root . '/app/Data/geo.php';
		if (is_file($geoFile)) {
			$geo = (array)(require $geoFile);
			foreach ((array)($geo['priority_services'] ?? []) as $svcSlug) {
				foreach ((array)($geo['geo'] ?? []) as $geoKey => $geoData) {
					if (!seo_geo_allows((array)$geoData, (string)$svcSlug)) {
						continue;
					}
					$urls[] = '/services/' . $svcSlug . '/' . $geoKey;
				}
			}
		}
		$urls = array_values(array_unique($urls));
		sort($urls);
		foreach ($urls as $u) {
			echo $base . $u . "\n";
		}
	}
}
