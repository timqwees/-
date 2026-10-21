<?php

use App\Controllers\PageController;
use App\Controllers\MailController;
use App\Controllers\SuggestController;
use App\Models\Router\Routes;
use Setting\Route\Functions\Llms;
use Setting\Route\Functions\Robots;
use Setting\Route\Functions\RssFeed;
use Setting\Route\Functions\SecurityHeaders;
use Setting\Route\Functions\Sitemap;
use Setting\Route\Functions\UrlList;
use Setting\Route\Functions\YmlFeed;

//==================================================================================================//FORMS (POST - единый endpoint для всех форм)
Routes::post('/send/email', [\Setting\Route\Functions\FormHandler::class, 'sendMail']);
//==================================================================================================//ПОДСКАЗКИ ПО ЛОКАЦИИ (DaData / Open-Meteo / Nominatim — ключ DaData только на сервере)
Routes::post('/api/suggest/location', [SuggestController::class, 'location']);
//==================================================================================================//MAIN
Routes::get('/', [PageController::class, 'home']);
// Служебный маркер DEV-режима наружу не отдаём (на Apache закрыт ещё и .htaccess)
Routes::get('/.dev', function () {
	Routes::error_404('/.dev');
});
//==================================================================================================//HUB
Routes::get('/uslugi', [PageController::class, 'uslugi']);
//==================================================================================================//PORTFOLIO + PROJECTS
Routes::get('/portfolio', [PageController::class, 'portfolio']);
// Детальные страницы объектов: /portfolio/{slug} (app/Data/projects.php)
Routes::get('/portfolio/{slug}', function (string $slug) {
	PageController::project($slug);
});
Routes::get('/about', [PageController::class, 'about']);
// Страница компании: /company (реквизиты, руководство, направления, выполненные объекты)
Routes::get('/company', [PageController::class, 'company']);
// Отдельная страница контактов: /contacts (телефоны, адрес, реквизиты, заявка)
Routes::get('/contacts', [PageController::class, 'contacts']);
// Старый адрес страницы заявки → редирект на актуальную страницу контактов
Routes::get('/contact', function () {
	Routes::redirect('/contacts');
});
Routes::get('/projects', function () { Routes::redirect('/portfolio'); });
//==================================================================================================//LEGAL
Routes::get('/politika', [PageController::class, 'politika']);
Routes::get('/soglasie', [PageController::class, 'soglasie']);
//==================================================================================================//SEARCH (закрывает SearchAction в schema и opensearch.xml)
Routes::get('/search', [PageController::class, 'search']);
Routes::get('/search/blog', [PageController::class, 'searchBlog']);
//==================================================================================================//SERVICE (1 услуга = 1 кластер)
Routes::get('/services/{slug}', function (string $slug) {
	PageController::service($slug);
});
//==================================================================================================//GEO (НЧ-гео посадочные: /services/{slug}/{geo})
Routes::get('/services/{slug}/{geo}', function (string $slug, string $geo) {
	PageController::serviceGeo($slug, $geo);
});
//==================================================================================================//BLOG (SEO контент + GEO для AI Overviews)
Routes::get('/blog', [PageController::class, 'blog']);
Routes::get('/blog/{slug}', function (string $slug) {
	PageController::blogPost($slug);
});
// Legacy blog redirect
Routes::get('/posts/{slug}', function (string $slug) {
	Routes::redirect('/blog/' . $slug);
});
//==================================================================================================//LEGACY (старые .php-адреса → ЧПУ 301, защита от дублей)
Routes::get('/index.php', function () { Routes::redirect('/'); });
Routes::get('/home.php', function () { Routes::redirect('/'); });
Routes::get('/pages/uslugi/uslugi.php', function () { Routes::redirect('/uslugi'); });
Routes::get('/pages/portfolio.php', function () { Routes::redirect('/portfolio'); });
Routes::get('/pages/projects.php', function () { Routes::redirect('/portfolio'); });
Routes::get('/pages/politic/index.php', function () { Routes::redirect('/politika'); });
Routes::get('/pages/agree/index.php', function () { Routes::redirect('/soglasie'); });
Routes::get('/pages/services/{slug}/index.php', function (string $slug) {
	Routes::redirect('/services/' . $slug);
});
Routes::get('/pages/services/{slug}', function (string $slug) {
	Routes::redirect('/services/' . $slug);
});
// Всё прочее под /pages/ — не существует: честный 404 (без редирект-цепочки на закрытый /public/pages/)
Routes::get('/pages/{rest:.+}', function (string $rest) {
	Routes::error_404('/pages/' . $rest);
});
Routes::get('/assets/{rest:.+}', function (string $rest) {
	Routes::redirect('/public/assets/' . $rest);
});
//==================================================================================================//SITEMAP (+index)
Routes::get('/sitemap.xml', function () {
	SecurityHeaders::sendSecurity();
	Sitemap::output();
});
Routes::get('/sitemap-index.xml', function () {
	SecurityHeaders::sendSecurity();
	Sitemap::outputIndex();
});
//==================================================================================================//RSS + TURBO
Routes::get('/rss.xml', function () {
	SecurityHeaders::sendSecurity();
	RssFeed::output();
});
Routes::get('/turbo.xml', function () {
	SecurityHeaders::sendSecurity();
	RssFeed::outputTurbo();
});
//==================================================================================================//YML (Яндекс.Товары/Услуги)
Routes::get('/yml.xml', function () {
	SecurityHeaders::sendSecurity();
	YmlFeed::output();
});
Routes::get('/feed.yml', function () {
	SecurityHeaders::sendSecurity();
	YmlFeed::output();
});
//==================================================================================================//ROBOTS
Routes::get('/robots.txt', function () {
	SecurityHeaders::sendSecurity();
	Robots::output();
});
//==================================================================================================//LLMS (GEO/AI)
Routes::get('/llms.txt', function () {
	SecurityHeaders::sendSecurity();
	Llms::output();
});
Routes::get('/llms-full.txt', function () {
	SecurityHeaders::sendSecurity();
	Llms::outputFull();
});
//==================================================================================================//PAGES LIST (Screaming Frog)
Routes::get('/pages', function () {
	SecurityHeaders::sendSecurity();
	UrlList::output();
});
//==================================================================================================//INDEXNOW KEY (<key>.txt отдаём из конфига, файл не нужен)
Routes::get('/{key}.txt', function (string $key) {
	$want = (string)seo_config('indexnow_key');
	if ($want !== '' && hash_equals($want, $key)) {
		SecurityHeaders::sendSecurity();
		header('Content-Type: text/plain; charset=utf-8');
		if (SecurityHeaders::isDev()) { SecurityHeaders::sendNoCache(); } else { header('Cache-Control: public, max-age=3600'); }
		echo $want;
		return;
	}
	Routes::error_404('/' . $key . '.txt');
});
//==================================================================================================//ИКОНКИ В КОРНЕ (стандартные пути браузеров, поисковиков и краулеров)
// Без них /favicon.ico, /favicon.svg, /apple-touch-icon.png отдают 404 — это минус в аудитах и
// в консоли поисковиков. Отдаём те же файлы, что лежат в /public/assets/image/logo/favicon/.
$rootIcons = [
	'/favicon.ico' => ['public/assets/image/logo/favicon/favicon.ico', 'image/x-icon'],
	'/favicon.svg' => ['public/assets/image/logo/favicon/favicon.svg', 'image/svg+xml'],
	'/apple-touch-icon.png' => ['public/assets/image/logo/favicon/apple-touch-icon.png', 'image/png'],
	'/favicon-32x32.png' => ['public/assets/image/logo/favicon/favicon-32x32.png', 'image/png'],
	'/favicon-16x16.png' => ['public/assets/image/logo/favicon/favicon-16x16.png', 'image/png'],
];
foreach ($rootIcons as $__iconPath => $__icon) {
	Routes::get($__iconPath, function () use ($__icon) {
		[$__file, $__mime] = $__icon;
		$__abs = dirname(__DIR__, 2) . '/' . $__file;
		if (!is_file($__abs)) {
			\App\Models\Router\Routes::error_404('/' . basename($__file));
			return;
		}
		SecurityHeaders::sendStaticCache($__abs);
		header('Content-Type: ' . $__mime);
		readfile($__abs);
	});
}
unset($rootIcons, $__iconPath, $__icon);

//==================================================================================================//OPENSEARCH + PWA
Routes::get('/opensearch.xml', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/opensearchdescription+xml; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/opensearch.xml');
});
Routes::get('/manifest.json', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/manifest+json; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/manifest.json');
});
Routes::get('/site.webmanifest', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/manifest+json; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/manifest.json');
});
Routes::get('/browserconfig.xml', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/xml; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/browserconfig.xml');
});
//==================================================================================================//AI DISCOVERY
Routes::get('/ai-plugin.json', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/json; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/ai-plugin.json');
});
Routes::get('/openapi.yaml', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: text/yaml; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/openapi.yaml');
});
Routes::get('/.well-known/agents.json', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/json; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/.well-known/agents.json');
});
// Алиас без точки: часть AI-агентов и краулеров запрашивает /agents.json,
// а часть окружений (в т.ч. локальный PHP-сервер) блокирует пути с dot-сегментом.
Routes::get('/agents.json', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/json; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/.well-known/agents.json');
});
// Service worker (PWA) — отдаём с корня, чтобы scope был '/'
Routes::get('/sw.js', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/javascript; charset=utf-8');
	header('Service-Worker-Allowed: /');
	header('Cache-Control: public, max-age=0, must-revalidate');
	readfile(dirname(__DIR__, 2) . '/public/sw.js');
});
Routes::get('/.well-known/security.txt', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: text/plain; charset=utf-8');
	readfile(dirname(__DIR__, 2) . '/public/.well-known/security.txt');
});
Routes::get('/.well-known/api-catalog', function () {
	SecurityHeaders::sendSecurity();
	header('Content-Type: application/linkset+json');
	readfile(dirname(__DIR__, 2) . '/public/.well-known/api-catalog');
});
