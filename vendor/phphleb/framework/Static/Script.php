<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use AsyncExitException;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\HlebBootstrap;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\ScriptInterface;
use JetBrains\PhpStorm\NoReturn;

#[Accessible]
final class Script extends BaseSingleton
{
    private static ScriptInterface|null $replace = null;

    /**
     * Simulation with exit from the process (script) for asynchronous mode
     * and normal exit for standard mode.
     * For the function to work correctly in asynchronous mode, it is necessary
     * do not catch the thrown exception AsyncExitException in the code above.
     *
     * Имитация с выходом из процесса (скрипта) для асинхронного режима
     * и обычный выход для стандартного режима.
     * Для корректной работы функции в асинхронном режиме необходимо
     * не перехватывать выбрасываемое исключение AsyncExitException
     * в коде уровнем выше.
     *
     *  ```php
     *
     *   try {
     *       ...
     *   } catch (\AsyncExitException $e) {
     *     throw $e
     *   } catch (\ErrorException $t) {
     *     ...
     *   }
     * ```
     *
     * @throws AsyncExitException
     */
    public static function asyncExit($message = '', ?int $httpStatus = null, array $headers = []): never
    {
        if (self::$replace) {
            self::$replace->asyncExit($message, $httpStatus, $headers);
        } else {
            try {
                $httpStatus = $httpStatus ?? Response::getStatus();
                Response::addHeaders($headers);
                $body = Response::getBody();
                $headers = Response::getHeaders();
            } catch (\Throwable) {
                // Exception may be thrown before initialization.
                // Исключение может быть выброшено раньше инициализации.
                $httpStatus = $httpStatus ?? 500;
                $body = $body ?? '';
            }
            $message = \is_int($message) ? $message : $body . $message;
            if (\defined('HLEB_LOAD_MODE') && HLEB_LOAD_MODE !== HlebBootstrap::ASYNC_MODE) {
                self::standardExit($message, $httpStatus, $headers);
            }
            $message = \is_int($message) ? '' : $message;
            throw (new AsyncExitException($message))->setHeaders($headers)->setStatus($httpStatus);
        }
    }

    /**
     * Wrapper for classic script exit. Not applicable for asynchronous mode.
     *
     * Обёртка для классического выхода из скрипта. Неприменимо для асинхронного режима.
     *
     * @internal
     */
    #[NoReturn]
    public static function standardExit($message = '', int $httpCode = 200, array $headers = []): never
    {
        if (self::$replace) {
            self::$replace->standardExit($message, $httpCode, $headers);
        } else {
            \headers_sent() or \http_response_code($httpCode);
            foreach ($headers as $name => $header) {
                if (\is_array($header)) {
                    foreach ($header as $h) {
                        \header("$name: $h");
                    }
                } else {
                    \header("$name: $header");
                }
            }
            exit($message);
        }
    }

    /**
     * @internal
     *
     * @see ScriptForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(ScriptInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
