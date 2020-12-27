<?php

declare(strict_types=1);

/*
 * Processing a terminating secure route.
 *
 * Обработка завершающего защищенного роута.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;

class RouteMethodEndProtect extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance) {
        $this->methodTypeName = "endProtect";
        $this->instance = $instance;
    }
}

