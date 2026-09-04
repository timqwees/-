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

require_once dirname(__DIR__, 1) . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/Antibot.php';
require_once __DIR__ . '/mailer/Maller.php';
require_once __DIR__ . '/telegram/Messenger.php';

$to_mail = MAIL_TO;
$subject = "Заказ с сайта ФСС.РУС © " . date('Y');

// Общий обработчик POST с антиботом
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['mailer']) || isset($_POST['mini-forma']) || isset($_POST['content_form']))) {

	// 1. Антибот проверка - первая линия защиты
	$antibotError = null;
	if (!Antibot::validate($_POST, $antibotError)) {
		Security::showAlert('error', $antibotError);
		// Логируем попытку, но не отправляем дальше
		return;
	}

	// Определяем тип формы
	$isMailer = isset($_POST['mailer']);
	$isMini = isset($_POST['mini-forma']);
	$isContent = isset($_POST['content_form']);

	if ($isMailer) {
		// Валидация
		$err = '';
		$nameV = Security::validateName($_POST['name'] ?? '', $err);
		if ($nameV === false) { Security::showAlert('error', $err); return; }
		$phoneV = Security::validatePhone($_POST['phone'] ?? '', $err);
		if ($phoneV === false) { Security::showAlert('error', $err); return; }
		$emailV = Security::validateEmail($_POST['email'] ?? '', $err, true);
		if ($emailV === false) { Security::showAlert('error', $err); return; }
		$msgV = Security::validateMessage($_POST['message'] ?? '', $err, true, 10);
		if ($msgV === false) { Security::showAlert('error', $err); return; }
		$checkbox = isset($_POST['checkbox']) && $_POST['checkbox'] === 'on';
		if (!$checkbox) { Security::showAlert('error', 'Необходимо согласие на обработку персональных данных.'); return; }

		$array = [
			'name' => $nameV,
			'phone' => $phoneV,
			'message' => $msgV,
			'email' => $emailV,
			'type' => 'modal',
		];
		$messenger = new Messenger();
		$messenger->sendToTelegram($array);

	} elseif ($isMini) {
		$err = '';
		$nameV = Security::validateName($_POST['name'] ?? '', $err);
		if ($nameV === false) { Security::showAlert('error', $err); return; }
		$phoneV = Security::validatePhone($_POST['phone'] ?? '', $err);
		if ($phoneV === false) { Security::showAlert('error', $err); return; }
		$array = [
			'name' => $nameV,
			'phone' => $phoneV,
			'type' => 'mini-forma',
		];
		$messenger = new Messenger();
		$messenger->sendToTelegram($array);

	} elseif ($isContent) {
		$err = '';
		$nameV = Security::validateName($_POST['name'] ?? '', $err);
		if ($nameV === false) { Security::showAlert('error', $err); return; }
		$phoneV = Security::validatePhone($_POST['phone'] ?? '', $err);
		if ($phoneV === false) { Security::showAlert('error', $err); return; }
		$msgV = Security::validateMessage($_POST['message'] ?? '', $err, true, 10);
		if ($msgV === false) { Security::showAlert('error', $err); return; }
		$checkbox = isset($_POST['checkbox']) && $_POST['checkbox'] === 'on';
		if (!$checkbox) { Security::showAlert('error', 'Необходимо согласие на обработку персональных данных.'); return; }
		$array = [
			'name' => $nameV,
			'phone' => $phoneV,
			'message' => $msgV,
			'type' => 'content_form',
		];
		$messenger = new Messenger();
		$messenger->sendToTelegram($array);
	}
}
?>