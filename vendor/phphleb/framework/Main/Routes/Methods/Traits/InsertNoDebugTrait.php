<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;

use Hleb\Route\NoDebug;

trait InsertNoDebugTrait
{
    /**
     * Applies the specified protection to the route.
     * Only relevant in DEBUG mode.
     * The implication is that this is not a workaround, and under these conditions
     * there is really no need to display a debug panel, such as a GET request
     * to an API expecting a JSON response.
     *
     * Отключает вывод отладочной панели у маршрута.
     * Актуально только в DEBUG-режиме.
     * Подразумевается, что это не временное решение, а в этих условиях действительно
     * не нужно выводить панель отладки, например, GET-запрос к API, ожидающий в ответ JSON.
     *
     * ```php
     *    Route::get('/site/healthcheck', 'OK')->noDebug();
     * ```
     */
    public function noDebug(): NoDebug
    {
        return new NoDebug();
    }
}
