<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\BaseType;

/**
 * @internal
 */
final class Get extends BaseType
{
    #[\Override]
    protected function types(): array
    {
        return ['GET', 'OPTIONS'];
    }

    #[\Override]
    protected function methodName(): string
    {
        return self::GET_SUBTYPE;
    }
}
