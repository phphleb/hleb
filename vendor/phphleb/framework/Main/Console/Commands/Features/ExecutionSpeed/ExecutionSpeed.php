<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\ExecutionSpeed;

use Hleb\Main\Console\Commands\Features\FeatureInterface;

/**
 * @internal
 */
final class ExecutionSpeed implements FeatureInterface
{
    private const DESCRIPTION = 'Returns the execution time (sec) of an empty console request by the framework.';

    /**
     * Returns the execution time of an empty console request.
     *
     * Возвращает время выполнения пустого консольного запроса.
     */
    #[\Override]
    public function run(array $argv): string
    {
        return (string)(\microtime(true) - HLEB_START);
    }

    /** @inheritDoc */
    #[\Override]
    public static function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    /** @inheritDoc */
    #[\Override]
    public function getCode(): int
    {
        return 0;
    }
}
