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

	public static function project(string $slug): void
	{
		$file = self::view('projects/project.php');
		$all = file_exists(dirname(__DIR__, 2) . '/app/Data/projects.php')
			? (require dirname(__DIR__, 2) . '/app/Data/projects.php')
			: [];
		if (!is_file($file) || !isset($all[$slug])) {
			Routes::error_404('/portfolio/' . $slug);
			return;
		}
		$GLOBALS['fss_project_slug'] = $slug;
		$GLOBALS['fss_canonical_path'] = '/portfolio/' . $slug;
		$GLOBALS['fss_page_file'] = $file;
		Routes::auto_element($file);
	}

	public static function about(): void
	{
		self::show('about/index.php');
	}

	/**
	 * Страница компании (/company) — реквизиты, руководство, направления
	 * деятельности и выполненные объекты (E-E-A-T, единый источник — app/Data/company.php).
	 */
	public static function company(): void
	{
		self::show('company/index.php');
	}

	/** Отдельная страница «Контакты» (/contacts) — телефоны, адрес, реквизиты, заявка. */
	public static function contacts(): void
	{
		self::show('contacts/index.php');
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

	public static function serviceGeo(string $slug, string $geo): void
	{
		$file = self::view('services/' . $slug . '/index.php');
		$g = function_exists('seo_geo') ? seo_geo() : ['geo' => [], 'priority_services' => []];
		$priority = (array)($g['priority_services'] ?? []);
		if (!is_file($file) || !isset($g['geo'][$geo]) || !in_array($slug, $priority, true)
			|| !seo_geo_allows((array)$g['geo'][$geo], $slug)) {
			Routes::error_404('/services/' . $slug . '/' . $geo);
			return;
		}
		$_GET['service'] = $slug;
		$GLOBALS['fss_service_geo'] = $geo;
		$GLOBALS['fss_canonical_path'] = '/services/' . $slug . '/' . $geo;
		$GLOBALS['fss_page_file'] = $file;
		Routes::auto_element($file);
	}

	public static function blog(): void
	{
		$GLOBALS['fss_page_file'] = self::view('blog/list.php');
		Routes::auto_element($GLOBALS['fss_page_file']);
	}

	public static function blogPost(string $slug): void
	{
		$GLOBALS['fss_page_file'] = self::view('blog/post.php');
		$_GET['post'] = $slug;
		Routes::auto_element($GLOBALS['fss_page_file']);
	}

	public static function searchBlog(): void
	{
		self::show('search-blog/index.php');
	}
}
