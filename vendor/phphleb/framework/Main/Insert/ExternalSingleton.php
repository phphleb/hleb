<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Insert;

use Hleb\Constructor\Attributes\AvailableAsParent;
use RuntimeException;

/**
 * For backward compatibility with custom containers,
 * this class can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот класс может только расширяться.
 */
#[AvailableAsParent]
class ExternalSingleton
{
    private static array $instances = [];

    final protected function __construct() {}

    final protected function __clone() {}

    /**
     * Allows you to get a reference to an instance of a class.
     *
     * Позволяет получить ссылку на экземпляр класса.
     */
    final public static function instance(): static
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
