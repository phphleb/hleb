<?php

declare(strict_types=1);

namespace Hleb\Main;

class MyDebug
{
    use \DeterminantStaticUncreated;

    // Add your own data to the debug panel
    /**
     *  Добавление собственных данных на отладочную панель ( заголовок, значение )
     *  Наименование создаваемого раздела может состоять из массива или строки
     *
     **/

    protected static $data = [];

    protected static $error = [];

    /**
     * @param string $name
     * @param $data
     */
    public static function add(string $name, $data)
    {
        if (!in_array($name, self::$error)) self::$data[$name] = $data;
    }

    /**
     * @param string $name
     * @return array
     */
    public static function get(string $name)
    {
        return isset(self::$data[$name]) ? self::$data[$name] : [];
    }

    public static function all()
    {
        return self::$data;
    }

    /**
     * @param string $name
     * @param $data
     */
    public static function insert_to_array(string $name, $data)
    {
        if (self::check()) {

            if (isset(self::$data[$name]) && !is_array(self::$data[$name])) {
                self::error_type($name, 'array');
            }

            if (!in_array($name, self::$error)) {
                isset(self::$data[$name]) ? self::$data[$name][] = $data : self::$data[$name] = [1 => $data];
            }
        }

    }

    /**
     * @param string $name
     * @param string $data
     */
    public static function insert_to_string(string $name, string $data): void
    {
        if (self::check()) {

            if (!in_array($name, self::$error)) {

                if (!isset(self::$data[$name]))
                    self::$data[$name] = '';

                is_string(self::$data[$name]) ? self::$data[$name] .= $data : self::error_type($name, 'string');
            }
        }

    }

    private static function error_type(string $name, string $type)
    {
        self::$data[$name] = "Invalid source value format ( insert_to_$type for $name: " . gettype(self::$data[$name]) . ' ) !';

        self::$error[$name] = $name;
    }

    private static function check()
    {
        return HLEB_PROJECT_DEBUG && $_SERVER['REQUEST_METHOD'] == 'GET';
    }


}

