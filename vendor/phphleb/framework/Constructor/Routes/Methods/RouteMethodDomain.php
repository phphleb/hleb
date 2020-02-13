<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodDomain extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodDomain constructor.
     * @param StandardRoute $instance
     * @param array|string $name
     * @param int $level
     * @param bool $pattern
     */
    function __construct(StandardRoute $instance, $name, $level, $pattern)
    {
        $this->method_type_name = "domain";

        $this->instance = $instance;

        $this->calc([is_array($name) ? $name : [$name], $level, $pattern]);

    }


    private function calc($params)
    {

        $this->domain = $params;

    }


}

