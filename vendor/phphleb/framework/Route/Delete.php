<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\BaseType;

/**
 * @internal
 */
final class Delete extends BaseType
{
    #[\Override]
    protected function types(): array
    {
        return ['DELETE', 'OPTIONS'];
    }

    #[\Override]
    protected function methodName(): string
    {
        return self::DELETE_SUBTYPE;
    }
}
