<?php

/*declare(strict_types=1);*/

namespace Hleb\Route\Group;

use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class EndGroup extends StandardRoute
{
    public function __construct()
    {
        $this->register([
            'method' => self::END_GROUP_TYPE,
        ]);
    }
}
