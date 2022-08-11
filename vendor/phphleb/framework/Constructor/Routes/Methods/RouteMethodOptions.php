<?php

declare(strict_types=1);

/*
 * Main OPTIONS-route processing.
 *
 * Обработка основного OPTIONS-роута.
 */

namespace Hleb\Constructor\Routes\Methods;


class RouteMethodOptions extends RouteMethodGet
{
    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'options';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return ['options'];
    }

}

