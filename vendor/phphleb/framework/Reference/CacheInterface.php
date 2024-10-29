<?php

namespace Hleb\Reference;

/**
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface CacheInterface
{
    /**
     * Returns the result of caching a string value.
     * The method supports PSR-16.
     * The TTL time is specified in seconds.
     *
     * Возвращает результат кеширования произвольного значения.
     * Метод поддерживает PSR-16.
     * Время TTL указывается в секундах.
     *
     */
    public function set(string $key, mixed $value, int $ttl): bool;

    /**
     * Returns the result of caching a string value.
     * The TTL time is specified in seconds.
     *
     * Возвращает результат кеширования строкового значения.
     * Время TTL указывается в секундах.
     */
    public function setString(string $key, string $value, int $ttl): bool;

    /**
     * Returns the result of caching an array.
     * The TTL time is specified in seconds.
     *
     * Возвращает результат кеширования массива.
     * Время TTL указывается в секундах.
     */
    public function setList(string $key, array $value, int $ttl): bool;

    /**
     * Returns the result of object caching (serialization).
     * The TTL time is specified in seconds.
     *
     * Возвращает результат кеширования объекта (сериализация).
     * Время TTL указывается в секундах.
     */
    public function setObject(string $key, object $value, int $ttl): bool;

    /**
     * Replaces the standard set of cache operations.
     * Checks if the value is in the cache and returns the result,
     * otherwise caches the result of the function execution and returns the value.
     * For example:
     *
     * Заменяет стандартный набор операций с кешем.
     * Проверяет наличие значения в кеше и возвращает результат,
     * в противном случае кеширует результат выполнения функции и возвращает значение.
     * Например:
     *
     *  $x = mt_rand();
     *  $data = $cache->getConform('test', function() use ($x) { return round($x / 10); }, 30);
     */
    public function getConform(string $key, callable $func, int $ttl): mixed;

    /**
     * Returns a cached value of an arbitrary type.
     * The method supports PSR-16.
     *
     * Возвращает кешированное значение произвольного типа.
     * Метод поддерживает PSR-16.
     */
    public function get(string $key, mixed $default): mixed;

    /**
     * Returns the previously given cache by key,
     * removing it from the cache.
     *
     * Возвращает ранее заданный кеш по ключу
     * с удалением его из кеша.
     */
    public function getDel(string $key, mixed $default): mixed;

    /**
     * Returns the previously set cache as a string,
     * or false (if no such cache is found or expired).
     *
     * Возвращает ранее установленный кеш в виде строки
     * или false (если такой кеш не найден или просрочен).
     */
    public function getString(string $key, string|false $default): string|false;

    /**
     * Returns the previously specified cache by key as a string,
     * removing it from the cache.
     *
     * Возвращает ранее заданный кеш по ключу в виде строки
     * с удалением его из кеша.
     */
    public function getStringDel(string $key, string|false $default): string|false;

    /**
     * Returns the previously set cache as an array,
     * or false (if no such cache is found or expired).
     *
     * Возвращает ранее установленный кеш в виде массива
     * или false (если такой кеш не найден или просрочен).
     */
    public function getList(string $key, array|false $default): array|false;

    /**
     * Returns the previously specified cache by key as a array,
     * removing it from the cache.
     *
     * Возвращает ранее заданный кеш по ключу в виде массива
     * с удалением его из кеша.
     */
    public function getListDel(string $key, array|false $default): string|false;

    /**
     * Returns the previously set cache as an object,
     * or false (if no such cache is found or expired).
     *
     * Возвращает ранее установленный кеш в виде объекта
     * или false (если такой кеш не найден или просрочен).
     */
    public function getObject(string $key, object|false $default): object|false;

    /**
     * Returns the previously specified cache by key as a object,
     * removing it from the cache.
     *
     * Возвращает ранее заданный кеш по ключу в виде объекта
     * с удалением его из кеша.
     */
    public function getObjectDel(string $key, object|false $default): object|false;

    /**
     * Returns a set of cached data according to an array of keys.
     * The method supports PSR-16.
     *
     * Возвращает набор кешированных данных согласно массиву ключей.
     * Метод поддерживает PSR-16.
     */
    public function getMultiple(array $keys, mixed $default = null): array;

    /**
     * Cache assignment by list key => value.
     * The method supports PSR-16.
     * The TTL time is specified in seconds.
     *
     * Назначение кеша по списку ключ => значение.
     * Метод поддерживает PSR-16.
     * Время TTL указывается в секундах.
     */
    public function setMultiple(array $values, int $ttl): bool;

    /**
     * Deleting the cache by the list of keys.
     * The method supports PSR-16.
     *
     * Удаление кеша по списку ключей.
     * Метод поддерживает PSR-16.
     */
    public function deleteMultiple(array $values): bool;

    /**
     * Clears the cache by key.
     * The method supports PSR-16.
     *
     * Очищает кеш по ключу.
     * Метод поддерживает PSR-16.
     */
    public function delete(string $key): bool;

    /**
     * Checking the existence of the cache by key.
     * The method supports PSR-16.
     *
     * Проверка существования кеша по ключу.
     * Метод поддерживает PSR-16.
     */
    public function has(string $key): bool;

    /**
     * Checking the existence of the cache by key.
     *
     * Проверка существования кеша по ключу.
     */
    public function isExists(string $key): bool;

    /**
     * Returns the remaining cache time in seconds,
     * or false (if no such cache is found or expired).
     *
     * Возвращает оставшееся время действия кеша в секундах
     * или false (если такой кеш не найден или просрочен).
     */
    public function getExpire(string $key): int|false;

    /**
     * Sets the new duration for an existing cache to expire.
     * The TTL time is specified in seconds.
     *
     * Задаёт новую продолжительность срока действия существующего кеша.
     * Время TTL указывается в секундах.
     */
    public function setExpire(string $key, int $ttl): bool;

    /**
     * Returns the total number of cache units stored.
     *
     * Возвращает общее количество сохраненных единиц кеша.
     */
    public function count(): int;

    /**
     * Complete removal of the cache.
     * The method supports PSR-16.
     *
     * Полное удаление кеша.
     * Метод поддерживает PSR-16.
     */
    public function clear(): bool;

    /**
     * Complete removal of expired cache.
     *
     * Полное удаление просроченного кеша.
     */
    public function clearExpired(): void;
}
