<?php

declare(strict_types=1);

namespace Hleb\Route\Group;

use Hleb\Main\Routes\Methods\Traits\Group\StandardGroupTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class GroupNoDebug extends StandardRoute
{
    use StandardGroupTrait;

    public function __construct()
    {
        $this->register([
            'method' => self::NO_DEBUG_TYPE,
            'from-group' => true,
        ]);
    }
}
