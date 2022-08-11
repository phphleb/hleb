<?php

declare(strict_types=1);

/*
 * Main POST-route processing.
 *
 * Обработка основного POST-роута.
 */

namespace Hleb\Constructor\Routes\Methods;


class RouteMethodPost extends RouteMethodGet
{
    // The unique name of the route.
    // Уникальный идентификатор роута.
    protected function getName() {
          return 'post';
    }

    // The request type for the route.
    // Тип запроса для роута.
    protected function getHttpMethodType() {
        return ['post'];
    }

}

