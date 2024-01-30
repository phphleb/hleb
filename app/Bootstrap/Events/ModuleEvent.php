<?php

declare(strict_types=1);

namespace App\Bootstrap\Events;

use Hleb\Base\Event;
use Hleb\Constructor\Attributes\Dependency;

#[Dependency]
final class ModuleEvent extends Event
{
    /**
     * Executes before any method (called from the framework)
     * of the module controller and returns the summary data
     * of the method arguments.
     * It is understood that under numerous execution conditions,
     * the code will be moved to additional classes.
     * If the method returns false, then this is the same
     * as terminating the script.
     *
     * Выполняется перед любым методом (вызываемым из фреймворка)
     * контроллера модуля и возвращает итоговые данные
     * аргументов метода.
     * Подразумевается, что при многочисленных условиях выполнения
     * код будет вынесен в дополнительные классы.
     * Если метод возвращает false, то это аналогично
     * завершению работы скрипта.
     *
     * ```php
     * switch($module) {
     *     case 'example':
     *         return (new ExampleModuleEvent())->before($class, $method, $arguments);
     *     // ... //
     *     default:
     * }
     * return $arguments;
     * ```
     * @param string $module - name of the module used.
     *                       - название используемого модуля.
     */
    // public function before(string $class, string $method, array $arguments, string $module): array|false { return $arguments; }

    // public function after(string $class, string $method, string $module, mixed &$result): void { }
}
