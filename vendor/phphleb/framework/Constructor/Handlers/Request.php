<?php

namespace Hleb\Constructor\Handlers;

use DeterminantStaticUncreated;

class Request
{
    use DeterminantStaticUncreated;

    private static $request = [];

    public static function get(string $name = null)
    {
        if (empty($name)) {

            return self::$request;

        }

        return self::$request[$name];

    }

    public static function add(string $name, string $value)
    {

        self::$request[$name] = $value;

    }


}