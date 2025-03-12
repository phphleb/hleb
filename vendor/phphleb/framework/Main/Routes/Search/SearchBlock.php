<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Search;

use Hleb\Helpers\RangeChecker;
use Hleb\HttpMethods\External\SystemRequest;

/**
 * @internal
 */
final class SearchBlock
{
    private array $data = [];

    private int $fallback = 0;

    private array $protected = [];

    private ?string $routeName = null;

    private ?bool $isPlain = null;

    private ?bool $isNoDebug = null;

    private bool $isCompleteAddress = true;

    public function __construct(
        readonly private SystemRequest $request,
        readonly private array         $list
    )
    {
    }

    /**
     * Returns dynamic route data when matching parts in `/{param}/` as 'param' => `value`.
     *
     * Возвращает данные динамического маршрута при совпадении частей в `/{param}/` как 'param' => `value`.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns the name of the matched route if present.
     *
     * Возвращает название совпавшего маршрута если оно присутствует.
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * Returns the block number assigned to intercept all non-matching routes (404).
     *
     * Возвращает номер блока назначенного на перехват всех не совпавших маршрутов (404).
     */
    public function getFallback(): int
    {
        return $this->fallback;
    }

    /**
     * Returns the security types of the route.
     *
     * Возвращает типы защищенности маршрута.
     */
    public function protected(): array
    {
        return $this->protected;
    }

    /**
     * Indicates simple or standard content delivery.
     *
     * Указывает на простую отдачу контента или стандартную.
     */
    public function getIsPlain(): null|bool
    {
        return $this->isPlain;
    }

    /**
     * Returns the flag for forcing the debug panel to be disabled.
     *
     * Возвращает признак принудительного отключения отладочной панели.
     */
    public function getIsNoDebug(): null|bool
    {
        return $this->isNoDebug;
    }

    /**
     * Returns an indication whether the found route has or does not have a trailing part.
     *
     * Возвращает признак того, что найденный маршрут имеет или не имеет конечную часть.
     */
    public function getIsCompleteAddress(): bool
    {
        return $this->isCompleteAddress;
    }

    /**
     * Parsing the minified data and checking for a match with the incoming request.
     * a - full route address, assembled together with prefixes.
     * k - serial number of the route in the general list (identifier).
     * w - a list of where() conditions for the route in an array.
     * d - is there wildcard data in the route.
     * v - the length of the route address can be variable.
     * f - is the first part of the address.
     * s - whether the address consists of only one part.
     * n - whether the first part of the address is dynamic.
     * c - whether the route is a fallback type.
     * m - there is variation in the route through `...`.
     * i - route name.
     * h - domain data.
     * p - the route is protected.
     * b - simple content.
     *
     * Разбор минимизированных данных и проверка на совпадение с входящим запросом.
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
     */
    public function getNumber(): int|false
    {
        $this->data = [];
        $this->protected = [];
        $address = $this->withIndex(\trim($this->request->getUri()->getPath(), '/'));
        $firstPart = \str_contains($address, '/') ? $this->withIndex(strstr($address, '/', true)) : $address;
        $this->fallback = 0;
        $fallbackNumber = 0;
        $addressParts = [];

        foreach ($this->list as $key => $route) {
            $this->data = [];
            $this->protected = [];

            if (!empty($route['h']) && !$this->domainMatching($route['h'])) {
                continue;
            }
            if (!empty($route['c'])) {
                $this->fallback = $route['k'];
                $fallbackNumber = $key;
                continue;
            }
            if (!empty($route['m'])) {
                $addressParts or $addressParts = $this->addressSeparation($address);
                $routeParts = $this->addressSeparation($route['a']);
                // Check if the address matches the variant route.
                // Проверяется, подходит ли адрес для вариативного маршрута.
                if ($this->checkVariableRoute($addressParts, $routeParts)) {
                    $this->setData($route, $this->data);
                    return $route['k'];
                }
            }
            // Direct match.
            // Прямое совпадение.
            if (\trim($route['a'], '?') === $address) {
                $this->setData($route);
                return $route['k'];
            }

            // If the route is not dynamic or its first part is not changeable.
            // Если маршрут не динамический или его первая часть не изменяемая.
            if ((empty($route['d']) && empty($route['v'])) || empty($route['n'])) {
                // Mismatch of the first part of the route.
                // Несовпадение первой части маршрута.
                if (isset($route['f']) && $firstPart !== $route['f']) {
                    continue;
                }
            }

            $addressParts or $addressParts = $this->addressSeparation($address);

            // If the route is not dynamic, but with variable length.
            // Если маршрут не динамический, но с изменяемой длиной.
            if (empty($route['d']) && !empty($route['v']) && $address === $route['a'] . '/' . \end($addressParts)) {
                $this->setData($route);
                return $route['k'];
            }

            $routeParts = $this->addressSeparation($route['a']);
            $countRouteParts = \count($routeParts);
            $countAddressParts = \count($addressParts);

            $this->isCompleteAddress = $countRouteParts === $countAddressParts;

            // If the route has a fixed length, but the number of parts is not the same.
            // Если маршрут неизменяемой длины, но количество частей не одинаково.
            if (empty($route['v']) && !$this->isCompleteAddress) {
                continue;
            }

            // If the route is variable length, but the number of parts is not in the likely range.
            // Если маршрут изменяемой длины, но количество частей не входит в вероятный диапазон.
            if (!empty($route['v']) && (!$this->isCompleteAddress && $countRouteParts !== $countAddressParts + 1)) {
                continue;
            }

            $data = [];
            $search = false;
            foreach ($routeParts as $index => $part) {
                $param = \trim($part, '{?}');
                // If there is no match for part of the address or it is optional.
                // Если нет совпадения по части адреса или он необязателен.
                if (!isset($addressParts[$index])) {
                    $search = \str_contains($part, '?');
                    if ($search && \str_contains($part, '{')) {
                        // If the dynamic parameter is optional and absent, then its value is null.
                        // Если динамический параметр необязателен и отсутствует, то значение его равно null.
                        $data[$param] = null;
                    }
                    break;
                }
                $addressPart = $addressParts[$index];

                // Handler for the initial `@` character in the address.
                // Обработчик начального символа `@` в адресе.
                if (\str_starts_with($part, '@')) {
                    if (!\str_starts_with($addressPart, '@')) {
                        $search = false;
                        break;
                    }
                    $addressPart = \substr($addressPart, 1);
                    $part = \substr($part, 1);
                }

                if (\str_contains($part, '{')) {
                    if (!empty($route['w'][$param])) {
                        if (\str_starts_with($route['w'][$param], '/')) {
                            if (!\preg_match($route['w'][$param], $addressPart)) {
                                $search = false;
                                break;
                            }
                        } else if (!\preg_match('/^' . $route['w'][$param] . '$/u', $addressPart)) {
                            $search = false;
                            break;
                        }
                    }

                    // Gather dynamic address matches.
                    // Сбор совпадений с динамическим адресом.
                    $data[$param] = $addressPart;
                } else if (\rtrim($part, '?') !== $addressPart) {
                    $search = false;
                    break;
                }
                $search = true;
            }
            if ($search) {
                $this->setData($route, $data);
                return $route['k'];
            }
        }

        if ($this->fallback) {
            $this->setData($this->list[$fallbackNumber]);
        }

        return $this->fallback ?: false;
    }

    /**
     * Assigning data for the matched route.
     *
     * Присвоение данных для совпавшего маршрута.
     */
    private function setData(array $route, array $data = []): void
    {
        $this->data = $this->updateData($data);
        $this->routeName = $route['i'] ?? null;
        if (!empty($route['p'])) {
            $this->protected = $route['p'];
        }
        if (isset($route['b'])) {
            $this->isPlain = (bool)$route['b'];
        }
        if (isset($route['u'])) {
            $this->isNoDebug = (bool)$route['u'];
        }
    }

    /**
     * Request data transformation.
     *
     * Преобразование данных запроса.
     */
    private function updateData(array $data): array
    {
        $result = [];
        foreach($data as $key => $value) {
            if (is_string($key)) {
                $key = \trim($key, '@{}');
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Breaks the address into parts.
     *
     * Разбивает адрес на части.
     */
    private function addressSeparation(string $address): array
    {
        if ($address === '/') {
            return [''];
        }
        return \explode('/', $address);
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

    /**
     * Checking parts of the route for the identified variability.
     *
     * Проверка частей маршрута на выявленную вариативность.
     */
    private function checkVariableRoute(array $addressParts, array $routeParts): bool
    {
        $exactPart = \ltrim(\array_pop($routeParts), '.');
        if (\count($routeParts) > \count($addressParts)) {
            return false;
        }
        $parts = \array_slice($addressParts, 0, \count($routeParts));
        if (\implode('/', $parts) !== \implode('/', $routeParts)) {
            return false;
        }
        $result = (new RangeChecker($exactPart))->check(\count($addressParts) - \count($routeParts));
        if ($result) {
            $this->data = $this->updateData(array_values(array_slice($addressParts, \count($routeParts))));
        }

        return $result;
    }

    /**
     * Checking for a match between the specified conditions for selecting a domain
     * and the current domain from the request.
     *
     * Проверка на совпадение указанных условий подбора домена и текущего домена из запроса.
     */
    private function domainMatching(array $data): bool
    {
        $domain = $this->request->getUri()->getHost();
        $parts = \array_reverse(explode('.', \strstr($domain, ':', true) ?: $domain));
        $max = \max(\array_keys($data));
        $countParts = \count($parts);
        if ($countParts < $max || ($countParts > $max && !\in_array('*', $data[$max], true))) {
            return false;
        }
        foreach ($data as $level => $rules) {
            $level = (int)$level - 1;
            $level < 0 and $level = 0;
            // Part of the domain from Request by level in conditions.
            // Часть домена из Request по уровню в условиях.
            $item = $parts[$level] ?? [];
            if ($item) {
                $isRegExp = false;
                // Check for a regular expression of the domain part.
                // Проверка на регулярное выражение части домена.
                foreach ($rules as $rule) {
                    if (\str_starts_with($rule, '/') && \preg_match($rule, $item)) {
                        $isRegExp = true;
                        break;
                    }
                }
                if ($isRegExp) {
                    continue;
                }
                if (!\in_array($item, $rules, true)) {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }
}
