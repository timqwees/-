<?php

declare(strict_types=1);

namespace App\Mail;

/**
 * Долговечная очередь заявок.
 *
 * Зачем: если письмо не ушло (SMTP недоступен, ящик-получатель не существует,
 * пароль отозван), заявка не должна потеряться. Она кладётся в очередь на диск
 * и уходит при следующей попытке — из cron-задачи или при следующей отправке
 * формы с сайта.
 *
 * Где хранится: var/outbox/ в корне проекта (каталог закрыт в .htaccess),
 * а если корень недоступен на запись — в рантайм-кэше.
 *
 * Формат: один JSON-файл на заявку, имя = id. Запись атомарная
 * (пишем во временный файл и переименовываем), поэтому оборванный процесс
 * не оставит «половинчатую» заявку.
 */
final class Outbox
{
	/** Каталог очереди внутри проекта. */
	private const DIR = 'var/outbox/';

	/** Не чаще раза в N секунд пробуем переотправить очередь (защита от шторма). */
	private const RETRY_COOLDOWN = 60;

	/**
	 * Положить письмо в очередь.
	 *
	 * @param array<string,string> $mail to, subject, body, alt_body, reply_to, reply_name, kind
	 *
	 * @return string id заявки ('' — если записать не удалось).
	 */
	public static function put(array $mail): string
	{
		$id = date('Ymd-His') . '-' . bin2hex(random_bytes(4));
		$payload = [
			'id' => $id,
			'created' => time(),
			'kind' => (string)($mail['kind'] ?? 'lead'),
			'to' => (string)($mail['to'] ?? ''),
			'subject' => (string)($mail['subject'] ?? ''),
			'body' => (string)($mail['body'] ?? ''),
			'alt_body' => (string)($mail['alt_body'] ?? ''),
			'reply_to' => (string)($mail['reply_to'] ?? ''),
			'reply_name' => (string)($mail['reply_name'] ?? ''),
			'lead' => is_array($mail['lead'] ?? null) ? $mail['lead'] : [],
			'attempts' => 0,
			'last_error' => '',
			'last_attempt' => 0,
		];

		$json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		if ($json === false) {
			return '';
		}

		$dir = self::dir();
		if ($dir === '') {
			return '';
		}
		$file = $dir . '/' . $id . '.json';
		$tmp = $file . '.tmp';
		if (@file_put_contents($tmp, $json, LOCK_EX) === false) {
			return '';
		}
		if (!@rename($tmp, $file)) {
			@unlink($tmp);
			return '';
		}
		@chmod($file, 0600);

		return $id;
	}

	/**
	 * Все заявки в очереди (по времени создания).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function pending(): array
	{
		$dir = self::dir();
		if ($dir === '') {
			return [];
		}
		$files = glob($dir . '/*.json');
		if (!is_array($files) || $files === []) {
			return [];
		}
		sort($files);

		$items = [];
		foreach ($files as $file) {
			$raw = @file_get_contents($file);
			if ($raw === false || $raw === '') {
				continue;
			}
			$item = json_decode($raw, true);
			if (!is_array($item) || ($item['to'] ?? '') === '') {
				continue;
			}
			$item['file'] = $file;
			$items[] = $item;
		}
		usort($items, static fn(array $a, array $b): int => ((int)($a['created'] ?? 0)) <=> ((int)($b['created'] ?? 0)));

		return $items;
	}

	/** Количество заявок в очереди. */
	public static function count(): int
	{
		return count(self::pending());
	}

	/** Убрать заявку из очереди (после успешной отправки). */
	public static function remove(string $id): void
	{
		if ($id === '' || !preg_match('/^[a-zA-Z0-9\-]+$/', $id)) {
			return;
		}
		$dir = self::dir();
		if ($dir === '') {
			return;
		}
		@unlink($dir . '/' . $id . '.json');
	}

	/**
	 * Попробовать отправить накопленное.
	 *
	 * @param int    $budgetSeconds Сколько секунд максимум тратим на попытку.
	 * @param string $onlyKind      '' — все заявки; 'lead' — только заявки клиентов
	 *                              (копии клиенту не срочные, их удобнее отправить
	 *                              по cron — так текущая заявка не ждёт лишнего).
	 *
	 * @return array{sent:int,failed:int,remaining:int}
	 */
	public static function flush(int $budgetSeconds = 10, string $onlyKind = ''): array
	{
		// Отправка выключена — очередь ждёт включения, не тратим время на попытки.
		if (Transport::isOff()) {
			return ['sent' => 0, 'failed' => 0, 'remaining' => self::count()];
		}

		$items = self::pending();
		if ($items === []) {
			return ['sent' => 0, 'failed' => 0, 'remaining' => 0];
		}
		if ($onlyKind !== '') {
			$items = array_values(array_filter(
				$items,
				static fn(array $item): bool => (string)($item['kind'] ?? 'lead') === $onlyKind
			));
			if ($items === []) {
				return ['sent' => 0, 'failed' => 0, 'remaining' => self::count()];
			}
		}

		return Transport::withDeadline(
			(float)max(1, $budgetSeconds),
			static function () use ($items): array {
				return self::flushItems($items);
			}
		);
	}

	/**
	 * Попытка отправки конкретных заявок (в пределах текущего бюджета времени).
	 *
	 * @param array<int,array<string,mixed>> $items
	 *
	 * @return array{sent:int,failed:int,remaining:int}
	 */
	private static function flushItems(array $items): array
	{
		$sent = 0;
		$failed = 0;

		foreach ($items as $item) {
			// Не долбим: если по этой заявке только что пробовали — ждём cooldown.
			if ((int)($item['last_attempt'] ?? 0) > 0 && (time() - (int)$item['last_attempt']) < self::RETRY_COOLDOWN) {
				continue;
			}
			if (Transport::remaining() < 3.0) {
				break;
			}

			$result = Transport::send(
				[
					'to_email' => (string)($item['to'] ?? ''),
					'subject' => (string)($item['subject'] ?? ''),
					'body' => (string)($item['body'] ?? ''),
					'alt_body' => (string)($item['alt_body'] ?? ''),
					'reply_to' => (string)($item['reply_to'] ?? ''),
					'reply_name' => (string)($item['reply_name'] ?? ''),
					'lead' => is_array($item['lead'] ?? null) ? $item['lead'] : [],
				],
				// Копия клиенту — это письмо, её нельзя отправлять в CRM
				// (получится лид с чужим email).
				(string)($item['kind'] ?? 'lead') === 'copy'
					? [Transport::MODE_SMTP, Transport::MODE_SENDMAIL]
					: []
			);

			if ($result['ok']) {
				self::remove((string)($item['id'] ?? ''));
				$sent++;
				error_log('[outbox] Заявка ' . ($item['id'] ?? '') . ' отправлена транспортом: ' . $result['transport']);
				continue;
			}

			$failed++;
			self::markAttempted((string)($item['id'] ?? ''), (string)($item['file'] ?? ''), $result['error']);
			error_log('[outbox] Заявка ' . ($item['id'] ?? '') . ' не отправлена: ' . $result['error']);
			// Транспорт не работает — дальше пробовать бессмысленно.
			break;
		}

		return ['sent' => $sent, 'failed' => $failed, 'remaining' => self::count()];
	}

	/** Записать результат неудачной попытки (счётчик попыток + текст ошибки). */
	private static function markAttempted(string $id, string $file, string $error): void
	{
		if ($id === '' || $file === '' || !is_file($file)) {
			return;
		}
		$item = json_decode((string)@file_get_contents($file), true);
		if (!is_array($item)) {
			return;
		}
		$item['attempts'] = (int)($item['attempts'] ?? 0) + 1;
		$item['last_error'] = mb_substr($error, 0, 500);
		$item['last_attempt'] = time();
		$json = json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		if ($json === false) {
			return;
		}
		$tmp = $file . '.tmp';
		if (@file_put_contents($tmp, $json, LOCK_EX) !== false) {
			@rename($tmp, $file);
		}
	}

	/**
	 * Каталог очереди: сначала var/outbox/ в проекте, иначе рантайм-кэш.
	 * Если недоступно ни то, ни другое — очередь выключена ('').
	 */
	public static function dir(): string
	{
		static $dir = null;
		if ($dir !== null) {
			return $dir;
		}

		$project = dirname(__DIR__, 2) . '/' . self::DIR;
		if (self::ensureDir($project)) {
			self::protectVar();
			return $dir = rtrim($project, '/');
		}

		$cache = \App\Models\Cache\Path::file('outbox/');
		if ($cache !== '' && self::ensureDir($cache)) {
			return $dir = rtrim($cache, '/');
		}

		return $dir = '';
	}

	/**
	 * Защита каталога var/ на случай, если корневой .htaccess на сервере
	 * не обновился: в var/ кладём свой .htaccess с запретом доступа.
	 * Внутри — ФИО и телефоны клиентов, отдавать их наружу нельзя.
	 */
	private static function protectVar(): void
	{
		$file = dirname(__DIR__, 2) . '/var/.htaccess';
		if (is_file($file)) {
			return;
		}
		@file_put_contents(
			$file,
			"# Служебные данные сайта (очередь заявок, счётчики) — наружу не отдаём.\n"
			. "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n"
			. "<IfModule !mod_authz_core.c>\n    Order allow,deny\n    Deny from all\n</IfModule>\n",
			LOCK_EX
		);
	}

	/** Создать каталог, если нужно, и проверить доступность на запись. */
	private static function ensureDir(string $dir): bool
	{
		if ($dir === '') {
			return false;
		}
		if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
			return false;
		}
		return is_writable($dir);
	}
}
