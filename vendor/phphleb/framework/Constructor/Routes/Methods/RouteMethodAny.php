<?php

declare(strict_types=1);

/*
 * 'get', 'post', 'delete', 'put', 'patch', 'options' processing.
 *
 * Обработка 'get', 'post', 'delete', 'put', 'patch', 'options'.
 */

namespace Hleb\Constructor\Routes\Methods;


class RouteMethodAny extends RouteMethodGet
{
    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'any';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return HLEB_HTTP_TYPE_SUPPORT;
    }

}

