<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodProtect extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodProtect constructor.
     * @param StandardRoute $instance
     * @param string $validate_type
     */
    function __construct(StandardRoute $instance, string $validate_type = "CSRF")
    {
        $this->method_type_name = "protect";

        $this->instance = $instance;

        $this->calc($validate_type);

    }


    private function calc($validate_type)
    {

        $this->protect[] = $validate_type;


    }


}

