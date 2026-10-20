<?php

declare(strict_types=1);

namespace App\Models\Router;

/**
 * Единый обработчик фатальных ошибок.
 *
 * Зачем: ранее "Failed opening required ... PageController.php" (файл исчез на мгновение
 * во время git checkout / правки редактором) давал белый экран + fatal в лог.
 * Теперь любая необработанная ошибка отдаёт нормальную страницу 500 с кодом,
 * а детали (сообщение, файл, строка) видны только в DEV-режиме.
 */
final class ErrorHandler
{
	private static bool $handled = false;

	public static function register(): void
	{
		set_exception_handler([self::class, 'handleException']);
		register_shutdown_function([self::class, 'handleShutdown']);
	}

	public static function handleException(\Throwable $e): void
	{
		self::render($e->getMessage(), $e->getFile() . ':' . $e->getLine());
	}

	public static function handleShutdown(): void
	{
		$e = error_get_last();
		if ($e === null) {
			return;
		}
		$fatal = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
		if (!in_array($e['type'], $fatal, true)) {
			return;
		}
		self::render($e['message'], $e['file'] . ':' . $e['line']);
	}

	private static function render(string $message, string $where): void
	{
		if (self::$handled) {
			return;
		}
		self::$handled = true;

		// Гасим уже выведенный мусор и отключаем "сырой" вывод PHP (детали — в нашей странице).
		while (ob_get_level() > 0) {
			@ob_end_clean();
		}
		@ini_set('display_errors', '0');
		@ini_set('html_errors', '0');

		if (!headers_sent()) {
			http_response_code(500);
			header('Content-Type: text/html; charset=utf-8', true);
			header('X-Robots-Tag: noindex, nofollow', true);
			header('Retry-After: 60', true);
		}

		$view = dirname(__DIR__) . '/Router/view/500/500.php';
		$fss_error = [
			'message' => $message,
			'where' => $where,
			'dev' => defined('FSS_DEV') && FSS_DEV === true,
		];
		if (is_file($view)) {
			require $view;
			return;
		}
		echo '500 Internal Server Error';
	}
}
