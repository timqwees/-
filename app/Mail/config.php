<?php
/**
 * Central config - FAVORIT-STROY-SNAB
 * Secrets вынесены сюда. В проде вынести в .env / env переменные.
 */
// Включаем буферизацию чтобы session_start работал даже после небольшого вывода
if (!ob_get_level() && !headers_sent()) {
    ob_start();
}
// Сессии не должны перебивать Cache-Control владельца — SecurityHeaders
ini_set('session.cache_limiter', '');
if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        @session_start();
    } else {
        @session_start();
        if (session_status() === PHP_SESSION_NONE && !isset($_SESSION)) {
            $_SESSION = [];
        }
    }
}

// Site canonical domain - IDN and punycode (auto-detected from current host)
$httpHost = $_SERVER['HTTP_HOST'] ?? 'xn--h1aafj.xn--p1ai';
$httpHost = strtolower(trim($httpHost));
$httpHost = preg_replace('/^www\./', '', $httpHost);

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
          (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    ? 'https' : 'http';

$baseHost = $httpHost;
if (preg_match('/[^\x00-\x7F]/', $baseHost)) {
    $baseHost = idn_to_ascii($baseHost, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
}

define('SITE_DOMAIN_IDN', $scheme . '://' . (preg_match('/^xn--/', $httpHost) ? idn_to_utf8($httpHost, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46) : $httpHost));
define('SITE_DOMAIN_PUNY', $scheme . '://' . $baseHost);
define('SITE_NAME', 'ФАВОРИТ-СТРОЙ-СНАБ');
define('SITE_BRAND', 'FAVORIT-STROY-SNAB');

// Helper: чтение секретов из окружения (phpdotenv кладёт в $_ENV/$_SERVER, getenv — fallback)
if (!function_exists('fss_env')) {
    function fss_env(string $key, string $default = ''): string {
        $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return ($v === false || $v === null || $v === '') ? $default : (string)$v;
    }
}

// Telegram — секреты ТОЛЬКО из .env (см. .env.example)
define('TELEGRAM_BOT_TOKEN', fss_env('TELEGRAM_BOT_TOKEN'));
$telegramChatsRaw = fss_env('TELEGRAM_CHAT_IDS');
$telegramChats = $telegramChatsRaw !== '' ? array_map('trim', explode(',', $telegramChatsRaw)) : [];
define('TELEGRAM_CHAT_IDS', serialize($telegramChats));

// Mail - ПРОСТЫЕ ЖЁСТКИЕ ЗНАЧЕНИЯ
define('MAIL_TO', 'order@фсс.рус');           // для отображения (IDN)
define('MAIL_TO_PUNY', 'order@xn--h1aafj.xn--p1ai'); // для SMTP (punycode)
define('MAIL_FROM', fss_env('MAIL_FROM'));    // bingiabonbasv@gmail.com
define('MAIL_PASS', fss_env('MAIL_PASS'));    // App password Gmail
define('MAIL_FROM_NAME', 'Новое обращение ФСС.РУС');
define('MAIL_PORT', 587);

// Antibot
define('ANTIBOT_MIN_TIME', 3);
define('ANTIBOT_MAX_TIME', 3600);
define('ANTIBOT_RATE_LIMIT_COUNT', 5);
define('ANTIBOT_RATE_LIMIT_WINDOW', 600);
define('ANTIBOT_HONEYPOT_FIELD', 'fax_number');
define('ANTIBOT_STORAGE_DIR', sys_get_temp_dir() . '/fss_antibot');

if (!is_dir(ANTIBOT_STORAGE_DIR)) {
    @mkdir(ANTIBOT_STORAGE_DIR, 0755, true);
}