<?php

/*
 * Executing your own console command.
 * Execute `php console --list` to get a list of all commands.
 *
 * Выполнение собственной консольной команды.
 * При выполнении `php console --list` получить список всех команд.
 */

namespace App\Commands;

class DefaultTask extends \Hleb\Scheme\App\Commands\MainTask
{
    /* Console command name comes from class name */
    /* Название консольной команды складывается из названия класса */
    /** php console default-task [arg] **/

    /* Short name of the action for the command */
    /* Короткое название действия для команды */
    const DESCRIPTION = "Default task";

    /**
     * @param string|int|null $arg - argument description
     */
    protected function execute($arg = null) {

        // Your code here.
        // Разместите здесь свой код.

        echo PHP_EOL . __CLASS__ . " done." . PHP_EOL;
    }

}


