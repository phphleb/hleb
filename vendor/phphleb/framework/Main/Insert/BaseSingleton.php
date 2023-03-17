<?php


namespace Hleb\Main\Insert;


class BaseSingleton
{
    private static $instances = [];

    protected function __construct() {}

    protected function __clone() {}

    /** @internal */
    public static function getInstance() {
        $className = get_called_class();
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new static;
        }
        return self::$instances[$className];
    }

    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}