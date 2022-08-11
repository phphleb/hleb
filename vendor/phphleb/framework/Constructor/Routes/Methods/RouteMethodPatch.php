<?php

declare(strict_types=1);

/*
 * Main PATCH-route processing.
 *
 * Обработка основного PATCH-роута.
 */

namespace Hleb\Constructor\Routes\Methods;


class RouteMethodPatch extends RouteMethodGet
{
    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'patch';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return ['patch'];
    }

}

