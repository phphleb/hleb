<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Logger;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Main\Logger\Log as Logger;

/**
 * Wrapper over Hleb\Static\Log.
 *
 * Обёртка над Hleb\Static\Log.
 */
#[AvailableAsParent] #[Accessible]
class LoggerWrapper  implements LoggerInterface, \Hleb\Reference\Interface\Log
{
    /** @inheritDoc */
    #[\Override]
    public function emergency(\Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::emergency($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function alert(\Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::alert($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function critical(\Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::critical($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function error(\Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::error($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function warning(\Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::warning($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function notice(\Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::notice($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function info(\Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::info($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function debug(\Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::debug($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function log(mixed $level, \Stringable|string $message, array $context = []): void
    {
        \Hleb\Static\Log::log($level, $message, self::b7e($context));
    }

    /**
     * The nesting level of the called log in the code to determine the file and call line.
     *
     * Уровень вложенности вызываемого лога в коде для определения файла и строки вызова.
     */
    private static function b7e(array $context): array
    {
        if (empty($context[Logger::B7E_NAME])) {
            $context[Logger::B7E_NAME] = Logger::WRAPPER_B7E;
        }

        return $context;
    }
}
