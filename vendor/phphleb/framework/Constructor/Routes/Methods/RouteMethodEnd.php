<?php

declare(strict_types=1);

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

    protected $main_params = [];

    protected $main_values = [];

    protected $render = [];

    protected $addresses = [];

    /**
     * RouteMethodEnd constructor.
     * @param StandardRoute $instance
     */
    function __construct(StandardRoute $instance)
    {
        $this->method_type_name = "end";

        $this->instance = $instance;

        $this->result = $this->instance->data();

        $this->result = self::create_groups();

        $this->check_controller();

        $this->result["render"] = $this->render;

        $this->result["addresses"] = $this->addresses;

        $this->result["update"] = date("r") . " / " . rand();

        $this->result["domains"] = self::search_domains();

        ErrorOutput::run();

    }

    public function data()
    {
        return $this->result;
    }

    private function search_domains()
    {
        $blocks = $this->result;

        foreach ($blocks as $key => $block) {

            if(isset($block["actions"])) {

                $actions = $block["actions"];

                foreach ($actions as $action) {

                    if (isset($action["domain"]) && count($action["domain"])) return true;
                }
            }

        }
        return false;

    }

    private function create_groups()
    {

        $blocks = $this->result;

        $sample_blocks = [];

        $close_blocks = [];

        $origin_blocks = [];

        $named_blocks = [];

        //

        $blocks = $this->global_methods_add($blocks);


        foreach ($blocks as $key => $block) {

            /*     Группа и элемент как одиночные блоки  */

            if ($block['method_type_name'] == "getGroup") {

                $sample_blocks[$key] = $block;

            } else if ($block['method_type_name'] == "endGroup") {

                $close_blocks[$key] = $block;

                if (!empty($block['data_name'])) {

                    $named_blocks[] = $block['data_name'];
                }

            } else if ($block['method_type_name'] == "get") {

                $origin_blocks[$key] = $block;


            } else if ($block['method_type_name'] == "renderMap") {

                $this->render[$block['data_name']] = $block['data_params'];

            }


        }


        if (count($sample_blocks) !== count($close_blocks)) {

            $this->errors[] = "HL001-ROUTE_ERROR: Error in method ->endGroup() ! " .
                "The number of open (" . count($sample_blocks) . ") and closed (" . count($close_blocks) . ") tags does not match. " .
                "~ Исключение в методе  ->endGroup() ! Количество открытых тегов (" . count($sample_blocks) . ") и закрытых (" . count($close_blocks) . ") не совпадает. ";

            ErrorOutput::add($this->errors);
        }

        $compilation_blocks = [];

        /* Параметры групп */

        foreach ($sample_blocks as $key => $sample_block) {// ПЕРЕБИРАЮТСЯ БЛОКИ С НАЧАЛАМИ ГРУПП

            $position = 1;

            for ($i = $key + 1; $i < count($blocks); $i++) {//ОТ НАЧАЛА С ПОЗИЦИИ ГРУППЫ

                if ($blocks[$i]['method_type_name'] === "endGroup" && (!empty($blocks[$i]['data_name']) &&
                        $sample_block['data_name'] == $blocks[$i]['data_name'])) {

                    $compilation_blocks[$key]["actions"] = $this->calc_environment($blocks, $key, $i);

                    break;

                }

                if (empty($blocks[$i]['data_name']) && empty($sample_block['data_ name'])) {

                    if ($blocks[$i]['method_type_name'] === "getGroup") $position++;

                    if ($blocks[$i]['method_type_name'] === "endGroup") $position--;

                    if ($blocks[$i]['method_type_name'] === "endGroup" && $position == 0) {

                        $compilation_blocks[$key]["actions"] = $this->calc_environment($blocks, $key, $i);

                        break;
                    }

                }

                if ($blocks[$i]['method_type_name'] === "get") {

                    $compilation_blocks[$key]['blocks'][] = $blocks[$i]['number'];
                }

            }


        }


        /* Параметры блоков */

        $final_array = [];


        foreach ($origin_blocks as $key => $origin_block) {

            $properties = $this->calc_environment($blocks, $key, $key);

            //////////////////////////////////////////////////

            $perem_block_actions = $origin_block['actions'] ?? [];

            $perem_properties_previous = $properties['actions']["previous"] ?? [];

            $perem_properties_following = $properties['actions']["following"] ?? [];

            $origin_block['actions'] = $this->main_array_merge([$perem_properties_previous, $perem_block_actions, $perem_properties_following]);


            $invert_compilation_blocks = array_reverse($compilation_blocks);


            foreach ($invert_compilation_blocks as $block) {

                if (!empty($block['blocks']) && in_array($origin_block["number"], $block['blocks'])) {

                    $block_actions_following = $block['actions']['actions']["following"] ?? [];

                    $block_actions_previous = $block['actions']['actions']["previous"] ?? [];

                    $origin_block['actions'] = $this->main_array_merge([$block_actions_previous, $origin_block['actions'], $block_actions_following]);

                }

            }

            $final_array[] = $origin_block;

        }

        return self::all_blocks_normalizer($final_array);

    }

    private function main_array_merge(array $array)
    {

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

    private function calc_environment(array $blocks, int $start, int $end)
    {
        $template = [];

        for ($i = $start - 1; $i >= 0; $i--) {

            if (in_array($blocks[$i]['method_type_name'], ["before", "type", "prefix", "protect", "domain"])) {

                $merge_on_first_position = $template["actions"]["previous"] ?? [];

                array_unshift($merge_on_first_position, $blocks[$i]);

                $template["actions"]["previous"] = $merge_on_first_position;

            } else if (in_array($blocks[$i]['method_type_name'], ["get", "getGroup", "endGroup"])) {

                break;

            }

        }


        for ($i = $end + 1; $i < count($blocks); $i++) {

            if (in_array($blocks[$i]['method_type_name'], ["after", "name", "where", "controller", "adminPanController"])) {

                $template["actions"]["following"][] = $blocks[$i];

            } else if (in_array($blocks[$i]['method_type_name'], ["get", "getGroup", "endGroup"])) {

                break;

            }

        }


        return $template;


    }

    private function all_blocks_normalizer($blocks)
    {

        foreach ($blocks as $key => $block) {

            $actions = $block["actions"];

            $normalize_action = [];

            foreach ($actions as $action) {

                switch ($action['method_type_name']) {

                    case "name":

                        $normalize_action[] = ["name" => $action['data_name']];

                        break;

                    case "after":
                    case "where":
                    case "controller":
                    case "adminPanController":
                    case "before":

                        $normalize_action[] = [$action['method_type_name'] => $action['actions']];

                        break;

                    case "type":

                        $normalize_action[] = ["type" => $action['type']];

                        break;

                    case "protect":

                        $normalize_action[] = ["protect" => $action['protect']];

                        break;

                    case "prefix":

                        $normalize_action[] = ["prefix" => $action['data_path']];

                        break;

                    case "domain":

                        $normalize_action[] = ["domain" => $action['domain']];

                        break;


                }

            }

            $blocks[$key]["actions"] = $normalize_action;
        }

        return $blocks;
    }

    private function global_methods_add($blocks)
    {


        $this->check_all_methods($blocks);

        $blocks = $this->global_method_type($blocks);

        $blocks = $this->global_method_protect($blocks);

        return $blocks;
    }

    private function global_method_type($blocks)
    {

        $history = [];

        $this->main_params = ["get"];


        foreach ($blocks as $key => $block) {

            if ($block['method_type_name'] == "getType") {

                $this->main_params = $block['type'];

                $history[] = $this->main_params;

            } else if ($block['method_type_name'] == "endType") {

                array_pop($history);

                $this->main_params = end($history);

            } else if ($block['method_type_name'] == "get") {

                $blocks[$key]['type'] = $this->main_params;
            }

        }


        return $blocks;
    }

    private function global_method_protect($blocks)
    {


        $this->main_params = [];

        foreach ($blocks as $key => $block) {

            if ($block['method_type_name'] == "getProtect") {

                $this->main_params = $block['protect'];

            } else if ($block['method_type_name'] == "endProtect") {

                $this->main_params = [];

            } else if ($block['method_type_name'] == "get") {

                $blocks[$key]['protect'] = $this->main_params;

            }


        }


        return $blocks;
    }

    private function check_all_methods($blocks)
    {

        $this->check_method_named_groups($blocks);

        $this->check_method_universal($blocks, "getType", "endType");

        $this->check_method_universal($blocks, "getProtect", "endProtect");

        $this->check_methods_before_and_after_block($blocks);


    }

    private function check_method_named_groups($blocks)
    {

        $this->main_params = [];

        $this->main_values = [];

        foreach ($blocks as $key => $block) {

            if ($block['method_type_name'] == "getGroup") {

                $this->main_values["getGroup"][] = 1;

                if (!empty($block["data_name"])) {

                    $this->main_params["getGroup"][] = $block["data_name"];

                }

            } else if ($block['method_type_name'] == "endGroup") {

                $this->main_values["endGroup"][] = 1;

                if (count($this->main_values["endGroup"]) > count($this->main_values["getGroup"])) {

                    $this->errors[] = "HL002-ROUTE_ERROR: Error in method ->endGroup() ! " . $key .
                        "No open tag `getGroup`. ~ " .
                        "Исключение в методе ->endGroup() ! Не открыт тег `getGroup`.";

                    ErrorOutput::add($this->errors);
                }

                if (!empty($block["data_name"])) {

                    $this->main_params["endGroup"][] = $block["data_name"];

                    if (!in_array($block["data_name"], $this->main_params["getGroup"])) {

                        $this->errors[] = "HL003-ROUTE_ERROR: Error in method ->endGroup() ! " .
                            "No open group named: `" . $block["data_name"] . "`. ~ " .
                            "Исключение в методе ->endGroup() ! Отсутствует открывающий тег `getGroup` для группы с названием: `" .
                            $block["data_name"] . "`.";

                        ErrorOutput::add($this->errors);
                    }

                }
            }
        }

        if (count($this->main_params) > 0) {

            if (isset($this->main_params["endGroup"]) && isset($this->main_params["getGroup"])) {

                $block_intersect = array_intersect($this->main_params["endGroup"], $this->main_params["getGroup"]);

                if (count($block_intersect) !== count($this->main_params["getGroup"])) {

                    $all_names = array_unique($this->main_array_merge([$this->main_params["endGroup"], $this->main_params["getGroup"]]));

                    $this->errors[] = "HL004-ROUTE_ERROR: Error in method ->endGroup() ! " .
                        "Names do not match: " . implode(", ", array_diff($all_names, $block_intersect)) . ". ~ " .
                        "Исключение в методе ->endGroup() ! Не найдены парные теги для именованных групп: " . implode(", ", array_diff($all_names, $block_intersect)) . ".";

                    ErrorOutput::add($this->errors);
                }

            }

        }

        return $blocks;

    }

    private function check_method_universal($blocks, $getType, $endType)
    {


        $this->main_params = [];

        foreach ($blocks as $block) {

            if ($block['method_type_name'] == $getType) {

                $this->main_params[$getType][] = 1;

            } else if ($block['method_type_name'] == $endType) {

                $this->main_params[$endType][] = 1;

            }
        }

        if (isset($this->main_params[$getType]) && isset($this->main_params[$endType]) &&

            count($this->main_params[$getType]) != count($this->main_params[$endType])) {

            $this->errors[] = "HL006-ROUTE_ERROR: Error in method ->$endType() ! " .
                "The number of `$getType` and `$endType` does not match. ~ " .
                "Исключение в методе ->$endType() ! Количество тегов `$getType` и `$endType` не одинаково. ";

            ErrorOutput::add($this->errors);
        }

        return $blocks;

    }

    private function check_methods_before_and_after_block($blocks)
    {

        foreach ($blocks as $key => $block) {

            $this->main_params = [];

            if ($block['method_type_name'] == "get") {

                for ($i = $key - 1; $i >= 0; $i--) {

                    if (in_array($blocks[$i]['method_type_name'], ["name", "where", "controller", "adminPanController"])) {

                        $this->main_params[] = $blocks[$i]['method_type_name'];


                    } else if (in_array($blocks[$i]['method_type_name'], ["getGroup"])) {

                        if (count($this->main_params) > 0) {

                            $this->errors[] = "HL007-3-ROUTE_ERROR: Error in method ->getGroup() ! " .
                                "Call `" . implode(", ", array_unique($this->main_params)) . "` cannot be applied to a method `getGroup`. ~ " .
                                "Исключение в методе ->getGroup() ! Вызовы `" . implode(", ", array_unique($this->main_params)) . "` не могут быть применены к методу `getGroup`";

                            ErrorOutput::add($this->errors);
                        }

                        break;

                    } else if ($blocks[$i]['method_type_name'] == "get") {

                        break;

                    }

                }

                $this->main_params = [];

                for ($i = $key + 1; $i < count($blocks); $i++) {

                    if (in_array($blocks[$i]['method_type_name'], ["before", "type", "prefix", "protect", "domain"])) {

                        $this->main_params[] = $blocks[$i]['method_type_name'];


                    } else if (in_array($blocks[$i]['method_type_name'], ["endGroup"])) {

                        if (count($this->main_params) > 0) {

                            $this->errors[] = "HL007-1-ROUTE_ERROR: Error in method ->endGroup() ! " .
                                "Call `" . implode(", ", array_unique($this->main_params)) . "` cannot be applied to a method `endGroup`. ~ " .
                                "Исключение в методе ->endGroup() ! Вызовы `" . implode(", ", array_unique($this->main_params)) . "` не могут быть применены к методу `endGroup`.";

                            ErrorOutput::add($this->errors);
                        }


                        break;

                    } else if ($blocks[$i]['method_type_name'] == "get") {

                        break;

                    } else if (empty($block["data_params"]) && ($i == $key + 1) &&
                        ($blocks[$i]['method_type_name'] != "controller" && $blocks[$i]['method_type_name'] != "adminPanController")) {

                        $this->errors[] = "HL022-ROUTE_ERROR: Error in method ->get() ! " .
                            "Missing controller() for get() method without parameters. ~ " .
                            "Исключение в методе ->get() ! Отсутствует controller у метода get() без параметров.";

                        ErrorOutput::add($this->errors);


                    }

                }

            }

        }


    }

    private function check_controller()
    {

        $blocks = $this->result;

        foreach ($blocks as $block) {

            if ($block['method_type_name'] === "get") {

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

                $path = preg_replace('#(/){2,}#',  "/", $prefix . "/" . $path);

                if (isset($block['data_name'])) $this->addresses[$block['data_name']] = $path;

                preg_match_all("/\{([^\}]*)\}/i", $path, $matches);

                $ids = $matches[1];

                if (count($ids) > 0 && count($ids) != count(array_unique($ids))) {

                    $array = [];

                    $missing_id = [];

                    foreach ($ids as $id) {

                        if (in_array($id, $array)) $missing_id[] = "{" . $id . "}";

                        $array[] = $id;

                    }

                    $missing_id = implode(", ", $missing_id);

                    $this->errors[] = "HL024-ROUTE_ERROR: Error in method ->get() ! " .
                        "Duplicate names: " . $missing_id . ". ~ " .
                        "Исключение в методе ->get() ! Дублирование названий переменных: " . $missing_id . ". Итоговый адрес " . str_replace("//", "/", $path);

                    ErrorOutput::add($this->errors);

                }
            }
        }
    }

}

