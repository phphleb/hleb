<?php

declare(strict_types=1);

namespace Hleb\Main\Console;

class MainConsole
{

    public function searchVersion($file, $const)
    {
        $content = file_get_contents($file, true);

        $search = preg_match_all("|define\(\s*\'" . $const . "\'\s*\,\s*([^\)]+)\)|u", $content, $def);

        return trim($def[1][0] ?? 'undefined', "' \"");
    }

    public function getInfo()
    {
        $file = HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . 'default.start.hleb.php';
        if (file_exists(HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . 'start.hleb.php')) {
            $file = HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . 'start.hleb.php';
        }

        $info_array = [
            'HLEB_PROJECT_DEBUG',
            'HLEB_PROJECT_CLASSES_AUTOLOAD',
            'HLEB_PROJECT_ENDING_URL',
            'HLEB_PROJECT_LOG_ON',
            'HLEB_PROJECT_VALIDITY_URL',
            'HLEB_PROJECT_ONLY_HTTPS',
            'HLEB_PROJECT_GLUE_WITH_WWW'
        ];

        if (!file_exists($file)) {
            echo "Missing file " . $file;
            exit();
        }

        echo "\n" . "File: " . $file . "\n" . "\n";

        $handle = fopen($file, "r");

        if($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle);
                if($buffer === false) continue;
                $buffer = trim($buffer);

                $search = preg_match_all("|^define\(\s*\'([A-Z0-9\_]+)\'\s*\,\s*([^\)]+)\)|u", $buffer, $def, PREG_PATTERN_ORDER);
                if ($search == 1) {
                    if (in_array($def[1][0], $info_array)) {
                        echo " " . $def[1][0] . " = " . str_replace(["\"", "'"], "", trim($def[2][0])) . "\n";
                    }
                }
                $search_errors = preg_match_all('|^error_reporting\(\s*([^)]+)\)|u', $buffer, $def, PREG_PATTERN_ORDER);
                if ($search_errors == 1) {
                    echo " error_reporting = " . str_replace("  ", " ", trim($def[1][0])) . "\n";
                }
            }
        }
        fclose($handle);
        echo "\n";
    }

    public function getRoutes()
    {
        $file = HLEB_STORAGE_CACHE_ROUTES_DIRECTORY . '/routes.txt';

        $data = [['SDOMAIN', 'PREFIX', 'ROUTE', 'TYPE', 'PROTECTED', 'CONTROLLER', 'NAME']];

        if (file_exists($file)) {

            $routes = json_decode(file_get_contents($file, true), true);

            if (!empty($routes)) {
                foreach ($routes as $route) {
                    if (isset($route['data_path']) && !empty($route['data_path'])) {
                        $prefix = "";
                        $name = $controller = '-';
                        $protect = '';
                        $types = [];
                        $domain = '';
                        $all_pro = !empty($route['protect']) && array_reverse($route['protect'])[0] == 'CSRF' ? 'ON' : '-';
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
                                    $domain = $domain || $this->domainCalc($action["domain"]);
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

                        $router = $route['data_path'] === "/" ? $route['data_path'] : "/" . trim($route["data_path"], "/") . "/";

                        $type = strtoupper(implode(", ", array_map("hl_allowed_http_types", array_unique(empty($types) ?
                            (is_array($route['type']) ? $route['type'] : [$route['type']]) : $types))));


                        $data[] = array($domain ? "YES" : "-", $prefix, $router, $type, $protect, $controller, $name);
                    }
                }
            }
        }
        if (count($data) === 1) return "No cached routes in project." . "\n";


        return $this->sortData($data);
    }

    public function sortData($data)
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
                    if ($key === 0) {
                        $r .= "\n";
                    }
                }
            }
        }
        return $r;
    }

    public function listing()
    {
        $files = $this->searchFiles(HLEB_GLOBAL_DIRECTORY . "/app/Commands/");

        $tasks_array = [["TASK", "COMMAND", "DESCRIPTION"]];

        foreach ($files as $file) {

            $names = $this->searchOnceNamespace($file, HLEB_GLOBAL_DIRECTORY);

            if ($names) {

                foreach ($names as $name) {

                    if (class_exists('App\Commands\\' . $name, true)) {

                        $cl_name = 'App\Commands\\' . $name;

                        $class = new $cl_name;

                        $tasks_array[] = [$name, $this->convertTaskToCommand($name), $class::DESCRIPTION];
                    }
                }
            }
        }

        if (count($tasks_array) === 1) return "No tasks in project." . "\n";

        return $this->sortData($tasks_array);
    }

    public function convertTaskToCommand($name)
    {
        $result = "";
        $parts = explode("/", str_replace(HLEB_GLOBAL_DIRECTORY, "/", $name));
        $end_name = array_pop($parts);

        if (!file_exists(str_replace("//", "/", HLEB_GLOBAL_DIRECTORY . "/app/Commands/" . (implode("/", $parts)) . "/" . $end_name . ".php"))) {
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

    public function convertCommandToTask($name)
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

    public function searchFiles($path)
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $result = [];
        foreach ($items as $item) {
            if(isset($item)) {
                if (is_object($item)) {
                    $result[] = $item->getPathName();
                } else if(is_file($item)){
                    $result[] = $item;
                }
            }

        }

        return $result;
    }

    public function searchOnceNamespace($link, $path)
    {
        if (strpos($link, '.php', strlen($link) - strlen('.php')) !== false) {

            $pathname = explode('/', str_replace("\\", "/", explode($path, $link)[1]));
            $file = explode('.php', array_pop($pathname))[0];
            foreach ($pathname as $key => $pathn) {
                $pathname[$key] = trim($pathn, ".-\\/");
            }
            $nsp_1 = ucfirst(end($pathname));
            $nsp_1 = empty($nsp_1) ? '' : $nsp_1 . "\\";
            $nsp_2 = trim(implode("\\", array_map('ucfirst', $pathname)), " \\/");
            $nsp_2 = empty($nsp_2) ? '' : $nsp_2 . "\\";

            return array_unique([$file, $nsp_1 . $file, $nsp_2 . $file]);
        }
        return false;
    }

    public function progressConsole($all, $total)
    {
        $step = floor($all / 10);
        if ($total === 0) return;
        $str = 'Clearing cache [';
        if ($all > 100) {
            $count = $step == 0 ? 0 : floor($total / $step);

            for ($i = 0; $i < 10; $i++) {
                if (floor($count) < $i) {
                    $str .= ' ';
                } else {
                    $str .= '/';
                }
            }

            $str .= '] - ' . ceil(100 / $all * $total) . "% ";

        } else {

            $str .= $all - 2 < $total ? '//////////' . '] - 100% ' : '/////     ] ~ 50% ';
        }

        fwrite(STDOUT, "\r");
        fwrite(STDOUT, $str);

    }

    public function searchNanorouter()
    {
        if (is_dir(HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/') &&
            (file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/ajax.php') ||
                file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/api.php'))
        ) {

            require_once HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Route.php';

            if (file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/api.php')) include_once HLEB_GLOBAL_DIRECTORY . '/routes/api.php';

            if (file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/ajax.php')) include_once HLEB_GLOBAL_DIRECTORY . '/routes/ajax.php';

            $nano = \Radjax\Route::getParams();

            $parameters = [['RADJAX:ROUTE', 'TYPE', 'PROTECTED', 'CONTROLLER']];

            foreach ($nano as $params) {

                $parameters [] = [
                    " " . str_replace("//", "/", "/" . trim(($params['route'] ?? "undefined"), "\\/") . "/"),
                    (strtoupper(isset($params['type']) ? implode(",", is_array($params['type']) ? $params['type'] : [$params['type']]) : "GET")),
                    ($params['protected'] ? "ON" : "-"),
                    ($params['controller'] ?? "undefined")
                ];
            }

            if (count($parameters) > 1) {
                return $this->sortData($parameters) . "\n";
            }

            return null;
        }
    }

    public function addBsp($versions)
    {
        $origin = 9;
        $versions = array_map('strlen', $versions);
        $result = ['', ''];
        foreach ($versions as $key => $version) {
            for ($i = 0; $i < $origin - $version; $i++) {
                $result[$key] .= ' ';
            }
        }
        return $result;
    }

    private function domainCalc($data)
    {
        return is_array($data) && count($data) > 1 && $data[1] > 2;
    }

}

