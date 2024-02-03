<?php

namespace modules_template\module_class_name_template\Controllers;

use Hleb\Base\Module;
use Hleb\Constructor\Data\View;

/**
 * Template for module controller.
 * You can assign it to a route like this:
 *
 * Шаблон для контроллера модуля.
 * Назначить его в маршруте можно следующим образом:
 *
 * ```php
 * use Modules\Admin\Controllers\DefaultModuleController;
 *
 * Route::get(...)->module('module_base_name_template', DefaultModuleController::class);
 * ```
 */
class DefaultModuleController extends Module
{
    public function index(): View
    {
        /*
         * An example of using the template
         * (to be included from the /views/ folder of the current module).
         *
         * Пример использования шаблона
         * (будет загружен из папки /views/ текущего модуля).
         */
        return view("example");
    }
}
