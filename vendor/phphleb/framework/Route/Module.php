<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Main\Routes\Methods\Traits\InsertAfterTrait;
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
final class Module extends StandardRoute
{
    use InsertWhereTrait;
    use InsertProtectTrait;
    use InsertNameTrait;
    use InsertMiddlewareTrait;
    use InsertAfterTrait;
    use InsertBeforeTrait;
    use InsertDomainTrait;
    use InsertPlainTrait;

    public function __construct(string $name, string $target, ?string $method = null)
    {
        [$class, $method] = $this->searchModuleAttributes($name, $target, $method);

        $this->register([
            'method' => self::MODULE_TYPE,
            'name' => $name,
            'class' => $class,
            'class-method' => $method,
        ]);
    }
}
