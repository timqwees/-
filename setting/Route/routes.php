<?php

use App\Controllers\PageController;
use App\Controllers\MailController;
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
Routes::get('/about', [PageController::class, 'about']);
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
