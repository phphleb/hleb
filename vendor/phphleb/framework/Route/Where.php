<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\Traits\InsertAfterTrait;
use Hleb\Main\Routes\Methods\Traits\InsertBeforeTrait;
use Hleb\Main\Routes\Methods\Traits\InsertControllerTrait;
use Hleb\Main\Routes\Methods\Traits\InsertDomainTrait;
use Hleb\Main\Routes\Methods\Traits\InsertMiddlewareTrait;
use Hleb\Main\Routes\Methods\Traits\InsertModuleTrait;
use Hleb\Main\Routes\Methods\Traits\InsertNameTrait;
use Hleb\Main\Routes\Methods\Traits\InsertPlainTrait;
use Hleb\Main\Routes\Methods\Traits\InsertPageTrait;
use Hleb\Main\Routes\Methods\Traits\InsertProtectTrait;
use Hleb\Main\Routes\Methods\Traits\InsertRedirectTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class Where extends StandardRoute
{
    use InsertControllerTrait;
    use InsertMiddlewareTrait;
    use InsertAfterTrait;
    use InsertBeforeTrait;
    use InsertProtectTrait;
    use InsertNameTrait;
    use InsertModuleTrait;
    use InsertDomainTrait;
    use InsertPageTrait;
    use InsertPlainTrait;
    use InsertRedirectTrait;

    public function __construct(array $rules)
    {
        $this->register([
            'method' => self::WHERE_TYPE,
            'data' => [
                'rules' => $rules,
            ]
        ]);
    }
}
