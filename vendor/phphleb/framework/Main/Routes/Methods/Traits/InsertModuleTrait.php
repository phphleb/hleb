<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;


use Hleb\Route\Module;

trait InsertModuleTrait
{

    /**
     * Refers to the controller in the `/modules/<name>/controllers/` folder by the name of the module.
     * If method is not specified, 'index' will be used.
     *
     * Обращается к контроллеру в папке `/modules/<name>/controllers/` по имени модуля.
     * Если метод не указан, то будет использован 'index'.
     *
     * ```php
     *    use Modules\Demo\Controllers\MainController;
     *
     *    Route::any('/demo/')->module('demo', MainController::class);
     *  ```
     *
     * @param string $name - the name of the module in the `/modules/` folder.
     *                     - название модуля в папке `/modules/`.
     *
     * @param string $target - a controller class with a method.
     *                         For example 'Modules\Demo\Controllers\MainController@{method}'
     *                         or Modules\Demo\Controllers\MainController::class.
     *                       - класс контроллера с методом.
     *                         Например 'Modules\Demo\Controllers\MainController@{method}'
     *                         или Modules\Demo\Controllers\MainController::class.
     *
     * @param string|null $method - controller method if not passed to $target.
     *                            - метод контроллера, если не был передан в $target.
     *
     * @return Module
     *
     * @see InsertControllerTrait::controller()
     */
    public function module(string $name, string $target, ?string $method = null): Module
    {
        return new Module($name, $target, $method);
    }
}
