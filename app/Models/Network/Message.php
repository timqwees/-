<?php

namespace App\Models\Network;

/**
 * Хранилище системных сообщений (QweesCore style).
 * Message::set('error', 'текст') — записать
 * Message::get('error')      — прочитать
 * Message::has('error')      — есть ли
 * Message::clear()           — очистить
 */
class Message
{
    private static array $messages = [];

    public static function set(string $type, string $text): void
    {
        self::$messages[$type][] = $text;
    }

    public static function get(string $type = 'error'): array
    {
        return self::$messages[$type] ?? [];
    }

    public static function first(string $type = 'error'): ?string
    {
        $list = self::get($type);
        return $list[0] ?? null;
    }

    public static function has(string $type = 'error'): bool
    {
        return !empty(self::$messages[$type]);
    }

    public static function clear(?string $type = null): void
    {
        if ($type === null) {
            self::$messages = [];
            return;
        }
        unset(self::$messages[$type]);
    }

    /** Все сообщения одной категории, готовые к выводу. */
    public static function all(string $type = 'error'): string
    {
        return implode('<br>', array_map('htmlspecialchars', self::get($type)));
    }
}
