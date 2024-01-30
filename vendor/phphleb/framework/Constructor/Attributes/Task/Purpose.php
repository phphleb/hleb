<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Attributes\Task;

use JetBrains\PhpStorm\ExpectedValues;

/**
 * Sets the execution scope for a custom command.
 * Can only be assigned to a task class. For example:
 *
 * Устанавливает область выполнения для пользовательской команды.
 * Может быть назначен только к классу команды. Например:
 *
 * ```php
 *
 * use Hleb\Constructor\Attributes\Task\Purpose;
 *
 * #[Purpose(status:Purpose::CONSOLE)]
 * class DefaultTask extends Task {...}
 * ```
 *
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
readonly final class Purpose
{
    /**
     * Allows any use of the command.
     *
     * Разрешает любое использование команды.
     */
    final public const FULL = 'full';

    /**
     * Can only be used as a console command.
     * If you try to use it outside the CLI, it will return an error.
     *
     * Можно использовать только в качестве консольной команды.
     * При попытке использовать вне CLI вернёт ошибку.
     */
    final public const CONSOLE = 'console';

    /**
     * Can be used in code, but not in a console command.
     * With this label, the task will not be displayed in the list
     * of console commands and will return an error when trying to execute it.
     *
     * Можно использовать в коде, но не в консольной команде.
     * С этой меткой задача не будет выводиться в списке консольных команд
     * и при попытке её выполнить вернет ошибку.
     */
    final public const EXTERNAL = 'external';

    public function __construct(
        #[ExpectedValues([
            Purpose::FULL,
            Purpose::CONSOLE,
            Purpose::EXTERNAL,
        ])]
        public string $status = Purpose::FULL
    )
    {
    }
}
