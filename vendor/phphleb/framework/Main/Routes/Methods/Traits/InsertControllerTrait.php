<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;


use Hleb\Route\Controller;

trait InsertControllerTrait
{
    /**
     * Assignment of the controller.
     * The class method can be specified via '@', for example,
     * 'App\Controllers\DefaultController@{method}'
     * If it is not specified, 'index' will be used.
     * $target can be set to App\Controllers\DefaultController::class,
     * then the controller method must be specified in the $method argument.
     *
     * Назначение контроллера.
     * Указать метод класса можно через '@', например,
     * 'App\Controllers\DefaultController@{method}'.
     * Если не указан, будет использован 'index'.
     * В $target можно указать App\Controllers\DefaultController::class,
     * тогда в аргументе $method должен быть указан метод контроллера.
     *
     * ```php
     *   Route::get('/user/')->controller(UserController::class);
     * ```
     */
    public function controller(string $target, ?string $method = null): Controller
    {
        return new Controller($target, $method);
    }
}
