<?php declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * Лёгкий HTML → Markdown (для Markdown Negotiation: клиент просит
 * Accept: text/markdown — отдаём текстовую версию страницы).
 * Без зависимостей, только встроенные функции. Покрывает типовую
 * вёрстку сайта: заголовки, абзацы, списки, ссылки, картинки, strong/em.
 */
final class Markdown
{
    public static function toMarkdown(string $html, string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');
        $md = $html;

        // 1. Выкидываем служебное
        $md = (string) preg_replace('~<script\b.*?</script\s*>~is', '', $md);
        $md = (string) preg_replace('~<style\b.*?</style\s*>~is', '', $md);
        $md = (string) preg_replace('~<noscript\b.*?</noscript\s*>~is', '', $md);
        $md = (string) preg_replace('~<!--.*?-->~s', '', $md);

        // 2. Картинки → ![alt](url)
        $md = (string) preg_replace_callback(
            '~<img\b[^>]*>~i',
            static function (array $m) use ($baseUrl): string {
                $alt = '';
                $src = '';
                if (preg_match('~alt\s*=\s*"([^"]*)"~i', $m[0], $a)) {
                    $alt = $a[1];
                } elseif (preg_match("~alt\s*=\s*'([^']*)'~i", $m[0], $a)) {
                    $alt = $a[1];
                }
                if (preg_match('~(?:data-src|src)\s*=\s*"([^"]+)"~i', $m[0], $s)) {
                    $src = $s[1];
                } elseif (preg_match("~(?:data-src|src)\s*=\s*'([^']+)'~i", $m[0], $s)) {
                    $src = $s[1];
                }
                $alt = trim(strip_tags(html_entity_decode($alt, ENT_QUOTES, 'UTF-8')));
                if ($src === '' || stripos($src, 'data:') === 0) {
                    return $alt !== '' ? "\n![{$alt}]\n" : '';
                }
                return "\n![" . $alt . '](' . self::absUrl($src, $baseUrl) . ")\n";
            },
            $md
        );

        // 3. Ссылки → [text](url)
        $md = (string) preg_replace_callback(
            '~<a\b[^>]*href\s*=\s*("[^"]*"|\'[^\']*\')[^>]*>(.*?)</a\s*>~is',
            static function (array $m) use ($baseUrl): string {
                $href = trim($m[1], '"\'');
                $text = trim(strip_tags($m[2]));
                $text = (string) preg_replace('/\s+/', ' ', $text);
                if ($text === '') {
                    return '';
                }
                if ($href === '' || $href[0] === '#' || stripos($href, 'javascript:') === 0) {
                    return $text;
                }
                return '[' . $text . '](' . self::absUrl($href, $baseUrl) . ')';
            },
            $md
        );

        // 4. Заголовки → # text
        for ($level = 1; $level <= 6; $level++) {
            $md = (string) preg_replace(
                '~<h' . $level . '\b[^>]*>(.*?)</h' . $level . '\s*>~is',
                "\n\n" . str_repeat('#', $level) . ' $1' . "\n\n",
                $md
            );
        }

        // 5. Инлайн
        $md = (string) preg_replace('~<(strong|b)\b[^>]*>(.*?)</\1\s*>~is', '**$2**', $md);
        $md = (string) preg_replace('~<(em|i)\b[^>]*>(.*?)</\1\s*>~is', '*$2*', $md);
        $md = (string) preg_replace('~<li\b[^>]*>~i', "\n- ", $md);
        $md = (string) preg_replace('~<br\s*/?>~i', "\n", $md);
        $md = (string) preg_replace('~</(p|div|section|article|header|footer|main|ul|ol|tr|table|blockquote)\s*>~i', "\n\n", $md);
        $md = (string) preg_replace('~<hr\s*/?>~i', "\n\n---\n\n", $md);

        // 6. Чистим остаток
        $md = strip_tags($md);
        $md = html_entity_decode($md, ENT_QUOTES, 'UTF-8');
        $lines = explode("\n", $md);
        $out = [];
        foreach ($lines as $line) {
            $out[] = rtrim($line);
        }
        $md = implode("\n", $out);
        $md = (string) preg_replace("/\n{3,}/", "\n\n", $md);

        return trim($md) . "\n";
    }

    private static function absUrl(string $url, string $baseUrl): string
    {
        if (preg_match('~^https?://~i', $url)) {
            return $url;
        }
        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        }
        if ($url !== '' && $url[0] === '/') {
            return $baseUrl . $url;
        }
        return $baseUrl . '/' . ltrim($url, '/');
    }
}
