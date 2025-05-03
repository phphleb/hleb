<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Data;

use Hleb\Helpers\ArrayHelper;
use Hleb\Helpers\RangeChecker;
use Hleb\InvalidArgumentException;
use Hleb\Main\Routes\Prepare\Optimizer;

/**
 * @internal
 */
final class UrlManager
{
    /**
     * Returns the converted route to a URL.
     *
     * Возвращает конвертированный маршрут в URL.
     *
     * @param array $routes - result of getting data (new HL2pPreviewCache{method})->getData();
     *                      - результат получения данных (new HL2pPreviewCache{method})->getData();
     *
     *
     * @see Optimizer::createRouteRequest() - more about key abbreviations.
     *                                      - подробнее про сокращения ключей.
     */
    public function getUrlAddressByName(array $routes, string $name, array $replacements = [], ?bool $endPart = null): string
    {
        foreach ($routes as $route) {
            if (!isset($route['i']) || $route['i'] !== $name) {
                continue;
            }
            $address = \trim($route['a'], '/');
            if (!$address) {
                return '/';
            }
            if (\str_contains($address, '?') && ($endPart === false ||
                ($endPart === null && \count($replacements) === \substr_count($address, '{') - 1))
            ) {
                $parts = \explode('/', $address);
                if (\count($parts) === 1) {
                    return '/';
                }
                \array_pop($parts);
                $address = \implode('/', $parts);
            }
            if (isset($route['m'])) {
                $address = $this->getFromVariableRoute($address, $replacements);
            } else if (isset($route['d'])) {
                if (ArrayHelper::isAssoc($replacements)) {
                    $address = $this->getFromDynamicRouteAssoc($address, $replacements, $endPart, $route['w'] ?? null);
                } else {
                    $address = $this->getFromDynamicRoute($address, $replacements, $endPart, $route['w'] ?? null);
                }
            } else {
                $address = $this->getFromStandardRoute($address, $replacements);
            }
            if (!$address) {
                return '/';
            }

            DynamicParams::isEndingUrl() and $address .= '/';

            if (\str_contains($address, '{')) {
                \preg_match_all('/\{(.*?)\}/', $address, $matches);

                throw new InvalidArgumentException('Wrong number of replacement parts for URL: ' . \implode(',', $matches[1] ?? []) . " Route name `{$name}`");
            }

            return '/' . $address;
        }

        throw new InvalidArgumentException("No match for route by name `{$name}`");
    }

    /**
     * Returns the URL for the standard route.
     *
     * Возвращает URL для стандартного маршрута.
     */
    private function getFromStandardRoute(string $address, array $replacements): string
    {
        if ($replacements) {
            throw new InvalidArgumentException('It is not possible to make a replacement if there are no substitution options.');
        }
        return \str_replace('?', '', $address);
    }

    /**
     * Returns the URL for the variable route.
     *
     * Возвращает URL для вариативного маршрута.
     */
    private function getFromVariableRoute(string $address, array $replacements): string
    {
        $parts = \explode('/', $address);
        $end = \ltrim(\array_pop($parts), '.');
        $address = \implode('/', $parts);
        // The array to be substituted into the variable route must not be associative.
        // Массив для подстановки в вариативный маршрут не должен быть ассоциативным.
        if (ArrayHelper::isAssoc($replacements)) {
            throw new InvalidArgumentException('The replacement array must not be associative.');
        }
        // The number of wildcard parts does not meet the requirements of the route.
        // Количество подстановочных частей не отвечает требованиям маршрута.
        if (!(new RangeChecker($end))->check(\count($replacements))) {
            throw new InvalidArgumentException('Wrong number of replacement parts for URL.');
        }
        if (\count($replacements)) {
            $address .= '/' . \implode('/', $replacements);
        }

        return $address;
    }

    /**
     * Returns the generated address for a dynamic route with a named array of replacements.
     * In this case, the substitution array is associative.
     *
     * Возвращает сформированный адрес для динамического маршрута с именованным массивом замен.
     * В этом случае массив замещений ассоциативный.
     */
    private function getFromDynamicRouteAssoc(string $address, array $replacements, ?bool $endPart, ?array $condition): string
    {
        if (!$this->checkPartCount($address, $replacements, $endPart)) {
            $error = 'Wrong number of replacement parts for URL.';
            if (\str_contains($address, '?')) {
                $found = false;
                foreach ($replacements as $key => $value) {
                    if (\str_contains($address, "{{$key}?}")) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $error .= ' It is possible that the last part `endPart: false` is missing.';
                }
            }
            throw new InvalidArgumentException($error);
        }
        $address = \str_replace('?', '', $address);

        foreach ($replacements as $key => $param) {
            if ($condition && isset($condition[$key]) && !$this->checkWhereCondition($key, $condition, $param)) {
                throw new InvalidArgumentException('Parts of the URL did not pass the where() condition in the route.');
            }
            $address = \str_replace('{' . $key . '}', (string)$param, $address);
        }

        return $address;
    }

    /**
     * Returns the generated address for a dynamic route with a sequential list of replacements.
     * In this case, substitutions from the array are substituted sequentially.
     *
     * Возвращает сформированный адрес для динамического маршрута с последовательным списком замен.
     * В этом случае замещения из массива подставляются последовательно.
     */
    private function getFromDynamicRoute(string $address, array $replacements, ?bool $endPart, ?array $condition): string
    {
        $parts = \explode('/', $address);
        $keys = [];
        // If it is a variable length route.
        // Если это маршрут непостоянной длины.
        if (!$this->checkPartCount($address, $replacements, $endPart)) {
            $error = 'Wrong number of replacement parts for URL.';
            if (\str_contains($address, '?')) {
                $error .= ' It is possible that the last part `endPart: false` is missing.';
            }
            throw new InvalidArgumentException($error);
        }
        $isUnstable = \str_contains($address, '?');
        if ($isUnstable) {
            $address = \str_replace('?', '', $address);
        }

        foreach($parts as $part) {
            if (\str_contains($part, '{')) {
                $keys[] = \trim($part, '@{}?');
            }
        }

        foreach ($replacements as $param) {
            $key = \array_shift($keys);

            // Check for where() conditions for the route.
            // Проверка на условия where() для маршрута.
            if ($condition && isset($condition[$key]) && !$this->checkWhereCondition($key, $condition, $param)) {
                throw new InvalidArgumentException('Parts of the URL did not pass the where() condition in the route.');
            }
            $address = \str_replace('{' . $key . '}', $param, $address);
        }

        return $address;
    }

    /**
     * Checking for a match against where() conditions in the route.
     *
     * Проверка на сопоставление с условиями where() в маршруте.
     */
    private function checkWhereCondition(string $key, array $condition, string $param): bool
    {
        $reg = $condition[$key];
        if (!\str_starts_with($reg, '/')) {
            $reg = "/^$reg$/";
        }
        return (bool)\preg_match($reg, $param);
    }

    /**
     * Checking for enough replacement parts of the URL.
     *
     * Проверка достаточного количества замещаемых частей URL.
     */
    private function checkPartCount(string $address, array $replacements, ?bool $endPart): bool
    {
        $countTags = \substr_count($address, '{');
        $originCount = \count($replacements);

        if ($originCount !== $countTags && !\str_contains($address, '?')) {
            return false;
        }

        if ($endPart === null) {
            if ($originCount < $countTags - 1 || $originCount > $countTags) {
                return false;
            }
        } else if ($originCount < $countTags - (int)!$endPart) {
            return false;
        }

        return true;
    }
}
