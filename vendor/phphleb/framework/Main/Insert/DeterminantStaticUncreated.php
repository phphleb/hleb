<?php

declare(strict_types=1);

/*
 * Forming pseudo-singleton trait.
 *
 * Трейт формирующий псевдо-синглетон.
 */

trait DeterminantStaticUncreated
{
    private static $instance = null;

    protected function __construct() {}

    protected function __clone() {}

    /**
     * @internal
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        } elseif (is_string(self::$instance)) {
            hleb_system_log('Object is destruct');
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args) {
        return call_user_func_array([self::instance(), $method], $args);
    }

    public function __wakeup() {
        throw new \Exception("Cannot unserialize class");
    }
}

