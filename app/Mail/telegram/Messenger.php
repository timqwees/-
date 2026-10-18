<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Security.php';

class Messenger
{
	/**
	 * Форматируем и отправляем сообщение в Telegram
	 */
	public function sendToTelegram(array $arr)
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
			curl_close($ch);

			if ($ok) {
				$successCount++;
			} else {
				$failCount++;
				$errors[] = "chat $chat_id: $err";
				@file_put_contents(ANTIBOT_STORAGE_DIR . '/telegram_errors.log', date('Y-m-d H:i:s') . " | chat $chat_id | $err | " . json_encode($arr, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
			}
		}

		if ($successCount > 0) {
			Security::showAlert('success', 'Вы успешно оставили заявление, ожидайте ответа!');
			echo '<script>if(window.dataLayer) dataLayer.push({event:"form_success", form_type:"'.htmlspecialchars($arr['type'] ?? 'unknown').'"});</script>';
			if ($failCount > 0) {
				error_log("Telegram partial fail: success $successCount fail $failCount " . implode('; ', $errors));
			}
		} else {
			Security::showAlert('error', 'Не удалось отправить заявку. Пожалуйста, позвоните нам +7 938 018-00-06 или попробуйте позже.');
			error_log("Telegram all fail: " . implode('; ', $errors));
		}
	}

	/**
	 * Отправка заявки в Битрикс24 через вебхук CRM
	 * @link https://b24-7ho6oc.bitrix24.ru/rest/1/nrb1ccmfgjaofeb5/
	 */
	public function sendToBitrix24(array $arr): void
	{
		$name = $arr['name'] ?? $arr['имя'] ?? '';
		$phone = $arr['phone'] ?? $arr['телефн'] ?? $arr['телефон'] ?? $arr['теефон'] ?? '';
		$email = $arr['email'] ?? $arr['почта'] ?? '';
		[$CATEGORY_ID, $STAGE_ID] = $this->resolveFunnel($arr);

		$comment = trim((string)($arr['сообщение'] ?? $arr['message'] ?? $arr['комментарий'] ?? $arr['comment'] ?? $arr['comment_'] ?? ''));
		$source = $arr['источник_заявки'] ?? '';
		$formId = $arr['форма'] ?? $arr['type'] ?? '';

		$utmSource   = trim((string)($arr['utm_source'] ?? ''));
		$utmMedium   = trim((string)($arr['utm_medium'] ?? ''));
		$utmCampaign = trim((string)($arr['utm_campaign'] ?? ''));
		$utmTerm     = trim((string)($arr['utm_term'] ?? ''));
		$utmContent  = trim((string)($arr['utm_content'] ?? ''));
		$yclid       = trim((string)($arr['yclid'] ?? ''));
		$gclid       = trim((string)($arr['gclid'] ?? ''));
		$landing     = trim((string)($arr['landing_page'] ?? ''));
		$referrer    = trim((string)($arr['referrer'] ?? ''));

		$landingDisplay = $landing;
		if ($landing !== '') {
			$lp = parse_url($landing);
			if (!empty($lp['query'])) {
				parse_str($lp['query'], $lq);
				unset($lq['message_status'], $lq['message_msg']);
				$lp['query'] = http_build_query($lq);
				if ($lp['query'] === '') unset($lp['query']);
				$landingDisplay = (isset($lp['scheme']) ? $lp['scheme'] . '://' : '') . ($lp['host'] ?? '') . ($lp['path'] ?? '/') . (isset($lp['query']) ? '?' . $lp['query'] : '');
			}
			if (mb_strlen($landingDisplay) > 120) $landingDisplay = mb_substr($landingDisplay, 0, 117) . '...';
		}
		$referrerHost = $referrer !== '' ? ((string)(parse_url($referrer, PHP_URL_HOST) ?: $referrer)) : '';
		if ($referrerHost !== '' && mb_strlen($referrerHost) > 40) $referrerHost = mb_substr($referrerHost, 0, 37) . '...';

		$hasPaid = $utmSource !== '' || $yclid !== '' || $gclid !== '';
		$landingHostForBadge = $landingDisplay !== '' ? (parse_url($landingDisplay, PHP_URL_HOST) ?: '') : '';
		$isInternalRef = $referrerHost !== '' && $landingHostForBadge !== '' && $referrerHost === $landingHostForBadge;

		if ($hasPaid) {
			$trafficBadge = 'РЕКЛАМА';
		} elseif ($referrer !== '' && !$isInternalRef) {
			$trafficBadge = 'ОРГАНИКА / РЕФЕРАЛ';
		} else {
			$trafficBadge = 'ПРЯМОЙ / ОРГАНИКА';
		}

		$formLabels = [
			'hero_glavnaya'    => 'Главная, hero-блок',
			'hero'             => 'Главная, hero-блок',
			'quiz_glavnaya'    => 'Квиз на главной',
			'quiz'             => 'Квиз',
			'header_dialog'    => 'Шапка, модалка «Получить расчет»',
			'header'           => 'Шапка',
			'modal_cta'        => 'Модальное окно',
			'cta'              => 'CTA-блок',
			'cta_form'         => 'CTA-форма',
			'pod_klyuch_hero'  => 'Услуга Под ключ, hero',
			'pod_klyuch_cta'   => 'Услуга Под ключ, CTA',
			'contact'          => 'Контакты',
			'calculator'       => 'Калькулятор',
			'price_table'      => 'Таблица цен',
			'portfolio'        => 'Портфолио',
			'reviews'          => 'Отзывы',
		];
		$formDesc = '';
		if ($formId !== '' && $formId !== '-') {
			$fl = mb_strtolower($formId, 'UTF-8');
			$formDesc = $formLabels[$fl] ?? $formLabels[$formId] ?? '';
		}

		$info = "Телефон: " . $phone;
		if ($name !== '') $info .= "\nИмя: " . $name;
		if ($email !== '') $info .= "\nEmail: " . $email;
		$info .= "\nИсточник: " . ($source ?: "-");
		$info .= "\nФорма: " . ($formId ?: "-") . ($formDesc !== '' ? " (" . $formDesc . ")" : "");

		$excludeExtra = ['имя', 'name', 'телефн', 'телефон', 'phone', 'почта', 'email', 'сообщение', 'message', 'комментарий', 'comment', 'comment_', 'both', 'источник_заявки', 'форма', 'type', 'website', '_ts', '_js_token', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'yclid', 'gclid', 'landing_page', 'referrer'];
		$extra = [];
		foreach ($arr as $key => $value) {
			$val = trim((string)$value);
			if ($val === '' || in_array(mb_strtolower((string)$key, 'UTF-8'), $excludeExtra, true)) continue;
			$extra[] = (string)$key . ": " . $val;
		}
		if ($extra) {
			$info .= "\n\n--- Данные формы ---";
			$info .= "\n" . implode("\n", $extra);
		}
		if ($comment) $info .= "\n\n--- Комментарий ---\n" . $comment;
		$info .= "\n\n--- Трафик / UTM метки ---";
		$trafficExample = $hasPaid ? 'РЕКЛАМА' : 'ПРЯМОЙ ЗАХОД / ОРГАНИКА (без меток)';
		if (!$hasPaid && $referrer !== '' && !$isInternalRef) $trafficExample = 'ОРГАНИКА / РЕФЕРАЛ (без utm)';
		$info .= "\nТип трафика: " . $trafficExample;
		$info .= "\nutm_source: " . ($utmSource !== '' ? $utmSource : '— нет');
		$info .= "\nutm_medium: " . ($utmMedium !== '' ? $utmMedium : '— нет');
		$info .= "\nutm_campaign: " . ($utmCampaign !== '' ? $utmCampaign : '— нет');
		$info .= "\nutm_term: " . ($utmTerm !== '' ? $utmTerm : '— нет');
		$info .= "\nutm_content: " . ($utmContent !== '' ? $utmContent : '— нет');
		$info .= "\nyclid: " . ($yclid !== '' ? $yclid : '— нет');
		$info .= "\ngclid: " . ($gclid !== '' ? $gclid : '— нет');
		$info .= "\nlanding_page: " . ($landingDisplay !== '' ? $landingDisplay : '— нет');
		$info .= "\nreferrer: " . ($referrer !== '' && !$isInternalRef ? $referrer : '— нет (прямой заход)' . ($isInternalRef ? ' (внутренний переход)' : ''));

		$utmSourceForApi  = $utmSource !== '' ? $utmSource : ($yclid !== '' ? 'yandex' : ($gclid !== '' ? 'google' : ''));
		$utmMediumForApi  = $utmMedium !== '' ? $utmMedium : ($hasPaid ? 'cpc' : '');

		if ($hasPaid) {
			$sourceDesc = 'Реклама: ' . $utmSourceForApi . ($utmMediumForApi ? " / {$utmMediumForApi}" : '') . ($utmCampaign ? " / {$utmCampaign}" : '') . ($utmTerm ? " | ключ: {$utmTerm}" : '');
		} elseif ($referrer !== '') {
			$refHost = parse_url($referrer, PHP_URL_HOST) ?: $referrer;
			$landingHost = $landingDisplay !== '' ? (parse_url($landingDisplay, PHP_URL_HOST) ?: '') : '';
			if ($landingHost !== '' && $refHost === $landingHost) {
				$sourceDesc = 'Прямой заход / Органика (без меток) | ' . ($landingDisplay ?: $source ?: 'pkvartira.ru');
			} else {
				$sourceDesc = 'Органика/Реферал: ' . $refHost . ' | ' . ($landingDisplay ?: $source);
			}
		} else {
			$sourceDesc = 'Прямой заход / Органика (без меток) | ' . ($landingDisplay ?: $source ?: 'pkvartira.ru');
		}

		$webhookUrl = 'https://b24-7ho6oc.bitrix24.ru/rest/1/nrb1ccmfgjaofeb5/crm.deal.add.json';
		$postData = http_build_query(['fields' => [
			'TITLE' => 'Заявка с сайта ' . ($_SERVER['SERVER_NAME'] ?? '') . ($hasPaid ? ' [РЕКЛАМА]' : ' [ОРГАНИКА]'),
			'CATEGORY_ID' => $CATEGORY_ID,
			'STAGE_ID' => $STAGE_ID,
			'COMMENTS' => $info,
			'SOURCE_DESCRIPTION' => $sourceDesc,
			'UTM_SOURCE' => $utmSourceForApi,
			'UTM_MEDIUM' => $utmMediumForApi,
			'UTM_CAMPAIGN' => $utmCampaign,
			'UTM_TERM' => $utmTerm,
			'UTM_CONTENT' => $utmContent,
		]]);

		$ch = curl_init($webhookUrl);
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $postData,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 15,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
		]);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		curl_close($ch);

		if (!is_string($response)) {
			error_log("Bitrix24: пустой ответ (curl_exec вернул false)");
			return;
		}
		if ($curlError) {
			error_log("Bitrix24 curl error: {$curlError}");
		} elseif ($httpCode !== 200) {
			error_log("Bitrix24 HTTP {$httpCode}: " . mb_substr($response, 0, 500));
		} else {
			$result = json_decode($response, true);
			if (!empty($result['error'])) {
				$errDesc = $result['error_description'] ?? '';
				error_log("Bitrix24 API error: {$result['error']} — {$errDesc}");
			}
		}
	}

	/**
	 * Определяем воронку и стадию в Битрикс24 на основе типа формы и источника
	 */
	private function resolveFunnel(array $arr)
	{
		$formId = mb_strtolower((string)($arr['форма'] ?? $arr['type'] ?? ''), 'UTF-8');
		$hasPaid = !empty($arr['utm_source']) || !empty($arr['yclid']) || !empty($arr['gclid']);

		$CATEGORY_ID = 0;
		$STAGE_ID = 'NEW';

		if ($hasPaid) {
			$CATEGORY_ID = 2;
		} elseif ($formId === 'hero' || $formId === 'hero_glavnaya' || $formId === 'header_dialog' || $formId === 'cta' || $formId === 'cta_form' || $formId === 'modal_cta') {
			$CATEGORY_ID = 1;
		} elseif ($formId === 'quiz' || $formId === 'quiz_glavnaya') {
			$CATEGORY_ID = 3;
		} elseif ($formId === 'contact') {
			$CATEGORY_ID = 4;
		} elseif ($formId === 'calculator') {
			$CATEGORY_ID = 5;
		} elseif ($formId === 'content_form') {
			$CATEGORY_ID = 6;
		}

		return [$CATEGORY_ID, $STAGE_ID];
	}
}
