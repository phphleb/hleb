<?php

declare(strict_types=1);

/*
 * Post-processing of all assigned routes.
 *
 * Завершающая системная обработка всех назначенных роутов.
 */

namespace Hleb\Constructor\Routes\Methods;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\MainRouteMethod;
use Hleb\Main\Errors\ErrorOutput;

class RouteMethodEnd extends MainRouteMethod
{
    protected $instance;

    protected $result = [];

    protected $mainParams = [];

    protected $mainValues = [];

    protected $render = [];

    protected $addresses = [];

    function __construct(StandardRoute $instance) {
        $this->methodTypeName = "end";
        $this->instance = $instance;
        $this->result = $this->instance->data();
        $this->result = self::createGroups();
        $this->checkController();
        $this->result["render"] = $this->render;
        $this->result["addresses"] = $this->addresses;
        $this->result["update"] = date("r") . " / " . rand();
        $this->result["domains"] = self::searchDomains();
        $this->result["multiple"] = self::searchMultiple();
        ErrorOutput::run();
    }
    
    // Returns the generated data of the current object.
    // Возвращает сформированные данные текущего объекта.
    public function data() {
        return $this->result;
    }

    // Search for the presence of domain settings.
    // Поиск присутствия настроек домена.
    private function searchDomains() {
        $blocks = $this->result;
        foreach ($blocks as $key => $block) {
            if (isset($block["actions"])) {
                $actions = $block["actions"];
                foreach ($actions as $action) {
                    if (isset($action["domain"]) && count($action["domain"])) return true;
                }
            }
        }
        return false;
    }

    // Finding the presence of a multiple route (with '...').
    // Поиск присутствия множественного марщрута (с '...').
    private function searchMultiple() {
        $blocks = $this->result;
        foreach ($blocks as $key => $block) {
            if (isset($block["data_path"])) {
                if (strpos($block["data_path"], '...') !== false) {
                    return true;
                }
            }
        }
        return false;
    }

    // Parse blocks nested in groups.
    // Разбор вложенных в группы блоков.
    private function createGroups() {
        $blocks = $this->result;
        $sampleBlocks = [];
        $closeBlocks = [];
        $originBlocks = [];
        $namedBlocks = [];
        $blocks = $this->globalMethodsAdd($blocks);
        
        foreach ($blocks as $key => $block) {
            if ($block['method_type_name'] == "getGroup") {
                $sampleBlocks[$key] = $block;
            } else if ($block['method_type_name'] == "endGroup") {
                $closeBlocks[$key] = $block;
                if (!empty($block['data_name'])) {
                    $namedBlocks[] = $block['data_name'];
                }
            } else if (in_array($block['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add"])) {
                $originBlocks[$key] = $block;
            } else if ($block['method_type_name'] == "renderMap") {
                $this->render[$block['data_name']] = $block['data_params'];
            }
        }
        if (count($sampleBlocks) !== count($closeBlocks)) {
            $this->errors[] = "HL001-ROUTE_ERROR: Error in method ->endGroup() ! " .
                "The number of open (" . count($sampleBlocks) . ") and closed (" . count($closeBlocks) . ") tags does not match. " .
                "~ Исключение в методе  ->endGroup() ! Количество открытых тегов (" . count($sampleBlocks) . ") и закрытых (" . count($closeBlocks) . ") не совпадает. ";
            ErrorOutput::add($this->errors);
        }

        $compilationBlocks = [];
        foreach ($sampleBlocks as $key => $sample_block) {
            $position = 1;
            $allBlocksCount = count($blocks);
            for ($i = $key + 1; $i < $allBlocksCount; $i++) {
                if ($blocks[$i]['method_type_name'] === "endGroup" && (!empty($blocks[$i]['data_name']) &&
                        $sample_block['data_name'] == $blocks[$i]['data_name'])) {
                    $compilationBlocks[$key]["actions"] = $this->calcEnvironment($blocks, $key, $i);
                    break;
                }
                if (empty($blocks[$i]['data_name']) && empty($sample_block['data_ name'])) {
                    if ($blocks[$i]['method_type_name'] === "getGroup") $position++;
                    if ($blocks[$i]['method_type_name'] === "endGroup") $position--;
                    if ($blocks[$i]['method_type_name'] === "endGroup" && $position == 0) {
                        $compilationBlocks[$key]["actions"] = $this->calcEnvironment($blocks, $key, $i);
                        break;
                    }
                }
                if (in_array($blocks[$i]['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add"])) {
                    $compilationBlocks[$key]['blocks'][] = $blocks[$i]['number'];
                }
            }
        }
        $finalList = [];

        foreach ($originBlocks as $key => $originBlock) {
            $properties = $this->calcEnvironment($blocks, $key, $key);
            $variableBlockActions = $originBlock['actions'] ?? [];
            $variablePropertiesPrevious = $properties['actions']["previous"] ?? [];
            $variablePropertiesFollowing = $properties['actions']["following"] ?? [];
            $originBlock['actions'] = $this->mainArrayMerge([$variablePropertiesPrevious, $variableBlockActions, $variablePropertiesFollowing]);
            $invertCompilationBlocks = array_reverse($compilationBlocks);

            foreach ($invertCompilationBlocks as $block) {
                if (!empty($block['blocks']) && in_array($originBlock["number"], $block['blocks'])) {
                    $blockActionsFollowing = $block['actions']['actions']["following"] ?? [];
                    $blockActionsPrevious = $block['actions']['actions']["previous"] ?? [];
                    $originBlock['actions'] = $this->mainArrayMerge([$blockActionsPrevious, $originBlock['actions'], $blockActionsFollowing]);
                }
            }
            $finalList[] = $originBlock;
        }
        return $this->allBlocksNormalizer($finalList);
    }

    // Sequential data change.
    // Последовательное изменение данных.
    private function globalMethodsAdd($blocks) {
        $this->checkAllMethods($blocks);
        $blocks = $this->globalMethodType($blocks);
        $blocks = $this->globalMethodProtect($blocks);
        return $blocks;
    }

    // Combine nested arrays of the same level.
    // Совмещение вложенных массивов одного уровня.
    private function mainArrayMerge(array $array) {
        $result = [];
        foreach ($array as $key => $arr) {
            if (is_array($arr)) {
                foreach ($arr as $a) {
                    $result[] = $a;
                }
            }
        }
        return $result;
    }

    // Collect data related to specific actions.
    // Сбор данных относящихся к конкретным действиям.
    private function calcEnvironment(array $blocks, int $start, int $end) {
        $template = [];
        for ($i = $start - 1; $i >= 0; $i--) {
            if (in_array($blocks[$i]['method_type_name'], ["before", "type", "prefix", "protect", "domain"])) {
                $mergeOnFirstPosition = $template["actions"]["previous"] ?? [];
                array_unshift($mergeOnFirstPosition, $blocks[$i]);
                $template["actions"]["previous"] = $mergeOnFirstPosition;
            } else if (in_array($blocks[$i]['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add", "getGroup", "endGroup"])) {
                break;
            }
        }
        for ($i = $end + 1; $i < count($blocks); $i++) {
            if (in_array($blocks[$i]['method_type_name'], ["after", "name", "where", "controller", "adminPanController"])) {
                $template["actions"]["following"][] = $blocks[$i];
            } else if (in_array($blocks[$i]['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add", "getGroup", "endGroup"])) {
                break;
            }
        }
        return $template;
    }

    // Collect data for caching.
    // Сбор данных для кеширования.
    private function allBlocksNormalizer($blocks) {
        foreach ($blocks as $key => $block) {
            $actions = $block["actions"];
            $normalizeAction = [];
            foreach ($actions as $action) {
                switch ($action['method_type_name']) {
                    case "name":
                        $normalizeAction[] = ["name" => $action['data_name']];
                        break;
                    case "after":
                    case "where":
                    case "controller":
                    case "adminPanController":
                    case "before":
                        $normalizeAction[] = [$action['method_type_name'] => $action['actions']];
                        break;
                    case "type":
                        $normalizeAction[] = ["type" => $action['type']];
                        break;
                    case "protect":
                        $normalizeAction[] = ["protect" => $action['protect']];
                        break;
                    case "prefix":
                        $normalizeAction[] = ["prefix" => $action['data_path']];
                        break;
                    case "domain":
                        $normalizeAction[] = ["domain" => $action['domain']];
                        break;
                }
            }
            $blocks[$key]["actions"] = $normalizeAction;
        }
        return $blocks;
    }

    // Identify the request method.
    // Выявление метода запроса.
    private function globalMethodType($blocks) {
        $history = [];
        $this->mainParams = [];
        foreach ($blocks as $key => $block) {
            if ($block['method_type_name'] == "getType") {
                $this->mainParams = $block['type'];
                $history[] = $this->mainParams;
            } else if ($block['method_type_name'] == "endType") {
                array_pop($history);
                $this->mainParams = end($history);
            } else if (in_array($block['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add"])) {
                $blocks[$key]['type'] =  $this->mainParams ?: $block['type'];
            }
        }
        return $blocks;
    }

    // Identify protecting methods.
    // Выявление защищающих методов.
    private function globalMethodProtect($blocks) {
        $this->mainParams = [];
        foreach ($blocks as $key => $block) {
            if ($block['method_type_name'] == "getProtect") {
                $this->mainParams = $block['protect'];
            } else if ($block['method_type_name'] == "endProtect") {
                $this->mainParams = [];
            } else if (in_array($block['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add"])) {
                $blocks[$key]['protect'] = $this->mainParams;
            }
        }
        return $blocks;
    }

    // Sequential validation of methods.
    // Последовательная проверка методов.
    private function checkAllMethods($blocks) {
        $this->checkMethodNamedGroups($blocks);
        $this->checkMethodUniversal($blocks, "getType", "endType");
        $this->checkMethodUniversal($blocks, "getProtect", "endProtect");
        $this->checkMethodsBeforeAndAfterBlock($blocks);
    }

    // Checking the correct nesting of groups.
    // Проверка правильной вложенности групп.
    private function checkMethodNamedGroups($blocks) {
        $this->mainParams = [];
        $this->mainValues = [];
        foreach ($blocks as $key => $block) {
            if ($block['method_type_name'] == "getGroup") {
                $this->mainValues["getGroup"][] = 1;
                if (!empty($block["data_name"])) {
                    $this->mainParams["getGroup"][] = $block["data_name"];
                }
            } else if ($block['method_type_name'] == "endGroup") {
                $this->mainValues["endGroup"][] = 1;
                if (count($this->mainValues["endGroup"]) > count($this->mainValues["getGroup"])) {
                    $this->errors[] = "HL002-ROUTE_ERROR: Error in method ->endGroup() ! " . $key .
                        "No open tag `getGroup`. ~ " .
                        "Исключение в методе ->endGroup() ! Не открыт тег `getGroup`.";
                    ErrorOutput::add($this->errors);
                }
                if (!empty($block["data_name"])) {
                    $this->mainParams["endGroup"][] = $block["data_name"];
                    if (!in_array($block["data_name"], $this->mainParams["getGroup"])) {
                        $this->errors[] = "HL003-ROUTE_ERROR: Error in method ->endGroup() ! " .
                            "No open group named: `" . $block["data_name"] . "`. ~ " .
                            "Исключение в методе ->endGroup() ! Отсутствует открывающий тег `getGroup` для группы с названием: `" .
                            $block["data_name"] . "`.";
                        ErrorOutput::add($this->errors);
                    }
                }
            }
        }

        if (count($this->mainParams) > 0) {
            if (isset($this->mainParams["endGroup"]) && isset($this->mainParams["getGroup"])) {
                $block_intersect = array_intersect($this->mainParams["endGroup"], $this->mainParams["getGroup"]);
                if (count($block_intersect) !== count($this->mainParams["getGroup"])) {
                    $all_names = array_unique($this->mainArrayMerge([$this->mainParams["endGroup"], $this->mainParams["getGroup"]]));
                    $this->errors[] = "HL004-ROUTE_ERROR: Error in method ->endGroup() ! " .
                        "Names do not match: " . implode(", ", array_diff($all_names, $block_intersect)) . ". ~ " .
                        "Исключение в методе ->endGroup() ! Не найдены парные теги для именованных групп: " . implode(", ", array_diff($all_names, $block_intersect)) . ".";
                    ErrorOutput::add($this->errors);
                }
            }
        }
        return $blocks;
    }


    // Check nesting for bordering methods.
    // Проверка вложенности для обрамляющих методов.
    private function checkMethodUniversal($blocks, $getType, $endType) {
        $this->mainParams = [];
        foreach ($blocks as $block) {
            if ($block['method_type_name'] == $getType) {
                $this->mainParams[$getType][] = 1;
            } else if ($block['method_type_name'] == $endType) {
                $this->mainParams[$endType][] = 1;
            }
        }
        if (isset($this->mainParams[$getType]) && isset($this->mainParams[$endType]) &&
            count($this->mainParams[$getType]) != count($this->mainParams[$endType])) {
            $this->errors[] = "HL006-ROUTE_ERROR: Error in method ->$endType() ! " .
                "The number of `$getType` and `$endType` does not match. ~ " .
                "Исключение в методе ->$endType() ! Количество тегов `$getType` и `$endType` не одинаково. ";
            ErrorOutput::add($this->errors);
        }
        return $blocks;
    }

    // Check for correct placement of pre and post methods.
    // Проверка правильного расположения предварительных и послеидущих методов.
    private function checkMethodsBeforeAndAfterBlock($blocks) {
        foreach ($blocks as $key => $block) {
            $this->mainParams = [];
            if (in_array($block['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add"])) {
                for ($i = $key - 1; $i >= 0; $i--) {
                    if (in_array($blocks[$i]['method_type_name'], ["name", "where", "controller", "adminPanController"])) {
                        $this->mainParams[] = $blocks[$i]['method_type_name'];
                    } else if (in_array($blocks[$i]['method_type_name'], ["getGroup"])) {
                        if (count($this->mainParams) > 0) {
                            $this->errors[] = "HL007-3-ROUTE_ERROR: Error in method ->getGroup() ! " .
                                "Call `" . implode(", ", array_unique($this->mainParams)) . "` cannot be applied to a method `getGroup`. ~ " .
                                "Исключение в методе ->getGroup() ! Вызовы `" . implode(", ", array_unique($this->mainParams)) . "` не могут быть применены к методу `getGroup`";
                            ErrorOutput::add($this->errors);
                        }
                        break;
                    } else if (in_array($blocks[$i]['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add"])) {
                        break;
                    }
                }
                $this->mainParams = [];
                $allBlocksCount = count($blocks);
                for ($i = $key + 1; $i < $allBlocksCount; $i++) {
                    if (in_array($blocks[$i]['method_type_name'], ["before", "type", "prefix", "protect", "domain"])) {
                        $this->mainParams[] = $blocks[$i]['method_type_name'];
                    } else if (in_array($blocks[$i]['method_type_name'], ["endGroup"])) {
                        if (count($this->mainParams) > 0) {
                            $this->errors[] = "HL007-1-ROUTE_ERROR: Error in method ->endGroup() ! " .
                                "Call `" . implode(", ", array_unique($this->mainParams)) . "` cannot be applied to a method `endGroup`. ~ " .
                                "Исключение в методе ->endGroup() ! Вызовы `" . implode(", ", array_unique($this->mainParams)) . "` не могут быть применены к методу `endGroup`.";
                            ErrorOutput::add($this->errors);
                        }
                        break;
                    } else if (in_array($blocks[$i]['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add"])) {
                        break;
                    } else if (empty($block["data_params"]) && ($i == $key + 1) &&
                        ($blocks[$i]['method_type_name'] != "controller" && $blocks[$i]['method_type_name'] != "adminPanController")) {
                        $this->errors[] = "HL022-ROUTE_ERROR: Error in method ->get() ! " .
                            "Missing controller() for " . $blocks[$i]['method_type_name'] . " method without parameters. ~ " .
                            "Исключение в методе ->" . $blocks[$i]['method_type_name'] . "() ! Отсутствует controller у метода " . $blocks[$i]['method_type_name'] . "() без параметров.";
                        ErrorOutput::add($this->errors);
                    }
                }
            }
        }
    }

    // Controller check.
    // Проверка контроллера.
    private function checkController() {
        $blocks = $this->result;
        foreach ($blocks as $block) {
            if (in_array($block['method_type_name'], ["get", "post", "put", "patch", "delete", "options", "any", "match", "add"])) {
                $actions = $block["actions"];
                $path = $block["data_path"];
                $prefix = '';
                foreach ($actions as $action) {
                    if (isset($action["prefix"])) {
                        $prefix .= "/" . $action["prefix"];
                    }
                    if (isset($action["name"])) {
                        $block["data_name"] = $action["name"];
                    }
                }
                $path = preg_replace('#(/){2,}#', "/", $prefix . "/" . $path);
                if (isset($block['data_name'])) $this->addresses[$block['data_name']] = $path;
                preg_match_all("/\{([^\}]*)\}/i", $path, $matches);
                $ids = $matches[1];
                if (count($ids) > 0 && count($ids) != count(array_unique($ids))) {
                    $array = [];
                    $missingId = [];
                    foreach ($ids as $id) {
                        if (in_array($id, $array)) $missingId[] = "{" . $id . "}";
                        $array[] = $id;
                    }
                    $missingId = implode(", ", $missingId);
                    $this->errors[] = "HL024-ROUTE_ERROR: Error in method ->" . $block['method_type_name'] . "() ! " .
                        "Duplicate names: " . $missingId . ". ~ " .
                        "Исключение в методе ->" . $block['method_type_name'] . "() ! Дублирование названий переменных: " . $missingId . ". Итоговый адрес " . str_replace("//", "/", $path);
                    ErrorOutput::add($this->errors);
                }
            }
        }
    }
}

