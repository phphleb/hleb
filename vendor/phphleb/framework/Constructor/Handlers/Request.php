<?php

namespace Hleb\Constructor\Handlers;

use DeterminantStaticUncreated;

class Request
{
    use DeterminantStaticUncreated;

    private static $request = [];

    private static $close = false;

    const NEEDED_TAGS = ['<', '>'];

    const REPLACING_TAGS = ['&lt;', '&gt;'];

    private static $post = null;

    private static $get = null;

    private static $req = null;

    private static $initial_cookie = null;

    private static $initial_session = null;

    private static $head = null;

    private static $uri = null;

    private static $url = null;

    private static $referer = null;

    private static $resources = null;

    private static $convert_uri= null;

    public static function getInitialSession($name = null)
    {
        return is_null($name) ? self::$initial_session : (isset(self::$initial_session[$name]) ? self::$initial_session[$name] : null);
    }

    public static function getInitialCookie($name = null)
    {
        return is_null($name) ? self::$initial_cookie : (isset(self::$initial_cookie[$name]) ? self::$initial_cookie[$name] : null);
    }

    public static function getSession($name = null)
    {
        return is_null($name) ? $_SESSION ?? [] : (isset($_SESSION) && isset($_SESSION[$name]) ? $_SESSION[$name] : null);
    }

    public static function getCookie($name = null)
    {
        return is_null($name) ? self::clearData($_COOKIE ?? []) : (isset($_COOKIE) && isset($_COOKIE[$name]) ? self::clearData($_COOKIE[$name]) : null);
    }

    public static function get(string $name = '')
    {
        return empty($name) ? self::$request : (self::$request[$name] ?? null);
    }

    public static function getString(string $name, $default = null)
    {
        return self::getTypeRequest($name, "strval", "request", $default);
    }

    public static function getInt(string $name, $default = 0)
    {
        return self::getTypeRequest($name, "intval", "request", $default);
    }

    public static function getFloat(string $name, $default = 0.0)
    {
        return self::getTypeRequest($name, "floatval", "request", $default);
    }

    public static function add(string $name, string $value)
    {
        if (!self::$close) self::$request[$name] = is_numeric($value) ? floatval($value) : self::clearData($value);
    }

    public static function close()
    {
        self::$post = self::getPostData();
        self::$get = self::getGetData();
        self::$req = self::getRequestData();
        self::$initial_cookie = self::clearData($_COOKIE ?? []);
        self::$initial_session = $_SESSION ?? [];
        self::$close = true;
    }

    public static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getUri()
    {
        if(!isset(self::$uri)) self::$uri = self::clearData(urldecode($_SERVER['REQUEST_URI']) ?? null);

        return self::$uri;
    }

    public static function getFullUrl()
    {
        if(!isset(self::$url)) self::$url = HLEB_PROJECT_PROTOCOL . HLEB_MAIN_DOMAIN . self::getUri();

        return self::$url;
    }

    public static function getReferer()
    {
        if(!isset(self::$referer)) self::$referer = self::clearData($_SERVER['HTTP_REFERER'] ?? null);

        return self::$referer;
    }

    public static function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function getHttpHeader($value = null)
    {
        return self::checkValueInArray($value, $_SERVER);
    }

    public static function isXmlHttpRequest()
    {
        return $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest';
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
        return self::checkValueInArray($value, self::getGetData());
    }

    public static function getGetString(string $name, $default = null)
    {
        return self::getTypeRequest($name, "strval", "get", $default);
    }

    public static function getGetInt(string $name, $default = 0)
    {
        return self::getTypeRequest($name, "intval", "get", $default);
    }

    public static function getGetFloat(string $name, $default = 0.0)
    {
        return self::getTypeRequest($name, "floatval", "get", $default);
    }

    public static function getPost($value = null)
    {
        return self::checkValueInArray($value, self::getPostData());
    }

    public static function getPostString(string $name, $default = null)
    {
        return self::getTypeRequest($name, "strval", "post", $default);
    }

    public static function getPostInt(string $name, $default = 0)
    {
        return self::getTypeRequest($name, "intval", "post", $default);
    }

    public static function getPostFloat(string $name, $default = 0.0)
    {
        return self::getTypeRequest($name, "floatval", "post", $default);
    }

    public static function getRequest($value = null)
    {
        return self::checkValueInArray($value, self::getRequestData());
    }

    public static function getRequestString(string $name, $default = null)
    {
        return self::getTypeRequest($name, "strval", "req", $default);
    }

    public static function getRequestInt(string $name, $default = 0)
    {
        return self::getTypeRequest($name, "intval", "req", $default);
    }

    public static function getRequestFloat(string $name, $default = 0.0)
    {
        return self::getTypeRequest($name, "floatval", "req", $default);
    }

    public static function returnPrivateTags(string $value)
    {
        return  str_replace(self::REPLACING_TAGS, self::NEEDED_TAGS, $value);
    }

    public static function getHead()
    {
        if(!isset(self::$head)) self::$head = new Head();

        return self::$head;
    }

    public static function getResources()
    {
        if(!isset(self::$resources)) self::$resources = new Resources();

        return self::$resources;
    }

    public static function getMainConvertUrl()
    {
        if(is_null(self::$convert_uri)) self::$convert_uri = self::getConvertUrl(urldecode($_SERVER['REQUEST_URI']));

        return self::$convert_uri;
    }

    public static function getMainClearUrl()
    {
        return explode('?', urldecode($_SERVER['REQUEST_URI']))[0];
    }

    protected static function getConvertUrl($url)
    {
        return rawurldecode($url);
    }

    private static function getTypeRequest(string $name,  string $function, string $param, $default)
    {
        return isset(self::$$param[$name]) ? (!is_null(self::$$param[$name]) ?
            $function(self::clearData(!is_array(self::$$param[$name]) ? self::$$param[$name] : null)) : $default) :
            $default;
    }

    private static function getPostData()
    {
        if(!isset(self::$post)) self::$post = self::clearData($_POST ?? []);

        return self::$post;
    }

    private static function getGetData()
    {
        if(!isset(self::$get)) self::$get = self::clearData($_GET ?? []);

        return self::$get;
    }

    private static function getRequestData()
    {
        if(!isset(self::$req)) self::$req = self::clearData($_REQUEST ?? []);

        return self::$req;
    }


    private static function clearData($value)
    {
        if(is_numeric($value)) return $value;
        if(is_array($value))   return self::clearDataInArray($value);
        if(is_string($value))  return self::convertPrivateTags($value);
        return null;
    }

    private static function clearDataInArray( array $data)
    {
        $result = [];
        foreach($data as $key => $value){
            $result[strip_tags($key)] = self::clearData($value);
        }
        return $result;
    }

    private static function checkValueInArray($value, $array)
    {
        return $value != null ? ((true === array_key_exists($value, $array) && strlen($array[$value]) > 0) ? $array[$value] : null) : $array;
    }

    private static function convertPrivateTags(string $value)
    {
        return  str_replace(self::NEEDED_TAGS, self::REPLACING_TAGS, $value);
    }

}

