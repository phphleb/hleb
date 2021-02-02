<?php

declare(strict_types=1);

/*
 * Processing route data for the controller.
 *
 * Обработка данных роута для контроллера.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodController extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance, string $controllerName, array $params = []) {
        $this->methodTypeName = "controller";
        $this->instance = $instance;
        $this->calc($controllerName, $params);
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc($controllerName, $params) {
        $this->actions = [$controllerName, $params, "controller"];
        $fileName = explode("@", $controllerName)[0];
        $classes = explode("/", $fileName);
        $className = end($classes);
        if (!$this->searchFile($fileName) && strip_tags($fileName) === $fileName) {
            $this->errors[] = "HL016-ROUTE_ERROR: Does not match in method ->controller() ! " .
                "Class `" . $className . "` ( file `" . $fileName . ".php` ) not found in folder `/app/Controllers/*` ~ " .
                "Класс `" . $className . "` ( предполагаемый файл `" . $fileName . ".php` ) не обнаружен в папке `/app/Controllers/*`";
            ErrorOutput::add($this->errors);
        }
    }

    // Returns the file search result.
    // Возвращает результат поиска файла.
    private function searchFile($name) {
        $list = hleb_search_filenames(HLEB_GLOBAL_DIRECTORY . "/app/Controllers/");
        $files = implode(" ",  is_array($list) ? $list : []);
        $pos = strripos(str_replace("\\", "/", $files), "/" . str_replace("\\", "/", $name) . ".php");
        return !($pos === false);
    }
}

