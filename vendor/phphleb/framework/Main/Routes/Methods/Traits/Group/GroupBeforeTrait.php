<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;


use Hleb\Route\Group\GroupMiddleware;

trait GroupBeforeTrait
{
    /**
     * Execution of the pre-controller.
     * The class method can be specified via '@', for example,
     * 'App\Middleware\DefaultMiddlewareBefore@{method}'.
     * If it is not specified, 'index' will be used.
     * $target can be set to App\Middleware\DefaultMiddlewareBefore::class,
     * then the controller method must be specified in the $method argument.
     * An alias for the middleware() method.
     *
     * Выполнение предварительного контроллера.
     * Указать метод класса можно через '@', например,
     * 'App\Middleware\DefaultMiddlewareBefore@{method}'.
     * Если не указан, будет использован 'index'.
     * В $target можно указать App\Middleware\DefaultMiddlewareBefore::class,
     * тогда в аргументе $method должен быть указан метод контроллера.
     * Псевдоним метода middleware().
     *
     * ```php
     *  Route::toGroup()->before(DefaultMiddleware::class);
     *     // ... //
     *  Route::endGroup();
     * ```
     */
    public function before(string $target, ?string $method = null, array $data = []):  GroupMiddleware
    {
        return new GroupMiddleware($target, $method, $data);
    }
}
