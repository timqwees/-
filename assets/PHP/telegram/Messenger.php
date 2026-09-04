<?php

class Messenger
{
	// Форматируем и отправляем сообщение в Telegram
	public function sendToTelegram(array $arr)
	{
		$token = "8480933826:AAHloEdO5bPRHxMbaXsHyiTvOAEviT3GjOg"; // ❗ Замените токен при первой возможности
		$chat_ids = [
			"1999094746", // timqwees
			"1901625695", // karim
			'1336615751',// Ген дир
		];

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

		// Показываем уведомление в интерфейсе
		if ($hasError) {
			echo '<div id="error-alert" class="p-4 mb-4 text-red-700 bg-red-100 rounded-lg flex items-center shadow-lg"
				style="position:fixed; top:20px; right:20px; z-index:9999; min-width:320px; max-width:100vw; box-shadow:0 4px 16px rgba(138, 138, 138, 0.53);" role="alert">
				<span class="font-semibold mr-4">❌ Ошибка при отправке сообщения в Telegram. ' . htmlspecialchars($errorText) . '</span>
				<button type="button" onclick="document.getElementById(\'error-alert\').style.display=\'none\'"
					style="position:absolute; top:13px; right:12px; background:none; border:none; font-size:1.5rem; color:#b91c1c; cursor:pointer; line-height:1;">&times;</button>
			</div>';
		} else {
			echo '<div id="success-alert" class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg flex items-center shadow-lg"
				style="position:fixed; top:20px; right:20px; z-index:9999; min-width:320px; max-width:100vw; box-shadow:0 4px 16px rgba(0,0,0,0.10);padding-right: 2rem" role="alert">
				<span class="font-semibold mr-4">✅ Вы успешно оставили заявление, ожидайте ответа!</span>
				<button type="button" onclick="document.getElementById(\'success-alert\').style.display=\'none\'"
					style="position:absolute; top:13px; right:12px; background:none; border:none; font-size:1.5rem; color:#166534; cursor:pointer; line-height:1;">&times;</button>
			</div>';
		}
	}
}
