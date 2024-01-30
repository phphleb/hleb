<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;

#[Accessible]
final class StrToList
{
    final public const INT_TYPE = 'int';

    final public const STRING_TYPE = 'string';

    final public const FLOAT_TYPE = 'float';

    final public const BOOL_TYPE = 'bool';

    private const TYPES = [
        'string' => 'strval', 'int' => 'intval', 'integer' => 'intval', 'float' => 'floatval', 'double' => 'floatval', 'bool' => 'boolval', 'boolean' => 'boolval'
    ];

    /**
     * Converting a comma-separated enum to a list. '1, 2, 3, test' in ['1', '2', '3', 'test'].
     *
     * Преобразование перечисления через запятую в список. '1, 2, 3, test' в ['1', '2', '3', 'test'].
     *
     * @param string $value - original value.
     *                      - исходное значение.
     *
     * @param string $type - type of values in array.
     *                     - тип значений в массиве.
     * @return array
     */
    public static function convert(string $value, string $type = self::STRING_TYPE): array
    {
        if ($value === '') {
            return [];
        }
        return \array_map(self::TYPES[$type], \array_map('trim', \explode(',', $value)));
    }
}
