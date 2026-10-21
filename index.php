<?php
/**
 * Front controller — тонкая точка входа (как у pkvartira).
 * Инициализация — app/bootstrap.php, маршрутизация — setting/Route/routes.php.
 */

declare(strict_types=1);

// --- DEV-режим (кэш выключен на время работ): env FSS_ENV=dev или файл .dev в корне ---
// ! Перед продом удалить .dev (иначе кэша не будет и на проде)
define('FSS_DEV', (getenv('FSS_ENV') ?: '') === 'dev' || is_file(__DIR__ . '/.dev'));
if (FSS_DEV && function_exists('opcache_reset')) {
	// Полный сброс opcache на КАЖДЫЙ запрос обнулял всю пользу от opcache:
	// каждый файл компилировался заново (рендер 13 мс вместо 2 мс).
	// Сброс нужен только когда opcache не умеет сам следить за mtime
	// (opcache.validate_timestamps=0) — тогда без сброса висял бы старый код.
	$validate = ini_get('opcache.validate_timestamps');
	if ($validate === false || $validate === '' || $validate === '0') {
		@opcache_reset();
	}
}

// --- Composer (PSR-4: App\ → app/, Setting\ → setting/) + fallback без него ---
$autoload = __DIR__ . '/vendor/autoload.php';
if (is_file($autoload)) {
	require_once $autoload;
}
/**
 * Fallback-автозагрузчик (на случай отсутствия vendor/).
 * include_once, а не require_once: отсутствующий файл даёт warning, а не fatal.
 * Плюс 3 попытки с паузой — файл мог на мгновение исчезнуть (git checkout, правка редактором).
 */
spl_autoload_register(function (string $class): void {
	foreach (['App\\' => __DIR__ . '/app/', 'Setting\\' => __DIR__ . '/setting/'] as $prefix => $dir) {
		if (!str_starts_with($class, $prefix)) {
			continue;
		}
		$file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
		for ($attempt = 0; $attempt < 3; $attempt++) {
			if (is_file($file)) {
				$ok = @include_once $file;
				if ($ok !== false) {
					return;
				}
			}
			usleep(30000);
		}
		throw new \RuntimeException('Не удалось загрузить класс ' . $class . ' (' . $file . ')');
	}
});

// --- Gzip через PHP, если mod_deflate недоступен ---
if (!ob_get_level() && extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
	ob_start('ob_gzhandler');
} elseif (!ob_get_level()) {
	ob_start();
}

// --- Bootstrap (идемпотентен) ---
require_once __DIR__ . '/app/bootstrap.php';

// --- Ошибки: вместо fatal/белого экрана — аккуратная страница 500 ---
\App\Models\Router\ErrorHandler::register();

// --- Routes ---
require_once __DIR__ . '/setting/Route/routes.php';

use App\Models\Router\Html;
use App\Models\Router\Routes;

// --- Полностраничный кэш (TTFB) ---
// Включён в проде; в DEV — только при FSS_CACHE=1. Кэшируются только GET
// без query-строки и без сессионных cookie (см. App\Models\Cache\HtmlCache).
$__htmlCacheKey = \App\Models\Cache\HtmlCache::enabled() ? \App\Models\Cache\HtmlCache::key() : null;
if ($__htmlCacheKey !== null) {
	$__cached = \App\Models\Cache\HtmlCache::get($__htmlCacheKey);
	if ($__cached !== null) {
		\App\Models\Cache\HtmlCache::send($__cached);
		exit;
	}
}

// --- Рендер страницы + финализация HTML ---
// Буфер без callback: так при аварии ErrorHandler может очистить вывод и отдать
// чистую страницу 500 (output-buffer callback такую очистку запрещает).
ob_start();
try {
	Routes::dispatch();
} catch (\Throwable $e) {
	@ob_end_clean();
	throw $e;
}
$html = (string) ob_get_clean();
$final = Html::finalize($html);
echo $final;

if ($__htmlCacheKey !== null) {
	// Живой рендер, но страницу можно отдавать и внешнему кэшу (CDN) —
	// иначе первый ответ остался бы с no-store и кэш не наполнялся бы.
	header('Cache-Control: public, max-age=0, s-maxage=600, stale-while-revalidate=86400', true);
	header('X-FSS-Cache: MISS', true);
	\App\Models\Cache\HtmlCache::put($__htmlCacheKey, $final);
}
