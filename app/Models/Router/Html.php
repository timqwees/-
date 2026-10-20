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
			$html = str_replace('?v=20261009', '?v=' . $ver, $html);
			$html = str_replace('/public/assets/distJS/main.js"', '/public/assets/distJS/main.js?v=' . $ver . '"', $html);
			$html = str_replace("/public/assets/distJS/main.js'", '/public/assets/distJS/main.js?v=' . $ver . "'", $html);
		}
		return Image::inject($html);
	}
}
