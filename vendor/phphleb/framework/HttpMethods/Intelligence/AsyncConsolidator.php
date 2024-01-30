<?php

/*declare(strict_types=1);*/

namespace Hleb\HttpMethods\Intelligence;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\HttpMethods\Intelligence\Cookies\StandardCookies;
use Hleb\InvalidArgumentException;
use Hleb\Static\Cookies;
use Hleb\HttpMethods\Intelligence\Cookies\AsyncCookies;
use Hleb\HttpMethods\Intelligence\Cookies\ParseCookies;
use Hleb\Static\Settings;

#[Accessible]
final class AsyncConsolidator
{
    /**
     * Sets the session if it doesn't exist.
     *
     * Устанавливает сессию при её отсутствии.
     */
    public static function initAsyncSessionAndCookies(): void
    {
        \session_abort();
        $_SESSION = [];
        $name = Settings::getParam('system', 'session.name');
        \session_name($name);
        AsyncCookies::setOptions(
            $_COOKIE ?? ParseCookies::getFromRequestHeaders(DynamicParams::getRequest()->getHeaders())
        );

        // To start a new session, you need to define an identifier.
        // Для запуска новой сессии необходимо определить идентификатор.
        $id = $_COOKIE[$name] ?? Cookies::get($name)->value() ?: \session_create_id();
        if ($id) {
            \session_id($id);
        }
        \session_start();

        if (\session_name()) {
            // Writing a session cookies.
            // Запись сессионной Cookies.
            $options = ['path' => '/'];
            $config = Settings::getParam('main', 'session.options');
            if ($config) {
                $options = $config;
            } else {
                $lifetime = Settings::getParam('system', 'max.session.lifetime');
                if ($lifetime > 0) {
                    $options['expires'] = time() + $lifetime;
                }
            }
            Cookies::set($name, \session_id(), $options);
            Cookies::setSessionName(\session_name());
        }
        if (!isset($_COOKIE)) {
            $_COOKIE = [];
            foreach (Cookies::all() as $name => $item) {
                $_COOKIE[$name] = $item;
            }
        }
    }

    /**
     * Cookie initialization and synchronization.
     *
     * Инициализация и синхронизация Cookie.
     */
    public static function initAllCookies(): void
    {
        if (Settings::isStandardMode()) {
            if (\session_status() !== PHP_SESSION_ACTIVE) {
                \session_name(Settings::getParam('system', 'session.name'));
                $options = Settings::getParam('main', 'session.options');
                if ($options) {
                    \session_set_cookie_params($options);
                } else {
                    $lifetime = Settings::getParam('system', 'max.session.lifetime');
                    if ($lifetime > 0) {
                        \session_set_cookie_params($lifetime);
                    }
                }
            }
            if (!\session_id()) {
                \session_start();
            }
            StandardCookies::sync();
        } else {
            self::initAsyncSessionAndCookies();
        }
    }

    /**
     * Converts a Cookie from the setcookie() function to a header standard (Set-Cookie).
     *
     * Преобразует Cookie из функции setcookie() в стандарт заголовка (Set-Cookie).
     *
     * @internal
     */
    public static function convertCookie(string $name, string $value, array $options = []): array
    {
        $optKeys = \array_keys($options);
        if ($options && \count(\array_intersect(Cookies::OPTION_KEYS, $optKeys)) !== \count($optKeys)) {
            self::error('`options` array keys can only be of the following types: ' . \implode(', ', Cookies::OPTION_KEYS));
        }
        $data = [
            'value' => $value,
            'Path' => $options['path'] ?? '/',
        ];
        if (isset($options['expires'])) {
            // This format has been used since PHP 8.2
            // Этот формат используется начиная с версии PHP 8.2
            $data['Expires'] = \date('D, d M Y H:i:s \G\M\T', $options['expires']);
        }
        if (isset($options['secure'])) {
            $data['Secure'] = true;
        }
        if (isset($options['httponly'])) {
            $data['HttpOnly'] = true;
        }
        if (isset($options['samesite'])) {
            if (!\in_array($options['samesite'], Cookies::SAMESITE_VALUES, true)) {
                self::error('`samesite` value can only be the following:' . \implode(', ', Cookies::SAMESITE_VALUES));
            }
            $data['SameSite'] = $options['samesite'];
        }

        return $data;
    }

    private static function error(string $text): void
    {
        throw new InvalidArgumentException($text);
    }
}
