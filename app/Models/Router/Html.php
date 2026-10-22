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
	 * Вынос инлайнового <style> в отдельные кэшируемые CSS-файлы.
	 *
	 * Зачем: в шаблонах десятки KB CSS внутри HTML — он не кэшируется отдельно
	 * и повторно грузится на каждой странице. После выноса:
	 *   - HTML уменьшается (быстрее TTFB и парсинг);
	 *   - CSS отдаётся отдельным файлом с gzip и долгим кэшем;
	 *   - стиль применяется до отрисовки (меньше FOUC/CLS).
	 *
	 * Файл адресуется хешем содержимого: одинаковый CSS у разных страниц
	 * шарится одним файлом. Именно поэтому блоки НЕ склеиваются в один файл
	 * на страницу: общий CSS (футер, шапка — 60+ KB) иначе копировался бы в
	 * каждую страницу, и браузер качал бы его заново на каждом переходе.
	 * Теперь общий блок — один файл на весь сайт, а у страницы свой маленький.
	 *
	 * Если файловую систему писать нельзя — HTML остаётся как есть (безопасно).
	 */
	public static function extractInlineCss(string $html): string
	{
		if ($html === '' || stripos($html, '<style') === false) {
			return $html;
		}

		$original = $html;
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

		$dir = dirname(__DIR__, 3) . '/public/assets/distCSS/auto';
		if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
			return $original; // ФС недоступна — оставляем стили инлайн.
		}

		$links = [];
		foreach ($blocks as $css) {
			$hash = substr(sha1($css), 0, 16);
			$file = $dir . '/p-' . $hash . '.css';
			if (!is_file($file)) {
				if (@file_put_contents($file, $css, LOCK_EX) === false) {
					return $original; // не записали — лучше инлайн, чем страница без стилей
				}
			}
			// mtime = время последнего использования: по нему чистим неиспользуемые файлы.
			@touch($file);
			// ?v= не нужен: имя файла — хеш содержимого, изменился CSS — изменилось имя.
			$links[] = '<link rel="stylesheet" href="/public/assets/distCSS/auto/p-' . $hash . '.css">';
		}

		self::cleanupAutoCss($dir);

		$pos = stripos($html, '</head>');
		$head = implode("\n", $links);
		if ($pos === false) {
			return $head . $html;
		}
		return substr($html, 0, $pos) . $head . "\n" . substr($html, $pos);
	}

	/**
	 * Уборка неиспользуемых auto-CSS файлов (раз в час).
	 *
	 * Файлы иммутабельны (имя = хеш содержимого), но после правки шаблона
	 * старый файл остаётся навсегда. Чистим те, к которым не обращались
	 * дольше двух недель: страница при этом не ломается — при следующем
	 * рендере файл создаётся заново.
	 */
	private static function cleanupAutoCss(string $dir): void
	{
		$marker = \App\Models\Cache\Path::file('auto-css-cleanup.txt');
		if ($marker !== '' && is_file($marker) && (time() - (int)@filemtime($marker)) < 3600) {
			return;
		}
		if ($marker !== '') {
			@touch($marker);
		}

		$files = glob($dir . '/p-*.css');
		if (!is_array($files)) {
			return;
		}
		$now = time();
		foreach ($files as $file) {
			if (($now - (int)@filemtime($file)) > 86400 * 14) {
				@unlink($file);
			}
		}
	}
}
