<?php

declare(strict_types=1);

/*
 * Internal methods of working with routes.
 *
 * Внутренние методы работы с роутами.
 */

namespace Hleb\Constructor\Routes;

use Hleb\Constructor\Routes\Methods\{
    RouteMethodEnd
};
use Hleb\Scheme\Home\Constructor\Routes\RouteMethodStandard;

class MainRoute
{
    use \DeterminantStaticUncreated;

    protected static $objectMethods = [];

    protected static $dataMethods = [];

    protected static $number = 1000;

    // Returns the collected route data.
    // Возвращает собранные данные маршрутов.
    public function data() {
        return self::$dataMethods;
    }

    // Removes route information.
    // Удаляет информацию о маршрутах.
    public function delete() {
        self::$instance = null;
    }

    // Finish parsing routes.
    // Завершает парсинг маршрутов.
    public function end() {
        if (!is_null(self::$instance)) {
            self::$dataMethods = (new RouteMethodEnd(self::$instance))->data();
        }
        return null;
    }

    /**
     * @param RouteMethodStandard $method
     * @return null|static
     */
    protected static function create(RouteMethodStandard $method) {
        self::$objectMethods[] = $method;
        if ($method->approved()) {
            return self::instance();
        }
        return null;
    }

    // Adds a route to the others.
    // Добавляет маршрут к остальным.
    protected static function add(RouteMethodStandard $method) {
        $data = $method->data();
        self::$number++;
        $data['number'] = self::$number;
        self::$dataMethods[] = $data;
        return self::create($method);
    }

}