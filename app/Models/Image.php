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
 * Размеры берутся из getimagesize() и кэшируются в рантайм-кэше (вне проекта).
 */
final class Image
{
	private const CACHE_FILE = 'img_dims.json';

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
			$src = $sm[1];
			// Lazy-картинка: реальный файл лежит в data-src, а src — data:-заглушка.
			// Размеры берём по data-src, иначе браузер не резервирует место (CLS).
			if (str_starts_with(trim($src), 'data:')
				&& preg_match('/\bdata-src\s*=\s*["\']([^"\']+)["\']/i', $tag, $dm)) {
				$src = $dm[1];
			}
			$dims = self::dims($src);
			if ($dims === null) {
				return $tag;
			}
			$attr = ' width="' . $dims[0] . '" height="' . $dims[1] . '"';
			// Вставляем перед закрывающим ">" (с учётом " />").
			return (string) preg_replace('/\s*\/?>$/', $attr . '/>', $tag, 1);
		}, $html);
	}

	/**
	 * Быстрое чтение размеров из заголовка файла.
	 *
	 * Почему не getimagesize(): на больших прогрессивных JPEG (фото с объектов по
	 * 10–30 МБ) getimagesize() читает файл целиком — до 2–5 секунд на файл.
	 * Здесь читаются только первые килобайты: PNG/JPEG/WEBP/AVIF/GIF.
	 *
	 * @return array{0:int,1:int}|null
	 */
	public static function fastSize(string $file): ?array
	{
		$fh = @fopen($file, 'rb');
		if (!$fh) {
			return null;
		}
		try {
			$head = (string) fread($fh, 65536);
			if ($head === '') {
				return null;
			}
			// PNG: IHDR на фиксированном месте
			if (str_starts_with($head, "\x89PNG\r\n\x1a\n") && strlen($head) >= 24) {
				$w = unpack('N', substr($head, 16, 4));
				$h = unpack('N', substr($head, 20, 4));
				return [(int)$w[1], (int)$h[1]];
			}
			// GIF
			if (str_starts_with($head, 'GIF') && strlen($head) >= 10) {
				$w = unpack('v', substr($head, 6, 2));
				$h = unpack('v', substr($head, 8, 2));
				return [(int)$w[1], (int)$h[1]];
			}
			// WEBP (RIFF....WEBP)
			if (str_starts_with($head, 'RIFF') && substr($head, 8, 4) === 'WEBP') {
				if (substr($head, 12, 4) === 'VP8 ') {
					return [unpack('v', substr($head, 26, 2))[1] & 0x3FFF, unpack('v', substr($head, 28, 2))[1] & 0x3FFF];
				}
				if (substr($head, 12, 4) === 'VP8L') {
					$b = unpack('V', substr($head, 21, 4))[1];
					return [($b & 0x3FFF) + 1, (($b >> 14) & 0x3FFF) + 1];
				}
				if (substr($head, 12, 4) === 'VP8X') {
					$w = unpack('V', substr($head, 24, 3) . "\x00")[1] + 1;
					$h = unpack('V', substr($head, 27, 3) . "\x00")[1] + 1;
					return [$w, $h];
				}
			}
			// AVIF/HEIF: ищем изо-бокс 'ispe' внутри ftyp/moov (без тяжёлых регулярок)
			if (str_contains($head, 'ftyp')) {
				$pos = strpos($head, 'ispe');
				if ($pos !== false && $pos + 16 <= strlen($head)) {
					$w = unpack('N', substr($head, $pos + 8, 4))[1];
					$h = unpack('N', substr($head, $pos + 12, 4))[1];
					if ($w > 0 && $h > 0) {
						return [$w, $h];
					}
				}
			}
			// JPEG: ищем маркер SOF0..SOF15 (кроме DHT/DAC/RST)
			if (str_starts_with($head, "\xFF\xD8")) {
				$len = strlen($head);
				$i = 2;
				while ($i + 9 < $len) {
					if ($head[$i] !== "\xFF") {
						$i++;
						continue;
					}
					$marker = ord($head[$i + 1]);
					if ($marker === 0xD8 || $marker === 0x01 || ($marker >= 0xD0 && $marker <= 0xD7)) {
						$i += 2;
						continue;
					}
					if ($i + 4 > $len) {
						break;
					}
					$segLen = unpack('n', substr($head, $i + 2, 2))[1];
					$isSof = ($marker >= 0xC0 && $marker <= 0xCF)
						&& $marker !== 0xC4 && $marker !== 0xC8 && $marker !== 0xCC;
					if ($isSof && $i + 9 <= $len) {
						$h = unpack('n', substr($head, $i + 5, 2))[1];
						$w = unpack('n', substr($head, $i + 7, 2))[1];
						if ($w > 0 && $h > 0) {
							return [$w, $h];
						}
					}
					if ($marker === 0xDA) {
						break; // начало сканирования — дальше размеров нет
					}
					$i += 2 + $segLen;
				}
			}
		} finally {
			fclose($fh);
		}
		return null;
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
			$info = self::fastSize($file);
			$size = ($info && (int) $info[0] > 0 && (int) $info[1] > 0) ? [(int) $info[0], (int) $info[1]] : null;
			self::toStore($file, $size);
		}
		self::$memo[$file] = $size;
		return $size;
	}

	/**
	 * Публичный URL файла из данных (projects.php и т.п.).
	 *
	 * Файлы могут лежать в двух местах:
	 *   - public/assets/image/services/<файл> — основные фото услуг;
	 *   - public/assets/image/<файл>           — swiper/, og/ и прочее.
	 * Путь в данных указывается относительно public/assets/image/.
	 */
	public static function publicUrl(string $file): string
	{
		$file = ltrim(str_replace('\\', '/', trim($file)), '/');
		if ($file === '' || str_contains($file, '..')) {
			return '/public/assets/image/services/';
		}
		$root = self::root();
		if (is_file($root . '/public/assets/image/services/' . $file)) {
			return '/public/assets/image/services/' . $file;
		}
		if (is_file($root . '/public/assets/image/' . $file)) {
			return '/public/assets/image/' . $file;
		}
		return '/public/assets/image/services/' . $file;
	}

	/**
	 * Заменить тяжёлые .jpg/.jpeg/.png на их .webp-варианты.
	 *
	 * Зачем: рядом с каждым фото лежит .webp (напр. banner.jpeg — 112 КБ,
	 * banner.jpeg.webp — 79 КБ, .avif — 52 КБ), но в разметке стоял .jpg —
	 * браузер тянул тяжёлую версию. Здесь подмена делается на сервере, при
	 * отдаче HTML, поэтому править десятки шаблонов не нужно.
	 *
	 * Важно: подменяется только src/data-src — сам тег <img> не меняется,
	 * структура DOM остаётся прежней (иначе сломались бы CSS-селекторы
	 * вида .step3-photo>img).
	 *
	 * @return string
	 */
	public static function optimize(string $html): string
	{
		if (stripos($html, '<img') === false) {
			return $html;
		}
		return (string) preg_replace_callback(
			'/<img\b[^>]*>/i',
			static function (array $m): string {
				$tag = $m[0];
				$changed = false;

				// src
				$tag = (string) preg_replace_callback(
					'/\b(src)\s*=\s*(["\'])([^"\']+)\2/i',
					static function (array $s) use (&$changed): string {
						$webp = self::webpVariant($s[3]);
						if ($webp === null) {
							return $s[0];
						}
						$changed = true;
						return $s[1] . '=' . $s[2] . $webp . $s[2];
					},
					$tag
				);

				// data-src (ленивая загрузка: реальный файл подставляет JS)
				$tag = (string) preg_replace_callback(
					'/\b(data-src)\s*=\s*(["\'])([^"\']+)\2/i',
					static function (array $s) use (&$changed): string {
						$webp = self::webpVariant($s[3]);
						if ($webp === null) {
							return $s[0];
						}
						$changed = true;
						return $s[1] . '=' . $s[2] . $webp . $s[2];
					},
					$tag
				);

				if (!$changed) {
					return $tag;
				}

				// srcset: подменить пути и убрать атрибут, если все кандидаты
				// указывают на один и тот же файл (тогда он ничего не даёт).
				return (string) preg_replace_callback(
					'/\b(srcset)\s*=\s*(["\'])([^"\']+)\2/i',
					static function (array $s): string {
						$parts = array_map('trim', explode(',', $s[3]));
						$urls = [];
						foreach ($parts as $part) {
							if ($part === '') {
								continue;
							}
							$bits = preg_split('/\s+/', $part) ?: [$part];
							$url = $bits[0];
							$webp = self::webpVariant($url);
							$bits[0] = $webp ?? $url;
							$urls[] = $bits[0];
							$parts[count($urls) - 1] = implode(' ', $bits);
						}
						$unique = array_values(array_unique($urls));
						if (count($unique) <= 1) {
							return '';
						}
						return $s[1] . '=' . $s[2] . implode(', ', $parts) . $s[2];
					},
					$tag
				);
			},
			$html
		);
	}

	/**
	 * WebP-вариант локального URL (путь + '.webp') либо null.
	 *
	 * Соглашение проекта: вариант лежит рядом с оригиналом с добавленным
	 * расширением (banner.jpeg → banner.jpeg.webp), поэтому к URL просто
	 * приписывается '.webp' — расширение оригинала не вырезается.
	 *
	 * @return string|null
	 */
	private static function webpVariant(string $url): ?string
	{
		$url = trim($url);
		if ($url === '' || str_starts_with($url, 'data:') || preg_match('~^(https?:)?//~i', $url)) {
			return null;
		}
		// query/fragment убираем, %-экранирование кириллицы раскрываем
		$clean = (string) preg_replace('/[?#].*$/', '', $url);
		$clean = rawurldecode($clean);
		if (!str_starts_with($clean, '/public/')) {
			return null;
		}
		$root = self::root();
		$path = $root . $clean;
		if (!is_file($path) || !is_file($path . '.webp')) {
			return null;
		}
		// Возвращаем исходный URL с приписанным '.webp' (кириллица не трогается).
		$suffix = substr($url, strlen($clean));
		return $clean . '.webp' . $suffix;
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
		$path = \App\Models\Cache\Path::file(self::CACHE_FILE);
		if ($path === '' || !is_file($path)) {
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
		$path = \App\Models\Cache\Path::file(self::CACHE_FILE);
		if ($path === '') {
			return;
		}
		@file_put_contents($path, (string) json_encode(self::$store, JSON_UNESCAPED_SLASHES), LOCK_EX);
	}
}
