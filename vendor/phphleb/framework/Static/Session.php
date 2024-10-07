<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Reference\SessionInterface;

/**
 * A simple wrapper for sessions.
 *
 * Простая обёртка для сессий.
 */
#[Accessible]
class Session extends BaseAsyncSingleton implements RollbackInterface
{
    private static SessionInterface|null $replace = null;

    /**
     * Returns an array with current session data.
     *
     * Возвращает массив с данными текущей сессии.
     */
    public static function all(): array
    {
        if (self::$replace) {
            return self::$replace->all();
        }

        return BaseContainer::instance()->get(SessionInterface::class)->all();
    }

    /**
     * Returns session data by parameter name.
     *
     * Возвращает данные сессии по названию параметра.
     */
    public static function get(string|int $name): mixed
    {
        if (self::$replace) {
            return self::$replace->get($name);
        }

        return BaseContainer::instance()->get(SessionInterface::class)->get($name);
    }

    /**
     * Returns the current session identifier.
     *
     * Возвращает идентификатор текущей сессии.
     */
    public static function getSessionId(): string|null
    {
        if (self::$replace) {
            return self::$replace->getSessionId();
        }

        return BaseContainer::instance()->get(SessionInterface::class)->getSessionId();
    }

    /**
     * Assigns session data by parameter name.
     *
     * Присваивает данные сессии по названию параметра.
     */
    public static function set(string|int $name, string|float|int|array|bool|null $data): void
    {
        if (self::$replace) {
            self::$replace->set($name, $data);
        } else {
            BaseContainer::instance()->get(SessionInterface::class)->set($name, $data);
        }
    }

    /**
     * Deleting session data by parameter name
     *
     * Удаление данных сессии по названию параметра.
     */
    public static function delete(string|int $name): void
    {
        if (self::$replace) {
            self::$replace->delete($name);
        } else {
            BaseContainer::instance()->get(SessionInterface::class)->delete($name);
        }
    }

    /**
     * Clears all session data.
     *
     * Очищает все данные сессии.
     */
    public static function clear(): void
    {
        if (self::$replace) {
            self::$replace->clear();
        } else {
            BaseContainer::instance()->get(SessionInterface::class)->clear();
        }
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
            BaseContainer::instance()->get(SessionInterface::class)::rollback();
        }
    }

    /**
     * @internal
     *
     * @see SessionForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(SessionInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
