<?php

declare(strict_types=1);

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

    /*
     * Returns the primary session data of $_SESSION
     * Возвращает первичные данные сессии $_SESSION
     */
    public static function getInitialSession($name = null)
    {
        return is_null($name) ? self::$initial_session : (isset(self::$initial_session[$name]) ? self::$initial_session[$name] : null);
    }

    /*
     * Returns the primary data of $_COOKIE
     * Возвращает первичные данные $_COOKIE
     */
    public static function getInitialCookie($name = null)
    {
        return is_null($name) ? self::$initial_cookie : (isset(self::$initial_cookie[$name]) ? self::$initial_cookie[$name] : null);
    }

    /*
     * Returns current session data $_SESSION
     * Возвращает текущие данные сессии $_SESSION
     */
    public static function getSession($name = null)
    {
        return is_null($name) ? $_SESSION ?? [] : (isset($_SESSION) && isset($_SESSION[$name]) ? $_SESSION[$name] : null);
    }

    /*
     * Returns current $_COOKIE data
     * Возвращает текущие данные $_COOKIE
     */
    public static function getCookie($name = null)
    {
        return is_null($name) ? self::clearData($_COOKIE ?? []) : (isset($_COOKIE) && isset($_COOKIE[$name]) ? self::clearData($_COOKIE[$name]) : null);
    }

    /*
     * Returns $_REQUEST data
     * Возвращает данные $_REQUEST
     */
    public static function get(string $name = '')
    {
        return empty($name) ? self::$request : (self::$request[$name] ?? null);
    }

    /*
     * Returns a value from $_REQUEST with conversion to a string value
     * Возвращает значение из $_REQUEST с преобразованием в строковое значение
     */
    public static function getString(string $name, $default = null)
    {
        return self::getTypeRequest($name, "strval", "request", $default);
    }

    /*
     * Returns a value from $_REQUEST with conversion to an integer value
     * Возвращает значение из $_REQUEST с преобразованием в целочисленное значение
     */
    public static function getInt(string $name, $default = 0)
    {
        return self::getTypeRequest($name, "intval", "request", $default);
    }

    /*
     * Returns a value from $_REQUEST converted to a numeric value
     * Возвращает значение из $_REQUEST с преобразованием в числовое значение
     */
    public static function getFloat(string $name, $default = 0.0)
    {
        return self::getTypeRequest($name, "floatval", "request", $default);
    }
    /*
     * Returns the request method. For example, 'GET', 'HEAD', 'POST', 'PUT'
     * Возвращает метод запроса. К примеру 'GET', 'HEAD', 'POST', 'PUT'
     */
    public static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /*
     * The address that was provided to access this page. For example '/index.php?p=example'
     * Адрес, который был предоставлен для доступа к этой странице. Например '/index.php?p=example'
     */
    public static function getUri()
    {
        if(!isset(self::$uri)) self::$uri = self::clearData(urldecode($_SERVER['REQUEST_URI']) ?? null);

        return self::$uri;
    }

    /*
     * The full URL of the current request, of the form 'http://site.com/index.php?p=example'
     * Полный URL-адрес текущего запроса, вида 'http://site.com/index.php?p=example'
     */
    public static function getFullUrl()
    {
        if(!isset(self::$url)) self::$url = HLEB_PROJECT_PROTOCOL . HLEB_MAIN_DOMAIN . self::getUri();

        return self::$url;
    }

    /*
     * The transmitted address of the page from which the user made the transition
     * Переданный адрес страницы, c которой пользователь совершил переход
     */
    public static function getReferer()
    {
        if(!isset(self::$referer)) self::$referer = self::clearData($_SERVER['HTTP_REFERER'] ?? null);

        return self::$referer;
    }

    /*
     * Returns current domain
     * Возвращает текущий домен
     */
    public static function getDomain()
    {
        return HLEB_MAIN_DOMAIN;
    }

    /*
     * Returns current host
     * Возвращает текущий хост
     */
    public static function getHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /*
     * Returns the information array $_SERVER created by the web server, or the requested value from it
     * Возвращает массив с информацией $_SERVER, созданный веб-сервером, или запрошенное значение из него
     */
    public static function getHttpHeader($value = null)
    {
        return self::checkValueInArray($value, $_SERVER);
    }

    /*
     * Determines if a request is requested as ajax
     * Определяет, запрошен ли запрос как ajax
     */
    public static function isXmlHttpRequest()
    {
        return $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /*
     * Returns the array with data for the downloaded file
     * Возвращает массив с данными для загруженного файла
     */
    public static function getFiles()
    {
        return $_FILES;
    }

    /*
     * Returns the part of the URL request after the script is executed
     * Возвращает часть URL-запроса после выполняемого сценария
     */
    public static function getUrlParameter()
    {
        return !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
    }

    /*
     * Returns the IP address of the client or IP of the last proxy server through which the client got to the site
     * Возвращает IP-адрес клиента или IP последнего прокси-сервера, через который клиент попал на сайт
     */
    public static function getRemoteAddress()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /*
     * Returns an array or a single value of $_GET
     * Возвращает массив или отдельное значение $_GET
     */
    public static function getGet($value = null)
    {
        return self::checkValueInArray($value, self::getGetData());
    }

    /*
     * Returns the value from $_GET with conversion to string value
     * Возвращает значение из $_GET с преобразованием в строковое значение
     */
    public static function getGetString(string $name, $default = null)
    {
        return self::getTypeRequest($name, "strval", "get", $default);
    }

    /*
     * Returns the value from $_GET with conversion to an integer value
     * Возвращает значение из $_GET с преобразованием в целочисленное значение
     */
    public static function getGetInt(string $name, $default = 0)
    {
        return self::getTypeRequest($name, "intval", "get", $default);
    }

    /*
     * Returns the value from $_GET with conversion to a numeric value
     * Возвращает значение из $_GET с преобразованием в числовое значение
     */
    public static function getGetFloat(string $name, $default = 0.0)
    {
        return self::getTypeRequest($name, "floatval", "get", $default);
    }

    /*
     * Returns an array or a single value of $_POST
     * Возвращает массив или отдельное значение $_POST
     */
    public static function getPost($value = null)
    {
        return self::checkValueInArray($value, self::getPostData());
    }

    /*
     * Returns the value from $_POST with conversion to string value
     * Возвращает значение из $_POST с преобразованием в строковое значение
     */
    public static function getPostString(string $name, $default = null)
    {
        return self::getTypeRequest($name, "strval", "post", $default);
    }

    /*
     * Returns the value from $_POST with conversion to an integer value
     * Возвращает значение из $_POST с преобразованием в целочисленное значение
     */
    public static function getPostInt(string $name, $default = 0)
    {
        return self::getTypeRequest($name, "intval", "post", $default);
    }

    /*
     * Returns the value from $_POST with conversion to a numeric value
     * Возвращает значение из $_POST с преобразованием в числовое значение
     */
    public static function getPostFloat(string $name, $default = 0.0)
    {
        return self::getTypeRequest($name, "floatval", "post", $default);
    }

    /*
     * Returns an array or a single value of $_REQUEST
     * Возвращает массив или отдельное значение $_REQUEST
     */
    public static function getRequest($value = null)
    {
        return self::checkValueInArray($value, self::getRequestData());
    }

    /*
     * Returns the value from $_REQUEST with conversion to string value
     * Возвращает значение из $_REQUEST с преобразованием в строковое значение
     */
    public static function getRequestString(string $name, $default = null)
    {
        return self::getTypeRequest($name, "strval", "req", $default);
    }

    /*
     * Returns the value from $_REQUEST with conversion to an integer value
     * Возвращает значение из $_REQUEST с преобразованием в целочисленное значение
     */
    public static function getRequestInt(string $name, $default = 0)
    {
        return self::getTypeRequest($name, "intval", "req", $default);
    }

    /*
     * Returns the value from $_REQUEST with conversion to a numeric value
     * Возвращает значение из $_REQUEST с преобразованием в числовое значение
     */
    public static function getRequestFloat(string $name, $default = 0.0)
    {
        return self::getTypeRequest($name, "floatval", "req", $default);
    }

    /*
     * Return cleared tags back
     * Возвращение очищенных тегов обратно
     */
    public static function returnPrivateTags(string $value)
    {
        return  str_replace(self::REPLACING_TAGS, self::NEEDED_TAGS, $value);
    }

    /*
     * Returns an object for placing headers, styles and scripts in the <head>...</head> of the page
     * Возвращает объект для размещения заголовков, стилей и скриптов в <head>...</head> страницы
     */
    public static function getHead()
    {
        if(!isset(self::$head)) self::$head = new Head();

        return self::$head;
    }

    /*
     * Returns an object for placing loaded resources at the bottom of the page
     * Возвращает объект для размещения подгружаемых ресурсов в нижней части страницы
     */
    public static function getResources()
    {
        if(!isset(self::$resources)) self::$resources = new Resources();

        return self::$resources;
    }

    /*
     * Returns the relative current URL, similar to getMainUrl()
     * Возвращает относительный текущий URL, аналогично функции getMainUrl()
     */
    public static function getMainConvertUrl()
    {
        if(is_null(self::$convert_uri)) self::$convert_uri = self::getConvertUrl(urldecode($_SERVER['REQUEST_URI']));

        return self::$convert_uri;
    }

    /*
     * Returns the relative current URL without GET parameters
     * Возвращает относительный текущий URL без GET-параметров
     */
    public static function getMainClearUrl()
    {
        return explode('?', urldecode($_SERVER['REQUEST_URI']))[0];
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

