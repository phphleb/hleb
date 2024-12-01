<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\Helpers\RouteHelper;
use Hleb\Main\Console\Colorizer;
use JsonException;

/**
 * @internal
 */
final class RouteList
{
    /**
     * Returns a list of routes from the cache.
     *
     * Возвращает список маршрутов из кеша.
     */
    public function run(): string
    {
        $error = 'Route cache not found. Use `php console --routes-upd` to generate the cache.' . PHP_EOL;
        $routes = (new RouteHelper())->getRawCachedData();
        if (!$routes) {
            return $error;
        }
        $result = '';
        foreach ($routes as $key => &$values) {
            if ($key === 'head') {
                unset($routes[$key]);
            }
            foreach ($values as &$value) {
                unset($value['k']);
                $value['data'] = \json_encode($value);
            }
        }
        unset($values);

        $routes = $this->sortNestedArrays($routes);
        $color = new Colorizer();

        foreach ($routes as $key => $values) {
            $result .= $color->yellow(\strtoupper($key)) . PHP_EOL;
            foreach ($values as $v) {
                try {
                    $domainData = \json_encode($v['h'] ?? [], JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    $domainData = 'undefined [' . $e->getMessage() . ']';
                }
                $route = $v['a'];
                $name = isset($v['i']) ? ' (' . $v['i'] . ')' : '';
                $protected = !empty($v['p']) ? ' [protected]' : '';
                $domain = isset($v['h']) ? ' domain:' . $domainData : '';
                $result .= '  ' . $route . $name . $protected . $domain . PHP_EOL;
            }
        }
        return $result;
    }

    function sortNestedArrays($origin): array
    {
        $result = [];
        $search = [];

        foreach ($origin as $methodData) {
            foreach ($methodData as $nestedArray) {
                $matches = [];
                $keys = [];
                $data = $nestedArray['data'];

                foreach ($origin as $key => $value) {
                    foreach ($value as $v) {
                        if (\in_array($v['data'], $search)) {
                            continue;
                        }
                        if ($v['data'] === $data) {
                            $matches[] = $v;
                            $keys[] = $key;
                        }
                    }

                }
                $search[] = $data;
                if ($matches) {
                    $tag = \implode(', ', \array_unique($keys));
                    if (isset($result[$tag])) {
                        $result[$tag] = \array_merge($result[$tag], $matches);
                    } else {
                        $result[$tag] = $matches;
                    }
                }
            }
        }
        foreach ($result as &$item) {
            $search = [];
            foreach ($item as $key => $value) {
                if (!\in_array($value['data'], $search)) {
                    $search[] = $value['data'];
                } else {
                    unset($item[$key]);
                }
            }
        }

        return $result;
    }
}
