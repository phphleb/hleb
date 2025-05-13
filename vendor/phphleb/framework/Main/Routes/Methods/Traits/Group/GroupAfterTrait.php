<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;


use Hleb\Route\Group\GroupAfter;

trait GroupAfterTrait
{
    /**
     * Execute a subsequent controller that will be executed
     * after the route's main controller(action).
     * The class method can be specified via '@', for example,
     * 'App\Middleware\DefaultMiddlewareAfter@{method}'.
     * If it is not specified, 'index' will be used.
     * $target can be set to App\Middleware\DefaultMiddlewareAfter::class,
     * then the controller method must be specified in the $method argument.
     *
     * Выполнение последующего контроллера, который будет выполнен
     * после основного контроллера(действия) маршрута.
     * Указать метод класса можно через '@', например,
     * 'App\Middleware\DefaultMiddlewareAfter@{method}'.
     * Если не указан, будет использован 'index'.
     * В $target можно указать App\Middleware\DefaultMiddlewareAfter::class,
     * тогда в аргументе $method должен быть указан метод контроллера.
     *
     * ```php
     *   Route::toGroup()->after(DefaultMiddlewareAfter::class);
     *      // ... //
     *   Route::endGroup();
     *  ```
     */
    public function after(string $target, ?string $method = null, array $data = []):  GroupAfter
    {
        return new GroupAfter($target, $method, $data);
    }
}
