<?php

/*
 * A specialized storage for a request array from a URL.
 *
 * Узкоспециализированное хранилище для переменных запроса из URL-адреса.
 */

declare(strict_types=1);

namespace Hleb\Constructor\Routes;

use Hleb\Main\Insert\BaseSingleton;

final class Data extends BaseSingleton
{
    private static $data = null;

    public static function createData(array $array) {
        if (is_null(self::$data)) self::$data = $array;
    }

    public static function returnData() {
        return self::$data ?? [];
    }

    public static function create_data(array $array) {
        self::createData($array);
    }

    public static function return_data() {
        return self::returnData();
    }
}

