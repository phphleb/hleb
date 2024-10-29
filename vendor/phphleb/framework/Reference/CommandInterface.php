<?php

namespace Hleb\Reference;

use Hleb\Base\Task;

interface CommandInterface
{
    /**
     * Execute an initiated command object with arguments.
     *
     * Выполнение инициированного объекта команды с аргументами.
     */
    public function execute(Task $task, array $arguments): mixed;
}
