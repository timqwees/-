<?php

declare(strict_types=1);

namespace App\Models\Cache;

/**
 * Пути рантайм-кэша проекта.
 *
 * Кэш намеренно лежит ВНЕ каталога проекта: внутри проекта нет ни var/,
 * ни временных файлов — в репозитории остаётся только код и контент.
 *
 * Порядок выбора базовой директории:
 *   1) FSS_CACHE_DIR (env) — если задана и доступна на запись;
 *   2) системный временный каталог (sys_get_temp_dir()) + '/fss-cache';
 *   3) домашний каталог пользователя + '/.fss-cache'.
 *
 * Если ни одно место недоступно на запись — кэш просто отключается (base() = ''),
 * сайт продолжает работать: все вызовы cacheGet/cachePut умеют работать без файлов.
 */
final class Path
{
	private static ?string $base = null;

	/** Базовая директория кэша или '' — если кэшировать некуда. */
	public static function base(): string
	{
		if (self::$base !== null) {
			return self::$base;
		}

		$candidates = [];
		$env = trim((string)($_ENV['FSS_CACHE_DIR'] ?? getenv('FSS_CACHE_DIR') ?: ''));
		if ($env !== '') {
			$candidates[] = rtrim($env, '/');
		}
		$tmp = rtrim((string)sys_get_temp_dir(), '/');
		if ($tmp !== '') {
			$candidates[] = $tmp . '/fss-cache';
		}
		$home = rtrim((string)($_ENV['HOME'] ?? getenv('HOME') ?: ''), '/');
		if ($home !== '') {
			$candidates[] = $home . '/.fss-cache';
		}

		foreach ($candidates as $dir) {
			if (self::ensure($dir)) {
				return self::$base = $dir;
			}
		}

		return self::$base = '';
	}

	/** Полный путь к файлу в кэше ('' — если кэш недоступен). */
	public static function file(string $rel): string
	{
		$base = self::base();
		if ($base === '') {
			return '';
		}
		return $base . '/' . ltrim($rel, '/');
	}

	/** Создать директорию (рекурсивно) и проверить доступность на запись. */
	public static function ensure(string $dir): bool
	{
		if ($dir === '') {
			return false;
		}
		if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			return false;
		}
		return is_writable($dir);
	}

	/** Сбросить запомненный путь (используется в тестах). */
	public static function reset(): void
	{
		self::$base = null;
	}
}
