<?php
// IndexNow: уведомление Bing и Яндекс об изменённых страницах.
// Использование:
//   php tools/indexnow.php           — отправить главную + хабы + все услуги
//   php tools/indexnow.php urls.txt  — отправить URL из файла (по одному на строке)
//
// Требуется: ключ в app/Config/site.php ('indexnow_key'). Роут /<key>.txt
// отдаёт его автоматически (setting/Route/routes.php) — файл класть не надо.

$site = require dirname(__DIR__) . '/app/Config/site.php';

const INDEXNOW_ENDPOINTS = [
    'https://www.bing.com/indexnow',
    'https://yandex.com/indexnow',
];

$key = (string)($site['indexnow_key'] ?? '');
if ($key === '') {
    fwrite(STDERR, "Нет ключа: впишите 'indexnow_key' в app/Config/site.php\n");
    fwrite(STDERR, "Ключ выдаётся на https://www.bing.com/indexnow#getting-started\n");
    exit(1);
}
$base = rtrim((string)$site['base_url'], '/');
$host = (string)$site['base_host'];

function collectUrls(string $base): array
{
    $root = dirname(__DIR__) . '/public/pages';
    $map = [
        'home.php' => '/',
        'uslugi/uslugi.php' => '/uslugi',
        'portfolio.php' => '/portfolio',
        'projects.php' => '/projects',
        'politic/index.php' => '/politika',
        'agree/index.php' => '/soglasie',
        'search.php' => '/search',
    ];
    $urls = [];
    foreach ($map as $file => $route) {
        if (is_file($root . '/' . $file)) {
            $urls[] = $base . $route;
        }
    }
    foreach (glob($root . '/services/*/index.php') ?: [] as $file) {
        $urls[] = $base . '/services/' . basename(dirname((string)$file));
    }
    sort($urls);
    return array_values(array_unique($urls));
}

$urls = [];
if (isset($argv[1]) && is_file($argv[1])) {
    $urls = array_values(array_filter(array_map('trim', file($argv[1]))));
} else {
    $urls = collectUrls($base);
}

$payload = json_encode([
    'host' => $host,
    'key' => $key,
    'keyLocation' => $base . '/' . $key . '.txt',
    'urlList' => $urls,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

echo 'URLs: ' . count($urls) . PHP_EOL;
foreach (INDEXNOW_ENDPOINTS as $endpoint) {
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
    ]);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    echo $endpoint . ' -> HTTP ' . $code . ($err ? " ($err)" : '') . PHP_EOL;
}
echo "OK: 202 = принято. Проверка ключа: " . $base . '/' . $key . ".txt" . PHP_EOL;
