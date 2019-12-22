<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodWhere extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodWhere constructor.
     * @param StandardRoute $instance
     * @param array $params
     */
    function __construct(StandardRoute $instance, array $params)
    {
        $this->method_type_name = "where";

        $this->instance = $instance;

        $this->calc($params);

    }


    private function calc($params)
    {

        $this->actions = [$params];

    }


}

