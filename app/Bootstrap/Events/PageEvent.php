<?php

declare(strict_types=1);

namespace App\Bootstrap\Events;

use Hleb\Base\Event;
use Hleb\Constructor\Attributes\Dependency;

#[Dependency]
final class PageEvent extends Event
{
    /**
     * Executes before any method (called from the framework)
     * of the controller, designated in routes as page(),
     * and returns the summary data of the method arguments.
     * If the method returns false, then this is the same
     * as terminating the script.
     *
     * Выполняется перед любым методом (вызываемым из фреймворка)
     * контроллера, обозначенного в маршрутах как page(),
     * и возвращает итоговые данные аргументов метода.
     * Если метод возвращает false, то это аналогично
     * завершению работы скрипта.
     *
     * ```php
     * switch($page) {
     *     case 'hlogin':
     *         return (new RegistrationPageEvent())->before($class, $method, $arguments);
     *     // ... //
     *     default:
     * }
     * return $arguments;
     * ```
     * @param string $page - the name of the page wrapper used.
     *                          - название используемой оболочки для страниц.
     */
    // public function before(string $class, string $method, array $arguments, string $page): array|false { return $arguments; }

    // public function after(string $class, string $method, string $page, mixed &$result): void { }
}
