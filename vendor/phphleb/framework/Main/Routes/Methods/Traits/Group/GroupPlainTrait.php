<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;


use Hleb\Route\Group\GroupPlain;

trait GroupPlainTrait
{
    /**
     * Indicates loading optimization for simple text or HTML
     * pages that do not require sessions or anything else.
     * Applies the action to routes in the group.
     * If present, overrides the 'session.enabled' value
     * in the framework configuration.
     * (!) A positive value disables session initialization
     * and Cookies when accessing routes in a group.
     *
     * Указывает на оптимизацию загрузки для простых текстовых
     * или HTML страниц не требующих сессий и прочего.
     * Применяет действие к маршрутам в группе.
     * При наличии переопределяет значение 'session.enabled'
     * в конфигурации фреймворка.
     * (!) Положительное значение отключает инициализацию сессий
     * и Cookies при обращении к маршрутам в группе.
     *
     * ```php
     *  Route::toGroup()->plain();
     *     // ... //
     *  Route::endGroup();
     *```
     *
     * @param bool $on - simplification mode active.
     *
     *                 - активность режима упрощения.
     */
    public function plain(bool $on = true): GroupPlain
    {
        return new GroupPlain($on);
    }
}
