<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;


use Hleb\Route\Group\GroupProtect;

trait GroupProtectTrait
{
    /**
     * Applies the specified route protection to subsequent methods in the group.
     *
     * Применяет указанную защиту маршрута к последующим методам в группе.
     *
     * ```php
     *  Route::toGroup()->protect();
     *       // ... //
     *  Route::endGroup();
     * ```
     */
    public function protect(string|array $rules = 'CSRF'): GroupProtect
    {
        return new GroupProtect($rules);
    }
}
