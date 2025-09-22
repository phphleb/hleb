<?php

/*declare(strict_types=1);*/

namespace Hleb\Main;

/**
 * Helper class for finding the final controller and method in a route.
 *
 * Вспомогательный класс для поиска конечного контроллера и метода в маршруте.
 *
 * @internal
 */
final class RouteExtractor
{
    /**
     * A pattern for replacing a controller or method
     * with the current request method.
     *
     * Паттерн для подмены в контроллере или методе
     * на текущий метод запроса.
     */
    private const PATTERN = '[verb]';

    /**
     * Substitution of query variables in a dynamic class and method.
     *
     * Подстановка переменных запроса в динамический класс и метод.
     *
     * @param string $controllerName - raw controller from route.
     *                               - необработанный контроллер из маршрута.
     *
     * @param string $methodName - raw method from route.
     *                           - необработанный метод из маршрута.
     *
     * @param int $countTags - total number of inserted '<' tags in the class and method.
     *                       - кол-во подставляемых тегов '<' всего в классе и методе.
     *
     * @param array $params - array for replacing tags.
     *                      - массив для замены тегов.
     */
    public function getCalledClassAndMethod(string $controllerName, string $methodName, int $countTags, array $params): array
    {
        foreach ($params as $key => $value) {
            if ($value === null) {
                continue;
            }
            $tag = '<' . $key . '>';
            $reformatValue = $this->reformatValue((string)$value);
            if ($reformatValue === false) {
                return [$controllerName, $methodName];
            }
            if (\str_starts_with($methodName, $tag)) {
                $methodName = \str_replace($tag, \lcfirst($reformatValue), $methodName);
                if ($countTags === 1) {
                    return [$controllerName, $methodName];
                }
            }
            if (\str_contains($methodName, $tag)) {
                $methodName = \str_replace($tag, \lcfirst($reformatValue), $methodName);
                if ($countTags === 1) {
                    return [$controllerName, $methodName];
                }
            }
            if (\str_contains($controllerName, $tag)) {
                $controllerName = \str_replace($tag, $reformatValue, $controllerName);
            }
        }

        return [$controllerName, $methodName];
    }

    /**
     * Replacing the pattern with the value
     * of the current HTTP method.
     * For example, with a POST request and a route with
     * [verb]Controller@[verb]Method[verb]
     * `PostController` controller will be searched
     * with the `postMethodPost` method.
     *
     * Замена паттерна на значение текущего HTTP метода.
     * Например, при POST-запросе и наличии маршрута с
     * [verb]Controller@[verb]Method[verb]
     * будет произведён поиск контроллера `PostController`
     * с методом `postMethodPost`.
     */
    public function replacePattern(string $controller, string $method, string $insertMethod): array
    {
        $insert = \ucfirst(\strtolower($insertMethod));

        if (\str_contains($controller, self::PATTERN)) {
            $controller = \str_replace(self::PATTERN, $insert, $controller);
        }
        if (\str_contains($method, self::PATTERN)) {
            if (\str_starts_with($method, self::PATTERN)) {
                $insert = \lcfirst($insert);
            }
            $method = \str_replace(self::PATTERN, $insert, $method);
        }

        return [$controller, $method];
    }

    /**
     * Converts the value to camelCase name.
     *
     * Преобразует значение в camelCase наименование.
     */
    private function reformatValue(string $value): false|string
    {
        $parts = \explode('-', $value);
        $result = '';
        foreach ($parts as $part) {
            if ($part === '') {
                return false;
            }
            $result .= \ucfirst($part);
        }
        return $result;
    }

}
