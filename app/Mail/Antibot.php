<?php
namespace App\Mail;

require_once __DIR__ . '/config.php';

class Antibot
{
    private static bool $jsEmitted = false;

    public static function generateFields(): string
    {
        $timeMs = (int) (microtime(true) * 1000);
        $honeypot = ANTIBOT_HONEYPOT_FIELD;
        $hpId = $honeypot . '_' . substr(md5((string)$timeMs), 0, 8);
        $tokenId = 'js_token_' . substr(md5((string)$timeMs), 0, 8);

        $html = '';
        $html .= '<div class="fss-hp" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">';
        $html .= '<label for="' . $hpId . '">Не заполняйте это поле</label>';
        $html .= '<input type="text" name="' . $honeypot . '" id="' . $hpId . '" value="" tabindex="-1" autocomplete="off">';
        $html .= '</div>';
        $html .= '<input type="hidden" name="_ts" value="' . $timeMs . '">';
        $html .= '<input type="hidden" name="_js_token" id="' . $tokenId . '" value="">';

        $html .= '<script>(function(){var id=' . json_encode($tokenId, JSON_UNESCAPED_SLASHES) . ';function fill(){var e=document.getElementById(id);if(e&&!e.value)e.value=Date.now().toString();}if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",fill);else fill();setTimeout(fill,1500);})();</script>';

        if (!self::$jsEmitted) {
            self::$jsEmitted = true;
            $html .= self::initScript();
        }
        return $html;
    }

    private static function initScript(): string
    {
        return <<<'HTML'
<script>
(function(){
  function ready(fn){ if(document.readyState!=="loading"){fn();}else{document.addEventListener("DOMContentLoaded",fn);} }
  ready(function(){
    var forms=document.querySelectorAll("form");
    Array.prototype.forEach.call(forms,function(form){
      if(form.getAttribute("data-fss-ab")==="1")return;
      var ts=form.querySelector('input[name="_ts"]');
      var jt=form.querySelector('input[name="_js_token"]');
      if(!ts||!jt)return;
      form.setAttribute("data-fss-ab","1");
      var hp=form.querySelector(".fss-hp input"); if(hp)hp.value="";
      var btn=form.querySelector('button[type="submit"],input[type="submit"]');
      if(!btn)return;
      var isInput=btn.tagName==="INPUT";
      var orig=isInput?btn.value:btn.innerHTML;
      var min=3,iv=null;
      function setText(t){ if(isInput){btn.value=t;}else{btn.innerHTML=t;} }
      function restore(){ if(iv){clearInterval(iv);iv=null;} btn.disabled=false; btn.style.opacity=""; setText(orig); }
      function tick(){
        var start=parseInt(ts.value,10)||Date.now();
        var elapsed=Math.floor((Date.now()-start)/1000);
        if(elapsed>=min){restore();return;}
        btn.disabled=true; btn.style.opacity="0.6";
        setText("Подождите "+(min-elapsed)+"с...");
      }
      tick();
      iv=setInterval(tick,300);
      setTimeout(restore,5000);
      form.addEventListener("submit",function(){
        if(btn.disabled)return;
        btn.disabled=true; btn.style.opacity="0.7"; setText("Отправляем...");
        setTimeout(function(){btn.disabled=false;btn.style.opacity="";setText(orig);},15000);
      });
    });
  });
})();
</script>
HTML;
    }

    public static function validate(array $data, &$error = null): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                @session_start();
            } else {
                @session_start();
                if (session_status() === PHP_SESSION_NONE && !isset($_SESSION)) {
                    $_SESSION = [];
                }
            }
        }

        $hp = ANTIBOT_HONEYPOT_FIELD;
        if (!empty($data[$hp])) {
            $error = 'Обнаружена автоматическая отправка.';
            self::logAttempt('honeypot', $data);
            return false;
        }
        foreach (['website','url','link'] as $trap) {
            if (!empty($data[$trap])) {
                $error = 'Spam detected.';
                self::logAttempt('honeypot_alt_'.$trap, $data);
                return false;
            }
        }

        $ts = isset($data['_ts']) ? (int)$data['_ts'] : 0;
        $jsToken = (string)($data['_js_token'] ?? '');

        if ($ts > 0) {
            $elapsedSec = (int) ((microtime(true) * 1000 - $ts) / 1000);
            if ($elapsedSec < -300) $elapsedSec = 0;
            if ($elapsedSec < ANTIBOT_MIN_TIME) {
                $error = 'Форма заполнена слишком быстро. Попробуйте ещё раз через несколько секунд.';
                self::logAttempt('too_fast_'.$elapsedSec, $data);
                return false;
            }
            if ($elapsedSec > ANTIBOT_MAX_TIME) {
                self::logAttempt('expired_'.$elapsedSec, $data);
            }
        } elseif ($jsToken === '' || !ctype_digit($jsToken) || strlen($jsToken) < 10 || strlen($jsToken) > 15) {
            $error = 'Некорректная форма (время). Обновите страницу.';
            self::logAttempt('missing_ts', $data);
            return false;
        } else {
            self::logAttempt('missing_ts_soft', $data);
        }

        if ($jsToken === '' || !ctype_digit($jsToken) || strlen($jsToken) < 10 || strlen($jsToken) > 15) {
            $error = 'Проверка браузера не пройдена. Включите JavaScript и обновите страницу.';
            self::logAttempt('missing_js_token', $data);
            return false;
        }

        $ip = self::getIP();
        if (!self::checkRateLimit($ip)) {
            $error = 'Слишком много заявок с вашего IP. Попробуйте через 10 минут.';
            self::logAttempt('rate_limit_'.$ip, $data);
            return false;
        }

        $combined = implode(' ', [
            $data['name'] ?? '',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['message'] ?? ''
        ]);
        if (preg_match('/https?:|www\./i', $combined)) {
            $error = 'Сообщение содержит запрещённый контент (ссылки/реклама).';
            self::logAttempt('spam_links', $data);
            return false;
        }

        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (empty($ua) || preg_match('/curl|wget|python|bot|crawler|spider|scrapy|httpclient/i', $ua)) {
            if (!empty($data['name'])) {
                $error = 'Подозрительный браузер.';
                self::logAttempt('bad_ua', $data);
                return false;
            }
        }

        $hash = md5(json_encode([$data['name'] ?? '', $data['phone'] ?? '', $data['message'] ?? '']));
        if (!empty($_SESSION['last_submit_hash']) && $_SESSION['last_submit_hash'] === $hash) {
            $lastTime = $_SESSION['last_submit_time'] ?? 0;
            if (time() - $lastTime < 60) {
                $error = 'Вы уже отправили такую заявку. Подождите минуту.';
                self::logAttempt('duplicate', $data);
                return false;
            }
        }
        $_SESSION['last_submit_hash'] = $hash;
        $_SESSION['last_submit_time'] = time();

        self::recordRateLimit($ip);
        return true;
    }

    public static function validatePhone(string $phone, &$error = null): bool
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
        if ($digits === '1234567890' || $digits === '0123456789') {
            $error = 'Некорректный номер телефона.';
            return false;
        }
        return true;
    }

    private static function getIP(): string
    {
        $keys = ['HTTP_X_FORWARDED_FOR','HTTP_CLIENT_IP','REMOTE_ADDR'];
        foreach ($keys as $k) {
            if (!empty($_SERVER[$k])) {
                $ip = trim(explode(',', $_SERVER[$k])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    private static function checkRateLimit(string $ip): bool
    {
        $file = ANTIBOT_STORAGE_DIR . '/' . md5($ip) . '.json';
        if (!file_exists($file)) return true;
        $data = json_decode(@file_get_contents($file), true);
        if (!$data) return true;
        $now = time();
        $data['timestamps'] = array_filter($data['timestamps'] ?? [], fn($t) => ($now - $t) < ANTIBOT_RATE_LIMIT_WINDOW);
        if (count($data['timestamps']) >= ANTIBOT_RATE_LIMIT_COUNT) {
            return false;
        }
        return true;
    }

    private static function recordRateLimit(string $ip): void
    {
        $file = ANTIBOT_STORAGE_DIR . '/' . md5($ip) . '.json';
        $data = ['timestamps' => []];
        if (file_exists($file)) {
            $data = json_decode(@file_get_contents($file), true) ?: $data;
        }
        $data['timestamps'][] = time();
        $now = time();
        $data['timestamps'] = array_values(array_filter($data['timestamps'], fn($t) => ($now - $t) < ANTIBOT_RATE_LIMIT_WINDOW));
        @file_put_contents($file, json_encode($data), LOCK_EX);
    }

    private static function logAttempt(string $reason, array $data): void
    {
        $ip = self::getIP();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '-';
        $logFile = ANTIBOT_STORAGE_DIR . '/antibot.log';
        $entry = date('Y-m-d H:i:s') . " | IP:$ip | Reason:$reason | UA:$ua | Data:" . json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . PHP_EOL;
        @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }
}