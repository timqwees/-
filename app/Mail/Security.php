<?php
namespace App\Mail;

class Security {

    public static function sanitizeString($str, $maxLen = 500) {
        $str = trim((string)$str);
        $str = strip_tags($str);
        // Remove null bytes
        $str = str_replace("\0", "", $str);
        // Limit length
        if (mb_strlen($str) > $maxLen) $str = mb_substr($str, 0, $maxLen);
        return $str;
    }

    public static function validateName($name, &$err) {
        $name = self::sanitizeString($name, 100);
        if (mb_strlen($name) < 2) { $err = 'Имя слишком короткое (минимум 2 символа).'; return false; }
        if (mb_strlen($name) > 100) { $err = 'Имя слишком длинное.'; return false; }
        // Разрешаем буквы, пробел, дефис, апостроф, точку (рус/англ)
        if (!preg_match('/^[\p{L}\s\-\.\'’`]+$/u', $name)) { $err = 'Имя содержит недопустимые символы.'; return false; }
        if (preg_match('/https?:|www\./i', $name)) { $err = 'Имя не должно содержать ссылки.'; return false; }
        return $name;
    }

    public static function validatePhone($phone, &$err) {
        $phone = trim((string)$phone);
        // Очищаем кроме + и цифр
        $clean = preg_replace('/[^\d\+]/', '', $phone);
        // Российcкий формат
        // Допускаем +7XXXXXXXXXX, 8XXXXXXXXXX, 7XXXXXXXXXX
        $digits = preg_replace('/\D/', '', $clean);
        if (strlen($digits) < 10 || strlen($digits) > 12) { $err = 'Укажите корректный телефон (10-12 цифр).'; return false; }
        // Если начинается с 8 -> 7
        if (strlen($digits) == 11 && $digits[0] == '8') $digits = '7'.substr($digits,1);
        // Форматируем
        $formatted = '+' . $digits;
        // Проверка на повторяющиеся цифры 7777777
        if (preg_match('/(\d)\1{6,}/', $digits)) { $err = 'Некорректный номер телефона.'; return false; }
        return $formatted;
    }

    public static function validateEmail($email, &$err, $required = true) {
        $email = trim((string)$email);
        if (empty($email)) {
            if ($required) { $err = 'Email обязателен.'; return false; }
            return '';
        }
        if (strlen($email) > 254) { $err = 'Email слишком длинный.'; return false; }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $err = 'Некорректный email.'; return false; }
        if (preg_match('/https?:|www\./i', $email)) { $err = 'Email не должен содержать ссылки.'; return false; }
        return strtolower($email);
    }

    public static function validateMessage($msg, &$err, $required = true, $min = 10) {
        $msg = self::sanitizeString($msg, 2000);
        if (empty($msg)) {
            if ($required) { $err = 'Сообщение обязательно.'; return false; }
            return '';
        }
        if (mb_strlen($msg) < $min) { $err = "Сообщение слишком короткое (минимум $min символов)."; return false; }
        return $msg;
    }

    public static function esc($str) {
        return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** Очередь уведомлений: выводим их в футере, чтобы не ломать DOCTYPE. */
    private static array $alerts = [];
    private static array $scripts = [];

    public static function showAlert($type, $message) {
        $key = $type . '|' . $message;
        self::$alerts[$key] = ['type' => $type, 'message' => $message];
    }

    public static function pushScript(string $js): void {
        self::$scripts[] = $js;
    }

    public static function renderAlerts(): void {
        if (!self::$alerts && !self::$scripts) return;
        foreach (self::$alerts as $a) {
            self::renderAlert($a['type'], $a['message']);
        }
        self::$alerts = [];
        foreach (self::$scripts as $js) {
            echo '<script>' . $js . '</script>';
        }
        self::$scripts = [];
    }

    private static function renderAlert($type, $message): void
    {
        $isSuccess = $type === 'success';
        $bg = $isSuccess ? '#ecfdf5' : '#fef2f2';
        $border = $isSuccess ? '#10b981' : '#ef4444';
        $text = $isSuccess ? '#065f46' : '#991b1b';
        $iconBg = $isSuccess ? '#10b981' : '#ef4444';
        $icon = $isSuccess
            ? '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9.5" stroke="currentColor" stroke-width="1.5"/><path d="M6 10l2.5 2.5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
            : '<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="9.5" stroke="currentColor" stroke-width="1.5"/><path d="M7 7l6 6M13 7l-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
        $id = $isSuccess ? 'success-alert' : 'error-alert';
        $closeColor = $isSuccess ? '#059669' : '#dc2626';

        echo '<div id="' . $id . '" role="alert" aria-live="polite" style="
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 420px;
            background: ' . $bg . ';
            border: 1px solid ' . $border . ';
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.12), 0 4px 8px rgba(0,0,0,0.06);
            padding: 16px 48px 16px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: fss-alert-in 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Manrope, sans-serif;
        ">
            <div style="flex-shrink:0; width:36px; height:36px; border-radius:10px; background:' . $iconBg . '; display:flex; align-items:center; justify-content:center; color:white; margin-top:1px;">' . $icon . '</div>
            <div style="flex:1; min-width:0;">
                <div style="font-weight:600; font-size:14px; color:' . $text . '; line-height:1.4;">' . self::esc($message) . '</div>
            </div>
            <button type="button" onclick="document.getElementById(\'' . $id . '\').style.display=\'none\'" aria-label="Закрыть" style="
                flex-shrink:0;
                width:28px;
                height:28px;
                border-radius:8px;
                background:transparent;
                border:none;
                color:' . $closeColor . ';
                cursor:pointer;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:18px;
                line-height:1;
                margin-top:-4px;
                margin-right:-8px;
                transition:background 0.15s;
            " onmouseover="this.style.background=\'rgba(0,0,0,0.06)\'" onmouseout="this.style.background=\'transparent\'">&times;</button>
        </div>
        <style>
            @keyframes fss-alert-in {
                from { opacity: 0; transform: translateX(40px) scale(0.96); }
                to { opacity: 1; transform: translateX(0) scale(1); }
            }
            @media (prefers-reduced-motion: reduce) {
                @keyframes fss-alert-in { from { opacity: 0; } to { opacity: 1; } }
            }
        </style>
        <script>
            setTimeout(function(){
                var e = document.getElementById("' . $id . '");
                if (e) {
                    e.style.transition = "opacity 0.25s ease, transform 0.25s ease";
                    e.style.opacity = "0";
                    e.style.transform = "translateX(20px) scale(0.98)";
                    setTimeout(function(){ e.style.display = "none"; }, 250);
                }
            }, 7000);
        </script>';
    }
}
