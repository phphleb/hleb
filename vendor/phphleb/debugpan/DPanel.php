<?php

/*
 * Debug panel output.
 *
 * Вывод отладочной панели.
 */

namespace Phphleb\Debugpan;

use Hleb\Main\Info;
use Hleb\Main\MyDebug;
use Hleb\Main\WorkDebug;
use Hleb\Main\DataDebug;
use Hleb\Main\Insert\BaseSingleton;

class DPanel extends BaseSingleton
{
    private static $queries = false;

    private static $initPanel = false;

    // Insert a debug panel
    // Вставка отладочной панели
    public static function init($info) {
        self::add($info);
        self::printWorkInfo();
    }

    // Adding a panel directly
    // Непосредственное добавление панели
    public static function add($info) {
        if (self::$initPanel) {
            return;
        }

        $debugActualMemoryMaxScript = round(memory_get_peak_usage (false) / 1024 / 1024, 2);

        $GLOBALS["HLEB_PROJECT_UPDATES"]["phphleb/debugpan"] = "1.8";

        if (isset($GLOBALS["HLEB_MAIN_DEBUG_RADJAX"])) {
            $GLOBALS["HLEB_PROJECT_UPDATES"]["phphleb/radjax"] = "dev";
            if (count($GLOBALS["HLEB_MAIN_DEBUG_RADJAX"]))
                MyDebug::add("RADJAX routes", self::createRajaxDebugInfo($GLOBALS["HLEB_MAIN_DEBUG_RADJAX"]));
        }

        $blockingParameter = defined('HLEB_BLOCKED_DEBUG_FROM_GET_PARAMETER') ? HLEB_BLOCKED_DEBUG_FROM_GET_PARAMETER : "_debug";
        if(isset($_REQUEST[$blockingParameter]) && $_REQUEST[$blockingParameter] === 'off') {
            return;
        }

        $debugBlockName = "__hl_debug_panel";
        $debugDataTime = "";
        $timing = $info["time"];
        $debugPreview = 0;

        foreach ($timing as $key => $value) {
            $debugDataTime .= "<div style='padding: 3px'>" . $key . ": " . $value . ($debugPreview > 0 ?
                    " (+" . round($value - $debugPreview, 4) . ")" : "") . "</div>";

            $debugPreview = $value;
        }

        $debugActualRoute = self::actualBlock($info["block"]);
        $debugUpdates = self::myLinks();
        require_once "panels/block.php";
    }

    // Gather information for output
    // Сбор информации для вывода
    protected static function actualBlock(array $block) {
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

        $ormReport = self::createOrmReport();
        self::$queries = !empty($ormReport[0]);
        $cacheRoutes = Info::get("CacheRoutes");
        $debugRenderMap = Info::get("RenderMap");
        $debugRenderMap = $debugRenderMap != null ? htmlspecialchars(json_encode($debugRenderMap)) : "";

        return [
            "name" => $name,
            "where" => $where,
            "actions" => $actions,
            "render_map" => $debugRenderMap,
            "my_params" => self::myDebug(),
            "workpan" => count(WorkDebug::get()) > 0 ? "inline-block" : "none",
            "sqlqpan" => self::$queries > 0 ? "inline-block" : "none",
            "cache_routes_color" => $cacheRoutes ? "yellowgreen" : "white",
            "path" => self::createPath($path . "/" . $block["data_path"]),
            "cache_routes_text" => $cacheRoutes ? " Updated now" : "",
            "route_path" => self::createPath($block["data_path"]),
            "autoload" => is_array(Info::get("Autoload")) ? Info::get("Autoload") : [],
            "templates" => is_array(Info::get("Templates")) ? Info::get("Templates") : [],
            "cache" => date(DATE_ATOM, filemtime(
                defined('HLEB_STORAGE_CACHE_ROUTES_DIRECTORY') ?
                    HLEB_STORAGE_CACHE_ROUTES_DIRECTORY . '/routes.txt' : HLEB_GLOBAL_DIRECTORY . '/routes/routes.txt'
            )),
            "orm_report" => $ormReport[0],
            "orm_time_report" => $ormReport[1],
            "orm_report_active" => self::$queries,
            "orm_count" => $ormReport[2],
        ];
    }

    // Returns the standardized path for the address
    // Возвращает стандантизированный путь для адреса
    private static function createPath($p) {
        $path = "/" . trim(preg_replace('|([/]+)|s', "/", $p), "/") . "/";
        $path = ($path == "//") ? "/" : $path;
        $path = preg_replace('|\{(.*?)\}|s', "<span style='color: #e3d027'>$1</span>", $path);
        return $path;
    }

    // Output information about requests from ORM
    // Вывод информации о запросах из ORM
    private static function createOrmReport(): array {
        $rows = "";
        $allTime = 0;
        $data = [];
        if (class_exists("\Hleb\Main\DataDebug")) {
            $data = DataDebug::get();
            foreach ($data as $key => $value) {
                $ms = round($value[1], 4);
                $allTime += round($ms, 4);
                $rows .= "<div style='padding: 4px; margin-bottom: 4px; background-color: whitesmoke; line-height: 2'>" .
                    "<div style='display: inline-block; min-width: 16px; color:gray; padding: 0 5px;" .
                    "' align = 'center'>" . ($key + 1) . "</div> <span style='color:gray'>[" .
                    "<div style='display: inline-block; color:black; min-width: 26px; width:max-content' align='right'>" . $value[3] . ($ms * 1000) .
                    "</div> ms] " . htmlentities($value[2]) . "</span>&#8195;" . trim($value[0], ";") . ";</div>";
            }
        }
        return [$rows, round($allTime, 4), count($data)];
    }

    // Outputting custom debug information WorkDebug
    // Вывод пользовательской отладочной информации WorkDebug
    public static function printWorkInfo() {
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
            echo "</div></noindex>" . PHP_EOL;
            echo "<!-- /WORK DEBUG PANEL -->";
        }
    }

    // Returns custom debug information from MyDebug for a specific panel
    // Возвращает пользовательскую отладочную информации MyDebug для отдельной панели
    private static function myDebug() {
        $info = MyDebug::all();
        $result = [];
        foreach ($info as $k => $inf) {
            $result[$k] = ['name' => $k, 'cont' => '', 'num' => 0];
            if (is_array($inf)) {
                $result[$k]['num'] = count($inf);
                foreach ($inf as $key => $value) {
                    $result[$k]['cont'] .= "<div style='padding: 6px;'><b>" . htmlspecialchars($key) . "</b>: ";
                    $result[$k]['cont'] .= (is_array($value) ? htmlspecialchars(stripcslashes(json_encode($value))) : $value);
                    $result[$k]['cont'] .= "</div>";
                }
            } else {
                $toStr = strval($inf);
                $result[$k]['cont'] .= $toStr;
                $result[$k]['num'] = "len " . strlen($toStr);
            }
        }
        return $result;
    }

    // Creating links to subsidiary addresses
    // Создание ссылок на вспомогательные ресурсы
    private static function myLinks() {
        $links = "<span style='display:inline-block; margin: 15px 15px 0 0;color:#9d9d9d;'>" .
            "<a href='https://phphleb.ru/'><span style='color:#9d9d9d;'>phphleb.ru</span></a></span>";
        foreach ($GLOBALS["HLEB_PROJECT_UPDATES"] as $key => $value) {
            if (stripos($key, "phphleb/") === 0) {
                $links .= "<div style='display:inline-block; margin: 15px 15px 0 0; white-space: nowrap; color:grey;'>" .
                    "<a href='https://github.com/$key/'><span style='color:#9d9d9d;'>$key</span></a> $value </div>";
            }
        }
        return $links;
    }

    // Separate display of information on the `phphleb/radjax` library
    // Отдельный вывод информации по библиотеке `phphleb/radjax`
    private static function createRajaxDebugInfo(array $param) {
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

