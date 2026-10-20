<?php declare(strict_types=1);

/**
 * Генератор JS-словаря городов из PHP-данных.
 *
 * Запуск: php tools/build-hot-cities.php
 *
 * Нужен, чтобы браузерный словарь (public/assets/distJS/hot-cities.js)
 * и серверный (app/Data/suggest_cities.php) не расходились.
 */

$root = dirname(__DIR__);
$dataFile = $root . '/app/Data/suggest_cities.php';
$outFile = $root . '/public/assets/distJS/hot-cities.js';

if (!is_file($dataFile)) {
	fwrite(STDERR, "Не найден файл данных: {$dataFile}\n");
	exit(1);
}

$cities = require $dataFile;
if (!is_array($cities)) {
	fwrite(STDERR, "Некорректный формат данных\n");
	exit(1);
}

$escape = static function (string $value): string {
	return str_replace(["\\", "'"], ["\\\\", "\\'"], $value);
};

$lines = [];
foreach ($cities as $city) {
	if (!is_array($city) || count($city) < 2) {
		continue;
	}
	$lines[] = "\t['" . $escape((string)$city[0]) . "', '" . $escape((string)$city[1]) . "'],";
}

$js = <<<JS
/**
 * Локальный словарь городов/районов — мгновенный отклик без обращения к API.
 *
 * Подсказки появляются сразу при вводе (0 мс), параллельно уходит запрос на
 * /api/suggest/location, и когда он приходит — список заменяется на полный.
 *
 * Источник данных: app/Data/suggest_cities.php
 * Пересборка: php tools/build-hot-cities.php
 *
 * Формат: [название, регион].
 */
window.FSS_HOT_CITIES = [
JS;

$js .= "\n" . implode("\n", $lines) . "\n];\n";

if (!is_dir(dirname($outFile))) {
	mkdir(dirname($outFile), 0755, true);
}
file_put_contents($outFile, $js);

echo "Записано " . count($lines) . " городов в {$outFile}\n";
