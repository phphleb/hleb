<?php

namespace Hleb\Scheme\Home\Constructor\Routes;

interface StandardRoute
{
    public static function get(string $route);

    public static function getGroup();

    public static function endGroup();

    public static function before(string $class_name);

    public static function after(string $class_name);

    public static function where(array $params);

    public static function type($types);

    public static function getType($types);

    public static function endType();

    public static function name(string $name);

    public static function prefix(string $add);

    public static function protect();

    public static function domain($name);

    public static function domainPattern($name);

    public static function domainTemplate($name);

    public static function getProtect();

    public static function endProtect();

    public static function renderMap(string $name, $map);

    public static function controller(string $class_name);

    public static function module(string $module_name);

    public static function adminPanController(string $class_name, $block_name);

    public static function add(string $route, $params = []);

    public static function post(string $route, $params = []);

    public static function patch(string $route, $params = []);

    public static function delete(string $route, $params = []);

    public static function options(string $route);

    public static function any(string $route, $params = []);

    public static function match(array $types, string $route, $params = []);

    public static function fallback(string $module_name);

}

