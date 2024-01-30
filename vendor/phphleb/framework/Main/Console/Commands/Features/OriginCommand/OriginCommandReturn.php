<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\OriginCommand;

use Hleb\Main\Console\Commands\Features\FeatureInterface;

/**
 * Return the arguments passed with the command.
 *
 * Возвращение аргументов, переданных с командой.
 *
 * @internal
 */
final class OriginCommandReturn implements FeatureInterface
{
    private const DESCRIPTION = 'RETURN_COMMAND_ARGUMENTS';

    /**
     * Returns the arguments passed with the command.
     *
     * Возвращает аргументы, переданные с командой.
     */
    #[\Override]
    public function run(array $argv): string
    {
        $argv and \array_shift($argv);
        if (!$argv) {
            return '';
        }
        return \implode(' ', $argv) . PHP_EOL;
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
