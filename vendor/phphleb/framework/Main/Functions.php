<?php

declare(strict_types=1);

namespace Hleb\Main;

use DeterminantStaticUncreated;

class Functions
{
    use DeterminantStaticUncreated;

    public static function mainHttp() // "https://"
    {
       return HLEB_PROJECT_PROTOCOL;
    }

    public static function mainUrl() // "site.ru/main/?get=on"
    {
       return  self::convertUrl($_SERVER['REQUEST_URI']);
    }

    public static function mainClearUrl() // "/main/"
    {
        return explode('?', $_SERVER['REQUEST_URI'])[0];
    }

    public static function clearMainUrl() // "/main/url/"
    {
       return self::convertUrl(preg_replace('~\?.*$~', '', $_SERVER['REQUEST_URI']));
    }

    public static function convertUrl($url) // "/main/url/"
    {
        return rawurldecode($url);
    }

    public static function mainHostUrl() // "site.ru"
    {
          return HLEB_MAIN_DOMAIN;
    }

    public static function mainFullHostUrl()
    {
        return self::mainHttp().self::mainHostUrl();
    }

    public static function mainRequestUrl() // "/main/?get=on"
    {
         return $_SERVER['REQUEST_URI'];
    }


}


