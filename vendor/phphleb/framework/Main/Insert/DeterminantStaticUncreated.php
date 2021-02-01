<?php

declare(strict_types=1);

/*
 * Forming pseudo-singleton trait.
 *
 * Трейт формирующий псевдо-синглетон.
 */

trait DeterminantStaticUncreated
{
    private static $instance;

    protected function __construct() {}

    protected function __clone() {}

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        } elseif (is_bool(self::$instance)) {
            throw new \Exception('Object is destruct');
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args) {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    public function __wakeup() {
        throw new \Exception("Cannot unserialize class");
    }
}

