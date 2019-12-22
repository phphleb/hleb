<?php

declare(strict_types=1);

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use \Closure;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodGet extends MainRouteMethod
{

    protected $instance;

    /**
     * RouteMethodGet constructor.
     * @param StandardRoute $instance
     * @param string|array $route_path
     * @param string|object|Closure|array $params
     */
    function __construct(StandardRoute $instance, string $route_path, $params = [])
    {
        $this->method_type_name = "get";

        $this->instance = $instance;

        $this->calc($route_path, $params);

    }


    private function calc($route_path, $params)
    {

        $this->data_path = $route_path;

        if (is_array($params)) {

            if (count($params) == 0) {

                // После этого должно идти название контроллера

                return;

            } else if (count($params) == 1) {

                // Название шаблона или функция (преобразует в view(...)) /

                $this->data_params = $this->calc_arg($params[0]);

                return;

            } else if (count($params) == 2 || count($params) == 3) {

                // Название шаблона с параметрами / Название шаблона или функция (преобразует в название) и функция (преобразуется в параметры) /

                if (empty($params[2])) $params[2] = "views";


                $this->data_params = [$this->calc_arg($params[0]), $this->calc_arg($params[1]), $params[2]];

                return;
            }

            $this->errors[] = "HL019-ROUTE_ERROR: Excess number of arguments on method ->get(arg1, arg2) ! " .
                "In stock arg2: " . count($params) . " expected  0, 1 or 2 ~ " .
                "Неправильное количество аргументов в методе ->get(arg1, arg2) ! Использовано в arg2:  " . count($params) . ", допускается 0, 1 или 2 аргумента.";

            ErrorOutput::add($this->errors);


        } else {

            $this->data_params = ["text" => $params];
        }


    }

    private function calc_arg($value)
    {

        if (is_string($value)) {

            return [$value];

        } else if (is_object($value)) {

            return $this->calculate_incoming_object($value);
        }

        return $value;

    }


}

