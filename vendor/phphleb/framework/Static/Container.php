<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use App\Bootstrap\ContainerFactory;
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Constructor\Containers\CoreContainer;
use Hleb\CoreProcessException;
use Hleb\Init\ShootOneselfInTheFoot\ContainerForTest;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Constructor\Containers\TestContainerInterface;

/**
 * Implements methods for working with custom containers.
 *
 * Реализует методы работы с пользовательскими контейнерами.
 */
#[Accessible]
final class Container extends BaseAsyncSingleton implements RollbackInterface
{
    private static TestContainerInterface|null $replace = null;

    /**
     * Getting a custom container by ID.
     *
     * Получение пользовательского контейнера по идентификатору.
     *
     * @template TContainerInterface
     * @param class-string<TContainerInterface> $id
     * @return TContainerInterface|mixed
     */
    public static function get(string $id): mixed
    {
        if (self::$replace) {
            return self::$replace->get($id);
        }

        return BaseContainer::instance()->get($id);
    }

    /**
     * Checking if a container exists by ID.
     *
     * Проверка существования контейнера по ID.
     */
    public static function has(string $id): bool
    {
        if (self::$replace) {
            return self::$replace->has($id);
        }

        return BaseContainer::instance()->has($id);
    }

    /**
     * Returns the configured container or its replacement (for tests).
     *
     * Возвращает сконфигурированный контейнер или его замену (для тестов).
     */
    public static function getContainer(): \App\Bootstrap\ContainerInterface
    {
        if (self::$replace) {
            return self::$replace->getContainer();
        }

        return BaseContainer::instance();
    }

    /**
     * Forced cleaning of containers.
     * (!) Use in development can lead to unpredictable consequences.
     *
     * Принудительная очистка контейнеров.
     * (!) Использование в разработке может привести к непредсказуемым последствиям.
     */
    #[\Override]
    public static function rollback(): void
    {
        if (self::$replace) {
            self::$replace->rollback();
        } else {
            CoreContainer::rollback();
            ContainerFactory::rollback();
        }
    }

    /**
     * @internal
     *
     * @see ContainerForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(TestContainerInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
