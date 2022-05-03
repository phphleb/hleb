<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

defined('HLEB_START') or  define('HLEB_START', microtime(true));

// End of script execution (before starting the main project).
if (!function_exists('hl_preliminary_exit')) {
    /**
     * @param string $text - message text.
     *
     * @internal
     */
    function hl_preliminary_exit($text = '') {
        exit(strval($text));
    }
}

if (intval(explode('.', phpversion())[0]) < 7) {
    // End of script execution before starting the framework.
    hl_preliminary_exit("The application requires PHP version higher than 7.0 (Current version " . phpversion() . ")");
}

if (empty($_SERVER['REQUEST_METHOD'])) {
    // End of script execution before starting the framework.
    hl_preliminary_exit('Undefined $_SERVER[\'REQUEST_METHOD\']');
}

$_SERVER['HTTP_HOST'] = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : function () {

    $possibleHostSources = ['HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR'];
    $sourceTransformations = [
        "HTTP_X_FORWARDED_HOST" => function ($value) {
            $elements = explode(',', $value);
            return trim(end($elements));
        }
    ];
    $host = '';
    foreach ($possibleHostSources as $key => $source) {
        if (!empty($host)) break;
        if (empty($_SERVER[$source])) continue;
        $host = $_SERVER[$source];
        if (array_key_exists($source, $sourceTransformations)) {
            $host = $sourceTransformations[$source]($host);
        }
    }
    return trim($host);
};


if (empty($_SERVER['HTTP_HOST'])) {
    // End of script execution before starting the framework.
    hl_preliminary_exit('Undefined $_SERVER[\'HTTP_HOST\']');
}

/** @internal */
function hleb_get_host() {
    return  $_SERVER['HTTP_HOST'];
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

define('HLEB_PROJECT_DIRECTORY', __DIR__);

const HLEB_PROJECT_VERSION = '1';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const HLEB_HTTP_TYPE_SUPPORT = ['get', 'post', 'delete', 'put', 'patch', 'options'];

// Project root directory
defined('HLEB_GLOBAL_DIRECTORY') or define('HLEB_GLOBAL_DIRECTORY', realpath(HLEB_PUBLIC_DIR . '/../'));

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * @param string $path
 *
 * @internal
 */
function hleb_require(string $path) {
    require_once "$path";
}

$pathToStartFileDir = rtrim(defined('HLEB_SEARCH_START_CONFIG_FILE') ? HLEB_SEARCH_START_CONFIG_FILE : HLEB_GLOBAL_DIRECTORY, '\\/ ');
hleb_require( $pathToStartFileDir .  '/' . (file_exists($pathToStartFileDir . '/start.hleb.php') ? '' : 'default.') . 'start.hleb.php');

if (!defined('HLEB_PROJECT_DEBUG') || !is_bool(HLEB_PROJECT_DEBUG)) {
    // End of script execution before starting the framework.
    hl_preliminary_exit("Incorrectly defined setting: ...DEBUG");
}

if (!defined('HLEB_PROJECT_CLASSES_AUTOLOAD') || !is_bool(HLEB_PROJECT_CLASSES_AUTOLOAD)) {
    // End of script execution before starting the framework.
    hl_preliminary_exit("Incorrectly defined setting: ...CLASSES_AUTOLOAD");
}

if (!defined('HLEB_PROJECT_ENDING_URL') || !is_bool(HLEB_PROJECT_ENDING_URL)) {
    // End of script execution before starting the framework.
    hl_preliminary_exit("Incorrectly defined setting: ...ENDING_URL");
}

if (!defined('HLEB_PROJECT_LOG_ON') || !is_bool(HLEB_PROJECT_LOG_ON)) {
    // End of script execution before starting the framework.
    hl_preliminary_exit("Incorrectly defined setting: ...LOG_ON");
}

if (!defined('HLEB_PROJECT_VALIDITY_URL') || !is_string(HLEB_PROJECT_VALIDITY_URL)) {
    // End of script execution before starting the framework.
    hl_preliminary_exit("Incorrectly defined setting: ...VALIDITY_URL");
}

define('HLEB_PROJECT_DEBUG_ON', (bool) (HLEB_PROJECT_DEBUG && $_SERVER['REQUEST_METHOD'] === 'GET' && (empty($_GET['_debug']) || $_GET['_debug'] === 'on')) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest');


// Demo redirection from "http" to "https"
defined('HLEB_PROJECT_ONLY_HTTPS') or define('HLEB_PROJECT_ONLY_HTTPS', false);

// Demo URL redirection from "www" to without "www" and back 0/1/2
defined('HLEB_PROJECT_GLUE_WITH_WWW') or define('HLEB_PROJECT_GLUE_WITH_WWW', 0);

// Allows to set/unset session_start when loading the framework. For GET request method only.
defined('HLEB_DEFAULT_SESSION_INIT') or define('HLEB_DEFAULT_SESSION_INIT', true);

if (isset($_GET["_token"])) {
    header("Referrer-Policy: origin-when-cross-origin");
}

//To set a different directory name 'vendor' add HLEB_VENDOR_DIR_NAME to the constants
define('HLEB_VENDOR_DIRECTORY', defined('HLEB_VENDOR_DIR_NAME') ? HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIR_NAME : dirname(__DIR__, 2));

defined('HLEB_STORAGE_DIRECTORY') or  define('HLEB_STORAGE_DIRECTORY', HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . "storage");
define('HLEB_PROJECT_STORAGE_DIR', (defined('HLEB_STORAGE_DIRECTORY') ? rtrim(HLEB_STORAGE_DIRECTORY, '\\/ ') : HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . 'storage'));

if (HLEB_PROJECT_LOG_ON) {
    require __DIR__ . DIRECTORY_SEPARATOR . 'common.php';
}

//Full path to folder '/storage'
if (!function_exists('hleb_system_storage_path')) {
    /**
     * @param string $subPath - directory name.
     * @return string
     *
     * @internal
     */
    function hleb_system_storage_path($subPath = '') {
        return HLEB_PROJECT_STORAGE_DIR . (!empty($subPath) ? DIRECTORY_SEPARATOR . (trim($subPath, '\\/ ')) : '');
    }
}
// For compatibility
/** @internal */
function hleb_dc64d27da09bab7_storage_directory() {
    return hleb_system_storage_path();
}

/**
 * @param $message
 * @param string $level
 * @internal
 */
function hleb_system_log($message, $level = 'error') {
    \Hleb\Main\Logger\Log::getInstance()->log($level, $message);
}

define('HLEB_LOAD_ROUTES_DIRECTORY', HLEB_GLOBAL_DIRECTORY . '/routes');

define('HLEB_STORAGE_CACHE_ROUTES_DIRECTORY', hleb_system_storage_path('cache' . DIRECTORY_SEPARATOR . 'routes'));

require_once HLEB_PROJECT_DIRECTORY . '/Main/Insert/DeterminantStaticUncreated.php';

require_once HLEB_PROJECT_DIRECTORY . '/Main/Insert/BaseSingleton.php';

require HLEB_PROJECT_DIRECTORY . '/Main/Info.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/Home/Main/Connector.php';

require HLEB_GLOBAL_DIRECTORY . '/app/Optional/MainConnector.php';

if (HLEB_PROJECT_CLASSES_AUTOLOAD) {

    require HLEB_PROJECT_DIRECTORY . '/Main/MainAutoloader.php';

    require HLEB_PROJECT_DIRECTORY . '/Main/HomeConnector.php';
}

ini_set('display_errors', HLEB_PROJECT_DEBUG ? '1' : '0');

// External autoloader
if (file_exists(HLEB_VENDOR_DIRECTORY . '/autoload.php')) {
    require_once HLEB_VENDOR_DIRECTORY . '/autoload.php';
}

//Own autoloader
/**
 * @param $class
 *
 * @internal
 */
function hl_main_autoloader($class) {
    if (HLEB_PROJECT_CLASSES_AUTOLOAD) {
        \Hleb\Main\MainAutoloader::get($class);
    }
    if (HLEB_PROJECT_DEBUG_ON) {
        $class = class_exists($class, false) || interface_exists($class, false) || trait_exists($class, false) ? '<b title="HLEB Autoloader">&#10004;</b> ' . $class : '&#9745; ' . $class;
        \Hleb\Main\Info::insert('Autoload', $class);
    }
}

spl_autoload_register('hl_main_autoloader', true, true);

if (is_dir(HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/')) {

    defined('HLEB_ONLY_RADJAX_ROUTES') or define('HLEB_ONLY_RADJAX_ROUTES', false);

    $GLOBALS['HLEB_MAIN_DEBUG_RADJAX'] = [];

    if (file_exists(HLEB_LOAD_ROUTES_DIRECTORY . '/radjax.php')) {

        defined("HLEB_RADJAX_PATHS_TO_ROUTE_PATHS") or define("HLEB_RADJAX_PATHS_TO_ROUTE_PATHS", [HLEB_LOAD_ROUTES_DIRECTORY . '/radjax.php']);

        require HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Route.php';

        require HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Src/RCreator.php';

        require HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Src/App.php';

        $radjaxIsActive = (new Radjax\Src\App(HLEB_RADJAX_PATHS_TO_ROUTE_PATHS))->get();
    }

    if(HLEB_ONLY_RADJAX_ROUTES) {
        if( !$radjaxIsActive) {
            include HLEB_GLOBAL_DIRECTORY . '/app/Optional/404.php';
            hl_preliminary_exit();
        } else {
            $radjaxIsActive = true;
        }
    }

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

const HLEB_TEMPLATE_CACHED_PATH = '/storage/cache/templates';

if(empty($radjaxIsActive)) {

    require HLEB_PROJECT_DIRECTORY . '/Constructor/Handlers/AddressBar.php';

    /**
     * @param bool $complete - full protocol.
     * @return string
     *
     * @internal
     */
    function hleb_actual_http_protocol($complete = true) {
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http') . ($complete ? '://' : '');
    }

    $addressBar = (new \Hleb\Constructor\Handlers\AddressBar(
        [
            'SERVER' => [
                'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                'HTTP_HOST' => $_SERVER['HTTP_HOST']
            ],
            'HTTPS' => hleb_actual_http_protocol(),
            'HLEB_PROJECT_ONLY_HTTPS' => HLEB_PROJECT_ONLY_HTTPS,
            'HLEB_PROJECT_ENDING_URL' => HLEB_PROJECT_ENDING_URL,
            'HLEB_PROJECT_DIRECTORY' => HLEB_PROJECT_DIRECTORY,
            'HLEB_PROJECT_GLUE_WITH_WWW' => HLEB_PROJECT_GLUE_WITH_WWW,
            'HLEB_PROJECT_VALIDITY_URL' => HLEB_PROJECT_VALIDITY_URL
        ]
    ));

    $address = $addressBar->get();

    if ($addressBar->redirect != null) {
        if (!headers_sent()) {
            header('Location: ' . $addressBar->redirect, true, 301);
        }
        hl_preliminary_exit();
    }

    unset($addressBar, $address, $pathToStartFileDir, $radjaxIsActive);

    require HLEB_VENDOR_DIRECTORY . '/phphleb/framework/init.php';

    if (file_exists(HLEB_GLOBAL_DIRECTORY . '/app/Optional/aliases.php')) {
        hleb_require(HLEB_GLOBAL_DIRECTORY . '/app/Optional/aliases.php');
    }
    if (file_exists(HLEB_GLOBAL_DIRECTORY . '/app/Optional/shell.php')) {
        hleb_require(HLEB_GLOBAL_DIRECTORY . '/app/Optional/shell.php');
    }
    hleb_require(__DIR__ . '/functions.php');

    \Hleb\Main\ProjectLoader::start();

}


