<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\FileResourceModificationException;
use Hleb\Main\Routes\Search\FindRoute;

/**
 * Implements external access from the framework to the use of some console commands.
 *
 * Реализует внешний доступ из фреймворка к использованию некоторых консольных команд.
 */
#[Accessible]
final class ConsoleAdapter
{
    /**
     * Updating the route cache from the actual route map.
     *
     * Обновление кеша маршрутов из актуальной карты маршрутов.
     */
   public function updateRouteCache(): void
   {
       (new ConsoleHandler())->updateRouteCache();
   }

    /**
     * Clearing the Twig cache. Returns the result of checking for
     * the connection of this template engine.
     *
     * Очистка кеша Twig. Возвращает результат проверки на подключение
     * этого шаблонизатора.
     */
   public function updateTwigCache(): bool
   {
       if (!\class_exists('Twig\Environment')) {
           return false;
       }
       (new ConsoleHandler())->updateTwigCache();

       return true;
   }

    /**
     * Returns a check to see if the URL matches the route map, while
     * Request errors and site blocking are counted as a not found option.
     *
     * Возвращает проверку на совпадение URL с картой маршрутов, при этом
     * ошибки запроса и блокировка сайта считаются как не найденный вариант.
     */
   public function searchRoute(string $url, $method = 'GET', ?string $domain = null): bool
   {
       $handler = (new FindRoute($url));
       $search = (bool)$handler->one($method, $domain);
       if ($handler->isBlocked() || $handler->getError()) {
           return false;
       }

       return $search;
   }

    /**
     * Locks or unlocks all project routes.
     * May be needed to temporarily block a running project on the framework.
     *
     * Блокирует или разблокирует все маршруты проекта.
     * Может потребоваться для временной блокировки работающего проекта на фреймворке.
     *
     * @param bool $lockStatus - blocking status.
     *                         - статус блокировки.
     */
   public function lockProject(bool $lockStatus): void
   {
       $file = SystemSettings::getRealPath('@storage/cache/routes/lock-status.info');
       \hl_create_directory($file);
       \file_put_contents($file, (int)$lockStatus);
       @\chmod($file, 0664);
   }
}
