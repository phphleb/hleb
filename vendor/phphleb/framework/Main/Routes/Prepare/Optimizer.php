<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Prepare;

use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final class Optimizer
{
    private array $routesByMethod = [];

    private array $routesInfo = [];

    private array $routesList = [];

    public function __construct(readonly private array $routesData)
    {
    }

    /**
     * Performs standardization of the array of data for routes.
     *
     * Производит стандартизацию массива данных для маршрутов.
     */
    public function update(): self
    {
        $this->routesDataByMethod();

        return $this;
    }

    /**
     * Returns routes sorted by HTTP methods.
     *
     * Возвращает рассортированные по HTTP-методам маршруты.
     */
    public function getRoutesByMethod(): array
    {
        return $this->routesByMethod;
    }

    /**
     * Returns general information on routes.
     *
     * Возвращает общую информацию по маршрутам.
     */
    public function getRoutesInfo(): array
    {
        return $this->routesInfo;
    }

    /**
     * Returns a compact list of routes sorted by method.
     *
     * Возвращает список маршрутов в компактном виде рассортированный по методам.
     */
    public function getRoutesList(): array
    {
        return $this->routesList;
    }

    /**
     * Returns mapped routes sorted by HTTP methods.
     *
     * Возвращает рассортированные по HTTP-методам преобразованные маршруты.
     */
    private function routesDataByMethod(): void
    {
        $this->routesInfo = [
            'index_page' => 0,
            'index_page_name' => '',
            'has_dynamic_rules' => 0,
            'has_where' => 0,
            'has_protect' => 0,
            'no_session' => 0,
            'all_methods' => [],
            'has_modules' => 0,
            'has_pages' => 0,
            'has_plain' => 0,
        ];
        $this->routesList = [];
        $this->routesByMethod = [];

        foreach ($this->routesData as $key => $route) {
            $address = $this->createAddress($route);
            $route['full-address'] = $address;

            if (\str_contains($address, '?') || \str_contains($address, '{')) {
                $this->routesInfo['has_dynamic_rules'] = 1;
            }

            $httpMethods = $route['types'];
            $isIndexPage = \in_array('GET', $httpMethods, true) && $route['data']['route'] === '/' && $this->routesInfo['index_page'] === 0;
            if ($isIndexPage) {
                $this->routesInfo['index_page'] = $key;
            }
            foreach ($httpMethods as $method) {
                $method = \strtolower($method);
                $this->routesByMethod[$method][$key] = $route;
                if (\in_array($method, $this->routesInfo['has_methods'] ?? [], true)) {
                    $this->routesInfo['has_methods'][] = $method;
                }
                $this->routesList[$method][] = $this->createRouteRequest($address, $route, $key);
            }
            foreach ($route['actions'] ?? [] as $action) {
                if ($action['method'] === StandardRoute::PROTECT_TYPE) {
                    $this->routesInfo['has_protect'] = 1;
                }
                if ($action['method'] === StandardRoute::NO_DEBUG_TYPE) {
                    $this->routesInfo['no_debug'] = 1;
                }
                if ($action['method'] === StandardRoute::PLAIN_TYPE) {
                    $this->routesInfo['has_plain'] = 1;
                }
                if ($action['method'] === StandardRoute::WHERE_TYPE) {
                    $this->routesInfo['has_where'] = 1;
                }
                if ($action['method'] === StandardRoute::MODULE_TYPE) {
                    $this->routesInfo['has_modules'] = 1;
                }
                if ($action['method'] === StandardRoute::PAGE_TYPE) {
                    $this->routesInfo['has_pages'] = 1;
                }
                if ($action['method'] === StandardRoute::DOMAIN_TYPE && $route['data']['route'] === '/') {
                    $this->routesInfo['index_page'] = 0;
                }
                if ($action['method'] === StandardRoute::NAME_TYPE && $isIndexPage) {
                    $this->routesInfo['index_page_name'] = $action['name'];
                }
            }
        }
    }

    /**
     * Extract and standardize route address.
     *
     * Извлечение и стандартизация адреса маршрута.
     */
    private function createAddress(array $route): string
    {
        $list = [];
        $base = \trim(\preg_replace('|([/]+)|s', '/', $route['data']['route']), '/');
        foreach ($route['actions'] ?? [] as $action) {
            if ($action['method'] === StandardRoute::PREFIX_TYPE) {
                $list[] = \trim(\preg_replace('|([/]+)|s', '/', $action['prefix']), '/');
            }
        }

        return $this->withIndex(\implode('/', \array_merge($list, [$base])));
    }

    /**
     * Retrieve data to identify a method request.
     * a - is the full address of the route, combined with prefixes.
     * k - serial number of the route in the general list (identifier).
     * w - a list of where() conditions for the route in an array.
     * d - is there wildcard data in the route.
     * v - route address length can be variable.
     * f - is the first part of the address.
     * s - whether the address consists of only one part.
     * n - whether the first part of the address is dynamic.
     * c - whether the route is a fallback type.
     * m - there is variation in the route through `...`.
     * i - route name.
     * h - domain data.
     * p - the route is protected.
     * b - simple content.
     * u - debug panel is disabled.
     *
     * Извлечение данных для идентификации запроса метода.
     * a - полный адрес маршрута, собранный вместе с префиксами.
     * k - порядковый номер маршрута в общем списке (идентификатор).
     * w - перечень условий where() для маршрута в массиве.
     * d - есть ли подстановочные данные в маршруте.
     * v - длина адреса маршрута может быть непостоянной.
     * f - первая часть адреса.
     * s - состоит ли адрес только из одной части.
     * n - является ли первая часть адреса динамической.
     * c - является ли маршрут типом fallback.
     * m - в маршруте присутствует вариативность через `...`.
     * i - имя маршрута.
     * h - данные домена.
     * p - маршрут защищён.
     * b - простое содержимое.
     * u - отключена отладочная панель.
     */
    private function createRouteRequest(string $address, array $route, int $key): array
    {
        $address !== '/' and $address = \rtrim($address, '/');
        $result = ['a' => $address, 'k' => $key];
        $domain = [];
        $result['f'] = \str_contains($address, '/') ? $this->withIndex(\strstr($address, '/', true)) : $address;

        if (!empty($route['actions'])) {
            foreach ($route['actions'] as $action) {
                if ($action['method'] === StandardRoute::WHERE_TYPE) {
                    $result['w'] = \array_merge($result['w'] ?? [], $action['data']['rules'] ?? []);
                }
                if ($action['method'] === StandardRoute::NAME_TYPE) {
                    $result['i'] = $action['name'];
                }
                if ($action['method'] === StandardRoute::DOMAIN_TYPE) {
                    $domain[$action['level']] = \array_merge($domain[$action['level']] ?? [], $action['name']);
                }
                if ($action['method'] === StandardRoute::PROTECT_TYPE) {
                    // Counts only on the last assigned.
                    // Считается только по последнему назначенному.
                    if (!empty($action['data']['rules'])) {
                        $result['p'] = $action['data']['rules'];
                    }
                }
                if ($action['method'] === StandardRoute::NO_DEBUG_TYPE) {
                    $result['u'] = 1;
                }
                if ($action['method'] === StandardRoute::PLAIN_TYPE) {
                    // Counts only on the last assigned.
                    // Считается только по последнему назначенному.
                    if (isset($action['data']['on'])) {
                        $result['b'] = (int)$action['data']['on'];
                    }
                }
            }
        }
        if (\str_contains($address, '{')) {
            $result['d'] = 1;
        }
        if (\str_contains($address, '?')) {
            $result['v'] = 1;
        }
        if (\str_contains($address, '...')) {
            $result['m'] = 1;
        }
        if ($result['f'] === $address) {
            $result['s'] = 1;
        }
        if (\str_contains($result['f'], '{') || \str_contains($result['f'], '?')) {
            $result['n'] = 1;
        }
        if ($route['name'] === StandardRoute::FALLBACK_SUBTYPE) {
            $result['c'] = 1;
        }
        if ($domain) {
            $result['h'] = $domain;
        }

        return $result;
    }

    /**
     * Convert to an index page if the address contains no parts.
     *
     * Преобразование в индексную страницу если адрес не содержит частей.
     */
    private function withIndex(string $address): string
    {
        return $address !== '' ? $address : '/';
    }

}
