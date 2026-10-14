<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Router\Routes;

/**
 * Страничный контроллер — тонкие действия поверх шаблонов public/pages/*.
 * Вся SEO-логика живёт в шаблонах + app/Seo/seo.php, здесь только выбор файла.
 */
class PageController
{
	private static function view(string $rel): string
	{
		return dirname(__DIR__, 2) . '/public/pages/' . ltrim($rel, '/');
	}

	private static function show(string $rel, array $vars = []): void
	{
		$file = self::view($rel);
		$GLOBALS['fss_page_file'] = $file;
		Routes::auto_element($file, $vars);
	}

	public static function home(): void
	{
		self::show('home.php');
	}

	public static function uslugi(): void
	{
		self::show('uslugi/uslugi.php');
	}

	public static function portfolio(): void
	{
		self::show('portfolio.php');
	}


	public static function politika(): void
	{
		self::show('politic/index.php');
	}

	public static function soglasie(): void
	{
		self::show('agree/index.php');
	}

	public static function search(): void
	{
		self::show('search.php');
	}

	public static function service(string $slug): void
	{
		$file = self::view('services/' . $slug . '/index.php');
		if (!is_file($file)) {
			Routes::error_404('/services/' . $slug);
			return;
		}
		$_GET['service'] = $slug;
		$GLOBALS['fss_page_file'] = $file;
		Routes::auto_element($file);
	}
}
