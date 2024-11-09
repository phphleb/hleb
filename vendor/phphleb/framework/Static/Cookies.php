<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Init\ShootOneselfInTheFoot\CookiesForTest;
use Hleb\HttpMethods\Specifier\DataType;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\CookieInterface;

/**
 * Class for working with project Cookies. If another method is used to set the cookies,
 * for example, setcookie(), then this method will not be able to return the correct array of all
 * used cookies. Therefore, you need to use only one of the options.
 * To improve performance, processing Cookies is divided into asynchronous and regular types.
 *
 * Класс для работы с Cookie проекта. Если используется другой метод для установки Cookie,
 * например, setcookie(), то данный способ не сможет вернуть правильный массив всех
 * использованных Cookie. Поэтому нужно использовать только один из вариантов.
 * Для улучшения производительности обработка Cookies разделена на асинхронный и обычный типы.
 */
#[Accessible]
final class Cookies extends BaseSingleton
{
    final public const OPTION_KEYS = ['expires', 'path', 'domain', 'secure', 'httponly', 'samesite'];

    final public const SAMESITE_VALUES = ['Strict', 'None', 'Lax'];

    private static CookieInterface|null $replace = null;

    /**
     * Setting a value for a Cookie is similar to setcookie()
     *
     * Установка значения для Cookie аналогично setcookie()
     *
     * @param array $options - setting values with possible keys:
     *                       - установка значений с возможными ключами:
     *  ('expires', 'path', 'domain', 'secure', 'httponly', 'samesite').
     *
     * @see setcookie()
     *
     */
    public static function set(string $name, string $value = '', array $options = []): void
    {
        if (self::$replace) {
            self::$replace->set($name, $value, $options);
        } else {
            BaseContainer::instance()->get(CookieInterface::class)->set($name, $value, $options);
        }
    }

    /**
     * Getting the Cookie value by name.
     * Returns a gettable object according to different types.
     *
     * Получение значения Cookie по имени.
     * Возвращает объект с возможностью получения значения согласно различным типам.
     */
    public static function get(string $name): DataType
    {
        if (self::$replace) {
            return self::$replace->get($name);
        }

        return BaseContainer::instance()->get(CookieInterface::class)->get($name);
    }

    /**
     * Returns a named array of objects with the current Cookies values.
     *
     * Возвращает именованный массив объектов с текущими значениями Cookies.
     *
     * @return DataType[]
     */
    public static function all(): array
    {
        if (self::$replace) {
            return self::$replace->all();
        }

        return BaseContainer::instance()->get(CookieInterface::class)->all();
    }

    /**
     * Sets the name for the session cookies. The previously set value will be deleted.
     *
     * Устанавливает название для сессионной Cookie. Ранее установленное значение будет удалено.
     */
    public static function setSessionName(string $name): void
    {
        if (self::$replace) {
            self::$replace->setSessionName($name);
        } else {
            BaseContainer::instance()->get(CookieInterface::class)->setSessionName($name);
        }
    }

    /**
     * Sets the name for the session cookies. The previously set value will be deleted.
     *
     * Устанавливает название для сессионной Cookie. Ранее установленное значение будет удалено.
     */
    public static function getSessionName(): string
    {
        if (self::$replace) {
            return self::$replace->getSessionName();
        }

        return BaseContainer::instance()->get(CookieInterface::class)->getSessionName();
    }

    /**
     * Sets a new session ID.
     *
     * Устанавливает новый идентификатор сессии.
     */
    public static function setSessionId(string $id): void
    {
        if (self::$replace) {
            self::$replace->setSessionId($id);
        } else {
            BaseContainer::instance()->get(CookieInterface::class)->setSessionId($id);
        }
    }

    /**
     * Returns the current session cookie identifier.
     *
     * Возвращает текущий индетификатор сессионной Cookie.
     */
    public static function getSessionId(): string
    {
        if (self::$replace) {
            return self::$replace->getSessionId();
        }

        return BaseContainer::instance()->get(CookieInterface::class)->getSessionId();
    }

    /**
     * Deleting a specific Cookie.
     *
     * Удаление конкретной Cookie.
     */
    public static function delete(string $name): void
    {
        if (self::$replace) {
            self::$replace->delete($name);
        } else {
            BaseContainer::instance()->get(CookieInterface::class)->delete($name);
        }
    }

    /**
     * Deleting all previously set Cookies.
     *
     * Удаление всех ранее установленных Cookies.
     */
    public static function clear(): void
    {
        if (self::$replace) {
            self::$replace->clear();
        } else {
            BaseContainer::instance()->get(CookieInterface::class)->clear();
        }
    }

    /**
     * Checks the existence of a cookie with a specific name.
     *
     * Проверяет наличие cookie с конкретным названием.
     */
    public function has(string $name): bool
    {
        if (self::$replace) {
            return self::$replace->has($name);
        }

        return BaseContainer::instance()->get(CookieInterface::class)->has($name);
    }

    /**
     * Checks a cookie with a specific name.
     * If the cookie does not exist or the value is an empty string, it will return false.
     *
     * Проверяет cookie с конкретным названием.
     * Если cookie не существует или значение пустая строка, то вернет false.
     */
    public function exists(string $name): bool
    {
        if (self::$replace) {
            return self::$replace->exists($name);
        }

        return BaseContainer::instance()->get(CookieInterface::class)->exists($name);
    }

    /**
     * @internal
     *
     * @see CookiesForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(CookieInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
