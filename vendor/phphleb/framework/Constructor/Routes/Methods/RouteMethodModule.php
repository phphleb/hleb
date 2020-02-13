<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodModule extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodModule constructor.
     * @param StandardRoute $instance
     * @param string $module_name
     * @param string $class_name
     * @param array $params
     */
    function __construct(StandardRoute $instance, string $module_name, string $class_name = "Controller", $params = [])
    {
        $this->method_type_name = "controller";

        $this->instance = $instance;

        $this->calc(trim($module_name, "/\\") , trim($class_name, "/\\"), $params);

    }


    private function calc($module_name, $controller_name, $params)
    {
        $controller = $module_name . "/" . $controller_name;

        $this->actions = [$controller, $params, "module"];

        $file_name = explode("@", $controller)[0];

        $classes = explode("/", $file_name);

        $class_name = end($classes);

        if(!file_exists(HLEB_GLOBAL_DIRECTORY . "/modules/")){

            $this->errors[] = "HL025-ROUTE_ERROR: No directory found for method ->module() ! " .
                "The `/modules/` folder was not found, create it in the root directory of the project. ~ " .
                "Не обнаружена папка `/modules/` в корневой директории проекта, необходимо эту папку создать.";
        }


        if (!$this->search_file($file_name)) {

            $this->errors[] = "HL023-ROUTE_ERROR: Does not match in method ->module() ! " .
                "Class `" . $class_name . "`  not found in folder `/modules/" . $module_name . "/` ~ " .
                "Класс-контроллер `" . $class_name . "`  не обнаружен в папке `/modules/" . $module_name . "/`";
        }

        if(count($this->errors)){
            ErrorOutput::add($this->errors);
        }

    }

    private function search_file($name)
    {

        $files = implode(" ", hleb_search_filenames(HLEB_GLOBAL_DIRECTORY . "/modules/"));

        $pos = strripos(str_replace ("\\", "/", $files), "/" . str_replace ("\\", "/",  $name) . ".php");

        return !($pos === false);

    }


}

