<?php

declare(strict_types=1);

/*
 * Processing of console actions.
 *
 * Обработка консольных действий.
 */

namespace Hleb\Main\Console;

final class MainConsole
{
    //Search for version from file by php-code.
    // Поиск версии из файла по php-коду.
    public function searchVersion($file, $const) {
        $content = file_get_contents($file, true);
        preg_match_all("|define\(\s*\'" . $const . "\'\s*\,\s*([^\)]+)\)|u", $content, $def);
        return trim($def[1][0] ?? 'undefined', "' \"");
    }

    // Get information from the configuration file.
    // Получение информации из конфигурационного файла.
    public function getInfo() {
        $pathFromSearchStartFile = defined('HLEB_SEARCH_START_CONFIG_FILE') ? HLEB_SEARCH_START_CONFIG_FILE : HLEB_GLOBAL_DIRECTORY;
        $file = $pathFromSearchStartFile . DIRECTORY_SEPARATOR . 'default.start.hleb.php';
        if (file_exists($pathFromSearchStartFile . DIRECTORY_SEPARATOR . 'start.hleb.php')) {
            $file = $pathFromSearchStartFile . DIRECTORY_SEPARATOR . 'start.hleb.php';
        }
        $infoList = [
            'HLEB_PROJECT_DEBUG',
            'HLEB_PROJECT_CLASSES_AUTOLOAD',
            'HLEB_PROJECT_ENDING_URL',
            'HLEB_PROJECT_LOG_ON',
            'HLEB_PROJECT_VALIDITY_URL',
            'HLEB_PROJECT_ONLY_HTTPS',
            'HLEB_PROJECT_GLUE_WITH_WWW',
            'HLEB_TEMPLATE_CACHE',
            'HL_TWIG_AUTO_RELOAD',
            'HL_TWIG_STRICT_VARIABLES'
        ];
        if (!file_exists($file)) {
            echo "Missing file " . $file;
            hl_preliminary_exit();
        }
        echo PHP_EOL, "File: ", $file, PHP_EOL, PHP_EOL;
        $handle = fopen($file, "r");
        if (!empty($handle)) {
            while (!feof($handle)) {
                $buffer = fgets($handle);
                if ($buffer === false) continue;
                $buffer = trim($buffer);

                $search = preg_match_all("|^define\(\s*\'([A-Z0-9\_]+)\'\s*\,\s*([^\;]+)|u", $buffer, $def, PREG_PATTERN_ORDER);
                if ($search == 1) {
                    if (in_array($def[1][0], $infoList)) {
                        echo " ", $def[1][0], " = ", str_replace(["\"", "'"], "", trim($def[2][0], "\n\r) ")), PHP_EOL;
                    }
                }
                $searchErrors = preg_match_all('|^error_reporting\(\s*([^)]+)\)|u', $buffer, $def, PREG_PATTERN_ORDER);
                if ($searchErrors == 1) {
                    echo " error_reporting = ", str_replace("  ", " ", trim($def[1][0])), PHP_EOL;
                }
            }
            fclose($handle);
        }
    }

    // Get the route schema.
    // Получение схемы роутов.
    public function getRoutes() {
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
                        $allProtect = !empty($route['protect']) && array_reverse($route['protect'])[0] == 'CSRF' ? 'ON' : '-';
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
                                    $admPan = $action["adminPanController"];
                                    $controller = $admPan[0] . " [AP]";
                                    $routeName = !is_array($admPan[2]) ? $admPan[2] : "x" . count($admPan[2]);
                                    $name .= " [" . $routeName . "]";
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
                            $protect = $allProtect;
                        }
                        $prefix = empty($prefix) ? "" : "/" . $prefix;
                        $router = $route['data_path'] === "/" ? $route['data_path'] : "/" . trim($route["data_path"], "/") . "/";
                        $type = strtoupper(implode(", ", array_map("hlAllowedHttpTypes", array_unique(empty($types) ?
                            (is_array($route['type']) ? $route['type'] : [$route['type']]) : $types))));
                        $data[] = array($domain ? "YES" : "-", $prefix, $router, $type, $protect, $controller, $name);
                    }
                }
            }
        }
        if (count($data) === 1) return "No cached routes in project." . PHP_EOL;
        return $this->sortData($data);
    }

    public function sortData($data) {
        $r = PHP_EOL;
        $col = [];
        $maxColumn = [];
        foreach ($data as $key => $line) {
            foreach ($line as $k => $c) {
                $col[$k][$key] = strlen(trim($c));
            }
        }
        foreach ($col as $k => $cls) {
            $maxColumn[$k] = max($cls) + 2;
        }
        foreach ($data as $key => $dt) {
            foreach ($dt as $k => $str) {
                $r .= trim($str);
                $add = $maxColumn[$k] - strlen(trim($str));
                for ($i = 0; $i < $add; $i++) {
                    $r .= " ";
                }
                if ($k + 1 == count($dt)) {
                    $r .= PHP_EOL;
                    if ($key === 0) {
                        $r .= PHP_EOL;
                    }
                }
            }
        }
        return $r;
    }

    public function listing() {
        $files = $this->searchFiles(HLEB_GLOBAL_DIRECTORY . "/app/Commands/");
        $taskList = [["TASK", "COMMAND", "DESCRIPTION"]];
        foreach ($files as $file) {
            $names = $this->searchOnceNamespace($file, HLEB_GLOBAL_DIRECTORY);
            if ($names) {
                foreach ($names as $name) {
                    if (class_exists('App\Commands\\' . $name, true)) {
                        $cl_name = 'App\Commands\\' . $name;
                        $class = new $cl_name;
                        $taskList[] = [$name, $this->convertTaskToCommand($name), $this->shortDescription($class::DESCRIPTION)];
                    }
                }
            }
        }
        $list = [];
        foreach($taskList as $key => $task) {
            if(in_array($task[0], $list)) {
                unset($taskList[$key]);
            }
            $list[] = $task[0];
        }
        if (count($taskList) === 1) return "No tasks in project." . PHP_EOL;
        return $this->sortData($taskList);
    }

    public function convertCommandToTask($name) {
        $result = '';
        $parts = array_map('ucfirst', explode("/", str_replace('\\', '/', $name)));
        $path = implode("/", $parts);
        $segments = explode("-", $path);
        foreach ($segments as $key => $segment) {
            $result .= ucfirst($segment);
        }
        return $result;
    }

    public function searchFiles($path) {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        $result = [];
        foreach ($items as $item) {
            if (isset($item)) {
                if (is_object($item)) {
                    $result[] = $item->getPathName();
                } else if (is_file($item)) {
                    $result[] = $item;
                }
            }

        }

        return $result;
    }

    public function searchOnceNamespace($link, $path) {
        if (strpos($link, '.php', strlen($link) - strlen('.php')) !== false) {
            $pathname = explode('/', str_replace("\\", "/", explode($path, $link)[1]));
            $file = explode('.php', array_pop($pathname))[0];
            foreach ($pathname as $key => $pathn) {
                $pathname[$key] = trim($pathn, ".-\\/");
            }
            $nsp1 = ucfirst(end($pathname));
            $nsp1 = empty($nsp1) ? '' : $nsp1 . "\\";
            $nsp2 = trim(implode("\\", array_map('ucfirst', $pathname)), " \\/");
            $nsp2 = empty($nsp2) ? '' : $nsp2 . "\\";

            return array_unique([$file, $nsp1 . $file, $nsp2 . $file]);
        }
        return false;
    }

    public function progressConsole($all, $total) {
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

    public function searchNanorouter() {
        if (is_dir(HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/') && file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/radjax.php')) {
            require_once HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Route.php';
            include_once HLEB_GLOBAL_DIRECTORY . '/routes/radjax.php';
            $nano = class_exists('Radjax\Route', false) ? \Radjax\Route::getParams() : [];
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
                return $this->sortData($parameters) . PHP_EOL;
            }
        }
        return null;
    }

    public function addBsp($versions) {
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

    public function getLogs() {
        $pathToLogsDir = rtrim(HLEB_STORAGE_DIRECTORY, '\\/ ') . DIRECTORY_SEPARATOR . "logs";

        $time = 0;
        $lastLogFile = null;
        $fileLogs = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($pathToLogsDir)
        );
        foreach ($fileLogs as $pathname => $logs) {
            if (!$logs->isFile()) continue;
            if (filemtime($logs->getRealPath()) > $time) {
                $lastLogFile = $logs->getRealPath();
            }
        }
        if (empty($lastLogFile) || empty($contentData = file($lastLogFile))) {
            print "No logs found in the project." . PHP_EOL;
            return;
        }

        if (count($contentData) < 3) {
            print implode(PHP_EOL, $contentData);
            return;
        }

        $contentData = array_reverse($contentData);
        $result = ["..." . PHP_EOL];
        $max = 0;
        foreach ($contentData as $str) {
            if ($str[0] === "[") {
                $result[] = $str;
                $max++;
            }
            if ($max > 2) {
                break;
            }
        }

        print implode($result);
    }

    private function domainCalc($data) {
        return is_array($data) && count($data) > 1 && $data[1] > 2;
    }

    private function shortDescription($str) {
        $max = 30;
        if (strlen($str) < $max) {
            return $str;
        }
        return substr($str, 0, $max - 1) . "...[" . (strlen($str) - $max + 1) . "]";
    }

    private function convertTaskToCommand($name) {
        $result = "";
        $parts = explode("/", str_replace(str_replace('\\', '/', HLEB_GLOBAL_DIRECTORY), "/", str_replace('\\', '/', $name)));
        $endName = array_pop($parts);
        $parts = array_map('ucfirst', $parts);
        if (!file_exists(str_replace("//", "/", HLEB_GLOBAL_DIRECTORY . "/app/Commands/" . (implode("/", $parts)) . "/" . $endName . ".php"))) {
            return "undefined (wrong namespace)";
        }
        $className = str_split($endName);
        foreach ($className as $key => $part) {
            if (isset($className[$key - 1]) && $className[$key - 1] == strtolower($className[$key - 1]) && $part == strtoupper($part)) {
                $result .= "-";
            }
            $result .= $part;
        }
        foreach($parts as $keyPart => $onePart) {
            $name = '';
            $onePart = str_split($onePart);
            foreach ($onePart as $key => $part) {
                $prefix  = '';
                if (isset($onePart[$key - 1]) && $onePart[$key - 1] == strtolower($onePart[$key - 1]) && $part == strtoupper($part)) {
                    $prefix  = "-";
                }
                $name .= $prefix . $part;
            }
            $parts[$keyPart] = $name;
        }

        $path = count($parts) ? implode("/", $parts) . "/" : "";

        return strtolower(str_replace(["-\\-", "-/-"], "/", $path . $result));
    }

}

