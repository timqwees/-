<?php

declare(strict_types=1);

namespace App\Models\Router;

use Setting\Route\Functions\Markdown;
use Setting\Route\Functions\SecurityHeaders;

/**
 * Роутер — единственная точка принятия решений о маршрутах (как у pkvartira).
 *
 * - Routes::get/post($path, $callback) — регистрация. Плейсхолдеры: /services/{slug}
 * - Routes::dispatch() — сопоставление метода+URI и вызов обработчика, иначе 404
 * - Routes::auto_element($file) — отдать PHP-шаблон с заголовками + 304 + markdown-negotiation
 * - Обработчик: замыкание, [Класс, метод] или 'ИмяКласса@метод'
 */
class Routes
{
	/** @var array<string, array<string, mixed>> */
	private static array $patterns = [];

	public static function addRoute(string $method, string $path, $callback): void
	{
		$method = strtoupper($method);
		// {name} → один сегмент, {rest:.+} → остаток пути
		$pattern = preg_replace('~\{([a-zA-Z0-9_]+):(.+?)\}~', '(?P<$1>$2)', $path) ?? $path;
		$pattern = preg_replace('~\{([a-zA-Z0-9_]+)\}~', '(?P<$1>[^/]+)', $pattern) ?? $pattern;
		$pattern = '~^' . $pattern . '$~';
		self::$patterns[$method][$pattern] = $callback;
	}

	public static function get(string $path, $callback): void
	{
		self::addRoute('GET', $path, $callback);
	}

	public static function post(string $path, $callback): void
	{
		self::addRoute('POST', $path, $callback);
	}

	public static function redirect(string $url, int $code = 301): void
	{
		$qs = $_SERVER['QUERY_STRING'] ?? '';
		if ($qs !== '' && !str_contains($url, '?')) {
			$url .= '?' . $qs;
		}
		header('Location: ' . $url, true, $code);
		exit;
	}

	public static function dispatch(): void
	{
		$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
		// HEAD обслуживаем как GET (совпадение маршрутов), тело обрежет SAPI
		if ($method === 'HEAD') {
			$method = 'GET';
		}
		$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
		$uri = '/' . trim(rawurldecode($uri), '/');
		$uri = rtrim($uri, '/') === '' ? '/' : rtrim($uri, '/');

		// POST-формы обрабатываются в index.php до роутинга (component_mail).
		// Если для URL нет отдельного POST-маршрута — рендерим ту же страницу как GET
		// (иначе виджет в футере на /about, /blog и т.п. отдавал 404 после отправки).
		$methods = [$method];
		if ($method === 'POST') {
			$methods[] = 'GET';
		}

		foreach ($methods as $m) {
			foreach (self::$patterns[$m] ?? [] as $pattern => $callback) {
				if (!preg_match($pattern, $uri, $m2)) {
					continue;
				}
				$params = [];
				foreach ($m2 as $k => $v) {
					if (is_string($k)) {
						$params[$k] = urldecode((string)$v);
					}
				}
				self::execute($callback, $params);
				return;
			}
		}

		self::error_404($uri);
	}

	/**
	 * @param callable|array|string $callback
	 * @param array<string,string> $params
	 */
	private static function execute($callback, array $params): void
	{
		if (is_array($callback) && isset($callback[0], $callback[1])) {
			[$class, $action] = $callback;
			$obj = is_string($class) ? new $class() : $class;
			call_user_func_array([$obj, $action], $params);
			return;
		}
		if (is_callable($callback)) {
			call_user_func_array($callback, $params);
			return;
		}
		self::error_404('bad-callback');
	}

	/**
	 * Отдать PHP-шаблон: security-заголовки + ETag/Last-Modified/304,
	 * markdown-negotiation для AI-клиентов (Accept: text/markdown).
	 */
	public static function auto_element(string $path, array $vars = []): void
	{
		if (!is_file($path)) {
			self::error_404($path);
			return;
		}
		SecurityHeaders::sendForHtml($path);
		if ($vars !== []) {
			extract($vars, EXTR_SKIP);
		}
		if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'
			&& stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/markdown') !== false) {
			header('Content-Type: text/markdown; charset=utf-8', true);
			$base = function_exists('seo_config') ? (string)seo_config('base_url') : '';
			ob_start();
			require $path;
			$html = (string)ob_get_clean();
			echo Markdown::toMarkdown($html, $base);
			return;
		}
		require $path;
	}

	public static function error_404(string $path): void
	{
		$view = dirname(__DIR__) . '/Router/view/404/404.php';
		if (!is_file($view)) {
			// app/Models/Router → корень проекта = dirname(__DIR__, 3).
			// (Раньше было 4 — уводило на уровень выше корня, фолбэк не находился.)
			$view = dirname(__DIR__, 3) . '/public/404.php';
		}
		http_response_code(404);
		SecurityHeaders::sendSecurity('noindex, follow');
		if (is_file($view)) {
			require $view;
			return;
		}
		echo '404 Not Found';
	}
}
