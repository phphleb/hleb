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

define('HLEB_PROJECT_FULL_VERSION', '1.3.2');

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

require HLEB_PUBLIC_DIR . '/../' . (file_exists(__DIR__ . '/../start.hleb.php') ? '' : 'default.') . 'start.hleb.php';

//To set a different directory name 'vendor' add HLEB_VENDOR_DIR_NAME to the constants
if(!defined('HLEB_VENDOR_DIR_NAME')){
    //Auto detect current library directory
    define('HLEB_VENDOR_DIR_NAME', array_reverse(explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 2)))[0] );
}

define('HLEB_VENDOR_DIRECTORY', HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIR_NAME );

if (is_dir(HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/')) {

    $GLOBALS['HLEB_MAIN_DEBUG_RADJAX'] = [];

    if ((file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/ajax.php') ||
        file_exists(HLEB_GLOBAL_DIRECTORY. '/routes/api.php'))
    ){

        require HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Route.php';

        require HLEB_VENDOR_DIRECTORY . '/phphleb/radjax/Src/App.php';

        if (file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/api.php'))
            include_once HLEB_GLOBAL_DIRECTORY. '/routes/api.php';

        if (file_exists(HLEB_GLOBAL_DIRECTORY . '/routes/ajax.php'))
            include_once HLEB_GLOBAL_DIRECTORY . '/routes/ajax.php';

        function radjax_main_autoloader(string $class)
        {
            \Hleb\Main\MainAutoloader::get($class);
        }

        (new Radjax\Src\App(Radjax\Route::getParams()))->get();

    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


define('HLEB_TEMPLATE_CACHED_PATH', '/storage/cache/templates');

if (HLEB_PROJECT_LOG_ON) {

    ini_set('log_errors', 'On');

    ini_set('error_log', HLEB_GLOBAL_DIRECTORY . '/storage/logs/' . date('Y_m_d_') . 'errors.log');
}

ini_set('display_errors', HLEB_PROJECT_DEBUG ? '1' : '0');


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


