<?php

namespace Hleb\Scheme\Home\Constructor\Routes;

interface StandardRoute
{
    public static function get($route);

    public static function getGroup();

    public static function endGroup();

    public static function before($class_name);

    public static function after($class_name);

    public static function where($params);

    public static function type($types);

    public static function getType($types);

    public static function endType();

    public static function name($name);

    public static function prefix($add);

    public static function protect();

    public static function domain($name);

    public static function domainPattern($name);

    public static function getProtect();

    public static function endProtect();

    public static function renderMap($name, $map);

    public static function controller($class_name);

    public static function data();

}

