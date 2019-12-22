<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodType extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodType constructor.
     * @param StandardRoute $instance
     * @param string|array $type
     */
    function __construct(StandardRoute $instance, $type)
    {
        $this->method_type_name = "type";

        $this->instance = $instance;

        $this->calc($type);

    }


    private function calc($types)
    {
        if (!empty($types)) $this->type = [];

        if (is_string($types)) $types = [strtolower($types)];

        foreach ($types as $type) {

            $this->type[] = strtolower($type);

            if (!in_array(strtolower($type), $this->types)) {

                $this->errors[] = "HL018-ROUTE_ERROR: Wrong argument to method ->type() ! " .
                    "In stock " . $type . " expected " . implode(",", $this->types) . " ~ " .
                    "Неправильный аргумент в методе ->type() ! Введено `" . $type . "`, допустимые значения " . implode(",", $this->types);

                ErrorOutput::add($this->errors);

            }

        }

    }


}

