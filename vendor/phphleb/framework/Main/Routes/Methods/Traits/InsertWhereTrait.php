<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;


use Hleb\Route\Where;

trait InsertWhereTrait
{
    /**
     * When where( ... ) is added after of the method get( ... ), it sets a check
     * of dynamic parts of the route using regular expressions.
     * Route::get('/ru/{version}/{page}/', 'Content')
     *     ->where(['version' => '[a-z0-9]+', 'page' => '[a-z]+']);
     *
     * При добавлении после метода get( ... ) устанавливает проверку
     * динамических частей маршрута с помощью регулярных выражений.
     * Route::get('/ru/{version}/{page}/', 'Content')
     *     ->where(['version' => '[a-z0-9]+', 'page' => '[a-z]+']);
     */
    public function where(array $rules): Where
    {
        return new Where($rules);

    }
}
