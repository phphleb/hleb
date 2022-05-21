<?php

declare(strict_types=1);

namespace Hleb\Main\Insert\Examples;

use Hleb\Main\Insert\BaseSingleton;
use Hleb\Scheme\Home\Constructor\Handlers\RequestInterface;
use Hleb\Scheme\Home\Main\DBInterface;
use Hleb\Scheme\Home\Main\LoggerInterface;

/**
 * Class for creating a tested substitution in the App() function.
 *
 * Класс для создания тестируемой подстановки в функцию App().
 *
 * if (!function_exist('App')) {
 *   function App(){
 *    return ExampleApp::getInstance();
 *   }
 * }
 *
 * ExampleApp::add(
 * [
 *       'request' => new ExampleMirrorRequest([...]),
 *       'db' => new ExampleMirrorDB(['run' => true]),
 *       'getCsrfToken' => null,
 *       ...
 *    ]
 * );
 * App()->getCsrfToken(); // NULL
 * App()->db()->run('Any value'); // true
 *
 * ExampleApp::set('getCsrfToken', 'test');
 *
 * App()->getCsrfToken(); // test
 *
 *
 * @package Hleb\Main\Insert\Examples
 */
class ExampleApp extends BaseSingleton
{
    private static $app = [];

    /**
     * @param array $list -  Adding test values to be returned in methods.
     *
     *                    -  Добавление тестовых значений, которые будут возвращены в методах.
     */
    public static function add(array $list)
    {
        self::$app = $list;
    }

    /**
     * Sets a specific value for an attachment.
     *
     * Устанавливает конкретное значение для вложения.
     *
     * @param array|string $name
     * @param mixed $value
     * @throws \ErrorException
     */
    public static function set($name, $value)
    {
        if (is_string($name)) {
            self::$app[$name] = $value;
        } else if (is_array($name)) {
            switch (count($name)) {
                case 1:
                    self::$app[$name[0]] = $value;
                    break;
                case 2:
                    self::$app[$name[0]][$name[1]] = $value;
                    break;
                case 3:
                    self::$app[$name[0]][$name[1]][$name[2]] = $value;
                    break;
                default:
                    throw new \ErrorException('Incorrect nesting for an array with settings.');
            }
        }
    }

    /**
     * ['request' => new ExampleMirrorRequest([...])];
     * @return RequestInterface
     */
    public function request(): RequestInterface
    {
        return self::$app['request'];
    }

    /**
     * ['db' => new ExampleMirrorDB([...])];
     * @param null $configKey
     * @return DBInterface
     */
    public function db($configKey = null): DBInterface
    {
        return self::$app['db'];
    }

    public function projectPath()
    {
        return self::$app['projectPath'];
    }

    public function storagePath()
    {
        return self::$app['storagePath'];
    }

    public function publicPath()
    {
        return self::$app['storagePath'];
    }

    public function viewPath()
    {
        return self::$app['viewPath'];
    }

    public function data(string $name = null)
    {
        return self::$app['data'];
    }

    public function getCsrfField()
    {
        return self::$app['getCsrfField'];
    }

    public function getCsrfToken()
    {
        return self::$app['getCsrfToken'];
    }

    public function redirectToSite($url)
    {
        return self::$app['redirectToSite'];
    }

    public function redirect(string $url, int $code = 303)
    {
        return self::$app['redirect'];
    }

    public function getProtectUrl(string $url)
    {
        return self::$app['getProtectUrl'];
    }

    public function getFullUrl(string $url)
    {
        return self::$app['getFullUrl'];
    }

    public function getMainUrl()
    {
        return self::$app['getMainUrl'];
    }

    public function getMainClearUrl()
    {
        return self::$app['getMainClearUrl'];
    }

    public function getUrlByName(string $name, array $args = [])
    {
        return self::$app['getUrlByName'];
    }

    public function getStandardUrl(string $name)
    {
        return self::$app['getStandardUrl'];
    }

    public function printR2($data, $desc = null)
    {
        return self::$app['printR2'];
    }

    public function includeCachedTemplate(string $template, array $params = [])
    {
        return self::$app['includeCachedTemplate'];
    }

    public function includeOwnCachedTemplate(string $template, array $params = [])
    {
        return self::$app['includeOwnCachedTemplate'];
    }

    public function insertTemplate(string $path, array $params = [])
    {
        return self::$app['insertTemplate'];
    }

    /**
     * ['logger' => new ExampleLog([...])];
     * @return LoggerInterface
     */
    public function logger(): LoggerInterface
    {
        return self::$app['logger'];
    }
}

