<?php

declare(strict_types=1);

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
     * RouteMethodRenderMap constructor.
     * @param StandardRoute $instance
     * @param string $name
     * @param string|array $map
     */
    function __construct(StandardRoute $instance, string $name, $map)
    {
        $this->method_type_name = "renderMap";

        $this->instance = $instance;

        $this->calc($name, $map);

    }


    private function calc($name, $map)
    {

        $this->data_name = $name;

        if (is_string($map)) $map = [$map];

        $this->data_params = $map;

        $instance_data = $this->instance->data();

        foreach ($instance_data as $key =>$inst) {

            if ($inst["data_name"] === $name) {

                $this->errors[] = "HL020-ROUTE_ERROR: Wrong argument to method ->renderMap() ! " .
                    "Name duplication: `" . $name . "` ~ " .
                    "Неправильный аргумент у метода ->renderMap() !  Такое название (`" . $name . "`) уже используется.";

                ErrorOutput::add($this->errors);
            }

        }

    }


}

