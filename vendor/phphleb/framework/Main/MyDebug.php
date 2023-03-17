<?php

declare(strict_types=1);

/*
 * Adding your own data to the debug panel (title, value).
 * The name of the section being created can consist of an array or a string.
 *
 * Добавление собственных данных на отладочную панель (заголовок, значение).
 * Наименование создаваемого раздела может состоять из массива или строки.
 *
 **/

namespace Hleb\Main;

use Hleb\Main\Insert\BaseSingleton;

final class MyDebug extends BaseSingleton
{
    protected static $data = [];

    protected static $error = [];

    /**
     * Adding your own data to the debug panel (title, value).
     * The name of the section being created can consist of an array or a string.
     *
     * Добавление собственных данных на отладочную панель (заголовок, значение).
     * Наименование создаваемого раздела может состоять из массива или строки.
     *
     * @param string $name
     * @param $data
     */
    public static function add(string $name, $data) {
        if (!in_array($name, self::$error)) self::$data[$name] = $data;
    }

    /**
     * Adds custom <data> to the <name> value in the debug panel.
     *
     * Добавляет к значению <name> в отладночной панели собственные данные <data>.
     *
     * @param string $name
     * @param $data
     */
    public static function insertToListing(string $name, $data) {
        self::insert_to_array($name, $data);
    }

    /**
     * Adds its own <data> string to the <name> value in the debug panel, which is appended to the current value.
     *
     * Добавляет к значению <name> в отладночной панели собственную строку <data>, которая добавляется к текущему значению.
     *
     * @param string $name
     * @param string $data
     */
    public static function insertToString(string $name, string $data) {
        self::insert_to_string($name, $data);
    }

    /**
     * Adds custom <data> to the <name> value in the debug panel.
     *
     * Добавляет к значению <name> в отладночной панели собственные данные <data>.
     *
     * @deprecated
     * @param string $name
     * @param $data
     */
    public static function insert_to_array(string $name, $data) {
        if (HLEB_PROJECT_DEBUG_ON) {
            if (isset(self::$data[$name]) && !is_array(self::$data[$name])) {
                self::errorType($name, 'array');
            }
            if (!in_array($name, self::$error)) {
                isset(self::$data[$name]) ? self::$data[$name][] = $data : self::$data[$name] = [1 => $data];
            }
        }
    }

    /**
     * Adds its own <data> string to the <name> value in the debug panel, which is appended to the current value.
     * 
     * Добавляет к значению <name> в отладночной панели собственную строку <data>, которая добавляется к текущему значению.
     *
     * @deprecated
     * @param string $name
     * @param string $data
     */
    public static function insert_to_string(string $name, string $data) {
        if (HLEB_PROJECT_DEBUG_ON) {
            if (!in_array($name, self::$error)) {
                if (!isset(self::$data[$name]))
                    self::$data[$name] = '';
                is_string(self::$data[$name]) ? self::$data[$name] .= $data : self::errorType($name, 'string');
            }
        }
    }

    public static function get(string $name) {
        return isset(self::$data[$name]) ? self::$data[$name] : [];
    }

    public static function all() {
        return self::$data;
    }

    private static function errorType(string $name, string $type) {
        self::$data[$name] = "Invalid source value format ( insert_to_$type for $name: " . gettype(self::$data[$name]) . ' ) !';
        self::$error[$name] = $name;
    }

}

