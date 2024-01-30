<?php

declare(strict_types=1);

namespace App\Bootstrap\Events;

use Hleb\Base\Event;
use Hleb\Constructor\Attributes\Dependency;

#[Dependency]
final class TaskEvent extends Event
{
    /**
     * Executes before each command and returns a summary of the arguments.
     *
     * Выполняется перед каждой командой и возвращает итоговые данные для аргументов.
     *
     * @param string $class - class of the command being executed.
     *                      - класс выполняемой команды.
     *
     * @param string $method - 'call' or 'run', run from code or console.
     *                       - 'call' или 'run', запуск из кода или из консоли.
     *
     * @param array $arguments - arguments of the command to be executed.
     *                         - аргументы выполняемой команды.
     *
     * ```php
     * switch([$class, $method]) {
     *     case [DefaultTask::class, 'run']:
     *         return (new DefaultTaskEvent())->beforeRun($arguments);
     *     // ... //
     *     default:
     * }
     * return $arguments
     * ```
     */
    // public function before(string $class, string $method, array $arguments): array { return $arguments; }

    // public function after(string $class, string $method, mixed &$result): void { }

    // public function statusCode(string $class, string $method, int $code): int { return $code; }
}
