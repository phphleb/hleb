<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Reference\DtoInterface;

/**
 * Intended only for rolling back a value after an asynchronous request has completed.
 * In controllers it is used as $this->container->dto().
 *
 * Предназначен только для отката значения после завершения асинхронного запроса.
 * В контроллерах используется как $this->container->dto().
 *
 * @see DtoInterface
 */
#[Accessible]
final class Dto extends BaseAsyncSingleton implements RollbackInterface
{
    private static DtoInterface|null $replace = null;

    /**
     * @inheritDoc
     *
     * @internal
     */
    #[\Override]
    public static function rollback(): void
    {
        if (self::$replace) {
            self::$replace::rollback();
        } else {
            BaseContainer::instance()->get(DtoInterface::class)::rollback();
        }
    }

    /**
     * @internal
     *
     * @see DtoForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(DtoInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
