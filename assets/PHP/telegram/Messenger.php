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

		$hasError = false; // Флаг наличия ошибки

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

			if (curl_errno($ch)) {
				$hasError = true;
				$errorText = 'Ошибка cURL: ' . curl_error($ch);
			} else {
				$response_data = json_decode($response, true);
				if (!isset($response_data['ok']) || $response_data['ok'] !== true) {
					$hasError = true;
					$errorText = 'Ошибка Telegram API: ' . $response;
				}
			}

			curl_close($ch);
		}

		// Показываем уведомление в интерфейсе - используем Security helper
		if ($hasError) {
			Security::showAlert('error', 'Ошибка при отправке сообщения в Telegram. ' . ($errorText ?? ''));
		} else {
			Security::showAlert('success', 'Вы успешно оставили заявление, ожидайте ответа!');
			// Для SEO: пушим событие в dataLayer если есть
			echo '<script>if(window.dataLayer) dataLayer.push({event:"form_success", form_type:"'.htmlspecialchars($arr['type'] ?? 'unknown').'"});</script>';
		}
	}
}
