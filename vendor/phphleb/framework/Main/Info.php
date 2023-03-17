<?php

declare(strict_types=1);

/*
 * Adding system information to the debug panel.
 *
 * Добавление системной информации в панель отладки.
 */

namespace Hleb\Main;

use Hleb\Main\Insert\BaseSingleton;

final class Info extends BaseSingleton
{
    protected static $data = [];

    const REG_NAMES = ['Autoload', 'CacheRoutes', 'RenderMap', 'Templates'];

    public static function add(string $name, $data) {
        if (in_array($name, self::REG_NAMES))
            self::$data[$name] = $data;
    }

    public static function get(string $name = '') {
        return empty($name) ? self::$data : (isset(self::$data[$name]) ? self::$data[$name] : null);
    }

    public static function insert(string $name, $data) {
        if (in_array($name, self::REG_NAMES))
            self::$data[$name][] = $data;
    }

}

