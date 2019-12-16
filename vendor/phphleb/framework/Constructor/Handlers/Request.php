<?php

namespace Hleb\Constructor\Handlers;

use DeterminantStaticUncreated;
use Hleb\Main\Functions;

class Request
{
    use DeterminantStaticUncreated;

    private static $request = [];

    private static $close = false;

    const NEEDED_TAGS = ["<", ">"];

    const REPLACING_TAGS = ["&lt;", "&gt;"];

    private static $post = null;

    private static $get = null;

    private static $req = null;

    private static $cookie = null;

    private static $head = null;

    private static $uri = null;

    private static $url = null;

    private static $referer = null;

    private static $resources = null;


    private static function getPostData()
    {
        if(!isset(self::$post)) self::$post = self::convertPrivateTagsInArray($_POST ?? []);

        return self::$post;
    }

    private static function getGetData()
    {
        if(!isset(self::$get)) self::$get = self::convertPrivateTagsInArray($_GET ?? []);

        return self::$get;
    }

    private static function getRequestData()
    {
        if(!isset(self::$req)) self::$req = self::convertPrivateTagsInArray($_REQUEST ?? []);

        return self::$req;
    }

    private static function getCookieData()
    {
        if(!isset(self::$cookie)) self::$cookie = self::convertPrivateTagsInArray($_COOKIE ?? []);

        return self::$cookie;
    }


    private static function checkValueInArray($value, $array)
    {
        return $value != null ? ((true === array_key_exists($value, $array) && strlen($array[$value]) > 0) ? $array[$value] : null) : $array;
    }

    public static function get(string $name = null)
    {
        return empty($name) ? self::$request : self::$request[$name];
    }

    public static function add(string $name, string $value)
    {
        if (!self::$close) self::$request[$name] = is_numeric($value) ? floatval($value) : $value;
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
        if(!isset(self::$uri)) self::$uri = self::convertPrivateTags($_SERVER['REQUEST_URI'] ?? null);

        return self::$uri;
    }

    public static function getFullUrl()
    {
        if(!isset(self::$url)) self::$url = self::convertPrivateTags(Functions::mainFullHostUrl());

        return self::$url;
    }

    public static function getReferer()
    {
        if(!isset(self::$referer)) self::$referer = self::convertPrivateTags($_SERVER['HTTP_REFERER'] ?? null);

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

    public static function getCookie($value = null)
    {
        return self::checkValueInArray($value, self::getCookieData());
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

    public static function getPost($value = null)
    {
        return self::checkValueInArray($value, self::getPostData());
    }

    public static function getRequest($value = null)
    {
        return self::checkValueInArray($value, self::getRequestData());
    }

    private static function convertPrivateTags(string $value)
    {
      return  str_replace(self::NEEDED_TAGS, self::REPLACING_TAGS, $value);
    }

    private static function convertPrivateTagsInArray(array $value)
    {
        return  array_map( [self::class,'convertPrivateTags'], $value);
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

}

