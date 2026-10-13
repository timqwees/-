# AGENTS — ФАВОРИТ-СТРОЙ-СНАБ (фсс.рус)

## Корень (qweescore-база, без сборщиков)
`index.php` (точка входа) · `.htaccess` · `composer.json` · `app/` · `setting/` ·
`public/` · `tools/` (indexnow.php) · `vendor/` (phpmailer).

## Ядро
- `app/bootstrap.php` — первая строка каждого шаблона (SEO-либа + `fss_page_file`).
- `app/Config/site.php` — NAP, цифры, счётчики. `app/Config/app.php` + `app/Mail/*` — секреты/формы.
- `app/Seo/seo.php` — canonical/OG/schema/аналитика. `app/Data/services.php` — СЯ (1 услуга = 1 кластер).
- `app/Models/Router/Routes.php` — роутер (`get/post`, `{slug}`, `auto_element`, `error_404`).
- `app/Controllers/PageController.php` — тонкие действия на шаблоны.

## Контент (`public/`)
`pages/` (home, uslugi, portfolio, projects, search, politic, agree, services/&lt;slug&gt;) ·
`components/` (header/footer/forma/related_services/sheoma/…) · `assets/` (distCSS/distJS/image/fonts) ·
техфайлы: manifest, browserconfig, opensearch, ai-plugin, openapi, `.well-known/`.

## Маршруты и фиды
Всё — только в `setting/Route/routes.php` (страницы, POST форм, legacy-301, robots/sitemap/rss/turbo/yml/llms, well-known).
Генераторы — `setting/Route/Functions/*`, заголовки — только `SecurityHeaders`.

## Команды
```bash
php -S 127.0.0.1:8080 -t .
find app setting public -name '*.php' -exec php -l {} \; | grep -v "No syntax errors" || echo OK
```

## Правила
1. Новая услуга = `public/pages/services/<slug>/index.php` + запись в `app/Data/services.php`. Остальное подхватится само.
2. Каноникл всегда ЧПУ (`/services/<slug>`). Ссылки внутри — относительные, статика — `/public/assets/...`.
3. Цифры/телефон/адрес — только из `app/Config/site.php`. Без `meta keywords`, 404 не ломать.
4. CSS/JS править напрямую в `distCSS/*.css` / `distJS/*.js`. Фирменные токены и theme-bridge (emerald→sunset) — `distCSS/premium.css` (грузится последним, порядок важен).
5. Перед выкатом: `php -l` + внешний краулер (Screaming Frog) по sitemap — 0 ошибок 4xx/5xx.
6. DEV: пока в корне лежит `.dev` (или `FSS_ENV=dev`), кэш выключен везде (no-store, без 304/ETag, OPcache сбрасывается). Перед продом `.dev` УДАЛИТЬ.
