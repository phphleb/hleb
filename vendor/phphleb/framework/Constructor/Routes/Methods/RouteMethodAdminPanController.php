<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodAdminPanController extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodAdminPanController constructor.
     * @param StandardRoute $instance
     * @param string $controller_name
     * @param array $params
     */
    function __construct(StandardRoute $instance, string $controller_name, $block_name, $params = [])
    {
        $this->method_type_name = "adminPanController";

        $this->instance = $instance;

        $this->calc($controller_name, $block_name, $params);

    }


    private function calc($controller_name, $block_name, $params)
    {

        $this->actions = [$controller_name, $params, $block_name];

        $file_name = explode("@", $controller_name)[0];

        $classes = explode("/", $file_name);

        $class_name = end($classes);


        if (!$this->search_file($file_name)) {

            $this->errors[] = "HL029-ROUTE_ERROR: Does not match in method ->adminPanController() ! " .
                "Class `" . $class_name . "` ( file `" . $file_name . ".php` ) not found in folder `/app/Controllers/*` ~ " .
                "Класс `" . $class_name . "` ( предполагаемый файл `" . $file_name . ".php` ) не обнаружен в папке `/app/Controllers/*`";

            ErrorOutput::add($this->errors);
        }

    }

    private function search_file($name)
    {

        $files = implode(" ", hleb_search_filenames(HLEB_GLOBAL_DIRECTORY . "/app/Controllers/"));

        $pos = strripos(str_replace ("\\", "/", $files), "/" . str_replace ("\\", "/",  $name) . ".php");

        return !($pos === false);

    }


}

