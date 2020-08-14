<?php

declare(strict_types=1);

/*
 * Class for working with request input data.
 *
 * Класс для работы с входными данными запроса.
 */

namespace Hleb\Constructor\Handlers;

use DeterminantStaticUncreated;

class Request
{
    use DeterminantStaticUncreated;

    const NEEDED_TAGS = ['<', '>'];

    const REPLACING_TAGS = ['&lt;', '&gt;'];

    private static $request = [];

    private static $close = false;

    private static $post = null;

    private static $get = null;

    private static $req = null;

    private static $initialCookie = null;

    private static $initialSession = null;

    private static $head = null;

    private static $uri = null;

    private static $url = null;

    private static $referer = null;

    private static $resources = null;

    private static $convertUri = null;

    /**
     * Returns the primary session data of $_SESSION.
     * @param mixed|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает первичные данные сессии $_SESSION.
     * @param mixed|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getInitialSession($name = null) {
        return is_null($name) ? self::$initialSession : (isset(self::$initialSession[$name]) ? self::$initialSession[$name] : null);
    }

    /**
     * Returns the primary session data of $_COOKIE.
     * @param mixed|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает первичные данные сессии $_COOKIE.
     * @param mixed|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getInitialCookie($name = null) {
        return is_null($name) ? self::$initialCookie : (isset(self::$initialCookie[$name]) ? self::$initialCookie[$name] : null);
    }

    /**
     * Returns the current session data of $_SESSION.
     * @param mixed|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает текущие данные сессии $_SESSION.
     * @param mixed|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getSession($name = null) {
        return is_null($name) ? $_SESSION ?? [] : (isset($_SESSION) && isset($_SESSION[$name]) ? $_SESSION[$name] : null);
    }

    /**
     * Returns the current session data of $_COOKIE.
     * @param mixed|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает текущие данные сессии $_COOKIE.
     * @param mixed|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getCookie($name = null) {
        return is_null($name) ? self::clearData($_COOKIE ?? []) : (isset($_COOKIE) && isset($_COOKIE[$name]) ? self::clearData($_COOKIE[$name]) : null);
    }

    /**
     * Returns data from the current route.
     * @param string|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает данные из текущего роута.
     * @param string|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function get(string $name = '') {
        return empty($name) ? self::$request : (self::$request[$name] ?? null);
    }

    /**
     * Returns a value from the current route with conversion to a string value.
     * @param string $name - name of the requested value.
     * @param null|string $default - default value for empty or undetected values.
     * @return null|string
     *//**
     * Возвращает значение из текущего роута с преобразованием в строковое значение.
     * @param string $name - название необходимого значения.
     * @param null|string $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|string
     */
    public static function getString(string $name, $default = null) {
        return self::getTypeRequest($name, "strval", "request", $default);
    }

    /**
     * Returns a value from current route with conversion to an integer value.
     * @param string $name - name of the requested value.
     * @param null|string $default - default value for empty or undetected values.
     * @return null|string
     *//**
     * Возвращает значение из текущего роута с преобразованием в целочисленное значение.
     * @param string $name - название необходимого значения.
     * @param null|integer $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|integer
     */
    public static function getInt(string $name, $default = 0) {
        return self::getTypeRequest($name, "intval", "request", $default);
    }

    /**
     * Returns a value from current route with conversion to an floating-point value.
     * @param string $name - name of the requested value.
     * @param null|float $default - default value for empty or undetected values.
     * @return null|float
     *//**
     * Возвращает значение из текущего роута  с преобразованием в число с плавающей запятой.
     * @param string $name - название необходимого значения.
     * @param null|float $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|float
     */
    public static function getFloat(string $name, $default = 0.0) {
        return self::getTypeRequest($name, "floatval", "request", $default);
    }

    /**
     * Returns the request method. For example, 'GET', 'HEAD', 'POST', 'PUT'
     * @return string
     *//**
     * Возвращает метод запроса. Например: 'GET', 'HEAD', 'POST', 'PUT'
     * @return string
     */
    public static function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * The address that was provided to access this page. For example '/index.php?p=example'
     * @return null|string
     *//**
     * Адрес, который был предоставлен для доступа к этой странице. Например '/index.php?p=example'
     * @return null|string
     */
    public static function getUri() {
        if (!isset(self::$uri)) self::$uri = self::clearData(urldecode($_SERVER['REQUEST_URI']) ?? null);
        return self::$uri;
    }

    /**
     * The full URL of the current request, of the form 'http://site.com/index.php?p=example'
     * @return null|string
     *//**
     * Полный URL-адрес текущего запроса, вида 'http://site.com/index.php?p=example'
     * @return null|string
     */
    public static function getFullUrl() {
        if (!isset(self::$url)) self::$url = HLEB_PROJECT_PROTOCOL . HLEB_MAIN_DOMAIN . self::getUri();
        return self::$url;
    }

    /**
     * The transmitted address of the page from which the user made the transition.
     * @return array|string|string[]|null
     *//**
     * Переданный адрес страницы, c которой пользователь совершил переход.
     * @return array|string|string[]|null
     */
    public static function getReferer() {
        if (!isset(self::$referer)) self::$referer = self::clearData($_SERVER['HTTP_REFERER'] ?? null);
        return self::$referer;
    }

    /**
     * Returns current domain.
     * @return string
     *//**
     * Возвращает текущий домен.
     * @return string
     */
    public static function getDomain() {
        return HLEB_MAIN_DOMAIN;
    }

    /**
     * Returns the content of the `Host` header.
     * @return mixed
     *//**
     * Возвращает содержимое заголовка `Host`.
     * @return mixed
     */
    public static function getHost() {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Returns the information array $_SERVER created by the web server, or the requested value from it.
     * @param null|string $value - parameter to get data by name.
     * @return null|array
     *//**
     * Возвращает массив с информацией $_SERVER, созданный веб-сервером, или запрошенное значение из него.
     * @param null|string $value - параметр для получения данных по названию.
     * @return null|mixed|array
     */
    public static function getHttpHeader($value = null) {
        return self::checkValueInArray($value, $_SERVER);
    }

    /**
     * Determines if a request is requested as ajax
     * @return bool
     *//**
     * Определяет, запрошен ли запрос как ajax
     * @return bool
     */
    public static function isXmlHttpRequest() {
        return $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * Returns the array with data for the downloaded file.
     * @return null|mixed
     *//**
     * Возвращает массив с данными для загруженного файла.
     * @return null|mixed
     */
    public static function getFiles() {
        return $_FILES ?? null;
    }

    /**
     * Returns the part of the URL request after the script is executed.
     * @return mixed|string
     *//**
     * Возвращает часть URL-запроса после выполняемого сценария.
     * @return mixed|string
     */
    public static function getUrlParameter() {
        return !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
    }

    /**
     * Returns the IP address of the client or IP of the last proxy server through which the client got to the site.
     * @return null|string
     *//**
     * Возвращает IP-адрес клиента или IP последнего прокси-сервера, через который клиент попал на сайт.
     * @return null|string
     */
    public static function getRemoteAddress() {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    /**
     * Returns an array or a single value of $_GET.
     * @param null|string $value - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает массив или отдельное значение $_GET.
     * @param null|string $value - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getGet($value = null) {
        return self::checkValueInArray($value, self::getGetData());
    }

    /**
     * Returns the value from $_GET with conversion to string value.
     * @param string $name - name of the requested value.
     * @param null|string $default - default value for empty or undetected values.
     * @return null|string
     *//**
     * Возвращает значение из $_GET с преобразованием в строковое значение.
     * @param string $name - название необходимого значения.
     * @param null|string $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|string
     */
    public static function getGetString(string $name, $default = null) {
        return self::getTypeRequest($name, "strval", "get", $default);
    }

    /**
     * Returns the value from $_GET with conversion to an integer value.
     * @param string $name - name of the requested value.
     * @param null|integer $default - default value for empty or undetected values.
     * @return null|integer
     *//**
     * Возвращает значение из $_GET с преобразованием в целочисленное значение.
     * @param string $name - название необходимого значения.
     * @param null|integer $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|integer
     */
    public static function getGetInt(string $name, $default = 0) {
        return self::getTypeRequest($name, "intval", "get", $default);
    }

    /**
     * Returns a value from $_GET with conversion to an floating-point value.
     * @param string $name - name of the requested value.
     * @param null|float $default - default value for empty or undetected values.
     * @return null|float
     *//**
     * Возвращает значение из $_GET с преобразованием в число с плавающей запятой.
     * @param string $name - название необходимого значения.
     * @param null|float $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|float
     */
    public static function getGetFloat(string $name, $default = 0.0) {
        return self::getTypeRequest($name, "floatval", "get", $default);
    }

    /**
     * Returns an array or a single value of $_POST.
     * @param null|string $value - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает массив или отдельное значение $_POST.
     * @param null|string $value - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getPost($value = null) {
        return self::checkValueInArray($value, self::getPostData());
    }

    /**
     * Returns the value from $_POST with conversion to string value.
     * @param string $name - name of the requested value.
     * @param null|string $default - default value for empty or undetected values.
     * @return null|string
     *//**
     * Возвращает значение из $_POST с преобразованием в строковое значение.
     * @param string $name - название необходимого значения.
     * @param null|string $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|string
     */
    public static function getPostString(string $name, $default = null) {
        return self::getTypeRequest($name, "strval", "post", $default);
    }

    /**
     * Returns the value from $_POST with conversion to an integer value.
     * @param string $name - name of the requested value.
     * @param null|integer $default - default value for empty or undetected values.
     * @return null|integer
     *//**
     * Возвращает значение из $_POST с преобразованием в целочисленное значение.
     * @param string $name - название необходимого значения.
     * @param null|integer $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|integer
     */
    public static function getPostInt(string $name, $default = 0) {
        return self::getTypeRequest($name, "intval", "post", $default);
    }

    /**
     * Returns a value from $_POST with conversion to an floating-point value.
     * @param string $name - name of the requested value.
     * @param null|float $default - default value for empty or undetected values.
     * @return null|float
     *//**
     * Возвращает значение из $_POST с преобразованием в число с плавающей запятой.
     * @param string $name - название необходимого значения.
     * @param null|float $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|float
     */
    public static function getPostFloat(string $name, $default = 0.0) {
        return self::getTypeRequest($name, "floatval", "post", $default);
    }

    /**
     * Returns an array or a single value of $_REQUEST.
     * @param null|string $value - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает массив или отдельное значение $_REQUEST.
     * @param null|string $value - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getRequest($value = null) {
        return self::checkValueInArray($value, self::getRequestData());
    }

    /**
     * Returns the value from $_REQUEST with conversion to string value.
     * @param string $name - name of the requested value.
     * @param null|string $default - default value for empty or undetected values.
     * @return null|string
     *//**
     * Возвращает значение из $_REQUEST с преобразованием в строковое значение.
     * @param string $name - название необходимого значения.
     * @param null|string $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|string
     */
    public static function getRequestString(string $name, $default = null) {
        return self::getTypeRequest($name, "strval", "req", $default);
    }

    /**
     * Returns the value from $_REQUEST with conversion to an integer value.
     * @param string $name - name of the requested value.
     * @param null|integer $default - default value for empty or undetected values.
     * @return null|integer
     *//**
     * Возвращает значение из $_REQUEST с преобразованием в целочисленное значение.
     * @param string $name - название необходимого значения.
     * @param null|integer $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|integer
     */
    public static function getRequestInt(string $name, $default = 0) {
        return self::getTypeRequest($name, "intval", "req", $default);
    }

    /**
     * Returns a value from $_REQUEST with conversion to an floating-point value.
     * @param string $name - name of the requested value.
     * @param null|float $default - default value for empty or undetected values.
     * @return null|float
     *//**
     * Возвращает значение из $_REQUEST с преобразованием в число с плавающей запятой.
     * @param string $name - название необходимого значения.
     * @param null|float $default - дефолтное значение для пустых или необнаруженнных значений.
     * @return null|float
     */
    public static function getRequestFloat(string $name, $default = 0.0) {
        return self::getTypeRequest($name, "floatval", "req", $default);
    }

    /**
     * Returns the original string with stripped tags.
     * @param string $value - line to clean up.
     * @return string
     *//**
     * Возвращает исходную строку с очищенными тегами.
     * @param string $value - строка для очистки.
     * @return string
     */
    public static function returnPrivateTags(string $value) {
        return str_replace(self::REPLACING_TAGS, self::NEEDED_TAGS, $value);
    }

    /**
     * Returns an object for placing headers, styles and scripts in the <head>...</head> of the page.
     * @return Head|null
     *//**
     * Возвращает объект для размещения заголовков, стилей и скриптов в <head>...</head> страницы.
     * @return Head|null
     */
    public static function getHead() {
        if (!isset(self::$head)) self::$head = new Head();
        return self::$head;
    }

    /**
     * Returns an object for placing loaded resources at the bottom of the page.
     * @return Resources|null
     *//**
     * Возвращает объект для размещения подгружаемых ресурсов в нижней части страницы.
     * @return Resources|null
     */
    public static function getResources() {
        if (!isset(self::$resources)) self::$resources = new Resources();
        return self::$resources;
    }

    /**
     * Returns the relative current URL, similar to getMainUrl()
     * @return string|null
     *//**
     * Возвращает относительный текущий URL, аналогично функции getMainUrl()
     * @return string|null
     */
    public static function getMainConvertUrl() {
        if (is_null(self::$convertUri)) self::$convertUri = self::getConvertUrl(urldecode($_SERVER['REQUEST_URI']));
        return self::$convertUri;
    }

    /**
     * Returns the relative current URL without GET parameters.
     * @return string
     *//**
     * Возвращает относительный текущий URL без GET-параметров.
     * @return string
     */
    public static function getMainClearUrl() {
        return explode('?', urldecode($_SERVER['REQUEST_URI']))[0];
    }

    // Adds a parameter by name and value.
    // Добавляет параметр по имени и значению.
    public static function add(string $name, string $value) {
        if (!self::$close) self::$request[$name] = is_numeric($value) ? floatval($value) : self::clearData($value);
    }

    // Keeps the original settings as original.
    // Сохраняет исходные параметры как первоначальные.
    public static function close() {
        self::$post = self::getPostData();
        self::$get = self::getGetData();
        self::$req = self::getRequestData();
        self::$initialCookie = self::clearData($_COOKIE ?? []);
        self::$initialSession = $_SESSION ?? [];
        self::$close = true;
    }

    // URL conversion.
    // Конвертация URL.
    protected static function getConvertUrl($url) {
        return rawurldecode($url);
    }

    // Returns the desired value from the request parameters by its name.
    // Возвращает нужное значение из параметров запроса по его названию.
    private static function getTypeRequest(string $name, string $function, string $param, $default) {
        return isset(self::$$param[$name]) ? (!is_null(self::$$param[$name]) ?
            $function(self::clearData(!is_array(self::$$param[$name]) ? self::$$param[$name] : null)) : $default) :
            $default;
    }

    // Returns $_POST data.
    // Возвращает данные $_POST.
    private static function getPostData() {
        if (!isset(self::$post)) self::$post = self::clearData($_POST ?? []);
        return self::$post;
    }

    // Returns $_GET data.
    // Возвращает данные $_GET.
    private static function getGetData() {
        if (!isset(self::$get)) self::$get = self::clearData($_GET ?? []);
        return self::$get;
    }

    // Returns $_REQUEST data.
    // Возвращает данные $_REQUEST.
    private static function getRequestData() {
        if (!isset(self::$req)) self::$req = self::clearData($_REQUEST ?? []);
        return self::$req;
    }

    // Determines the type of the value and clears tags from it or nested data. Returns a cleared value.
    // Определяет тип значения и очищает его или вложенные данные от тегов. Возвращает очищенное значение.
    private static function clearData($value) {
        if (is_numeric($value)) return $value;
        if (is_array($value)) return self::clearDataInArray($value);
        if (is_string($value)) return self::convertPrivateTags($value);
        return null;
    }

    // Returns the result of clearing values from the array.
    // Возвращает результат очистки значений из массива.
    private static function clearDataInArray(array $data) {
        $result = [];
        foreach ($data as $key => $value) {
            $result[strip_tags($key)] = self::clearData($value);
        }
        return $result;
    }

    //Returns the result of clearing values from an array by name.
    // Возвращает результат получения значений из массива по названию.
    private static function checkValueInArray($value, array $list) {
        return $value != null ? ((true === array_key_exists($value, $list) && strlen($list[$value]) > 0) ? $list[$value] : null) : $list;
    }

    //Returns the result of clearing values ​​in a string.
    // Возвращает результат очистки значений в строке.
    private static function convertPrivateTags(string $value) {
        return str_replace(self::NEEDED_TAGS, self::REPLACING_TAGS, $value);
    }

}

