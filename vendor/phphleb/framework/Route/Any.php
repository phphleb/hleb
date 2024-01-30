<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\HlebBootstrap;
use Hleb\Main\Routes\Methods\BaseType;

/**
 * @internal
 */
final class Any extends BaseType
{
    #[\Override]
    protected function types(): array
    {
        return HlebBootstrap::HTTP_TYPES;
    }

    #[\Override]
    protected function methodName(): string
    {
        return self::ANY_SUBTYPE;
    }
}
