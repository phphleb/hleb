<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodEndProtect extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodGetProtect constructor.
     * @param StandardRoute $instance
     */
    function __construct(StandardRoute $instance)
    {
        $this->method_type_name = "endProtect";

        $this->instance = $instance;

    }

}

