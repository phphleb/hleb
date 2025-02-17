<?php

namespace Hleb\Reference;

interface SessionInterface
{
    /**
     * Returns an array with current session data.
     *
     * Возвращает массив с данными текущей сессии.
     */
    public function all(): array;

    /**
     * Returns session data by parameter name.
     *
     * Возвращает данные сессии по названию параметра.
     */
    public function get(string|int $name, mixed $default = null): mixed;

    /**
     * Returns the current session identifier.
     *
     * Возвращает идентификатор текущей сессии.
     */
    public function getSessionId(): string|null;


    /**
     * Assigns session data by parameter name.
     *
     * Присваивает данные сессии по названию параметра.
     */
    public function set(string|int $name, string|float|int|array|bool|null $data): void;

    /**
     * Deleting session data by parameter name
     *
     * Удаление данных сессии по названию параметра.
     */
    public function delete(string|int $name): void;

    /**
     * Clears all session data.
     *
     * Очищает все данные сессии.
     */
    public function clear(): void;

    /**
     * Checks the existence of a session with a specific name.
     *
     * Проверяет наличие сессии с конкретным названием.
     */
    public function has(string|int $name): bool;

    /**
     * Checks a session with a specific name.
     * If the session does not exist or the value is null or the empty string, it will return false.
     *
     * Проверяет сессию с конкретным названием.
     * Если сессия не существует или значение равно null или пустой строке, то вернет false.
     */
    public function exists(string|int $name): bool;

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
    public function setFlash(string $name, string|float|int|array|bool|null $data, int $repeat = 1): void;

    /**
     * Returns data from the active Flash session, but not the new one established in the current request.
     * If the session is new or not found, returns null.
     * If the session is new or not found, returns null or the default value.
     * To obtain data from all sessions, including new ones, use the method:
     *
     * Возвращает данные активной flash-сессии, но не новой, установленной в текущем запросе.
     * Если сессия новая или не найдена, возвращает null или дефолтное значение.
     * Для получения данных всех сессий и новых в том числе, используйте метод:
     *
     * @see self::allFlash()
     *
     */
    public function getFlash(string $name, string|float|int|array|bool|null $default = null): string|float|int|array|bool|null;

    /**
     * Obtaining a specific flash session and then deleting this data from the current session.
     *
     * Получение конкретной flash-сессии с последующим удалением этих данных из текущей сессии.
     */
    public function getAndClearFlash(string $name, string|float|int|array|bool|null $default = null): string|float|int|array|bool|null;

    /**
     * Checking the existence of a flash session.
     * Types can be: `all` - all available, `new` - new, installed in the current request
     * and `old` - active, installed in the previous request.
     *
     * Проверка существования flash-сессии.
     * Типы могут быть: `all` - все имеющиеся, `new` - новые, установленные в текущем запросе
     * и `old` - активные, установленные в прошлом запросе.
     */
    public function hasFlash(string $name, string $type = 'old'): bool;

    /**
     * Deletes all existing flash sessions.
     *
     * Удаляет все существующие flash-сессии.
     *
     * @see self::setFlash()
     */
    public function clearFlash(): void;

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
    public function allFlash(): array;

    /**
     * Increases the value of the session with the specified name by the `amount`.
     * Thus, it can be used as a counter that works for different user requests.
     * The value type of a session with this name must be numeric.
     *
     * Увеличивает значение сессии с указанным названием на число `amount`.
     * Таким образом, можно использовать как счетчик, работающий при разных запросах пользователя.
     * Тип значения сессии с таким названием должен быть числовым.
     */
    public function increment(string $name, int $amount = 1): void;

    /**
     * Decreases the value of the session with the specified name by the number `amount`.
     * Thus, it can be used as a rollback of a counter that works for different user requests.
     * The value type of a session with this name must be numeric.
     *
     * Уменьшает значение сессии с указанным названием на число `amount`.
     * Таким образом, можно использовать как откат счетчика, работающего при разных запросах пользователя.
     * Тип значения сессии с таким названием должен быть числовым.
     */
    public function decrement(string $name, int $amount = 1): void;

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
    public function counter(string $name, int $amount): void;
}
