<?php

declare(strict_types=1);

namespace App\Models\Router;

use App\Models\Image;

/**
 * Финализация HTML перед отдачей клиенту.
 *
 * 1) Cache-busting версия ассетов (?v=YYYYMMDDHH).
 * 2) width/height для <img> без них — браузер резервирует место под картинку,
 *    поэтому при загрузке (особенно lazy) секция не «уезжает» (CLS).
 */
final class Html
{
	public static function finalize(string $html): string
	{
		if ($html === '') {
			return $html;
		}
		if (defined('FSS_ASSET_VER')) {
			$ver = (string) FSS_ASSET_VER;
			// Версия ассетов в разметке задана как ?v=YYYYMMDD (в шаблонах — своя дата).
			// Раньше подменялась только одна «магическая» строка (?v=20261009),
			// а шаблоны держали ?v=20261018 / ?v=20261022 — версия не обновлялась.
			$html = (string) preg_replace(
				'~(/public/assets/[^"\'\s?]+\.(?:css|js|mjs|woff2?))(\?v=)\d{6,10}~i',
				'${1}${2}' . $ver,
				$html
			);
			$html = str_replace('/public/assets/distJS/main.js"', '/public/assets/distJS/main.js?v=' . $ver . '"', $html);
			$html = str_replace("/public/assets/distJS/main.js'", '/public/assets/distJS/main.js?v=' . $ver . '"', $html);
		}
		$html = self::extractInlineCss($html);
		// Тяжёлые .jpg/.png → их .webp-варианты (файлы уже сгенерированы).
		// Экономит сотни КБ на главной: banner.jpeg 112 КБ → .webp 79 КБ.
		$html = Image::optimize($html);
		return Image::inject($html);
	}

	/**
	 * Вынос инлайнового <style> в отдельный кэшируемый CSS-файл.
	 *
	 * Зачем: в шаблонах до 90 KB CSS внутри HTML — он не кэшируется отдельно
	 * и повторно грузится на каждой странице. После выноса:
	 *   - HTML уменьшается на десятки килобайт (быстрее TTFB и парсинг);
	 *   - CSS отдаётся один раз с immutable-кэшем и gzip;
	 *   - стиль применяется до отрисовки (меньше FOUC/CLS).
	 *
	 * Файл адресуется хешем содержимого: одинаковый CSS у разных страниц
	 * шарится одним файлом. Если файловую систему писать нельзя — HTML
	 * остаётся как есть (безопасно).
	 */
	public static function extractInlineCss(string $html): string
	{
		if ($html === '' || stripos($html, '<style') === false) {
			return $html;
		}
		$blocks = [];
		$html = (string) preg_replace_callback(
			'#(?is)<style([^>]*)>(.*?)</style>#',
			static function (array $m) use (&$blocks): string {
				$attr = trim((string)$m[1]);
				// <style media="print"> и подобные — оставляем на месте.
				if ($attr !== '' && !preg_match('/^media\s*=\s*["\']?all["\']?$/i', $attr)) {
					return $m[0];
				}
				$css = trim((string)$m[2]);
				if ($css === '') {
					return '';
				}
				$blocks[] = $css;
				return '';
			},
			$html
		);
		if (!$blocks) {
			return $html;
		}
		$css = implode("\n", $blocks);
		$hash = substr(sha1($css), 0, 16);
		$dir = dirname(__DIR__, 3) . '/public/assets/distCSS/auto';
		$file = $dir . '/p-' . $hash . '.css';
		if (!is_file($file)) {
			if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
				return $html; // ФС недоступна — оставляем инлайн.
			}
			@file_put_contents($file, $css, LOCK_EX);
		}
		$ver = defined('FSS_ASSET_VER') ? (string) FSS_ASSET_VER : (string) seo_config('cache_bust');
		$link = '<link rel="stylesheet" href="/public/assets/distCSS/auto/p-' . $hash . '.css?v=' . $ver . '">';
		$pos = stripos($html, '</head>');
		if ($pos === false) {
			return $link . $html;
		}
		return substr($html, 0, $pos) . $link . "\n" . substr($html, $pos);
	}
}
