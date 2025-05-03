<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\HlebBootstrap;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class Alias extends StandardRoute
{
    public function __construct(string $route, string $newName, string $name)
    {
        $this->register([
            'method' => self::ALIAS_SUBTYPE,
            'name' => $name,
            'new-name' => $newName,
            'types' => $this->types(),
            'data' => [
                'route' => $route,
            ]
        ]);
    }

    protected function types(): array
    {
        return HlebBootstrap::HTTP_TYPES;
    }

    protected function methodName(): string
    {
        return self::ALIAS_SUBTYPE;
    }
}
