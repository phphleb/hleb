<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\RoutesPreview;
use Hleb\Constructor\Data\UrlManager;
use Hleb\DomainException;
use Hleb\HlebBootstrap;
use Hleb\InvalidArgumentException;
use Hleb\Main\Insert\ContainerUniqueItem;

#[Accessible] #[AvailableAsParent]
class RouterReference extends ContainerUniqueItem implements RouterInterface, Interface\Router
{
    /** @inheritDoc */
    #[\Override]
    public function name(): ?string
    {
        return DynamicParams::getRouteName();
    }

    /** @inheritDoc */
    #[\Override]
    public function url(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): false|string
    {
        $upperMethod = strtoupper($method);
        if (!in_array($upperMethod, HlebBootstrap::HTTP_TYPES)) {
            throw new InvalidArgumentException('Unsupported HTTP method parameter.');
        }
        $routes = RoutesPreview::getByMethod($method);
        if (!$routes) {
            throw new DomainException('Routes not found.');
        }
        return (new UrlManager)->getUrlAddressByName($routes, $routeName, $replacements, $endPart);
    }

    /** @inheritDoc */
    #[\Override]
    public function address(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): false|string
    {
        $uri = DynamicParams::getRequest()->getUri();
        $url = self::url($routeName, $replacements, $endPart, $method);
        $ending = DynamicParams::isEndingUrl() ? '/' : '';
        if ($url === '/') {
            $url = '';
            $ending = '';
        }
        return \rtrim($uri->getScheme() . '://' . $uri->getHost() . $url, '/') . $ending;
    }

    /** @inheritDoc */
    #[\Override]
    public function data(): array
    {
        return DynamicParams::getControllerRelatedData();
    }
}
