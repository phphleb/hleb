<?php

declare(strict_types=1);

/*
 * Processing route map data with finding a suitable route.
 *
 * Обработка данных карты маршрутов с поиском подходящего роута.
 */

namespace Hleb\Constructor\Handlers;

final class URLHandler
{
    // Parse the array with routes.
    // Разбор массива с маршрутами.
    public function page(array $blocks) {
        $searchDomains = $blocks['domains'] ?? false;
        // Clearing global data.
        // Очистка глобальных данных.
        unset($blocks['domains']);
        unset($blocks['update']);
        unset($blocks['render']);
        unset($blocks['addresses']);

        $url = Request::getMainClearUrl();
        $blocks = $searchDomains ? $this->matchSubdomains($blocks) : $blocks;
        if (empty(count($blocks))) {
            // No suitable route was found for the current subdomain.
            // Подходящего роута по текущему поддомену не найдено.
            return false;
        }

        $blocks = $this->matchSearchType($blocks);
        if (empty(count($blocks))) {
            // No suitable route of type REQUEST_METHOD found.
            // Подходящего роута по типу REQUEST_METHOD не найдено.
            return false;
        }
        return $this->matchSearchAllPath($blocks, $url);
    }

    // Remove extra slashes.
    // Удаление лишних слешей.
    private function compoundUrl($strokes) {
        return preg_replace('#(/){2,}#', '/', implode('/', $strokes));
    }

    // Clears the trailing slash.
    // Очищает конечный слеш.
    private function trimEndSlash(string $stroke): string {
        return rtrim($stroke, '/');
    }

    // Find a method for subdomains.
    // Поиск метода для субдоменов.
    private function matchSubdomains($blocks) {
        $host = array_reverse(explode('.', hleb_get_host()));
        if ($host[0] === 'localhost') {
            array_unshift($host, '*');
        }
        $resultBlocks = [];
        foreach ($blocks as $key => $block) {
            $search = [];
            $actions = !empty($block['actions']) ? $block['actions'] : [];
            foreach ($actions as $k => $action) {
                if (!empty($action['domain'])) {
                    $domainPart = $host[intval($action['domain'][1]) - 1] ?? null;
                    if (!$action['domain'][2]) {
                        $validDomain = 0;
                        foreach ($action['domain'][0] as $domain) {
                            if ($domainPart === '*' || (is_null($domain) && is_null($domainPart)) ||
                                ($domainPart != null && $domain != null && strtolower($domainPart) == strtolower($domain))) {
                                $validDomain++;
                            }
                        }
                        $search[] = $validDomain > 0;
                    } else {
                        $validDomain = 0;
                        foreach ($action['domain'][0] as $domain) {
                            if ($domainPart === '*' || (is_null($domain) && is_null($domainPart))) {
                                $validDomain++;
                            } else if ($domainPart != null) {
                                preg_match('/^' . $domain . '$/u', strtolower($domainPart), $matches);
                                if (count($matches) && $matches[0] == strtolower($domainPart)) {
                                    $validDomain++;
                                }
                            }
                        }
                        $search[] = $validDomain > 0;
                    }
                }
            }
            if (count($search) == 0 || !in_array(false, $search)) $resultBlocks[] = $block;
        }
        return $resultBlocks;
    }

    // Sort the list of routes and filter by the appropriate type.
    // Сортировка списка роутов и отбор по подходящему типу.
    private function matchSearchType($blocks) {
        $realType = strtolower($_SERVER['REQUEST_METHOD']);
        if (!in_array($realType, HLEB_HTTP_TYPE_SUPPORT)) {
            if (!headers_sent()) {
                http_response_code (405);
                header('Allow: ' . strtoupper(implode(',', HLEB_HTTP_TYPE_SUPPORT)));
                header('Content-length: 0');
            }
            // End of script execution before starting the main project.
            hl_preliminary_exit();
        }
        $resultBlocks = [];
        $adminPanData = [];
        foreach ($blocks as $key => $block) {
            $type = [];
            $actions = !empty($block['actions']) ? $block['actions'] : [];
            foreach ($actions as $kt => $action) {
                // Determine the type of action.
                // Определяется тип действия.
                if (!empty($action['type'])) {
                    $action_types = $action['type'];
                    foreach ($action_types as $k => $action_type) {
                        $type[] = $action_type;
                    }
                }
                if (isset($action['adminPanController'])) {
                    $adminPanData[] = $block;
                }
            }
            if (count($type) === 0) {
                $type = !empty($block['type']) ? $block['type'] : [];
            }
            if (count($type) === 0) {
                $type = ['get'];
            }
            if (in_array($realType, $type) || $realType == 'options') {
                $resultBlocks[] = $block;
            }
        }
        foreach ($resultBlocks as &$resultBlock) {
            $resultBlock['_AdminPanelData'] = $adminPanData;
        }
        return $resultBlocks;
    }

    // Returns the matching route, or `false` if not found.
    // Возвращает совпавший роут или `false` если не найден.
    private function matchSearchAllPath(array $blocks, string $resultUrl) {
        $resultUrlParts = array_reverse(explode('/', $resultUrl));
        $url = $this->trimEndSlash($resultUrl);
        foreach ($blocks as $key => $block) {
            $result = $this->matchSearchPath($block, $url, $resultUrlParts);
            if ($result !== false) return $result;
        }
        return false;
    }

    // Returns data if the route matches the URL, otherwise `false`.
    // Возвращает данные, если роут подходит под URL, иначе `false`.
    private function matchSearchPath(array $block, string $resultUrl, array $resultUrlParts) {
        $url = '';
        $actions = $block['actions'] ?? [];
        $mat = [];
        foreach ($actions as $k => $action) {
            if (isset($action['prefix'])) {
                $url = $this->compoundUrl([$url, $action['prefix']]);
            } else if (isset($action['where']) && count($action['where'][0]) > 0) {
                foreach ($action['where'][0] as $key => $value) {
                    $mat[$key] = $value;
                }
            }
        }
        $originUrl = $this->compoundUrl([$url, $block['data_path'] ?? '']);
        $url = $this->trimEndSlash($originUrl);
        $urlParts = array_reverse(explode('/', $url));
        $resultShift = array_shift($urlParts);

        // /.../.../ or /.../...?/
        if ($resultUrl == trim($url, '?') ||
            (strlen($resultShift) && $resultShift[strlen($resultShift) - 1] === '?' && implode($resultUrlParts) === implode($urlParts))) {
            // Direct match.
            // Прямое совпадение.
            return $block;
        } else {
            // If there is variability in the route /{...}/, /{...?}/ or where(...).
            // Если есть вариативность в маршруте /{...}/, /{...?}/ или where(...).
            if (count($mat) > 0 || strpos($url, '{') !== false) {
                $generateRealUrls = explode('/', $resultUrl);
                $generateUrls = explode("/", $url);
                if (count($generateRealUrls) !== count($generateUrls) &&
                    !(($resultShift[strlen($resultShift) - 2] == '?' || $resultShift[strlen($resultShift) - 1] == '?') &&
                        count($generateRealUrls) + 1 == count($generateUrls))) {
                    // The length of the route does not match with the url.
                    // Не совпадает длина маршрута с url.
                    return false;
                }
                foreach ($generateUrls as $q => $generateUrl) {
                    $generateRealUrls[$q] = $generateRealUrls[$q] ?? '';
                    if (!empty($generateUrl)) {
                        if ($generateUrl[0] === '{' && $generateUrl[strlen($generateUrl) - 1] === '}') {
                            $exp = trim($generateUrl, '{?}');
                            if (isset($mat[$exp])) {
                                if (!(empty($generateRealUrls[$q]) && $generateUrl[strlen($generateUrl) - 2] === '?')) {
                                    preg_match('/^' . $mat[$exp] . '$/u', $generateRealUrls[$q], $matches);
                                    if (!isset($matches[0]) || $matches[0] != $generateRealUrls[$q]) {
                                        return false;
                                    }
                                }
                            }
                            Request::add($exp, $generateRealUrls[$q]);
                        } else {
                            // There is variation, but there are direct matches:
                            // Есть вариативность, но и есть прямые совпадения:
                            if (!(empty($generateRealUrls[$q]) && $generateUrl[strlen($generateUrl) - 1] === '?')) {
                                if (trim($generateUrl, "?") !== $generateRealUrls[$q]) {
                                    return false;
                                }
                            }
                        }
                    }
                } // foreach
                return $block;
            }
        }
        return false;
    }
}

