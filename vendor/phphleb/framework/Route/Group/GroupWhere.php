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
final class GroupWhere extends StandardRoute
{
    use StandardGroupTrait;
    use GroupPlainTrait;
    use GroupProtectTrait;

    public function __construct(array $rules)
    {
        $this->register([
            'method' => self::WHERE_TYPE,
            'data' => [
                'rules' => $rules,
            ],
            'from-group' => true
        ]);
    }
}
