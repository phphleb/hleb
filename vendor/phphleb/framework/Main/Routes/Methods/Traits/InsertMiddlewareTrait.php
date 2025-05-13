<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;


use Hleb\Route\Middleware;

trait InsertMiddlewareTrait
{
    /**
     * Execution of the pre-controller.
     * The class method can be specified via '@', for example,
     * 'App\Middleware\DefaultMiddlewareBefore@{method}'.
     * If it is not specified, 'index' will be used.
     * $target can be set to App\Middleware\DefaultMiddlewareBefore::class,
     * then the controller method must be specified in the $method argument.
     *
     * Выполнение предварительного контроллера.
     * Указать метод класса можно через '@', например,
     * 'App\Middleware\DefaultMiddlewareBefore@{method}.
     * Если не указан, будет использован 'index'.
     * В $target можно указать App\Middleware\DefaultMiddlewareBefore::class,
     * тогда в аргументе $method должен быть указан метод контроллера.
     *
     * ```php
     *  Route::get('/', view('home'))->middleware(DefaultMiddleware::class);
     * ```
     */
    public function middleware(string $target, ?string $method = null, array $data = []): Middleware
    {
        return new Middleware($target, $method, $data);
    }
}
