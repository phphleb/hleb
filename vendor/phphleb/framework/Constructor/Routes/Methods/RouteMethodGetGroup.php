<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodGetGroup extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodGetGroup constructor.
     * @param StandardRoute $instance
     * @param string|null $name
     */
    function __construct(StandardRoute $instance, string $name = null)
    {
        $this->method_type_name = "getGroup";

        $this->instance = $instance;

        if (!empty($name)) $this->calc($name);

    }


    private function calc($name)
    {

        $this->data_name = $name;

        $instance_data = $this->instance->data();

        foreach ($instance_data as $k =>$inst) {

            if ($inst["data_name"] === $name && $inst["method_type_name"] === $this->method_type_name) {

                $this->errors[] = "HL015-ROUTE_ERROR: Wrong argument to method ->getGroup() ! " .
                    "Group name duplication: " . $name . "~" .
                    "Исключение в методе ->getGroup() ! Такое имя группы уже используется: " . $name;

                ErrorOutput::add($this->errors);
            }

        }

    }


}

