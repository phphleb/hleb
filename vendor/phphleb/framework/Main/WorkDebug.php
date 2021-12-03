<?php

declare(strict_types=1);

/*
 * Adding temporary debug data for output to the debug panel.
 *
 * Добавление временных отладочных данных для вывода в панель отладки.
 */

namespace Hleb\Main;

use Hleb\Main\Insert\BaseSingleton;

final class WorkDebug extends BaseSingleton
{
    protected static $data = [];

    /**
     * Adds data for any type of display on top of content as structure and value information.
     *
     * Добавляет данные для вывода любого типа поверх контента в виде информации о структуре и значениях.
     *
     * @param mixed $data
     * @param string|null $desc
     */
    public static function add($data, string $desc = null) {
        if (HLEB_PROJECT_DEBUG_ON) {
            self::$data[] = [$data, $desc];
        }
    }

    /** @internal */
    public static function get(): array {
        return self::$data;
    }

}


