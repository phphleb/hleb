<?php

/*declare(strict_types=1);*/

namespace Hleb\HttpMethods\Intelligence\Cookies;

use Hleb\Base\RollbackInterface;
use Hleb\HttpMethods\Intelligence\AsyncConsolidator;
use Hleb\Static\Response;
use Hleb\HttpMethods\Specifier\DataType;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Static\Settings;

/**
 * @internal
 */
final class AsyncCookies extends BaseAsyncSingleton implements CookieInterface, RollbackInterface
{
    /**
     * Current dynamic data Cookies.
     *
     * Текущие динамические данные Cookies.
     *
     * ```php
     * [
     *    ['name1' => 'test1'], // Original
     *    ['name2' => [ // Changed or added
     *        'value' => 'test2', // Not empty
     *        'Path' => '/',
     *        'Domain' => '.example.com',
     *        'Secure' => true, // or false
     *        'HttpOnly' => true, // or false
     *        'SameSite' => 'None' // None || Lax  || Strict
     *        'Expires' => 'Sun, 10 Dec 2023 22:36:02 GMT', // Or missing
     *     ],
     * ]
     * ```
     */
    private static array $data = [];

    private static string $sessionName = 'PHPSESSID';

    /**
     * List of entities moved for deletion from self::$data.
     *
     * Список перенесенных на удаление сущностей из self::$data.
     *
     * [['name1' => true], ['name2' => true]]
     */
    private static array $deleteList = [];

    /**
     * Возвращает объект DataType со значением Cookie по названию.
     *
     * Returns a DataType object with the Cookie value by name.
     *
     * @internal
     */
    #[\Override]
    public static function get(string $name): DataType
    {
        $cookie = self::$data[$name] ?? null;
        if (\is_array($cookie)) {
            $cookie = $cookie['value'];
        }
        return new DataType($cookie);
    }

    /**
     * Converts and stores user data Cookies.
     * Does not support the extremely rare need to set multiple values
     * with the same name, for example if they contain different `path`.
     *
     * Преобразует и сохраняет пользовательские данные Cookie.
     * Не поддерживает крайне редкую необходимость установки нескольких значений
     * с одинаковым именем, например, если они содержат различный `path`.
     *
     * @internal
     */
    #[\Override]
    public static function set(string $name, string $value, array $options = []): void
    {
        self::$data[$name] = AsyncConsolidator::convertCookie($name, $value, $options);
        unset(self::$deleteList[$name]);
    }

    /**
     * Parsing data from Cookies taken from a PSR-7 object or from $_COOKIE.
     *
     * Разбор данных с Cookies взятых из объекта PSR-7 или из $_COOKIE.
     *
     * @internal
     */
    public static function setOptions(array $data): void
    {
        self::$data = [];
        self::$deleteList = [];
        foreach ($data as $id => $cookie) {
            // $_COOKIE
            if (\is_string($id)) {
                self::$data[$id] = $cookie;
                continue;
            }
            if (\is_array($cookie)) {
                $body = \explode('=', \array_shift($cookie));
                $name = \array_shift($body);
                if ($body) {
                    self::$data[$name] = \implode('=', $body);
                }
                continue;
            }
            if (\is_string($cookie)) {
                $cookies = \str_contains($cookie, ';') ? \array_map('ltrim', \explode(';', $cookie)) : [$cookie];
                foreach ($cookies as $block) {
                    $body = \explode('=', $block);
                    $name = \array_shift($body);
                    if ($body) {
                        $body = \implode('=', $body);
                        // When duplicating, the very first value is taken.
                        // При дублировании берется самое первое значение.
                        isset(self::$data[$name]) or self::$data[$name] = $body;
                    }
                }
            }
        }
    }

    /**
     * Returns a named array of objects with the current Cookies values.
     *
     * Возвращает именованный массив объектов с текущими значениями Cookies.
     *
     * @return DataType[]
     *
     * @internal
     */
    #[\Override]
    public static function all(): array
    {
        $data = [];
        foreach (self::$data as $key => $value) {
            if (\is_array($value)) {
                $value = $value['value'];
            }
            $data[$key] = new DataType($value);
        }
        return $data;
    }

    /**
     * Sets a new value for the session Cookie name.
     *
     * Устанавливает новое значение названия сессионной Cookie.
     *
     * @internal
     */
    #[\Override]
    public static function setSessionName(string $name): void
    {
        if (isset(self::$data[self::$sessionName]) && $name !== self::$sessionName) {
            self::$data[$name] = self::$data[self::$sessionName];
            self::$deleteList[self::$sessionName] = true;
            unset(
                self::$data[self::$sessionName],
                self::$deleteList[$name],
            );
        }
        self::$sessionName = $name;
    }

    /**
     * @internal
     * @see CookieReference::getSessionName()
     */
    #[\Override]
    public static function getSessionName(): string
    {
        return self::$sessionName;
    }

    /**
     * @internal
     * @see CookieReference::setSessionId()
     */
    #[\Override]
    public static function setSessionId(string $id): void
    {
        unset(self::$deleteList[self::$sessionName]);

        $options = Settings::getParam('main', 'session.options');
        if ($options) {
            self::set(self::$sessionName, $options);
            return;
        }
        $params = [
            'value' => $id,
            'Path' => '/',
            'SameSite' => 'Strict',
        ];
        $lifetime = Settings::getParam('system', 'max.session.lifetime');
        if ($lifetime) {
            $params['Expires'] = \date('D, d M Y H:i:s \G\M\T', \time() + $lifetime);
        }
        self::$data[self::$sessionName] = $params;
    }

    /**
     * @internal
     * @see CookieReference::getSessionId()
     */
    #[\Override]
    public static function getSessionId(): string
    {
        return self::get(self::$sessionName)->asString('');
    }

    /**
     * Deletes the selected Cookie by name.
     *
     * Удаляет выбранную Cookie по названию.
     *
     * @internal
     */
    #[\Override]
    public static function delete(string $name): void
    {
        self::$deleteList[$name] = true;
        unset(self::$data[$name]);
    }

    /**
     * @internal
     */
    #[\Override]
    public static function output(): void
    {
        foreach (self::$data as $name => $data) {
            // Only the added data is saved, not the original data.
            // Сохраняются только добавленные данные, а не исходные.
            if (!\is_array($data)) {
                continue;
            }
            $cookie = [];
            foreach ($data as $param => $value) {
                if ($param === 'value') {
                    $cookie[] = $name . '=' . $value;
                    continue;
                }
                if ($value === true) {
                    $cookie[] = $param;
                    continue;
                }
                $cookie[] = $param . '=' . $value;
            }
            self::update($cookie);
        }
        $expires = \date('D, d M Y H:i:s \G\M\T', \time() - 31536000);
        foreach (self::$deleteList as $name => $data) {
            if (\is_array($data)) {
                $cookie = [];
                foreach ($data as $param => $value) {
                    if ($param === 'value') {
                        $cookie[] = $name . '=';
                        continue;
                    }
                    if ($param === 'Expires') {
                        continue;
                    }
                    if ($value === true) {
                        $cookie[] = $param;
                        continue;
                    }
                    $cookie[] = $param . '=' . $value;
                }
                $cookie[] = 'Expires=' . $expires;
                self::update($cookie);
                if (!in_array('Path', $data)) {
                    $cookie[] = 'Path=/';
                    self::update($cookie);
                }
            } else {
                $cookie = [$name . '=', 'Expires=' . $expires];
                self::update($cookie);
                $cookie[] = 'Path=/';
                self::update($cookie);
            }
        }
        self::rollback();
    }

    /**
     * Deleting all existing Cookies.
     *
     * Удаление всех имеющихся Cookies.
     *
     * @internal
     */
    #[\Override]
    public static function clear(): void
    {
        foreach (self::$data as $name => $item) {
            self::delete($name);
        }
    }

    /**
     * @inheritDoc
     *
     * @internal
     */
    #[\Override]
    public static function rollback(): void
    {
        self::$data = [];

        self::$deleteList = [];

        self::$sessionName = 'PHPSESSID';
    }

    private static function update(array$cookie): void
    {
        Response::addHeaders(['Set-Cookie' => \implode('; ', $cookie)], false);
    }
}
