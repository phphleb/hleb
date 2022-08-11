<?php

declare(strict_types=1);

/*
 * Processing a method to assign a request type to a specific route.
 *
 * Обработка метода для назначения типа запроса конкретному роуту.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodType extends MainRouteMethod
{
    protected $instance;

    /**
     * @param StandardRoute $instance
     * @param string|array $type
     */
    function __construct(StandardRoute $instance, $type) {
        $this->methodTypeName = "type";
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
                $this->errors[] = "HL018-ROUTE_ERROR: Wrong argument to method ->type() ! " .
                    "In stock " . $type . " expected " . implode(",", $this->httpTypes) . " ~ " .
                    "Неправильный аргумент в методе ->type() ! Введено `" . $type . "`, допустимые значения " . implode(",", $this->httpTypes);
                ErrorOutput::add($this->errors);
            }
        }
    }

}

