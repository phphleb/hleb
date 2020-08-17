<?php

declare(strict_types=1);

/*
 * Processing route data to configure domain and subdomains.
 *
 * Обработка данных роута для настройки домена и субдоменов.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;

class RouteMethodDomain extends MainRouteMethod
{
    protected $instance;

    /**
     * @param StandardRoute $instance
     * @param array|string $name
     * @param int $level
     * @param bool $pattern
     */
    function __construct(StandardRoute $instance, $name, $level, $pattern) {
        $this->methodTypeName = "domain";
        $this->instance = $instance;
        $this->domain = [is_array($name) ? $name : [$name], $level, $pattern];
    }
}

