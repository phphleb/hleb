<?php

declare(strict_types=1);

/*
 * Controller processing for modular development.
 *
 * Обработка контроллера для модульной разработки.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodName extends MainRouteMethod
{
    protected $instance;

    function __construct(StandardRoute $instance, string $name) {
        $this->methodTypeName = "name";
        $this->instance = $instance;
        $this->calc($name);
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc($name) {
        $this->dataName = $name;
        $instanceData = $this->instance->data();
        foreach ($instanceData as $inst) {
            if ($inst["data_name"] === $name) {
                $this->errors[] = "HL017-ROUTE_ERROR: Wrong argument to method ->name() ! " .
                    "Name duplication: " . $name . " ~ " .
                    "Исключение в методе ->name() ! Такое название уже используется: " . $name;
                ErrorOutput::add($this->errors);
            }
        }
    }

}

