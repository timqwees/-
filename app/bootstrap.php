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

// --- Автозагрузка Composer ---
// bootstrap.php могут подключать не только index.php, но и отдельные файлы
// (404-страница, шаблоны услуг). Без автозагрузки падал phpdotenv, а следом
// «Function 'seo_config' not found» — SEO-функции просто не успевали объявиться.
if (!class_exists(\Dotenv\Dotenv::class, false)) {
	$__autoload = dirname(__DIR__) . '/vendor/autoload.php';
	if (is_file($__autoload)) {
		require_once $__autoload;
	}
	unset($__autoload);
}

// --- Резолвер путей рантайм-кэша (вне каталога проекта: без var/ и временных файлов) ---
require_once __DIR__ . '/Models/Cache/Path.php';

// --- Загружаем .env через vlucas/phpdotenv ---
$__envPath = dirname(__DIR__);
if (file_exists($__envPath . '/.env') && class_exists(\Dotenv\Dotenv::class)) {
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

// --- Безопасное чтение GET-параметров ---
// $_GET['q'] может прийти массивом (?q[]=x) — приведение к строке даёт
// "Array to string conversion", а mb_substr() с массивом — фатальную ошибку 500.
if (!function_exists('fss_param')) {
	/**
	 * Скалярный GET/POST-параметр: массивы и объекты отбрасываются.
	 */
	function fss_param(string $key, string $default = ''): string
	{
		$value = $_GET[$key] ?? null;
		if (!is_scalar($value)) {
			return $default;
		}
		$value = trim((string)$value);
		return $value === '' ? $default : $value;
	}
}

// --- Секрет для подписи CSRF-токена ---
// Настраивать и переносить на сервер не нужно: секрет создаётся автоматически
// и хранится в рантайм-кэше (вне проекта). Если кэш недоступен на запись —
// используется детерминированный секрет из пути проекта и composer.lock:
// он не меняется между запросами, поэтому подписи остаются валидными
// и формы не отбиваются.
if (!function_exists('fss_secret')) {
	function fss_secret(): string
	{
		static $secret = null;
		if ($secret !== null) {
			return $secret;
		}
		$candidates = [
			\App\Models\Cache\Path::file('secret.key'),
		];
		foreach ($candidates as $path) {
			if ($path === '' || !is_file($path)) {
				continue;
			}
			$raw = trim((string)@file_get_contents($path));
			if ($raw !== '') {
				return $secret = $raw;
			}
		}
		$secret = bin2hex(random_bytes(32));
		$saved = false;
		foreach ($candidates as $path) {
			if ($path === '' || !\App\Models\Cache\Path::ensure(dirname($path))) {
				continue;
			}
			if (@file_put_contents($path, $secret, LOCK_EX) !== false) {
				@chmod($path, 0600);
				$saved = true;
				break;
			}
		}
		if (!$saved) {
			// Хранилища нет — детерминированный секрет: одинаковый во всех
			// запросах, иначе подпись CSRF не совпала бы при проверке.
			$lock = dirname(__DIR__) . '/composer.lock';
			$secret = hash('sha256', __DIR__ . '|' . (is_file($lock) ? (string)md5_file($lock) : 'no-lock'));
			error_log('[security] Рантайм-кэш недоступен на запись — используется детерминированный CSRF-секрет');
		}
		return $secret;
	}
}

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
			// Имя cookie — только [a-z0-9_], иначе 'mini-forma' давал 'fss_form_mini-forma_success',
			// а фронтенд виджета ждал 'fss_form_mini_success' и экран успеха не показывался.
			$__cookieType = preg_replace('/[^a-zA-Z0-9_]+/', '_', (string)$__type) ?: 'form';
			setcookie('fss_form_' . $__cookieType . '_success', '1', [
				'expires' => time() + 300,
				'path' => '/',
				'secure' => $__secure,
				'httponly' => true,
				'samesite' => 'Lax',
			]);
		}
		\App\Mail\Security::pushScript('if(window.dataLayer) dataLayer.push({event:"form_success", form_type:"' . addslashes($__type) . '"});');
		// Цель в Метрику/GA4: заявка успешно отправлена (конверсия, а не просто submit)
		\App\Mail\Security::pushScript('try{if(window.fssGoal){window.fssGoal("form_success",{form:"' . addslashes($__type) . '"});}}catch(e){}');
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
	// Версия = самая «свежая» дата среди ассетов (CSS/JS), а не только premium.css:
	// иначе правка любого файла в distCSS не меняла бы ?v=… и браузер держал бы старый кэш.
	// Результат кэшируем на 5 минут — иначе каждый запрос опрашивал бы каталог ассетов.
	$__cacheFile = \App\Models\Cache\Path::file('asset_ver.txt');
	$__ver = 0;
	if (is_file($__cacheFile) && (time() - (int)@filemtime($__cacheFile)) < 300) {
		$__ver = (int)trim((string)@file_get_contents($__cacheFile));
	}
	if ($__ver <= 0) {
		$__newest = 0;
		// vendor/fontawesome тоже сканируется: его пересборка (новый сабсет
		// иконок) иначе не сменит ?v= и браузер останется на старом CSS.
		foreach (['/public/assets/distCSS', '/public/assets/distJS', '/public/assets/vendor/fontawesome'] as $__dir) {
			$__abs = dirname(__DIR__) . $__dir;
			if (!is_dir($__abs)) {
				continue;
			}
			$__it = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($__abs, FilesystemIterator::SKIP_DOTS)
			);
			foreach ($__it as $__f) {
				if ($__f->isFile()) {
					$__mt = (int)@filemtime($__f->getPathname());
					if ($__mt > $__newest) {
						$__newest = $__mt;
					}
				}
			}
		}
		$__ver = $__newest > 0 ? $__newest : time();
		if ($__cacheFile !== '') {
			$__cacheDir = dirname($__cacheFile);
			if (!is_dir($__cacheDir)) {
				@mkdir($__cacheDir, 0775, true);
			}
			@file_put_contents($__cacheFile, (string)$__ver);
		}
		unset($__newest, $__dir, $__abs, $__it, $__f, $__mt);
	}
	define('FSS_ASSET_VER', date('YmdHi', $__ver));
	unset($__ver, $__cacheFile);
}
