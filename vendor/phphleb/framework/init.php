<?php

define('HLEB_PROJECT_DIRECTORY', __DIR__);

define('HLEB_PROJECT_VERSION', "1");

define('HLEB_PROJECT_FULL_VERSION', "1.2.4");

$GLOBALS["HLEB_PROJECT_UPDATES"] = ["phphleb/hleb" => HLEB_FRAME_VERSION, "phphleb/framework" => HLEB_PROJECT_FULL_VERSION ];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Demo redirection from "http" to "https"
if(!defined('HLEB_PROJECT_ONLY_HTTPS')) {
    define('HLEB_PROJECT_ONLY_HTTPS', false);
}

// Demo URL redirection from "www" to without "www" and back 0/1/2
if(!defined('HLEB_PROJECT_GLUE_WITH_WWW')) {
    define('HLEB_PROJECT_GLUE_WITH_WWW', 0);
}

define('HLEB_HTTP_TYPE_SUPPORT', ['get', 'post', 'delete', 'put', 'patch', 'options']);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (HLEB_PROJECT_LOG_ON) {

    ini_set('log_errors', 'On');

    ini_set('error_log', HLEB_GLOBAL_DIRECTORY . '/storage/logs/' . date("Y_m_d_") . 'errors.log');
}

ini_set('display_errors', HLEB_PROJECT_DEBUG ? '1' : '0');


require HLEB_PROJECT_DIRECTORY. "/Constructor/Handlers/AddressBar.php";

$hl_actual_protocol = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";

$hl_address_object =(new \Hleb\Constructor\Handlers\AddressBar(
    [
        "SERVER" => $_SERVER,
        "HTTPS" => $hl_actual_protocol,
        "HLEB_PROJECT_ONLY_HTTPS" => HLEB_PROJECT_ONLY_HTTPS,
        "HLEB_PROJECT_ENDING_URL" => HLEB_PROJECT_ENDING_URL,
        "HLEB_PROJECT_DIRECTORY" => HLEB_PROJECT_DIRECTORY,
        "HLEB_PROJECT_GLUE_WITH_WWW" => HLEB_PROJECT_GLUE_WITH_WWW,
        "HLEB_PROJECT_VALIDITY_URL" => HLEB_PROJECT_VALIDITY_URL
    ]
));

$hl_address = $hl_address_object->get_state();

if($hl_address_object->redirect != null){
    if (!headers_sent()) {
        header('Location: ' . $hl_address_object->redirect, true, 301);
    }
    exit();
}


require_once HLEB_PROJECT_DIRECTORY. "/Main/Insert/DeterminantStaticUncreated.php";

require HLEB_PROJECT_DIRECTORY . "/Main/Info.php";

require HLEB_PROJECT_DIRECTORY . "/Scheme/Home/Main/Connector.php";

require HLEB_GLOBAL_DIRECTORY . "/app/Optional/MainConnector.php";

// Чтобы установить другое название каталога 'vendor' добавить в константы HLEB_VENDOR_DIR_NAME
if(!defined('HLEB_VENDOR_DIR_NAME')){
    // Автоопределение текущего каталога с библиотеками
    define('HLEB_VENDOR_DIR_NAME', array_reverse(explode(DIRECTORY_SEPARATOR, dirname(__DIR__, 2)))[0] );
}

define('HLEB_VENDOR_DIRECTORY', HLEB_GLOBAL_DIRECTORY . "/" . HLEB_VENDOR_DIR_NAME );

if(HLEB_PROJECT_CLASSES_AUTOLOAD) {

    require HLEB_PROJECT_DIRECTORY . "/Main/MainAutoloader.php";

    require HLEB_PROJECT_DIRECTORY . "/Main/HomeConnector.php";
}

require HLEB_PROJECT_DIRECTORY. "/Scheme/App/Controllers/MainController.php";

require HLEB_PROJECT_DIRECTORY. "/Scheme/App/Middleware/MainMiddleware.php";

require HLEB_PROJECT_DIRECTORY. "/Scheme/App/Models/MainModel.php";


require HLEB_PROJECT_DIRECTORY . "/Constructor/Routes/MainRoute.php";

require HLEB_PROJECT_DIRECTORY. "/Scheme/Home/Constructor/Routes/StandardRoute.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Routes/Route.php";

require HLEB_PROJECT_DIRECTORY. "/Main/ProjectLoader.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Cache/CacheRoutes.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Routes/LoadRoutes.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Handlers/URL.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Handlers/URLHandler.php";

require HLEB_PROJECT_DIRECTORY. "/Main/Functions.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Handlers/ProtectedCSRF.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Workspace.php";

require HLEB_PROJECT_DIRECTORY. "/Main/TryClass.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Handlers/Request.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/VCreator.php";

require HLEB_PROJECT_DIRECTORY. "/Constructor/Routes/Data.php";


// Сторонний автозагрузчик классов

if (file_exists(HLEB_VENDOR_DIRECTORY. '/autoload.php')) {
    require_once HLEB_VENDOR_DIRECTORY . '/autoload.php';
}


// Собственный автозагрузчик классов

function hl_main_autoloader($class)
{
    if(HLEB_PROJECT_CLASSES_AUTOLOAD){
        \Hleb\Main\MainAutoloader::get($class);
    }
    if(HLEB_PROJECT_DEBUG){
        \Hleb\Main\Info::insert("Autoload", $class);
    }
}

spl_autoload_register('hl_main_autoloader', true, true);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function hleb_v5ds34hop4nm1d_page_view($view = null, $data = null)
{

    if (func_num_args() === 0) {
        return [null, null, "views"];
    }

    return [$view, $data, "views"];
}


function hleb_gop0m3f4hpe10d_all($view = null, $data = null, $type = "views")
{

    if (func_num_args() === 0) {
        return [null, null, $type];
    }

    return [$view, $data, $type];
}

function hleb_hol6h1d32sm0l1of_storage($view = null, $data = null)
{

    return hleb_gop0m3f4hpe10d_all($view, $data, "storage");

}

function hleb_to0me1cd6vo7gd_data()
{

    return \Hleb\Constructor\Routes\Data::return_data();

}


function hleb_v10s20hdp8nm7c_render($render, $data = null)
{
    if (gettype($render) == "string") {

        $render = [$render];
    }

    return hleb_gop0m3f4hpe10d_all($render, $data, "render");

}


function hleb_search_filenames($dir)
{

    $handle = opendir($dir) or die("Can't open directory $dir");

    $files = Array();

    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {

            if (is_dir($dir . "/" . $file)) {
                $subfiles = hleb_search_filenames($dir . "/" . $file);

                $files = array_merge($files, $subfiles);

            } else {

                $files[] = $dir . "/" . $file;

            }

        }

    }

    closedir($handle);

    return $files;
}

function hleb_get_host() {

    // Symfony origin function
    $possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
    $sourceTransformations = array(
        "HTTP_X_FORWARDED_HOST" => function($value) {
            $elements = explode(',', $value);
            return trim(end($elements));
        }
    );
    $host = '';
    foreach ($possibleHostSources as $source)
    {
        if (!empty($host)) break;
        if (empty($_SERVER[$source])) continue;
        $host = $_SERVER[$source];
        if (array_key_exists($source, $sourceTransformations))
        {
            $host = $sourceTransformations[$source]($host);
        }
    }

    // Remove port number from host
    $host = preg_replace('/:\d+$/', '', $host);

    return trim($host);
}

function hleb_c3dccfa0da1a3e_csrf_token()
{

    return \Hleb\Constructor\Handlers\ProtectedCSRF::key();
}

function hleb_ds5bol10m0bep2_csrf_field()
{

    return '<input type="hidden" name="_token" value="' . hleb_c3dccfa0da1a3e_csrf_token() . '">';
}


function hleb_ba5c9de48cba78c_redirectToSite($url)
{

    \Hleb\Constructor\Handlers\URL::redirectToSite($url);

}

function hleb_ad7371873a6ad40_redirect(string $url, int $code = 303)
{

    \Hleb\Constructor\Handlers\URL::redirect($url, $code);

}

function hleb_ba5c9de48cba78c_getProtectUrl($url)
{

    return \Hleb\Constructor\Handlers\URL::getProtectUrl($url);

}

function hleb_e0b1036cd5b501_getFullUrl($url)
{

    return \Hleb\Constructor\Handlers\URL::getFullUrl($url);

}

function hleb_e2d3aeb0253b7_getMainUrl()
{

    return \Hleb\Constructor\Handlers\URL::getMainUrl();

}

function hleb_daa581cdd6323_getMainClearUrl()
{


    return explode("?", hleb_e2d3aeb0253b7_getMainUrl())[0];

}

function hleb_i245eaa1a3b6d_getByName(string $name, array $perem = [])
{

    return \Hleb\Constructor\Handlers\URL::getByName($name, $perem);

}

function hleb_a1a3b6di245ea_getStandardUrl(string $name)
{

    return \Hleb\Constructor\Handlers\URL::getStandardUrl($name);

}

function hleb_e0b1036c1070101_template(string $template, array $params = [])
{

    new \Hleb\Main\MainTemplate($template, $params);

}

function hleb_e0b1036c1070102_template(string $template, array $params = [])
{

    new \Hleb\Constructor\Cache\CachedTemplate($template, $params);

}

function hleb_ade9e72e1018c6_template(string $template, array $params = [])
{

    new \Hleb\Constructor\Cache\OwnCachedTemplate($template, $params);

}

function hleb_a581cdd66c107015_print_r2($data, $desc = null)
{

    \Hleb\Main\WorkDebug::add($data, $desc);

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if (HLEB_PROJECT_DEBUG && (new Hleb\Main\TryClass("XdORM\XD"))->is_connect() &&
    file_exists(HLEB_VENDOR_DIRECTORY . "/phphleb/xdorm")){

    $GLOBALS["HLEB_PROJECT_UPDATES"]['phphleb/xdorm'] = "dev";
}
if(HLEB_PROJECT_DEBUG &&(file_exists(HLEB_VENDOR_DIRECTORY . "/phphleb/adminpan"))){
    $GLOBALS["HLEB_PROJECT_UPDATES"]['phphleb/adminpan'] = "dev";
}



require HLEB_GLOBAL_DIRECTORY . "/app/Optional/shell.php";

\Hleb\Main\ProjectLoader::start();

