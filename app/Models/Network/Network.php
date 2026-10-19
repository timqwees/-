<?php

namespace App\Models\Network;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Network
{
    public static $db;

    public static array $patterns = [
        'GET' => [],
        'POST' => []
    ];

    public function __construct()
    {
        self::$db = \App\Config\Database::getConnection();
    }

    public static function onTableAllExists()
    {
        $schemaFile = \App\Config\Database::$schema_name;
        if (!file_exists($schemaFile)) {
            Message::set('error', "Файл схемы не найден: $schemaFile");
            return;
        }
        $schema = file_get_contents($schemaFile);
        if ($schema === false || trim($schema) === '') {
            Message::set('error', "Схема пуста или не может быть прочитана: $schemaFile");
            return;
        }

        $tables = [];
        if (preg_match_all('/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+([`"]?)([a-zA-Z0-9_]+)\1/i', $schema, $matches)) {
            $tables = $matches[2];

            foreach ($tables as $table) {
                if (!self::onTableExists($table)) {
                    try {
                        $pattern = '/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+[`"]?' . preg_quote($table, '/') . '[`"]?\s*\((.*?)\);/is';
                        if (preg_match($pattern, $schema, $tableMatch)) {
                            $createSql = "CREATE TABLE IF NOT EXISTS `$table` (" . trim($tableMatch[1]) . ");";

                            $db_selection = $_ENV['DATABASE'] ?? getenv('DATABASE') ?? 'mysql';
                            if ($db_selection === 'mysql') {
                                $createSql = preg_replace('/INTEGER\s+PRIMARY\s+KEY\s+AUTOINCREMENT/i', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY', $createSql);
                            }

                            \App\Config\Database::send($createSql);
                            Message::set('info', "Создана таблица '$table' по схеме.");
                        } else {
                            Message::set('error', "Не удалось найти SQL для создания таблицы '$table' в " . \App\Config\Database::$schema_name);
                        }
                    } catch (\PDOException $e) {
                        Message::set('error', "Ошибка при создании таблицы '$table': " . $e->getMessage());
                    }
                }
            }
        }
    }

    private static function onTableExists(string $tableName)
    {
        try {
            $db_selection = $_ENV['DATABASE'] ?? getenv('DATABASE') ?? 'mysql';
            if ($db_selection === 'sqlite') {
                $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name=?";
                $params = [$tableName];
                $result = \App\Config\Database::send($sql, $params);
                return is_array($result) && count($result) > 0;
            } else {
                $sql = "SHOW TABLES LIKE ?";
                $params = [$tableName];
                $result = \App\Config\Database::send($sql, $params);
                return is_array($result) && count($result) > 0;
            }
        } catch (\PDOException $e) {
            Message::set('error', "Ошибка при проверке существования таблицы '$tableName': " . $e->getMessage());
            return false;
        }
    }

    public static function onColumnExists(string $columnName, string $tableName)
    {
        try {
            $db_selection = $_ENV['DATABASE'] ?? getenv('DATABASE') ?? 'mysql';

            $columnExists = false;

            if ($db_selection === 'sqlite') {
                $sql = "PRAGMA table_info($tableName)";
                $columns = \App\Config\Database::send($sql);
                if (is_array($columns)) {
                    foreach ($columns as $column) {
                        if (isset($column['name']) && $column['name'] === $columnName) {
                            $columnExists = true;
                            break;
                        }
                    }
                }
            } else {
                $sql = "SHOW COLUMNS FROM `$tableName` LIKE ?";
                $columns = \App\Config\Database::send($sql, [$columnName]);
                if (is_array($columns) && count($columns) > 0) {
                    $columnExists = true;
                }
            }

            if (!$columnExists) {
                $addColumnSql = ($db_selection === 'sqlite')
                    ? "ALTER TABLE \"$tableName\" ADD COLUMN \"$columnName\" TEXT"
                    : "ALTER TABLE `$tableName` ADD COLUMN `$columnName` VARCHAR(255)";
                \App\Config\Database::send($addColumnSql);
                Message::set('error', "Создание новой колонки '$columnName' в таблице '$tableName'");
            }

            return true;
        } catch (\Throwable $e) {
            Message::set('error', "Ошибка при проверке/создании колонки '$columnName' в таблице '$tableName': " . $e->getMessage());
            return false;
        }
    }

    public static function onRedirect(string $path)
    {
        try {
            if (empty($path)) {
                throw new \Exception("Путь для перенаправления не может быть пустым");
            }

            $path = preg_replace('#^/search/search/#', '/search/', $path);
            $path = preg_replace('#^search/search/#', 'search/', $path);

            $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
            $normalizedPath = parse_url($path, PHP_URL_PATH) ?? $path;

            $currentUri = preg_replace('#/+#', '/', rtrim($currentUri, '/')) ?: '/';
            $normalizedPath = preg_replace('#/+#', '/', rtrim($normalizedPath, '/')) ?: '/';

            if ($_SERVER['REQUEST_METHOD'] === 'GET' && $currentUri === $normalizedPath) {
                throw new \Exception("Обнаружен циклический редирект на: " . $path);
            }

            if (strpos($path, '/') !== 0) {
                $path = '/' . $path;
            }

            if (ob_get_level()) {
                ob_end_clean();
            }

            if (headers_sent($file, $line)) {
                throw new \Exception("Заголовки уже были отправлены в файле $file на строке $line");
            }

            header("Location: " . $path, true, 302);
            exit();

        } catch (\Exception $e) {
            Message::set('error', "Ошибка при перенаправлении: " . $e->getMessage());

            if (!headers_sent()) {
                header("HTTP/1.1 500 Internal Server Error");
                echo "Произошла внутренняя ошибка. Пожалуйста, проверьте ваше интернет соединение!";
                exit();
            } else {
                return false;
            }
        }
    }

    public static function onRoute()
    {
        self::onAutoloadRegister();
        \App\Config\Database::getConnection();

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if (isset($_GET['route'])) {
            $route = trim($_GET['route']);
            $route = '/' . ltrim($route, '/');
            if ($route === '//') {
                $route = '/';
            }
        } else {
            $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
            $route = preg_replace('#/+#', '/', $route);
            if ($route === '') {
                $route = '/';
            }
        }

        $route = preg_replace('#/+#', '/', $route);

        $findRoute = false;

        $candidateMethods = [$method];
        if ($method === 'POST') {
            $candidateMethods[] = 'GET';
        } elseif ($method === 'HEAD') {
            $candidateMethods[] = 'GET';
        }

        foreach ($candidateMethods as $candidateMethod) {
            $routes = self::$patterns[$candidateMethod] ?? [];
            foreach ($routes as $pattern => $callback) {
                if (preg_match($pattern, $route, $matches)) {
                    $findRoute = true;
                    array_shift($matches);

                    if (is_array($callback) && isset($callback[0], $callback[1]) && class_exists($callback[0])) {
                        $controller = new $callback[0];
                        $action = $callback[1];
                        if (method_exists($controller, $action)) {
                            $controller->$action(...$matches);
                        } else {
                            self::handleInvalidCallback($route, $callback);
                        }
                    } elseif (is_callable($callback)) {
                        $named_params = [];
                        foreach ($matches as $key => $value) {
                            if (is_string($key)) {
                                $named_params[$key] = $value;
                            }
                        }
                        call_user_func_array($callback, array_values($named_params));
                    } else {
                        self::handleInvalidCallback($route, $callback);
                    }

                    break 2;
                }
            }
        }

        if (!$findRoute) {
            header("HTTP/1.1 404 Страница не найдена");
            (new \App\Models\Router\Routes())->error_404($route);
            exit();
        }
    }

    public static function onAutoloadRegister(): void
    {
        spl_autoload_register(function ($className) {
            $filePath = dirname(__DIR__) . '/' . str_replace(['\\', 'App\Models'], ['/', ''], $className) . '.php';

            if (file_exists($filePath)) {
                require_once $filePath;
            } else {
                Message::set('error', "Ошибка загрузки класса '$className'. Файл не существует по пути: $filePath");
            }
        });
    }

    public static function onPHPMailer(array $data)
    {
        try {
            $mail = new PHPMailer();
            $mail->CharSet = 'UTF-8';

            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPDebug = 0;

            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port = (int)($_ENV['EMAIL_PORT'] ?? getenv('EMAIL_PORT') ?? 587);
            $mail->Username = (string)($_ENV['EMAIL'] ?? getenv('EMAIL') ?? '');
            $mail->Password = (string)($_ENV['EMAIL_PASSWORD'] ?? getenv('EMAIL_PASSWORD') ?? '');
            $mail->setFrom(
                (string)($_ENV['EMAIL'] ?? getenv('EMAIL') ?? ''),
                (string)($_ENV['EMAIL_NICKNAME'] ?? getenv('EMAIL_NICKNAME') ?? '')
            );
            $mail->addAddress($data['to_email']);
            $mail->Subject = $data['subject'];

            $mail->msgHTML($data['body']);

            $mail->send();
            $mail->clearAddresses();

            return true;
        } catch (\Exception $e) {
            Message::set('error', 'Ошибка при отправке письма: ' . $e->getMessage());
            return false;
        }
    }

    public static function handleInvalidCallback(string $path, $callback): void
    {
        $callbackType = is_object($callback) ? get_class($callback) : gettype($callback);
        $callbackName = is_string($callback) ? $callback : $callbackType;
        $request_method = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'post' : 'get';
        http_response_code(500);

        ob_start(); ?>
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Call to unknown method: <?= htmlspecialchars($callbackName) ?></title>
            <script src='https://cdn.tailwindcss.com'></script>
        </head>
        <body class='flex items-center justify-center h-[100dvh]'>
            <div class='container mx-auto w-full p-10 flex flex-col items-center justify-center'>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center">
                        <img src="app/Models/Network/assets/logo.png" alt="logo">
                    </div>
                    <span class="text-black text-2xl font-bold">QweesCore</span>
                </div>
                <div class='text-9xl font-bold text-red-500'>ERROR</div>
                <div class='text-6xl font-bold text-red-700'>UNKNOWN METHOD!</div>
                <div class='bg-black rounded-lg p-4 text-sm font-mono relative mt-4'>
                    <div class='flex gap-1 mb-2'>
                        <span class='w-2 h-2 bg-red-400 rounded-full'></span>
                        <span class='w-2 h-2 bg-yellow-400 rounded-full'></span>
                        <span class='w-2 h-2 bg-green-400 rounded-full'></span>
                    </div>
                    <div class='text-gray-400 mb-2'>// Вызываеться неизвестный метод при вызове:</div>
                    <div class='text-white'>
                        <span class='text-blue-400'>Routes</span><span class='text-blue-300'>::</span><span class='text-indigo-400'><?= $request_method ?></span><span class='text-yellow-300'>(</span><span class='text-red-300'>'</span><span class='text-green-300'><?= htmlspecialchars($path) ?></span><span class='text-red-300'>'</span>,
                        <span class='text-red-300'>'</span><span class='bg-red-400 px-2'><?= htmlspecialchars($callbackName) ?></span><span class='text-red-300'>'</span><span class='text-yellow-300'>)</span>;
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        echo ob_get_clean();
        exit;
    }

    public static function debugOutput(array $debug_info = [], $db_error = null, string $title = 'Диагностика проблемы', bool $as_json = true)
    {
        global $DB_ERROR_INFO;

        if ($db_error === null && isset($DB_ERROR_INFO)) {
            $db_error = $DB_ERROR_INFO;
        }

        $output = [];

        if (!empty($debug_info)) {
            $output['debug'] = $debug_info;
        }

        if ($db_error !== null) {
            $output['db_error'] = [
                'type' => $db_error['type'] ?? 'unknown',
                'message' => $db_error['message'] ?? '',
                'exception' => $db_error['exception'] ?? null,
                'code' => $db_error['code'] ?? null,
            ];

            if (isset($db_error['errorInfo'])) {
                $output['db_error']['pdo_error'] = $db_error['errorInfo'];
            }

            if (isset($db_error['rowCount'])) {
                $output['db_error']['rows_affected'] = $db_error['rowCount'];
            }
        }

        echo "<pre style='background:#f0f0f0;padding:10px;border:1px solid #ccc;margin:10px;font-size:12px;'>";
        echo "<strong style='color:#d32f2f;'>" . htmlspecialchars($title) . "</strong>\n\n";

        if (!empty($debug_info)) {
            echo htmlspecialchars(implode("\n", $debug_info)) . "\n";
        }

        if ($db_error !== null) {
            echo "\n<strong style='color:#d32f2f;'>Ошибка БД:</strong>\n";
            echo "Тип: " . htmlspecialchars($db_error['type'] ?? 'unknown') . "\n";
            if (isset($db_error['message'])) {
                echo "Сообщение: " . htmlspecialchars($db_error['message']) . "\n";
            }
            if (isset($db_error['code'])) {
                echo "Код: " . htmlspecialchars($db_error['code']) . "\n";
            }
        }

        echo "</pre>";

        if ($as_json) {
            echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }

    public static function errorOutput(string $message, $db_error = null, int $http_code = 400)
    {
        http_response_code($http_code);

        global $DB_ERROR_INFO;
        if ($db_error === null && isset($DB_ERROR_INFO)) {
            $db_error = $DB_ERROR_INFO;
        }

        $debug_info = ["Ошибка: " . $message];

        if ($db_error !== null) {
            $debug_info[] = "Детали: " . ($db_error['message'] ?? 'неизвестно');
        }

        self::debugOutput($debug_info, $db_error, 'Ошибка', true);
    }
}