<?php
/**
 * Обработчик POST-форм сайта ФАВОРИТ-СТРОЙ-СНАБ.
 * Единая точка входа для всех форм (как в pkvartira).
 * Валидация, антибот, отправка в Telegram + почта.
 */

require_once dirname(__DIR__, 1) . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/Antibot.php';
require_once __DIR__ . '/telegram/Messenger.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Mail\Security;
use App\Mail\Antibot;
use App\Mail\Telegram\Messenger;

function setFormSuccessCookie(string $formType): void
{
    if (headers_sent()) {
        return;
    }
    $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $cookieName = 'fss_form_' . $formType . '_success';
    $cookieValue = '1';
    $expire = time() + 300;
    setcookie($cookieName, $cookieValue, [
        'expires' => $expire,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

class FormMailer
{
    private string $to;
    private string $from;
    private string $pass;
    private string $fromName;
    private int $port;

    public function __construct()
    {
        $this->to       = defined('MAIL_TO_PUNY') ? MAIL_TO_PUNY : MAIL_TO;
        $this->from     = MAIL_FROM;
        $this->pass     = MAIL_PASS;
        $this->fromName = MAIL_FROM_NAME;
        $this->port     = defined('MAIL_PORT') ? (int)MAIL_PORT : 587;
    }

    public function send(array $array): bool
    {
        $type  = $array['type'] ?? 'form';
        $name  = $array['name']  ?? '';
        $phone = $array['phone'] ?? '';
        $email = $array['email'] ?? '';
        $msg   = $array['message'] ?? '';
        $form  = $array['форма'] ?? $type;

        $logoUrl   = SITE_DOMAIN_PUNY . '/public/assets/image/logo/favicon/favicon.svg';
        $mainColor = '#22c5be';
        $bgColor   = '#ffffff';
        $border    = '#e5e7eb';
        $font      = 'Segoe UI, Arial, sans-serif';
        $dateStr   = date('d.m.Y H:i');

        $header = "
            <div style='background: $bgColor; border-radius: 12px 12px 0 0; padding: 32px 32px 16px 32px; text-align: center; border-bottom: 1px solid $border;'>
                <img src='$logoUrl' alt='Логотип' style='width: 64px; height: 64px; margin-bottom: 12px;'>
                <h2 style='color: $mainColor; font-family: $font; margin: 0; font-size: 1.6rem;'>Новая заявка с сайта ФСС.РУС</h2>
            </div>
        ";

        $footer = "
            <div style='background: $bgColor; border-radius: 0 0 12px 12px; padding: 18px 32px 24px 32px; text-align: center; border-top: 1px solid $border; color: #6b7280; font-size: 0.95rem; font-family: $font;'>
                <small>Это автоматическое письмо, не отвечайте на него.</small>
            </div>
        ";

        $bodyRows = '';
        if ($name)  $bodyRows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Имя:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "</td></tr>";
        if ($phone) $bodyRows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Телефон:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>+" . htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') . "</td></tr>";
        if ($email) $bodyRows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Email:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</td></tr>";
        if ($msg)   $bodyRows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500;vertical-align:top'>Сообщение:</td><td style='padding:8px 0;color:#374151;background:#f0fdf4;border-radius:6px'>" . nl2br(htmlspecialchars($msg, ENT_QUOTES, 'UTF-8')) . "</td></tr>";
        $bodyRows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Форма:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>" . htmlspecialchars($form, ENT_QUOTES, 'UTF-8') . "</td></tr>";
        $bodyRows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Дата:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>$dateStr</td></tr>";

        $utmRows = '';
        foreach (['utm_source','utm_medium','utm_campaign','utm_term','utm_content','yclid','gclid','landing_page','referrer'] as $p) {
            if (!empty($array[$p])) {
                $utmRows .= "<tr><td style='padding:6px 0;color:#374151;font-weight:500'>" . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . ":</td><td style='padding:6px 0;color:#6b7280'>" . htmlspecialchars($array[$p], ENT_QUOTES, 'UTF-8') . "</td></tr>";
            }
        }

        $utmSection = $utmRows !== ''
            ? "<tr><td colspan='2' style='padding-top:12px'><hr style='border:none;border-top:1px solid $border'></td></tr>
                <tr><td colspan='2' style='padding:8px 0;color:#374151;font-weight:600'>UTM / трекинг</td></tr>$utmRows"
            : '';

        $body = "
            <div style='max-width:520px;margin:0 auto;background:$bgColor;border-radius:12px;box-shadow:0 4px 24px rgba(34,197,94,0.08);border:1px solid $border;font-family:$font;'>
                $header
                <div style='padding:28px 32px 18px 32px;'>
                    <table style='width:100%;border-collapse:collapse;font-size:1.08rem;'>
                        $bodyRows
                        $utmSection
                    </table>
                </div>
                $footer
            </div>
        ";

        if ($this->port == 465) {
            $smtpSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($this->port == 25) {
            $smtpSecure = '';
        } else {
            $smtpSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->CharSet    = 'UTF-8';
            $mail->Encoding   = 'base64';
            $mail->isSMTP();
            $mail->SMTPAuth   = true;
            $mail->SMTPDebug  = 0;
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPSecure = $smtpSecure;
            $mail->Port       = $this->port;
            $mail->Username   = $this->from;
            $mail->Password   = $this->pass;
            $mail->setFrom($this->from, $this->fromName);
            $mail->addAddress($this->to);
            $mail->Subject    = "Новая заявка с ФСС.РУС — форма: $form";
            $mail->msgHTML($body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email send failed: " . $mail->ErrorInfo);
            return false;
        }
    }
}

function deliverLead(FormMailer $formMailer, Messenger $messenger, array $array): void
{
    $mailOk = $formMailer->send($array);
    $tgOk = $messenger->sendToTelegram($array);
    $type = $array['type'] ?? 'form';

    if ($mailOk || $tgOk) {
        setFormSuccessCookie($type);
        Security::pushScript('if(window.dataLayer) dataLayer.push({event:"form_success", form_type:"' . addslashes($type) . '"});');
        Security::showAlert('success', 'Вы успешно оставили заявку! Мы свяжемся с вами в ближайшее время.');
    } else {
        Security::showAlert('error', 'Не удалось отправить заявку. Пожалуйста, позвоните нам +7 938 018-00-06 или попробуйте позже.');
    }
}

$utmParams = [];
foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'yclid', 'gclid', 'landing_page', 'referrer'] as $p) {
    $v = $_POST[$p] ?? $_GET[$p] ?? null;
    if ($v !== null && $v !== '') $utmParams[$p] = $v;
}
if (empty($utmParams['landing_page'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $utmParams['landing_page'] = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/');
}
if (empty($utmParams['referrer'])) {
    $utmParams['referrer'] = $_SERVER['HTTP_REFERER'] ?? '';
}

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['mailer']) || isset($_POST['mini-forma']) || isset($_POST['content_form']))) {

    $antibotError = null;
    if (!Antibot::validate($_POST, $antibotError)) {
        Security::showAlert('error', $antibotError);
        return;
    }

    $isMailer = isset($_POST['mailer']);
    $isMini = isset($_POST['mini-forma']);
    $isContent = isset($_POST['content_form']);

    $formMailer = new FormMailer();
    $messenger = new Messenger();
    $err = '';

    if ($isMailer) {
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
            'форма' => 'modal',
        ] + $utmParams;

        deliverLead($formMailer, $messenger, $array);

    } elseif ($isMini) {
        $nameV = Security::validateName($_POST['name'] ?? '', $err);
        if ($nameV === false) { Security::showAlert('error', $err); return; }
        $phoneV = Security::validatePhone($_POST['phone'] ?? '', $err);
        if ($phoneV === false) { Security::showAlert('error', $err); return; }
        $array = [
            'name' => $nameV,
            'phone' => $phoneV,
            'type' => 'mini-forma',
            'форма' => 'mini-forma',
        ] + $utmParams;

        deliverLead($formMailer, $messenger, $array);

    } elseif ($isContent) {
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
            'форма' => 'content_form',
        ] + $utmParams;

        deliverLead($formMailer, $messenger, $array);
    }
}