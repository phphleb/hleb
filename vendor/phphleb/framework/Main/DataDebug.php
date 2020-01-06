<?php

declare(strict_types=1);

namespace Hleb\Main;

class DataDebug
{
    use \DeterminantStaticUncreated;

    protected static $data = [];

    //Adding SQL Query data for debug

    /**
     * Добавление данных из ORM для вывода в панель отладки
     * @param $sql
     * @param string $time
     * @param string $dbname
     */
    public static function add(string $sql, $time, string $dbname, $exec = false)
    {   
        if(HLEB_PROJECT_DEBUG && $_SERVER['REQUEST_METHOD'] === 'GET') {

            $time_about = $exec ? self::time_about($sql) : '';

            self::$data[] = [$sql, $time, $dbname, $time_about];
        }
    }

    /**
     * @return array
     */
    public static function get(): array
    {
        return self::$data;
    }

    public static function create_html_part($part, $driver = 'mysql'): string
    {
        $pattern = $driver == "mysql" ? '/(`[^`]+`)/' : '/`([^`]+)`/';

        return preg_replace($pattern, '<span style="color: #a5432d">$1</span>', $part);
    }

    public static function create_html_param($param): string
    {
        if (is_null($param)) return "";

        switch (gettype($param)){
            case 'double':
                return "<span style='color: #4e759d'>" . strval($param) . "</span>";
                break;
            case 'integer':
                return "<span style='color: #51519d'>" . strval($param) . "</span>";
                break;
            case 'string':
            default:
                return "<span style='color: #4c8442'>" . htmlentities($param) . "</span>";
         }
    }

    private static function time_about($sql): string
    {
        return stripos(trim($sql), 'select') == 0 ? '&asymp;' : '';
    }
}


