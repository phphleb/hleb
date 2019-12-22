<?php

declare(strict_types=1);

namespace Hleb\Main;

class WorkDebug
{
    use \DeterminantStaticUncreated;

    protected static $data = [];

    //Adding data for debug output

    /**
     * Добавление данных для вывода в панель отладки
     * @param $data
     * @param string|null $desc
     */
    public static function add($data, string $desc = null)
    {   
        if(HLEB_PROJECT_DEBUG && $_SERVER['REQUEST_METHOD'] == 'GET') {

            self::$data[] = [$data, $desc];

        }
    }

    /**
     * @return array
     */
    public static function get(): array
    {
        return self::$data;
    }


}


