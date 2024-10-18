<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\CommandDetails;

use Hleb\Main\Console\Commands\Features\FeatureInterface;

/**
 * @internal
 */
final class CommandArgument implements FeatureInterface
{
    private const DESCRIPTION = 'List of arguments selected for the next possible argument.';

    /**
     * @param array $commands - a list of basic framework commands.
     *                        - список базовых команд фреймворка.
     */
    public function __construct(private readonly array $commands)
    {
    }

    /**
     * Returns a list of options for the next argument.
     * Arguments are separated by line breaks.
     *
     * Возвращает перечень вариантов следующего аргумента.
     * Аргументы разделены переносом строки.
     */
    #[\Override]
    public function run(array $argv): string|false
    {
        $arguments = ['--help'];
        try {
            $custom = (new CustomList())->get();
            $all = \array_merge($this->commands, $custom);
            if (!\in_array(\trim(\current($argv)), $all, true)) {
                // Command not supported.
                // Команда не поддерживается.
                return '';
            }
            if (\in_array(\trim(\current($argv)), $custom, true)) {
                $arguments[] = "--desc";
            }

        } catch (\Throwable) {
            // To avoid breaking autocomplete, you need to return an empty value.
            // Чтобы не ломать автокомплит, нужно вернуть пустое значение.
            return "";
        }

        return \implode(PHP_EOL, $arguments);
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
