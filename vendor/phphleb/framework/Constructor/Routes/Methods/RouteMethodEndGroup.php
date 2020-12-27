<?php

declare(strict_types=1);

/*
 * Group terminating route handling.
 *
 * Обработка завершающего роута группы.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodEndGroup extends MainRouteMethod
{
    protected $instance;

    /**
     * @param StandardRoute $instance
     * @param string|null $name
     */
    function __construct(StandardRoute $instance, string $name = null) {
        $this->methodTypeName = "endGroup";
        $this->instance = $instance;
        if (!empty($name)) $this->calc($name);
        $this->calcGroup();
    }

    // Check the correct location of the group method.
    // Проверка правильного расположения метода группы.
    private function calcGroup() {
        $instanceData = $this->instance->data();
        $open = false;
        foreach ($instanceData as $inst) {
            if ($inst["method_type_name"] == "getGroup") {
                $open = true;
            }
        }
        if (!$open) {
            $this->errors[] = "HL012-ROUTE_ERROR: Error in method ->endGroup() ! " .
                "Closing group without opening group. ~ " .
                "Ошибка в методе ->endGroup() ! Закрытие тега группы до его открытия.";
            ErrorOutput::add($this->errors);
        }
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    private function calc($name) {
        $this->dataName = $name;
        $instanceData = $this->instance->data();
        $search = false;
        foreach ($instanceData as $inst) {
            if ($inst["data_name"] == $name && $inst["method_type_name"] == $this->methodTypeName) {
                $this->errors[] = "HL013-ROUTE_ERROR: Wrong argument to method ->endGroup() ! " .
                    "Group name duplication: " . $name . " ~ " .
                    "Ошибка в методе ->endGroup() ! Такое имя группы уже используется: " . $name;
                ErrorOutput::add($this->errors);
            }

            if ($inst["data_name"] == $name && $inst["method_type_name"] == "getGroup") {
                $search = true;
            }
        }
        if (!$search) {
            $this->errors[] = "HL014-ROUTE_ERROR: Wrong argument to method ->endGroup() ! " .
                "Closing tag for named group `" . $name . "` without open tag ~ " .
                "Ошибка в методе ->endGroup() ! Закрытый тег именованной группы `" . $name . "` без открытого тега. ";
            ErrorOutput::add($this->errors);
        }
    }
}

