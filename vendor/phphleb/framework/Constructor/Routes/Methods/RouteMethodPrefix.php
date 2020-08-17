<?php

declare(strict_types=1);

/*
 * Method handling for setting the prefix.
 *
 * Обработка метода для установки префикса.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;

class RouteMethodPrefix extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance, string $prefix) {
        $this->methodTypeName = "prefix";
        $this->instance = $instance;
        $this->dataPath = $prefix;
    }

}

