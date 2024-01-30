<?php

declare(strict_types=1);

namespace App\Bootstrap\Events;

use Hleb\Base\Event;
use Hleb\Constructor\Attributes\Dependency;

#[Dependency]
final class ControllerEvent extends Event
{
    /**
     * Executed before any controller method (called from the framework)
     * and returns the summary data of the method arguments.
     * It is understood that under numerous execution conditions,
     * the code will be moved to additional classes.
     * If the method returns false, then this is the same
     * as terminating the script.
     *
     * Выполняется перед любым (вызываемым из фреймворка) методом
     * контроллера и возвращает итоговые данные аргументов метода.
     * Подразумевается, что при многочисленных условиях выполнения
     * код будет вынесен в дополнительные классы.
     * Если метод возвращает false, то это аналогично
     * завершению работы скрипта.
     *
     * ```php
     * switch([$class, $method]) {
     *   case [DefaultController::class, 'index']:
     *       return (new DefaultControllerEvent())->beforeIndex($arguments);
     *   // ... //
     *   default:
     * }
     * return $arguments;
     * ```
     */
    // public function before(string $class, string $method, array $arguments): array|false { return $arguments; }

    // public function after(string $class, string $method, mixed &$result): void { }
}
