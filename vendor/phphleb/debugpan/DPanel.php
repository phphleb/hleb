<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

namespace Phphleb\Debugpan;

use Hleb\Main\Info;
use Hleb\Main\MyDebug;
use Hleb\Main\MyWork;
use Hleb\Main\WorkDebug;

class DPanel
{
    use \DeterminantStaticUncreated;

    public static function add($info)
    {

        $GLOBALS["HLEB_PROJECT_UPDATES"]["phphleb/debugpan"] = "1.0.1";

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

    protected static function this_block($block)
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

                $actions .= "<div style='padding-left: 8px'>" . $key . ": ";
                $actions .= htmlspecialchars(stripcslashes(json_encode($value))) . "</div>";

            }
        }

        $updates =

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
            "cache_routes_color" => $cashe_routes ? "yellowgreen" : "white",
            "path" => self::create_path($path . "/" . $block["data_path"]),
            "cache_routes_text" => $cashe_routes ? " Updated now" : "",
            "route_path" => self::create_path($block["data_path"]),
            "autoload" => is_array(Info::get("Autoload")) ? Info::get("Autoload") : [],
            "cache" => date(DATE_ATOM, filemtime(HLEB_GLOBAL_DIRECTORY . "/storage/cache/routes/routes.txt"))
        ];
    }

    private static function create_path($path)
    {
        $path = "/" . trim(preg_replace('|([/]+)|s', "/", $path), "/") . "/";

        $path = $path == "//" ? "/" : $path;

        $path = preg_replace('|\{(.*?)\}|s', "<span style='color: #e3d027'>$1</span>", $path);

        return $path;

    }

    public static function print_work_info()
    {
        $data = WorkDebug::get();

        if (count($data) > 0) {
            require_once "panels/w_header.php";
            foreach ($data as $key => $value) {
                print "<div style='border: 1px solid #bfbfbf; padding: 10px;'><pre>";
                print "#" . ($key + 1) . ($value[1] != null ? " description: " . $value[1] . PHP_EOL : " ");
                var_dump($value[0]);
                print "</pre></div>";
            }
            print "</div>";
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
                  $result[$k]['cont'] .=  "<div style='padding: 3px;'><b>" . $key . "</b>: ";
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

}

