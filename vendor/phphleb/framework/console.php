<?php

$path = $argv[2] ?? ".";
$arguments = $argv[1] ?? null;
$set_arguments = $argv[6] ?? null;
$vendor_name = $argv[3] ?? null;

define('HLEB_GLOBAL_DIRECTORY', $path);

define('HLEB_VENDOR_DIRECTORY', $vendor_name);

define('HLEB_PROJECT_DIRECTORY', $path . '/' . $vendor_name . '/phphleb/framework');

define('HLEB_PROJECT_DEBUG', false);

define('HLEB_HTTP_TYPE_SUPPORT', ['get', 'post', 'delete', 'put', 'patch', 'options']);

if ($arguments) {

    switch ($arguments) {
        case "--version":
        case "-v":
            $ver = [hl_get_frame_version(), hl_get_framework_version()];
            $bsp = hl_add_bsp($ver);
            print "\n" .
                " ╔═ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ═╗ " . "\n" .
                " ║ HLEB frame project version " . $ver[0] . $bsp[0] . "  ║" . "\n" .
                " ║ phphleb/framework version  " . $ver[1] . $bsp[1] . "  ║" . "\n" .
                " ║ " . hl_console_copyright() . "                  ║" . "\n" .
                " ╚═ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ═╝ " . "\n";
            break;
        case "--clear-cache":
        case "-cc":
            array_map('unlink', glob($path . '/storage/cache/routes/*.txt'));
            array_map('unlink', glob($path . '/storage/cache/templates/*.txt'));
            print "  Cache cleared.";
            break;
        case "--help":
        case "-h":
            print " --version or -v" . "\n" . " --clear-cache or -cc" . "\n" . " --info or -i" .
                "\n" . " --help or -h" . "\n" . " --routes or -r" . "\n" . " --list or -l";
            break;
        case "--routes":
        case "-r":
            print hl_search_nanorouter() . hl_get_routes($path);
            break;
        case "--list":
        case "-l":
            print hl_list($path);
            break;
        case "--info":
        case "-i":
            hl_get_info($path);
            break;
        default:
            $file = hl_convert_command_to_task($arguments);

            if (file_exists($path . '/app/Commands/' . $file . ".php")) {

                hl_create_users_task($path, $file, $set_arguments ?? null, $vendor_name ?? null);

            } else {

                print "Missing required arguments after `console`. Add --help to display more options.";
            }
    }
}

function hl_get_info($path)
{
    $index = $path . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . 'index.php';

    if (file_exists($index)) {
        print "\n" . "File: " . $index . "\n" . "\n";

        print " HLEB_PROJECT_ONLY_HTTPS = " . hl_search_version($index, "HLEB_PROJECT_ONLY_HTTPS") . "\n";

        print " HLEB_PROJECT_GLUE_WITH_WWW = " . hl_search_version($index, "HLEB_PROJECT_GLUE_WITH_WWW") . "\n" . "\n";

    }

    $file = $path . DIRECTORY_SEPARATOR . 'default.start.hleb.php';
    if (file_exists($path . DIRECTORY_SEPARATOR . 'start.hleb.php')) {
        $file = $path . DIRECTORY_SEPARATOR . 'start.hleb.php';
    }

    $info_array = [
        'HLEB_PROJECT_DEBUG',
        'HLEB_PROJECT_CLASSES_AUTOLOAD',
        'HLEB_PROJECT_ENDING_URL',
        'HLEB_PROJECT_LOG_ON',
        'HLEB_PROJECT_VALIDITY_URL'
    ];

    if (!file_exists($file)) {
        print "Missing file " . $file;
        exit();
    }

    print "\n" . "File: " . $file . "\n" . "\n";

    $handle = fopen($file, "r");

    while (!feof($handle)) {
        $buffer = trim(fgets($handle));
        $search = preg_match_all("|^define\(\s*\'([A-Z0-9\_]+)\'\s*\,\s*([^\)]+)\)|u", $buffer, $def, PREG_PATTERN_ORDER);
        if ($search == 1) {
            if (in_array($def[1][0], $info_array)) {
                echo " " . $def[1][0] . " = " . str_replace(["\"", "'"], "", trim($def[2][0])) . "\n";
            }
        }
        $search_errors = preg_match_all('|^error_reporting\(\s*([^)]+)\)|u', $buffer, $def, PREG_PATTERN_ORDER);
        if ($search_errors == 1) {
            print " error_reporting = " . str_replace("  ", " ", trim($def[1][0])) . "\n";
        }
    }
    fclose($handle);
}


function hl_console_copyright()
{
    $start = "2019";
    $cp = date("Y") != $start ? "$start - " . date("Y") : $start;
    return "(c)$cp Foma Tuturov";
}

function hl_get_routes($path)
{
    $file = $path . "/storage/cache/routes/routes.txt";

    $data = [["SDOMAIN", "PREFIX", "ROUTE", "TYPE", "PROTECTED", "CONTROLLER", "NAME"]];

    if (file_exists($file)) {

        $routes = json_decode(file_get_contents($file, true), true);

        if (!empty($routes)) {
            foreach ($routes as $route) {
                if (isset($route['data_path']) && !empty($route['data_path'])) {
                    $prefix = "";
                    $name = $controller = "-";
                    $protect = "";
                    $types = [];
                    $domain = "";
                    $all_pro = !empty($route['protect']) && array_reverse($route['protect'])[0] == "CSRF" ? "ON" : "-";
                    if (isset($route['actions']) && count($route['actions'])) {
                        foreach ($route['actions'] as $action) {
                            if (!empty($action["protect"])) {
                                $protect = ($action["protect"][0] == "CSRF") ? "ON" : "-";
                            }
                            if (isset($action["name"])) {
                                $name = $action["name"];
                            }
                            if (isset($action["controller"])) {
                                $controller = $action["controller"][0];
                            }
                            if (isset($action["adminPanController"])) {
                                $controller = $action["adminPanController"][0] . " [AdmPan]";
                                $name .= " [" . $action["adminPanController"][2] . "]";
                            }

                            if (isset($action["domain"])) {
                                $domain = $domain || hl_domain_calc($action["domain"]);
                            }

                            if (isset($action["prefix"])) {
                                $prefix .= trim($action["prefix"], "/") . "/";
                            }

                            if (isset($action["type"])) {
                                $atype = $action["type"];
                                foreach ($atype as $tp) {
                                    $types [] = $tp;
                                }
                            }
                        }
                    }

                    if (empty($protect)) {
                        $protect = $all_pro;
                    }

                    $prefix = empty($prefix) ? "" : "/" . $prefix;

                    $router = $route['data_path'] == "/" ? $route['data_path'] : "/" . trim($route["data_path"], "/") . "/";

                    $type = strtoupper(implode(", ", array_map("hl_allowed_http_types", array_unique(empty($types) ?
                        (is_array($route['type']) ? $route['type'] : [$route['type']]) : $types))));


                    $data[] = array($domain ? "YES" : "-", $prefix, $router, $type, $protect, $controller, $name);
                }
            }
        }
    }
    if (count($data) === 1) return "No cached routes in project." . "\n";


    return hl_sort_data($data);
}

function hl_allowed_http_types($type)
{
    return empty($type) ? "GET" : ((in_array(strtolower($type), HLEB_HTTP_TYPE_SUPPORT)) ? $type : $type . " [NOT SUPPORTED]");
}

function hl_domain_calc($data)
{

    return is_array($data) && count($data) > 1 && $data[1] > 2;

}

function hl_create_users_task($path, $class, $arg, $vendor)
{
    require HLEB_PROJECT_DIRECTORY . "/Main/Insert/DeterminantStaticUncreated.php";

    require HLEB_PROJECT_DIRECTORY . "/Main/Info.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/App/Commands/MainTask.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/App/Controllers/MainController.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/App/Middleware/MainMiddleware.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/App/Models/MainModel.php";

    require HLEB_PROJECT_DIRECTORY . "/Scheme/Home/Main/Connector.php";

    require HLEB_GLOBAL_DIRECTORY  . "/app/Optional/MainConnector.php";

    require HLEB_PROJECT_DIRECTORY . "/Main/MainAutoloader.php";

    require HLEB_PROJECT_DIRECTORY . "/Main/HomeConnector.php";

    // Сторонний автозагрузчик классов

    if (file_exists(HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIRECTORY . '/autoload.php')) {
        require HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIRECTORY . '/autoload.php';
    }


    // Собственный автозагрузчик классов

    function hl_main_autoloader($class)
    {
        \Hleb\Main\MainAutoloader::get($class);
    }

    spl_autoload_register('hl_main_autoloader', true, true);

    // Выполнение команды

    $real_path = $path . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . $class . ".php";

    include_once "$real_path";

    $search_names = hl_search_once_namespace($real_path, $path . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands');

    if ($search_names) {

        foreach ($search_names as $search_name) {

            if (class_exists('App\Commands\\' . $search_name)) {

                $class_name = 'App\Commands\\' . $search_name;

                (new $class_name())->create_task($arg);

                break;
            }
        }
    }

}

function hl_sort_data($data)
{
    $r = "\n";
    $col = [];
    $max_col = [];

    foreach ($data as $key => $line) {
        foreach ($line as $k => $c) {
            $col[$k][$key] = strlen(trim($c));
        }
    }
    foreach ($col as $k => $cls) {
        $max_col[$k] = max($cls) + 2;
    }
    foreach ($data as $key => $dt) {

        foreach ($dt as $k => $str) {

            $r .= trim($str);
            $add = $max_col[$k] - strlen(trim($str));

            for ($i = 0; $i < $add; $i++) {
                $r .= " ";
            }

            if ($k + 1 == count($dt)) {
                $r .= "\n";
                if ($key == 0) {
                    $r .= "\n";
                }
            }
        }
    }
    return $r;
}

function hl_list($path)
{

    $files = hl_search_files($path . "/app/Commands/");

    $tasks_array = [["TASK", "COMMAND", "DESCRIPTION"]];

    include $path . "/" . HLEB_VENDOR_DIRECTORY . "/phphleb/framework/Scheme/App/Commands/MainTask.php";

    foreach ($files as $file) {

        $names = hl_search_once_namespace($file, $path);

        include_once "$file";

        if ($names) {

            foreach ($names as $name) {

                if (class_exists('App\Commands\\' . $name)) {

                    $cl_name = 'App\Commands\\' . $name;

                    $class = new $cl_name;

                    $tasks_array[] = [$name, hl_convert_task_to_command($name, $path), $class::DESCRIPTION];
                }
            }
        }
    }

    if (count($tasks_array) === 1) return "No tasks in project." . "\n";

    return hl_sort_data($tasks_array);

}


function hl_convert_task_to_command($name, $path)
{
    $result = "";
    $parts = explode("/", str_replace(DIRECTORY_SEPARATOR, "/", $name));
    $end_name = array_pop($parts);

    if (!file_exists(str_replace("//", "/", $path . "/app/Commands/" . (implode("/", $parts)) . "/" . $end_name . ".php"))) {
        return "undefined (wrong namespace)";
    }
    $class_name = str_split($end_name);

    $path = count($parts) ? implode("/", $parts) . "/" : "";
    foreach ($class_name as $key => $part) {
        if (isset($class_name[$key - 1]) && $class_name[$key - 1] == strtolower($class_name[$key - 1]) && $part == strtoupper($part)) {
            $result .= "-";
        }
        $result .= $part;
    }

    return strtolower($path . $result);
}

function hl_convert_command_to_task($name)
{
    $result = '';
    $parts = explode("/", $name);
    $path = "";
    if (count($parts) > 1) {
        $name = array_pop($parts);
        $path = implode("/", $parts) . "/";
    }
    $parts = explode("-", $name);
    foreach ($parts as $key => $part) {
        $result .= ucfirst($part);
    }

    return $path . $result;
}

function hl_search_files($path)
{
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $result = [];
    foreach ($items as $item) {
        if (is_file($item)) {

            if (isset($item)) {
                $result[] = $item;
            }
        }
    }
    return $result;
}

function hl_search_once_namespace($link, $path)
{
    if (strpos($link, ".php", strlen($link) - strlen(".php")) !== false) {

        $pathname = explode("/", str_replace("\\", "/", explode($path, $link)[1]));
        $file = explode(".php", array_pop($pathname))[0];
        foreach ($pathname as $key => $pathn) {
            $pathname[$key] = trim($pathn, ".-\\/");
        }
        $nsp_1 = ucfirst(end($pathname));
        $nsp_1 = empty($nsp_1) ? "" : $nsp_1 . "\\";
        $nsp_2 = trim(implode("\\", array_map("ucfirst", $pathname)), " \\/");
        $nsp_2 = empty($nsp_2) ? "" : $nsp_2 . "\\";

        return array_unique([$file, $nsp_1 . $file, $nsp_2 . $file]);
    }
    return false;
}

function hl_get_frame_version()
{

    return hl_search_version(HLEB_GLOBAL_DIRECTORY . "/public/index.php", "HLEB_FRAME_VERSION");
}

/**
 * @param array $versions
 * @return array
 */
function hl_add_bsp($versions)
{
    $origin = 9;
    $versions = array_map("strlen", $versions);
    $result = ["", ""];
    foreach ($versions as $key => $version) {
        for ($i = 0; $i < $origin - $version; $i++) {
            $result[$key] .= " ";
        }
    }

    return $result;

}

function hl_get_framework_version()
{

    return hl_search_version(HLEB_PROJECT_DIRECTORY . "/init.php", "HLEB_PROJECT_FULL_VERSION");
}

function hl_search_version($file, $const)
{

    $content = file_get_contents($file, true);

    $search = preg_match_all("|define\(\s*\'" . $const . "\'\s*\,\s*([^\)]+)\)|u", $content, $def);

    return trim($def[1][0] ?? 'undefined', "' \"");

}

function hl_search_nanorouter()
{
    if (is_dir(dirname(__FILE__, 3) . "/phphleb/radjax/") &&
        (file_exists(dirname(__FILE__, 4) .  '/routes/ajax.php') ||
            file_exists(dirname(__FILE__, 4) .  '/routes/api.php'))
    ) {

        require dirname(__FILE__, 3) . "/phphleb/radjax/Route.php";

        if(file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/api.php')) include_once HLEB_GLOBAL_DIRECTORY . '/routes/api.php';

        if(file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/ajax.php')) include_once HLEB_GLOBAL_DIRECTORY . '/routes/ajax.php';

        $nano = Radjax\Route::getParams();

        $parameters = [["RADJAX:ROUTE",  "TYPE", "PROTECTED", "CONTROLLER"]];

        foreach($nano as $params){

            $parameters []= [
                " " . str_replace("//", "/", "/" . trim(($params['route'] ?? "undefined"), "\\/") . "/"),
                (strtoupper(isset($params['type']) ? implode(",", is_array($params['type']) ? $params['type'] : [$params['type']]) : "GET" )),
                ($params['protected'] ? "ON" : "-"),
                ($params['controller'] ?? "undefined")
            ];
        }

        if (count($parameters) > 1) {
            return  hl_sort_data($parameters) . "\n";
        }

        return null;
    }
}


