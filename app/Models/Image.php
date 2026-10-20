<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Размеры изображений — чтобы браузер резервировал место под картинку.
 *
 * Зачем: <img> без width/height при загрузке меняет высоту блока → секция «уезжает»
 * во время прокрутки (CLS). Здесь размеры подставляются на сервере, до отдачи HTML:
 * браузер сразу резервирует правильный бокс, lazy-картинки не «выталкивают» контент.
 *
 * Размеры берутся из getimagesize() и кэшируются в var/cache/img_dims.json.
 */
final class Image
{
	private const CACHE_FILE = '/var/cache/img_dims.json';

	/** @var array<string, array{0:int,1:int}|null> */
	private static array $memo = [];

	/** @var array<string, array{0:int,1:int}|null>|null */
	private static ?array $store = null;

	private static string $root = '';

	private static function root(): string
	{
		if (self::$root === '') {
			self::$root = dirname(__DIR__, 2);
		}
		return self::$root;
	}

	/**
	 * Добавляет width/height к <img>, у которых их нет.
	 * Изображения без локального файла (внешние, data:, svg) не трогаем.
	 */
	public static function inject(string $html): string
	{
		if (strpos($html, '<img') === false) {
			return $html;
		}
		return (string) preg_replace_callback('/<img\b[^>]*>/i', static function (array $m): string {
			$tag = $m[0];
			// Уже есть размеры или aspect-ratio — не трогаем.
			if (preg_match('/\bwidth\s*=/i', $tag) || preg_match('/\bheight\s*=/i', $tag)) {
				return $tag;
			}
			if (preg_match('/aspect-ratio/i', $tag)) {
				return $tag;
			}
			if (!preg_match('/\bsrc\s*=\s*["\']([^"\']+)["\']/i', $tag, $sm)) {
				return $tag;
			}
			$dims = self::dims($sm[1]);
			if ($dims === null) {
				return $tag;
			}
			$attr = ' width="' . $dims[0] . '" height="' . $dims[1] . '"';
			// Вставляем перед закрывающим ">" (с учётом " />").
			return (string) preg_replace('/\s*\/?>$/', $attr . '/>', $tag, 1);
		}, $html);
	}

	/**
	 * @return array{0:int,1:int}|null — размеры в пикселях или null
	 */
	public static function dims(string $src): ?array
	{
		$src = html_entity_decode(trim($src), ENT_QUOTES);
		if ($src === '' || str_starts_with($src, 'data:') || preg_match('~^(https?:)?//~i', $src)) {
			return null;
		}
		// Отбрасываем query/fragment и декодируем %-экранирование кириллицы в путях.
		$src = (string) preg_replace('/[?#].*$/', '', $src);
		$src = rawurldecode($src);
		if ($src === '') {
			return null;
		}

		$file = self::localPath($src);
		if ($file === null) {
			return null;
		}
		if (array_key_exists($file, self::$memo)) {
			return self::$memo[$file];
		}
		$size = self::fromStore($file);
		if ($size === null) {
			if (!is_file($file)) {
				self::$memo[$file] = null;
				return null;
			}
			$info = @getimagesize($file);
			$size = ($info && (int) $info[0] > 0 && (int) $info[1] > 0) ? [(int) $info[0], (int) $info[1]] : null;
			self::toStore($file, $size);
		}
		self::$memo[$file] = $size;
		return $size;
	}

	/** @return string|null — абсолютный путь к локальному файлу */
	private static function localPath(string $src): ?string
	{
		$root = self::root();
		$prefix = '/public/';
		if (str_starts_with($src, $prefix)) {
			$rel = substr($src, strlen($prefix));
		} elseif (str_starts_with($src, 'public/')) {
			$rel = substr($src, strlen('public/'));
		} else {
			return null;
		}
		if ($rel === '' || str_contains($rel, '..')) {
			return null;
		}
		$path = $root . '/public/' . $rel;
		return is_file($path) ? $path : null;
	}

	/** @return array{0:int,1:int}|null */
	private static function fromStore(string $file): ?array
	{
		if (self::$store === null) {
			self::$store = self::readStore();
		}
		$key = substr($file, strlen(self::root()) + 1);
		return self::$store[$key] ?? null;
	}

	/** @param array{0:int,1:int}|null $size */
	private static function toStore(string $file, ?array $size): void
	{
		if (self::$store === null) {
			self::$store = self::readStore();
		}
		$key = substr($file, strlen(self::root()) + 1);
		self::$store[$key] = $size;
		self::writeStore();
	}

	/** @return array<string, array{0:int,1:int}|null> */
	private static function readStore(): array
	{
		$path = self::root() . self::CACHE_FILE;
		if (!is_file($path)) {
			return [];
		}
		$raw = @file_get_contents($path);
		if ($raw === false || $raw === '') {
			return [];
		}
		$data = json_decode($raw, true);
		return is_array($data) ? $data : [];
	}

	private static function writeStore(): void
	{
		$dir = self::root() . '/var/cache';
		if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			return;
		}
		@file_put_contents($dir . '/img_dims.json', (string) json_encode(self::$store, JSON_UNESCAPED_SLASHES), LOCK_EX);
	}
}
