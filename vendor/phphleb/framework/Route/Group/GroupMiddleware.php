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
final class GroupMiddleware extends StandardRoute
{
    use StandardGroupTrait;
    use GroupPlainTrait;
    use GroupProtectTrait;

    public function __construct(string $target, ?string $method = null, array $data = [])
    {
        [$class, $method] = $this->searchMiddlewareAttributes($target, $method);

        $this->register([
            'method' => self::MIDDLEWARE_TYPE,
            'class' => $class,
            'class-method' => $method,
            'from-group' => true,
            'related-data' => $data,
        ]);
    }
}
