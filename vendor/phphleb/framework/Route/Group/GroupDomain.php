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
final class GroupDomain extends StandardRoute
{
    use StandardGroupTrait;
    use GroupPlainTrait;
    use GroupProtectTrait;

    public function __construct(string|array $part, int $level = 2)
    {
        $this->register([
            'method' => self::DOMAIN_TYPE,
            'name' => \is_array($part) ? $part : [$part],
            'level' => $level,
            'from-group' => true
        ]);
    }
}
