<?php

declare(strict_types=1);

namespace Hleb\Route\Group;

use Hleb\Main\Routes\Methods\Traits\Group\GroupPlainTrait;
use Hleb\Main\Routes\Methods\Traits\Group\GroupProtectTrait;
use Hleb\Main\Routes\Methods\Traits\Group\StandardGroupTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class GroupPrefix extends StandardRoute
{
    use StandardGroupTrait;
    use GroupPlainTrait;
    use GroupProtectTrait;

    public function __construct(string $prefix)
    {
        $this->register([
            'method' => self::PREFIX_TYPE,
            'prefix' => $prefix,
            'from-group' => true
        ]);
    }
}
