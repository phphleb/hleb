<?php

declare(strict_types=1);

namespace Hleb\Route\Group;

use Hleb\Main\Routes\Methods\Traits\Group\StandardGroupTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class GroupProtect extends StandardRoute
{
    use StandardGroupTrait;

    /**
     * @param string|string[] $rules
     */
    public function __construct(string|array $rules)
    {
        if (\is_string($rules)) {
            $rules = [$rules];
        }

        $this->register([
            'method' => self::PROTECT_TYPE,
            'from-group' => true,
            'data' => ['rules' => $rules],
            'code' => $this->getFileAndLineNumber(),
        ]);
    }
}
