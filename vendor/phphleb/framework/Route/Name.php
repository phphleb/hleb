<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\Traits\InsertAfterTrait;
use Hleb\Main\Routes\Methods\Traits\InsertBeforeTrait;
use Hleb\Main\Routes\Methods\Traits\InsertControllerTrait;
use Hleb\Main\Routes\Methods\Traits\InsertDomainTrait;
use Hleb\Main\Routes\Methods\Traits\InsertMiddlewareTrait;
use Hleb\Main\Routes\Methods\Traits\InsertModuleTrait;
use Hleb\Main\Routes\Methods\Traits\InsertPlainTrait;
use Hleb\Main\Routes\Methods\Traits\InsertPageTrait;
use Hleb\Main\Routes\Methods\Traits\InsertProtectTrait;
use Hleb\Main\Routes\Methods\Traits\InsertWhereTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class Name extends StandardRoute
{
    use InsertWhereTrait;
    use InsertControllerTrait;
    use InsertMiddlewareTrait;
    use InsertAfterTrait;
    use InsertBeforeTrait;
    use InsertProtectTrait;
    use InsertModuleTrait;
    use InsertDomainTrait;
    use InsertPageTrait;
    use InsertPlainTrait;

    public function __construct(string $name)
    {
        $this->register([
            'method' => self::NAME_TYPE,
            'name' => $name
        ]);
    }
}
