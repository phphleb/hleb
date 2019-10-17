<?php

namespace Hleb\Constructor\Handlers;

use DeterminantStaticUncreated;

class Request
{
    use DeterminantStaticUncreated;

    private static $request = [];

    private static $close = false;

    public static function get(string $name = null)
    {
        return empty($name) ? self::$request : self::$request[$name];
    }

    public static function add(string $name, string $value)
    {
        if(!self::$close) self::$request[$name] = is_numeric($value) ? floatval($value) : $value;
    }

    public static function close()
    {
        self::$close = true;
    }

    public static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getReferer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    public static function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function getHttpHeader($value = null)
    {
        return $value != null ? (isset($_SERVER[$value]) ? $_SERVER[$value] : null ) : $_SERVER;
    }

    public static function isXmlHttpRequest()
    {
        return $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    public static function getCookie($value = null)
    {
        return $value != null ? (isset($_COOKIE[$value]) ? $_COOKIE[$value] : null ) : $_COOKIE;
    }

    public static function getFiles()
    {
        return $_FILES;
    }

    public static function getUrlParameter()
    {
        return $_SERVER['PATH_INFO'];
    }

    public static function getRemoteAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function getGet($value = null)
    {
        return $value != null ? (isset($_GET[$value]) ? $_GET[$value] : null ) : $_GET;
    }

    public static function getPost($value = null)
    {
        return $value != null ? (isset($_POST[$value]) ? $_POST[$value] : null ) : $_POST;
    }

    public static function getRequest($value = null)
    {
        return $value != null ? (isset($_REQUEST[$value]) ? $_REQUEST[$value] : null ) : $_REQUEST;
    }

}

