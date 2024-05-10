<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;

use Route;

trait GroupTrait
{
    /**
     * Allows you to use group assignments within a function.
     *
     * Позволяет использовать назначение групп внутри функции.
     *
     * ```php
     * Route::toGroup()
     *     ->prefix('example')
     *     ->group(function () {
     *        Route::get('/first/', '1st');
     *        Route::get('/second/', '2nd');
     *     });
     *
     * ```
     */
    public function group(callable $fn): void
    {
        $fn();

        Route::endGroup();
    }
}
