<?php

namespace App\Mail;

class Security
{
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
        // Палитра — по дизайн-системе сайта (тёмное стекло, paper/ink, акцент).
        // Ошибка — штатный «красный» системы (#e08a8a), как в формах; без оранжевых оттенков.
        $iconBg = $isSuccess ? 'background:#c7ab94; color:#171310;' : 'background:rgba(224,138,138,.16); color:#e08a8a; border:1px solid rgba(224,138,138,.4);';
        $icon = $isSuccess
            ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>'
            : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5"></path><path d="M12 16.5h.01"></path><circle cx="12" cy="12" r="9"></circle></svg>';
        $id = $isSuccess ? 'success-alert' : 'error-alert';

        echo '<div id="' . $id . '" role="alert" aria-live="polite" style="
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            max-width: 420px;
            background: rgba(15, 15, 17, .94);
            -webkit-backdrop-filter: blur(28px);
            backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 8px;
            box-shadow: 0 18px 48px rgba(0, 0, 0, .42);
            padding: 18px 18px 18px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            animation: fss-alert-in 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            font-family: DM Sans Variable, Wix Madefor Display, Manrope, system-ui, -apple-system, sans-serif;
            color: #f0efed;
        ">
            <div style="flex-shrink:0; width:36px; height:36px; border-radius:6px; display:flex; align-items:center; justify-content:center; margin-top:1px; ' . $iconBg . '">' . $icon . '</div>
            <div style="flex:1; min-width:0;">
                <div style="font-weight:500; font-size:13px; color:#f0efed; line-height:1.5;">' . self::sanitizeString($message) . '</div>
            </div>
            <button type="button" onclick="document.getElementById(\'' . $id . '\').style.display=\'none\'" aria-label="Закрыть" style="
                flex-shrink:0;
                width:28px;
                height:28px;
                border-radius:50%;
                background:transparent;
                border:1px solid rgba(255,255,255,.16);
                color:#a6a09a;
                cursor:pointer;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:18px;
                line-height:1;
                margin-top:-2px;
                margin-right:-4px;
                transition:background 0.2s, color 0.2s, border-color 0.2s;
            " onmouseover="this.style.background=\'rgba(255,255,255,.08)\'; this.style.color=\'#f0efed\'; this.style.borderColor=\'rgba(255,255,255,.34)\'" onmouseout="this.style.background=\'transparent\'; this.style.color=\'#a6a09a\'; this.style.borderColor=\'rgba(255,255,255,.16)\'">&times;</button>
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

    public static function sanitizeString(string $str, int $maxLen = 500): string
    {
        $str = trim($str);
        $str = strip_tags($str);
        $str = str_replace("\0", '', $str);
        return mb_substr($str, 0, $maxLen, 'UTF-8');
    }
}