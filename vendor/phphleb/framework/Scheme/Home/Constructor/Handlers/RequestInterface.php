<?php

declare(strict_types=1);

namespace Hleb\Scheme\Home\Constructor\Handlers;

use Hleb\Constructor\Handlers\Head;
use Hleb\Constructor\Handlers\Resources;

interface RequestInterface
{
    /**
     * Returns the primary session data of $_SESSION.
     * @param mixed|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает первичные данные сессии $_SESSION.
     * @param mixed|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getInitialSession($name = null);

    /**
     * Returns the primary session data of $_COOKIE.
     * @param mixed|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает первичные данные сессии $_COOKIE.
     * @param mixed|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getInitialCookie($name = null);

    /**
     * Returns the current session data of $_SESSION.
     * @param mixed|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает текущие данные сессии $_SESSION.
     * @param mixed|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getSession($name = null);

    /**
     * Returns the current session data of $_COOKIE.
     * @param mixed|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает текущие данные сессии $_COOKIE.
     * @param mixed|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getCookie($name = null);

    /**
     * Returns data from the current route.
     * @param string|null $name - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает данные из текущего роута.
     * @param string|null $name - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function get(string $name = '');

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
    public static function getString(string $name, $default = null);

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
    public static function getInt(string $name, $default = 0);

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
    public static function getFloat(string $name, $default = 0.0);

    /**
     * Returns the request method. For example, 'GET', 'HEAD', 'POST', 'PUT'
     * @return string
     *//**
     * Возвращает метод запроса. Например: 'GET', 'HEAD', 'POST', 'PUT'
     * @return string
     */
    public static function getMethod();

    /**
     * Returns the current request protocol 'http' or 'https'
     * @return string
     *//**
     * Возвращает текущий протокол запроса 'http' или 'https'
     * @return string
     */
    public static function getHttpProtocol();

    /**
     * Returns the complete current request protocol 'http://' or 'https://'
     * @return string
     *//**
     * Возвращает полный текущий протокол запроса 'http://' или 'https://'
     * @return string
     */
    public static function getHttpFullProtocol();

    /**
     * Returns the full host with an http-prefix of the form 'https://site.com'
     * @return string
     *//**
     * Возвращает полный хост с http-префиксом вида 'https://site.com'
     * @return string
     */
    public static function getFullHost();

    /**
     * Trying to find the localization value
     * @return string|false
     *//**
     * Попытка найти значение локализации
     * @return string|false
     */
    public static function getLang();

    /**
     * The address that was provided to access this page. For example '/index.php?p=example'
     * @return null|string
     *//**
     * Адрес, который был предоставлен для доступа к этой странице. Например '/index.php?p=example'
     * @return null|string
     */
    public static function getUri();

    /**
     * The full URL of the current request, of the form 'http://site.com/index.php?p=example'
     * @return null|string
     *//**
     * Полный URL-адрес текущего запроса, вида 'http://site.com/index.php?p=example'
     * @return null|string
     */
    public static function getFullUrl();

    /**
     * The transmitted address of the page from which the user made the transition.
     * @return array|string|string[]|null
     *//**
     * Переданный адрес страницы, c которой пользователь совершил переход.
     * @return array|string|string[]|null
     */
    public static function getReferer();

    /**
     * Returns current domain.
     * @return string
     *//**
     * Возвращает текущий домен.
     * @return string
     */
    public static function getDomain();

    /**
     * Returns the content of the `Host` header.
     * @return string
     *//**
     * Возвращает содержимое заголовка `Host`.
     * @return string
     */
    public static function getHost();

    /**
     * Get the port of the current connection from the host.
     * @return string|null
     *//**
     * Получить порт текущего соединения из хоста.
     * @return string|null
     */
    public static function getPort();

    /**
     * Returns the information array $_SERVER created by the web server, or the requested value from it.
     * @param null|string $value - parameter to get data by name.
     * @return null|array
     *//**
     * Возвращает массив с информацией $_SERVER, созданный веб-сервером, или запрошенное значение из него.
     * @param null|string $value - параметр для получения данных по названию.
     * @return null|mixed|array
     */
    public static function getHttpHeader($value = null);

    /**
     * Determines if a request is requested as ajax
     * @return bool
     *//**
     * Определяет, запрошен ли запрос как ajax
     * @return bool
     */
    public static function isXmlHttpRequest();

    /**
     * Returns the array with data for the downloaded file.
     * @return null|mixed
     *//**
     * Возвращает массив с данными для загруженного файла.
     * @return null|mixed
     */
    public static function getFiles();

    /**
     * Returns the part of the URL request after the script is executed.
     * @return mixed|string
     *//**
     * Возвращает часть URL-запроса после выполняемого сценария.
     * @return mixed|string
     */
    public static function getUrlParameter();

    /**
     * Returns the IP address of the client or IP of the last proxy server through which the client got to the site.
     * @return null|string
     *//**
     * Возвращает IP-адрес клиента или IP последнего прокси-сервера, через который клиент попал на сайт.
     * @return null|string
     */
    public static function getRemoteAddress();

    /**
     * Returns an array or a single value of $_GET.
     * @param null|string $value - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает массив или отдельное значение $_GET.
     * @param null|string $value - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getGet($value = null);

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
    public static function getGetString(string $name, $default = null);

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
    public static function getGetInt(string $name, $default = 0);

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
    public static function getGetFloat(string $name, $default = 0.0);

    /**
     * Returns an array or a single value of $_POST.
     * @param null|string $value - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает массив или отдельное значение $_POST.
     * @param null|string $value - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getPost($value = null);

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
    public static function getPostString(string $name, $default = null);

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
    public static function getPostInt(string $name, $default = 0);

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
    public static function getPostFloat(string $name, $default = 0.0);

    /**
     * Returns an array or a single value of $_REQUEST.
     * @param null|string $value - parameter to get data by name.
     * @return mixed|null
     *//**
     * Возвращает массив или отдельное значение $_REQUEST.
     * @param null|string $value - параметр для получения данных по названию.
     * @return mixed|null
     */
    public static function getRequest($value = null);

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
    public static function getRequestString(string $name, $default = null);

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
    public static function getRequestInt(string $name, $default = 0);

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
    public static function getRequestFloat(string $name, $default = 0.0);

    /**
     * Returns the original string with stripped tags.
     * @param string $value - line to clean up.
     * @return string
     *//**
     * Возвращает исходную строку с очищенными тегами.
     * @param string $value - строка для очистки.
     * @return string
     */
    public static function returnPrivateTags(string $value);

    /**
     * Returns an object for placing headers, styles and scripts in the <head>...</head> of the page.
     * @return Head|null
     *//**
     * Возвращает объект для размещения заголовков, стилей и скриптов в <head>...</head> страницы.
     * @return Head|null
     */
    public static function getHead();

    /**
     * Returns an object for placing loaded resources at the bottom of the page.
     * @return Resources|null
     *//**
     * Возвращает объект для размещения подгружаемых ресурсов в нижней части страницы.
     * @return Resources|null
     */
    public static function getResources();

    /**
     * Returns the relative current URL, similar to getMainUrl()
     * @return string|null
     *//**
     * Возвращает относительный текущий URL, аналогично функции getMainUrl()
     * @return string|null
     */
    public static function getMainConvertUrl();

    /**
     * Returns the relative current URL without GET parameters.
     * @return string
     *//**
     * Возвращает относительный текущий URL без GET-параметров.
     * @return string
     */
    public static function getMainClearUrl();

}