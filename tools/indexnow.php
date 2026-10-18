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
    $urls = [];

    // 1. Ключевые/хабовые страницы — из массива tools/indexnow.urls.php
    $listFile = __DIR__ . '/indexnow.urls.php';
    foreach ((array)(is_file($listFile) ? require $listFile : []) as $path) {
        $path = '/' . ltrim(trim((string)$path), '/');
        $urls[] = $base . $path;
    }

    // 2. Услуги — по факту наличия public/pages/services/*/index.php
    foreach (glob($root . '/services/*/index.php') ?: [] as $file) {
        $urls[] = $base . '/services/' . basename(dirname((string)$file));
    }

    // 3. Статьи блога — из app/Data/blog.php (slug => ...)
    $blogFile = dirname(__DIR__) . '/app/Data/blog.php';
    if (is_file($blogFile)) {
        foreach (array_keys((array)require $blogFile) as $slug) {
            $urls[] = $base . '/blog/' . $slug;
        }
    }

    // 4. Гео-посадочные (приоритетные услуги × города) — из app/Data/geo.php
    $geoFile = dirname(__DIR__) . '/app/Data/geo.php';
    if (is_file($geoFile)) {
        $geo = (array)require $geoFile;
        $services = array_map('basename', glob($root . '/services/*', GLOB_ONLYDIR) ?: []);
        foreach (($geo['priority_services'] ?? []) as $svc) {
            if (!in_array($svc, $services, true)) {
                continue;
            }
            foreach (array_keys((array)($geo['geo'] ?? [])) as $g) {
                $urls[] = $base . '/services/' . $svc . '/' . $g;
            }
        }
    }

    $urls = array_values(array_unique(array_filter($urls)));
    sort($urls);
    return $urls;
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
