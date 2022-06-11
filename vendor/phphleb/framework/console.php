<?php

declare(strict_types=1);

/** Console command management. */

if (empty($argv) && isset($_SERVER['argv'])) {
    $argv = $_SERVER['argv'];
}
$baseArgument = $argv[1] ?? null;

// End of script execution (before starting the main project).
/**
 * @param string $text - message text.
 *
 * @internal
 */
function hl_preliminary_exit($text = '')
{
    exit($text);
}

define('HLEB_CLI_COMMAND', implode(' ', $argv));

defined('HLEB_GLOBAL_DIRECTORY') or define('HLEB_GLOBAL_DIRECTORY', dirname(__DIR__, 3));

/* To set a different directory name 'vendor' add HLEB_VENDOR_DIR_NAME to the constants */

define('HLEB_VENDOR_DIRECTORY', defined('HLEB_VENDOR_DIR_NAME') ? HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIR_NAME : dirname(__DIR__, 2));

defined('HLEB_STORAGE_DIRECTORY') or define('HLEB_STORAGE_DIRECTORY', HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . "storage");

define('HLEB_STORAGE_CACHE_ROUTES_DIRECTORY', rtrim(HLEB_STORAGE_DIRECTORY, '\\/ ') . "/cache/routes");

defined('HLEB_PROJECT_LOG_ON') or define('HLEB_PROJECT_LOG_ON', true);

ini_set('display_errors', HLEB_PROJECT_LOG_ON ? '0' : '1');

define('HLEB_LOAD_ROUTES_DIRECTORY', HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . 'routes');

const HLEB_PROJECT_DIRECTORY = HLEB_VENDOR_DIRECTORY . '/phphleb/framework';

const HLEB_PROJECT_DEBUG = false;

const HLEB_PROJECT_DEBUG_ON = false;

const HLEB_HTTP_TYPE_SUPPORT = ['get', 'post', 'delete', 'put', 'patch', 'options'];

const HLEB_TEMPLATE_CACHED_PATH = '/storage/cache/templates';

const HL_TWIG_CACHED_PATH = '/storage/cache/twig/compilation';

define('HL_TWIG_CONNECTED', file_exists(HLEB_VENDOR_DIRECTORY . "/twig/twig"));

defined('HLEB_PROJECT_CLASSES_AUTOLOAD') or define('HLEB_PROJECT_CLASSES_AUTOLOAD', true);

if (HLEB_PROJECT_LOG_ON) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'common.php';
}

include_once HLEB_PROJECT_DIRECTORY . '/Main/Console/MainConsole.php';

$consoleHelper = new \Hleb\Main\Console\MainConsole($argv);

/**
 * @param string $path - file path.
 *
 * @internal
 */
function hleb_require(string $path)
{
    require_once "$path";
}

/**
 * @param string $subPath - directory name.
 * @return string
 *
 * @internal
 */
function hleb_system_storage_path($subPath = '')
{
    return HLEB_STORAGE_DIRECTORY . (!empty($subPath) ? DIRECTORY_SEPARATOR . (trim($subPath, '\\/ ')) : '');
}

/**
 * @param string $type
 * @return string
 *
 * @internal
 */
function hlAllowedHttpTypes($type) {
    return empty($type) ? "GET" : ((in_array(strtolower($type), HLEB_HTTP_TYPE_SUPPORT)) ? $type : $type . " [NOT SUPPORTED]");
}

// Auto update packages
if (!empty($baseArgument) &&
    strpos($baseArgument, 'phphleb/') !== false &&
    file_exists(dirname(__DIR__, 2) . '/' . $baseArgument . '/' . 'start.php')
) {
    hleb_require(__DIR__ . DIRECTORY_SEPARATOR . "autoloader.php");
    require dirname(__DIR__, 2) . '/' . $baseArgument . '/' . 'start.php';
    hl_preliminary_exit();
}

define('HLEB_CONSOLE_USER_NAME', @exec('whoami'));

define(
    'HLEB_CONSOLE_PERMISSION_MESSAGE',
    "Permission denied! It is necessary to assign rights to the directory `sudo chmod -R 775 ./storage` and the current user " .
    (HLEB_CONSOLE_USER_NAME ? "`" . HLEB_CONSOLE_USER_NAME . "`" : '')
);

if ($baseArgument) {
    switch ($baseArgument) {
        case '--version':
        case '-v':
            echo $consoleHelper->getVersion();
            break;
        case '--clear-routes-cache':
        case '-routes-cc':
            echo $consoleHelper->clearRoutesCache();
            break;
        case '--clear-cache':
        case '-cc':
            $consoleHelper->clearCache(HLEB_TEMPLATE_CACHED_PATH, 'cache');
            break;
        case '--forced-cc':
            $consoleHelper->forcedClearCache(HLEB_GLOBAL_DIRECTORY . HLEB_TEMPLATE_CACHED_PATH);
            break;
        case '--forced-cc-twig':
            if (HL_TWIG_CONNECTED) {
                $consoleHelper->forcedClearCache(HLEB_GLOBAL_DIRECTORY . HL_TWIG_CACHED_PATH);
                break;
            }
        case '--clear-cache--twig':
        case '-cc-twig':
            if (HL_TWIG_CONNECTED) {
                $consoleHelper->clearCache(HL_TWIG_CACHED_PATH, 'php');
                break;
            }
        case '--help':
        case '-h':
            echo PHP_EOL;
            echo " --version or -v   (displays the version of the framework)" . PHP_EOL .
                " --clear-cache or -cc (clears the templates)" . PHP_EOL .
                " --forced-cc       (forcefully clears the templates)" . PHP_EOL .
                " --clear-routes-cache or -routes-cc (clear routes cache)" . PHP_EOL .
                " --info or -i      (displays the values of the main settings)" . PHP_EOL .
                " --help or -h      (displays a list of default console actions)" . PHP_EOL .
                " --routes or -r    (forms a list of routes)" . PHP_EOL .
                " --list or -l      (forms a list of commands)" . PHP_EOL .
                " --list <command> [--help] (command info)" . PHP_EOL .
                " --logs or -lg     (prints multiple trailing lines from a log file)" . PHP_EOL .
                " --find-route <url> [method] [domain] (route search by url)" . PHP_EOL .
                " --new-task        (сreates a new command)" . PHP_EOL .
                "                   --new-task example-task \"Short description\"" . PHP_EOL .
                (HL_TWIG_CONNECTED ? " --clear-cache--twig or -cc-twig" . PHP_EOL . " --forced-cc-twig" . PHP_EOL : '');
            echo PHP_EOL;
            break;
        case '--routes':
        case '-r':
            echo $consoleHelper->searchRadjaxRoutes();
            echo $consoleHelper->searchStandardRoutes();
            echo PHP_EOL;
            break;
        case '--list':
        case '-l':
            hleb_require(__DIR__ . DIRECTORY_SEPARATOR . "autoloader.php");
            echo $consoleHelper->getTaskList();
            echo PHP_EOL;
            break;
        case '--info':
        case '-i':
            $consoleHelper->getInfo();
            break;
        case '--logs':
        case '-lg':
            $consoleHelper->getLogs();
            break;
        case '--new-task':
            $consoleHelper->createTask();
            break;
        case '--find-route':
        case '-fr':
            hleb_require(__DIR__ . DIRECTORY_SEPARATOR . "autoloader.php");
            $consoleHelper->findRoute();
            break;
        default:
            $file = $consoleHelper->convertCommandToTask($baseArgument);
            if (file_exists(HLEB_GLOBAL_DIRECTORY . "/app/Commands/$file.php")) {
                hleb_require(__DIR__ . DIRECTORY_SEPARATOR . "autoloader.php");
                if (end($argv) === '--help') {
                    $consoleHelper->showCommandHelp(HLEB_GLOBAL_DIRECTORY, $file);
                } else {
                    $consoleHelper->createUsersTask(HLEB_GLOBAL_DIRECTORY, $file);
                }
            } else {
                echo "Missing required arguments after `console`. Add --help to display more options.", PHP_EOL;
            }
    }
} else {
    echo "Missing arguments after `console`. Add --help to display more options.", PHP_EOL;
}


