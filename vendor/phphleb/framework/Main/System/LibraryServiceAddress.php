<?php

namespace Hleb\Main\System;

use Hleb\Constructor\Data\DynamicParams;

/**
 * @internal
 */
final class LibraryServiceAddress
{
    final public const KEY = 'hlresource';

    /**
     * Returns the full path to the library resource.
     *
     * Возвращает полный путь до ресурса библиотеки.
     */
    public static function getFullAddress(string $library, string $version = 'v1'): string
    {
        $uri = DynamicParams::getRequest()->getUri();
        return $uri->getScheme() . '://' . $uri->getHost() . self::getAddress($library, $version);
    }

    /**
     * Returns the path to the library resource.
     *
     * Возвращает путь до ресурса библиотеки.
     */
    public static function getAddress(string $library, string $version = 'v1'): string
    {
        return '/' . self::KEY . '/' . $library . '/' . $version;
    }
}
