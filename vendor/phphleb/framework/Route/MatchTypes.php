<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Constructor\Data\View;
use Hleb\Main\Routes\Methods\BaseType;

/**
 * @internal
 */
final class MatchTypes extends BaseType
{
    public function __construct(
        readonly private array     $types,
        string                     $route,
        float|View|int|string|null $view = null)
    {
        parent::__construct($route, $view);
    }

    #[\Override]
    protected function types(): array
    {
        return \array_unique(\array_merge($this->types, ['OPTIONS']));
    }

    #[\Override]
    protected function methodName(): string
    {
        return self::MATCH_SUBTYPE;
    }
}
