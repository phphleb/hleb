<?php

declare(strict_types=1);

/*
 * Processing the start tag of the assignment of the request type.
 *
 * Обработка начального тега назначения типа запроса.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodGetType extends MainRouteMethod
{
    protected $instance;

    /**
     * @param StandardRoute $instance
     * @param string|array $type
     */
    function __construct(StandardRoute $instance, $type) {
        $this->methodTypeName = "getType";
        $this->instance = $instance;
        $this->calc($type);
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc($types) {
        if (!empty($types)) $this->type = [];
        if (is_string($types)) $types = [strtolower($types)];
        foreach ($types as $type) {
            $this->type[] = strtolower($type);
            if (!in_array(strtolower($type), $this->httpTypes)) {
                $this->errors[] = "HL009-ROUTE_ERROR: Wrong argument to method  ->getType() ! " .
                    "In stock `" . $type . "` expected in `" . implode(",", array_unique($this->httpTypes)) . "`. ~ " .
                    "Неправильный аргумент в методе ->getType() ! В наличии `" . $type . "`, ожидалось получить из `" . implode(",", array_unique($this->httpTypes)) . "`.";
                ErrorOutput::add($this->errors);
            }
        }
    }

}

