<?php

declare(strict_types=1);

/*
 * Processing the start tag of a secure route.
 *
 * Обработка начального тега защищенного роута.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;

class RouteMethodGetProtect extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance, string $protect = "CSRF") {
        $this->methodTypeName = "getProtect";
        $this->instance = $instance;
        $this->protect[] = $protect;
    }

}

