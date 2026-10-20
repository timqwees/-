<?php
/**
 * Bootstrap — единая точка инициализации (вызывается из index.php и страниц).
 * Идемпотентен: повторный require ничего не делает.
 */

declare(strict_types=1);

if (defined('FSS_BOOT')) {
	return;
}
define('FSS_BOOT', microtime(true));

// --- Загружаем .env через vlucas/phpdotenv ---
$__envPath = dirname(__DIR__);
if (file_exists($__envPath . '/.env')) {
	$dotenv = Dotenv\Dotenv::createImmutable($__envPath);
	$dotenv->load();
}
unset($__envPath);

// --- Фиксируем файл страницы для canonical (PageController кладёт сам) ---
$__caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0]['file'] ?? '';
if ($__caller !== '' && !isset($GLOBALS['fss_page_file'])) {
	$__real = realpath($__caller);
	$__index = realpath(dirname(__DIR__) . '/index.php');
	$__ctrl = realpath(__DIR__ . '/Controllers/PageController.php');
	if ($__real && $__real !== $__index && $__real !== $__ctrl) {
		$GLOBALS['fss_page_file'] = $__caller;
	}
}
unset($__caller, $__real, $__index, $__ctrl);

// --- SEO-библиотека ---
require_once __DIR__ . '/Seo/seo.php';

// --- Email константы (QweesCore формат) ---
if (!defined('EMAIL')) {
	define('EMAIL', (string)($_ENV['EMAIL'] ?? $_SERVER['EMAIL'] ?? getenv('EMAIL') ?? ''));
	define('EMAIL_PASSWORD', (string)($_ENV['EMAIL_PASSWORD'] ?? $_SERVER['EMAIL_PASSWORD'] ?? getenv('EMAIL_PASSWORD') ?? ''));
	define('EMAIL_PORT', (int)($_ENV['EMAIL_PORT'] ?? $_SERVER['EMAIL_PORT'] ?? getenv('EMAIL_PORT') ?? 587));
	define('EMAIL_NICKNAME', (string)($_ENV['EMAIL_NICKNAME'] ?? $_SERVER['EMAIL_NICKNAME'] ?? getenv('EMAIL_NICKNAME') ?? 'ФСС.РУС'));
	define('EMAIL_TO', (string)($_ENV['EMAIL_TO'] ?? $_SERVER['EMAIL_TO'] ?? getenv('EMAIL_TO') ?? 'order@фсс.рус'));
}

// --- Сессия + PRG-алерты после отправки форм ---
// Ленивый старт: файл сессии создаётся только если браузер уже прислал cookie.
// Анонимные GET-запросы не пишут на диск session-файл → меньше I/O на каждый запрос.
if (session_status() === PHP_SESSION_NONE && !empty($_COOKIE[session_name()])) {
	@session_start();
}

if (!empty($_SESSION['fss_form_success'])) {
	$__success = $_SESSION['fss_form_success'];
	unset($_SESSION['fss_form_success']);

	$__type = $__success['type'] ?? 'form';
	$__mailOk = $__success['mail'] ?? false;
	$__message = trim((string)($__success['message'] ?? ''));

	if ($__mailOk) {
		if (!headers_sent()) {
			$__secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
				|| (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
			setcookie('fss_form_' . $__type . '_success', '1', [
				'expires' => time() + 300,
				'path' => '/',
				'secure' => $__secure,
				'httponly' => true,
				'samesite' => 'Lax',
			]);
		}
		\App\Mail\Security::pushScript('if(window.dataLayer) dataLayer.push({event:"form_success", form_type:"' . addslashes($__type) . '"});');
		\App\Mail\Security::showAlert('success', $__message !== '' ? $__message : 'Вы успешно оставили заявку! Мы свяжемся с вами в ближайшее время.');
	} else {
		\App\Mail\Security::showAlert('error', $__message !== '' ? $__message : 'Не удалось отправить заявку. Пожалуйста, позвоните нам +7 938 018-00-06 или попробуйте позже.');
	}
	unset($__success, $__type, $__mailOk, $__message);
}

// --- Заголовки ---
if (!headers_sent()) {
	header_remove('X-Powered-By');
	header_remove('Server');
}

// --- Автоверсия статики (cache busting) ---
// Финализация HTML (версия ассетов + размеры картинок) выполняется в index.php
// через App\Models\Router\Html::finalize() — так страница не зависит от
// output-buffer callback, который ломает аварийный рендер (500).
if (!defined('FSS_ASSET_VER')) {
	$__mt = @filemtime(dirname(__DIR__) . '/public/assets/distCSS/premium.css') ?: time();
	define('FSS_ASSET_VER', date('YmdHi', $__mt));
	unset($__mt);
}
