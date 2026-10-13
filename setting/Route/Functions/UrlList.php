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
		$base = rtrim(seo_config('base_url'), '/');
		$root = dirname(__DIR__, 3);
		header('Content-Type: text/plain; charset=utf-8');
		if (SecurityHeaders::isDev()) {
			SecurityHeaders::sendNoCache();
		} else {
			header('Cache-Control: public, max-age=3600');
		}
		$urls = ['/', '/uslugi', '/portfolio', '/projects', '/politika', '/soglasie', '/search'];
		foreach (glob($root . '/public/pages/services/*/index.php') ?: [] as $file) {
			$urls[] = '/services/' . basename(dirname((string)$file));
		}
		sort($urls);
		foreach ($urls as $u) {
			echo $base . $u . "\n";
		}
	}
}
