<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

namespace Phphleb\Debugpan;

use Hleb\Main\Info;
use Hleb\Main\MyDebug;
use Hleb\Main\MyWork;
use Hleb\Main\WorkDebug;
use Hleb\Main\DataDebug;

class DPanel
{
    use \DeterminantStaticUncreated;

    private static $queries = false;

    private static $wdebug = false;

    public static function add($info)
    {
        $GLOBALS["HLEB_PROJECT_UPDATES"]["phphleb/debugpan"] = "1.1";

        if(isset($GLOBALS["HLEB_MAIN_DEBUG_RADJAX"])){
            $GLOBALS["HLEB_PROJECT_UPDATES"]["phphleb/radjax"] = "dev";
            if(count($GLOBALS["HLEB_MAIN_DEBUG_RADJAX"]))
                MyDebug::add("RADJAX routes", self::create_ajax_debug_info($GLOBALS["HLEB_MAIN_DEBUG_RADJAX"]) );
        }

        $hl_block_name = "__hl_debug_panel";

        $hl_data_time = "";

        $timing = $info["time"];

        $hl_preview = 0;

        foreach ($timing as $key => $value) {

            $hl_data_time .= "<div style='padding: 3px'>" . $key . ": " . $value . ($hl_preview > 0 ?
                    " (+" . round($value - $hl_preview, 4) . ")" : "") . "</div>";

            $hl_preview = $value;
        }

        $hl_this_route = self::this_block($info["block"]);

        $hl_pr_updates = self::my_links();

        $hl_block_data = $info["block"];

        require_once "panels/block.php";

    }

    protected static function this_block(array $block)
    {
        $name = $where = $actions = $path = "";

        foreach ($block["actions"] as $bl) {
            if (isset($bl["name"]) && $name == "") {
                $name = $bl["name"];
            }
            if (isset($bl["where"]) && $where == "") {
                $where = $bl["where"];
            }
            if (isset($bl["prefix"]) && $where == "") {
                $path .= "/" . $bl["prefix"];
            }
            foreach ($bl as $key => $value) {

                $actions .= "<div style='padding-left: 8px; white-space: nowrap;'>" . $key . ": ";
                $actions .= htmlspecialchars(stripcslashes(json_encode($value))) . "</div>";

            }
        }

        $orm_report = self::create_orm_report();

        self::$queries = !empty($orm_report[0]);

        $cashe_routes = Info::get("CacheRoutes");

        $render_map = Info::get("RenderMap");

        $render_map = $render_map != null ? htmlspecialchars(json_encode($render_map)) : "";


        return [
            "name" => $name,
            "where" => $where,
            "actions" => $actions,
            "render_map" => $render_map,
            "my_params" => self::my_debug(),
            "workpan" => count(WorkDebug::get()) > 0 ? "inline-block" : "none",
            "sqlqpan" => self::$queries > 0 ? "inline-block" : "none",
            "cache_routes_color" => $cashe_routes ? "yellowgreen" : "white",
            "path" => self::create_path($path . "/" . $block["data_path"]),
            "cache_routes_text" => $cashe_routes ? " Updated now" : "",
            "route_path" => self::create_path($block["data_path"]),
            "autoload" => is_array(Info::get("Autoload")) ? Info::get("Autoload") : [],
            "templates" => is_array(Info::get("Templates")) ? Info::get("Templates") : [],
            "cache" => date(DATE_ATOM, filemtime(
                defined('HLEB_STORAGE_CACHE_ROUTES_DIRECTORY') ?
                    HLEB_STORAGE_CACHE_ROUTES_DIRECTORY . '/routes.txt' : HLEB_GLOBAL_DIRECTORY . '/routes/routes.txt'
            )),
            "orm_report" => $orm_report[0],
            "orm_time_report" => $orm_report[1],
            "orm_report_active" => self::$queries,
            "orm_count" => $orm_report[2],
        ];
    }

    private static function create_path($p)
    {
        $path = "/" . trim(preg_replace('|([/]+)|s', "/", $p), "/") . "/";

        $path = ($path == "//") ? "/" : $path;

        $path = preg_replace('|\{(.*?)\}|s', "<span style='color: #e3d027'>$1</span>", $path);

        return $path;

    }

    private static function create_orm_report(): array
    {
        $rows = "";
        if(class_exists("\Hleb\Main\DataDebug")){

            $data = DataDebug::get();
            $all_time = 0;
            foreach($data as $key => $value){
                $ms = round($value[1], 4);
                $all_time += round($ms, 4);
                $rows .= "<div style='padding: 4px; margin-bottom: 4px; background-color: whitesmoke; line-height: 2'>" .
                    "<div style='display: inline-block; min-width: 16px; color:gray; padding: 0 5px;" .
                    "' align = 'center'>" . ($key+1) . "</div> <span style='color:gray'>[" .
                    "<div style='display: inline-block; color:black; min-width: 26px; width:max-content' align='right'>" . $value[3] . ($ms * 1000) .
                    "</div> ms] " . htmlentities($value[2]) . "</span>&#8195;" . trim($value[0], ";") . ";</div>";
            }
        }
        return [$rows, round($all_time, 4), count($data)];
    }

    public static function print_work_info()
    {
        $data = WorkDebug::get();

        if (count($data) > 0) {
            $right = self::$queries ? 100 : 60;
            require_once "panels/w_header.php";
            foreach ($data as $key => $value) {
                echo "<div style='border: 1px solid #bfbfbf; padding: 10px;'><pre>";
                echo "#" . ($key + 1) . ($value[1] != null ? " description: " . $value[1] . PHP_EOL : " ");
                var_dump($value[0]);
                echo "</pre></div>";
            }
            echo "</div>" . PHP_EOL;
            echo "<!-- /WORK DEBUG PANEL -->";
        }
    }

    private static function my_debug()
    {
        $info = MyDebug::all();
        $result = [];
        foreach($info as $k=>$inf) {
            $result[$k] = ['name'=>$k, 'cont'=>'', 'num'=>0];
            if (is_array($inf)) {
                $result[$k]['num'] = count($inf);
                foreach ($inf as $key=>$value) {
                    $result[$k]['cont'] .=  "<div style='padding: 6px;'><b>" .  htmlspecialchars($key) . "</b>: ";
                    $result[$k]['cont'] .= (is_array($value) ? htmlspecialchars(stripcslashes(json_encode($value))) : $value);
                    $result[$k]['cont'] .= "</div>";
                }
            } else {
                $to_str = strval($inf);
                $result[$k]['cont'] .= $to_str;
                $result[$k]['num'] = "len " . strlen($to_str);
            }
        }
        return $result;
    }

    private static function my_links()
    {
        $links = "<span style='display:inline-block; margin: 15px 15px 0 0;color:#9d9d9d;'>" .
            "<a href='https://phphleb.ru/'><span style='color:#9d9d9d;'>phphleb.ru</span></a></span>";
        foreach($GLOBALS["HLEB_PROJECT_UPDATES"] as $key => $value) {
            if(stripos($key, "phphleb/") === 0) {
                $links .= "<div style='display:inline-block; margin: 15px 15px 0 0; white-space: nowrap; color:grey;'>" .
                    "<a href='https://github.com/$key/'><span style='color:#9d9d9d;'>$key</span></a> $value </div>";
            }
        }
        return $links;
    }

    public static function init($info)
    {
        self::add($info);
        self::print_work_info();
    }

    private static function create_ajax_debug_info(array $param)
    {
        $result = [];
        foreach ($param as $data) {
            foreach ($data as $key => $value) {
                $result[] = "<span style='color:yellowgreen'> " . $key . "</span>: <span style='color:whitesmoke'>" .
                    (is_string($value) ? htmlentities($value) : htmlentities(json_encode($value))) . "</span>";
            }
        }

        return "[ " . implode(", ", $result) . " ]";
    }

}

