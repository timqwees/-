<?php

declare(strict_types=1);

namespace Setting\Route\Functions;

/**
 * Динамический /robots.txt — всегда согласован с картой сайта и ЧПУ.
 */
final class Robots
{
	public static function output(): void
	{
		$base = rtrim(seo_config('base_url'), '/');
		header('Content-Type: text/plain; charset=utf-8');
		if (SecurityHeaders::isDev()) {
			SecurityHeaders::sendNoCache();
		} else {
			header('Cache-Control: public, max-age=3600');
		}
		header('Content-Signal: search=yes, ai-train=yes, ai-input=yes');

		$out = "# robots.txt — " . seo_config('brand') . " (" . seo_config('base_host') . ")\n";
		$out .= "# Генерируется из app/Config/site.php — не редактировать вручную\n\n";
		$out .= "User-agent: *\n";
		$out .= "Allow: /\n";
		$out .= "Disallow: /app/\n";
		$out .= "Disallow: /setting/\n";
		$out .= "Disallow: /vendor/\n";
		$out .= "Disallow: /tools/\n";
		$out .= "Disallow: /public/pages/\n";
		$out .= "Disallow: /*?s=\n";
		$out .= "Disallow: /*?*ref=\n";
		$out .= "Disallow: /*?*utm_\n\n";
		$out .= "# Статика — индексировать и кэшировать\n";
		$out .= "Allow: /public/assets/\n";
		foreach (['css', 'js', 'png', 'jpg', 'jpeg', 'svg', 'webp', 'avif', 'woff2'] as $ext) {
			$out .= "Allow: /*.{$ext}\$\n";
		}
		$out .= "\n# Параметрические дубли — Яндекс\n";
		foreach (['ref', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'yclid', 'gclid', 'fbclid', '_openstat'] as $p) {
			$out .= "Clean-param: {$p} /\n";
		}
		$out .= "\n# AI / GEO-краулеры: явно пускаем — им нужны llms.txt и чистый HTML\n";
		foreach (['GPTBot', 'OAI-SearchBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-Web', 'anthropic-ai', 'PerplexityBot', 'Google-Extended', 'Applebot-Extended', 'Bytespider', 'YandexAdditional', 'YandexBot', 'Googlebot', 'Bingbot'] as $bot) {
			$out .= "User-agent: {$bot}\nAllow: /\n\n";
		}
		$out .= "Host: " . seo_config('base_host') . "\n";
		$out .= "Sitemap: {$base}/sitemap.xml\n";
		$out .= "Sitemap: {$base}/sitemap-index.xml\n";
		echo $out;
	}
}
