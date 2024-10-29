<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Base\Task;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * Caching data of various types in the framework.
 *
 * Кеширование данных различного типа во фреймворке.
 */
#[Accessible] #[AvailableAsParent]
class CommandReference implements CommandInterface, Interface\Command
{
    /**
     * @inheritDoc
     */
    #[\Override]
    public function execute(Task $task, array $arguments = []): mixed
    {
        $task->call($arguments);

        return $task->getExecResult();
    }
}
