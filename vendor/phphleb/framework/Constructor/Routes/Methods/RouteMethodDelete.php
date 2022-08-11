<?php

declare(strict_types=1);

/*
 * Main DELETE-route processing.
 *
 * Обработка основного DELETE-роута.
 */

namespace Hleb\Constructor\Routes\Methods;


class RouteMethodDelete extends RouteMethodGet
{
    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'delete';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return ['delete'];
    }

}

