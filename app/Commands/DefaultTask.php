<?php

declare(strict_types=1);

/*
 * Own console command.
 * By executing `php console --list` you can get a list of all commands.
 *
 * Собственная консольная команда.
 * При выполнении `php console --list` можно получить список всех команд.
 */

namespace App\Commands;

use Hleb\Base\Task;

class DefaultTask extends Task
{
    /** php console default-task [arg] **/

    /**
     * <The first line from the command description will be the info in the command list.>
     * Console command name comes from class name: `php console default-task`.
     *
     * Первая строчка из описания команды будет описанием команды в перечне команд.
     * Название консольной команды складывается из названия класса: `php console default-task`.
     *
     * @param string|null $arg - demo unnamed argument, default null.
     *                         - демонстрационный неименованный аргумент, по умолчанию null.
     *
     * @return int - returns the numeric command execution code.
     *             - возвращает числовой код выполнения команды.
     */
    protected function run(?string $arg = null): int
    {
        // Your code here.
        // Разместите здесь свой код.
        // ... //

        return self::SUCCESS_CODE;
    }
}
