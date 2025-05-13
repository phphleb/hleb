<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;


use Hleb\Route\Where;

trait InsertWhereTrait
{
    /**
     * When added after get(), post(), etc. methods, sets up regular expression
     * checking of dynamic parts of the route.
     *
     * При добавлении после методов get(), post() и т.д. устанавливает проверку
     * динамических частей маршрута с помощью регулярных выражений.
     *
     * ```php
     * Route::get('/ru/{version}/{page}/', 'Content')
     *     ->where(['version' => '[a-z0-9]+', 'page' => '[a-z]+']);
     * ```
     */
    public function where(array $rules): Where
    {
        return new Where($rules);

    }
}
