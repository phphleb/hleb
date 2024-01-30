<?php

declare(strict_types=1);

namespace Hleb\Main\Routes;

use Hleb\Main\Insert\BaseAsyncSingleton;

/**
 * @internal
 */
final class BaseRoute extends BaseAsyncSingleton
{

    private static array $data = [];

    /**
     * @inheritDoc
     *
     * @internal
     */
    #[\Override]
    public static function rollback(): void
    {
        self::$data = [];
    }

    /**
     * @internal
     */
    public static function completion(): array
    {
        $data = self::$data;
        self::$data = [];

        return $data;
    }

    /**
     * @internal
     */
    public static function add(array $method): void
    {
        self::$data[] = $method;
    }
}
