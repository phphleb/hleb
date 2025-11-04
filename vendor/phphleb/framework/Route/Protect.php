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
use Hleb\Main\Routes\Methods\Traits\InsertPageTrait;
use Hleb\Main\Routes\Methods\Traits\InsertRedirectTrait;
use Hleb\Main\Routes\Methods\Traits\InsertWhereTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class Protect extends StandardRoute
{
    use InsertWhereTrait;
    use InsertControllerTrait;
    use InsertMiddlewareTrait;
    use InsertAfterTrait;
    use InsertBeforeTrait;
    use InsertNameTrait;
    use InsertModuleTrait;
    use InsertDomainTrait;
    use InsertPageTrait;
    use InsertRedirectTrait;

    public function __construct(string|array $rules)
    {
        \is_string($rules) and $rules = [$rules];

        $this->register([
            'method' => self::PROTECT_TYPE,
            'from-group' => false,
            'data' => ['rules' => $rules],
            'code' => $this->getFileAndLineNumber(),
        ]);
    }
}
