<?php
// Универсальные антибот поля - подключать внутри каждого <form>
// Генерируем поля напрямую без зависимости от удалённого класса
$timeMs = (int) (microtime(true) * 1000);
$honeypot = 'fax_number';
// id должны быть уникальными внутри страницы: страница может включать этот файл
// дважды (форма на странице + виджет в футере) — одинаковые id ломали заполнение
// токена (document.getElementById попадал в первый input, второй оставался пустым).
static $fssAbSeq = 0;
$fssAbSeq++;
// Глобальный счётчик: файл подключается в разных областях видимости, где static
// сбрасывается, — при совпадении миллисекунд id получались одинаковыми (дубли id в HTML).
$GLOBALS['fss_ab_seq'] = (int)($GLOBALS['fss_ab_seq'] ?? 0) + 1;
$uniq = substr(md5((string)$timeMs . '|' . $fssAbSeq . '|' . $GLOBALS['fss_ab_seq'] . '|' . getmypid() . '|' . mt_rand()), 0, 10);
$hpId = $honeypot . '_' . $uniq;
$tokenId = 'js_token_' . $uniq;
$csrfId = 'csrf_token_' . $uniq;

echo '<div class="fss-hp" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">';
echo '<label for="' . $hpId . '">Не заполняйте это поле</label>';
echo '<input type="text" name="' . $honeypot . '" id="' . $hpId . '" value="" tabindex="-1" autocomplete="off">';
echo '</div>';
echo '<input type="hidden" name="_ts" value="' . $timeMs . '">';
echo '<input type="hidden" name="_js_token" id="' . $tokenId . '" value="">';
// CSRF-токен: подпись времени отправки секретом сервера (сессия не нужна).
// Без него любой сторонний сайт мог бы отправить заявку от имени пользователя.
$__csrf = hash_hmac('sha256', 'form|' . $timeMs, fss_secret());
echo '<input type="hidden" name="_csrf" id="' . $csrfId . '" value="' . htmlspecialchars($__csrf, ENT_QUOTES) . '">';

echo '<script>(function(){var id=' . json_encode($tokenId, JSON_UNESCAPED_SLASHES) . ';function fill(){var e=document.getElementById(id);if(e&&!e.value)e.value=Date.now().toString();}if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",fill);else fill();setTimeout(fill,1500);})();</script>';