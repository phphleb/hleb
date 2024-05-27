<?php

declare(strict_types=1);

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;
use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;

/**
 * Methods for simplified work with arrays.
 * Some methods using 'dot notation' are found
 * in Laravel and can be slow to execute.
 *
 * Методы для упрощённой работы с массивами.
 * Некоторые методы, использующие 'точечную нотацию',
 * подсмотрены в Laravel и могут выполняться медленно.
 */
#[Immutable] #[Accessible]
final class ArrayHelper
{
    /**
     * Checking if an array is associative.
     * If the array is empty, returns false.
     *
     * Проверка, что массив ассоциативный.
     * При пустом массиве возвращает false.
     */
    #[Pure]
    public static function isAssoc(array $array): bool
    {
        if (!$array) {
            return false;
        }
        return !\array_is_list($array);
    }

    /**
     * Merging two arrays, in which the named values of the first
     * will be replaced by values from the second.
     *
     * Слияние двух массивов, при котором именованные значения первого
     * будут заменены значениями из второго.
     */
    #[Pure]
    public static function append(array $original, array $complement): array
    {
        $result = [];
        foreach ($original as $key => $value) {
            if (\is_int($key)) {
                return $original;
            }
            if (!\array_key_exists($key, $complement)) {
                $result[$key] = $value;
                continue;
            }
            if (\is_array($value)) {
                $result[$key] = self::append($value, $complement[$key]);
                continue;
            }
            $result[$key] = $complement[$key];
        }
        return $result;
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
        \usort($list, static function ($a, $b) use ($field) {
            if (\is_numeric($a[$field])) {
                $a[$field] = (string)$a[$field];
            }
            if (\is_numeric($b[$field])) {
                $b[$field] = (string)$b[$field];
            }
            return \strnatcmp($b[$field], $a[$field]);
        });
        return $list;
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
        \usort($array, static function ($a, $b) use ($field) {
            if (\is_numeric($a[$field])) {
                $a[$field] = (string)$a[$field];
            }
            if (\is_numeric($b[$field])) {
                $b[$field] = (string)$b[$field];
            }
            return \strnatcmp($a[$field], $b[$field]);
        });
        return $array;
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
        if (!$array) {
            return [];
        }
        if (!\array_key_exists($key, $array)) {
            $strict and throw new \RuntimeException('`' . $key . '` key not found in array');
            return $array;
        }
        $isAssoc = self::isAssoc($array);
        $value = $array[$key];
        unset($array[$key]);

        $result = \array_merge([$key => $value], $array);
        if ($isAssoc) {
            return $result;
        }
        return \array_values($result);
    }

    /**
     * Returns a new array with only the listed $keys from $array selected.
     *
     * Возвращает новый массив, где выбраны только перечисленные ключи $keys из $array.
     */
    #[Pure]
    public static function only(array $array, array $keys): array
    {
        $result = \array_intersect_key($array, \array_flip($keys));

        return self::isAssoc($array) ? $result : \array_values($result);
    }

    /**
     * Returns an array of two arrays: one containing the keys
     * and the other containing the value of the passed array.
     *
     * Возвращает массив двух массивов: один содержит ключи,
     * а другой – значения переданного массива.
     */
    #[Pure]
    public static function divide(array $array): array
    {
        return [\array_keys($array), \array_values($array)];
    }

    /**
     * Returns a value from a nested array using "dot notation".
     * If the key is not in the array, $default will be returned.
     * Usage example:
     *
     * Возвращает значение из вложенного массива, используя "точечную нотацию".
     * Если ключ в массиве отсутствует, то будет возвращено $default.
     * Пример использования:
     * ```php
     *
     * $array = ['products' => ['desk' => ['price' => 100]]];
     *
     * $price = ArrayHelper::get($array, 'products.desk.price'); // 100
     *
     * $price = ArrayHelper::get($array, 'products.desk.example', 0); // 0
     *
     * ```
     */
    public static function get(array $array, int|string|null $key, mixed $default = null): mixed
    {
        if ($key === null || !$array) {
            return $default;
        }
        if (\array_key_exists($key, $array)) {
            return $array[$key];
        }
        if (!\str_contains((string)$key, '.')) {
            return $array[$key] ?? $default;
        }
        foreach (\explode('.', $key) as $part) {
            if (\is_array($array) && \array_key_exists($part, $array)) {
                $array = $array[$part];
            } else {
                return $default;
            }
        }

        return $array;
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
     * ArrayHelper::forget($array, 'products.desk');
     *
     * // ['products' => []]
     * ```
     */
    public static function forget(array &$array, array|string|int $keys): void
    {
        $original = &$array;
        $keys = (array)$keys;
        if (\count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            if (\array_key_exists($key, $array)) {
                unset($array[$key]);
                continue;
            }
            $parts = \explode('.', $key);
            $array = &$original;

            while (\count($parts) > 1) {
                $part = \array_shift($parts);
                if (isset($array[$part]) && \is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[\array_shift($parts)]);
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
     * $r = ArrayHelper::has($array, 'product.name'); // true
     *
     * $r = ArrayHelper::has($array, ['product.price', 'product.discount']); // false
     * ```
     */
    public static function has(array $array, string|array|int $keys): bool
    {
        $keys = (array)$keys;
        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;
            if (\array_key_exists( $key, $array)) {
                continue;
            }
            foreach (\explode('.', $key) as $segment) {
                if (\is_array($subKeyArray) && \array_key_exists($segment, $subKeyArray)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
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
     * $array = ArrayHelper::add(['name' => 'Table'], 'price', 100);
     * // ['name' => 'Table', 'price' => 100]
     *
     * $array = ArrayHelper::add(['name' => 'Table', 'price' => null], 'price', 100);
     * // ['name' => 'Table', 'price' => 100]
     * ```
     */
    public static function add(array $array, string|int $key, mixed $value): array
    {
        if (self::get($array, $key) === null) {
            self::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Sets the value by key according to "dot notation".
     * If no key is given to the method, the entire array will be replaced.
     * Usage example:
     *
     * Устанавливает значение по ключу согласно "точечной нотации".
     * Если методу не присвоен ключ, будет заменён весь массив.
     * Пример использования:
     * ```php
     * $array = ['products' => ['desk' => ['price' => 100]]];
     *
     * ArrayHelper::set($array, 'products.desk.price', 200);
     *
     * // ['products' => ['desk' => ['price' => 200]]]
     * ```
     */
    public static function set(array &$array, string|int|null $key, mixed $value): array
    {
        if ($key === null) {
            return $array = $value;
        }

        $keys = \is_string($key) ? \explode('.', $key) : [$key];

        foreach ($keys as $i => $key) {
            if (\count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            if (!isset($array[$key]) || !\is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }

        $array[\array_shift($keys)] = $value;

        return $array;
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
     * $array = ArrayHelper::expand($array);
     * // ['user' => ['name' => 'Sherlock Holmes', 'occupation' => 'detective']]
     * ```
     */
    public static function expand(iterable $array): array
    {
        $results = [];
        foreach ($array as $key => $value) {
            self::set($results, $key, $value);
        }

        return $results;
    }

    /**
     * Replaces the first element found with the specified value in first place.
     *
     * Переставляет на первое место первый найденный элемент с указанным значением.
     */
    public static function moveFirstByValue(array $array, mixed $value, bool $strict = true): array
    {
        $key = \array_search($value, $array, true);
        if ($key === false) {
            $strict and throw new \RuntimeException('Value not found in array');
            return $array;
        }
        $assoc = self::isAssoc($array);
        unset($array[$key]);
        if ($assoc) {
            $new = [$key => $value];
            foreach($array as $name => $value) {
                $new[$name] = $value;
            }
            return $new;
        }
        return \array_values(\array_merge([$key => $value], $array));

    }
}
