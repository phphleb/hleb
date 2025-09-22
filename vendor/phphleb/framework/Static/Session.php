<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\SessionInterface;

/**
 * A simple wrapper for sessions.
 *
 * Простая обёртка для сессий.
 */
#[Accessible]
class Session extends BaseSingleton
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
    public static function get(string|int $name, mixed $default = null): mixed
    {
        if (self::$replace) {
            return self::$replace->get($name, $default);
        }

        return BaseContainer::instance()->get(SessionInterface::class)->get($name, $default);
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
     * Checks the existence of a session with a specific name.
     *
     * Проверяет наличие сессии с конкретным названием.
     */
    public static function has(string|int $name): bool
    {
        if (self::$replace) {
            return self::$replace->has($name);
        }

        return BaseContainer::instance()->get(SessionInterface::class)->has($name);
    }

    /**
     * Checks a session with a specific name.
     * If the session does not exist or the value is null or the empty string, it will return false.
     *
     * Проверяет сессию с конкретным названием.
     * Если сессия не существует или значение равно null или пустой строке, то вернет false.
     */
    public static function exists(string|int $name): bool
    {
        if (self::$replace) {
            return self::$replace->exists($name);
        }

        return BaseContainer::instance()->get(SessionInterface::class)->exists($name);
    }

    /**
     * Create a flash session that exists only for one or more of the following requests.
     * This session is usually used to show the user a message in the next request
     * and will then be deleted automatically.
     * Can also be used for any temporary data that only exists
     * for the next request (or a specified amount of it).
     * If you set null as a value or repeat=0, then such a session will be disabled.
     *
     * Создание flash-сессии которая существует только при одном или нескольких следующих запросах.
     * Такая сессия обычно используется, чтобы показать пользователю сообщение в следующем запросе
     * и потом будет удалена автоматически.
     * Также можно использовать для любых временных данных, которые существуют
     * только при следующем запросе (или указанном их количестве).
     * Если установить null как значение или repeat=0, то такая сессия будет отключена.
     *
     * @param string $name - name of the flash session.
     *                     - название flash-сессии.
     *
     * @param string|float|int|array|bool|null $data - session data, for example, the text of the message to the user.
     *                                               - данные сессии, например, текст сообщения пользователю.
     *
     * @param int $repeat - the number of next requests during which the session will exist.
     *                    - количество следующих запросов при котором сессия будет существовать.
     */
    public static function setFlash(string $name, string|float|int|array|bool|null $data, int $repeat = 1): void
    {
        if (self::$replace) {
            self::$replace->setFlash($name, $data, $repeat);
        } else {
            BaseContainer::instance()->get(SessionInterface::class)->setFlash($name, $data, $repeat);
        }
    }

    /**
     * Returns data from the active Flash session, but not the new one established in the current request.
     * If the session is new or not found, returns null.
     * To obtain data from all sessions, including new ones, use the method:
     *
     * Возвращает данные активной flash-сессии, но не новой, установленной в текущем запросе.
     * Если сессия новая или не найдена, возвращает null.
     * Для получения данных всех сессий и новых в том числе, используйте метод:
     *
     * @see self::allFlash()
     *
     */
    public static function getFlash(string $name, string|float|int|array|bool|null $default = null): string|float|int|array|bool|null
    {
        if (self::$replace) {
            return self::$replace->getFlash($name, $default);
        }

        return BaseContainer::instance()->get(SessionInterface::class)->getFlash($name, $default);
    }

    /**
     * Obtaining a specific flash session and then deleting this data from the current session.
     *
     * Получение конкретной flash-сессии с последующим удалением этих данных из текущей сессии.
     */
    public function getAndClearFlash(string $name, string|float|int|array|bool|null $default = null): string|float|int|array|bool|null
    {
        if (self::$replace) {
            return self::$replace->getAndClearFlash($name, $default);
        }

        return BaseContainer::instance()->get(SessionInterface::class)->getAndClearFlash($name, $default);
    }

    /**
     * Checking the existence of a flash session.
     * Types can be: `all` - all available, `new` - new, installed in the current request
     * and `old` - active, installed in the previous request.
     *
     * Проверка существования flash-сессии.
     * Типы могут быть: `all` - все имеющиеся, `new` - новые, установленные в текущем запросе
     * и `old` - активные, установленные в прошлом запросе.
     */
    public static function hasFlash(string $name, string $type = 'old'): bool
    {
        if (self::$replace) {
            return self::$replace->hasFlash($name);
        }

        return BaseContainer::instance()->get(SessionInterface::class)->hasFlash($name);
    }

    /**
     * Deletes all existing flash sessions.
     *
     * Удаляет все существующие flash-сессии.
     *
     * @see self::setFlash()
     */
    public static function clearFlash(): void
    {
        if (self::$replace) {
            self::$replace->clearFlash();
        } else {
            BaseContainer::instance()->get(SessionInterface::class)->clearFlash();
        }
    }

    /**
     * Returns an array with data from all current flash sessions.
     * The array contains arrays containing data for the session,
     * if it was installed in the last request (old),
     * in the current request (new) and the remaining number
     * of repetitions (reps_left).
     *
     * Возвращает массив с данными всех текущих flash-сессий.
     * Массив содержит массивы, в которых указаны данные для сессии,
     * если она установлена в прошлом запросе (old),
     * в текущем запросе (new) и оставшееся кол-во повторов (reps_left).
     */
    public static function allFlash(): array
    {
        if (self::$replace) {
            return self::$replace->allFlash();
        }

        return BaseContainer::instance()->get(SessionInterface::class)->allFlash();
    }

    /**
     * Increases the value of the session with the specified name by the `amount`.
     * Thus, it can be used as a counter that works for different user requests.
     * The value type of a session with this name must be numeric.
     *
     * Увеличивает значение сессии с указанным названием на число `amount`.
     * Таким образом, можно использовать как счетчик, работающий при разных запросах пользователя.
     * Тип значения сессии с таким названием должен быть числовым.
     */
    public static function increment(string $name, int $amount = 1): void
    {
        if (self::$replace) {
            self::$replace->increment($name, $amount);
        } else {
            BaseContainer::instance()->get(SessionInterface::class)->increment($name, $amount);
        }
    }

    /**
     * Decreases the value of the session with the specified name by the number `amount`.
     * Thus, it can be used as a rollback of a counter that works for different user requests.
     * The value type of a session with this name must be numeric.
     *
     * Уменьшает значение сессии с указанным названием на число `amount`.
     * Таким образом, можно использовать как откат счетчика, работающего при разных запросах пользователя.
     * Тип значения сессии с таким названием должен быть числовым.
     */
    public static function decrement(string $name, int $amount = 1): void
    {
        if (self::$replace) {
            self::$replace->decrement($name, $amount);
        } else {
            BaseContainer::instance()->get(SessionInterface::class)->decrement($name, $amount);
        }
    }

    /**
     * Adds the number `amount` to the value of the session with the specified name,
     * which can be either positive or negative.
     * Thus, it can be used as a counter that works for different user requests.
     * The value type of a session with this name must be numeric.
     *
     * Добавляет к значению сессии с указанным названием число `amount`,
     * которое может быть как положительным, так и отрицательным.
     * Таким образом, можно использовать как счетчик, работающий при разных запросах пользователя.
     * Тип значения сессии с таким названием должен быть числовым.
     */
    public static function counter(string $name, int $amount): void
    {
        if (self::$replace) {
            self::$replace->counter($name, $amount);
        } else {
            BaseContainer::instance()->get(SessionInterface::class)->counter($name, $amount);
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
