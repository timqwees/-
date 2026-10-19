<?php
/**
 * Bootstrap — грузит SEO-библиотеку и фиксирует файл текущей страницы.
 * Маршруты и 301 — setting/Route/routes.php, заголовки — SecurityHeaders.
 * Безопасен при прямом вызове шаблона (wicked URLs всё равно отдадут контент).
 */

if (defined('FSS_BOOT')) {
	return;
}
define('FSS_BOOT', microtime(true));

// Загружаем .env через vlucas/phpdotenv (как в pkvartira)
$envPath = dirname(__DIR__, 1);
if (file_exists($envPath . '/.env')) {
	$dotenv = Dotenv\Dotenv::createImmutable($envPath);
	$dotenv->load();
}

// Какой файл-страница нас подключил (фронт-контроллер и контроллер игнорируем,
// иначе canonical уедет не туда; PageController кладёт путь сам)
$__caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'] ?? '';
if ($__caller !== '' && !isset($GLOBALS['fss_page_file'])) {
	$__real = realpath($__caller);
	$__index = realpath(__DIR__ . '/../index.php');
	$__ctrl = realpath(__DIR__ . '/Controllers/PageController.php');
	if ($__real && $__real !== $__index && $__real !== $__ctrl) {
		$GLOBALS['fss_page_file'] = $__caller;
	}
}
unset($__caller, $__real, $__index, $__ctrl);

require_once __DIR__ . '/Seo/seo.php';

// Глобальный обработчик форм (должен быть до роутинга, как в pkvartira)
require_once __DIR__ . '/Mail/component_mail.php';

if (!headers_sent()) {
	header_remove('X-Powered-By');
	header_remove('Server');
}
// Сессии (формы/антибот) не должны перебивать Cache-Control владельца — SecurityHeaders
ini_set('session.cache_limiter', '');
// Автоверсия статики: захардкоженный ?v=20261009 в шаблонах заменяем живым ?v= по mtime,
// иначе браузер кэширует CSS/JS и не видит правок. Плюс добавляем ?v к main.js без версии.
if (!defined('FSS_ASSET_VER')) {
	$__mt = @filemtime(__DIR__ . '/../public/assets/distCSS/premium.css') ?: time();
	define('FSS_ASSET_VER', date('YmdHi', $__mt));
	unset($__mt);
	ob_start(function ($__html) {
		$__html = str_replace('?v=20261009', '?v=' . FSS_ASSET_VER, $__html);
		$__html = str_replace('/public/assets/distJS/main.js"', '/public/assets/distJS/main.js?v=' . FSS_ASSET_VER . '"', $__html);
		$__html = str_replace("/public/assets/distJS/main.js'", '/public/assets/distJS/main.js?v=' . FSS_ASSET_VER . "'", $__html);
		return $__html;
	});
}
