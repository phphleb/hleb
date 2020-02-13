<?php

declare(strict_types=1);

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute, RouteMethodStandard
};
use Hleb\Constructor\Routes\Methods\{
    RouteMethodGet, RouteMethodType, RouteMethodName, RouteMethodController,
    RouteMethodGetGroup, RouteMethodEndGroup, RouteMethodBefore, RouteMethodAfter, RouteMethodWhere, RouteMethodGetType,
    RouteMethodEndType, RouteMethodEnd, RouteMethodPrefix, RouteMethodProtect, RouteMethodGetProtect, RouteMethodEndProtect,
    RouteMethodRenderMap, RouteMethodDomain, RouteMethodAdminPanController, RouteMethodModule
};
use Hleb\Constructor\Routes\MainRoute;

class Route extends MainRoute implements StandardRoute
{
    use \DeterminantStaticUncreated;

    private static $object_methods = [];

    private static $data_methods = [];

    private static $number = 1000;


    public static function get($route, $params = [])
    {

        return self::add(new RouteMethodGet(self::instance(), $route, $params));
    }

    public static function getGroup($name = null)
    {

        return self::add(new RouteMethodGetGroup(self::instance(), $name));
    }

    public static function endGroup($name = null)
    {

        return self::add(new RouteMethodEndGroup(self::instance(), $name));
    }

    public static function before($class_name, array $params = [])
    {

        return self::add(new RouteMethodBefore(self::instance(), $class_name, $params));
    }

    public static function after($class_name, array $params = [])
    {

        return self::add(new RouteMethodAfter(self::instance(), $class_name, $params));
    }

    public static function where($params)
    {

        return self::add(new RouteMethodWhere(self::instance(), $params));
    }

    public static function type($types)
    {

        return self::add(new RouteMethodType(self::instance(), $types));

    }

    public static function getType($types)
    {

        return self::add(new RouteMethodGetType(self::instance(), $types));
    }

    public static function endType()
    {

        return self::add(new RouteMethodEndType(self::instance()));
    }

    public static function renderMap($name, $map)
    {

        return self::add(new RouteMethodRenderMap(self::instance(), $name, $map));
    }

    public static function protect($validate = 'CSRF')
    {

        return self::add(new RouteMethodProtect(self::instance(), $validate));

    }

    public static function getProtect($validate = 'CSRF')
    {

        return self::add(new RouteMethodGetProtect(self::instance(), $validate));
    }

    public static function endProtect()
    {

        return self::add(new RouteMethodEndProtect(self::instance()));
    }

    public static function domain($name, $level = 3)
    {

        return self::add(new RouteMethodDomain(self::instance(), $name, $level, false));
    }

    public static function domainPattern($name, $level = 3)
    {

        return self::add(new RouteMethodDomain(self::instance(), $name, $level, true));
    }

    public static function name($name)
    {

        return self::add(new RouteMethodName(self::instance(), $name));
    }

    public static function controller($class_name, array $params = [])
    {

        return self::add(new RouteMethodController(self::instance(), $class_name, $params));

    }

    public static function module($module_name, $class_name = "Controller", array $params = [])
    {

        return self::add(new RouteMethodModule(self::instance(), $module_name, $class_name, $params));

    }

    public static function adminPanController($class_name, string $block_name, array $params = [])
    {

        return self::add(new RouteMethodAdminPanController(self::instance(), $class_name, $block_name, $params));

    }

    public static function prefix($add)
    {

        return self::add(new RouteMethodPrefix(self::instance(), $add));
    }

    public static function end()
    {

        self::$data_methods = (new RouteMethodEnd(self::instance()))->data();

        return null;

    }


    ///////////////CREATE///////////////


    public static function data()
    {

        return self::$data_methods;
    }

    public static function delete()
    {

        self::$instance = null;
    }

    /**
     * @param RouteMethodStandard $method
     * @return null|static
     */
    private static function create(RouteMethodStandard $method)
    {


        self::$object_methods[] = $method;

        if ($method->approved()) {

            return self::instance();
        }

        return null;


    }

    private static function add(RouteMethodStandard $method)
    {

        $data = $method->data();

        self::$number++;

        $data['number'] = self::$number;

        self::$data_methods[] = $data;

        return self::create($method);

    }

}

