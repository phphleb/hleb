<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;


use Hleb\Route\Module;

trait InsertModuleTrait
{

    /**
     * Refers to the controller in the `/modules/<name>/controllers/` folder by the name of the module.
     *
     * Обращается к контроллеру в папке `/modules/<name>/controllers/` по имени модуля.
     *
     * @param string $name - the name of the module in the `/modules/` folder.
     *                     - название модуля в папке `/modules/`.
     *
     * @param string $target - a controller class with a method.
     *                         For example 'App\Controllers\DefaultModuleController@{method}'
     *                         or App\Controllers\DefaultModuleController::class.
     *                       - класс контроллера с методом.
     *                         Например 'App\Controllers\DefaultModuleController@{method}'
     *                         или App\Controllers\DefaultModuleController::class.
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
