<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

/*

Converting a json string to a readable form (spaces are shown with dots):

Преобразование json-строки в читаемый вид (пробелы показаны точками):

'{"example":["first","second"]}'

to

'{\n
...."example":.[\n
........"first",\n
........"second"\n
....]\n
}'

 */

namespace Phphleb\Nicejson;

/**
 * Converts to formatted JSON,
 * not a single line (as the json_encode() function does).
 *
 * Преобразует в форматированный JSON,
 * а не одной строкой (как это делает функция json_encode()).
 */
final readonly class JsonConverter
{
    /**
     * @param int $mode - conversion flag (second parameter of json_encode() function).
     *                  - флаг преобразования (второй параметр функции json_encode()).
     *
     * @param int $depth - maximum nesting (the third parameter of the json_encode() function).
     *                   - максимум вложенности (третий параметр функции json_encode()).
     *
     * @param null|string $hyphenation - substitution of own value in places of line breaks.
     *                                 - подстановка собственного значения в места переноса строк.
     */
    public function __construct(
        private int     $mode = 0,
        private int     $depth = 0,
        private ?string $hyphenation = null
    )
    {
    }

    /**
     * Getting the result of the transformation.
     *
     * Получение результата преобразования.
     *
     * @param string|object|array $data - data to be converted, it can be an object, an array,
     *                                    or a valid JSON string.
     *                                  - данные для преобразования, это может быть объект, массив
     *                                    или валидная JSON-строка.
     */
    public function get(array|object|string $data): false|string
    {
        if (\is_string($data)) {
            $data = \json_decode($data);
        }
        return $data !== false && $data !== null ? $this->getConvertedData($data) : false;
    }

    private function getConvertedData(array|object $data): false|string
    {
        \ob_start();
        if ($this->depth) {
            echo \json_encode($data, $this->mode | JSON_PRETTY_PRINT, $this->depth);
        } else {
            echo \json_encode($data, $this->mode | JSON_PRETTY_PRINT);
        }
        $result = (string)\ob_get_clean();

        if ($this->hyphenation !== null) {
            $result = $this->createHyphenation($result);
        }
        return $result;
    }

    private function createHyphenation(string $str): string
    {
        return \str_replace(["\r\n", "\r", "\n"], $this->hyphenation, $str);
    }
}
