<?php declare(strict_types=1);

namespace Setting\Route\Functions;

class TheFunction
{
    /**
     * Возвращает базовую информацию о сайте (baseUrl, canonical, shareImageUrl и т.д.)
     */
    public static function site(): array
    {
        $rawHost = $_SERVER['HTTP_HOST'] ?? 'xn--h1aafj.xn--p1ai';
        $rawHost = preg_replace('/:\d+$/', '', (string)$rawHost) ?? '';
        $isProd = str_ends_with(strtolower($rawHost), 'xn--h1aafj.xn--p1ai') || str_ends_with(strtolower($rawHost), 'фсс.рус');

        if ($isProd) {
            $scheme = 'https';
            $host = 'xn--h1aafj.xn--p1ai';
        } else {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : $scheme;
            }
            $host = $rawHost ?: 'xn--h1aafj.xn--p1ai';
        }

        $baseUrl = $scheme . '://' . $host;
        $pathOnly = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (!is_string($pathOnly) || $pathOnly === '') {
            $pathOnly = '/';
        }
        $canonicalUrl = $baseUrl . $pathOnly;
        $shareImageUrl = $baseUrl . '/public/assets/image/logo/favicon/favicon-512x512.png';

        return [
            'baseUrl'       => $baseUrl,
            'path'          => $pathOnly,
            'canonicalUrl'  => $canonicalUrl,
            'shareImageUrl' => $shareImageUrl,
            'name'          => 'ФАВОРИТ-СТРОЙ-СНАБ',
        ];
    }

    /**
     * Генерирует SEO-данные для страницы
     */
    public static function seo(array $params = []): array
    {
        $site = self::site();

        $title       = $params['title']       ?? $site['name'];
        $description = $params['description'] ?? 'Строительство БВЗ под ключ: склады, ангары, АБК, общежития. Капремонт, спецтехника. От 14 дней, гарантия 50 лет.';
        $keywords    = $params['keywords']    ?? 'ФАВОРИТ-СТРОЙ-СНАБ, БВЗ, быстровозводимые здания, ангары, склады, АБК, капремонт, Москва';
        $image       = $params['image']       ?? $site['shareImageUrl'];
        $url         = $params['url']         ?? $site['baseUrl'] . ($params['path'] ?? $site['path']);
        $type        = $params['type']        ?? 'website';
        $pageType    = $params['pageType']    ?? 'WebPage';
        $breadcrumbs = $params['breadcrumbs'] ?? [['name' => 'Главная', 'url' => $site['baseUrl'] . '/']];

        $canonical = $params['canonical'] ?? $url;

        $og = [
            'type'       => $type,
            'title'      => $title,
            'description'=> $description,
            'url'        => $url,
            'image'      => $image,
            'site_name'  => $site['name'],
            'locale'     => 'ru_RU',
        ];

        $twitter = [
            'card'       => 'summary_large_image',
            'site'       => '@fss_rus',
            'title'      => $title,
            'description'=> $description,
            'image'      => $image,
            'creator'    => '@fss_rus',
            'domain'     => $site['baseUrl'],
        ];

        return [
            'title'       => $title,
            'description' => $description,
            'keywords'    => $keywords,
            'image'       => $image,
            'url'         => $url,
            'type'        => $type,
            'pageType'    => $pageType,
            'breadcrumbs' => $breadcrumbs,
            'canonical'   => $canonical,
            'og'          => $og,
            'twitter'     => $twitter,
        ];
    }
}