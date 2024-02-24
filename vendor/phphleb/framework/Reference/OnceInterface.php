<?php

namespace Hleb\Reference;

interface OnceInterface
{
    /**
     * @see once()
     */
    public static function get(callable $func): mixed;

    public static function rollback(): void;
}
