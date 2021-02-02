<?php

declare(strict_types=1);

/*
 * Processing route data for the admin panel.
 *
 * Обработка данных роута для административной панели.
 */

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
     * @param StandardRoute $instance
     * @param string $controllerName
     * @param string|array $blockName
     * @param array $params
     */
    function __construct(StandardRoute $instance, string $controllerName, $blockName, array $params = []) {
        $this->methodTypeName = "adminPanController";
        $this->instance = $instance;
        $this->calc($controllerName, $blockName, $params);
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc(string $controllerName, $blockName, array$params) {
        $this->actions = [$controllerName, $params, $blockName];
        $fileName = explode("@", $controllerName)[0];
        $classes = explode("/", $fileName);
        $className = end($classes);
        if (!$this->searchFile($fileName) && strip_tags($fileName) === $fileName) {
            $this->errors[] = "HL029-ROUTE_ERROR: Does not match in method ->adminPanController() ! " .
                "Class `" . $className . "` ( file `" . $fileName . ".php` ) not found in folder `/app/Controllers/*` ~ " .
                "Класс `" . $className . "` ( предполагаемый файл `" . $fileName . ".php` ) не обнаружен в папке `/app/Controllers/*`";
            ErrorOutput::add($this->errors);
        }
    }

    // Returns the file search result.
    // Возвращает результат поиска файла.
    private function searchFile(string $name) {
        $list = hleb_search_filenames(HLEB_GLOBAL_DIRECTORY . "/app/Controllers/");
        $files = implode(" ",  is_array($list) ? $list : []);
        $pos = strripos(str_replace("\\", "/", $files), "/" . str_replace("\\", "/", $name) . ".php");
        return !($pos === false);
    }
}

