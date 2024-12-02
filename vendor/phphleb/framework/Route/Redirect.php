<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\Traits\InsertBeforeTrait;
use Hleb\Main\Routes\Methods\Traits\InsertDomainTrait;
use Hleb\Main\Routes\Methods\Traits\InsertMiddlewareTrait;
use Hleb\Main\Routes\Methods\Traits\InsertNameTrait;
use Hleb\Main\Routes\Methods\Traits\InsertPlainTrait;
use Hleb\Main\Routes\Methods\Traits\InsertProtectTrait;
use Hleb\Main\Routes\Methods\Traits\InsertWhereTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class Redirect extends StandardRoute
{
    use InsertWhereTrait;
    use InsertProtectTrait;
    use InsertNameTrait;
    use InsertMiddlewareTrait;
    use InsertBeforeTrait;
    use InsertDomainTrait;
    use InsertPlainTrait;

    public function __construct(string $location, int $status = 302)
    {
        $this->register([
            'method' => self::REDIRECT_TYPE,
            'location' => $location,
            'status' => $status,
        ]);
    }
}
