<?php

namespace Hleb\Reference;

use Hleb\HttpMethods\Specifier\DataType;

/**
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface CookieInterface
{
    /**
     * Returns a named array of objects with the current Cookies values.
     *
     * Возвращает именованный массив объектов с текущими значениями Cookies.
     *
     * @return DataType[]
     */
    public function all(): array;

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
    public function set(string $name, string $value = '', array $options = []): void;

    /**
     * Getting the Cookie value by name.
     * Returns a gettable object according to different types.
     *
     * Получение значения Cookie по имени.
     * Возвращает объект с возможностью получения значения согласно различным типам.
     */
    public function get(string $name): DataType;

    /**
     * Sets the name for the session cookies. The previously set value will be deleted.
     *
     * Устанавливает название для сессионной Cookie. Ранее установленное значение будет удалено.
     */
    public function setSessionName(string $name): void;

    /**
     * Returns the name of the session cookie.
     *
     * Возвращает название сессионной Cookie.
     */
    public function getSessionName(): string;

    /**
     * Sets a new session ID.
     *
     * Устанавливает новый идентификатор сессии.
     */
    public function setSessionId(string $id): void;

    /**
     * Returns the current cookie session ID.
     *
     * Возвращает текущий идентификатор сессии Cookie.
     */
    public function getSessionId(): string;

    /**
     * Deleting a specific Cookie.
     *
     * Удаление конкретной Cookie.
     */
    public function delete(string $name): void;

    /**
     * Deleting all previously set Cookies.
     *
     * Удаление всех ранее установленных Cookies.
     */
    public function clear(): void;

    /**
     * Checks the existence of a cookie with a specific name.
     *
     * Проверяет наличие cookie с конкретным названием.
     */
    public function has(string $name): bool;

    /**
     * Checks a cookie with a specific name.
     * If the cookie does not exist or the value is an empty string, it will return false.
     *
     * Проверяет cookie с конкретным названием.
     * Если cookie не существует или значение пустая строка, то вернет false.
     */
    public function exists(string $name): bool;
}
