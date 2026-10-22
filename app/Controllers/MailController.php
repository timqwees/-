<?php

namespace App\Controllers;

use App\Mail\Outbox;
use App\Mail\Transport;
use App\Models\Network\Message;

class MailController
{
    /**
     * Отправка письма (контракт QweesCore: onMail($to_mail, $subject, $body)).
     *
     * Четвёртый аргумент ($options) — расширение сайта, сам вызов
     * (new MailController())->onMail($to, $subject, $body) остаётся валидным.
     * $options: reply_to, reply_name, alt_body, cc, bcc, queue_only.
     *
     * Возвращает true, если письмо ушло. Если отправка не удалась, но заявка
     * надёжно сохранена в очереди — используйте dispatch(): там видно,
     * что заявка не потеряна.
     */
    public function onMail(string $to_mail, string $subject, string $body, array $options = [])
    {
        return $this->dispatch($to_mail, $subject, $body, $options)['status'] === 'sent';
    }

    /**
     * Отправка с гарантией «заявка не теряется».
     *
     * Порядок действий:
     *   1) пробуем отправить письмо (SMTP → запасной sendmail);
     *   2) если не вышло — кладём письмо в долговечную очередь (App\Mail\Outbox);
     *   3) если и очередь недоступна — только тогда заявка считается потерянной.
     *
     * @param array<string,mixed> $options
     *
     * @return array{status:string,transport:string,error:string,id:string}
     *         status: sent — письмо ушло; queued — заявка в очереди; lost — заявка потеряна
     */
    public function dispatch(string $to_mail, string $subject, string $body, array $options = [])
    {
        if (empty($to_mail)) {
            Message::set('error', "Пустой email получателя!");
            return ['status' => 'lost', 'transport' => '', 'error' => 'пустой получатель', 'id' => ''];
        } elseif (empty($subject)) {
            Message::set('error', "Пустая тема письма!");
            return ['status' => 'lost', 'transport' => '', 'error' => 'пустая тема', 'id' => ''];
        } elseif (empty($body)) {
            Message::set('error', "Пустое содержание письма!");
            return ['status' => 'lost', 'transport' => '', 'error' => 'пустое тело', 'id' => ''];
        }

        $mail = [
            'to_email' => $to_mail,
            'subject'  => $subject,
            'body'     => $body,
        ];
        foreach (['reply_to', 'reply_name', 'alt_body', 'cc', 'bcc'] as $key) {
            if (!empty($options[$key])) {
                $mail[$key] = (string)$options[$key];
            }
        }
        if (!empty($options['lead']) && is_array($options['lead'])) {
            $mail['lead'] = $options['lead'];
        }

        try {
            // Письмо, которое не обязано уйти сразу (копия клиенту):
            // сразу в очередь, чтобы не задерживать ответ пользователю.
            if (!empty($options['queue_only'])) {
                // Если почтовых транспортов нет (выключены или работает только
                // Bitrix) — копию не создаём: письма всё равно не уйдут.
                if (!Transport::hasMailTransport()) {
                    error_log('[mail] Копия клиенту пропущена: почтовые транспорты отключены');
                    return ['status' => 'lost', 'transport' => '', 'error' => 'почтовые транспорты отключены', 'id' => ''];
                }
                $id = Outbox::put([
                    'to' => $mail['to_email'],
                    'subject' => $mail['subject'],
                    'body' => $mail['body'],
                    'alt_body' => (string)($mail['alt_body'] ?? ''),
                    'reply_to' => (string)($mail['reply_to'] ?? ''),
                    'reply_name' => (string)($mail['reply_name'] ?? ''),
                    'lead' => is_array($mail['lead'] ?? null) ? $mail['lead'] : [],
                    'kind' => 'copy',
                ]);
                if ($id === '') {
                    // Очередь недоступна — отправляем как обычно, не теряя копию.
                    $result = Transport::send($mail);
                    return $result['ok']
                        ? ['status' => 'sent', 'transport' => $result['transport'], 'error' => '', 'id' => '']
                        : $this->queueOrLost($mail, $result['error']);
                }
                error_log('[mail] Копия клиенту поставлена в очередь: ' . $id);
                return ['status' => 'queued', 'transport' => 'outbox', 'error' => '', 'id' => $id];
            }

            $result = Transport::send($mail);
            if ($result['ok']) {
                error_log('[mail] Письмо отправлено транспортом: ' . $result['transport']);
                return ['status' => 'sent', 'transport' => $result['transport'], 'error' => '', 'id' => ''];
            }

            return $this->queueOrLost($mail, $result['error']);
        } catch (\Throwable $e) {
            $error = "Ошибка отправки письма: " . $e->getMessage();
            Message::set('error', $error);
            self::saveLastError($error);
            error_log('[mail] ' . $error);
            return ['status' => 'lost', 'transport' => '', 'error' => $e->getMessage(), 'id' => ''];
        }
    }

    /**
     * Письмо не ушло — сохраняем заявку в очередь.
     * Если очередь недоступна — заявка действительно потеряна.
     *
     * @param array<string,string> $mail
     *
     * @return array{status:string,transport:string,error:string,id:string}
     */
    private function queueOrLost(array $mail, string $error): array
    {
        $id = Outbox::put([
            'to' => (string)($mail['to_email'] ?? ''),
            'subject' => (string)($mail['subject'] ?? ''),
            'body' => (string)($mail['body'] ?? ''),
            'alt_body' => (string)($mail['alt_body'] ?? ''),
            'reply_to' => (string)($mail['reply_to'] ?? ''),
            'reply_name' => (string)($mail['reply_name'] ?? ''),
            'lead' => is_array($mail['lead'] ?? null) ? $mail['lead'] : [],
            'kind' => 'lead',
        ]);

        if ($id !== '') {
            Message::set('error', $error);
            self::saveLastError($error);
            error_log('[mail] Письмо не отправлено (' . $error . '), заявка сохранена в очереди: ' . $id);
            return ['status' => 'queued', 'transport' => 'outbox', 'error' => $error, 'id' => $id];
        }

        Message::set('error', $error);
        self::saveLastError($error);
        error_log('[mail] Письмо не отправлено (' . $error . '), очередь недоступна — заявка потеряна');
        return ['status' => 'lost', 'transport' => '', 'error' => $error, 'id' => ''];
    }

    /**
     * Последняя ошибка отправки — в файл рантайм-кэша.
     * Читается без доступа к сайту: php tools/mail-test.php --last-error
     * или просто cat файла из панели хостинга.
     */
    private static function saveLastError(string $error): void
    {
        $file = \App\Models\Cache\Path::file('mail-last-error.txt');
        if ($file === '') {
            return;
        }
        $dir = dirname($file);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents(
            $file,
            '[' . date('Y-m-d H:i:s') . "] " . $error . "\n",
            FILE_APPEND | LOCK_EX
        );
    }
}
