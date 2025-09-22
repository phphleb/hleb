<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\ReflectionProcessException;

/**
 * A set of methods for obtaining the values of class constants.
 *
 * Набор методов для получения значений констант класса.
 */
#[Accessible]
final class ReflectionConstant
{
    private array $constants = [];

    public function __construct(string $className)
    {
        try {
            $method = (new \ReflectionClass($className));
        } catch (\ReflectionException $e) {
            throw new ReflectionProcessException($e);
        }
        $this->constants = $method->getConstants();
    }

    public function all(): array
    {
        return $this->constants;
    }

    public function get(string $name): mixed
    {
        return $this->constants[$name] ?? false;
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->constants);
    }
}
