<?php

namespace App\Controllers;

use App\Models\Network\Network;
use App\Models\Network\Message;

class MailController
{
    /**
     * Отправка письма.
     *
     * Контракт QweesCore: onMail($to_mail, $subject, $body).
     * Четвёртый аргумент ($options) — расширение сайта, сам вызов
     * (new MailController())->onMail($to, $subject, $body) остаётся валидным.
     *
     * $options: reply_to, reply_name, alt_body, cc, bcc.
     */
    public function onMail(string $to_mail, string $subject, string $body, array $options = [])
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

        foreach (['reply_to', 'reply_name', 'alt_body', 'cc', 'bcc'] as $key) {
            if (!empty($options[$key])) {
                $mailer[$key] = (string)$options[$key];
            }
        }

        try {
            return Network::onPHPMailer($mailer);
        } catch (\Exception $e) {
            Message::set('error', "Ошибка отправки письма: " . $e->getMessage());
            error_log('[mail] Ошибка отправки письма: ' . $e->getMessage());
            return false;
        }
    }
}