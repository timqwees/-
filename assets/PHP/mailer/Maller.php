<?php
/**
	* 
	*  _____                                                                                _____ 
	* ( ___ )                                                                              ( ___ )
	*  |   |~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|   | 
	*  |   |                                                                                |   | 
	*  |   |                                                                                |   | 
	*  |   |    ________  ___       __   _______   _______   ________                       |   | 
	*  |   |   |\   __  \|\  \     |\  \|\  ___ \ |\  ___ \ |\   ____\                      |   | 
	*  |   |   \ \  \|\  \ \  \    \ \  \ \   __/|\ \   __/|\ \  \___|_                     |   | 
	*  |   |    \ \  \\\  \ \  \  __\ \  \ \  \_|/_\ \  \_|/_\ \_____  \                    |   | 
	*  |   |     \ \  \\\  \ \  \|\__\_\  \ \  \_|\ \ \  \_|\ \|____|\  \                   |   | 
	*  |   |      \ \_____  \ \____________\ \_______\ \_______\____\_\  \                  |   | 
	*  |   |       \|___| \__\|____________|\|_______|\|_______|\_________\                 |   | 
	*  |   |             \|__|                                 \|_________|                 |   | 
	*  |   |    ________  ________  ________  _______   ________  ________  ________        |   | 
	*  |   |   |\   ____\|\   __  \|\   __  \|\  ___ \ |\   __  \|\   __  \|\   __  \       |   | 
	*  |   |   \ \  \___|\ \  \|\  \ \  \|\  \ \   __/|\ \  \|\  \ \  \|\  \ \  \|\  \      |   | 
	*  |   |    \ \  \    \ \  \\\  \ \   _  _\ \  \_|/_\ \   ____\ \   _  _\ \  \\\  \     |   | 
	*  |   |     \ \  \____\ \  \\\  \ \  \\  \\ \  \_|\ \ \  \___|\ \  \\  \\ \  \\\  \    |   | 
	*  |   |      \ \_______\ \_______\ \__\\ _\\ \_______\ \__\    \ \__\\ _\\ \_______\   |   | 
	*  |   |       \|_______|\|_______|\|__|\|__|\|_______|\|__|     \|__|\|__|\|_______|   |   | 
	*  |   |                                                                                |   | 
	*  |   |                                                                                |   | 
	*  |___|~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~|___| 
	* (_____)                                                                              (_____)
	* 
	* Эта программа является свободным программным обеспечением: вы можете распространять ее и/или модифицировать
	* в соответствии с условиями GNU General Public License, опубликованными
	* Фондом свободного программного обеспечения (Free Software Foundation), либо в версии 3 Лицензии, либо (по вашему выбору) в любой более поздней версии.
	*
	* @author TimQwees
	* @link https://github.com/TimQwees/Qwees_CorePro
	* 
	*/

namespace {

	require_once dirname(__DIR__, 3) . '/vendor/autoload.php';
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	class Mailler
	{

		public function onMail(string $to_sender, string $subject, array $array)
		{

			############### STYLE DESFING ##############
			$logoUrl = 'https://www.xn--h1aafj.xn--p1ai/assets/image/logo/logo.svg'; // Можно заменить на свой логотип
			$mainColor = '#22c55e'; // Зеленый оттенок
			$bgColor = '#ffffff';
			$borderColor = '#e5e7eb';
			$fontFamily = 'Segoe UI, Arial, sans-serif';

			$header = "
					<div style='background: $bgColor; border-radius: 12px 12px 0 0; padding: 32px 32px 16px 32px; text-align: center; border-bottom: 1px solid $borderColor;'>
						<img src='$logoUrl' alt='Logo' style='width: 64px; height: 64px; margin-bottom: 12px;'>
						<h2 style='color: $mainColor; font-family: $fontFamily; margin: 0; font-size: 1.6rem;'>Новая заявка с сайта ФСС.РУС</h2>
					</div>
				";

			$footer = "
					<div style='background: $bgColor; border-radius: 0 0 12px 12px; padding: 18px 32px 24px 32px; text-align: center; border-top: 1px solid $borderColor; color: #6b7280; font-size: 0.95rem; font-family: $fontFamily;'>
						Спасибо за обращение!<br>
						<small>Это автоматическое письмо, не отвечайте на него.</small>
					</div>
				";
			############### STYLE DESFING ##############


			if ($array['type'] == 'modal') {

				$body = "
						<div style='max-width: 480px; margin: 0 auto; background: $bgColor; border-radius: 12px; box-shadow: 0 4px 24px rgba(34,197,94,0.08); border: 1px solid $borderColor; font-family: $fontFamily;'>
							$header
							<div style='padding: 28px 32px 18px 32px;'>
								<table style='width: 100%; border-collapse: collapse; font-size: 1.08rem;'>
									<tr>
										<td style='padding: 8px 0; color: #374151; font-weight: 500;'>Каталог:</td>
										<td style='padding: 8px 0; color: $mainColor; font-weight: 600;'>" . htmlspecialchars($array['type']) . "</td>
									</tr>
									<tr>
										<td style='padding: 8px 0; color: #374151; font-weight: 500;'>Имя:</td>
										<td style='padding: 8px 0; color: $mainColor; font-weight: 600;'>" . htmlspecialchars($array['name']) . "</td>
									</tr>
									<tr>
										<td style='padding: 8px 0; color: #374151; font-weight: 500;'>Телефон:</td>
										<td style='padding: 8px 0; color: $mainColor; font-weight: 600;'> +" . htmlspecialchars($array['phone']) . "</td>
									</tr>
									<tr>
										<td style='padding: 8px 0; color: #374151; font-weight: 500;'>Почта:</td>
										<td style='padding: 8px 0; color: $mainColor; font-weight: 600;'>" . htmlspecialchars($array['email']) . "</td>
									</tr>
									<tr>
										<td style='padding: 8px 0; color: #374151; font-weight: 500; vertical-align: top;'>Сообщение:</td>
										<td style='padding: 8px 0; color: #374151; background: #f0fdf4; border-radius: 6px;'>" . nl2br(htmlspecialchars($array['message'])) ?? '' . "</td>
									</tr>
								</table>
							</div>
							$footer
						</div>
					";
			} elseif ($array['type'] == 'mini-forma') {
				$body = "
						<div style='max-width: 420px; margin: 0 auto; background: $bgColor; border-radius: 12px; box-shadow: 0 4px 24px rgba(34,197,94,0.08); border: 1px solid $borderColor; font-family: $fontFamily;'>
							$header
							<div style='padding: 28px 32px 18px 32px;'>
								<table style='width: 100%; border-collapse: collapse; font-size: 1.08rem;'>
									<tr>
										<td style='padding: 8px 0; color: #374151; font-weight: 500;'>Дата:</td>
										<td style='padding: 8px 0; color: $mainColor; font-weight: 600;'>" . date('d.m.Y') . "</td>
									</tr>
									<tr>
										<td style='padding: 8px 0; color: #374151; font-weight: 500;'>Имя:</td>
										<td style='padding: 8px 0; color: $mainColor; font-weight: 600;'>" . htmlspecialchars($array['name']) . "</td>
									</tr>
									<tr>
										<td style='padding: 8px 0; color: #374151; font-weight: 500;'>Телефон:</td>
										<td style='padding: 8px 0; color: $mainColor; font-weight: 600;'> +" . htmlspecialchars($array['phone']) . "</td>
									</tr>
								</table>
							</div>
							$footer
						</div>
					";
			}

			$data = [
				"email" => "bingiabonbasv@gmail.com", // Отправитель
				"pass" => "admu ohaa oeoz lvtd", // Пароль для внешних приложений
				"name" => "Новое обращение ФСС.РУС", // Имя отправителя
				"subject" => $subject,
				"body" => $body,
				"to_email" => $to_sender,
				"port" => 587,
			];

			$mail = new PHPMailer(true);

			try {
				$mail->CharSet = 'UTF-8';
				$mail->isSMTP();
				$mail->SMTPAuth = true;
				$mail->SMTPDebug = 0;
				$mail->Host = 'smtp.gmail.com';
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port = $data['port'];
				$mail->Username = $data['email'];
				$mail->Password = $data['pass'];
				$mail->setFrom($data['email'], $data['name']);
				$mail->addAddress($data['to_email']);
				$mail->Subject = $data['subject'];
				$mail->msgHTML($data['body']);

				if ($mail->send()) {
					if ($array['type'] == 'modal') {
						echo '<div id="success-alert" class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg flex items-center shadow-lg"
                        style="position:fixed; top:20px; right:20px; z-index:9999; min-width:320px; max-width:90vw; box-shadow:0 4px 16px rgba(0,0,0,0.10);" role="alert">
                        <span class="font-semibold mr-4">Ваша заявление успешно отправлена!</span>
                        <button type="button" onclick="document.getElementById(\'success-alert\').style.display=\'none\'"
                            style="position:absolute; top:8px; right:12px; background:none; border:none; font-size:1.5rem; color:#166534; cursor:pointer; line-height:1;">&times;</button>
                    </div>';
					} elseif ($array['type'] == 'mini-forma') {
						echo '<div id="success-alert" class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg flex items-center shadow-lg"
                        style="position:fixed; top:20px; right:20px; z-index:9999; min-width:320px; max-width:100vw; box-shadow:0 4px 16px rgba(0,0,0,0.10);padding-right: 2rem" role="alert">
                        <span class="font-semibold mr-4">✅ Вы успешно оставили заявление, ожидайте ответа!</span>
                        <button type="button" onclick="document.getElementById(\'success-alert\').style.display=\'none\'"
                            style="position:absolute; top:13px; right:12px; background:none; border:none; font-size:1.5rem; color:#166534; cursor:pointer; line-height:1;">&times;</button>
                    </div>';
					} else {
						echo '<div id="error-alert" class="z-20 p-4 mb-4 text-red-700 bg-red-100 rounded-lg flex items-center shadow-lg"
                        style="position:fixed; top:20px; right:20px; z-index:9999; min-width:320px; max-width:90vw; box-shadow:0 4px 16px rgba(0,0,0,0.10);" role="alert">
                        <span class="font-semibold mr-4">Ошибка при отправке заявки. Пожалуйста, попробуйте позже.' . htmlspecialchars($mail->ErrorInfo) . '</span>
                        <button type="button" onclick="document.getElementById(\'error-alert\').style.display=\'none\'"
                            style="position:absolute; top:8px; right:12px; background:none; border:none; font-size:1.5rem; color:#b91c1c; cursor:pointer; line-height:1;">&times;</button>
                    </div>';
						$mail->clearAddresses();
					}
				}
			} catch (Exception $e) {
				echo '<div id="error-alert" class="z-20 p-4 mb-4 text-red-700 bg-red-100 rounded-lg flex items-center shadow-lg"
                    style="position:fixed; top:20px; right:20px; z-index:9999; min-width:320px; max-width:90vw; box-shadow:0 4px 16px rgba(0,0,0,0.10);" role="alert">
                    <span class="font-semibold mr-4">Ошибка отправки: ' . htmlspecialchars($mail->ErrorInfo) . '</span>
                    <button type="button" onclick="document.getElementById(\'error-alert\').style.display=\'none\'"
                        style="position:absolute; top:8px; right:12px; background:none; border:none; font-size:1.5rem; color:#b91c1c; cursor:pointer; line-height:1;">&times;</button>
                </div>';
			}
		}
	}
}