<?php

declare(strict_types=1);

/*
 * Internal methods of working with routes.
 *
 * Внутренние методы работы с роутами.
 */

namespace Hleb\Constructor\Routes;

use Hleb\Main\Insert\BaseSingleton;
use Hleb\Constructor\Routes\Methods\{
    RouteMethodEnd
};
use Hleb\Scheme\Home\Constructor\Routes\RouteMethodStandard;

class MainRoute extends BaseSingleton
{
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
        Route::getInstance()->__destruct();
        parent::getInstance()->__destruct();
    }

    // Finish parsing routes.
    // Завершает парсинг маршрутов.
    public function end() {
        if (!is_null(self::getInstance())) {
            self::$dataMethods = (new RouteMethodEnd(self::getInstance()))->data();
        }
        return null;
    }

    // Adds a route to the others.
    // Добавляет маршрут к остальным.
    /**
     * @param RouteMethodStandard $method
     * @return null|static
     */
    protected static function create(RouteMethodStandard $method) {
        self::$objectMethods[] = $method;
        if ($method->approved()) {
            return self::getInstance();
        }
        return null;
    }

    // Returns the collected and prepared routes.
    // Возвращает собранные и подготовленные маршруты.
    protected static function add(RouteMethodStandard $method) {
        $data = $method->data();
        self::$number++;
        $data['number'] = self::$number;
        self::$dataMethods[] = $data;
        return self::create($method);
    }

}