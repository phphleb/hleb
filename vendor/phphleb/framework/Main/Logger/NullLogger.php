<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Logger;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * Stub for logging.
 *
 * Заглушка для логирования.
 */
#[AvailableAsParent] #[Accessible]
class NullLogger  implements LoggerInterface, \Hleb\Reference\Interface\Log
{
    /** @inheritDoc */
    #[\Override]
    public function emergency(\Stringable|string $message, array $context = []): void
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function alert(\Stringable|string $message, array $context = []): void
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function critical(\Stringable|string $message, array $context = []): void
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function error(\Stringable|string $message, array $context = []): void
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function warning(\Stringable|string $message, array $context = []): void
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function notice(\Stringable|string $message, array $context = []): void
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function info(\Stringable|string $message, array $context = []): void
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function debug(\Stringable|string $message, array $context = []): void
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function log(mixed $level, \Stringable|string $message, array $context = []): void
    {
    }
}
