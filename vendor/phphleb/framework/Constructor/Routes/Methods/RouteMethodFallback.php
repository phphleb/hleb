<?php

declare(strict_types=1);

/*
 * 'get', 'post', 'delete', 'put', 'patch', 'options' processing for all paths.
 *
 * Обработка 'get', 'post', 'delete', 'put', 'patch', 'options' для всех путей.
 */

namespace Hleb\Constructor\Routes\Methods;


use Hleb\Main\Errors\ErrorOutput;
use Hleb\Scheme\Home\Constructor\Routes\StandardRoute;

class RouteMethodFallback extends RouteMethodGet
{
    private static $isExists = false;

    public function __construct(StandardRoute $instance, $params) {

        if (self::$isExists) {
            $this->errors[] = "HL056-ROUTE_ERROR: Error in ->fallback() method!" .
                " The method must not be repeated. ~ " .
                "Ошибка в методе ->fallback() ! Метод не должен повторяться.";
            ErrorOutput::get($this->errors);
        }
        self::$isExists = true;

        parent::__construct($instance, '*' , $params);
    }

    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'fallback';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return HLEB_HTTP_TYPE_SUPPORT;
    }
}

