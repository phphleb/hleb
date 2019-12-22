<?php

declare(strict_types=1);

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
     * RouteMethodEndGroup constructor.
     * @param StandardRoute $instance
     * @param string|null $name
     */
    function __construct(StandardRoute $instance, string $name = null)
    {
        $this->method_type_name = "endGroup";

        $this->instance = $instance;

        if (!empty($name)) $this->calc($name);

        $this->calc_group();

    }

    private function calc_group()
    {

        $instance_data = $this->instance->data();


        $open = false;

        foreach ($instance_data as $inst) {


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


    private function calc($name)
    {

        $this->data_name = $name;

        $instance_data = $this->instance->data();

        $search = false;


        foreach ($instance_data as $inst) {


            if ($inst["data_name"] == $name && $inst["method_type_name"] == $this->method_type_name) {

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

