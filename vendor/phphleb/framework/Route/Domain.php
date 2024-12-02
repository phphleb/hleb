<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\Traits\InsertAfterTrait;
use Hleb\Main\Routes\Methods\Traits\InsertBeforeTrait;
use Hleb\Main\Routes\Methods\Traits\InsertControllerTrait;
use Hleb\Main\Routes\Methods\Traits\InsertPlainTrait;
use Hleb\Main\Routes\Methods\Traits\InsertPageTrait;
use Hleb\Main\Routes\Methods\Traits\InsertDomainTrait;
use Hleb\Main\Routes\Methods\Traits\InsertMiddlewareTrait;
use Hleb\Main\Routes\Methods\Traits\InsertModuleTrait;
use Hleb\Main\Routes\Methods\Traits\InsertNameTrait;
use Hleb\Main\Routes\Methods\Traits\InsertProtectTrait;
use Hleb\Main\Routes\Methods\Traits\InsertRedirectTrait;
use Hleb\Main\Routes\Methods\Traits\InsertWhereTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class Domain extends StandardRoute
{
    use InsertWhereTrait;
    use InsertControllerTrait;
    use InsertPageTrait;
    use InsertMiddlewareTrait;
    use InsertAfterTrait;
    use InsertBeforeTrait;
    use InsertProtectTrait;
    use InsertModuleTrait;
    use InsertNameTrait;
    use InsertDomainTrait;
    use InsertPlainTrait;
    use InsertRedirectTrait;

    public function __construct(string|array $part, int $level = 2)
    {
        $this->register([
            'method' => self::DOMAIN_TYPE,
            'name' => \is_array($part) ? $part : [$part],
            'level' => $level
        ]);
    }
}
