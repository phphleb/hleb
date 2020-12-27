<?php

declare(strict_types=1);

/*
 * Handling the method for creating a list of routes for the page constructor.
 *
 * Обработка метода создания списка роутов для конструктора страниц.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodRenderMap extends MainRouteMethod
{
    protected $instance;

    /**
     * @param StandardRoute $instance
     * @param string $name
     * @param string|array $map
     */
    function __construct(StandardRoute $instance, string $name, $map) {
        $this->methodTypeName = "renderMap";
        $this->instance = $instance;
        $this->calc($name, $map);
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc($name, $map) {
        $this->dataName = $name;
        if (is_string($map)) $map = [$map];
        $this->dataParams = $map;
        $instanceData = $this->instance->data();
        foreach ($instanceData as $key => $inst) {
            if ($inst["data_name"] === $name) {
                $this->errors[] = "HL020-ROUTE_ERROR: Wrong argument to method ->renderMap() ! " .
                    "Name duplication: `" . $name . "` ~ " .
                    "Неправильный аргумент у метода ->renderMap() !  Такое название (`" . $name . "`) уже используется.";
                ErrorOutput::add($this->errors);
            }
        }
    }

}

