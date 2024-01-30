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

    /**
     * Used if you need to rollback data
     * for an asynchronous request.
     *
     * Используется, если необходимо откатить
     * данные для асинхронного запроса.
     */
    public static function rollback(): void;
}
