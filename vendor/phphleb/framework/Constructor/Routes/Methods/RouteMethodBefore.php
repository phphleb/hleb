<?php

declare(strict_types=1);

/*
 * Processing route data for the upstream secondary controller.
 *
 * Обработка данных роута для предварительного второстепенного контроллера.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodBefore extends MainRouteMethod
{
    protected $instance;

    public function __construct(StandardRoute $instance, string $controllerName, array $params = []) {
        $this->methodTypeName = "before";
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
            $this->errors[] = "HL011-ROUTE_ERROR: Does not match in method ->before() ! " .
                "Class `" . $className . "` ( file `" . $fileName . ".php` ) not found in folder `/app/Middleware/Before/*` ~" .
                "Исключение в методе ->before() ! Класс `" . $className . "` ( предполагаемый файл `" . $fileName . ".php` ) не обнаружен в папке `/app/Middleware/Before/*` ";
            ErrorOutput::add($this->errors);
        }
    }

    // Returns the file search result.
    // Возвращает результат поиска файла.
    private function searchFile($name) {
        $list = hleb_search_filenames(HLEB_GLOBAL_DIRECTORY . "/app/Middleware/Before/");
        $files = implode(" ",  is_array($list) ? $list : []);
        $pos = strripos(str_replace("\\", "/", $files), "/" . str_replace("\\", "/", $name) . ".php");
        return !($pos === false);
    }
}

