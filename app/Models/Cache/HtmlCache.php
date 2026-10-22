<?php

declare(strict_types=1);

namespace App\Models\Cache;

/**
 * Полностраничный кэш HTML (TTFB).
 *
 * Зачем: HTML не кэшируется браузером и CDN (в разметке есть per-render
 * метка времени _ts), поэтому каждый запрос уходит в PHP. Здесь рендер
 * сохраняется на диск и отдаётся за ~1-2 мс.
 *
 * Безопасность:
 *  - кэшируются только GET-запросы без query-строки;
 *  - запросы с cookie (сессия) НЕ кэшируются — чтобы после отправки формы
 *    отработал PRG-алерт «заявка принята» из bootstrap;
 *  - метка времени _ts перевыпускается на каждый ответ, поэтому антибот
 *    (защита «слишком быстро») работает корректно и для кэшированных страниц;
 *  - инвалидация — по TTL (300 с) и по «сигнатуре» данных/шаблонов.
 */
final class HtmlCache
{
	private const TTL = 300;
	private const DIR = 'pages/';

	/** @var array<string,string> */
	private static array $notCacheable = [
		'/search',
		'/search-blog',
		'/send',
		'/send/email',
		'/api/',
	];

	/** Включён ли кэш. В DEV — только по явному FSS_CACHE=1. */
	public static function enabled(): bool
	{
		$dev = defined('FSS_DEV') && FSS_DEV === true;
		if ($dev) {
			return (getenv('FSS_CACHE') ?: '') === '1';
		}
		return true;
	}

	/** Путь запроса без query-строки. */
	private static function path(): string
	{
		$uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
		$cut = strcspn($uri, '?#');
		return $cut > 0 ? substr($uri, 0, $cut) : '/';
	}

	/** Ключ кэша или null, если запрос кэшировать нельзя. */
	public static function key(): ?string
	{
		if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
			return null;
		}
		if (!empty($_SERVER['QUERY_STRING'])) {
			return null;
		}
		// Сессия или служебные cookie — живой рендер (PRG-алерты, персональные состояния).
		if (!empty($_COOKIE[session_name()])) {
			return null;
		}
		foreach (array_keys($_COOKIE) as $ck) {
			if (str_starts_with((string)$ck, 'fss_form_')) {
				return null;
			}
		}
		$path = self::path();
		foreach (self::$notCacheable as $prefix) {
			if (str_starts_with($path, $prefix)) {
				return null;
			}
		}
		// Только HTML-роуты: файлы с расширением (xml/json/txt/yaml/webmanifest…)
		// отдаются со своим Content-Type и не должны попадать под text/html.
		$last = basename($path);
		if (str_contains($last, '.')) {
			return null;
		}
		$host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
		return $host . $path;
	}

	/** Сигнатура данных и шаблонов: изменение любого файла инвалидирует кэш. */
	private static function signature(): string
	{
		static $sig = null;
		if ($sig !== null) {
			return $sig;
		}
		$root = dirname(__DIR__, 3);
		$files = [
			$root . '/app/Config/site.php',
			$root . '/app/Data/projects.php',
			$root . '/app/Data/services.php',
			$root . '/app/Data/blog.php',
			$root . '/app/Data/geo.php',
		];
		// Код приложения (app/ + setting/): правка PHP-файла тоже должна
		// сбрасывать кэш — иначе после изменения шаблонизатора или логики
		// браузер продолжит получать старый HTML до истечения TTL.
		foreach (['/app', '/setting'] as $dir) {
			$it = @scandir($root . $dir);
			if ($it === false) {
				continue;
			}
			foreach ($it as $f) {
				if (substr($f, -4) === '.php') {
					$files[] = $root . $dir . '/' . $f;
				}
			}
		}
		// Вложенные каталоги кода (app/Models, app/Controllers, setting/Route/…)
		foreach ([
			'/app/Models', '/app/Models/Cache', '/app/Models/Router', '/app/Models/Network',
			'/app/Controllers', '/app/Mail', '/app/Seo',
			'/setting/Route', '/setting/Route/Functions',
		] as $dir) {
			$it = @scandir($root . $dir);
			if ($it === false) {
				continue;
			}
			foreach ($it as $f) {
				if (substr($f, -4) === '.php') {
					$files[] = $root . $dir . '/' . $f;
				}
			}
		}
		// Шаблоны страниц и общие компоненты — рекурсивно: правка вёрстки любой
		// страницы (в т.ч. /public/pages/contacts/index.php или
		// /public/pages/services/<slug>/index.php) обязана сбрасывать кэш —
		// иначе после правки браузер до конца TTL получает старый HTML.
		foreach (['/public/pages', '/public/components'] as $dir) {
			$path = $root . $dir;
			if (!is_dir($path)) {
				continue;
			}
			$it = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::LEAVES_ONLY
			);
			foreach ($it as $file) {
				if ($file instanceof \SplFileInfo && $file->isFile() && strtolower($file->getExtension()) === 'php') {
					$files[] = $file->getPathname();
				}
			}
		}
		$max = 0;
		foreach ($files as $f) {
			$mt = @filemtime($f);
			if ($mt !== false && $mt > $max) {
				$max = $mt;
			}
		}
		return $sig = (string)$max;
	}

	private static function file(string $key): string
	{
		return \App\Models\Cache\Path::file(self::DIR . sha1($key) . '.html');
	}

	/** Тело из кэша или null. */
	public static function get(?string $key): ?string
	{
		if ($key === null) {
			return null;
		}
		$file = self::file($key);
		if ($file === '' || !is_file($file)) {
			return null;
		}
		$age = time() - (int)@filemtime($file);
		if ($age > self::TTL) {
			return null;
		}
		$raw = (string)@file_get_contents($file);
		if ($raw === '' || !str_starts_with($raw, "\x00FSS1 ")) {
			return null;
		}
		$parts = explode("\n", $raw, 2);
		$meta = $parts[0];
		if (substr($meta, 0, 6) !== "\x00FSS1 ") {
			return null;
		}
		$stored = trim(substr($meta, 6));
		if ($stored !== self::signature()) {
			return null;
		}
		return $parts[1] ?? '';
	}

	/** Сохранить отрендеренный HTML. */
	public static function put(?string $key, string $body): void
	{
		if ($key === null || $body === '') {
			return;
		}
		$file = self::file($key);
		if ($file === '') {
			return;
		}
		$dir = dirname($file);
		if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
			return;
		}
		$payload = "\x00FSS1 " . self::signature() . "\n" . $body;
		@file_put_contents($file, $payload, LOCK_EX);
	}

	/**
	 * Актуализировать per-request метку времени (_ts) и подпись (_csrf)
	 * в закэшированном HTML.
	 *
	 * Иначе повторная отправка формы с закэшированной страницы выглядела бы
	 * как «отправлено мгновенно» (антибот-фильтр).
	 *
	 * ВАЖНО: перевыпуск только _ts ломает CSRF-подпись. _csrf — это
	 * HMAC(secret, 'form|{_ts}'), поэтому вместе с _ts обязана
	 * перевыпускаться и подпись. Иначе любая форма, отправленная с
	 * закэшированной страницы (то есть у всех посетителей, кроме первого
	 * за окно кэша), отбивалась бы проверкой «безопасность не пройдена».
	 */
	public static function refreshTimestamp(string $body): string
	{
		$now = (string)(int)(microtime(true) * 1000);
		$body = (string)preg_replace_callback(
			'/name="_ts" value="\d+"/',
			static fn(array $m): string => 'name="_ts" value="' . $now . '"',
			$body
		);

		if (!function_exists('fss_secret')) {
			return $body;
		}

		$csrf = hash_hmac('sha256', 'form|' . $now, fss_secret());

		return (string)preg_replace_callback(
			'/(<input[^>]*\bname="_csrf"[^>]*\bvalue=")[^"]*(")/',
			static fn(array $m): string => $m[1] . $csrf . $m[2],
			$body
		);
	}

	/** Отдать ответ из кэша с ETag/Cache-Control. */
	public static function send(string $body): void
	{
		$body = self::refreshTimestamp($body);
		$etag = '"' . md5($body) . '"';
		// Заголовки безопасности (CSP, HSTS, X-Frame-Options) — те же, что и при живом рендере
		\Setting\Route\Functions\SecurityHeaders::sendSecurity();
		header('Content-Type: text/html; charset=utf-8');
		header('Cache-Control: public, max-age=0, s-maxage=600, stale-while-revalidate=86400', true);
		header('X-FSS-Cache: HIT', true);
		header('ETag: ' . $etag, true);
		if (trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
			http_response_code(304);
			exit;
		}
		echo $body;
	}
}
