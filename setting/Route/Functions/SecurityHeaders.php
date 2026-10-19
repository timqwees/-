<?php declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * Централизованные HTTP-заголовки — ЕДИНСТВЕННОЕ место их установки.
 *
 * reg.ru на shared-хостинге жалуется на директивы Header в .htaccess,
 * поэтому .htaccess содержит только rewrite/сжатие/Expires для статики,
 * а ВСЕ заголовки (безопасность + кэш HTML) ставятся отсюда через header().
 *
 * Покрывает замечания enterno.io:
 * - HSTS 7/20 → max-age + includeSubDomains (+preload)
 * - Referrer-Policy 0/5 → strict-origin-when-cross-origin
 * - Permissions-Policy 0/5 → camera/microphone/geolocation off
 * - COOP/COEP/CORP 0/5 (bonus) → безопасные значения, не ломающие сторонние виджеты
 * - ETag 0/15 → ETag + 304 handling
 * - Дубль Cache-Control (no-cache vs max-age=7200) → один Cache-Control только отсюда
 */
final class SecurityHeaders
{
    public const HTML_CACHE_MAX_AGE = 7200;

    /** DEV-режим (работы идут): FSS_DEV=true в index.php через env FSS_ENV=dev или файл .dev */
    public static function isDev(): bool
    {
        return defined('FSS_DEV') && FSS_DEV === true;
    }

    /** Жёсткое отключение кэша на время работ */
    public static function sendNoCache(): void
    {
        if (headers_sent()) {
            return;
        }
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0', true);
        header('Pragma: no-cache', true);
        header('Expires: 0', true);
    }

    /** Готовое тело ответа: в DEV — всегда свежее, в проде — ETag/304 */
    public static function emitCached(string $body, string $mime, int $maxAge = 3600): void
    {
        if (self::isDev()) {
            header('Content-Type: ' . $mime);
            self::sendNoCache();
            echo $body;
            return;
        }
        $etag = '"' . md5($body) . '"';
        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=' . $maxAge, true);
        header('ETag: ' . $etag, true);
        if (trim($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
            http_response_code(304);
            exit;
        }
        echo $body;
    }

    public static function sendSecurity(string $robots = ''): void
    {
        if (headers_sent()) {
            return;
        }
        // HSTS имеет смысл только на HTTPS; за прокси (nginx/cloudflare) смотрим X-Forwarded-Proto
        if (self::isHttps()) {
            header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload', true);
        }
        header('X-Frame-Options: SAMEORIGIN', true);
        header('X-Content-Type-Options: nosniff', true);
        header('X-XSS-Protection: 0', true);
        header('Referrer-Policy: strict-origin-when-cross-origin', true);
        header('X-DNS-Prefetch-Control: on', true);
        header('X-Permitted-Cross-Domain-Policies: none', true);
        header('X-Download-Options: noopen', true);
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()', true);
        header('Cross-Origin-Opener-Policy: same-origin-allow-popups', true);
        header('Cross-Origin-Resource-Policy: same-origin', true);
        header('Cross-Origin-Embedder-Policy: unsafe-none', true);
// Permissive CSP: сайт использует инлайн-скрипты/стили, строгий CSP всё сломает.
	// frame-ancestors усиливает X-Frame-Options.
	// TODO: после перехода на nonce/hash — ужесточить CSP
	header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://mc.yandex.ru https://www.googletagmanager.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https:; connect-src 'self' https://mc.yandex.ru https://www.google-analytics.com https://www.googletagmanager.com; frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com; frame-ancestors 'self'; base-uri 'self'; form-action 'self';", true);
        // AI-readiness (enterno ai-check): явное разрешение индексации + машиночитаемые связи.
        // Без noai/noimageai — политика сайта «разрешить всех» (см. robots.php).
        if ($robots !== '') {
            header('X-Robots-Tag: ' . $robots, true);
        }
        header('Link: </llms.txt>; rel="llms", </sitemap.xml>; rel="sitemap", </openapi.yaml>; rel="service-desc"', true);
    }

    /**
     * HTML-кэширование + условные запросы.
     * Один ЕДИНСТВЕННЫЙ Cache-Control (replace=true), ETag и Last-Modified.
     * При совпадении If-None-Match / If-Modified-Since отдаёт 304 и завершает запрос.
     */
    public static function sendHtmlCache(string $templatePath): void
    {
        if (headers_sent()) {
            return;
        }
        if (self::isDev()) {
            self::sendNoCache();
            return;
        }
        $mtime = is_file($templatePath) ? (int) filemtime($templatePath) : time();
        $etag = '"' . md5($templatePath . '|' . $mtime) . '"';

        // replace=true — затирает любые предыдущие Cache-Control от вызывающего кода
        header('Cache-Control: public, max-age=' . self::HTML_CACHE_MAX_AGE . ', must-revalidate', true);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT', true);
        header('ETag: ' . $etag, true);

        $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';
        if ($ifNoneMatch !== '' && trim($ifNoneMatch) === $etag) {
            http_response_code(304);
            exit;
        }
        if ($ifNoneMatch === '' && $ifModifiedSince !== '') {
            $since = strtotime($ifModifiedSince);
            if ($since !== false && $since >= $mtime) {
                http_response_code(304);
                exit;
            }
        }
    }

    /** Совместимость с прокси: HTTPS напрямую или X-Forwarded-Proto=https */
    public static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
            return true;
        }
        if (($_SERVER['REQUEST_SCHEME'] ?? '') === 'https') {
            return true;
        }
        return false;
    }

    /** Полный набор для HTML-страницы: безопасность + кэш. Вызывать ДО вывода тела. */
    public static function sendForHtml(string $templatePath): void
    {
        self::sendSecurity();
        self::sendHtmlCache($templatePath);
    }

    /**
     * Кэш для статики, отдаваемой через PHP (Routes::file).
     * Напрямую Apache отдаёт статику с Expires из .htaccess,
     * а через этот метод — с явным immutable Cache-Control + ETag/304.
     */
    public static function sendStaticCache(string $filePath): void
    {
        if (headers_sent()) {
            return;
        }
        if (self::isDev()) {
            self::sendSecurity();
            self::sendNoCache();
            return;
        }
        self::sendSecurity();
        $mtime = is_file($filePath) ? (int) filemtime($filePath) : time();
        $size = is_file($filePath) ? (int) filesize($filePath) : 0;
        $etag = '"' . md5($filePath . '|' . $mtime . '|' . $size) . '"';

        header('Cache-Control: public, max-age=31536000, immutable', true);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT', true);
        header('ETag: ' . $etag, true);

        $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        if ($ifNoneMatch !== '' && trim($ifNoneMatch) === $etag) {
            http_response_code(304);
            exit;
        }
    }
}
