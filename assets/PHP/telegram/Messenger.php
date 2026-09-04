<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Security.php';

class Messenger
{
	// Форматируем и отправляем сообщение в Telegram
	public function sendToTelegram(array $arr)
	{
		$token = TELEGRAM_BOT_TOKEN;
		$chat_ids = unserialize(TELEGRAM_CHAT_IDS);

		$message = "📝 <b>Новая заявка с сайта</b>\n";

		// Формируем сообщение
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

		// Отправляем сообщение каждому chat_id
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
					// Частая причина — пользователь заблокировал бота, не показываем это клиенту
					if (strpos($response, 'blocked') !== false || strpos($response, 'Forbidden') !== false) {
						$err = "chat $chat_id blocked bot";
					}
				}
			}
			curl_close($ch);

			if ($ok) {
				$successCount++;
			} else {
				$failCount++;
				$errors[] = "chat $chat_id: $err";
				// Логируем, но не показываем клиенту детали
				@file_put_contents(ANTIBOT_STORAGE_DIR . '/telegram_errors.log', date('Y-m-d H:i:s') . " | chat $chat_id | $err | " . json_encode($arr, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
			}
		}

		// Показываем уведомление — успех если хотя бы одному доставлено
		if ($successCount > 0) {
			Security::showAlert('success', 'Вы успешно оставили заявление, ожидайте ответа!');
			echo '<script>if(window.dataLayer) dataLayer.push({event:"form_success", form_type:"'.htmlspecialchars($arr['type'] ?? 'unknown').'"});</script>';
			// Если были частичные ошибки (например, 1 из 3 заблокировал), логируем тихо
			if ($failCount > 0) {
				error_log("Telegram partial fail: success $successCount fail $failCount " . implode('; ', $errors));
			}
		} else {
			// Все доставки провалились — показываем общую ошибку без деталей API
			Security::showAlert('error', 'Не удалось отправить заявку. Пожалуйста, позвоните нам +7 938 018-00-06 или попробуйте позже.');
			error_log("Telegram all fail: " . implode('; ', $errors));
		}
	}
}
