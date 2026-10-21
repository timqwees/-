<?php
/**
 * Dev-роутер для встроенного сервера PHP:
 *
 *   php -S 127.0.0.1:8899 tools/dev-router.php
 *
 * Поведение повторяет .htaccess (Apache):
 *   • служебные каталоги (app/, setting/, tools/, vendor/) и .env — 403;
 *   • существующие файлы отдаются как есть (статика, PHP-страницы);
 *   • всё остальное — на front controller index.php (ЧПУ-маршрутизация).
 *
 * Документ-рут сервера = корень проекта (cwd), поэтому пути строятся от __DIR__.
 */

$root = dirname(__DIR__);
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri  = '/' . ltrim(rawurldecode($uri), '/');
$path = realpath($root . $uri);

// --- 1. Закрываем служебные файлы (как в .htaccess) -----------------------
if (preg_match('#^/(?:\.|app/|setting/|tools/|vendor/|composer\.(?:json|lock)|\.env)#i', $uri)) {
	http_response_code(403);
	echo 'Forbidden';
	return true;
}
if (preg_match('#^/public/(?:components|pages)(?:/|$)#i', $uri)) {
	http_response_code(403);
	echo 'Forbidden';
	return true;
}

// --- 2. Существующий файл ------------------------------------------------
if ($path && is_file($path) && strpos($path, $root) === 0) {
	if (strtolower(substr($path, -4)) === '.php') {
		require $path;
		return true;
	}
	return false; // статика — отдаёт встроенный сервер
}

// --- 3. Каталог с index.php ----------------------------------------------
if ($path && is_dir($path) && is_file($path . '/index.php')) {
	chdir($path);
	require $path . '/index.php';
	return true;
}

// --- 4. Front controller -------------------------------------------------
chdir($root);
require $root . '/index.php';
return true;
