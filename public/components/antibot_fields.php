<?php
// Универсальные антибот поля - подключать внутри каждого <form>
// Генерируем поля напрямую без зависимости от удалённого класса
$timeMs = (int) (microtime(true) * 1000);
$honeypot = 'fax_number';
$hpId = $honeypot . '_' . substr(md5((string)$timeMs), 0, 8);
$tokenId = 'js_token_' . substr(md5((string)$timeMs), 0, 8);

echo '<div class="fss-hp" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">';
echo '<label for="' . $hpId . '">Не заполняйте это поле</label>';
echo '<input type="text" name="' . $honeypot . '" id="' . $hpId . '" value="" tabindex="-1" autocomplete="off">';
echo '</div>';
echo '<input type="hidden" name="_ts" value="' . $timeMs . '">';
echo '<input type="hidden" name="_js_token" id="' . $tokenId . '" value="">';

echo '<script>(function(){var id=' . json_encode($tokenId, JSON_UNESCAPED_SLASHES) . ';function fill(){var e=document.getElementById(id);if(e&&!e.value)e.value=Date.now().toString();}if(document.readyState==="loading")document.addEventListener("DOMContentLoaded",fill);else fill();setTimeout(fill,1500);})();</script>';