<?php

namespace Hleb\Main;

use DeterminantStaticUncreated;

class Functions
{
    use DeterminantStaticUncreated;

    static function mainHttp() // "https://"
    {
       return HLEB_PROJECT_PROTOCOL;
    }

    static function mainUrl() // "site.ru/main/?get=on"
    {
       return  self::convertUrl($_SERVER["REQUEST_URI"]);
    }

    static function mainClearUrl() // "/main/"
    {
        return explode("?", $_SERVER["REQUEST_URI"])[0];
    }

    static function clearMainUrl() // "/main/url/"
    {
       return self::convertUrl(preg_replace('~\?.*$~', '', $_SERVER['REQUEST_URI']));
    }

    static function convertUrl($url) // "/main/url/"
    {
        return rawurldecode($url);
    }

    static function mainHostUrl() // "site.ru"
    {
          return HLEB_MAIN_DOMAIN;
    }

    static function mainFullHostUrl()
    {
        return self::mainHttp().self::mainHostUrl();
    }

    static function mainRequestUrl() // "/main/?get=on"
    {
         return $_SERVER["REQUEST_URI"];
    }


}


