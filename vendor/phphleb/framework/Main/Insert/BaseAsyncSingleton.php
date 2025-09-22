<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Insert;

use Hleb\Constructor\Attributes\AvailableAsParent;
use RuntimeException;

/**
 * Does not imply getting instance.
 *
 * Не подразумевает получение instance.
 */
#[AvailableAsParent]
abstract class BaseAsyncSingleton
{
    final protected function __construct() {}

    final protected function __clone() {}

    /** @throws RuntimeException */
    final public function __wakeup(): void
    {
        throw new RuntimeException("Cannot serialize singleton");
    }

    final public function __serialize(): array
    {
        self::__wakeup();
    }

    final public function __unserialize(array $data)
    {
        self::__wakeup();
    }

    /**
     * Sets the default values when the framework initializes the class.
     * Only used for this purpose in asynchronous requests.
     *
     * Выставляет значения по умолчанию при инициализации класса фреймворком.
     * Используется только для этой цели в асинхронных запросах.
     */
    abstract public static function rollback(): void;

}
