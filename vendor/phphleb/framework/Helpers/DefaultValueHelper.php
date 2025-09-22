<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\DomainException;
use Throwable;

/**
 * @internal
 */
final class DefaultValueHelper
{
    /**
     * Allows the use of an error class in the value to indicate that the parameter is missing.
     *
     * Разрешает использовать в значении класс ошибки для обозначения параметра отсутствующим.
     *
     * @throws Throwable
     */
    public static function err(bool|string $exc, string $text = 'A required parameter is missing.'): void
    {
        if ($exc === false) {
            return;
        }
        if ($exc === true) {
            throw new DomainException($text);
        }
        if (\class_exists($exc) && \is_subclass_of($exc, Throwable::class)) {
            throw new $exc($text);
        }
        throw new \RuntimeException("The class $exc should be an exception.");
    }
}
