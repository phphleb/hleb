<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods;

use Hleb\Constructor\Data\View;
use Hleb\Main\Routes\Methods\Traits\StandardTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
abstract class BaseType extends StandardRoute
{
    use StandardTrait;

    public function __construct(string $route, null|int|float|string|View $view = null)
    {
        $params = null;
        if ($view instanceof View) {
            $params = $view->toArray();
        } else if ($view !== null) {
            $params = (string)$view;
        }
        $types = \array_unique(\array_map('strtoupper', $this->types()));

        $this->register([
            'method' => self::ADD_TYPE,
            'name' => $this->methodName(),
            'types' => $types,
            'data' => [
                'route' => $this->updateRouteAddress($route),
                'view' => $params,
            ]
        ]);
    }

    abstract protected function types(): array;

    abstract protected function methodName(): string;
}
