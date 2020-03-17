<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (intval(explode('.', phpversion())[0]) < 7)
    die("The application requires PHP version higher than 7.0 (Current version " . phpversion() . ")");

if(empty($_SERVER['REQUEST_METHOD']))
    die('Undefined $_SERVER[\'REQUEST_METHOD\']');

if(empty($_SERVER['HTTP_HOST']))
    die('Undefined $_SERVER[\'HTTP_HOST\']');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

define('HLEB_PROJECT_DIRECTORY', __DIR__);

define('HLEB_PROJECT_VERSION', '1');

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Demo redirection from "http" to "https"
if(!defined('HLEB_PROJECT_ONLY_HTTPS')) {
    define('HLEB_PROJECT_ONLY_HTTPS', false);
}

// Demo URL redirection from "www" to without "www" and back 0/1/2
if(!defined('HLEB_PROJECT_GLUE_WITH_WWW')) {
    define('HLEB_PROJECT_GLUE_WITH_WWW', 0);
}

define('HLEB_HTTP_TYPE_SUPPORT', ['get', 'post', 'delete', 'put', 'patch', 'options']);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

require HLEB_PUBLIC_DIR . '/../' . (file_exists(HLEB_PUBLIC_DIR . '/../start.hleb.php') ? '' : 'default.') . 'start.hleb.php';

//To set a different directory name 'vendor' add HLEB_VENDOR_DIR_NAME to the constants
if(!defined('HLEB_VENDOR_DIR_NAME')){
    //Auto detect current library directory
    define('HLEB_VENDOR_DIR_NAME', array_reverse(explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 2)))[0] );
}

define('HLEB_VENDOR_DIRECTORY', HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIR_NAME );

define('HLEB_LOAD_ROUTES_DIRECTORY', HLEB_GLOBAL_DIRECTORY . '/routes');

define('HLEB_STORAGE_CACHE_ROUTES_DIRECTORY', HLEB_GLOBAL_DIRECTORY . '/storage/cache/routes');


require_once HLEB_PROJECT_DIRECTORY. '/Main/Insert/DeterminantStaticUncreated.php';

require HLEB_PROJECT_DIRECTORY . '/Main/Info.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/Home/Main/Connector.php';

require HLEB_GLOBAL_DIRECTORY . '/app/Optional/MainConnector.php';

if(HLEB_PROJECT_CLASSES_AUTOLOAD) {

    require HLEB_PROJECT_DIRECTORY . '/Main/MainAutoloader.php';

    require HLEB_PROJECT_DIRECTORY . '/Main/HomeConnector.php';
}

if (HLEB_PROJECT_LOG_ON) {

    ini_set('log_errors', 'On');

    ini_set('error_log', HLEB_GLOBAL_DIRECTORY . '/storage/logs/' . date('Y_m_d_') . 'errors.log');
}

ini_set('display_errors', HLEB_PROJECT_DEBUG ? '1' : '0');

// External autoloader
if (file_exists(HLEB_VENDOR_DIRECTORY. '/autoload.php')) {
    require_once HLEB_VENDOR_DIRECTORY . '/autoload.php';
}

//Own autoloader
function hl_main_autoloader($class)
{
    $ignore_classes = ['Twig\Loader\LoaderInterface'];

    if(HLEB_PROJECT_CLASSES_AUTOLOAD){
        \Hleb\Main\MainAutoloader::get($class);
    }
    if(HLEB_PROJECT_DEBUG && !in_array($class, $ignore_classes)){
        \Hleb\Main\Info::insert('Autoload', $class);
    }
}

spl_autoload_register('hl_main_autoloader', true, true);

if (is_dir(HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/')) {

    $GLOBALS['HLEB_MAIN_DEBUG_RADJAX'] = [];

    if (file_exists(HLEB_LOAD_ROUTES_DIRECTORY . '/api.php') ||
        file_exists(HLEB_LOAD_ROUTES_DIRECTORY . '/ajax.php')) {

    if (!defined("HLEB_RADJAX_PATHS_TO_ROUTE_PATHS")) {
        define("HLEB_RADJAX_PATHS_TO_ROUTE_PATHS", [
            HLEB_LOAD_ROUTES_DIRECTORY . '/api.php',
            HLEB_LOAD_ROUTES_DIRECTORY . '/ajax.php'
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

require HLEB_PROJECT_DIRECTORY. '/Constructor/Handlers/AddressBar.php';

$hl_actual_protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

$hl_address_object =(new \Hleb\Constructor\Handlers\AddressBar(
    [
        'SERVER' => $_SERVER,
        'HTTPS' => $hl_actual_protocol,
        'HLEB_PROJECT_ONLY_HTTPS' => HLEB_PROJECT_ONLY_HTTPS,
        'HLEB_PROJECT_ENDING_URL' => HLEB_PROJECT_ENDING_URL,
        'HLEB_PROJECT_DIRECTORY' => HLEB_PROJECT_DIRECTORY,
        'HLEB_PROJECT_GLUE_WITH_WWW' => HLEB_PROJECT_GLUE_WITH_WWW,
        'HLEB_PROJECT_VALIDITY_URL' => HLEB_PROJECT_VALIDITY_URL
    ]
));

$hl_address = $hl_address_object->get_state();

if($hl_address_object->redirect != null){
    if (!headers_sent()) {
        header('Location: ' . $hl_address_object->redirect, true, 301);
    }
    exit();
}

unset($hl_address_object, $hl_actual_protocol, $hl_address);

require HLEB_VENDOR_DIRECTORY . '/phphleb/framework/init.php';

require HLEB_GLOBAL_DIRECTORY . '/app/Optional/shell.php';

\Hleb\Main\ProjectLoader::start();


