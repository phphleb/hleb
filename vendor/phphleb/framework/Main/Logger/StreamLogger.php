<?php

declare(strict_types=1);

namespace Hleb\Main\Logger;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * Output logs to the specified STDOUT event stream.
 *
 * Вывод логов в указанный поток событий STDOUT.
 */
#[AvailableAsParent] #[Accessible]
class StreamLogger extends BaseLogger implements LoggerInterface
{
    public function __construct(
        readonly private string $stream,
        readonly private string $host,
        bool $isDebug = false,
    )
    {
        $this->isDebug = $isDebug;
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler('emergency', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler('alert', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler('critical', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler('error', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler('warning', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler('notice', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler('info', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler('debug', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function log(mixed $level, \Stringable|string $message, array $context = []): void
    {
        $this->saveToStream($this->contextHandler($level, $message, $context));
    }

    /**
     * The output of the prepared log line.
     *
     * Вывод подготовленной строки лога.
     */
    private function saveToStream(string $row): void
    {
        \file_put_contents($this->stream,$row . PHP_EOL);
    }

    private function contextHandler(string $level, string $message, array $context): string
    {
        return $this->createLog($level, $message, \array_merge($context, ['host' => $this->host, 'source' => \dirname(__DIR__, 5)]));
    }
}
