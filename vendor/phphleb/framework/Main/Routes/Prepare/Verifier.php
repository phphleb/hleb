<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Prepare;

use Hleb\Constructor\Data\DynamicParams;
use Hleb\AsyncRouteException;
use Hleb\RouteColoredException;
use Hleb\Helpers\RangeChecker;
use Hleb\HlebBootstrap;
use Hleb\Main\Routes\StandardRoute;

/**
 * @internal
 */
final readonly class Verifier
{
    public function __construct(private array $routes)
    {
    }

    /**
     * Checking data for compliance with the rules for compiling routes.
     *
     * Проверка данных на соответствие правилам составления маршрутов.
     *
     * @throws RouteColoredException
     */
    public function isCheckedOrError(): true
    {
        $fallbacks = [];
        $nonDuplicateNames = [];
        foreach ($this->routes as $httpMethod => $routes) {
            foreach ($routes as $route) {
                $searchTypes = HlebBootstrap::HTTP_TYPES;
                $all = \strtolower(\implode(', ', $searchTypes));
                $this->checkAddress($route['full-address']);
                $this->checkHttpMethods($route['types'], $all);
                $tags = [];
                if (\str_contains($route['full-address'], '{')) {
                    \preg_match_all('/[{]+}/i', $route['full-address'], $tags);
                    $tags = \array_map(static function ($v) {
                        return \trim($v, '?}{');
                    }, $tags[0] ?? []);
                }

                if (\str_contains($route['full-address'], '...')) {
                    if (\str_contains($route['full-address'], '{') || \str_contains($route['full-address'], '?')) {
                        $this->error(AsyncRouteException::HL18_ERROR);
                    }
                    $parts = \explode('/', $route['full-address']);
                    $endPart = \array_pop($parts);
                    $checkVariableRoute = (new RangeChecker(rtrim($endPart, '.')))->validation(true);
                    if (\substr_count($route['full-address'], '...') !== 1 ||
                        !\str_starts_with($endPart, '...') ||
                        $checkVariableRoute
                    ) {
                        $this->error(AsyncRouteException::HL19_ERROR);
                    }
                }

                if ($route['method'] === StandardRoute::ALIAS_SUBTYPE) {
                    $this->error(AsyncRouteException::HL41_ERROR, ['origin' => $route['name'], 'target' => $route['new-name']]);
                }

                $realTagKeys = [];
                $controllerCount = 0;
                $nameCount = 0;
                $pageCount = 0;
                $protectCount = 0;
                $plainCount = 0;
                $noDebugCount = 0;
                foreach ($route['actions'] ?? [] as $action) {
                    $method = $action['method'];
                    if (\in_array($method, [
                        StandardRoute::CONTROLLER_TYPE,
                        StandardRoute::MODULE_TYPE,
                        StandardRoute::PAGE_TYPE,
                        StandardRoute::REDIRECT_TYPE,
                    ])) {
                        if ($route['data']['view'] !== null) {
                            // Если есть второй параметр в методе и контроллер.
                            $this->error(AsyncRouteException::HL04_ERROR, ['method' => $route['name'], 'controller' => $method]);
                        }
                        $controllerCount++;
                    }
                    if ($route['name'] !== StandardRoute::OPTIONS_SUBTYPE && \count($route['types']) < 2) {
                        $this->error(AsyncRouteException::HL13_ERROR, ['types' => $all]);
                    }
                    if ($method === StandardRoute::PAGE_TYPE) {
                        $pageCount++;
                    }
                    if ($method === StandardRoute::PROTECT_TYPE) {
                        $protectCount++;
                    }
                    if ($method === StandardRoute::NO_DEBUG_TYPE) {
                        $noDebugCount++;
                    }
                    if ($method === StandardRoute::PLAIN_TYPE && $action['data']['on']) {
                        $plainCount++;
                    }
                    if ($method === StandardRoute::MODULE_TYPE) {
                        $this->checkModule($action);
                    }
                    if ($method === StandardRoute::REDIRECT_TYPE) {
                        $status = $action['status'];
                        if ($status < 300 || $status > 308) {
                            $this->error(AsyncRouteException::HL39_ERROR);
                        }
                    }
                    if ($method === StandardRoute::WHERE_TYPE) {
                        $rules = $route['data']['rules'] ?? [];
                        foreach ($rules as $key => $rule) {
                            if (!\is_string($key)) {
                                $this->error(AsyncRouteException::HL05_ERROR);
                            }
                            if (\in_array($key, $realTagKeys, true) || !\in_array($key, $tags, true)) {
                                $this->error(AsyncRouteException::HL06_ERROR);
                            }
                            // Check if the passed regular expression is valid.
                            // Проверка на валидность переданного регулярного выражения.
                            if (\preg_match($rule, '') === false && \preg_match("/$rule/", '') === false) {
                                $this->error(AsyncRouteException::HL07_ERROR);
                            }
                            $realTagKeys[] = $key;
                        }
                    }
                    if ($action['method'] === StandardRoute::NAME_TYPE) {
                        $errorName = $action['name'] !== '' ? $action['name'] : 'undefined';

                        if ($action['name'] && $route['method'] === StandardRoute::ADD_TYPE) {
                            if (\in_array($action['name'], $nonDuplicateNames[$httpMethod] ?? [], true)) {
                                $this->error(AsyncRouteException::HL27_ERROR, ['name' =>  $errorName]);
                            }
                            $nonDuplicateNames[$httpMethod][] = $action['name'];
                        }

                        if ($nameCount > 0) {
                            $this->error(AsyncRouteException::HL28_ERROR, ['name' =>  $errorName]);
                        }
                        if (!\preg_match('/^[a-z0-9\.\-]+$/i', $action['name'])) {
                            $this->error(AsyncRouteException::HL20_ERROR, ['method' => $route['name'], 'name' =>  $errorName]);
                        }
                        $nameCount++;
                    }
                }
                if ($pageCount && !$nameCount) {
                    $this->error(AsyncRouteException::HL29_ERROR);
                }
                if ($protectCount && $plainCount) {
                    $this->error(AsyncRouteException::HL34_ERROR);
                }
                if (\count($realTagKeys) > \count($tags)) {
                    $this->error(AsyncRouteException::HL05_ERROR);
                }
                if ($controllerCount > 1) {
                    $this->error(AsyncRouteException::HL11_ERROR, ['method' => $route['name']]);
                }
                if (!$controllerCount && $route['data']['view'] === null && $route['name'] !== StandardRoute::FALLBACK_SUBTYPE) {
                    $this->error(AsyncRouteException::HL12_ERROR, ['method' => $route['name']]);
                }
                if ($route['name'] === StandardRoute::FALLBACK_SUBTYPE) {
                    $fallbacks[] = $route;
                }
                if ($route['method'] === StandardRoute::DOMAIN_TYPE) {
                    $this->checkDomain($route);
                }
                if ($noDebugCount > 1) {
                    $this->error(AsyncRouteException::HL40_ERROR);
                }
            }
        }

        $this->checkFallbacks($fallbacks);

        return true;
    }

    /**
     * @throws RouteColoredException
     */
    private function checkAddress(string $address): void
    {
        if (\str_contains($address, '{') || \str_contains($address, '}')) {
            if (\substr_count($address, '{') !== \substr_count($address, '}')) {
                $this->error(AsyncRouteException::HL08_ERROR);
            }
            $openParts = \explode('{', $address);
            foreach ($openParts as $part) {
                if (\substr_count($part, '}') > 1) {
                    $this->error(AsyncRouteException::HL09_ERROR);
                }
            }
            $closeParts = \explode('}', $address);
            foreach ($closeParts as $part) {
                if (\substr_count($part, '{') > 1) {
                    $this->error(AsyncRouteException::HL09_ERROR);
                }
            }
            if (\str_contains($address, '?') &&
                (\substr_count($address, '?') > 1 || !\str_ends_with(\rtrim($address, '/}'), '?'))
            ) {
                $this->error(AsyncRouteException::HL10_ERROR);
            }
            $uriParts = \explode('/', trim($address, '/'));
            foreach($uriParts as $part) {
                if (\str_contains($part, '@') && (!\str_starts_with($part, '@') || \substr_count($part, '@') > 1)) {
                    $this->error(AsyncRouteException::HL33_ERROR);
                }
                $part = \ltrim($part, '@');
                if (\str_contains($part, '{')) {
                    if (!\str_starts_with($part, '{') || !\str_contains($part, '}')) {
                        $this->error(AsyncRouteException::HL32_ERROR, ['route' => $address, 'part' => $part]);
                    }
                }
                if (\str_contains($part, '}')) {
                    if (!\str_ends_with($part, '}') || !\str_contains($part, '{')) {
                        $this->error(AsyncRouteException::HL32_ERROR, ['route' => $address, 'part' => $part]);
                    }
                }

                if (\str_contains($part, '{') &&
                    (
                        (\substr_count($part, '{') > 1 || \substr_count($part, '}') > 1) ||
                        (\str_contains($part, '?') && (\substr_count($part, '?') > 1 || !\str_ends_with(\rtrim($part, '}'), '?')))
                    )
                ) {
                    $this->error(AsyncRouteException::HL32_ERROR, ['route' => $address, 'part' => $part]);
                }
            }

        } else if (\str_contains($address, '?') && !\str_ends_with(\trim($address, '/'), '?')) {
            $this->error(AsyncRouteException::HL10_ERROR);
        }
    }

    /**
     * @throws RouteColoredException
     */
    private function checkHttpMethods(array $types, string $all): void
    {
        foreach ($types as $type) {
            if (!\in_array($type, HlebBootstrap::HTTP_TYPES, true)) {
                $this->error(AsyncRouteException::HL13_ERROR, ['types' => $all]);
            }
        }
    }

    /**
     * @throws RouteColoredException
     */
    private function checkFallbacks(array $routes): void
    {
        $types = [];
        foreach ($routes as $route) {
            unset($route['types'][\array_search('options', $route['types'], true)]);
            if (!\count($route['types']) || \array_intersect($types, $route['types'])) {
                $this->error(AsyncRouteException::HL14_ERROR);
            }
        }
    }

    /**
     * @throws RouteColoredException
     */
    private function checkModule(array $action): void
    {
        $name = $action['name'];
        if (!$name || !preg_match('/^[a-z][a-z0-9\/\-]*[a-z0-9]$/', $name)) {
            $this->error(AsyncRouteException::HL36_ERROR, ['name' => $name]);
        }
    }

    /**
     * @throws RouteColoredException
     */
    private function checkDomain(array $route): void
    {
       foreach($route['name'] as $name) {
           if (!\str_starts_with($name, '/') && !\preg_match('/^[a-z0-9\-]$/', $name)) {
               $this->error(AsyncRouteException::HL22_ERROR, ['name' => $name]);
           } else if (\preg_match($name, '') === false) {
               $this->error(AsyncRouteException::HL23_ERROR, ['name' => $name]);
           }
       }
       if ($route['level'] < 0) {
           $this->error(AsyncRouteException::HL24_ERROR);
       }

    }

    /**
     * @throws RouteColoredException
     */
    private function error(string $tag, array $replace = []): void
    {
        throw (new RouteColoredException($tag))->complete(DynamicParams::isDebug(), $replace);
    }

}
