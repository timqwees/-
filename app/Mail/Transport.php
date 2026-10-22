<?php

declare(strict_types=1);

namespace App\Mail;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Транспорт доставки заявок.
 *
 * Транспорты (MAIL_TRANSPORT в .env, через запятую — порядок попыток):
 *   smtp     — PHPMailer, SMTP-сервер из .env (основной);
 *   auto     — сокращение для "smtp,sendmail";
 *   sendmail — PHP mail() через локальный MTA хостинга (запасной);
 *   bitrix   — вебхук Bitrix24 (crm.lead.add), заявка падает прямо в CRM;
 *   off      — отправка выключена: заявки только копятся в очереди
 *              (App\Mail\Outbox) и уйдут, как только транспорт включат.
 *
 * Примеры: "smtp" (по умолчанию), "bitrix", "bitrix,smtp", "off".
 *
 * Настройки (все — в .env):
 *   SMTP_HOST      — SMTP-сервер (по умолчанию smtp.gmail.com);
 *   SMTP_PORT      — порт (по умолчанию EMAIL_PORT, иначе 587);
 *   SMTP_SECURE    — tls | ssl | none (по умолчанию tls);
 *   SMTP_USER      — логин (по умолчанию EMAIL);
 *   SMTP_PASS      — пароль (по умолчанию EMAIL_PASSWORD);
 *   SMTP_TIMEOUT   — таймаут соединения/чтения, сек (по умолчанию 15);
 *   EMAIL_FROM     — адрес отправителя (для sendmail это должен быть ящик
 *                    на домене, иначе SPF не проходит и письмо уходит в спам);
 *   BITRIX_WEBHOOK — адрес вебхука Bitrix24 (секрет, в репозиторий не попадает);
 *   BITRIX_SOURCE  — идентификатор источника в CRM (необязательно);
 *   MAIL_TRANSPORT — список транспортов (по умолчанию smtp).
 */
final class Transport
{
	public const MODE_AUTO = 'auto';
	public const MODE_SMTP = 'smtp';
	public const MODE_SENDMAIL = 'sendmail';
	public const MODE_BITRIX = 'bitrix';
	public const MODE_OFF = 'off';

	/** Таймаут SMTP-соединения и чтения ответа, сек. */
	private const SMTP_TIMEOUT = 15;

	/** Сколько раз пробуем SMTP подряд при сетевом сбое. */
	private const SMTP_ATTEMPTS = 2;

	/**
	 * Общий бюджет времени на почту за один запрос, сек.
	 * Пользователь не должен долго смотреть на «отправляем…»: если письмо
	 * не уходит за 12 секунд — переходим к запасному транспорту, а если и он
	 * недоступен — кладём заявку в очередь (App\Mail\Outbox).
	 */
	private const BUDGET = 12;

	/** Момент, когда бюджет времени исчерпан (0 — бюджет не задан). */
	private static float $deadline = 0.0;

	/**
	 * Задать бюджет времени на отправку (в секундах) для текущего запроса.
	 */
	public static function startBudget(float $seconds = self::BUDGET): void
	{
		self::$deadline = $seconds > 0 ? microtime(true) + $seconds : 0.0;
	}

	/** Сколько секунд ещё осталось в бюджете (PHP_INT_MAX, если бюджет не задан). */
	public static function remaining(): float
	{
		if (self::$deadline <= 0.0) {
			return (float)PHP_INT_MAX;
		}
		return self::$deadline - microtime(true);
	}

	/**
	 * Выполнить действие с более жёстким бюджетом времени.
	 * Используется, чтобы фоновая отправка накопленных заявок не съедала
	 * время текущей заявки.
	 *
	 * @template T
	 * @param callable():T $fn
	 * @return T
	 */
	public static function withDeadline(float $seconds, callable $fn)
	{
		$previous = self::$deadline;
		$target = microtime(true) + $seconds;
		if ($previous > 0.0 && $previous < $target) {
			$target = $previous;
		}
		self::$deadline = $target;
		try {
			return $fn();
		} finally {
			self::$deadline = $previous;
		}
	}
	/**
	 * Отправить заявку, перебрав транспорты по очереди.
	 *
	 * @param array<string,mixed> $data    to_email, subject, body, alt_body,
	 *                                     reply_to, reply_name, cc, bcc, lead
	 * @param array<int,string>    $allowed Ограничить список транспортов
	 *                                     (например, для копии клиенту — только почтовые).
	 *
	 * @return array{ok:bool,transport:string,error:string}
	 */
	public static function send(array $data, array $allowed = []): array
	{
		$chain = self::chain();
		if ($allowed !== []) {
			$chain = array_values(array_filter($chain, static fn(string $t): bool => in_array($t, $allowed, true)));
			if ($chain === []) {
				return self::fail('', 'нет подходящих транспортов');
			}
		}
		$errors = [];

		foreach ($chain as $transport) {
			// Выключено — дальше идти некуда: заявку заберёт очередь.
			if ($transport === self::MODE_OFF) {
				return self::fail(self::MODE_OFF, 'отправка выключена (MAIL_TRANSPORT=off)');
			}

			$result = match ($transport) {
				self::MODE_SENDMAIL => self::sendSendmail($data),
				self::MODE_BITRIX => self::sendBitrix($data),
				default => self::sendSmtp($data),
			};
			if ($result['ok']) {
				return $result;
			}
			$errors[] = $transport . ': ' . $result['error'];

			// Бюджет исчерпан — остальные транспорты тоже не успеют.
			if (self::remaining() < 1.0) {
				break;
			}
		}

		return self::fail($chain[0] ?? '', $errors !== [] ? implode(' | ', $errors) : 'нет доступных транспортов');
	}

	/** Есть ли в цепочке почтовые транспорты (письма)? */
	public static function hasMailTransport(): bool
	{
		foreach (self::chain() as $transport) {
			if ($transport === self::MODE_SMTP || $transport === self::MODE_SENDMAIL) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Отправка через SMTP (PHPMailer) с одной повторной попыткой
	 * при сетевом сбое (SMTP-сервер не ответил / соединение оборвалось).
	 * Ошибка авторизации не повторяется — там нужен новый пароль, а не retry.
	 *
	 * @param array<string,string> $data
	 *
	 * @return array{ok:bool,transport:string,error:string}
	 */
	public static function sendSmtp(array $data): array
	{
		$lastError = '';

		for ($attempt = 1; $attempt <= self::SMTP_ATTEMPTS; $attempt++) {
			// Бюджет исчерпан — дальше ждать нет смысла, отдаём заявку в очередь.
			if (self::remaining() < 3.0) {
				return self::fail(self::MODE_SMTP, $lastError !== '' ? $lastError : 'исчерпан лимит времени на отправку');
			}
			$result = self::sendSmtpOnce($data);
			if ($result['ok']) {
				return $result;
			}
			$lastError = $result['error'];
			if ($attempt < self::SMTP_ATTEMPTS && self::isTransientError($lastError) && self::remaining() > 5.0) {
				error_log('[mail] SMTP: сетевой сбой (' . $lastError . '), повтор через 2 c');
				sleep(2);
				continue;
			}
			break;
		}

		return self::fail(self::MODE_SMTP, $lastError);
	}

	/**
	 * Одно SMTP-соединение с отправкой письма.
	 *
	 * @param array<string,string> $data
	 *
	 * @return array{ok:bool,transport:string,error:string}
	 */
	private static function sendSmtpOnce(array $data): array
	{
		$config = self::config();

		try {
			$mail = new PHPMailer(true);
			$mail->CharSet = 'UTF-8';
			$mail->isSMTP();
			$mail->SMTPDebug = 0;
			$mail->Host = $config['host'];
			$mail->Port = $config['port'];
			$mail->SMTPSecure = $config['secure'] === 'none' ? '' : $config['secure'];
			// Таймаут не больше остатка бюджета: одна попытка не должна
			// длиться дольше, чем весь запрос на отправку.
			$remaining = self::remaining();
			$timeout = $remaining === (float)PHP_INT_MAX
				? $config['timeout']
				: (int)max(3, min($config['timeout'], $remaining - 1));
			$mail->Timeout = $timeout;
			// У PHPMailer таймаут сокета не ограничивает ожидание ответа
			// (stream_select ждёт свой Timelimit = 300 c) — из-за этого
			// зависший SMTP-сервер держал запрос до конца max_execution_time.
			$mail->getSMTPInstance()->Timelimit = $timeout;
			$mail->SMTPAuth = $config['user'] !== '';
			$mail->Username = $config['user'];
			$mail->Password = $config['pass'];
			$mail->setFrom($config['from'], $config['name']);

			$to = self::asciiEmail((string)($data['to_email'] ?? ''));
			if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
				return self::fail(self::MODE_SMTP, 'Некорректный email получателя: ' . $to);
			}
			$mail->addAddress($to);

			$replyTo = self::asciiEmail((string)($data['reply_to'] ?? ''));
			if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
				$replyName = trim((string)($data['reply_name'] ?? ''));
				if ($replyName !== '') {
					$mail->addReplyTo($replyTo, $replyName);
				} else {
					$mail->addReplyTo($replyTo);
				}
			}

			foreach (['cc' => 'addCC', 'bcc' => 'addBCC'] as $key => $method) {
				$extra = self::asciiEmail((string)($data[$key] ?? ''));
				if ($extra !== '' && filter_var($extra, FILTER_VALIDATE_EMAIL)) {
					$mail->{$method}($extra);
				}
			}

			$mail->Subject = (string)($data['subject'] ?? '');
			$mail->msgHTML((string)($data['body'] ?? ''));
			$altBody = trim((string)($data['alt_body'] ?? ''));
			if ($altBody !== '') {
				$mail->AltBody = $altBody;
			}

			if (!$mail->send()) {
				return self::fail(self::MODE_SMTP, 'PHPMailer вернул false: ' . $mail->ErrorInfo);
			}
			$mail->clearAddresses();

			return ['ok' => true, 'transport' => self::MODE_SMTP, 'error' => ''];
		} catch (\Throwable $e) {
			return self::fail(self::MODE_SMTP, $e->getMessage());
		}
	}

	/**
	 * Отправка через локальный MTA (PHP mail()).
	 *
	 * Работает там, где внешний SMTP закрыт: письмо уходит с IP хостинга,
	 * поэтому отправитель должен быть ящиком на домене (SPF домена содержит
	 * IP хостинга).
	 *
	 * @param array<string,string> $data
	 *
	 * @return array{ok:bool,transport:string,error:string}
	 */
	public static function sendSendmail(array $data): array
	{
		if (!function_exists('mail')) {
			return self::fail(self::MODE_SENDMAIL, 'Функция mail() отключена на сервере (disable_functions)');
		}

		$config = self::config();

		$to = self::asciiEmail((string)($data['to_email'] ?? ''));
		if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
			return self::fail(self::MODE_SENDMAIL, 'Некорректный email получателя: ' . $to);
		}
		$from = self::asciiEmail($config['from']);
		if ($from === '' || !filter_var($from, FILTER_VALIDATE_EMAIL)) {
			return self::fail(self::MODE_SENDMAIL, 'Некорректный адрес отправителя (EMAIL_FROM): ' . $config['from']);
		}

		$subject = trim(preg_replace('/[\r\n]+/', ' ', (string)($data['subject'] ?? '')));
		if ($subject === '') {
			return self::fail(self::MODE_SENDMAIL, 'Пустая тема письма');
		}

		$html = (string)($data['body'] ?? '');
		$alt = trim((string)($data['alt_body'] ?? ''));
		if ($alt === '') {
			$alt = trim(preg_replace('/\s+/u', ' ', strip_tags($html)));
		}

		$boundary = 'fss' . bin2hex(random_bytes(12));
		$headers = [
			'From: ' . self::formatAddress($from, $config['name']),
			'MIME-Version: 1.0',
			'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
		];

		$replyTo = self::asciiEmail((string)($data['reply_to'] ?? ''));
		if ($replyTo !== '' && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
			$replyName = trim(preg_replace('/[\r\n]+/', ' ', (string)($data['reply_name'] ?? '')));
			$headers[] = $replyName !== ''
				? 'Reply-To: ' . self::formatAddress($replyTo, $replyName)
				: 'Reply-To: ' . $replyTo;
		}

		foreach (['cc' => 'Cc', 'bcc' => 'Bcc'] as $key => $header) {
			$extra = self::asciiEmail((string)($data[$key] ?? ''));
			if ($extra !== '' && filter_var($extra, FILTER_VALIDATE_EMAIL)) {
				$headers[] = $header . ': ' . $extra;
			}
		}

		$body = "--{$boundary}\r\n"
			. "Content-Type: text/plain; charset=UTF-8\r\n"
			. "Content-Transfer-Encoding: base64\r\n\r\n"
			. chunk_split(base64_encode($alt))
			. "\r\n--{$boundary}\r\n"
			. "Content-Type: text/html; charset=UTF-8\r\n"
			. "Content-Transfer-Encoding: base64\r\n\r\n"
			. chunk_split(base64_encode($html))
			. "\r\n--{$boundary}--";

		$sent = @mail(
			$to,
			'=?UTF-8?B?' . base64_encode($subject) . '?=',
			$body,
			implode("\r\n", $headers),
			'-f' . $from
		);

		if (!$sent) {
			return self::fail(self::MODE_SENDMAIL, 'mail() вернул false (письмо не принято локальным MTA)');
		}

		return ['ok' => true, 'transport' => self::MODE_SENDMAIL, 'error' => ''];
	}

	/**
	 * Отправка заявки в Bitrix24 через входящий вебхук.
	 *
	 * Формат вызова (проверен на рабочем вебхуке):
	 *   POST {BITRIX_WEBHOOK}{BITRIX_METHOD}.json
	 *   тело: fields[TITLE]=…&fields[CATEGORY_ID]=…&fields[STAGE_ID]=…&fields[COMMENTS]=…
	 *   (form-encoded, как принимает Bitrix24 REST)
	 *
	 * Данные берутся из $data['lead'] — структурированные поля заявки
	 * (имя, телефон, почта, комментарий), а не из текста письма.
	 *
	 * @param array<string,mixed> $data
	 *
	 * @return array{ok:bool,transport:string,error:string}
	 */
	public static function sendBitrix(array $data): array
	{
		$url = self::bitrixUrl();
		if ($url === '') {
			return self::fail(self::MODE_BITRIX, 'не задан BITRIX_WEBHOOK в .env');
		}
		if (!function_exists('curl_init')) {
			return self::fail(self::MODE_BITRIX, 'расширение curl недоступно на сервере');
		}

		$lead = is_array($data['lead'] ?? null) ? $data['lead'] : [];
		$fields = self::bitrixFields($lead);

		$post = http_build_query(['fields' => $fields]);
		$timeout = (int)max(3, min(15, self::remaining() === (float)PHP_INT_MAX ? 15 : self::remaining() - 1));

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => $timeout,
			CURLOPT_FOLLOWLOCATION => false,
		]);
		$raw = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		$status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		curl_close($ch);

		if ($raw === false || $errno !== 0) {
			return self::fail(self::MODE_BITRIX, 'ошибка запроса к вебхуку: ' . ($error !== '' ? $error : 'curl error ' . $errno));
		}

		$json = json_decode((string)$raw, true);
		if (!is_array($json)) {
			return self::fail(self::MODE_BITRIX, 'неожиданный ответ вебхука (HTTP ' . $status . '): ' . mb_substr((string)$raw, 0, 200));
		}
		if (isset($json['error'])) {
			$code = (string)$json['error'];
			$desc = (string)($json['error_description'] ?? $code);
			// INVALID_CREDENTIALS — вебхук недействителен: токен отозван или указан неверно.
			if ($code === 'INVALID_CREDENTIALS') {
				$desc .= ' — вебхук недействителен: проверьте токен в портале Bitrix24'
					. ' (CRM → Автоматизация → Входящие вебхуки) или сгенерируйте новый';
			}
			return self::fail(self::MODE_BITRIX, 'Bitrix24 вернул ошибку: ' . $desc);
		}
		if (!array_key_exists('result', $json)) {
			return self::fail(self::MODE_BITRIX, 'в ответе Bitrix24 нет result: ' . mb_substr((string)$raw, 0, 200));
		}

		return ['ok' => true, 'transport' => self::MODE_BITRIX, 'error' => ''];
	}

	/**
	 * Адрес вызова Bitrix24: к базовому URL добавляется метод.
	 * Если в BITRIX_WEBHOOK уже указан метод (например …/crm.deal.add.json) —
	 * используем как есть.
	 */
	public static function bitrixUrl(): string
	{
		$base = rtrim(trim(self::env('BITRIX_WEBHOOK')), '/');
		if ($base === '') {
			return '';
		}
		$method = trim(self::env('BITRIX_METHOD', 'crm.deal.add'));
		if ($method === '') {
			$method = 'crm.deal.add';
		}
		// Метод уже прописан в URL (в нём есть точка, как у «crm.deal.add.json») —
		// не дублируем. Токен вебхука точек не содержит.
		if (preg_match('#/[a-z0-9_]+(\.[a-z0-9_]+)+(\.json)?$#i', $base)) {
			return $base;
		}
		return $base . '/' . $method . '.json';
	}

	/**
	 * Поля сделки Bitrix24 из данных заявки.
	 *
	 * @param array<string,mixed> $lead
	 *
	 * @return array<string,string>
	 */
	private static function bitrixFields(array $lead): array
	{
		$name = trim((string)($lead['name'] ?? ''));
		$phone = trim((string)($lead['phone'] ?? ''));
		$email = trim((string)($lead['email'] ?? ''));
		$company = trim((string)($lead['company'] ?? ''));
		$message = trim((string)($lead['message'] ?? ''));
		$product = trim((string)($lead['product'] ?? ''));
		$location = trim((string)($lead['location'] ?? ''));
		$type = trim((string)($lead['type'] ?? ''));
		$page = trim((string)($lead['page'] ?? ''));
		$utm = is_array($lead['utm'] ?? null) ? $lead['utm'] : [];

		$host = (string)($_SERVER['HTTP_HOST'] ?? '');
		$host = preg_replace('/:\d+$/', '', $host) ?: '';
		$host = (string)preg_replace('/[^A-Za-z0-9.\-]/', '', $host);
		$host = trim($host, '.');
		if ($host === '' || mb_strlen($host) > 63) {
			$host = 'фсс.рус';
		}

		// Название сайта для заголовка сделки: бренд, а не технический хост.
		$brand = function_exists('seo_config') ? (string)(seo_config('brand') ?: '') : '';
		if ($brand === '') {
			$brand = 'ФСС.РУС';
		}

		$hasPaid = ($utm['utm_source'] ?? '') !== '' || ($utm['yclid'] ?? '') !== '' || ($utm['gclid'] ?? '') !== '';

		$fields = [
			'TITLE' => 'Заявка с сайта ' . $brand . ' [' . ($hasPaid ? 'РЕКЛАМА' : 'ОРГАНИКА') . ']',
			'COMMENTS' => self::bitrixComment($lead, $host),
		];

		// Воронка/этап — идентификаторы конкретного портала, без них Bitrix
		// подставит значения по умолчанию. Разные типы форм можно развести
		// по разным воронкам (например, подписку — отдельно от заявок).
		$pipeline = self::bitrixPipeline($type);
		if ($pipeline['category'] !== '') {
			$fields['CATEGORY_ID'] = $pipeline['category'];
		}
		if ($pipeline['stage'] !== '') {
			$fields['STAGE_ID'] = $pipeline['stage'];
		}

		$source = trim(self::env('BITRIX_SOURCE'));
		if ($source !== '') {
			$fields['SOURCE_ID'] = $source;
		}

		// UTM — подставляем, только если метки реально были.
		$utmSource = (string)($utm['utm_source'] ?? '');
		$utmMedium = (string)($utm['utm_medium'] ?? '');
		$utmCampaign = (string)($utm['utm_campaign'] ?? '');
		$utmTerm = (string)($utm['utm_term'] ?? '');
		$utmContent = (string)($utm['utm_content'] ?? '');
		$yclid = (string)($utm['yclid'] ?? '');
		$gclid = (string)($utm['gclid'] ?? '');

		$fields['UTM_SOURCE'] = $utmSource !== ''
			? $utmSource
			: ($yclid !== '' ? 'yandex' : ($gclid !== '' ? 'google' : ''));
		$fields['UTM_MEDIUM'] = $utmMedium !== '' ? $utmMedium : ($hasPaid ? 'cpc' : '');
		if ($utmCampaign !== '') {
			$fields['UTM_CAMPAIGN'] = $utmCampaign;
		}
		if ($utmTerm !== '') {
			$fields['UTM_TERM'] = $utmTerm;
		}
		if ($utmContent !== '') {
			$fields['UTM_CONTENT'] = $utmContent;
		}

		// SOURCE_DESCRIPTION — сразу видно в списке сделок: реклама или органика.
		if ($hasPaid) {
			$fields['SOURCE_DESCRIPTION'] = 'Реклама: ' . $fields['UTM_SOURCE']
				. ($fields['UTM_MEDIUM'] !== '' ? ' / ' . $fields['UTM_MEDIUM'] : '')
				. ($utmCampaign !== '' ? ' / ' . $utmCampaign : '')
				. ($utmTerm !== '' ? ' | ключ: ' . $utmTerm : '');
		} else {
			$fields['SOURCE_DESCRIPTION'] = 'Прямой заход / Органика (без меток) | ' . ($page !== '' ? $page : $host);
		}

		return $fields;
	}

	/**
	 * Воронка и этап для типа заявки.
	 *
	 * Настройка в .env (JSON):
	 *   BITRIX_PIPELINE='{"default":{"category":"7","stage":"C7:NEW"},
	 *                      "subscribe":{"category":"12","stage":"C12:NEW"}}'
	 * Значения по умолчанию — BITRIX_CATEGORY_ID / BITRIX_STAGE_ID.
	 * Пусто → Bitrix подставит свои значения по умолчанию.
	 *
	 * @return array{category:string,stage:string}
	 */
	private static function bitrixPipeline(string $type): array
	{
		$default = [
			'category' => trim(self::env('BITRIX_CATEGORY_ID')),
			'stage' => trim(self::env('BITRIX_STAGE_ID')),
		];

		$raw = trim(self::env('BITRIX_PIPELINE'));
		if ($raw === '') {
			return $default;
		}
		$map = json_decode($raw, true);
		if (!is_array($map)) {
			return $default;
		}
		foreach ([$type, 'default'] as $key) {
			if ($key === '' || !isset($map[$key]) || !is_array($map[$key])) {
				continue;
			}
			return [
				'category' => trim((string)($map[$key]['category'] ?? '')),
				'stage' => trim((string)($map[$key]['stage'] ?? '')),
			];
		}
		return $default;
	}

	/**
	 * Текст заявки для поля COMMENTS — компактно, без эмодзи
	 * (Bitrix режет 4-байтный UTF-8 в некоторых полях).
	 *
	 * @param array<string,mixed> $lead
	 */
	private static function bitrixComment(array $lead, string $host = ''): string
	{
		$lines = [];
		$add = static function (string $label, string $value) use (&$lines): void {
			if (trim($value) !== '') {
				$lines[] = $label . ': ' . trim($value);
			}
		};

		$add('Телефон', (string)($lead['phone'] ?? ''));
		$add('Имя', (string)($lead['name'] ?? ''));
		$add('Email', (string)($lead['email'] ?? ''));
		$add('Компания', (string)($lead['company'] ?? ''));
		$add('Услуга', (string)($lead['product'] ?? ''));
		$add('Локация', (string)($lead['location'] ?? ''));
		$add('Форма', (string)($lead['type'] ?? ''));

		$message = trim((string)($lead['message'] ?? ''));
		if ($message !== '') {
			$lines[] = '';
			$lines[] = '--- Комментарий ---';
			$lines[] = $message;
		}

		$page = trim((string)($lead['page'] ?? ''));
		if ($page !== '') {
			$lines[] = '';
			$lines[] = '--- Страница ---';
			$lines[] = ($host !== '' ? $host : '') . $page;
		}

		$utm = is_array($lead['utm'] ?? null) ? $lead['utm'] : [];
		$utmLines = [];
		foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'yclid', 'gclid'] as $key) {
			$value = trim((string)($utm[$key] ?? ''));
			if ($value !== '') {
				$utmLines[] = $key . ': ' . $value;
			}
		}
		$lines[] = '';
		$lines[] = '--- Трафик / UTM метки ---';
		$lines[] = $utmLines !== [] ? implode("\n", $utmLines) : 'меток нет (прямой заход)';

		return implode("\n", $lines);
	}

	/**
	 * Итоговая конфигурация почты (пароль скрыт).
	 *
	 * @return array<string,string|int>
	 */
	public static function config(): array
	{
		$user = self::env('SMTP_USER', self::env('EMAIL'));
		$from = self::env('EMAIL_FROM', self::env('EMAIL'));

		return [
			'host' => self::env('SMTP_HOST', 'smtp.gmail.com'),
			'port' => (int)self::env('SMTP_PORT', self::env('EMAIL_PORT', '587')),
			'secure' => self::env('SMTP_SECURE', 'tls'),
			'user' => $user,
			'pass' => self::env('SMTP_PASS', self::env('EMAIL_PASSWORD')),
			'from' => $from,
			'name' => self::env('EMAIL_NICKNAME', 'ФСС.РУС'),
			'timeout' => (int)self::env('SMTP_TIMEOUT', (string)self::SMTP_TIMEOUT),
			'mode' => self::mode(),
			'bitrix' => self::env('BITRIX_WEBHOOK') !== '' ? 'задан' : 'не задан',
		];
	}

	/** Режим работы транспортов (как указано в .env). */
	public static function mode(): string
	{
		return strtolower(trim(self::env('MAIL_TRANSPORT', self::MODE_SMTP)));
	}

	/**
	 * Список транспортов в порядке попыток.
	 * "auto" разворачивается в "smtp,sendmail".
	 * "off" отключает почтовые транспорты (SMTP/sendmail), но не мешает
	 * остальным: "off,bitrix" — письма выключены, заявки идут в Bitrix.
	 *
	 * @return array<int,string>
	 */
	public static function chain(): array
	{
		$raw = self::mode();
		$parts = array_values(array_filter(array_map('trim', explode(',', $raw)), static fn(string $p): bool => $p !== ''));
		if ($parts === []) {
			return [self::MODE_SMTP];
		}

		$chain = [];
		foreach ($parts as $part) {
			if ($part === self::MODE_AUTO) {
				$chain[] = self::MODE_SMTP;
				$chain[] = self::MODE_SENDMAIL;
				continue;
			}
			if (!in_array($part, [self::MODE_SMTP, self::MODE_SENDMAIL, self::MODE_BITRIX, self::MODE_OFF], true)) {
				continue;
			}
			$chain[] = $part;
		}
		$chain = array_values(array_unique($chain));

		if (in_array(self::MODE_OFF, $chain, true)) {
			$chain = array_values(array_filter(
				$chain,
				static fn(string $t): bool => !in_array($t, [self::MODE_OFF, self::MODE_SMTP, self::MODE_SENDMAIL], true)
			));
		}

		return $chain === [] ? [self::MODE_OFF] : $chain;
	}

	/** Отправка полностью выключена? */
	public static function isOff(): bool
	{
		return self::chain() === [self::MODE_OFF];
	}

	/**
	 * Сетевой ли это сбой (стоит повторить) или ошибка конфигурации (не стоит).
	 *
	 * Повторяем только то, что может пройти само: таймауты, обрыв, недоступный
	 * DNS. «Connection refused» повторять бессмысленно — порт закрыт, и через
	 * две секунды он не откроется; на таком сбое сразу уходим в очередь.
	 *
	 * @param string $error Текст ошибки PHPMailer.
	 */
	private static function isTransientError(string $error): bool
	{
		$markers = [
			'Connection timed out',
			'Connection reset',
			'Broken pipe',
			'Timeout',
			'timed out',
			'getaddrinfo',
			'Network is unreachable',
			'No route to host',
		];
		foreach ($markers as $marker) {
			if (stripos($error, $marker) !== false) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Конвертирует IDN-домен в punycode — SMTP принимает только ASCII.
	 * order@фсс.рус → order@xn--q1aah.xn--p1acf
	 */
	public static function asciiEmail(string $email): string
	{
		$email = trim($email);
		$at = strrpos($email, '@');
		if ($at === false) {
			return $email;
		}
		$domain = substr($email, $at + 1);
		if (!preg_match('/[^\x00-\x7F]/', $domain)) {
			return $email; // уже ASCII
		}
		if (!function_exists('idn_to_ascii')) {
			return $email;
		}
		$ascii = idn_to_ascii($domain, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
		if ($ascii === false || $ascii === '') {
			return $email;
		}
		return substr($email, 0, $at + 1) . $ascii;
	}

	/** Адрес в виде "Имя" <mail@example.com> (без кавычек, если имя пустое). */
	private static function formatAddress(string $email, string $name): string
	{
		$name = trim(preg_replace('/[\r\n]+/', ' ', $name));
		if ($name === '') {
			return $email;
		}
		return '=?UTF-8?B?' . base64_encode($name) . '?= <' . $email . '>';
	}

	/**
	 * Значение переменной окружения с fallback.
	 * getenv() возвращает false, а не null — `??` это не лечит.
	 */
	private static function env(string $key, string $default = ''): string
	{
		$value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
		if ($value === null || $value === false || (string)$value === '') {
			$value = getenv($key);
		}
		$value = ($value === false || $value === null) ? '' : (string)$value;
		return $value !== '' ? $value : $default;
	}

	/**
	 * @return array{ok:bool,transport:string,error:string}
	 */
	private static function fail(string $transport, string $error): array
	{
		return ['ok' => false, 'transport' => $transport, 'error' => $error !== '' ? $error : 'неизвестная ошибка'];
	}
}
