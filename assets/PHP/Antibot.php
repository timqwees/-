<?php
/**
 * Antibot System - FSS
 * Многоуровневая защита от ботов и спама:
 * - Honeypot (скрытое поле)
 * - Time-trap (минимальное/максимальное время заполнения)
 * - JS-token (заполняется JS через 1 сек)
 * - Rate limiting по IP
 * - Контент-фильтр (ссылки, спам-слова, реклама)
 * - CSRF токен (опционально)
 * - Проверка на повторяющиеся отправки
 */
require_once __DIR__ . '/config.php';

class Antibot {
    // Спам-словари - русский + англ реклама
    private static $spamPatterns = [
        '/https?:\/\//i',
        '/www\./i',
        '/\[url/i',
        '/<a\s+href/i',
        '/viagra|cialis|casino|poker|loan|credit|payday|xxx|porn|sex|escort/i',
        '/казино|ставки|букмекер|займ|кредит|порно|проститутк|интим/i',
        '/seo\s*продвижение|раскрутка\s*сайт|продвижение\s*сайт/i',
        '/предлагаем\s*услуги|предлагаю\s*реклам|разместим\s*реклам/i',
        '/bitcoin|crypto|инвестиц.*доход|заработок\s*в\s*интернет/i',
    ];

    // Рекламные фразы
    private static $adPhrases = [
        'предлагаем', 'рекламу', 'продвижение', 'раскрутка', 'сео', 'seo',
        'заработок', 'инвестиции', 'криптовалюта', 'casino'
    ];

    public static function generateFields(): string {
        // Генерируем токены для формы
        if (session_status() === PHP_SESSION_NONE) session_start();
        $time = time();
        $csrf = bin2hex(random_bytes(16));
        $_SESSION['antibot_csrf'] = $csrf;
        $_SESSION['antibot_time'] = $time; // fallback

        $honeypot = ANTIBOT_HONEYPOT_FIELD;
        // HTML полей - honeypot скрыт стилем, время и csrf, js-token
        $html = '';
        $html .= '<div style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">';
        $html .= '<label for="'.$honeypot.'">Не заполняйте это поле</label>';
        $html .= '<input type="text" name="'.$honeypot.'" id="'.$honeypot.'" value="" tabindex="-1" autocomplete="off">';
        $html .= '</div>';
        $html .= '<input type="hidden" name="render_time" value="'.$time.'">';
        $html .= '<input type="hidden" name="csrf_token" value="'.$csrf.'">';
        $html .= '<input type="hidden" name="js_token" id="js_token_'.substr($csrf,0,6).'" value="">';
        // Инлайн JS для заполнения js_token через 1 сек + time trap proof
        $html .= '<script>(function(){var t=document.currentScript.previousElementSibling;setTimeout(function(){var e=document.querySelector(\'input[name="js_token"]\');if(e)e.value="js_"+Date.now()+"_'.substr($csrf,0,8).'";},1100);})();</script>';
        // Дополнительный JS: запрет автозаполнения honeypot
        $html .= '<script>document.addEventListener("DOMContentLoaded",function(){var h=document.getElementById("'.$honeypot.'");if(h)h.value="";});</script>';
        return $html;
    }

    public static function validate(array $data, &$error = null): bool {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Honeypot
        $hp = ANTIBOT_HONEYPOT_FIELD;
        if (!empty($data[$hp])) {
            $error = 'Обнаружена автоматическая отправка (honeypot).';
            self::logAttempt('honeypot', $data);
            return false;
        }
        // Доп. проверка: если бот заполнил любые поля с похожими именами
        foreach (['website','url','link'] as $trap) {
            if (!empty($data[$trap])) {
                $error = 'Spam detected.';
                self::logAttempt('honeypot_alt_'.$trap, $data);
                return false;
            }
        }

        // 2. Time trap
        $renderTime = isset($data['render_time']) ? (int)$data['render_time'] : 0;
        if ($renderTime === 0) {
            $error = 'Некорректная форма (время). Обновите страницу.';
            self::logAttempt('missing_render_time', $data);
            return false;
        }
        $elapsed = time() - $renderTime;
        if ($elapsed < ANTIBOT_MIN_TIME) {
            $error = 'Форма заполнена слишком быстро. Попробуйте ещё раз через несколько секунд.';
            self::logAttempt('too_fast_'.$elapsed, $data);
            return false;
        }
        if ($elapsed > ANTIBOT_MAX_TIME) {
            $error = 'Время действия формы истекло. Обновите страницу.';
            self::logAttempt('expired_'.$elapsed, $data);
            return false;
        }

        // 3. JS token (бот без JS не заполнит)
        $jsToken = $data['js_token'] ?? '';
        if (empty($jsToken) || strpos($jsToken, 'js_') !== 0) {
            // Мягкая проверка: если нет js_token но есть JS disabled - разрешаем если остальные проверки ок, но логируем
            // Для строгости - блокируем, но с возможностью настройки
            // Сейчас: требуем js_token, иначе считаем ботом
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            // Разрешаем если это может быть No-JS браузер? Но для anti-spam ботов обычно JS нет.
            // Делаем проверку строгой:
            $error = 'Проверка браузера не пройдена. Включите JavaScript и обновите страницу.';
            self::logAttempt('missing_js_token', $data);
            return false;
        }
        // Проверка формата js_token
        if (!preg_match('/^js_\d+_[a-f0-9]{8}$/', $jsToken)) {
            $error = 'Некорректный токен браузера.';
            self::logAttempt('invalid_js_token', $data);
            return false;
        }

        // 4. CSRF
        $csrf = $data['csrf_token'] ?? '';
        if (empty($_SESSION['antibot_csrf']) || $csrf !== $_SESSION['antibot_csrf']) {
            // Мягкая: если сессии нет, пропускаем? Но лучше блокировать
            // Для UX: если csrf не совпал, но это повторная отправка после успешной - тоже блок
            // Разрешим только если сессия отсутствует из-за кеша? Логируем но не блокируем жёстко?
            // Сейчас: строго
            $error = 'Ошибка безопасности формы (CSRF). Обновите страницу.';
            self::logAttempt('csrf_mismatch', $data);
            return false;
        }

        // 5. Rate limiting по IP
        $ip = self::getIP();
        if (!self::checkRateLimit($ip)) {
            $error = 'Слишком много заявок с вашего IP. Попробуйте через 10 минут.';
            self::logAttempt('rate_limit_'.$ip, $data);
            return false;
        }

        // 6. Контент-фильтр: проверяем все текстовые поля на спам
        $combined = implode(' ', [
            $data['name'] ?? '',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['message'] ?? ''
        ]);
        foreach (self::$spamPatterns as $pattern) {
            if (preg_match($pattern, $combined)) {
                $error = 'Сообщение содержит запрещённый контент (ссылки/реклама).';
                self::logAttempt('spam_pattern_'.$pattern, $data);
                return false;
            }
        }
        // Доп проверка: если в имени или телефоне есть http
        if (preg_match('/https?:|www\.|\.ru\/|\.com/i', $data['name'] ?? '')) {
            $error = 'Некорректное имя.';
            self::logAttempt('spam_name_link', $data);
            return false;
        }
        if (preg_match('/https?:|www\./i', $data['phone'] ?? '')) {
            $error = 'Некорректный телефон.';
            self::logAttempt('spam_phone_link', $data);
            return false;
        }
        // Проверка на рекламные фразы в сообщении
        $msgLower = mb_strtolower($data['message'] ?? '', 'UTF-8');
        $adCount = 0;
        foreach (self::$adPhrases as $phrase) {
            if (mb_strpos($msgLower, $phrase) !== false) $adCount++;
        }
        if ($adCount >= 2) {
            $error = 'Рекламные сообщения не принимаются.';
            self::logAttempt('ad_phrases', $data);
            return false;
        }
        // Проверка на слишком длинное сообщение (спам часто длинный)
        if (mb_strlen($combined) > 2000) {
            $error = 'Сообщение слишком длинное.';
            self::logAttempt('too_long', $data);
            return false;
        }
        // Проверка на повторяющиеся символы (спам)
        if (preg_match('/(.)\1{7,}/u', $combined)) {
            $error = 'Сообщение содержит некорректные символы.';
            self::logAttempt('repeated_chars', $data);
            return false;
        }

        // 7. Проверка на пустой User-Agent или подозрительный
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($ua) || preg_match('/curl|wget|python|bot|crawler|spider|scrapy|httpclient/i', $ua)) {
            // Разрешаем яндекс/гугл бота? Но для POST - это точно не легитимный
            if (!empty($data['name'])) {
                $error = 'Подозрительный браузер.';
                self::logAttempt('bad_ua_'.$ua, $data);
                return false;
            }
        }

        // 8. Duplicate submission check (хеш последней заявки в сессии)
        $hash = md5(json_encode([$data['name'] ?? '', $data['phone'] ?? '', $data['message'] ?? '']));
        if (!empty($_SESSION['last_submit_hash']) && $_SESSION['last_submit_hash'] === $hash) {
            // Если прошло меньше 60 сек с последней идентичной заявки
            $lastTime = $_SESSION['last_submit_time'] ?? 0;
            if (time() - $lastTime < 60) {
                $error = 'Вы уже отправили такую заявку. Подождите минуту.';
                self::logAttempt('duplicate', $data);
                return false;
            }
        }
        $_SESSION['last_submit_hash'] = $hash;
        $_SESSION['last_submit_time'] = time();

        // Успешно - записываем rate limit
        self::recordRateLimit($ip);
        // Инвалидируем CSRF после успешной проверки чтобы нельзя было реплеить
        unset($_SESSION['antibot_csrf']);
        return true;
    }

    private static function getIP(): string {
        $keys = ['HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR'];
        foreach ($keys as $k) {
            if (!empty($_SERVER[$k])) {
                $ip = trim(explode(',', $_SERVER[$k])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    private static function checkRateLimit(string $ip): bool {
        $file = ANTIBOT_STORAGE_DIR . '/' . md5($ip) . '.json';
        if (!file_exists($file)) return true;
        $data = json_decode(@file_get_contents($file), true);
        if (!$data) return true;
        $now = time();
        // Очищаем старые записи вне окна
        $data['timestamps'] = array_filter($data['timestamps'] ?? [], fn($t) => ($now - $t) < ANTIBOT_RATE_LIMIT_WINDOW);
        if (count($data['timestamps']) >= ANTIBOT_RATE_LIMIT_COUNT) {
            return false;
        }
        return true;
    }

    private static function recordRateLimit(string $ip): void {
        $file = ANTIBOT_STORAGE_DIR . '/' . md5($ip) . '.json';
        $data = ['timestamps' => []];
        if (file_exists($file)) {
            $data = json_decode(@file_get_contents($file), true) ?: $data;
        }
        $data['timestamps'][] = time();
        // Очищаем старые
        $now = time();
        $data['timestamps'] = array_values(array_filter($data['timestamps'], fn($t) => ($now - $t) < ANTIBOT_RATE_LIMIT_WINDOW));
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }

    private static function logAttempt(string $reason, array $data): void {
        $ip = self::getIP();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '-';
        $logFile = ANTIBOT_STORAGE_DIR . '/antibot.log';
        $entry = date('Y-m-d H:i:s') . " | IP:$ip | Reason:$reason | UA:$ua | Data:" . json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . PHP_EOL;
        @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }
}
