<?php
// Универсальные антибот поля - подключать внутри каждого <form>
require_once dirname(__DIR__, 2) . '/app/Mail/Antibot.php';
echo Antibot::generateFields();
?>
