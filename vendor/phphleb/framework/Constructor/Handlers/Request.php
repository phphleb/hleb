<?php

declare(strict_types=1);

/*
 * Class for working with request input data.
 *
 * Класс для работы с входными данными запроса.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Main\Insert\BaseSingleton;

class Request extends BaseSingleton
{
    /** @internal */
    const NEEDED_TAGS = ['<', '>'];

    /** @internal */
    const REPLACING_TAGS = ['&lt;', '&gt;'];

    private static $request = [];

    private static $post = null;

    private static $get = null;

    private static $req = null;

    private static $cookie = null;

    private static $head = null;

    private static $uri = null;

    private static $lang = null;

    private static $url = null;

    private static $domain = null;

    private static $address = null;

    private static $referer = null;

    private static $resources = null;

    private static $convertUri = null;

    private static $inputBody = null;

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
        if (is_null(self::$cookie)) self::$cookie = self::getCookieData();
        return is_null($name) ? self::$cookie : (isset($_COOKIE) && isset(self::$cookie[$name]) ? self::$cookie[$name] : null);
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
        return self::getData($name);
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
        return self::getData($name, $default, 'strval');
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
        return self::getData($name, $default, 'intval');
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
        return self::getData($name, $default, 'floatval');
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
     * Returns the current request protocol 'http' or 'https'
     * @return string
     *//**
     * Возвращает текущий протокол запроса 'http' или 'https'
     * @return string
     */
    public static function getHttpProtocol() {
        return hleb_actual_http_protocol(false);
    }

    /**
     * Returns the complete current request protocol 'http://' or 'https://'
     * @return string
     *//**
     * Возвращает полный текущий протокол запроса 'http://' или 'https://'
     * @return string
     */
    public static function getHttpFullProtocol() {
        return hleb_actual_http_protocol(true);
    }

    /**
     * Returns the full host with an http-prefix of the form 'https://site.com'
     * @return string
     *//**
     * Возвращает полный хост с http-префиксом вида 'https://site.com'
     * @return string
     */
    public static function getFullHost() {
        return self::getHttpFullProtocol() . self::getHost();
    }

    /**
     * Trying to find the localization value
     * @return string|false
     *//**
     * Попытка найти значение локализации
     * @return string|false
     */
    public static function getLang() {
        if (!isset(self::$lang)) {
            self::$lang = self::searchLang();
        }
        return self::$lang;
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
     * @return string
     *//**
     * Полный URL-адрес текущего запроса, вида 'http://site.com/index.php?p=example'
     * @return string
     */
    public static function getFullUrl() {
        if (!isset(self::$url)) self::$url = self::getFullHost() . self::getUri();
        return self::$url;
    }

    /**
     * Full URL of the current request without GET parameters, of the form 'http://site.com/index.php'
     * @return string
     *//**
     * Полный URL-адрес текущего запроса без GET-параметров, вида 'http://site.com/index.php'
     * @return string
     */
    public static function getFullUrlAddress() {
        if (!isset(self::$address)) self::$address = explode('?', self::getFullUrl())[0];
        return self::$address;
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
     * Returns current domain, of the form 'site.com'.
     * @return string
     *//**
     * Возвращает текущий домен, в виде 'site.com'.
     * @return string
     */
    public static function getDomain() {
        if(is_null(self::$domain)) {
            self::$domain = preg_replace('/^www./', '', trim(self::getHost(), ' /'));
        }
        return self::$domain;
    }

    /**
     * Returns the content of the `Host` header.
     * @return string
     *//**
     * Возвращает содержимое заголовка `Host`.
     * @return string
     */
    public static function getHost() {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Get the port of the current connection from the host.
     * @return string|null
     *//**
     * Получить порт текущего соединения из хоста.
     * @return string|null
     */
    public static function getPort() {
        $hostParts =  explode(':', self::getHost());
        return count($hostParts) === 2 ? end($hostParts) : null;
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
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
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
        return self::getData($name, $default, "strval", self::getGetData());
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
        return self::getData($name, $default, "intval", self::getGetData());
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
        return self::getData($name, $default, "floatval", self::getGetData());
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
        return self::getData($name, $default, "strval", self::getPostData());
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
        return self::getData($name, $default, "intval", self::getPostData());
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
        return self::getData($name, $default, "floatval", self::getPostData());
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
        return self::getData($name, $default, "strval", self::getRequestData());
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
        return self::getData($name, $default, "intval", self::getRequestData());
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
        return self::getData($name, $default, "floatval", self::getRequestData());
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
     * Returns request body, does not work with `multipart/form-data`.
     * @return string
     *//**
     * Возвращает тело запроса, не работает с `multipart/form-data`.
     * @return string
     */
    public static function getInputBody() {
        if (!is_null(self::$inputBody)) {
            return self::$inputBody;
        }
        $inputData =  (string)file_get_contents('php://input');

        return self::$inputBody = $inputData ? self::convertPrivateTags($inputData) : $inputData;
    }

    /**
     * Returns the request body converted from JSON.
     * @return array|false
     *//**
     * Возвращает преобразованное из JSON тело запроса.
     * @return array|false
     */
    public static function getJsonBodyList() {
        $body = self::getInputBody();
        if (!$body) {
            return false;
        }
        $list = json_decode($body, true);
        return is_array($list) ? $list : false;
    }

    /**
     * Checking the execution of the application from the console.
     * @return bool
     */
    /**
     * Проверка выполнения приложения из консоли.
     * @return bool
     */
    public static function isConsoleMode() {
       return !is_null(self::getConsoleCommand());
    }

    /**
     * Returns the current console command when run from a terminal.
     * @return string|null
     *//**
     * Возвращает текущую консольную команду при запуске из терминала.
     * @return string|null
     */
    public static function getConsoleCommand() {
        return defined('HLEB_CLI_COMMAND') ? HLEB_CLI_COMMAND : null;
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
     * Returns the relative path of a URL with parameters.
     * @return string
     *//**
     * Возвращает относительный путь URL с параметрами.
     * @return string
     */
    public static function getMainUrl() {
        return (string)self::clearData(urldecode($_SERVER['REQUEST_URI']));
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

    // Reserved for backward compatibility
    // Оставлено для обратной совместимости
    /** @deprecated  */
    public static function close() {
        return null;
    }

    // Adds a parameter by name and value.
    // Добавляет параметр по имени и значению.
    /**
     * @param string $name
     * @param string $value
     *
     * @internal
     */
    public static function add(string $name, string $value) {
       self::$request[$name] = is_numeric($value) ? floatval($value) : self::clearData($value);
    }

    // URL conversion.
    // Конвертация URL.
    private static function getConvertUrl($url) {
        return rawurldecode($url);
    }

    // Returns $_POST data.
    // Возвращает данные $_POST.
    private static function getPostData() {
        if(is_null(self::$post)) self::$post = self::clearData($_POST ?? []);
        return self::$post;
    }

    // Returns $_GET data.
    // Возвращает данные $_GET.
    private static function getGetData() {
        if(is_null(self::$get)) self::$get = self::clearData($_GET ?? []);
        return self::$get;
    }

    // Returns $_REQUEST data.
    // Возвращает данные $_REQUEST.
    private static function getRequestData() {
        if(is_null(self::$req)) self::$req = self::clearData($_REQUEST ?? []);
        return self::$req;
    }

    // Returns $_COOKIE data.
    // Возвращает данные $_COOKIE.
    private static function getCookieData() {
        if(is_null(self::$cookie)) self::$cookie = self::clearData($_COOKIE ?? []);
        return self::$cookie;
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
        return $value != null ? ((true === array_key_exists($value, $list) && $list[$value] !== '') ? $list[$value] : null) : $list;
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

    // Returns a standardized value
    // Возвращает стандартизированное значение
    private static function getData($name = null, $default = '', $function = '', array $list = null) {
        $data = is_null($list) ? self::$request : $list;
        if ($name) {
            $value = isset($data[$name]) ? $data[$name] : null;
            if (!is_numeric($value) && in_array($function, ['intval', 'floatval'])) {
                return $default;
            }
            if (!is_null($value)) {
                $value = $function && $value !== '' ? $function($value) : $value;
                if (!empty($value) || (is_numeric($value) && $value == 0)) {
                    return $value;
                }
            }
            return $default;
        }
        return $data ?? [];
    }

}

