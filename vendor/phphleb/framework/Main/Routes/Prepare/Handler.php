<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Prepare;

use Hleb\Constructor\Data\DynamicParams;
use Hleb\AsyncRouteException;
use Hleb\RouteColoredException;
use Hleb\Main\Routes\StandardRoute;
use Hleb\Route\Fallback;

/**
 * @internal
 */
final class Handler
{
    private array $rawData;

    public function __construct(array $rawData)
    {
        $this->rawData = $this->offset($rawData);
    }

    /**
     * Returns an array of sorted data by source route indexes.
     *
     * Возвращает массив сортированных данных по исходным индексам маршрутов.
     *
     * @throws RouteColoredException
     */
    public function sort(): array
    {
        $this->isolateFallback();

        return $this->sortRoutes();
    }

    /**
     * Parsing and processing basic data, only routes should remain.
     *
     * Разбор и обработка основных данных, должны остаться только маршруты.
     *
     * @throws RouteColoredException
     */
    private function sortRoutes(): array
    {
        $routes = [];
        $hasAlias = false;
        $this->checkGroups();
        foreach ($this->rawData as $key => $data) {
            $searchAlias = $data['method'] === StandardRoute::ALIAS_SUBTYPE;
            if ($data['method'] === StandardRoute::ADD_TYPE || $searchAlias) {
                $routes[$key] = $data;
                $routeGroups = $this->getGroupActions($key);
                $RouteReference = $this->getRouteReference($key);
                $routes[$key]['actions'] = array_merge($routeGroups, $RouteReference);
            }
            $searchAlias and $hasAlias = true;
         }
        if ($hasAlias) {
            foreach ($routes as $key => $route) {
                if ($route['method'] === StandardRoute::ALIAS_SUBTYPE) {
                    $searchName = $route['name'];
                    foreach ($routes as $item) {
                        foreach ($item['actions'] ?? [] as $keyAction => $action) {
                            if ($action['method'] === StandardRoute::NAME_TYPE && $action['name'] === $searchName) {
                                $new = $item;
                                $new['actions'][$keyAction]['name'] = $route['new-name'];
                                $new['data']['route'] = $route['data']['route'];
                                $actions = $route['actions'] ?? [];
                                foreach($new['actions'] ?? [] as $originAction) {
                                    $actions[] = $originAction;
                                }
                                $new['actions'] = $actions;
                                $routes[$key] = $new;
                                break 2;
                            }
                        }
                    }
                }
            }
        }
        return $routes;
    }

    /**
     * Moves the fallback() method data to the end of the route list.
     * Along with fallback, the methods assigned to it (name and controllers) are transferred.
     *
     * Перемещает данные метода fallback() в конец списка маршрутов.
     * Вместе с fallback переносятся присвоенные ему методы (название и контроллеры).
     *
     * @see Fallback - list of available methods.
     *               - список доступных методов.
     *
     * @see Route::fallback() - use of fallback.
     *                        - использование fallback.
     */
    private function isolateFallback(): void
    {
        $fallbacks = [];
        $search = false;

        foreach ($this->rawData as $key => $data) {
            $name = $data['name'] ?? null;
            $method = $data['method'] ?? null;

            if ($search) {
                if ($method === StandardRoute::CONTROLLER_TYPE ||
                    $method === StandardRoute::NAME_TYPE ||
                    $method === StandardRoute::MIDDLEWARE_TYPE
                ) {
                    $fallbacks[] = $data;
                    $this->rawData[$key] = null;
                    unset($this->rawData[$key]);
                } else {
                    $search = false;
                }
            }

            if ($method === StandardRoute::ADD_TYPE && $name === StandardRoute::FALLBACK_SUBTYPE) {
                $fallbacks[] = $data;
                $this->rawData[$key] = null;
                unset($this->rawData[$key]);
                $search = true;
            }
        }
        if ($fallbacks) {
            $this->rawData = \array_values($this->rawData);
            \array_unshift($this->rawData, ...\array_values($fallbacks));
            $this->rawData = $this->offset($this->rawData);
        }
    }

    /**
     * Returns a list of actions assigned to a specific route.
     *
     * Возвращает список действий закрепленных за конкретным маршрутом.
     */
    private function getRouteReference(int $key): array
    {
        $result = [];
        foreach ($this->rawData as $num => $method) {
            if ($num <= $key) {
                continue;
            }
            if ($method['method'] === StandardRoute::ADD_TYPE ||
                $method['method'] === StandardRoute::TO_GROUP_TYPE ||
                $method['method'] === StandardRoute::END_GROUP_TYPE ||
                $method['method'] === StandardRoute::ALIAS_SUBTYPE
            ) {
                break;
            }
            if (empty($method['from-group'])) {
                $result[] = $method;
            }
        }

        return $result;
    }

    /**
     * Checking the correct positioning of opening and closing methods for groups.
     *
     * Проверка правильности расположения открывающих и закрывающих методов для групп.
     *
     * @throws RouteColoredException
     */
    private function checkGroups(): void
    {
        $groups = $this->rawData;
        $start = 0;
        $end = 0;
        foreach ($groups as $key => $item) {
            if ($item['method'] === StandardRoute::TO_GROUP_TYPE) {
                $start++;
                continue;
            }
            if ($item['method'] === StandardRoute::END_GROUP_TYPE) {
                $end++;
                continue;
            }
            unset($groups[$key]);
        }
        if (!$start && !$end) {
            return;
        }
        if ($start !== $end) {
            // Error, the number of closed and open group tags does not match.
            // Ошибка, кол-во закрытых и открытых тегов групп не совпадает.
            $this->error(AsyncRouteException::HL02_ERROR);
        }
        foreach ($groups as $key => $item) {
            if ($item['method'] === StandardRoute::TO_GROUP_TYPE) {
                $search = 0;
                foreach ($groups as $num => $group) {
                    if ($num < $key) {
                        continue;
                    }
                    if ($group['method'] === StandardRoute::TO_GROUP_TYPE) {
                        $search++;
                    }
                    if ($group['method'] === StandardRoute::END_GROUP_TYPE) {
                        $search--;
                    }
                    if ($search === 0) {
                        break;
                    }
                }
                if ($search !== 0) {
                    // Error, no end tag for toGroup.
                    // Ошибка, нет завершающего тега для toGroup.
                    $this->error(AsyncRouteException::HL03_ERROR);
                }
            }
        }
    }

    /**
     * Enumeration of groups with the definition of the nesting level
     * of the route in group corresponding to it and the choice
     * of methods assigned to these groups.
     *
     * Перебор групп с определением уровня вложенности маршрута в
     * соответствующие ему группы и выбор методов, назначенных
     * этим группам.
     */
    public function getGroupActions(int $key): array
    {
        $items = $this->rawData;
        $groups = [];
        $num = 0;
        $result = [];

        foreach ($items as $k => $item) {
            if ($item['method'] !== StandardRoute::ALIAS_SUBTYPE) {
                if ($item['method'] === StandardRoute::TO_GROUP_TYPE) {
                    $num++;
                }
                // If the group at a certain level has ended, then it is not considered.
                // Если группа на определённом уровне закончилась, то она не считается.
                if ($item['method'] === StandardRoute::END_GROUP_TYPE) {
                    unset($groups[$num]);
                    $num--;
                    continue;
                }
                if (!empty($item['from-group'])) {
                    $groups[$num][] = $item;
                }
            }
            // Ends once all groups have been iterated up to the current route number.
            // Завершается, как только все группы перебраны до текущего номера маршрута.
            if ($k === $key) {
                break;
            }
        }
        foreach ($groups as $numGroup => $group) {
            if ($num >= $numGroup) {
                $prepare = [$result, $group];
                $result = \array_merge(...$prepare);
            }
        }
        return $result;
    }

    /**
     * It is necessary that the counting of routes be ordered and start from 1.
     *
     * Необходимо, чтобы отсчёт маршрутов был упорядочен и начинался с 1.
     */
    private function offset(array $data): array
    {
        $result = [];
        if (\array_key_exists(0, $data)) {
            foreach($data as $key => $value) {
                $result[$key + 1] = $value;
            }
        }
        return $result;
    }

    /**
     * @throws RouteColoredException
     */
    private function error(string $tag): void
    {
        throw (new RouteColoredException($tag))->complete(DynamicParams::isDebug());
    }
}
