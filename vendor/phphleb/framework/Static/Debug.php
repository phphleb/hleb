<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Reference\DebugInterface;

#[Accessible]
final class Debug extends BaseAsyncSingleton implements RollbackInterface
{
    private static DebugInterface|null $replace = null;

    /**
     * Sends data for storage when debug mode is active.
     * By default, the data is displayed in the framework debug panel.
     *
     * При активном отладочном режиме отправляет данные для хранения.
     * По умолчанию данные выводятся в панель отладки фреймворка.
     */
    public static function send(mixed $data, ?string $name = null): void
    {
        if (self::$replace) {
            self::$replace->send($data, $name);
        } else {
            BaseContainer::instance()->get(DebugInterface::class)->send($data, $name);
        }
    }

    /**
     * When debug mode is active, returns any previously added debugging data.
     *
     * При активном режиме отладки возвращает все добавленные ранее отладочные данные.
     */
    public static function getCollection(): array
    {
        if (self::$replace) {
            return self::$replace->getCollection();
        }
        return BaseContainer::instance()->get(DebugInterface::class)->getCollection();
    }

    /**
     * Saves the system tag to debug data.
     *
     * Сохраняет системную метку в отладочные данные.
     *
     * @see hl_check()
     */
    public static function setHlCheck(string $message, ?string $file = null, ?int $line = null): void
    {
        if (self::$replace) {
            self::$replace->setHlCheck($message, $file, $line);
        } else {
            BaseContainer::instance()->get(DebugInterface::class)->setHlCheck($message, $file, $line);
        }
    }

    /**
     * Returns the status of the debug mode activity.
     *
     * Возвращает статус активности режима отладки.
     */
    public static function isActive(): bool
    {
        if (self::$replace) {
            return self::$replace->isActive();
        }
        return BaseContainer::instance()->get(DebugInterface::class)->isActive();
    }

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
            BaseContainer::instance()->get(DebugInterface::class)::rollback();
        }
    }

    /**
     * @internal
     *
     * @see DebugForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(DebugInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
