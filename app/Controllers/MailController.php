<?php

namespace App\Controllers;

use App\Models\Network\Network;
use App\Models\Network\Message;

class MailController
{
    public function onMail(string $to_mail, string $subject, string $body)
    {
        if (empty($to_mail)) {
            Message::set('error', "Пустой email получателя!");
            return false;
        } elseif (empty($subject)) {
            Message::set('error', "Пустая тема письма!");
            return false;
        } elseif (empty($body)) {
            Message::set('error', "Пустое содержание письма!");
            return false;
        }

        $mailer = [
            "to_email" => $to_mail,
            "subject"  => $subject,
            "body"     => $body,
        ];

        try {
            return Network::onPHPMailer($mailer);
        } catch (\Exception $e) {
            Message::set('error', "Ошибка отправки письма: " . $e->getMessage());
            return false;
        }
    }
}