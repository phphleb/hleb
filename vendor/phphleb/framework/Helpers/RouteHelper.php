<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Cache\RouteMark;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\HlebBootstrap;
use Hleb\Main\Console\Commands\SearchRoute;

/**
 * @internal
 */
final class RouteHelper
{
    /**
     * Returns an array containing route data from the route cache.
     *
     * Возвращает массив с данными маршрутов из кеша маршрутов.
     */
    public function getCachedData(): array
    {
        $routes = $this->getRawCachedData();

        $result = [];
        foreach ($routes as $key => $route) {
            $result[] = [
                'method' => $key,
                'route' => $route['a'],
                'name' => $route['i'] ?? null,
                'domain' => $route['h'] ?? null];
        }

        return $result;
    }

    /**
     * Takes an address as input and returns supported HTTP methods or null.
     * Only for the specified domain.
     *
     * Принимает на вход адрес и возвращает поддерживаемые HTTP-методы или null.
     * Только для указанного домена.
     */
    public function getRouteHttpMethods(string $uri, string $domain): array
    {
       $mandatory = ['HEAD', 'OPTIONS'];
       $result = [];
       $search = new SearchRoute();
       foreach(HlebBootstrap::HTTP_TYPES as $method) {
           if (\in_array($method, $mandatory)) {
               continue;
           }
           $status = $search->run($uri, $method, $domain);
           if (\trim($status) === 'OK') {
               $result[] = $method;
           }
       }

       return \array_unique(\array_merge($mandatory, $result));
    }

    /**
     * @internal
     */
   public function getRawCachedData(): array
   {
       $dir = SystemSettings::getRealPath('@storage/cache/routes/Preview');
       if (!$dir) {
           return [];
       }
       $postfixList = \array_map('ucfirst', \array_map('strtolower', HlebBootstrap::HTTP_TYPES));
       $routes = [];
       foreach ($postfixList as $postfix) {
           $class = RouteMark::getRouteClassName(RouteMark::PREVIEW_PREFIX . $postfix);
           if (!\file_exists($dir . DIRECTORY_SEPARATOR . $class . '.php')) {
               continue;
           }
           if (!\class_exists($class, false)) {
               require $dir . DIRECTORY_SEPARATOR . $class . '.php';
           }
           $routes[\strtolower($postfix)] = $class::getData();
       }
       return $routes;
   }
}
