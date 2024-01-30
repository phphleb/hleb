<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\BaseType;

/**
 * @internal
 */
final class Put extends BaseType
{
    #[\Override]
    protected function types(): array
    {
        return ['PUT', 'OPTIONS'];
    }

    #[\Override]
    protected function methodName(): string
    {
        return self::PUT_SUBTYPE;
    }
}
