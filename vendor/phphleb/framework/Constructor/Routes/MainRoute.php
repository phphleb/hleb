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

    /**
     * @param string $name
     * @param mixed $arguments
     * @return array
     * @internal
     */
    public function __call($name, $arguments) {
        switch ($name) {
            case 'data':
                return $this->data();
                break;
            case 'clear':
                $this->clear();
                break;
            case 'end':
                $this->end();
                break;
        }
    }

    // Returns the collected route data.
    // Возвращает собранные данные маршрутов.
    /** @internal */
    protected function data() {
        return self::$dataMethods;
    }

    // Removes route information.
    // Удаляет информацию о маршрутах.
    /** @internal */
    protected function clear() {
        self::$instance = false;
    }

    // Finish parsing routes.
    // Завершает парсинг маршрутов.
    /** @internal */
    protected function end() {
        if (!is_null(self::$instance)) {
            self::$dataMethods = (new RouteMethodEnd(self::$instance))->data();
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
            return self::instance();
        }
        return null;
    }

    // Returns the collected and prepared routes.
    // Возвращает собранные и подготовленные маршруты.
    protected static function insert(RouteMethodStandard $method) {
        $data = $method->data();
        self::$number++;
        $data['number'] = self::$number;
        self::$dataMethods[] = $data;
        return self::create($method);
    }

}

