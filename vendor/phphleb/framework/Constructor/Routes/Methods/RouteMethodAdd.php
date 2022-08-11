<?php

declare(strict_types=1);

/*
 * Main GET-route processing.
 *
 * Обработка основного GET-роута.
 */

namespace Hleb\Constructor\Routes\Methods;


class RouteMethodAdd extends RouteMethodGet
{
    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'add';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return ['get'];
    }

}

