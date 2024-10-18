<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\CommandDetails;

use Hleb\Helpers\TaskHelper;

/**
 * @internal
 */
final class CustomList
{
    /**
     * Returns an array of supported command names.
     *
     * Возвращает массив с поддерживаемыми названиями команд.
     */
    public function get(): array
    {
        $custom = [];
        foreach ((new TaskHelper())->getCommands() as $command) {
            $custom[] = $command['name'];
        }
        return $custom;
    }
}
