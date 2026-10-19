<?php

namespace App\Models\Network;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Network
{
    /**
     * Отправка email через PHPMailer (QweesCore style)
     *
     * @param array $data ['to_email', 'subject', 'body']
     * @return bool
     */
    public function onPHPMailer(array $data): bool
    {
        $to_email = $data['to_email'] ?? EMAIL_TO;
        $subject  = $data['subject'] ?? 'Новая заявка с ФСС.РУС';
        $body     = $data['body'] ?? '';

        if (empty($to_email) || empty($subject) || empty($body)) {
            error_log('Network::onPHPMailer: missing required fields');
            return false;
        }

        if (!defined('EMAIL') || !defined('EMAIL_PASSWORD') || !EMAIL || !EMAIL_PASSWORD) {
            error_log('Network::onPHPMailer: missing SMTP config (EMAIL, EMAIL_PASSWORD)');
            return false;
        }

        $port = defined('EMAIL_PORT') ? EMAIL_PORT : 587;
        if ($port == 465) {
            $smtpSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($port == 25) {
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
            $mail->Port       = $port;
            $mail->Username   = EMAIL;
            $mail->Password   = EMAIL_PASSWORD;
            $mail->setFrom(EMAIL, EMAIL_NICKNAME);
            $mail->addAddress($to_email);
            $mail->Subject    = $subject;
            $mail->msgHTML($body);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Network::onPHPMailer ERROR: ' . $e->getMessage() . ' | ' . $mail->ErrorInfo);
            return false;
        }
    }
}