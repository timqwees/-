<?php declare(strict_types=1);

namespace Setting\Route\Functions;

use App\Controllers\MailController;
use App\Models\Network\Message;
use Exception;

/**
 * Обработчик POST-форм сайта.
 * Маршрут: POST /send/email → self::sendMail()
 * Письмо: MailController::onMail() → Network::onPHPMailer()
 *
 * Типы форм (маркер в hidden-поле):
 *   modal        — заявка с главной и /contacts (enquiry_form.php)
 *   mini-forma   — плавающий виджет в футере (forma.php)
 *   content_form — калькулятор на страницах услуг (service_template.php)
 *   subscribe    — подписка на рассылку (blog/list.php)
 */
class FormHandler
{
    /** Типы форм, для которых не нужны имя/телефон. */
    private const TYPES_WITHOUT_CONTACTS = ['subscribe'];

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

        // Возвращаемся на страницу, с которой отправлена форма.
        // Редирект относительный: host берём из Referer только как путь,
        // чтобы значение HTTP_HOST нельзя было подделать (open redirect).
        header('Location: ' . self::refererPath(), true, 303);
        exit;
    }

    /** Путь страницы-источника из Referer (только путь, без хоста). */
    private static function refererPath(): string
    {
        $referer = (string)($_SERVER['HTTP_REFERER'] ?? '');
        if ($referer === '') {
            return '/';
        }
        $path = parse_url($referer, PHP_URL_PATH);
        if (!is_string($path) || $path === '' || $path[0] !== '/') {
            return '/';
        }
        // Защита от CRLF-инъекции в заголовок Location
        if (str_contains($path, "\r") || str_contains($path, "\n")) {
            return '/';
        }
        return $path;
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
                self::redirect(false, 'Заявка не отправлена. Обновите страницу и попробуйте ещё раз или позвоните нам.', $type);
                return;
            }
        }

        // === АНТИ-БОТ: уровень 3 — time-check + CSRF-подпись ===
        // _ts обязателен: без него проверка времени не выполнялась вовсе.
        // _csrf — HMAC от времени, подписанный секретом сервера: сторонний сайт
        // не может получить валидную пару (_ts/_csrf) и отправить форму от имени пользователя.
        $ts = (int)($_POST['_ts'] ?? 0);
        if ($ts <= 0) {
            error_log('Anti-bot: missing _ts');
            self::redirect(false, 'Заявка не отправлена. Обновите страницу и попробуйте ещё раз или позвоните нам.', $type);
            return;
        }
        if (!self::antiBotCsrf((int)$ts, (string)($_POST['_csrf'] ?? ''))) {
            error_log('Anti-bot: invalid CSRF token');
            self::redirect(false, 'Заявка не отправлена: проверка безопасности не пройдена. Обновите страницу и попробуйте ещё раз.', $type);
            return;
        }
        $elapsed = (int)((microtime(true) * 1000 - $ts) / 1000);
        if ($elapsed < 1) {
            // Отклонять здесь нельзя: реальный пользователь с автозаполнением
            // успевает отправить форму быстрее секунды, и он получал отказ.
            // Настоящую защиту даёт CSRF-подпись выше (её бот подделать не может).
            error_log("Anti-bot: form submitted in {$elapsed}s");
        }

        // === АНТИ-БОТ: уровень 4 — JS-токен ===
        if (!self::antiBotJsToken()) {
            self::redirect(false, 'Не удалось отправить заявку: браузер не прошёл проверку. Включите JavaScript или позвоните нам.', $type);
            return;
        }

        // === ВАЛИДАЦИЯ ===
        $name = trim(strip_tags($_POST['name'] ?? ''));
        $phone = trim(strip_tags($_POST['phone'] ?? ''));
        $email = trim(strip_tags($_POST['email'] ?? ''));
        $company = trim(strip_tags($_POST['company'] ?? ''));
        $location = trim(strip_tags($_POST['location'] ?? ''));
        $msg = trim(strip_tags($_POST['message'] ?? ''));
        $needsContacts = !in_array($type, self::TYPES_WITHOUT_CONTACTS, true);

        if ($needsContacts) {
            if (mb_strlen($name, 'UTF-8') < 2 || !preg_match('/^[\p{L}\s\-\.\']+$/u', $name)) {
                self::redirect(false, 'Некорректное имя.', $type);
                return;
            }
            if ($company !== '' && mb_strlen($company, 'UTF-8') > 120) {
                self::redirect(false, 'Название компании слишком длинное.', $type);
                return;
            }
            $phone = self::normalizePhone($phone);
            if ($phone === '' || !self::antiBotPhoneCheck($phone)) {
                self::redirect(false, 'Укажите корректный номер телефона.', $type);
                return;
            }
        } else {
            $phone = '';
        }

        // Email: для полной формы — обязателен, для подписки — обязателен,
        // для калькулятора на услугах — необязателен (но проверяем формат).
        if ($type === 'modal' || $type === 'subscribe') {
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                self::redirect(false, 'Некорректный email.', $type);
                return;
            }
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::redirect(false, 'Некорректный email.', $type);
            return;
        }

        if (mb_strlen($location, 'UTF-8') > 120) {
            self::redirect(false, 'Название локации слишком длинное.', $type);
            return;
        }

        if (($type === 'modal' || $type === 'content_form') && mb_strlen($msg, 'UTF-8') < 10) {
            self::redirect(false, 'Сообщение слишком короткое.', $type);
            return;
        }

        // Согласие на обработку данных — обязательно для форм, где есть чекбоксы.
        // Быстрый виджет (mini-forma) оставляет только имя и телефон — согласие там не запрашивается.
        if ($type !== 'mini-forma' && (string)($_POST['checkbox'] ?? '') !== 'on') {
            self::redirect(false, 'Необходимо согласие на обработку персональных данных.', $type);
            return;
        }

        // Для полной формы (modal) отдельно требуем согласие на обработку ПД
        if ($type === 'modal' && (string)($_POST['personal_data'] ?? '') !== 'on') {
            self::redirect(false, 'Необходимо согласие на обработку моих персональных данных.', $type);
            return;
        }

        // === BUILD EMAIL ===
        $subject = self::subjectFor($type, $name);
        $heading = $type === 'subscribe' ? 'Новая подписка на рассылку ФСС.РУС' : 'Новая заявка с сайта ФСС.РУС';
        if ($type === 'subscribe' && $msg === '') {
            $msg = 'Подписка на информационную рассылку ФСС.РУС';
        }
        $body = self::buildEmailBody($name, $phone, $email, $msg, $type, $company, $location, $heading);
        $altBody = self::buildPlainText($name, $phone, $email, $msg, $type, $company, $location);

        // === SEND ===
        $success = false;
        try {
            $mailController = new MailController();
            $toEmail = self::envValue('EMAIL_TO', 'order@фсс.рус');
            $options = [];
            if ($email !== '' && $type !== 'subscribe') {
                // Ответить менеджеру можно прямо на почту клиента
                $options['reply_to'] = $email;
                $options['reply_name'] = $name !== '' ? $name : $company;
            }
            $options['alt_body'] = $altBody;
            $success = $mailController->onMail($toEmail, $subject, $body, $options);
        } catch (Exception $e) {
            error_log('Mail Error: ' . $e->getMessage());
        }

        if (!$success) {
            $detail = Message::first('error');
            error_log('[form] Отправка не удалась (тип: ' . $type . '): ' . ($detail ?: 'без описания'));
        } elseif ($type === 'modal' && $email !== '') {
            // Копия клиенту — на экране успеха обещано «Дубли отправили на вашу почту»
            self::sendCopyToClient($mailController, $email, [
                'name' => $name,
                'company' => $company,
                'phone' => $phone,
                'email' => $email,
                'location' => $location,
                'message' => $msg,
                'product' => (string)($_POST['product'] ?? ''),
            ]);
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

    /**
     * Значение переменной окружения с fallback.
     * getenv() возвращает false, а не null — `??` это не лечит,
     * поэтому разбираем варианты явно.
     */
    private static function envValue(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        if ($value === null || $value === false || (string)$value === '') {
            $value = getenv($key);
        }
        $value = ($value === false || $value === null) ? '' : (string)$value;
        return $value !== '' ? $value : $default;
    }

    /** Тема письма по типу формы. */
    private static function subjectFor(string $type, string $name): string
    {
        if ($type === 'subscribe') {
            return 'Подписка на рассылку ФСС.РУС';
        }
        return "Новая заявка с ФСС.РУС — форма: {$type}";
    }

    /**
     * Копия заявки клиенту на его почту.
     * Ошибка копии не влияет на результат основной отправки.
     *
     * @param array<string,string> $fields Поля заявки
     */
    private static function sendCopyToClient(
        MailController $mailController,
        string $email,
        array $fields
    ): void {
        try {
            $ok = $mailController->onMail(
                $email,
                'ФСС.РУС — ваша заявка от ' . date('d.m.Y H:i'),
                self::buildCopyBody($fields),
                ['alt_body' => self::buildCopyPlainText($fields)]
            );
            if (!$ok) {
                error_log('[form] Копия клиенту не отправлена: ' . (Message::first('error') ?: 'без описания'));
            }
        } catch (Exception $e) {
            error_log('[form] Ошибка копии клиенту: ' . $e->getMessage());
        }
    }

    /** Тело письма-копии клиенту (без внутренних деталей заявки). */
    private static function buildCopyBody(array $fields): string
    {
        $mainColor = '#22c5be';
        $font = 'Segoe UI, Arial, sans-serif';
        $greeting = $fields['name'] !== '' ? htmlspecialchars($fields['name']) : 'Клиент';

        $rows = '';
        $labels = [
            'name' => 'Имя',
            'company' => 'Компания',
            'phone' => 'Телефон',
            'email' => 'Email',
            'location' => 'Локация',
            'product' => 'Услуга',
            'message' => 'Сообщение',
        ];
        foreach ($labels as $key => $label) {
            $value = trim((string)($fields[$key] ?? ''));
            if ($value === '') {
                continue;
            }
            $rows .= "<tr><td style='padding:6px 12px 6px 0;color:#6b7280;white-space:nowrap;vertical-align:top'>"
                . htmlspecialchars($label) . ":</td>"
                . "<td style='padding:6px 0;color:#374151'>" . nl2br(htmlspecialchars($value)) . "</td></tr>";
        }

        return "
            <div style='max-width:520px;margin:0 auto;background:#ffffff;border-radius:12px;border:1px solid #e5e7eb;font-family:$font;'>
                <div style='padding:32px 32px 16px 32px;text-align:center;border-bottom:1px solid #e5e7eb;'>
                    <h2 style='color:$mainColor;margin:0;font-size:1.5rem;'>Заявка принята</h2>
                </div>
                <div style='padding:28px 32px 18px 32px;'>
                    <p style='color:#374151;font-size:1.05rem;line-height:1.6;margin:0 0 12px'>Здравствуйте, $greeting!</p>
                    <p style='color:#374151;font-size:1.05rem;line-height:1.6;margin:0 0 20px'>Мы получили вашу заявку и перезвоним в течение 15 минут в рабочее время.</p>
                    <table style='width:100%;border-collapse:collapse;font-size:1rem;'>$rows</table>
                </div>
                <div style='background:#ffffff;border-radius:0 0 12px 12px;padding:18px 32px 24px 32px;text-align:center;border-top:1px solid #e5e7eb;color:#6b7280;font-size:0.95rem;font-family:$font;'>
                    <small>Это автоматическое письмо, не отвечайте на него.</small>
                </div>
            </div>";
    }

    /** Plain-text версия копии клиенту. */
    private static function buildCopyPlainText(array $fields): string
    {
        $labels = [
            'name' => 'Имя',
            'company' => 'Компания',
            'phone' => 'Телефон',
            'email' => 'Email',
            'location' => 'Локация',
            'product' => 'Услуга',
            'message' => 'Сообщение',
        ];
        $lines = [];
        foreach ($labels as $key => $label) {
            $value = trim((string)($fields[$key] ?? ''));
            if ($value !== '') {
                $lines[] = $label . ': ' . $value;
            }
        }
        return "ФСС.РУС — заявка принята\n\nМы получили вашу заявку и перезвоним в течение 15 минут в рабочее время.\n\n"
            . implode("\n", $lines);
    }

    private static function getFormType(): ?string
    {
        if (isset($_POST['mailer'])) return 'modal';
        if (isset($_POST['subscribe'])) return 'subscribe';
        if (isset($_POST['mini-forma'])) return 'mini-forma';
        if (isset($_POST['content_form'])) return 'content_form';
        return null;
    }

    /**
     * IP клиента для rate limit.
     * REMOTE_ADDR — доверенный источник; X-Forwarded-For берём только если
     * сам REMOTE_ADDR приватный (значит, мы за прокси).
     */
    private static function clientIp(): string
    {
        $remote = trim((string)($_SERVER['REMOTE_ADDR'] ?? ''));
        if ($remote !== '' && !self::isPrivateIp($remote)) {
            return $remote;
        }

        $chain = array_reverse(array_filter(array_map('trim', explode(',', (string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '')))));
        foreach ($chain as $candidate) {
            if (filter_var($candidate, FILTER_VALIDATE_IP) && !self::isPrivateIp($candidate)) {
                return $candidate;
            }
        }

        return $remote !== '' ? $remote : 'unknown';
    }

    private static function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    private static function antiBotRateLimit(): bool
    {
        $ip = self::clientIp();
        $dir = sys_get_temp_dir() . '/fss-ratelimit/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        $file = $dir . md5($ip) . '.json';
        $now = time();
        $window = 600;      // окно 10 минут
        $maxRequests = 8;   // максимум 8 заявок в 10 минут с одного IP

        // Блокировка файла: без неё параллельные запросы перетирали историю
        // друг друга и лимит срабатывал через раз.
        $fh = @fopen($file, 'c+');
        if ($fh === false) {
            return true; // не смогли открыть файл — не блокируем отправку
        }
        try {
            if (!flock($fh, LOCK_EX)) {
                return true;
            }
            $raw = stream_get_contents($fh);
            $history = json_decode((string)$raw, true);
            $history = is_array($history) ? array_values(array_filter($history, fn($t) => ($now - (int)$t) < $window)) : [];

            if (count($history) >= $maxRequests) {
                error_log("Anti-bot: rate limit exceeded for IP {$ip} (" . count($history) . " requests/10min)");
                return false;
            }

            $history[] = $now;
            ftruncate($fh, 0);
            rewind($fh);
            fwrite($fh, (string)json_encode($history));
            fflush($fh);
            return true;
        } finally {
            @flock($fh, LOCK_UN);
            @fclose($fh);
        }
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

    /**
     * Проверка CSRF-подписи: HMAC(secret, 'form|{_ts}') === _csrf.
     * Токен подписывается на рендере страницы (public/components/antibot_fields.php),
     * поэтому чужая страница не может сгенерировать валидную пару _ts/_csrf.
     */
    private static function antiBotCsrf(int $ts, string $token): bool
    {
        if ($token === '' || !function_exists('fss_secret')) {
            return false;
        }
        // Токен живёт не дольше суток и не из «будущего» — защита от replay.
        $ageMs = (int)((microtime(true) * 1000) - $ts);
        if ($ageMs < 0 || $ageMs > 86400000) {
            return false;
        }
        $expected = hash_hmac('sha256', 'form|' . $ts, fss_secret());
        return hash_equals($expected, $token);
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
        string $location = '',
        string $heading = 'Новая заявка с сайта ФСС.РУС'
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
                . "<td style='padding:8px 0;color:#374151;background:#f8f4ef;border-radius:6px'>" . nl2br(htmlspecialchars($msg)) . "</td></tr>";
        }
        $rows .= $row('Форма', $type);
        $rows .= $row('Дата', $date);

        return "
            <div style='max-width:520px;margin:0 auto;background:$bgColor;border-radius:12px;box-shadow:0 4px 24px rgba(34,197,94,0.08);border:1px solid $border;font-family:$font;'>
                <div style='background: $bgColor; border-radius: 12px 12px 0 0; padding: 32px 32px 16px 32px; text-align: center; border-bottom: 1px solid $border;'>
                    <img src='$logo' alt='Логотип' style='width: 64px; height: 64px; margin-bottom: 12px;'>
                    <h2 style='color: $mainColor; font-family: $font; margin: 0; font-size: 1.6rem;'>" . htmlspecialchars($heading) . "</h2>
                </div>
                <div style='padding:28px 32px 18px 32px;'>
                    <table style='width:100%;border-collapse:collapse;font-size:1.08rem;'>$rows</table>
                </div>
                <div style='background: $bgColor; border-radius: 0 0 12px 12px; padding: 18px 32px 24px 32px; text-align: center; border-top: 1px solid $border; color: #6b7280; font-size: 0.95rem; font-family:$font;'>
                    <small>Это автоматическое письмо, не отвечайте на него.</small>
                </div>
            </div>";
    }

    /** Plain-text версия письма (для клиентов без HTML-рендера). */
    private static function buildPlainText(
        string $name,
        string $phone,
        string $email,
        string $msg,
        string $type,
        string $company = '',
        string $location = ''
    ): string {
        $lines = [];
        $push = function (string $label, string $value) use (&$lines): void {
            if ($value === '') return;
            $lines[] = $label . ': ' . $value;
        };

        $push('Имя', $name);
        $push('Компания', $company);
        $push('Телефон', $phone);
        $push('Email', $email);
        $push('Локация', $location);
        $push('Сообщение', $msg);
        $push('Форма', $type);
        $push('Дата', date('d.m.Y H:i'));

        return "Новая заявка с сайта ФСС.РУС\n\n" . implode("\n", $lines);
    }
}
