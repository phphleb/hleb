<?php

namespace Hleb\HttpMethods\Intelligence\Cookies;

use Hleb\HttpMethods\Specifier\DataType;

/**
 * Interface for combining types of work with Cookies.
 *
 * Интерфейс для объединения типов работы с Cookies.
 *
 * @internal
 */
interface CookieInterface
{
    /**
     * @return  DataType[]
     */
    public static function all(): array;

    public static function set(string $name, string $value, array $options = []): void;

    public static function get(string $name): DataType;

    public static function setSessionName(string $name): void;

    public static function getSessionName(): string;

    public static function setSessionId(string $id): void;

    public static function getSessionId(): string;

    public static function delete(string $name): void;

    public static function clear(): void;

    /** @internal */
    public static function output(): void;
}
