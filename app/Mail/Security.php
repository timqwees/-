<?php

namespace App\Mail;

class Security
{
    public static function sanitizeString(string $str, int $maxLen = 500): string
    {
        $str = trim($str);
        $str = strip_tags($str);
        $str = str_replace("\0", '', $str);
        return mb_substr($str, 0, $maxLen, 'UTF-8');
    }

    public static function validateName(string $name, ?string &$error = null): string|false
    {
        $name = self::sanitizeString($name, 100);
        if (mb_strlen($name, 'UTF-8') < 2) {
            $error = 'Имя слишком короткое (минимум 2 символа).';
            return false;
        }
        if (!preg_match('/^[\p{L}\s\-\.\']+$/u', $name)) {
            $error = 'Имя содержит недопустимые символы.';
            return false;
        }
        return $name;
    }

    public static function validatePhone(string $phone, ?string &$error = null): string|false
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) < 10 || strlen($digits) > 15) {
            $error = 'Укажите корректный телефон (10-15 цифр).';
            return false;
        }
        if (preg_match('/^(\d)\1+$/', $digits)) {
            $error = 'Некорректный номер телефона.';
            return false;
        }
        return $digits;
    }

    public static function validateEmail(string $email, ?string &$error = null, bool $required = true): string|false
    {
        $email = trim($email);
        if ($email === '') {
            if ($required) {
                $error = 'Email обязателен.';
                return false;
            }
            return '';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Некорректный email.';
            return false;
        }
        if (strlen($email) > 254) {
            $error = 'Email слишком длинный.';
            return false;
        }
        return strtolower($email);
    }

    public static function validateMessage(string $message, ?string &$error = null, bool $required = true, int $minLen = 10): string|false
    {
        $message = trim($message);
        if ($message === '') {
            if ($required) {
                $error = 'Сообщение обязательно.';
                return false;
            }
            return '';
        }
        if (mb_strlen($message, 'UTF-8') < $minLen) {
            $error = "Сообщение слишком короткое (минимум $minLen символов).";
            return false;
        }
        return self::sanitizeString($message, 5000);
    }

    private static array $scripts = [];

    public static function pushScript(string $script): void
    {
        self::$scripts[] = $script;
    }

    public static function printScripts(): void
    {
        if (empty(self::$scripts)) return;
        echo '<script>' . "\n";
        foreach (self::$scripts as $s) {
            echo $s . "\n";
        }
        echo '</script>' . "\n";
    }

    public static function showAlert(string $type, string $message): void
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
                <div style="font-weight:600; font-size:14px; color:' . $text . '; line-height:1.4;">' . self::sanitizeString($message) . '</div>
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