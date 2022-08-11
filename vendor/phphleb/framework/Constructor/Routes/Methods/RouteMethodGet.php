<?php

declare(strict_types=1);

/*
 * Main route processing.
 *
 * Обработка основного роута.
 */

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
     * @param StandardRoute $instance
     * @param string $routePath
     * @param string|object|Closure|array $params
     */
    function __construct(StandardRoute $instance, string $routePath, $params = []) {
        $this->methodTypeName = $this->getName();
        $this->type = $this->getHttpMethodType();
        $this->instance = $instance;
        if (is_array($params)) {unset($params[HLEB_TAG_INTERNAL]);}
        $this->calc($routePath, $params);
    }

    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
        return 'get';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return ['get'];
    }

    // Parsing and initial data validation.
    // Разбор и первоначальная проверка данных.
    protected function calc($routePath, $params) {
        $this->dataPath = $routePath;
        if (is_array($params)) {
            if (count($params) == 0) {
                // This should be followed by the name of the controller.
                // После этого должно идти название контроллера.
                return;
            } else if (count($params) == 1) {
                // Template name or function (converts to view (...)).
                // Название шаблона или функция (преобразует в view(...)).
                $this->dataParams = $this->calcArg($params[0]);
                return;
            } else if (count($params) == 2 || count($params) == 3) {
                // Template name with parameters / Template name or function (converts to name) and function (converts to parameters).
                // Название шаблона с параметрами / Название шаблона или функция (преобразует в название) и функция (преобразуется в параметры).
                if (empty($params[2])) $params[2] = "views";
                $this->dataParams = [$this->calcArg($params[0]), $this->calcArg($params[1]), $params[2]];
                return;
            }
            $this->errors[] = "HL019-ROUTE_ERROR: Excess number of arguments on method ->" . $this->getName() . "(arg1, arg2) ! " .
                "In stock arg2: " . count($params) . " expected  0, 1 or 2 ~ " .
                "Неправильное количество аргументов в методе ->" . $this->getName() . "(arg1, arg2) ! Использовано в arg2:  " . count($params) . ", допускается 0, 1 или 2 аргумента.";
            ErrorOutput::add($this->errors);
        } else {
            $this->dataParams = ["text" => $params];
        }
    }

    // Checking values.
    // Проверка значений.
    protected function calcArg($value) {
        if (is_string($value)) {
            return [$value];
        } else if (is_object($value)) {
            return $this->calculateIncomingObject($value);
        }
        return $value;
    }
}

