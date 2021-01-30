<?php

declare(strict_types=1);

/*
 * Class for working with request input data.
 *
 * Класс для работы с входными данными запроса.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Main\Insert\BaseSingleton;
use Hleb\Scheme\Home\Constructor\Handlers\RequestInterface;

class Request extends BaseSingleton implements RequestInterface
{
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

    private static $lang = null;

    private static $url = null;

    private static $referer = null;

    private static $resources = null;

    private static $convertUri = null;


    /**
     * @inheritDoc
     */
    public static function getInitialSession($name = null) {
        return is_null($name) ? self::$initialSession : (isset(self::$initialSession[$name]) ? self::$initialSession[$name] : null);
    }

    /**
     * @inheritDoc
     */
    public static function getInitialCookie($name = null) {
        return is_null($name) ? self::$initialCookie : (isset(self::$initialCookie[$name]) ? self::$initialCookie[$name] : null);
    }

    /**
     * @inheritDoc
     */
    public static function getSession($name = null) {
        return is_null($name) ? $_SESSION ?? [] : (isset($_SESSION) && isset($_SESSION[$name]) ? $_SESSION[$name] : null);
    }

    /**
     * @inheritDoc
     */
    public static function getCookie($name = null) {
        return is_null($name) ? self::clearData($_COOKIE ?? []) : (isset($_COOKIE) && isset($_COOKIE[$name]) ? self::clearData($_COOKIE[$name]) : null);
    }

    /**
     * @inheritDoc
     */
    public static function get(string $name = '') {
        return empty($name) ? self::$request : (self::$request[$name] ?? null);
    }

    /**
     * @inheritDoc
     */
    public static function getString(string $name, $default = null) {
        return self::getTypeRequest($name, "strval", "request", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getInt(string $name, $default = 0) {
        return self::getTypeRequest($name, "intval", "request", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getFloat(string $name, $default = 0.0) {
        return self::getTypeRequest($name, "floatval", "request", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @inheritDoc
     */
    public static function getHttpProtocol() {
        return hleb_actual_http_protocol(false);
    }

    /**
     * @inheritDoc
     */
    public static function getHttpFullProtocol() {
        return hleb_actual_http_protocol(true);
    }

    /**
     * @inheritDoc
     */
    public static function getFullHost() {
        return self::getHttpFullProtocol() . self::getHost();
    }

    /**
     * @inheritDoc
     */
    public static function getLang() {
        if (!isset(self::$lang)) {
            self::$lang = self::searchLang();
        }
        return self::$lang;
    }

    /**
     * @inheritDoc
     */
    public static function getUri() {
        if (!isset(self::$uri)) self::$uri = self::clearData(urldecode($_SERVER['REQUEST_URI']) ?? null);
        return self::$uri;
    }

    /**
     * @inheritDoc
     */
    public static function getFullUrl() {
        if (!isset(self::$url)) self::$url = HLEB_PROJECT_PROTOCOL . HLEB_MAIN_DOMAIN . self::getUri();
        return self::$url;
    }

    /**
     * @inheritDoc
     */
    public static function getReferer() {
        if (!isset(self::$referer)) self::$referer = self::clearData($_SERVER['HTTP_REFERER'] ?? null);
        return self::$referer;
    }

    /**
     * @inheritDoc
     */
    public static function getDomain() {
        return self::getHost();
    }

    /**
     * @inheritDoc
     */
    public static function getHost() {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * @inheritDoc
     */
    public static function getPort() {
        $hostParts =  explode(':', self::getHost());
        return count($hostParts) === 2 ? end($hostParts) : null;
    }

    /**
     * @inheritDoc
     */
    public static function getHttpHeader($value = null) {
        return self::checkValueInArray($value, $_SERVER);
    }

    /**
     * @inheritDoc
     */
    public static function isXmlHttpRequest() {
        return $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * @inheritDoc
     */
    public static function getFiles() {
        return $_FILES ?? null;
    }

    /**
     * @inheritDoc
     */
    public static function getUrlParameter() {
        return !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
    }

    /**
     * @inheritDoc
     */
    public static function getRemoteAddress() {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public static function getGet($value = null) {
        return self::checkValueInArray($value, self::getGetData());
    }

    /**
     * @inheritDoc
     */
    public static function getGetString(string $name, $default = null) {
        return self::getTypeRequest($name, "strval", "get", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getGetInt(string $name, $default = 0) {
        return self::getTypeRequest($name, "intval", "get", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getGetFloat(string $name, $default = 0.0) {
        return self::getTypeRequest($name, "floatval", "get", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getPost($value = null) {
        return self::checkValueInArray($value, self::getPostData());
    }

    /**
     * @inheritDoc
     */
    public static function getPostString(string $name, $default = null) {
        return self::getTypeRequest($name, "strval", "post", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getPostInt(string $name, $default = 0) {
        return self::getTypeRequest($name, "intval", "post", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getPostFloat(string $name, $default = 0.0) {
        return self::getTypeRequest($name, "floatval", "post", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getRequest($value = null) {
        return self::checkValueInArray($value, self::getRequestData());
    }

    /**
     * @inheritDoc
     */
    public static function getRequestString(string $name, $default = null) {
        return self::getTypeRequest($name, "strval", "req", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getRequestInt(string $name, $default = 0) {
        return self::getTypeRequest($name, "intval", "req", $default);
    }

    /**
     * @inheritDoc
     */
    public static function getRequestFloat(string $name, $default = 0.0) {
        return self::getTypeRequest($name, "floatval", "req", $default);
    }

    /**
     * @inheritDoc
     */
    public static function returnPrivateTags(string $value) {
        return str_replace(self::REPLACING_TAGS, self::NEEDED_TAGS, $value);
    }

    /**
     * @inheritDoc
     */
    public static function getHead() {
        if (!isset(self::$head)) self::$head = new Head();
        return self::$head;
    }

    /**
     * @inheritDoc
     */
    public static function getResources() {
        if (!isset(self::$resources)) self::$resources = new Resources();
        return self::$resources;
    }

    /**
     * @inheritDoc
     */
    public static function getMainConvertUrl() {
        if (is_null(self::$convertUri)) self::$convertUri = self::getConvertUrl(urldecode($_SERVER['REQUEST_URI']));
        return self::$convertUri;
    }

    /**
     * @inheritDoc
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
    private static function getConvertUrl($url) {
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
            $result[strip_tags(strval($key))] = self::clearData($value);
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

    // Trying to find the localization value
    // Попытка найти значение локализации
    private static function searchLang() {
        // ISO 639-1

        // Search in the passed parameter
        // Поиск в переданном параметре
        $requestLang = self::getString('lang') ?? self::getString('Lang') ?? self::getString('LANG');
        if ($requestLang && !is_numeric($requestLang) && strlen($requestLang) === 2) {
            $requestLang = strtolower($requestLang);
            return $requestLang;
        }

        // Search at the beginning of url
        // Поиск в начале url
        $urlParts = explode('/', trim(self::getMainClearUrl(), '/'));
        if (count($urlParts) && !is_numeric($urlParts[0]) && strlen($urlParts[0]) === 2) {
            $urlPartLang = strtolower($urlParts[0]);
            return $urlPartLang;
        }

        // Search in a server variable
        // Поиск в серверной переменной
        $serverLang = self::getHttpHeader('HTTP_ACCEPT_LANGUAGE');
        if (is_string($serverLang) && !is_numeric($serverLang) && strlen($serverLang) === 2) {
            $serverLang = strtolower($serverLang);
            return $serverLang;
        }

        return false;
    }

}

