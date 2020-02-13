<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodPrefix extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodPrefix constructor.
     * @param StandardRoute $instance
     * @param string $prefix
     */
    function __construct(StandardRoute $instance, string $prefix)
    {
        $this->method_type_name = "prefix";

        $this->instance = $instance;

        $this->calc($prefix);

    }


    private function calc($prefix)
    {

        $this->data_path = $prefix;

    }


}

