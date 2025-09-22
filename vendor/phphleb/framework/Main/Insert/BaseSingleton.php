<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Insert;

use Hleb\Constructor\Attributes\AvailableAsParent;
use RuntimeException;

#[AvailableAsParent]
class BaseSingleton
{
    private static array $instances = [];

    final protected function __construct() {}

    final protected function __clone() {}

    /**
     * @internal - use ExternalSingleton to get an instance.
     *           - для получения экземпляра используйте ExternalSingleton.
     */
    final protected static function getInstance(): static
    {
        $className = static::class;
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new static;
        }
        return self::$instances[$className];
    }

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
}
