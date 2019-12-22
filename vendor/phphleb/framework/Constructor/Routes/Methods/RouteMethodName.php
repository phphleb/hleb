<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodName extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodName constructor.
     * @param StandardRoute $instance
     * @param string $name
     */
    function __construct(StandardRoute $instance, string $name)
    {
        $this->method_type_name = "name";

        $this->instance = $instance;

        $this->calc($name);

    }


    private function calc($name)
    {

        $this->data_name = $name;

        $instance_data = $this->instance->data();

        foreach ($instance_data as $inst) {

            if ($inst["data_name"] === $name) {

                $this->errors[] = "HL017-ROUTE_ERROR: Wrong argument to method ->name() ! " .
                    "Name duplication: " . $name . " ~ " .
                    "Исключение в методе ->name() ! Такое название уже используется: " . $name;

                ErrorOutput::add($this->errors);
            }

        }

    }


}

