<?php

declare(strict_types=1);

class NanoRoute
{
    use \DeterminantStaticUncreated;

    private static $params = [];

    //$param ~ ["route"=>"", "methods"=>["post"], "controller"=>"", "protected"=>false, "arguments"=>[], "autoloader" => false, "session_saved" => true];

    public static function get(array $param){

        self::$params[]= $param;
    }

    public static function run(){

      if(self::$params) new \HlebNanoRouter(self::$params);

    }
}

