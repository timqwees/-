<?php
namespace App\Mail\Telegram;

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Security.php';

use App\Mail\Security;

class Messenger
{
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

		// Parallel requests via curl_multi
		$mh = curl_multi_init();
		$handles = [];

		foreach ($chat_ids as $chat_id) {
			$ch = curl_init("https://api.telegram.org/bot{$token}/sendMessage");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);        // reduced from 10
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // reduced from 5
			curl_setopt($ch, CURLOPT_POSTFIELDS, [
				'chat_id' => $chat_id,
				'parse_mode' => 'html',
				'text' => $message
			]);
			curl_multi_add_handle($mh, $ch);
			$handles[$chat_id] = $ch;
		}

		// Execute all in parallel
		$running = null;
		do {
			curl_multi_exec($mh, $running);
			if ($running) {
				curl_multi_select($mh, 0.1); // wait for activity
			}
		} while ($running > 0);

		$successCount = 0;
		foreach ($handles as $chat_id => $ch) {
			$response = curl_multi_getcontent($ch);
			$ok = false;
			if (!curl_errno($ch)) {
				$response_data = json_decode($response, true);
				if (isset($response_data['ok']) && $response_data['ok'] === true) {
					$ok = true;
				}
			}
			if ($ok) {
				$successCount++;
			} else {
				$err = curl_errno($ch) ? 'cURL: ' . curl_error($ch) : $response;
				if (strpos($response, 'blocked') !== false || strpos($response, 'Forbidden') !== false) {
					$err = "chat $chat_id blocked bot";
				}
				@file_put_contents(ANTIBOT_STORAGE_DIR . '/telegram_errors.log', date('Y-m-d H:i:s') . " | chat $chat_id | $err | " . json_encode($arr, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
			}
			curl_multi_remove_handle($mh, $ch);
		}
		curl_multi_close($mh);

		return $successCount > 0;
	}
}
