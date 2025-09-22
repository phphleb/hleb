<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Data;

use Hleb\Constructor\Cache\CacheRoutes;
use Hleb\Constructor\Cache\RouteMark;
use Hleb\RouteColoredException;
use Hleb\Main\Routes\Update\RouteData;

/**
 * @internal
 */
final class RoutesPreview
{
    /**
     * Returns route data according to the HTTP method.
     * If there is no route cache, it will be generated.
     *
     * Возвращает данные маршрутов согласно HTTP-методу.
     * Если кеш маршрутов отсутствует, то будет сгенерирован.
     */
    public static function getByMethod(string $method): array
    {
        $method = \ucfirst(\strtolower($method));
        $class = RouteMark::getRouteClassName(RouteMark::PREVIEW_PREFIX . $method);
        $file = SystemSettings::getRealPath("@storage/cache/routes/Preview/$class.php");
        if (!\class_exists($class, false)) {
            $infoClassName = RouteMark::getRouteClassName(RouteMark::INFO_CLASS_NAME);
            if (!$file && !SystemSettings::getRealPath("@storage/cache/routes/$infoClassName.php")) {
                $routes = (new RouteData())->dataExtraction();
                try {
                    if ((new CacheRoutes($routes))->save() !== false) {
                        require $file;
                    }
                } catch (RouteColoredException) {
                    return [];
                }
            }
        }
        if (!class_exists($class)) {
            require $file;
        }
        try {
            // Маршруты могут быть изменены без наличия этого метода.
            /** @var object $class */
            return $class::getData();
        } catch (\Throwable) {
            return [];
        }
    }
}
