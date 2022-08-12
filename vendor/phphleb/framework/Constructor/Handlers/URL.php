<?php

declare(strict_types=1);

/*
 * Systemic interaction with URLs.
 *
 * Системное взаимодействие с адресами URL.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Main\Helpers\RangeChecker;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Main\Logger\Log;

/**
 * @package Hleb\Constructor\Handlers *
 * @internal
 */
final class URL extends BaseSingleton
{
    const NEEDED_TAGS = ['<', '>'];

    const REPLACING_TAGS = ['&lt;', '&gt;'];

    const MAX_COUNT_LIST = 100;

    protected static $standardUrlList = [];

    protected static $addresses = null;

    // Create a list of used URLs.
    // Создание списка используемых адресов URL
    public static function create(array $address) {
        self::$addresses = $address;
    }

    // Adds a separate URL
    // Добавляет отдельный адрес URL
    public static function add($newAddress) {
        $newAddress = is_array($newAddress) ? $newAddress : [$newAddress];
        self::$addresses = array_merge(self::$addresses, $newAddress);
    }

    // Returns all URLs.
    // Возвращает все адреса URL.
    public static function getAll() {
        return self::$addresses;
    }

    // Returns the address of the route by name with the replacement of variable values ​​in it with the required values.
    // Возвращает адрес роута по имени с заменой переменных значений в нём на необходимые значения.
    public static function getRouteByName(string $name, array $perem = []) {
        return self::getByName($name, $perem);
    }

    // Returns the address of the route by name with the replacement of variable values in it with the required values.
    // Возвращает адрес роута по имени с заменой переменных значений в нём на необходимые значения.
    public static function getByName(string $name, array $params = []) {
        // Получение пути с префиксами по существующему имени роута
        if (!isset(self::$addresses[$name])) return false;
        if (count($params) === 0 && (strpos(self::$addresses[$name], '?}') === false)) {
            return self::endingUrl(self::$addresses[$name]);
        }
        $addressParts = explode('/', trim(self::$addresses[$name], '/\\ ?'));
        if (strpos(end($addressParts), '...') === 0) {
            return self::endingUrl(self::getMultiple($addressParts, $params));
        }
        foreach ($addressParts as $key => $part) {
            $isTag = $part && $part[0] == '@' && $part[1] == '{';

            if ($isTag) {
                $part = ltrim($part, '@');
             }
            if ($part && strlen($part) > 2 && ($part[0] == '{')) {
                if (count($params)) {
                    foreach ($params as $k => $p) {
                        if (($part[strlen($part) - 2] == '?' && $part == '{' . $k . '?}') ||
                            $part == '{' . $k . '}') {
                            $addressParts[$key] = ($isTag ? '@' : '') . $p;
                        } else if ($part[strlen($part) - 2] == '?') {
                            $addressParts[$key] = '';
                        }
                    }
                } else {
                    if ($part[strlen($part) - 2] == '?') {
                        $addressParts[$key] = '';
                    }
                }
            }
        }

        return self::endingUrl(preg_replace('|([/]+)|s', '/', '/' . implode('/', $addressParts) . '/'));
    }

    // Handles URL ending depending on settings.
    // Обрабатывает окончание URL в зависимости от настроек.
    protected static function endingUrl(string $url) {
        if ($url !== '' && defined('HLEB_PROJECT_ENDING_URL')) {
            $ending = $url[strlen($url) - 1];
            $element = explode('/', $url);
            $endElement = end($element);
            if (strpos($endElement, '.') !== false) return $url;
            if (HLEB_PROJECT_ENDING_URL && $ending !== '/') {
                return $url . '/';
            } else if (!HLEB_PROJECT_ENDING_URL && $ending == '/') {
                return substr($url, 0, -1);
            }
        }
        return $url;
    }

    // Redirect to URL from https:// third party site.
    // Редиректит на URL с https:// стороннего сайта.
    public static function redirectToSite(string $url) {
        self::universalRedirect($url, 301);
    }

    // Redirect to internal URL.
    // Редиректит на внутренний адрес URL.
    public static function redirect(string $url, int $code = 303) {
        self::universalRedirect($url, $code);
    }

    // Returns the current URL.
    // Возвращает текущий URL.
    public static function getMainUrl() {
        return Request::getMainConvertUrl();
    }

    // Returns the current URL without parameters.
    // Возвращает текущий URL без параметров.
    public static function getMainClearUrl() {
        return explode('?', urldecode($_SERVER['REQUEST_URI']))[0];
    }

    // Returns a secure URL with a GET parameter containing a token. Not recommended for use!
    // Возвращает защищённый URL с GET-параметром, который содержит токен. Не рекомендуется к использованию!
    public static function getProtectUrl(string $url) {
        $newUrlParts = explode('?', $url);
        if (count($newUrlParts) === 1) {
            return self::getStandard(self::endingUrl($newUrlParts[0])) . '?_token=' . ProtectedCSRF::key();
        }
        $params = '';
        foreach ($newUrlParts as $key => $param) {
            if ($key !== 0) {
                $params .= '?' . str_replace(self::NEEDED_TAGS, self::REPLACING_TAGS, $param);
            }
        }
        return self::getStandard(self::endingUrl($newUrlParts[0])) . $params . '&_token=' . ProtectedCSRF::key();
    }

    // Returns a standardized URL.
    // Возвращает стандартизированный URL.
    public static function getStandardUrl(string $url) {
        if (isset(self::$standardUrlList[$url])) {
            if (count(self::$standardUrlList) < self::MAX_COUNT_LIST) {
                return self::$standardUrlList[$url];
            } else {
                array_unshift(self::$standardUrlList);
            }
        }
        $allUrls = explode('?', $url);
        $params = '';
        foreach ($allUrls as $key => $allUrl) {
            if ($key !== 0) {
                $params .= '?' . str_replace(self::NEEDED_TAGS, self::REPLACING_TAGS, $allUrl);
            }
        }
        $standardUrl = preg_replace('|([/]+)|s', '/', self::getStandard(self::endingUrl($allUrls[0])) . $params);
        self::$standardUrlList[$url] = !empty(trim($standardUrl, '/')) ? $standardUrl : "/";

        return self::$standardUrlList[$url];
    }

    // Returns a standardized string.
    // Возвращает стандартизированную строку.
    private static function getStandard(string $url) {
        if (self::ifHttp($url)) {
            $arr_url = array_slice(explode('/', $url), 3);
            return HLEB_PROJECT_PROTOCOL . HLEB_MAIN_DOMAIN . ($url[0] == '/' ? '' : '/') . (implode('/', $arr_url));
        }
        return rawurldecode($url);
    }


    // Returns the result of checking the contents of the http(s) protocol.
    // Возвращает результат проверки на содержание протокола http(s).
    private static function ifHttp(string $url) {
        return preg_match('/http(s?)\:\/\//i', $url) == 1;
    }

    // Returns the full url.
    // Возвращает полный адрес url.
    public static function getFullUrl(string $url) {
        if (!self::ifHttp($url)) {
            return HLEB_PROJECT_PROTOCOL . HLEB_MAIN_DOMAIN . ($url[0] == '/' ? '' : '/') . self::getStandardUrl($url);
        }
        return self::getStandardUrl($url);
    }

    // Implements a redirect.
    // Реализует редирект.
    protected static function universalRedirect(string $url, int $code = 302) {
        if (!headers_sent()) {
            if($url === '/') {
                header('Location: /', true, $code);
            } else {
                header('Location: ' . self::getStandardUrl($url), true, $code);
            }
        }
        hl_preliminary_exit();
    }

    // Multiple route handling.
    // Обработка множественного маршрута.
    protected static function getMultiple(array $addressParts, array $params = []) {
        $endUrl = array_pop($addressParts);
        $checkRange = (new RangeChecker(trim($endUrl, ' .')))->check(count($params));
        if (!$checkRange) {
            return false;
        }
        foreach ($params as $key => $value) {
            if (!is_numeric($key)) {
                return false;
            }
        }
        ksort($params, SORT_NUMERIC);
        return implode('/', $addressParts) . '/' . implode('/', $params);
    }
}

