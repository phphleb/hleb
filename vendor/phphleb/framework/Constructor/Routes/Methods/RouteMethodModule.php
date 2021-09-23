<?php

declare(strict_types=1);

/*
 * Controller processing for modular development.
 *
 * Обработка контроллера для модульной разработки.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodModule extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance, string $moduleName, string $className = "Controller", array $params = []) {
        $this->methodTypeName = "controller";
        $this->instance = $instance;
        $this->calc(trim($moduleName, "/\\"), trim($className, "/\\"), $params);
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc($moduleName, $controllerName, $params) {
        $controller = $moduleName . "/" . $controllerName;
        $this->actions = [$controller, $params, "module"];
        $fileName = explode("@", $controller)[0];
        $classes = explode("/", $fileName);
        $className = end($classes);
        if (!file_exists(HLEB_GLOBAL_DIRECTORY . "/modules/")) {
            $this->errors[] = "HL025-ROUTE_ERROR: No directory found for method ->module() ! " .
                "The `/modules/` folder was not found, create it in the root directory of the project. ~ " .
                "Не обнаружена папка `/modules/` в корневой директории проекта, необходимо эту папку создать.";
        }
        if (!$this->searchFile($fileName) && strip_tags($fileName) === $fileName) {
            $this->errors[] = "HL023-ROUTE_ERROR: Does not match in method ->module() ! " .
                "Class `" . $className . "`  not found in folder `/modules/" . $moduleName . "/` ~ " .
                "Класс-контроллер `" . $className . "`  не обнаружен в папке `/modules/" . $moduleName . "/`";
        }
        if (count($this->errors)) {
            ErrorOutput::add($this->errors);
        }
    }

    // Returns the file search result.
    // Возвращает результат поиска файла.
    private function searchFile($name) {
        $list = hleb_search_filenames(HLEB_GLOBAL_DIRECTORY . "/modules/");
        $files = implode(" ",  is_array($list) ? $list : []);
        $pos = strripos(str_replace("\\", "/", $files), "/" . str_replace("\\", "/", $name) . ".php");
        return !($pos === false);
    }
}

