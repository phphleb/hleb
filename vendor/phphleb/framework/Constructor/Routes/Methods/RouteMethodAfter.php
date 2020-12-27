<?php

declare(strict_types=1);

/*
 * Processing route data for the downstream secondary controller.
 *
 * Обработка данных роута для послеидущего второстепенного контроллера.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodAfter extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance, string $controllerName, array $params = []) {
        $this->methodTypeName = "after";
        $this->instance = $instance;
        $this->calc($controllerName, $params);
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc($controllerName, $params) {
        $this->actions = [$controllerName, $params];
        $fileName = explode("@", $controllerName)[0];
        $classes = explode("/", $fileName);
        $className = end($classes);
        if (!$this->searchFile($fileName)) {
            $this->errors[] = "HL010-ROUTE_ERROR: Does not match in method ->after() ! " .
                "Class `" . $className . "` ( file `" . $fileName . ".php` ) not found in folder `/app/Middleware/After/*` ~" .
                "Исключение в методе ->after() ! Класс `" . $className . "` ( предполагаемый файл `" . $fileName . ".php` ) не обнаружен в папке `/app/Middleware/After/*` ";
            ErrorOutput::add($this->errors);
        }
    }

    // Returns the file search result.
    // Возвращает результат поиска файла.
    private function searchFile($name) {
        $list = hleb_search_filenames(HLEB_GLOBAL_DIRECTORY . "/app/Middleware/After/");
        $files = implode(" ",  is_array($list) ? $list : []);
        $pos = strripos(str_replace("\\", "/", $files), "/" . str_replace("\\", "/", $name) . ".php");
        return !($pos === false);
    }
}

