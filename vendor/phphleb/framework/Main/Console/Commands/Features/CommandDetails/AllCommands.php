<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\CommandDetails;

use Hleb\Main\Console\Commands\Features\FeatureInterface;

/**
 * @internal
 */
final class AllCommands implements FeatureInterface
{
    private const DESCRIPTION = 'Lists all supported commands for autocompletion.';

    /**
     * @param array $commands - a list of basic framework commands.
     *                        - список базовых команд фреймворка.
     */
    public function __construct(private readonly array $commands)
    {
    }

    /**
     * Returns a list of all supported commands.
     * Commands are separated by line breaks.
     *
     * Возвращает перечень всех поддерживаемых команд.
     * Команды разделены переносом строки.
     */
    #[\Override]
    public function run(array $argv): string|false
    {
        try {
            $custom = (new CustomList())->get();
            $all = \array_merge($this->commands, $custom);
        } catch (\Throwable) {
            // To avoid breaking autocomplete, you need to return an empty value.
            // Чтобы не ломать автокомплит, нужно вернуть пустое значение.
            return "";
        }

        return \implode(PHP_EOL, $all);
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
