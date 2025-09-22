<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Cache;

use Hleb\DynamicStateException;

/**
 * The class contains the execution logic for the once() function,
 * which allows a piece of specific code to be executed once per request.
 *
 * Класс содержит логику выполнения для функции once(),
 * она позволяет выполнять часть определенного кода один раз за запрос.
 */
final class OnceResult
{
    private static array $data = [];

    /**
     * @see once()
     *
     * @internal
     */
    public static function get(callable $func): mixed
    {
        $backtrace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $pos = $backtrace[2] ?? null;

        if (empty($pos) || $pos['function'] !== 'once') {
            throw new DynamicStateException('Incorrect use of the once() function');
        }

        $key = $pos['file'] . ':' . $pos['line'];

        if (\array_key_exists($key, self::$data)) {
            return self::$data[$key];
        }

        return self::$data[$key] = $func();
    }

    /**
     * @internal
     */
    public static function rollback(): void
    {
        self::$data = [];
    }
}
