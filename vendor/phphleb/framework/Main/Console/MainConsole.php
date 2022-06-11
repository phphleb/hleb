<?php

declare(strict_types=1);

namespace Hleb\Main\Console;


use Hleb\Scheme\App\Commands\MainTask;

final class MainConsole
{
    protected $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * Returns a formatted output of the current frame and framework versions.
     *
     * Возвращает отформатированный вывод текущих версий каркаса разработки и фреймворка.
     *
     * @return string
     * @internal
     */
    public function getVersion()
    {
        $frameVersion = $this->getFrameVersion();
        $frameworkVersion = $this->getFrameworkVersion();
        list($spacesProject, $spacesFramework) = $this->addSpaces([$frameVersion, $frameworkVersion]);
        return PHP_EOL .
            " ╔═ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ═╗ " . PHP_EOL .
            " ║   " . "HLEB frame" . " project version " . $frameVersion . $spacesProject . "║" . PHP_EOL .
            " ║   " . "phphleb/framework" . " version  " . $frameworkVersion . $spacesFramework . "║" . PHP_EOL .
            " ║     " . $this->getConsoleCopyright() . "       ║" . PHP_EOL .
            " ╚═ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ══ ═╝ " . PHP_EOL . PHP_EOL;
    }

    /**
     * Returns the response after the routing cache has been cleared.
     *
     * Возвращает ответ после удаления кеша маршрутизации.
     *
     * @return string
     * @internal
     */
    public function clearRoutesCache()
    {
        $result = PHP_EOL;
        if (file_exists(HLEB_STORAGE_CACHE_ROUTES_DIRECTORY . '/routes.txt')) {
            unlink(HLEB_STORAGE_CACHE_ROUTES_DIRECTORY . '/routes.txt');
            $result .= ' Deleted one file.';
        }
        return $result . PHP_EOL . ' Route cache cleared.' . PHP_EOL;
    }

    /**
     * Clears the development template cache. Deprecated solution.
     *
     * Очищает кеш шаблонов разработки. Устаревшее решение.
     *
     * @param string $cachePath
     * @param string $extension
     * @internal
     */
    public function clearCache(string $cachePath, string $extension)
    {
        $files = glob(HLEB_GLOBAL_DIRECTORY . $cachePath . '/*/*' . $extension, GLOB_NOSORT);
        $this->clearCacheFiles($files, HLEB_TEMPLATE_CACHED_PATH, $cachePath . '/*/*' . $extension);
        echo PHP_EOL . PHP_EOL;
    }

    /**
     * Quickly clear the template cache by moving the folder with files and then deleting them.
     *
     * Быстрая очистка кеша шаблонов путем перемещения папки с файлами, а затем уже их удаления.
     *
     * @param string $path
     * @internal
     */
    public function forcedClearCache(string $path)
    {
        $this->forcedClearCacheFiles($path);
        echo PHP_EOL;
    }

    /**
     * Returns a list of Radjax routes.
     *
     * Возвращает список маршрутов Radjax.
     *
     * @return string|null
     * @internal
     */
    public function searchRadjaxRoutes()
    {
        if (is_dir(HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/') && file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/radjax.php')) {
            require_once HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Route.php';
            include_once HLEB_GLOBAL_DIRECTORY . '/routes/radjax.php';
            $router = class_exists('Radjax\Route', false) ? \Radjax\Route::getParams() : [];
            $parameters = [['RADJAX:ROUTE', 'TYPE', 'PROTECTED', 'CONTROLLER']];
            foreach ($router as $params) {
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

    /**
     * Returns a list of standard routes.
     *
     * Возвращает список стандартных маршрутов.
     *
     * @return string
     * @internal
     */
    public function searchStandardRoutes()
    {
        $file = HLEB_STORAGE_CACHE_ROUTES_DIRECTORY . '/routes.txt';
        $data = [['SDM', 'PREFIX', 'ROUTE', 'TYPE', 'PRO', 'CONTROLLER', 'NAME']];
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
        return $this->sortData($data) . PHP_EOL . "  *SDM - SUBDOMAIN, *PRO - PROTECTED";
    }

    /**
     * Returns a list of commands.
     *
     * Возвращает список команд.
     *
     * @return string
     * @internal
     */
    public function getTaskList()
    {
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
        foreach ($taskList as $key => $task) {
            if (in_array($task[0], $list)) {
                unset($taskList[$key]);
            }
            $list[] = $task[0];
        }
        if (count($taskList) === 1) return "No tasks in project." . PHP_EOL;
        return $this->sortData($taskList);
    }

    /**
     * Getting information from the configuration file according to the values of the main constants.
     *
     * Получение информации из конфигурационного файла согласно значениям основных констант.
     *
     * @internal
     */
    public function getInfo()
    {
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
            'HL_TWIG_STRICT_VARIABLES',
            'HLEB_DB_LOG_ENABLED',
            'HLEB_DEFAULT_SESSION_INIT',
            'HLEB_PROJECT_LOG_SORT_BY_DOMAIN',
            'HLEB_MAX_LOG_LEVEL'
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

    /**
     * Создает новую комманду.
     *
     * Creates a new command.
     *
     * @internal
     */
    public function createTask()
    {
        include_once HLEB_PROJECT_DIRECTORY . '/Main/Console/CreateTask.php';
        new \Hleb\Main\Console\CreateTask(strval($this->arguments[2] ?? ''), strval($this->arguments[3] ?? ''));
    }

    /**
     * Получение последних строчек из лог-файла.
     *
     * Getting the latest lines from the log file.
     *
     * @internal
     */
    public function getLogs()
    {
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
        $contentData = array_reverse($contentData);
        if (count($contentData) < 3) {
            print implode(PHP_EOL, $contentData);
            return;
        }
        $max = 0;
        $result = [];
        foreach ($contentData as $str) {
            if ($max > 3) {
                break;
            }
            if ($str[0] === "[") {
                $max++;
            }
            $result[] = $str;
        }
        print "..." . PHP_EOL . implode(array_reverse($result));
    }

    /**
     * Searches for a route by parameters.
     *
     * Осуществляет поиск маршрута по параметрам.
     *
     * @internal
     */
    public function findRoute()
    {
        if (class_exists('Phphleb\Rfinder\RouteFinder', true)) {
            $address = $this->arguments[2] ?? '';
            if (!empty($address)) {
                $finder = (new \Phphleb\Rfinder\RouteFinder(strval($address), $this->arguments[3] ?? 'GET', $this->arguments[4] ?? null));
                echo $finder->getInfo() . PHP_EOL;
            } else {
                echo 'Required URL not specified! php console --find-route <url> [method] [domain]' . PHP_EOL;
            }
        } else {
            echo 'You need to install the phphleb/rfinder library.' . PHP_EOL;
        }
    }

    /**
     * Getting the path to the file from the command name.
     *
     * Получение пути до файла из названия команды.
     *
     * @param $name
     * @return string
     * @internal
     */
    public function convertCommandToTask($name)
    {
        $result = '';
        $parts = array_map('ucfirst', explode("/", str_replace('\\', '/', $name)));
        $path = implode("/", $parts);
        $segments = explode("-", $path);
        foreach ($segments as $key => $segment) {
            $result .= ucfirst($segment);
        }
        return $result;
    }

    /**
     * Display description for the command.
     *
     * Отображение описания к команде.
     *
     * @param string $path
     * @param string $class
     * @internal
     */
    public function showCommandHelp(string $path, string $class)
    {
        /** @var object|null $task */
        $task = $this->createTaskClass($path, $class);
        if (!is_null($task)) {
            print PHP_EOL . 'DESCRIPTION: ' . $task::DESCRIPTION . PHP_EOL . PHP_EOL;
            try {
                $reflector = new \ReflectionClass(get_class($task));
                $comment = str_replace('  ', '', $reflector->getMethod('execute')->getDocComment());
                if (!empty($comment)) {
                    print $comment . PHP_EOL;
                }
            } catch (\Throwable $e) {
                print '#' . $e->getMessage();
            }
        }

        $content = file_get_contents(HLEB_GLOBAL_DIRECTORY . "/app/Commands/$class.php");
        preg_match('/function( *)execute\(([^)]*?)\)/', $content, $match_1);

        if (!empty($match_1[2])) {
            $args = explode(',', $match_1[2]);
            foreach ($args as $arg) {
                $item = array_map('trim', explode('=', $arg));
                print PHP_EOL . ' - ' . $item[0] . (isset($item[1]) ? ' default ' . $item[1] : '') . PHP_EOL;
            }
            return;
        }
        print PHP_EOL . "No arguments." . PHP_EOL;
    }

    /**
     * Executes a custom command.
     *
     * Выполняет пользовательскую команду.
     *
     * @param string $path
     * @param string $class
     * @internal
     */
    public function createUsersTask(string $path, string $class)
    {
        $task = $this->createTaskClass($path, $class);
        if ($task) {
            $task->createTask(count($this->arguments) ? array_slice($this->arguments, 2) : []);
        }
    }

    private function getFrameVersion()
    {
        if (file_exists(HLEB_PUBLIC_DIR . '/index.php')) {
            return $this->searchVersion(HLEB_PUBLIC_DIR . '/index.php', 'HLEB_FRAME_VERSION');
        }
        return '-';
    }

    private function getFrameworkVersion()
    {
        return $this->searchVersion(HLEB_PROJECT_DIRECTORY . '/init.php', 'HLEB_PROJECT_FULL_VERSION');
    }

    private function getConsoleCopyright()
    {
        $start = "2019";
        $cp = date("Y") != $start ? "$start - " . date("Y") : $start;
        return "(c)$cp Foma Tuturov";
    }

    private function searchVersion(string $file, $const)
    {
        $content = file_get_contents($file, true);
        preg_match_all("|define\(\s*\'" . $const . "\'\s*\,\s*([^\)]+)\)|u", $content, $def);
        return trim($def[1][0] ?? 'undefined', "' \"");
    }

    private function addSpaces(array $versions)
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

    private function clearCacheFiles($files, $path, $scan_path)
    {
        echo PHP_EOL . "Clearing cache [          ] 0% ";
        $all = count($files);
        $error = 0;
        if (count($files)) {
            $counter = 1;
            foreach ($files as $k => $value) {
                if (file_exists($value) && !is_writable($value)) {
                    $error++;
                } else {
                    @chmod($value, 0777);
                }
                @unlink($value);
                if (file_exists($value)) {
                    $error++;
                }
                $this->progressConsole(count($files), $k);
                echo " (" . $counter . "/" . $all . ")";
                $counter++;
            }
            $pathDirectory = glob(HLEB_GLOBAL_DIRECTORY . $scan_path);
            if (!empty($pathDirectory)) {
                @array_map('unlink', $pathDirectory);
            }
            $directories = glob(HLEB_GLOBAL_DIRECTORY . $path . '/*', GLOB_NOSORT);
            foreach ($directories as $key => $directory) {
                if (!file_exists($directory)) break;
                $listDirectory = scandir($directory);
                if ([] === (array_diff((is_array($listDirectory) ? $listDirectory : []), ['.', '..']))) {
                    @rmdir($directory);
                }
            }
            if (count($files) < 100) {
                fwrite(STDOUT, "\r");
                fwrite(STDOUT, "Clearing cache [//////////] - 100% ($all/$all)");
            }
        } else {
            fwrite(STDOUT, "\r");
            fwrite(STDOUT, "No files in " . $path . ". Cache cleared.");
        }
        if ($error) {
            fwrite(STDOUT, "\r");
            fwrite(STDOUT, HLEB_CONSOLE_PERMISSION_MESSAGE);
        }
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

    private function forcedClearCacheFiles(string $path)
    {
        $standardPath = str_replace('\\', '/', $path);
        if (!file_exists($path)) {
            hl_preliminary_exit("No files in " . $standardPath . ". Cache cleared." . PHP_EOL);
        }
        if (!is_writable($path)) {
            hl_preliminary_exit(HLEB_CONSOLE_PERMISSION_MESSAGE . PHP_EOL);
        }
        $newPath = rtrim($path, "/") . "_" . md5(microtime() . rand());
        rename($path, $newPath);
        if (file_exists($newPath) && !is_writable($newPath)) {
            hl_preliminary_exit(HLEB_CONSOLE_PERMISSION_MESSAGE . PHP_EOL);
        }
        if (!file_exists($newPath)) {
            hl_preliminary_exit("Error! Couldn't move directory." . PHP_EOL);
        }
        echo "Moving files from a folder " . $standardPath . ". Cache cleared." . PHP_EOL;
        fwrite(STDOUT, "Delete files...");
        $this->removeDir($newPath);
        fwrite(STDOUT, "\r");
        fwrite(STDOUT, "Delete files [//////////] 100% ");
    }

    private function removeDir(string $path)
    {
        if (file_exists($path) && is_dir($path)) {
            $dir = opendir($path);
            if (!is_resource($dir)) return;
            while (false !== ($element = readdir($dir))) {
                if ($element != '.' && $element != '..') {
                    $tmp = $path . '/' . $element;
                    chmod($tmp, 0777);
                    if (is_dir($tmp)) {
                        $this->removeDir($tmp);
                    } else {
                        unlink($tmp);
                    }
                }
            }
            closedir($dir);
            if (file_exists($path)) {
                rmdir($path);
            }
        }
    }

    private function sortData($data)
    {
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

    private function domainCalc($data)
    {
        return is_array($data) && count($data) > 1 && $data[1] > 2;
    }

    public function searchFiles($path)
    {
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

    public function searchOnceNamespace($link, $path)
    {
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

    private function convertTaskToCommand($name)
    {
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
        foreach ($parts as $keyPart => $onePart) {
            $name = '';
            $onePart = str_split($onePart);
            foreach ($onePart as $key => $part) {
                $prefix = '';
                if (isset($onePart[$key - 1]) && $onePart[$key - 1] == strtolower($onePart[$key - 1]) && $part == strtoupper($part)) {
                    $prefix = "-";
                }
                $name .= $prefix . $part;
            }
            $parts[$keyPart] = $name;
        }

        $path = count($parts) ? implode("/", $parts) . "/" : "";

        return strtolower(str_replace(["-\\-", "-/-"], "/", $path . $result));
    }

    private function shortDescription($str)
    {
        $max = 30;
        if (strlen($str) < $max) {
            return $str;
        }
        return substr($str, 0, $max - 1) . "...[" . (strlen($str) - $max + 1) . "]";
    }

    /**
     * @param string $path
     * @param string $class
     * @return null|MainTask
     */
    private function createTaskClass(string $path, string $class)
    {
        $realPath = $path . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR . $class . ".php";
        include_once "$realPath";
        $namespace = str_replace('/', '\\', trim($class, '\\/ '));

        if (class_exists('App\Commands\\' . $namespace)) {
            $className = 'App\Commands\\' . $namespace;
            return new $className();
        }
        return null;
    }

}

