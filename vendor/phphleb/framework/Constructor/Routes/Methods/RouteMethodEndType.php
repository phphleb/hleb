<?php

declare(strict_types=1);

/*
 * Processing a terminating route indicating the type of request.
 *
 * Обработка завершающего роута указывающего тип запроса.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;

class RouteMethodEndType extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance) {
        $this->methodTypeName = "endType";
        $this->instance = $instance;
    }
}

