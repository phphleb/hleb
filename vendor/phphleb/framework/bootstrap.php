<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (intval(explode('.', phpversion())[0]) < 7) {
    // End of script execution before starting the framework.
    exit("The application requires PHP version higher than 7.0 (Current version " . phpversion() . ")");
}

if (empty($_SERVER['REQUEST_METHOD'])) {
    // End of script execution before starting the framework.
    exit('Undefined $_SERVER[\'REQUEST_METHOD\']');
}

if (empty($_SERVER['HTTP_HOST'])) {
    // End of script execution before starting the framework.
    exit('Undefined $_SERVER[\'HTTP_HOST\']');
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

define('HLEB_PROJECT_DIRECTORY', __DIR__);

define('HLEB_PROJECT_VERSION', '1');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


define('HLEB_HTTP_TYPE_SUPPORT', ['get', 'post', 'delete', 'put', 'patch', 'options']);

// Project root directory
if (!defined('HLEB_GLOBAL_DIRECTORY')) {
    define('HLEB_GLOBAL_DIRECTORY', realpath(HLEB_PUBLIC_DIR . '/../'));
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// End of script execution (before starting the main project).
function hl_preliminary_exit( $text = '') {
    exit($text);
}

// Monitors the execution of unnecessary output
function hl_print_fulfillment_inspector(string $firstPartOfPath, string $secondPartOfPath) {
    $log = defined('HLEB_PROJECT_LOG_ON') && HLEB_PROJECT_LOG_ON;
    $debug = defined('HLEB_PROJECT_DEBUG') && HLEB_PROJECT_DEBUG;
    $fullPath = realpath($firstPartOfPath . $secondPartOfPath);
    $error = " ERROR! The file " . (!$debug && $log ? $fullPath : $secondPartOfPath);
    if (!file_exists($fullPath)) {
        $error .= " not found. " . PHP_EOL;
        // End of script execution before starting the framework.
        exit(!$debug && $log ? error_log($error) : $error);
    }
    ob_start();
    require_once "$fullPath";
    $content = ob_get_contents();
    ob_end_flush();
    if ($content !== '') {
        $error .= " is not intended to display content. " . PHP_EOL;
        // End of script execution before starting the framework.
        exit(!$debug && $log ? error_log($error) : $error);
    }
}
$pathToStartFileDir = rtrim(defined('HLEB_SEARCH_START_CONFIG_FILE') ? HLEB_SEARCH_START_CONFIG_FILE : HLEB_GLOBAL_DIRECTORY, '\\/ ');
hl_print_fulfillment_inspector( $pathToStartFileDir,  '/' . (file_exists($pathToStartFileDir . '/start.hleb.php') ? '' : 'default.') . 'start.hleb.php');

if (!defined('HLEB_PROJECT_DEBUG') || !is_bool(HLEB_PROJECT_DEBUG)) {
    // End of script execution before starting the framework.
    exit("Incorrectly defined setting: ...DEBUG");
}

if (!defined('HLEB_PROJECT_CLASSES_AUTOLOAD') || !is_bool(HLEB_PROJECT_CLASSES_AUTOLOAD)) {
    // End of script execution before starting the framework.
    exit("Incorrectly defined setting: ...CLASSES_AUTOLOAD");
}

if (!defined('HLEB_PROJECT_ENDING_URL') || !is_bool(HLEB_PROJECT_ENDING_URL)) {
    // End of script execution before starting the framework.
    exit("Incorrectly defined setting: ...ENDING_URL");
}

if (!defined('HLEB_PROJECT_LOG_ON') || !is_bool(HLEB_PROJECT_LOG_ON)) {
    // End of script execution before starting the framework.
    exit("Incorrectly defined setting: ...LOG_ON");
}

if (!defined('HLEB_PROJECT_VALIDITY_URL') || !is_string(HLEB_PROJECT_VALIDITY_URL)) {
    // End of script execution before starting the framework.
    exit("Incorrectly defined setting: ...VALIDITY_URL");
}


// Demo redirection from "http" to "https"
if (!defined('HLEB_PROJECT_ONLY_HTTPS')) {
    define('HLEB_PROJECT_ONLY_HTTPS', false);
}

// Demo URL redirection from "www" to without "www" and back 0/1/2
if (!defined('HLEB_PROJECT_GLUE_WITH_WWW')) {
    define('HLEB_PROJECT_GLUE_WITH_WWW', 0);
}

if (isset($_GET["_token"])) {
    header("Referrer-Policy: origin-when-cross-origin");
}

//To set a different directory name 'vendor' add HLEB_VENDOR_DIR_NAME to the constants
if (!defined('HLEB_VENDOR_DIR_NAME')) {
    //Auto detect current library directory
    define('HLEB_VENDOR_DIR_NAME', array_reverse(explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 2)))[0]);
}

function hleb_dc64d27da09bab7_storage_directory() {
    return (defined('HLEB_STORAGE_DIRECTORY') ?
        rtrim(HLEB_STORAGE_DIRECTORY , '\\/ ') :
        HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . 'storage');
}

define('HLEB_VENDOR_DIRECTORY', HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIR_NAME);

define('HLEB_LOAD_ROUTES_DIRECTORY', HLEB_GLOBAL_DIRECTORY . '/routes');

define('HLEB_STORAGE_CACHE_ROUTES_DIRECTORY', hleb_dc64d27da09bab7_storage_directory() . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'routes');

require_once HLEB_PROJECT_DIRECTORY . '/Main/Insert/DeterminantStaticUncreated.php';

require HLEB_PROJECT_DIRECTORY . '/Main/Info.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/Home/Main/Connector.php';

require HLEB_GLOBAL_DIRECTORY . '/app/Optional/MainConnector.php';

if (HLEB_PROJECT_CLASSES_AUTOLOAD) {

    require HLEB_PROJECT_DIRECTORY . '/Main/MainAutoloader.php';

    require HLEB_PROJECT_DIRECTORY . '/Main/HomeConnector.php';
}

if (HLEB_PROJECT_LOG_ON) {

    ini_set('log_errors', 'On');

    ini_set('error_log', hleb_dc64d27da09bab7_storage_directory()  . '/logs/' . date('Y_m_d_') . 'errors.log');
}

ini_set('display_errors', HLEB_PROJECT_DEBUG ? '1' : '0');

// External autoloader
if (file_exists(HLEB_VENDOR_DIRECTORY . '/autoload.php')) {
    require_once HLEB_VENDOR_DIRECTORY . '/autoload.php';
}

//Own autoloader
function hl_main_autoloader($class) {
    if (HLEB_PROJECT_CLASSES_AUTOLOAD) {
        \Hleb\Main\MainAutoloader::get($class);
    }
    if (HLEB_PROJECT_DEBUG) {
        \Hleb\Main\Info::insert('Autoload', $class);
    }
}

spl_autoload_register('hl_main_autoloader', true, true);

if (is_dir(HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/')) {

    $GLOBALS['HLEB_MAIN_DEBUG_RADJAX'] = [];

    if (file_exists(HLEB_LOAD_ROUTES_DIRECTORY . '/radjax.php')) {

        if (!defined("HLEB_RADJAX_PATHS_TO_ROUTE_PATHS")) {
            define("HLEB_RADJAX_PATHS_TO_ROUTE_PATHS", [
                HLEB_LOAD_ROUTES_DIRECTORY . '/radjax.php'
            ]);
        }
        require HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Route.php';

        require HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Src/RCreator.php';

        require HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Src/App.php';

        (new Radjax\Src\App(
            HLEB_RADJAX_PATHS_TO_ROUTE_PATHS,
            HLEB_GLOBAL_DIRECTORY . '/app/Controllers'
        ))->get();
    }

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

define('HLEB_TEMPLATE_CACHED_PATH', '/storage/cache/templates');

require HLEB_PROJECT_DIRECTORY . '/Constructor/Handlers/AddressBar.php';

$actualProtocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

$addressBar = (new \Hleb\Constructor\Handlers\AddressBar(
    [
        'SERVER' => $_SERVER,
        'HTTPS' => $actualProtocol,
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
    exit();
}

unset($addressBar, $actualProtocol, $address, $pathToStartFileDir);

require HLEB_VENDOR_DIRECTORY . '/phphleb/framework/init.php';

if (file_exists(HLEB_GLOBAL_DIRECTORY . '/app/Optional/aliases.php')) {
    hl_print_fulfillment_inspector(HLEB_GLOBAL_DIRECTORY, '/app/Optional/aliases.php');
}
hl_print_fulfillment_inspector(HLEB_GLOBAL_DIRECTORY, '/app/Optional/shell.php');

\Hleb\Main\ProjectLoader::start();


