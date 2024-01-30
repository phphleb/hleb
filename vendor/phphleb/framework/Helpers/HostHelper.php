<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;

#[Accessible]
final class HostHelper
{
    /**
     * Returns the result of checking whether the specified host is localhost.
     *
     * Возвращает результат проверки - является ли указанный хост localhost.
     */
    public static function isLocalhost(string $host): bool
    {
        if (\in_array($host, ['localhost', '127.0.0.1', '::1'])) {
            return true;
        }
        if (\str_starts_with($host, 'localhost:')) {
            return true;
        }
        if (\str_starts_with($host, '127.0.0.')) {
            return true;
        }
        return false;
    }
}
