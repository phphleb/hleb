<?php

declare(strict_types=1);

namespace Hleb\Main\Console;

use Hleb\Constructor\Attributes\Accessible;

/**
 * @inheritDoc
 *
 * Plug for color design.
 *
 * Заглушка для цветового оформления.
 */
#[Accessible]
class ReplacingColorizer extends Colorizer
{
    /** @inheritDoc */
    public static function standard(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function red(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function green(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function cyan(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function yellow(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function error(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function errorMessage(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function success(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function successMessage(string $text): string
    {
        return $text;
    }

    /** @inheritDoc */
    public static function blue(string $text): string
    {
        return $text;
    }
}
