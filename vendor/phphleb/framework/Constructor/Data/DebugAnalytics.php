<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Data;

use Hleb\Base\RollbackInterface;
use Hleb\Main\Insert\BaseAsyncSingleton;

/**
 * Class for storing the state of the framework in debug mode.
 *
 * Класс для хранения состояния фреймворка в режиме отладки.
 *
 * @internal
 */
final class DebugAnalytics extends BaseAsyncSingleton implements RollbackInterface
{
    final public const CLASSES_AUTOLOAD = 'classes.autoload';

    final public const INSERT_TEMPLATE = 'insert.template';

    final public const DATA_DEBUG = 'data.debug';

    final public const DB_DEBUG = 'db.debug';

    final public const INITIATOR = 'initiator';

    final public const MIDDLEWARE = 'middleware';

    final public const HL_CHECK = 'hl.check';

    /**
     * Need to maintain compatibility with @see self::rollback()
     *
     * Необходимо поддерживать совместимость с @see self::rollback()
     */
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

    public static function getData(): array
    {
        return self::$data;
    }

    /** @internal */
    public static function setData(#[\SensitiveParameter] array $data): void
    {
        self::$data = $data;
    }

    /** @internal */
    public static function addData(
        #[\SensitiveParameter] string $name,
        #[\SensitiveParameter] mixed  $value
    ): void
    {
        empty(self::$data[$name]) and self::$data[$name] = [];
        self::$data[$name][] = $value;
    }
}
