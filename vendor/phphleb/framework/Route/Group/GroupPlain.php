<?php

declare(strict_types=1);

namespace Hleb\Route\Group;

use Hleb\Main\Routes\Methods\Traits\Group\StandardGroupTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class GroupPlain extends StandardRoute
{
    use StandardGroupTrait;

    public function __construct(bool $on = true)
    {
        $this->register([
            'method' => self::PLAIN_TYPE,
            'from-group' => true,
            'data' => ['on' => $on],
        ]);
    }
}
