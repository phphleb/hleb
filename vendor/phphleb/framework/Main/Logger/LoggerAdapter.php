<?php

declare(strict_types=1);

namespace Hleb\Main\Logger;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Psr\Log\LoggerInterface as PsrLogger;

/**
 * It is an adapter for PSR-3 logging.
 * The class object can also be used as an internal framework object.
 *
 * Представляет собой адаптер для логирования по PSR-3.
 * Объект класса может быть использован также как внутренний объект фреймворка.
 */
#[AvailableAsParent] #[Accessible]
readonly class LoggerAdapter  implements LoggerInterface, PsrLogger
{
    public function __construct(private PsrLogger $logger)
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    /** @inheritDoc */
    #[\Override]
    public function log(mixed $level, \Stringable|string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
