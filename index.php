<?php
/**
 * Front controller — тонкая точка входа (как у pkvartira).
 * Вся маршрутизация — setting/Route/routes.php, диспетчер — App\Models\Router\Routes.
 */

declare(strict_types=1);

// --- DEV-режим (кэш выключен на время работ): env FSS_ENV=dev или файл .dev в корне ---
// ! Перед продом удалить .dev (иначе кэша не будет и на проде)
define('FSS_DEV', (getenv('FSS_ENV') ?: '') === 'dev' || is_file(__DIR__ . '/.dev'));
if (FSS_DEV && function_exists('opcache_reset')) {
	@opcache_reset();
}

// --- Composer (PSR-4: App\ → app/, Setting\ → setting/) + fallback без него ---
$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
	require_once $autoload;
}
spl_autoload_register(function (string $class): void {
	foreach (['App\\' => __DIR__ . '/app/', 'Setting\\' => __DIR__ . '/setting/'] as $prefix => $dir) {
		if (str_starts_with($class, $prefix)) {
			$file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
			if (is_file($file)) {
				require_once $file;
			}
			return;
		}
	}
});

// --- Gzip через PHP, если mod_deflate недоступен ---
if (!ob_get_level() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
	ob_start('ob_gzhandler');
} elseif (!ob_get_level()) {
	ob_start();
}

require_once __DIR__ . '/app/bootstrap.php';

// POST-формы: обрабатываем на любой странице (виджет в футере есть везде),
// а не только на главной/услугах. Вывод буферизован, заголовки ещё не отправлены.
$mailHandler = __DIR__ . '/app/Mail/component_mail.php';
if (is_file($mailHandler)) {
	require_once $mailHandler;
}

require_once __DIR__ . '/setting/Route/routes.php';

use App\Models\Router\Routes;

Routes::dispatch();
