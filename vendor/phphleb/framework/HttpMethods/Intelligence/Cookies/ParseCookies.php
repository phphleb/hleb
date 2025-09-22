<?php

/*declare(strict_types=1);*/

namespace Hleb\HttpMethods\Intelligence\Cookies;

/**
 * @internal
 */
final class ParseCookies
{
    /**
     * Search for Cookies in headers and return a standardized array.
     *
     * Поиск Cookie в заголовках и возвращение стандартизированного массива.
     */
    public static function getFromRequestHeaders(
        #[\SensitiveParameter] array $requestHeaders,
    ): array
    {
        $cookies = $requestHeaders['Cookie'] ?? $requestHeaders['cookie'] ?? [];
        if (!\is_array($cookies)) {
            $cookies = \array_map('trim', \explode(';', (string)$cookies));
        }
        return $cookies;
    }
}
