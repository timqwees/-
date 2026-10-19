<?php declare(strict_types=1);

namespace Setting\Route\Functions;

use App\Models\Router\Routes;
use App\Controllers\MailController;
use App\Models\Network\Network;
use App\Models\Network\Message;
use Exception;

class FormHandler
{
    private static function redirect(string $path): void
    {
        $url = "https://фсс.рус" . $path;
        header("Location: $url", true, 303);
        exit;
    }

    public static function sendMail(): void
    {
        // === АНТИ-БОТ: уровень 1 — rate limit по IP ===
        if (!self::antiBotRateLimit()) {
            self::redirect("/?message_status=error&message_msg=" . urlencode('Слишком много заявок. Попробуйте позже или позвоните нам.'));
            return;
        }

        // === АНТИ-БОТ: уровень 2 — honeypot ===
        if (!empty($_POST['website']) || !empty($_POST['url']) || !empty($_POST['link'])) {
            error_log('Honeypot trap triggered: ' . ($_POST['website'] ?? $_POST['url'] ?? $_POST['link'] ?? ''));
            self::redirect("/?message_status=success&message_msg=" . urlencode('Спасибо, мы свяжемся с вами!'));
            return;
        }

        // === АНТИ-БОТ: уровень 3 — time-check (отправка быстрее 3 секунд) ===
        $ts = (int) ($_POST['_ts'] ?? 0);
        if ($ts > 0) {
            $elapsed = (int) ((microtime(true) * 1000 - $ts) / 1000);
            if ($elapsed < 3) {
                error_log("Anti-bot: form submitted in {$elapsed}s — likely bot");
                self::redirect("/?message_status=success&message_msg=" . urlencode('Спасибо, мы свяжемся с вами!'));
                return;
            }
        }

        // === АНТИ-БОТ: уровень 4 — JS-токен ===
        if (!self::antiBotJsToken()) {
            self::redirect("/?message_status=success&message_msg=" . urlencode('Спасибо, мы свяжемся с вами!'));
            return;
        }

        // === ВАЛИДАЦИЯ ===
        $type = self::getFormType();
        if (!$type) {
            self::redirect("/?message_status=error&message_msg=" . urlencode('Неизвестный тип формы.'));
            return;
        }

        $name = trim(strip_tags($_POST['name'] ?? ''));
        if (mb_strlen($name, 'UTF-8') < 2 || !preg_match('/^[\p{L}\s\-\.\']+$/u', $name)) {
            self::redirect("/?message_status=error&message_msg=" . urlencode('Некорректное имя.'));
            return;
        }

        $phone = self::normalizePhone($_POST['phone'] ?? '');
        if ($phone === '' || !self::antiBotPhoneCheck($phone)) {
            self::redirect("/?message_status=error&message_msg=" . urlencode('Укажите корректный номер телефона.'));
            return;
        }

        $email = '';
        if ($type === 'modal') {
            $email = trim($_POST['email'] ?? '');
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                self::redirect("/?message_status=error&message_msg=" . urlencode('Некорректный email.'));
                return;
            }
        }

        $msg = trim(strip_tags($_POST['message'] ?? ''));
        if (($type === 'modal' || $type === 'content_form') && mb_strlen($msg, 'UTF-8') < 10) {
            self::redirect("/?message_status=error&message_msg=" . urlencode('Сообщение слишком короткое.'));
            return;
        }

        if (isset($_POST['checkbox']) && $_POST['checkbox'] !== 'on') {
            self::redirect("/?message_status=error&message_msg=" . urlencode('Необходимо согласие на обработку персональных данных.'));
            return;
        }

        // === BUILD EMAIL ===
        $subject = "Новая заявка с ФСС.РУС — форма: $type";
        $body = self::buildEmailBody($name, $phone, $email, $msg, $type);

        // === SEND ===
        $success = false;
        try {
            $mailController = new MailController();
            $toEmail = (string)($_ENV['EMAIL_TO'] ?? $_SERVER['EMAIL_TO'] ?? getenv('EMAIL_TO') ?? 'order@фсс.рус');
            $success = $mailController->onMail($toEmail, $subject, $body);
        } catch (Exception $e) {
            error_log('Mail Error: ' . $e->getMessage());
        }

        $status = $success ? 'success' : 'error';
        $msg = $success ? 'Вы успешно оставили заявку! Мы свяжемся с вами в ближайшее время.' : 'Не удалось отправить заявку. Пожалуйста, позвоните нам +7 938 018-00-06 или попробуйте позже.';
        self::redirect("/?message_status={$status}&message_msg=" . urlencode($msg));
    }

    private static function getFormType(): ?string
    {
        if (isset($_POST['mailer'])) return 'modal';
        if (isset($_POST['mini-forma'])) return 'mini-forma';
        if (isset($_POST['content_form'])) return 'content_form';
        return null;
    }

    private static function antiBotRateLimit(): bool
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ip = trim(explode(',', $ip)[0]);
        $dir = sys_get_temp_dir() . '/fss-ratelimit/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        $file = $dir . md5($ip) . '.json';
        $now = time();
        $window = 600;      // окно 10 минут
        $maxRequests = 5;   // максимум 5 заявок в 10 минут с одного IP

        $history = [];
        if (is_file($file)) {
            $history = json_decode((string) file_get_contents($file), true) ?: [];
            $history = array_values(array_filter($history, fn($t) => ($now - (int) $t) < $window));
        }

        if (count($history) >= $maxRequests) {
            error_log("Anti-bot: rate limit exceeded for IP {$ip} (" . count($history) . " requests/10min)");
            return false;
        }

        $history[] = $now;
        file_put_contents($file, json_encode($history));
        return true;
    }

    private static function antiBotJsToken(): bool
    {
        $token = (string) ($_POST['_js_token'] ?? '');
        if ($token === '') {
            error_log('Anti-bot: missing _js_token — likely bot without JS');
            return false;
        }
        if (!ctype_digit($token) || strlen($token) < 10 || strlen($token) > 15) {
            error_log("Anti-bot: invalid _js_token format: {$token}");
            return false;
        }
        return true;
    }

    private static function antiBotPhoneCheck(string $phone): bool
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return false;
        }
        if (preg_match('/^(\d)\1+$/', $digits)) {
            return false;
        }
        if ($digits === '1234567890' || $digits === '0123456789') {
            return false;
        }
        return true;
    }

private static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if ($digits === '') {
            return '';
        }
        if (strlen($digits) === 10) {
            return '+7' . $digits;
        } elseif (strlen($digits) === 11 && $digits[0] === '8') {
            return '+7' . substr($digits, 1);
        } else {
            return '+' . ltrim($digits, '+');
        }
    }

    private static function buildEmailBody(string $name, string $phone, string $email, string $msg, string $type): string
    {
        $mainColor = '#22c5be';
        $bgColor = '#ffffff';
        $border = '#e5e7eb';
        $font = 'Segoe UI, Arial, sans-serif';
        $date = date('d.m.Y H:i');
        $logo = 'https://фсс.рус/public/assets/image/logo/favicon/favicon.svg';

        $rows = "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Имя:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>" . htmlspecialchars($name) . "</td></tr>";
        $rows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Телефон:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>" . htmlspecialchars($phone) . "</td></tr>";
        if ($email) $rows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Email:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>" . htmlspecialchars($email) . "</td></tr>";
        if ($msg) $rows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500;vertical-align:top'>Сообщение:</td><td style='padding:8px 0;color:#374151;background:#f0fdf4;border-radius:6px'>" . nl2br(htmlspecialchars($msg)) . "</td></tr>";
        $rows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Форма:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>" . htmlspecialchars($type) . "</td></tr>";
        $rows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>Дата:</td><td style='padding:8px 0;color:$mainColor;font-weight:600'>$date</td></tr>";

        return "
            <div style='max-width:520px;margin:0 auto;background:$bgColor;border-radius:12px;box-shadow:0 4px 24px rgba(34,197,94,0.08);border:1px solid $border;font-family:$font;'>
                <div style='background: $bgColor; border-radius: 12px 12px 0 0; padding: 32px 32px 16px 32px; text-align: center; border-bottom: 1px solid $border;'>
                    <img src='$logo' alt='Логотип' style='width: 64px; height: 64px; margin-bottom: 12px;'>
                    <h2 style='color: $mainColor; font-family: $font; margin: 0; font-size: 1.6rem;'>Новая заявка с сайта ФСС.РУС</h2>
                </div>
                <div style='padding:28px 32px 18px 32px;'>
                    <table style='width:100%;border-collapse:collapse;font-size:1.08rem;'>$rows</table>
                </div>
                <div style='background: $bgColor; border-radius: 0 0 12px 12px; padding: 18px 32px 24px 32px; text-align: center; border-top: 1px solid $border; color: #6b7280; font-size: 0.95rem; font-family: $font;'>
                    <small>Это автоматическое письмо, не отвечайте на него.</small>
                </div>
            </div>";
    }
}