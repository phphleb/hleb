<?php

namespace Hleb\Reference;

interface ArrInterface
{
    /**
     * Checking if an array is associative.
     *
     * Проверка, что массив ассоциативный.
     */
    public function isAssoc(array $array): bool;

    /**
     * Merging two arrays, in which the named values of the first
     * will be replaced by values from the second.
     *
     * Слияние двух массивов, при котором именованные значения первого
     * будут заменены значениями из второго.
     */
    public function append(array $original, array $complement): array;

    /**
     * Returns an array of arrays sorted in descending order by the value
     * of the specified field in each nested array.
     *
     * Возвращает массив массивов отсортированный по убыванию значения
     * определенного поля в каждом вложенном в него массиве.
     */
    public function sortDescByField(array $list, string $field): array;

    /**
     * Returns an array of arrays sorted in ascending order by the value
     * of the specified field in each nested array.
     *
     * Возвращает массив массивов отсортированный по возрастанию значения
     * определенного поля в каждом вложенном в него массиве.
     */
    public function sortAscByField(array $array, string $field): array;

    /**
     * In the source array, rearranges the value with the required key to the first place.
     *
     * В исходном массиве переставляет значение с необходимым ключом на первое место.
     *
     * @param array $array - snowflake array from snow.
     *                     - оригинальный массив с данными.
     *
     * @param string $key  - the key of the array, rearranged to the first place of the data.
     *                     - ключ данных, переставляемых на первое место.
     *
     * @param bool $strict - throw an error if there is no key in the array.
     *                     - при отсутствии ключа в массиве выбрасывать ошибку.
     */
    public function moveToFirst(array $array, string $key, bool $strict = true): array;

    /**
     * Returns a new array with only the listed $keys from $array selected.
     *
     * Возвращает новый массив, где выбраны только перечисленные ключи $keys из $array.
     */
    public function only(array $array, array $keys): array;

    /**
     * Returns an array of two arrays: one containing the keys
     * and the other containing the value of the passed array.
     *
     * Возвращает массив двух массивов: один содержит ключи,
     * а другой – значения переданного массива.
     */
    public function divide(array $array): array;

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
    public function get(array $array, int|string|null $key, mixed $default = null): mixed;

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
     *  ```php
     * $array = ['products' => ['desk' => ['price' => 100]]];
     *
     * Arr::forget($array, 'products.desk');
     *
     * // ['products' => []]
     * ```
     */
    public function forget(array &$array, array|string|int|float $keys): void;

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
     * $array = ['product' => ['name' => 'Desk', 'price' => 100]];
     *
     * $contains = Arr::has($array, 'product.name'); // true
     *
     * $contains = Arr::has($array, ['product.price', 'product.discount']); // false
     * ```
     */
    public function has(array $array, string|array $keys): bool;

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
    public function add(array $array, string|int|float $key, mixed $value): array;

    /**
     * Sets the value by key according to "dot notation".
     * If no key is given to the method, the entire array will be replaced.
     * Usage example:
     *
     * Устанавливает значение по ключу согласно "точечной нотации".
     * Если методу не присвоен ключ, будет заменён весь массив.
     * Пример использования:
     *
     * ``php
     *
     * $array = ['products' => ['desk' => ['price' => 100]]];
     *
     * Arr::set($array, 'products.desk.price', 200);
     *
     * // ['products' => ['desk' => ['price' => 200]]]
     * ```
     */
    public function set(array &$array, string|int|null $key, mixed $value): array;

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
     *      'user.name' => 'Hercule Poirot',
     *      'user.occupation' => 'detective',
     *   ];
     *
     * $array = Arr::expand($array);
     * // ['user' => ['name' => 'Hercule Poirot', 'occupation' => 'detective']]
     * ```
     */
    public function expand(iterable $array): array;
}
