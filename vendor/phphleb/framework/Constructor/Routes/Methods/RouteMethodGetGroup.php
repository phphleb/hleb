<?php

declare(strict_types=1);

/*
 * Handling the initial route of the group.
 *
 * Обработка начального роута группы.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodGetGroup extends MainRouteMethod
{
    protected $instance;

    /**
     * @param StandardRoute $instance
     * @param string|null $name
     */
    function __construct(StandardRoute $instance, string $name = null) {
        $this->methodTypeName = "getGroup";
        $this->instance = $instance;
        if (!empty($name)) $this->calc($name);
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc($name) {
        $this->dataName = $name;
        $instanceData = $this->instance->data();
        foreach ($instanceData as $k => $inst) {
            if ($inst["data_name"] === $name && $inst["method_type_name"] === $this->methodTypeName) {
                $this->errors[] = "HL015-ROUTE_ERROR: Wrong argument to method ->getGroup() ! " .
                    "Group name duplication: " . $name . "~" .
                    "Исключение в методе ->getGroup() ! Такое имя группы уже используется: " . $name;
                ErrorOutput::add($this->errors);
            }
        }
    }
}

