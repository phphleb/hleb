<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Base\Task;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\CommandInterface;

#[Accessible]
final class Command extends BaseSingleton
{
    private static CommandInterface|null $replace = null;

    /**
     * Execute an initiated command object with arguments.
     *
     * Выполнение инициированного объекта команды с аргументами.
     */
    public static function execute(Task $task, array $arguments = []): mixed
    {
        if (self::$replace) {
            return self::$replace->execute($task, $arguments);
        }

        return BaseContainer::instance()->get(CommandInterface::class)->execute($task, $arguments);
    }

    /**
     * @internal
     *
     * @see CommandForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(CommandInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
