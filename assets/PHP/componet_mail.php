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
require_once __DIR__ . '/mailer/Maller.php';
require_once __DIR__ . '/telegram/Messenger.php';

$to_mail = 'artemnersisyan777@gmail.com';
$subject = "Заказ с сайта ФСС.РУС © " . date('Y');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mailer'])) {

	$name = isset($_POST['name']) ? trim($_POST['name']) : '';
	$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
	$message = isset($_POST['message']) ? trim($_POST['message']) : '';
	$email = isset($_POST['email']) ? trim($_POST['email']) : '';
	$checkbox = isset($_POST['checkbox']) && $_POST['checkbox'] === 'on';

	$array = [
		'name' => $name,
		'phone' => $phone,
		'message' => $message,
		'email' => $email,
		'type' => 'modal',
	];

	// Проверка обязательных полей
	if (empty($name) || empty($phone) || empty($message) || empty($email) || !$checkbox) {
		echo '<div id="error-alert" class="z-20 p-4 mb-4 text-red-700 bg-red-100 rounded-lg flex items-center shadow-lg bg-white"
                style="position:fixed; top:20px; right:20px; z-index:9999; max-width:100vw; width: auto; box-shadow:0 4px 16px rgba(138, 138, 138, 0.53);" role="alert">
                <span class="font-semibold mr-4" style="padding-right: 1rem">Пожалуйста, заполните все обязательные поля корректно!</span>
                <button type="button" onclick="document.getElementById(\'error-alert\').style.display=\'none\'"
                    style="position:absolute; top:13.5px; right:12px; background:none; border:none; font-size:1.5rem; color:#b91c1c; cursor:pointer; line-height:1;">&times;</button>
            </div>';
	} else {
		// $mailer = new Mailler();
		// $mailer->onMail($to_mail, $subject, $array);
		$messenger = new Messenger();
		$messenger->sendToTelegram($array);
	}
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mini-forma'])) {

	$name = isset($_POST['name']) ? trim($_POST['name']) : '';
	$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

	$array = [
		'name' => $name,
		'phone' => $phone,
		'type' => 'mini-forma',
	];

	// Проверка обязательных полей
	if (empty($name) || empty($phone)) {
		echo '<div id="error-alert" class="z-20 p-4 mb-4 text-red-700 bg-red-100 rounded-lg flex items-center shadow-lg bg-white"
                style="position:fixed; top:20px; right:20px; z-index:9999; max-width:100vw; width: auto; box-shadow:0 4px 16px rgba(138, 138, 138, 0.53);" role="alert">
                <span class="font-semibold mr-4" style="padding-right: 1rem">Пожалуйста, заполните все обязательные поля корректно!</span>
                <button type="button" onclick="document.getElementById(\'error-alert\').style.display=\'none\'"
                    style="position:absolute; top:13.5px; right:12px; background:none; border:none; font-size:1.5rem; color:#b91c1c; cursor:pointer; line-height:1;">&times;</button>
            </div>';
	} else {
		// $mailer = new Mailler();
		// $mailer->onMail($to_mail, $subject, $array);
		$messenger = new Messenger();
		$messenger->sendToTelegram($array);

	}
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content_form'])) {

	$name = isset($_POST['name']) ? trim($_POST['name']) : '';
	$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
	$message = isset($_POST['message']) ? trim($_POST['message']) : '';
	$checkbox = isset($_POST['checkbox']) && $_POST['checkbox'] === 'on';

	$array = [
		'name' => $name,
		'phone' => $phone,
		'message' => $message,
		'type' => 'content_form',
	];

	// Проверка обязательных полей
	if (empty($name) || empty($phone) || empty($message) || !$checkbox) {
		echo '<div id="error-alert" class="z-20 p-4 mb-4 text-red-700 bg-red-100 rounded-lg flex items-center shadow-lg bg-white"
                style="position:fixed; top:20px; right:20px; z-index:9999; max-width:100vw; width: auto; box-shadow:0 4px 16px rgba(138, 138, 138, 0.53);" role="alert">
                <span class="font-semibold mr-4" style="padding-right: 1rem">Пожалуйста, заполните все обязательные поля корректно!</span>
                <button type="button" onclick="document.getElementById(\'error-alert\').style.display=\'none\'"
                    style="position:absolute; top:13.5px; right:12px; background:none; border:none; font-size:1.5rem; color:#b91c1c; cursor:pointer; line-height:1;">&times;</button>
            </div>';
	} else {
		// $mailer = new Mailler();
		// $mailer->onMail($to_mail, $subject, $array);
		$messenger = new Messenger();
		$messenger->sendToTelegram($array);

	}
}
?>