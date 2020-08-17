<?php

declare(strict_types=1);

/*
 * Processing a method for setting a regular expression condition for an address.
 *
 * Обработка метода для установки условия по регулярному выражению для адреса.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;

class RouteMethodWhere extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance, array $params) {
        $this->methodTypeName = "where";
        $this->instance = $instance;
        $this->actions = [$params];
    }

}

