<?php

declare(strict_types=1);

namespace Hleb\Route;

use Hleb\Constructor\Data\View;
use Hleb\HlebBootstrap;
use Hleb\Main\Routes\Methods\Traits\InsertAfterTrait;
use Hleb\Main\Routes\Methods\Traits\InsertBeforeTrait;
use Hleb\Main\Routes\Methods\Traits\InsertControllerTrait;
use Hleb\Main\Routes\Methods\Traits\InsertMiddlewareTrait;
use Hleb\Main\Routes\Methods\Traits\InsertNameTrait;
use Hleb\Main\Routes\Methods\Traits\InsertPageTrait;
use Hleb\Main\Routes\Methods\Traits\InsertRedirectTrait;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class Fallback extends StandardRoute
{
    use InsertNameTrait;
    use InsertMiddlewareTrait;
    use InsertAfterTrait;
    use InsertBeforeTrait;
    use InsertControllerTrait;
    use InsertPageTrait;
    use InsertRedirectTrait;

    private array $types;

    public function __construct(float|View|int|string|null $view = null, array $httpTypes = [])
    {
        $httpTypes or $httpTypes = HlebBootstrap::HTTP_TYPES;

        $this->types = \array_merge($httpTypes, ['OPTIONS']);

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
                'route' => '*',
                'view' => $params,
            ]
        ]);
    }

    protected function types(): array
    {
        return $this->types;
    }

    protected function methodName(): string
    {
        return self::FALLBACK_SUBTYPE;
    }
}
