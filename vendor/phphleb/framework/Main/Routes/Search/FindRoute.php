<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Search;

use Hleb\Constructor\Data\DynamicParams;
use Hleb\AsyncRouteException;
use Hleb\HttpMethods\External\RequestUri;
use Hleb\HttpMethods\External\SystemRequest;

/**
 * @internal
 */
final class FindRoute
{
    private array $error = [];

    private bool $isBlocked = false;

    private string|null $routeName = null;

    private array $data = [];

    /**
     * Allows you to search for a match of the URL address with the current routes.
     *
     * Позволяет искать совпадение адреса URL c текущими маршрутами.
     */
    public function __construct(private readonly string $url)
    {
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function getRouteName(): null|string
    {
        return $this->routeName;
    }

    /**
     * Returns the result of the route search.
     *
     * Возвращает результат поиска маршрута.
     */
    public function one(string $method = 'GET', ?string $domain = null): false|array
   {
       $url = $this->withIndex(\trim($this->url, '/?'));
       $method = \strtoupper($method);
       $oldRequest = DynamicParams::getBaseRequest();
       $uri = new RequestUri($domain, $url, '', 80, 'https', '127.0.0.1');
       $request = new SystemRequest([], null, null, null, $method, [], '1.1', $uri);
       DynamicParams::setDynamicRequest($request);
       try {
           $search = new RouteAnyFileManager();
           $block = $search->getBlock();
       } catch (AsyncRouteException $e) {
           $this->rollbackRequest($oldRequest);
           $this->error[]= $e->getError();
           return false;
       }
       $this->rollbackRequest($oldRequest);
       if ($search->isBlocked()) {
           $this->isBlocked = true;
           return false;
       }
       $this->routeName = $search->getRouteName();
       $this->data = $search->getData();

       return $block;
   }

    /**
     * Convert to an index page if the address contains no parts.
     *
     * Преобразование в индексную страницу, если адрес не содержит частей.
     */
    private function withIndex(string $address): string
    {
        return $address !== '' ? $address : '/';
    }

    /**
     * Returns the Request to its previous value.
     *
     * Возвращает Request к прежнему значению.
     */
    private function rollbackRequest(?SystemRequest $request): void
    {
       $request === null ? DynamicParams::rollback() : DynamicParams::setDynamicRequest($request);
    }
}
