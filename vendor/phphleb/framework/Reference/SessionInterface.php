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
    public function get(string|int $name): mixed;

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
     * Used if you need to rollback data
     * for an asynchronous request.
     *
     * Используется, если необходимо откатить
     * данные для асинхронного запроса.
     */
    public static function rollback(): void;
}
