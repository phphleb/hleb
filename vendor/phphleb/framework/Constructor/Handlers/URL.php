<?php

namespace Hleb\Constructor\Handlers;

use DeterminantStaticUncreated;

class URL
{

    use DeterminantStaticUncreated;

    const NEEDED_TAGS = ['<', '>'];

    const REPLACING_TAGS = ['&lt;', '&gt;'];

    protected static $addresses;


    public static function create($address)
    {
        self::$addresses = $address;
    }

    public static function add($new_address)
    {
        self::$addresses = array_merge(self::$addresses, $new_address);
    }

    public static function getAll()
    {
        return self::$addresses;
    }

    public static function getRouteByName(string $name, array $perem = []) // Название и замена переменных
    {
        return self::getByName($name, $perem);
    }

    public static function getByName(string $name, array $perem = []) // Название и замена переменных
    {
        // Получение пути с префиксами по существующему имени роута
        if(!isset(self::$addresses[$name])) return false;

        if (count($perem) === 0 && (strpos(self::$addresses[$name], '?}') === false)) {
            return self::endingUrl(self::$addresses[$name]);
        }

        $address_parts = explode('/', self::$addresses[$name]);

        foreach ($address_parts as $key => $part) {
            if (strlen($part)>2 && $part[0] == '{') {
                if(count($perem)) {

                    foreach ($perem as $k => $p) {
                        if (($part[strlen($part) - 2] == '?' && $address_parts[$key] == '{' . $k . '?}') ||
                            $address_parts[$key] == '{' . $k . '}') {
                            $address_parts[$key] = $p;
                        } else if ($part[strlen($part) - 2] == '?') {
                            $address_parts[$key] = '';
                        }
                    }
                } else {
                    if ($part[strlen($part) - 2] == '?') {
                        $address_parts[$key] = '';
                    }
                }
            }
        }

        return self::endingUrl(preg_replace('|([/]+)|s', '/', '/' . implode('/', $address_parts) . '/'));

    }

    protected static function endingUrl($url)
    {

        $ending = $url[strlen($url) - 1];

        $element = explode('/', $ending);

        $end_element = end($element);

        if (strpos($end_element, '.') !== false) return $url;


        if (HLEB_PROJECT_ENDING_URL && $ending !== '/') {

            return $url . '/';

        } else if (!HLEB_PROJECT_ENDING_URL && $ending == '/') {

            return substr($url, 0, -1);

        }

        return str_replace('?', '', $url);

    }


    public static function redirectToSite($url) // Полное url с https:// стороннего сайта
    {

        self::universalRedirect($url, 301);

    }

    public static function redirect(string $url, int $code = 303) // На внутренний адрес URL
    {

        self::universalRedirect($url, $code);

    }

    public static function getMainUrl() // Получить текущий URL
    {

        return Request::getMainConvertUrl();

    }

    public static function getMainClearUrl() // Получить текущий URL без параметров
    {

        return Request::getMainClearUrl();

    }

    public static function getProtectUrl($url) // Защита URL
    {

        $new_url = explode('?', $url);

        if (count($new_url) === 1) {

            return self::getStandard(self::endingUrl($new_url[0])) . '?_token=' . ProtectedCSRF::key();
        }
        $params = '';

        foreach ($new_url as $key => $param) {
            if ($key !== 0) {
                $params .= '?' . str_replace(self::NEEDED_TAGS, self::REPLACING_TAGS, $param);
            }

        }

        return self::getStandard(self::endingUrl($new_url[0])) . $params . '&_token=' . ProtectedCSRF::key();

    }

    public static function getStandardUrl($url)
    {

        $all_urls = explode('?', $url);

        $params = '';

        foreach ($all_urls as $key => $all_url) {

            if ($key !== 0) {

                $params .= '?' .  str_replace(self::NEEDED_TAGS, self::REPLACING_TAGS, $all_url);
            }
        }

        return self::getStandard(self::endingUrl($all_urls[0])) . $params;

    }

    private static function getStandard($url)
    {

        if (self::ifHttp($url)) {

            $arr_url = array_slice(explode('/', $url), 3);

            return HLEB_PROJECT_PROTOCOL . HLEB_MAIN_DOMAIN . ($url[0] == '/' ? '' : '/') . (implode('/', $arr_url));

        }

        return rawurldecode($url);
    }

    private static function ifHttp($url)
    {

        return preg_match('/http(s?)\:\/\//i', $url) == 1;
    }


    public static function getFullUrl($url) // Полный адрес url
    {

        if (!self::ifHttp($url)) {

            return HLEB_PROJECT_PROTOCOL . HLEB_MAIN_DOMAIN . ($url[0] == '/' ? '' : '/') . self::getStandardUrl($url);

        }

        return self::getStandardUrl($url);
    }

    protected static function universalRedirect(string $url, int $code = 302)
    {
        if (!headers_sent()) {

            header('Location: ' . self::getStandardUrl($url), true, $code);
        }
        exit();


    }

}

