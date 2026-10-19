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

// Helper для куки успеха формы
function setFormSuccessCookie(string $formType): void
{
	if (headers_sent()) return;
	$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
	$cookieName = 'fss_form_' . $formType . '_success';
	setcookie($cookieName, '1', [
		'expires' => time() + 300,
		'path' => '/',
		'domain' => '',
		'secure' => $secure,
		'httponly' => true,
		'samesite' => 'Lax',
	]);
}

// --- Bootstrap (inline из app/bootstrap.php) ---
if (defined('FSS_BOOT')) {
	// already booted
} else {
	define('FSS_BOOT', microtime(true));

	// Загружаем .env через vlucas/phpdotenv
	$envPath = __DIR__;
	if (file_exists($envPath . '/.env')) {
		$dotenv = Dotenv\Dotenv::createImmutable($envPath);
		$dotenv->load();
	}

	// Фиксируем файл страницы для canonical (PageController кладёт сам)
	$__caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'] ?? '';
	if ($__caller !== '' && !isset($GLOBALS['fss_page_file'])) {
		$__real = realpath($__caller);
		$__index = realpath(__DIR__ . '/index.php');
		$__ctrl = realpath(__DIR__ . '/app/Controllers/PageController.php');
		if ($__real && $__real !== $__index && $__real !== $__ctrl) {
			$GLOBALS['fss_page_file'] = $__caller;
		}
	}
	unset($__caller, $__real, $__index, $__ctrl);

	require_once __DIR__ . '/app/Seo/seo.php';

	// PRG алерты после форм (GET после 303 редиректа)
	if (session_status() === PHP_SESSION_NONE) {
		@session_start();
	}
	if (!empty($_SESSION['fss_form_success'])) {
		$success = $_SESSION['fss_form_success'];
		unset($_SESSION['fss_form_success']);
		$type = $success['type'] ?? 'form';
		$mailOk = $success['mail'] ?? false;
		if ($mailOk) {
			setFormSuccessCookie($type);
			\App\Mail\Security::pushScript('if(window.dataLayer) dataLayer.push({event:"form_success", form_type:"' . addslashes($type) . '"});');
			\App\Mail\Security::showAlert('success', 'Вы успешно оставили заявку! Мы свяжемся с вами в ближайшее время.');
		} else {
			\App\Mail\Security::showAlert('error', 'Не удалось отправить заявку. Пожалуйста, позвоните нам +7 938 018-00-06 или попробуйте позже.');
		}
	}

	if (!headers_sent()) {
		header_remove('X-Powered-By');
		header_remove('Server');
	}
	// Автоверсия статики
	if (!defined('FSS_ASSET_VER')) {
		$__mt = @filemtime(__DIR__ . '/public/assets/distCSS/premium.css') ?: time();
		define('FSS_ASSET_VER', date('YmdHi', $__mt));
		unset($__mt);
		ob_start(function ($__html) {
			$__html = str_replace('?v=20261009', '?v=' . FSS_ASSET_VER, $__html);
			$__html = str_replace('/public/assets/distJS/main.js"', '/public/assets/distJS/main.js?v=' . FSS_ASSET_VER . '"', $__html);
			$__html = str_replace("/public/assets/distJS/main.js'", '/public/assets/distJS/main.js?v=' . FSS_ASSET_VER . "'", $__html);
			return $__html;
		});
	}
}

// --- Routes ---
require_once __DIR__ . '/setting/Route/routes.php';

use App\Models\Router\Routes;

Routes::dispatch();