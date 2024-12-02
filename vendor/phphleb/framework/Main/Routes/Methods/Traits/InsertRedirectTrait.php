<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;

use Hleb\Route\Redirect;

trait InsertRedirectTrait
{
    /**
     * Route-level redirection, when added,
     * all matches to the rules of a given route will be redirected.
     * Cannot be used with one of the controller types or the after() method.
     * The $location value must be the same as implied in the Location HTTP header.
     * Dynamic route parameters can be inserted into the target address.
     * For example:
     *
     * Перенаправление на уровне маршрута, при добавлении
     * все совпадения с правилами данного маршрута будут перенаправлены.
     * Не может быть использовано вместе с одним из типов контроллеров или методом after().
     * Значение $location должно быть аналогичным как подразумевается в HTTP-заголовке Location.
     * Динамические параметры маршрута могут быть вставлены в целевой адрес.
     * Например:
     *
     * ```php
     *  Route::get('/old/address/{name}')->redirect('/new/address/{%name%}', 301);
     * ```
     */
    public function redirect(string $location, int $status = 302): Redirect
    {
        return new Redirect($location, $status);
    }
}
