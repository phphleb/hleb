<?php

declare(strict_types=1);

/*
 * Main PUT-route processing.
 *
 * Обработка основного PUT-роута.
 */

namespace Hleb\Constructor\Routes\Methods;


class RouteMethodPut extends RouteMethodGet
{
    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'put';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return ['put'];
    }

}

