<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\BaseType;

/**
 * @internal
 */
final class Patch extends BaseType
{
    #[\Override]
    protected function types(): array
    {
        return ['PATCH', 'OPTIONS'];
    }

    #[\Override]
    protected function methodName(): string
    {
        return self::PATCH_SUBTYPE;
    }
}
