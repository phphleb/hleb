<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\BaseType;

/**
 * @internal
 */
final class Options extends BaseType
{
    public function __construct(string $route)
    {
        parent::__construct($route);
    }

    #[\Override]
    protected function types(): array
    {
        return ['OPTIONS'];
    }

    #[\Override]
    protected function methodName(): string
    {
        return self::OPTIONS_SUBTYPE;
    }
}
