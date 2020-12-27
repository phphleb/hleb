<?php

declare(strict_types=1);

/*
 * Handling a method to set up a route guard.
 *
 * Обработка метода для установки защиты роута.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;

class RouteMethodProtect extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance, string $validate_type = "CSRF") {
        $this->methodTypeName = "protect";
        $this->instance = $instance;
        $this->protect[] = $validate_type;
    }

}

