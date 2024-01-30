<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;

use Hleb\Route\Page;

trait InsertPageTrait
{
    /**
     * The purpose of a 'page', which is a controller that has HTML resources
     * added before and after it, making it a full-fledged page of a certain type.
     * The class method can be specified via '@', for example,
     * `App\Controllers\DefaultController@{method}`
     * If it is not specified, 'index' will be used.
     * $target can be set to App\Controllers\DefaultController::class,
     * then the controller method must be specified in the $method argument.
     *
     * Назначение 'страницы', представляющей из себя контроллер, к которому добавлены
     * ресурсы HTML до и после, делающие его полноценной страницей определенного типа.
     * Указать метод класса можно через '@', например,
     * 'App\Controllers\DefaultController@{method}'.
     * Если не указан, будет использован 'index'.
     * В $target можно указать App\Controllers\DefaultController::class,
     * тогда в аргументе $method должен быть указан метод контроллера.
     */
    public function page(string $type, string $target, ?string $method = null): Page
    {
        return new Page($type, $target, $method);
    }
}
