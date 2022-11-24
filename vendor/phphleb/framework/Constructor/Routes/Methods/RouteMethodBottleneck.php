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

class RouteMethodBottleneck extends RouteMethodGet
{
    private static $isExists = false;

    public function __construct(StandardRoute $instance, string $route, bool $isActive, $params, $code = 302) {

        if (self::$isExists) {
            $this->errors[] = "HL058-ROUTE_ERROR: Error in ->bottleneck() method!" .
                " The method must not be repeated. ~ " .
                "Ошибка в методе ->bottleneck() ! Метод не должен повторяться.";
            ErrorOutput::get($this->errors);
        }
        self::$isExists = true;

        if ($code > 399 && $code < 300) {
            $this->errors[] = "HL059-ROUTE_ERROR: Error in ->bottleneck() method!" .
                " Wrong http redirect code. ~ " .
                "Ошибка в методе ->bottleneck() ! Неправильно задан HTTP-код редиректа.";
            ErrorOutput::get($this->errors);
        }

        parent::__construct($instance, $route , $params, ['add' => $isActive ? ['redirect' => (int)$code] : []]);
    }

    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'bottleneck';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return HLEB_HTTP_TYPE_SUPPORT;
    }
}

