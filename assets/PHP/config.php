<?php
/**
 * Central config - FAVORIT-STROY-SNAB
 * Secrets вынесены сюда. В проде вынести в .env / env переменные.
 */
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Site canonical domain - IDN and punycode
define('SITE_DOMAIN_IDN', 'https://www.фсс.рус');
define('SITE_DOMAIN_PUNY', 'https://www.xn--h1aafj.xn--p1ai');
define('SITE_NAME', 'ФАВОРИТ-СТРОЙ-СНАБ');
define('SITE_BRAND', 'FAVORIT-STROY-SNAB');

// Telegram - берём из env если есть, иначе дефолт (скрывать в git)
$telegramToken = getenv('TELEGRAM_BOT_TOKEN') ?: '8480933826:AAHloEdO5bPRHxMbaXsHyiTvOAEviT3GjOg';
$telegramChats = getenv('TELEGRAM_CHAT_IDS') ? explode(',', getenv('TELEGRAM_CHAT_IDS')) : ['1999094746','1901625695','1336615751'];
define('TELEGRAM_BOT_TOKEN', $telegramToken);
define('TELEGRAM_CHAT_IDS', serialize($telegramChats));

// Mail
define('MAIL_TO', getenv('MAIL_TO') ?: 'artemnersisyan777@gmail.com');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'bingiabonbasv@gmail.com');
define('MAIL_PASS', getenv('MAIL_PASS') ?: 'admu ohaa oeoz lvtd'); // App password Gmail
define('MAIL_FROM_NAME', 'Новое обращение ФСС.РУС');

// Antibot
define('ANTIBOT_MIN_TIME', 3); // сек минимальное время заполнения
define('ANTIBOT_MAX_TIME', 3600); // макс время жизни формы (1 час)
define('ANTIBOT_RATE_LIMIT_COUNT', 5); // max за окно
define('ANTIBOT_RATE_LIMIT_WINDOW', 600); // окно 10 минут в сек
define('ANTIBOT_HONEYPOT_FIELD', 'fax_number'); // honeypot
define('ANTIBOT_STORAGE_DIR', sys_get_temp_dir() . '/fss_antibot');

// Ensure storage dir
if (!is_dir(ANTIBOT_STORAGE_DIR)) {
    @mkdir(ANTIBOT_STORAGE_DIR, 0755, true);
}

// Helper: get punycode for canonical href
function site_canonical_url($path = '/') {
    $path = '/' . ltrim($path, '/');
    // Use Punycode in canonical for max compatibility
    return rtrim(SITE_DOMAIN_PUNY, '/') . $path;
}
function site_url_idn($path = '/') {
    $path = '/' . ltrim($path, '/');
    return rtrim(SITE_DOMAIN_IDN, '/') . $path;
}
