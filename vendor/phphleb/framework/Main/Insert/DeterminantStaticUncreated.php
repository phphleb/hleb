<?php

declare(strict_types=1);

trait DeterminantStaticUncreated
{
    private static $instance;

    private function __construct()
    {

    }

    public function __clone()
    {

    }

    public static function instance()
    {

        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }


}

