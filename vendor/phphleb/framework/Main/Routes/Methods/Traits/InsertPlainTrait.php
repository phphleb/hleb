<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;


use Hleb\Route\Plain;

trait InsertPlainTrait
{
    /**
     * Indicates loading optimization for simple text or HTML
     * pages that do not require sessions or anything else.
     * Applies an action to a route.
     * If present, overrides the 'session.enabled' value
     * in the framework configuration.
     * (!) A positive value disables the initialization
     * of sessions and Cookies when accessing a route.
     *
     * Указывает на оптимизацию загрузки для простых текстовых
     * или HTML страниц не требующих сессий и прочего.
     * Применяет действие к маршруту.
     * При наличии переопределяет значение 'session.enabled'
     * в конфигурации фреймворка.
     * (!) Положительное значение отключает инициализацию сессий
     * и Cookies при обращении к маршруту.
     *
     * ```php
     *     Route::any('/site/info', view('info'))->plain();
     *  ```
     *
     * @param bool $on - simplification mode active.
     *
     *                 - активность режима упрощения.
     */
    public function plain(bool $on = true): Plain
    {
        return new Plain($on);
    }
}
