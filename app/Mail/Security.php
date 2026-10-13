<?php
/**
 * Security & Validation helper
 */
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

    public static function showAlert($type, $message) {
        // type: success|error
        $color = $type === 'success' ? 'green' : 'red';
        $bg = $type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
        $id = $type === 'success' ? 'success-alert' : 'error-alert';
        $icon = $type === 'success' ? '✅' : '❌';
        $border = $type === 'success' ? '#166534' : '#b91c1c';
        echo '<div id="'.$id.'" class="p-4 mb-4 rounded-lg flex items-center shadow-lg '.$bg.'"
                style="position:fixed; top:20px; right:20px; z-index:9999; min-width:320px; max-width:90vw; box-shadow:0 4px 16px rgba(0,0,0,0.10);padding-right: 2.5rem" role="alert">
                <span class="font-semibold mr-4">'.$icon.' '.self::esc($message).'</span>
                <button type="button" onclick="document.getElementById(\''.$id.'\').style.display=\'none\'"
                    style="position:absolute; top:13px; right:12px; background:none; border:none; font-size:1.5rem; color:'.$border.'; cursor:pointer; line-height:1;">&times;</button>
            </div>';
        // Авто скрытие через 6 сек
        echo '<script>setTimeout(()=>{var e=document.getElementById("'.$id.'");if(e)e.style.display="none";},6000);</script>';
    }
}
