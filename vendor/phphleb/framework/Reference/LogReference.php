<?php

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Main\Logger\Log as Logger;

#[Accessible] #[AvailableAsParent]
class LogReference extends ContainerUniqueItem implements LogInterface, Interface\Log
{
    /** @inheritDoc */
    #[\Override]
    public function emergency(string $message, array $context = []): void
    {
        Logger::instance()->emergency($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function alert(string $message, array $context = []): void
    {
        Logger::instance()->alert($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function critical(string $message, array $context = []): void
    {
        Logger::instance()->critical($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function error($message, array $context = []): void
    {
        Logger::instance()->error($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function warning(string $message, array $context = []): void
    {
        Logger::instance()->warning($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function notice(string $message, array $context = []): void
    {
        Logger::instance()->notice($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function info(string $message, array $context = []): void
    {
        Logger::instance()->info($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function debug(string $message, array $context = []): void
    {
        Logger::instance()->debug($message, self::b7e($context));
    }

    /** @inheritDoc */
    #[\Override]
    public function log($level, string $message, array $context = []): void
    {
        Logger::instance()->log($level, $message, self::b7e($context));
    }

    /**
     * The nesting level of the called log in the code to determine the file and call line.
     *
     * Уровень вложенности вызываемого лога в коде для определения файла и строки вызова.
     */
    private static function b7e(array $context): array
    {
        if (empty($context[Logger::B7E_NAME])) {
            $context[Logger::B7E_NAME] = Logger::LOGGER_B7E;
        }

        return $context;
    }
}
