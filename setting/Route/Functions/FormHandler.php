<?php declare(strict_types=1);

namespace Setting\Route\Functions;

use App\Controllers\MailController;
use Exception;

/**
 * Обработчик POST-форм сайта.
 * Маршрут: POST /send/email → self::sendMail()
 * Письмо: MailController::onMail() → Network::onPHPMailer()
 */
class FormHandler
{
    /**
     * Редирект на текущую страницу + запись результата в сессию
     * (её читает app/bootstrap.php и показывает уведомление).
     *
     * @param array<string,string> $result Данные заявки для экрана «Заявка принята»
     *                                       (ProMaintenance enquiry-success)
     */
    private static function redirect(bool $success, string $message, string $formType = 'modal', array $result = []): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['fss_form_success'] = [
            'type' => $formType,
            'mail' => $success,
            'message' => $message,
            'time' => time(),
        ];

        // Данные заявки для экрана успеха (enquiry-success) — только при успешной отправке
        if ($success && $result !== []) {
            $_SESSION['fss_enquiry_result'] = $result + ['type' => $formType];
        }

        // Возвращаемся на страницу, с которой отправлена форма
        $path = '/';
        $referer = (string)($_SERVER['HTTP_REFERER'] ?? '');
        if ($referer !== '') {
            $refPath = parse_url($referer, PHP_URL_PATH);
            if (is_string($refPath) && $refPath !== '') {
                $path = $refPath;
            }
        }

        $host = (string)($_SERVER['HTTP_HOST'] ?? 'xn--q1aah.xn--p1acf');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';

        header("Location: {$scheme}://{$host}{$path}", true, 303);
        exit;
    }

    public static function sendMail(): void
    {
        $type = self::getFormType();
        if (!$type) {
            self::redirect(false, 'Неизвестный тип формы.');
            return;
        }

        // === АНТИ-БОТ: уровень 1 — rate limit по IP ===
        if (!self::antiBotRateLimit()) {
            self::redirect(false, 'Слишком много заявок. Попробуйте позже или позвоните нам.', $type);
            return;
        }

        // === АНТИ-БОТ: уровень 2 — honeypot ===
        // Поле из public/components/antibot_fields.php — скрытое, человек его не видит
        $honeypotFields = ['fax_number', 'website', 'url', 'link'];
        foreach ($honeypotFields as $trap) {
            if (!empty($_POST[$trap])) {
                error_log('Honeypot trap triggered: ' . $trap);
                self::redirect(true, 'Спасибо, мы свяжемся с вами!', $type);
                return;
            }
        }

        // === АНТИ-БОТ: уровень 3 — time-check (отправка быстрее 3 секунд) ===
        $ts = (int)($_POST['_ts'] ?? 0);
        if ($ts > 0) {
            $elapsed = (int)((microtime(true) * 1000 - $ts) / 1000);
            if ($elapsed < 3) {
                error_log("Anti-bot: form submitted in {$elapsed}s — likely bot");
                self::redirect(true, 'Спасибо, мы свяжемся с вами!', $type);
                return;
            }
        }

        // === АНТИ-БОТ: уровень 4 — JS-токен ===
        if (!self::antiBotJsToken()) {
            self::redirect(true, 'Спасибо, мы свяжемся с вами!', $type);
            return;
        }

        // === ВАЛИДАЦИЯ ===
        $name = trim(strip_tags($_POST['name'] ?? ''));
        if (mb_strlen($name, 'UTF-8') < 2 || !preg_match('/^[\p{L}\s\-\.\']+$/u', $name)) {
            self::redirect(false, 'Некорректное имя.', $type);
            return;
        }

        $company = trim(strip_tags($_POST['company'] ?? ''));
        if ($company !== '' && mb_strlen($company, 'UTF-8') > 120) {
            self::redirect(false, 'Название компании слишком длинное.', $type);
            return;
        }

        $phone = self::normalizePhone($_POST['phone'] ?? '');
        if ($phone === '' || !self::antiBotPhoneCheck($phone)) {
            self::redirect(false, 'Укажите корректный номер телефона.', $type);
            return;
        }

        $email = '';
        if ($type === 'modal') {
            $email = trim($_POST['email'] ?? '');
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                self::redirect(false, 'Некорректный email.', $type);
                return;
            }
        }

        $location = trim(strip_tags($_POST['location'] ?? ''));
        if (mb_strlen($location, 'UTF-8') > 120) {
            self::redirect(false, 'Название локации слишком длинное.', $type);
            return;
        }

        $msg = trim(strip_tags($_POST['message'] ?? ''));
        if (($type === 'modal' || $type === 'content_form') && mb_strlen($msg, 'UTF-8') < 10) {
            self::redirect(false, 'Сообщение слишком короткое.', $type);
            return;
        }

        // Согласие на обработку данных — обязательно для полных форм.
        // Быстрый виджет (mini-forma) оставляет только имя и телефон — согласие там не запрашивается.
        if ($type !== 'mini-forma' && (string)($_POST['checkbox'] ?? '') !== 'on') {
            self::redirect(false, 'Необходимо согласие на обработку персональных данных.', $type);
            return;
        }

        // === BUILD EMAIL ===
        $subject = "Новая заявка с ФСС.РУС — форма: {$type}";
        $body = self::buildEmailBody($name, $phone, $email, $msg, $type, $company, $location);

        // === SEND ===
        $success = false;
        try {
            $mailController = new MailController();
            $toEmail = (string)($_ENV['EMAIL_TO'] ?? $_SERVER['EMAIL_TO'] ?? getenv('EMAIL_TO') ?? 'order@фсс.рус');
            $success = $mailController->onMail($toEmail, $subject, $body);
        } catch (Exception $e) {
            error_log('Mail Error: ' . $e->getMessage());
        }

        $message = $success
            ? 'Вы успешно оставили заявку! Мы свяжемся с вами в ближайшее время.'
            : 'Не удалось отправить заявку. Пожалуйста, позвоните нам +7 938 018-00-06 или попробуйте позже.';

        // Данные для экрана «Заявка принята» (ProMaintenance enquiry-success)
        $result = [];
        if ($success && $type === 'modal') {
            $result = [
                'name' => $name,
                'service' => (string)($_POST['product'] ?? ''),
                'email' => $email !== '' ? $email : $phone,
                'phone' => $phone,
                'reference' => 'ФСС-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2))),
            ];
        }

        self::redirect($success, $message, $type, $result);
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
            $history = json_decode((string)file_get_contents($file), true) ?: [];
            $history = array_values(array_filter($history, fn($t) => ($now - (int)$t) < $window));
        }

        if (count($history) >= $maxRequests) {
            error_log("Anti-bot: rate limit exceeded for IP {$ip} (" . count($history) . " requests/10min)");
            return false;
        }

        $history[] = $now;
        @file_put_contents($file, json_encode($history));
        return true;
    }

    private static function antiBotJsToken(): bool
    {
        $token = (string)($_POST['_js_token'] ?? '');
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
        }
        if (strlen($digits) === 11 && $digits[0] === '8') {
            return '+7' . substr($digits, 1);
        }
        return '+' . $digits;
    }

    private static function buildEmailBody(
        string $name,
        string $phone,
        string $email,
        string $msg,
        string $type,
        string $company = '',
        string $location = ''
    ): string {
        $mainColor = '#22c5be';
        $bgColor = '#ffffff';
        $border = '#e5e7eb';
        $font = 'Segoe UI, Arial, sans-serif';
        $date = date('d.m.Y H:i');
        $logo = 'https://xn--q1aah.xn--p1acf/public/assets/image/logo/favicon/favicon.svg';

        $row = function (string $label, string $value) use ($mainColor): string {
            if ($value === '') return '';
            return "<tr><td style='padding:8px 0;color:#374151;font-weight:500'>" . htmlspecialchars($label) . ":</td>"
                . "<td style='padding:8px 0;color:" . $mainColor . ";font-weight:600'>" . htmlspecialchars($value) . "</td></tr>";
        };

        $rows = $row('Имя', $name);
        $rows .= $row('Компания', $company);
        $rows .= $row('Телефон', $phone);
        $rows .= $row('Email', $email);
        $rows .= $row('Локация', $location);
        if ($msg !== '') {
            $rows .= "<tr><td style='padding:8px 0;color:#374151;font-weight:500;vertical-align:top'>Сообщение:</td>"
                . "<td style='padding:8px 0;color:#374151;background:#f0fdf4;border-radius:6px'>" . nl2br(htmlspecialchars($msg)) . "</td></tr>";
        }
        $rows .= $row('Форма', $type);
        $rows .= $row('Дата', $date);

        return "
            <div style='max-width:520px;margin:0 auto;background:$bgColor;border-radius:12px;box-shadow:0 4px 24px rgba(34,197,94,0.08);border:1px solid $border;font-family:$font;'>
                <div style='background: $bgColor; border-radius: 12px 12px 0 0; padding: 32px 32px 16px 32px; text-align: center; border-bottom: 1px solid $border;'>
                    <img src='$logo' alt='Логотип' style='width: 64px; height: 64px; margin-bottom: 12px;'>
                    <h2 style='color: $mainColor; font-family: $font; margin: 0; font-size: 1.6rem;'>Новая заявка с сайта ФСС.РУС</h2>
                </div>
                <div style='padding:28px 32px 18px 32px;'>
                    <table style='width:100%;border-collapse:collapse;font-size:1.08rem;'>$rows</table>
                </div>
                <div style='background: $bgColor; border-radius: 0 0 12px 12px; padding: 18px 32px 24px 32px; text-align: center; border-top: 1px solid $border; color: #6b7280; font-size: 0.95rem; font-family:$font;'>
                    <small>Это автоматическое письмо, не отвечайте на него.</small>
                </div>
            </div>";
    }
}
