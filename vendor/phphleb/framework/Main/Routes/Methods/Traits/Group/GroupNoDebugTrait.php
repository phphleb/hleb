<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;


use Hleb\Route\Group\GroupNoDebug;

trait GroupNoDebugTrait
{
    /**
     * Disables the debug panel output for group routes.
     * Only relevant in DEBUG mode.
     * The implication is that this is not a workaround, and under these conditions
     * there is really no need to display a debug panel, such as a GET request
     * to an API expecting a JSON response.
     *
     * Отключает вывод отладочной панели у маршрутов группы.
     * Актуально только в DEBUG-режиме.
     * Подразумевается, что это не временное решение, а в этих условиях действительно
     * не нужно выводить панель отладки, например, GET-запрос к API, ожидающий в ответ JSON.
     *
     * ```php
     *  Route::toGroup()->noDebug();
     *    // ... //
     *  Route::endGroup();
     * ```
     */
    public function noDebug(): GroupNoDebug
    {
        return new GroupNoDebug();
    }
}
