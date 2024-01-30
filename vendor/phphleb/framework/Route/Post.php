<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\BaseType;

/**
 * @internal
 */
final class Post extends BaseType
{
    #[\Override]
    protected function types(): array
    {
        return ['POST', 'OPTIONS'];
    }

    #[\Override]
    protected function methodName(): string
    {
        return self::POST_SUBTYPE;
    }
}
