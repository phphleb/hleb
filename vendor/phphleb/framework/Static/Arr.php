<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Init\ShootOneselfInTheFoot\ArrForTest;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\ArrInterface;

/**
 * Methods for simplified work with arrays.
 * Some methods using 'dot notation' are found
 * in Laravel and can be slow to execute.
 * Standard methods of working with arrays
 * do not need to be replaced with a mock object,
 * but this may be necessary when changing
 * the implementation in the container.
 *
 * Методы для упрощённой работы с массивами.
 * Некоторые методы, использующие 'точечную нотацию',
 * подсмотрены в Laravel и могут выполняться медленно.
 * Стандартные методы работы с массивами не нуждаются
 * в подмене на мок-объект, но это может понадобиться
 * при изменении реализации в контейнере.
 */
#[Accessible]
final class Arr extends BaseSingleton
{
    private static ArrInterface|null $replace = null;

    /**
     * Checking if an array is associative.
     * If the array is empty, returns false.
     *
     * Проверка, что массив ассоциативный.
     * При пустом массиве возвращает false.
     */
    public static function isAssoc(array $array): bool
    {
        if (self::$replace) {
            return self::$replace->isAssoc($array);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->isAssoc($array);
    }

    /**
     * Merging two arrays, in which the named values of the first
     * will be replaced by values from the second.
     *
     * Слияние двух массивов, при котором именованные значения первого
     * будут заменены значениями из второго.
     */
    public static function append(array $original, array $complement): array
    {
        if (self::$replace) {
            return self::$replace->append($original, $complement);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->append($original, $complement);
    }

    /**
     * Returns an array of arrays sorted in descending order by the value
     * of the specified field in each nested array.
     *
     * Возвращает массив массивов отсортированный по убыванию значения
     * определенного поля в каждом вложенном в него массиве.
     */
    public static function sortDescByField(array $list, string $field): array
    {
        if (self::$replace) {
            return self::$replace->sortDescByField($list, $field);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->sortDescByField($list, $field);
    }

    /**
     * Returns an array of arrays sorted in ascending order by the value
     * of the specified field in each nested array.
     *
     * Возвращает массив массивов отсортированный по возрастанию значения
     * определенного поля в каждом вложенном в него массиве.
     */
    public static function sortAscByField(array $array, string $field): array
    {
        if (self::$replace) {
            return self::$replace->sortAscByField($array, $field);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->sortAscByField($array, $field);
    }

    /**
     * In the source array, rearranges the value with the required key to the first place.
     *
     * В исходном массиве переставляет значение с необходимым ключом на первое место.
     *
     * @param array $array - snowflake array from snow.
     *                     - оригинальный массив с данными.
     *
     * @param string $key - the key of the array, rearranged to the first place of the data.
     *                     - ключ данных, переставляемых на первое место.
     *
     * @param bool $strict - throw an error if there is no key in the array.
     *                     - при отсутствии ключа в массиве выбрасывать ошибку.
     */
    public static function moveToFirst(array $array, string $key, bool $strict = true): array
    {
        if (self::$replace) {
            return self::$replace->moveToFirst($array, $key, $strict);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->moveToFirst($array, $key, $strict);
    }

    /**
     * Returns a new array with only the listed $keys from $array selected.
     *
     * Возвращает новый массив, где выбраны только перечисленные ключи $keys из $array.
     */
    public static function only(array $array, array $keys): array
    {
        if (self::$replace) {
            return self::$replace->only($array, $keys);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->only($array, $keys);
    }

    /**
     * Returns an array of two arrays: one containing the keys
     * and the other containing the value of the passed array.
     *
     * Возвращает массив двух массивов: один содержит ключи,
     * а другой – значения переданного массива.
     */
    public static function divide(array $array): array
    {
        if (self::$replace) {
            return self::$replace->divide($array);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->divide($array);
    }

    /**
     * Returns a value from a nested array using "dot notation".
     * If the key is not in the array, $default will be returned.
     * Usage example:
     *
     * Возвращает значение из вложенного массива, используя "точечную нотацию".
     * Если ключ в массиве отсутствует, то будет возвращено $default.
     * Пример использования:
     *
     * ```php
     *
     * $array = ['products' => ['desk' => ['price' => 100]]];
     *
     * $price = Arr::get($array, 'products.desk.price'); // 100
     *
     * $price = Arr::get($array, 'products.desk', 0); // 0
     * ```
     */
    public static function get(array $array, int|string|null $key, mixed $default = null): mixed
    {
        if (self::$replace) {
            return self::$replace->get($array, $key, $default);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->get($array, $key, $default);
    }

    /**
     *
     * Removing one or more matches by key(s) from a multidimensional
     * array according to "dot notation".
     * Usage example:
     *
     * Удаление одного или более совпадений по ключу(ключам)
     * из многомерного массива согласно "точечной нотации".
     * Пример использования:
     *
     * ```php
     *
     * $array = ['products' => ['desk' => ['price' => 100]]];
     *
     * Arr::forget($array, 'products.desk');
     *
     * // ['products' => []]
     * ```
     */
    public static function forget(array &$array, array|string|int|float $keys): void
    {
        if (self::$replace) {
            self::$replace->forget($array, $keys);
        } else {
            BaseContainer::instance()->get(ArrInterface::class)->forget($array, $keys);
        }
    }

    /**
     * Returns the result of a test for the presence of a key(s)
     * in a multidimensional array according to "dot notation".
     * Usage example:
     *
     * Возвращает результат проверки на присутствие ключа(ключей)
     * в многомерном массиве согласно "точечной нотации".
     * Пример использования:
     *
     * ```php
     *
     * $array = ['product' => ['name' => 'Desk', 'price' => 100]];
     *
     * $contains = Arr::has($array, 'product.name'); // true
     *
     * $contains = Arr::has($array, ['product.price', 'product.discount']); // false
     * ```
     */
    public static function has(array $array, string|array $keys): bool
    {
        if (self::$replace) {
            return self::$replace->has($array, $keys);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->has($array, $keys);
    }

    /**
     * Adds the specified key/value pair to the array if the specified key,
     * according to dot notation, does not already exist in the array or is null.
     * Usage example:
     *
     * Добавляет заданную пару ключ/значение в массив, если указанный ключ
     * согласно "точечной нотации" еще не существует в массиве или равен null.
     * Пример использования:
     *
     * ```php
     *
     * $array = Arr::add(['name' => 'Table'], 'price', 100);
     * // ['name' => 'Table', 'price' => 100]
     *
     * $array = Arr::add(['name' => 'Table', 'price' => null], 'price', 100);
     * // ['name' => 'Table', 'price' => 100]
     * ```
     */
    public static function add(array $array, string|int|float $key, mixed $value): array
    {
        if (self::$replace) {
            return self::$replace->add($array, $key, $value);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->add($array, $key, $value);
    }

    /**
     * Sets the value by key according to "dot notation".
     * If no key is given to the method, the entire array will be replaced.
     * Usage example:
     *
     * Устанавливает значение по ключу согласно "точечной нотации".
     * Если методу не присвоен ключ, будет заменён весь массив.
     * Пример использования:
     *
     * ```php
     *
     * $array = ['products' => ['desk' => ['price' => 100]]];
     *
     * Arr::set($array, 'products.desk.price', 200);
     *
     * // ['products' => ['desk' => ['price' => 200]]]
     * ```
     */
    public static function set(array &$array, string|int|null $key, mixed $value): array
    {
        if (self::$replace) {
            return self::$replace->set($array, $key, $value);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->set($array, $key, $value);
    }

    /**
     * Returns a multidimensional array expanded from
     * a one-dimensional array using "dot notation".
     * Usage example:
     *
     * Возвращает многомерный массив, расширенный
     * из одномерного по "точечной нотации".
     * Пример использования:
     *
     * ```php
     *
     * $array = [
     *      'user.name' => 'Sherlock Holmes',
     *      'user.occupation' => 'detective',
     *   ];
     *
     * $array = Arr::expand($array);
     *
     * // ['user' => ['name' => 'Sherlock Holmes', 'occupation' => 'detective']]
     * ```
     */
    public static function expand(iterable $array): array
    {
        if (self::$replace) {
            return self::$replace->expand($array);
        }

        return BaseContainer::instance()->get(ArrInterface::class)->expand($array);
    }

    /**
     * @internal
     *
     * @see ArrForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(ArrInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
