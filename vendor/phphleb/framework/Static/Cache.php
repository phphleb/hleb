<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Init\ShootOneselfInTheFoot\CacheForTest;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\CacheInterface;

/**
 * Caching data of various types in the framework.
 *
 * Кеширование данных различного типа во фреймворке.
 */
#[Accessible]
final class Cache extends BaseSingleton
{
    final public const DEFAULT_TIME = 60;

    private static CacheInterface|null $replace = null;

    /**
     * Returns the result of caching a string value.
     * The method supports PSR-16.
     * The TTL time is specified in seconds.
     *
     * Возвращает результат кеширования произвольного значения.
     * Метод поддерживает PSR-16.
     * Время TTL указывается в секундах.
     */
    public static function set(string $key, mixed $value, int $ttl = self::DEFAULT_TIME): bool
    {
        if (self::$replace) {
            return self::$replace->set($key, $value, $ttl);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->set($key, $value, $ttl);
    }

    /**
     * Returns the result of caching a string value.
     * The TTL time is specified in seconds.
     *
     * Возвращает результат кеширования строкового значения.
     * Время TTL указывается в секундах.
     */
    public static function setString(string $key, string $value, int $ttl = self::DEFAULT_TIME): bool
    {
        if (self::$replace) {
            return self::$replace->setString($key, $value, $ttl);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->setString($key, $value, $ttl);
    }

    /**
     * Returns the result of caching an array.
     * The TTL time is specified in seconds.
     *
     * Возвращает результат кеширования массива.
     * Время TTL указывается в секундах.
     */
    public static function setList(string $key, array $value, int $ttl = self::DEFAULT_TIME): bool
    {
        if (self::$replace) {
            return self::$replace->setList($key, $value, $ttl);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->setList($key, $value, $ttl);
    }

    /**
     * Returns the result of object caching (serialization).
     * The TTL time is specified in seconds.
     *
     * Возвращает результат кеширования объекта (сериализация).
     * Время TTL указывается в секундах.
     */
    public static function setObject(string $key, object $value, int $ttl = self::DEFAULT_TIME): bool
    {
        if (self::$replace) {
            return self::$replace->setObject($key, $value, $ttl);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->setObject($key, $value, $ttl);
    }

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
    public static function getConform(string $key, callable $func, int $ttl = self::DEFAULT_TIME): mixed
    {
        if (self::$replace) {
            return self::$replace->getConform($key, $func, $ttl);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getConform($key, $func, $ttl);
    }

    /**
     * Returns a cached value of an arbitrary type.
     * The method supports PSR-16.
     *
     * Возвращает кешированное значение произвольного типа.
     * Метод поддерживает PSR-16.
     */
    public static function get(string $key, mixed $default = false): mixed
    {
        if (self::$replace) {
            return self::$replace->get($key, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->get($key, $default);
    }

    /**
     * Returns the previously given cache by key,
     * removing it from the cache.
     *
     * Возвращает ранее заданный кеш по ключу
     * с удалением его из кеша.
     */
    public static function getDel(string $key, mixed $default = false): mixed
    {
        if (self::$replace) {
            return self::$replace->getDel($key, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getDel($key, $default);
    }

    /**
     * Returns the previously set cache as a string,
     * or false (if no such cache is found or expired).
     *
     * Возвращает ранее установленный кеш в виде строки
     * или false (если такой кеш не найден или просрочен).
     */
    public static function getString(string $key, string|false $default = false): string|false
    {
        if (self::$replace) {
            return self::$replace->getString($key, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getString($key, $default);
    }

    /**
     * Returns the previously specified cache by key as a string,
     * removing it from the cache.
     *
     * Возвращает ранее заданный кеш по ключу в виде строки
     * с удалением его из кеша.
     */
    public static function getStringDel(string $key, string|false $default = false): string|false
    {
        if (self::$replace) {
            return self::$replace->getStringDel($key, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getStringDel($key, $default);
    }

    /**
     * Returns the previously set cache as an array,
     * or false (if no such cache is found or expired).
     *
     * Возвращает ранее установленный кеш в виде массива
     * или false (если такой кеш не найден или просрочен).
     */
    public static function getList(string $key, array|false $default = false): array|false
    {
        if (self::$replace) {
            return self::$replace->getList($key, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getList($key, $default);
    }

    /**
     * Returns the previously specified cache by key as a array,
     * removing it from the cache.
     *
     * Возвращает ранее заданный кеш по ключу в виде массива
     * с удалением его из кеша.
     */
    public static function getListDel(string $key, array|false $default = false): string|false
    {
        if (self::$replace) {
            return self::$replace->getListDel($key, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getListDel($key, $default);
    }

    /**
     * Returns the previously set cache as an object,
     * or false (if no such cache is found or expired).
     *
     * Возвращает ранее установленный кеш в виде объекта
     * или false (если такой кеш не найден или просрочен).
     */
    public static function getObject(string $key, object|false $default = false): object|false
    {
        if (self::$replace) {
            return self::$replace->getObject($key, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getObject($key, $default);
    }

    /**
     * Returns the previously specified cache by key as a object,
     * removing it from the cache.
     *
     * Возвращает ранее заданный кеш по ключу в виде объекта
     * с удалением его из кеша.
     */
    public static function getObjectDel(string $key, object|false $default = false): object|false
    {
        if (self::$replace) {
            return self::$replace->getObjectDel($key, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getObjectDel($key, $default);
    }

    /**
     * Returns a set of cached data according to an array of keys.
     * The method supports PSR-16.
     *
     * Возвращает набор кешированных данных согласно массиву ключей.
     * Метод поддерживает PSR-16.
     */
    public static function getMultiple(array $keys, mixed $default = null): array
    {
        if (self::$replace) {
            return self::$replace->getMultiple($keys, $default);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getMultiple($keys, $default);
    }

    /**
     * Cache assignment by list key => value.
     * The method supports PSR-16.
     * The TTL time is specified in seconds.
     *
     * Назначение кеша по списку ключ => значение.
     * Метод поддерживает PSR-16.
     * Время TTL указывается в секундах.
     */
    public static function setMultiple(array $values, int $ttl = self::DEFAULT_TIME): bool
    {
        if (self::$replace) {
            return self::$replace->setMultiple($values, $ttl);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->setMultiple($values, $ttl);
    }

    /**
     * Deleting the cache by the list of keys.
     * The method supports PSR-16.
     *
     * Удаление кеша по списку ключей.
     * Метод поддерживает PSR-16.
     */
    public static function deleteMultiple(array $values): bool
    {
        if (self::$replace) {
            return self::$replace->deleteMultiple($values);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->deleteMultiple($values);
    }

    /**
     * Clears the cache by key.
     * The method supports PSR-16.
     *
     * Очищает кеш по ключу.
     * Метод поддерживает PSR-16.
     */
    public static function delete(string $key): bool
    {
        if (self::$replace) {
            return self::$replace->delete($key);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->delete($key);
    }

    /**
     * Checking the existence of the cache by key.
     * The method supports PSR-16.
     *
     * Проверка существования кеша по ключу.
     * Метод поддерживает PSR-16.
     */
    public static function has(string $key): bool
    {
        if (self::$replace) {
            return self::$replace->has($key);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->has($key);
    }

    /**
     * Checking the existence of the cache by key.
     *
     * Проверка существования кеша по ключу.
     */
    public static function isExists(string $key): bool
    {
        if (self::$replace) {
            return self::$replace->isExists($key);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->isExists($key);
    }

    /**
     * Returns the remaining cache time in seconds,
     * or false (if no such cache is found or expired).
     *
     * Возвращает оставшееся время действия кеша в секундах
     * или false (если такой кеш не найден или просрочен).
     */
    public static function getExpire(string $key): int|false
    {
        if (self::$replace) {
            return self::$replace->getExpire($key);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->getExpire($key);
    }

    /**
     * Sets the new duration for an existing cache to expire.
     * The TTL time is specified in seconds.
     *
     * Задаёт новую продолжительность срока действия существующего кеша.
     * Время TTL указывается в секундах.
     */
    public static function setExpire(string $key, int $ttl): bool
    {
        if (self::$replace) {
            return self::$replace->setExpire($key, $ttl);
        }

        return BaseContainer::instance()->get(CacheInterface::class)->setExpire($key, $ttl);
    }

    /**
     * Returns the total number of cache units stored.
     *
     * Возвращает общее количество сохраненных единиц кеша.
     */
    public static function count(): int
    {
        if (self::$replace) {
            return self::$replace->count();
        }

        return BaseContainer::instance()->get(CacheInterface::class)->count();
    }

    /**
     * Complete removal of the cache.
     * The method supports PSR-16.
     *
     * Полное удаление кеша.
     * Метод поддерживает PSR-16.
     */
    public static function clear(): bool
    {
        if (self::$replace) {
            return self::$replace->clear();
        }

        return BaseContainer::instance()->get(CacheInterface::class)->clear();
    }

    /**
     * Complete removal of expired cache.
     * (!) With a large amount of cache, execution may take a long time.
     *
     * Полное удаление просроченного кеша.
     * (!) При большом количестве кеша выполнение может занять длительное время.
     */
    public static function clearExpired(): void
    {
        if (self::$replace) {
            self::$replace->clearExpired();
        } else {
            BaseContainer::instance()->get(CacheInterface::class)->clearExpired();
        }
    }

    /**
     * @internal
     *
     * @see CacheForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(CacheInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
