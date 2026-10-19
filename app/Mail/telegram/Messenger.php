<?php
namespace App\Mail\Telegram;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Security.php';

use App\Mail\Security;

class Messenger
{
	/**
	 * Форматируем и отправляем сообщение в Telegram
	 */
	public function sendToTelegram(array $arr): bool
	{
		$token = TELEGRAM_BOT_TOKEN;
		$chat_ids = unserialize(TELEGRAM_CHAT_IDS);

		$message = "📝 <b>Новая заявка с сайта</b>\n";

		if (!empty($arr['name'])) {
			$message .= "👤 Имя: " . htmlspecialchars($arr['name']) . "\n";
		}
		if (!empty($arr['phone'])) {
			$message .= "📞 Телефон: " . htmlspecialchars($arr['phone']) . "\n";
		}
		if (!empty($arr['email'])) {
			$message .= "✉️ Почта: " . htmlspecialchars($arr['email']) . "\n";
		}
		if (!empty($arr['message'])) {
			$message .= "💬 Сообщение: " . htmlspecialchars($arr['message']) . "\n";
		}
		$message .= "⏰ Время: " . date('d.m.Y H:i:s');

		$successCount = 0;
		$failCount = 0;
		$errors = [];

		foreach ($chat_ids as $chat_id) {
			$ch = curl_init("https://api.telegram.org/bot{$token}/sendMessage");

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($ch, CURLOPT_POSTFIELDS, [
				'chat_id' => $chat_id,
				'parse_mode' => 'html',
				'text' => $message
			]);

			$response = curl_exec($ch);
			$ok = false;
			$err = null;

			if (curl_errno($ch)) {
				$err = 'cURL: ' . curl_error($ch);
			} else {
				$response_data = json_decode($response, true);
				if (isset($response_data['ok']) && $response_data['ok'] === true) {
					$ok = true;
				} else {
					$err = $response;
					if (strpos($response, 'blocked') !== false || strpos($response, 'Forbidden') !== false) {
						$err = "chat $chat_id blocked bot";
					}
				}
			}

			if ($ok) {
				$successCount++;
			} else {
				$failCount++;
				$errors[] = "chat $chat_id: $err";
				@file_put_contents(ANTIBOT_STORAGE_DIR . '/telegram_errors.log', date('Y-m-d H:i:s') . " | chat $chat_id | $err | " . json_encode($arr, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
			}
		}

		if ($failCount > 0) {
			error_log("Telegram: success $successCount fail $failCount " . implode('; ', $errors));
		}
		return $successCount > 0;
	}
}
