<?php

define('HLEB_PROJECT_FULL_VERSION', '1.5.90');

require HLEB_PROJECT_DIRECTORY . '/Scheme/App/Controllers/MainController.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/App/Middleware/MainMiddleware.php';

require HLEB_PROJECT_DIRECTORY . '/Scheme/App/Models/MainModel.php';

require HLEB_PROJECT_DIRECTORY . "/Constructor/Routes/MainRoute.php";

require HLEB_PROJECT_DIRECTORY . '/Scheme/Home/Constructor/Routes/StandardRoute.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Routes/Route.php';

require HLEB_PROJECT_DIRECTORY . '/Main/ProjectLoader.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Cache/CacheRoutes.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Routes/LoadRoutes.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Handlers/URL.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Handlers/URLHandler.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Handlers/ProtectedCSRF.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Workspace.php';

require HLEB_PROJECT_DIRECTORY . '/Main/TryClass.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/VCreator.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Routes/Data.php';


define('HL_TWIG_CONNECTED', file_exists(HLEB_VENDOR_DIRECTORY . '/twig'));

if (HL_TWIG_CONNECTED) {

    //Folder with .twig files
    defined('HL_TWIG_LOADER_FILESYSTEM') or  define('HL_TWIG_LOADER_FILESYSTEM', HLEB_GLOBAL_DIRECTORY . '/resources/views');

    //Twig template encoding
    defined('HL_TWIG_CHARSET') or define('HL_TWIG_CHARSET', 'utf-8');

    //Turn on/off Twig caching. Set HL_TWIG_CACHED_ON (Twig) or HLEB_TEMPLATE_CACHE (All)
    define('HL_TWIG_CACHED', (defined('HL_TWIG_CACHED_ON') && HL_TWIG_CACHED_ON) ||
    (defined('HLEB_TEMPLATE_CACHE') && HLEB_TEMPLATE_CACHE) ? HLEB_GLOBAL_DIRECTORY . "/storage/cache/twig/compilation" : false);

    //Recompilation of Twig templates
    defined('HL_TWIG_AUTO_RELOAD') or define('HL_TWIG_AUTO_RELOAD', HLEB_PROJECT_DEBUG);

    //Ignoring non-existent Twig variables
    defined('HL_TWIG_STRICT_VARIABLES') or define('HL_TWIG_STRICT_VARIABLES', false);

    // Automatic screening of Twig data
    defined('HL_TWIG_AUTOESCAPE') or define('HL_TWIG_AUTOESCAPE', false);

    // Optimize data with Twig
    defined('HL_TWIG_OPTIMIZATIONS') or define('HL_TWIG_OPTIMIZATIONS', -1);
}

/**
 * @see view()
 * @deprecated
 */
function hleb_v5ds34hop4nm1d_page_view($view = null, $data = null) {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_view($view, $data);
}

/**
 * @see view()
 * @internal
 */
function hleb_view($view = null, $data = null) {
    if (func_num_args() === 0) {
        return [null, null, 'views'];
    }

    return [$view, $data, 'views'];
}

/** @deprecated */
function hleb_gop0m3f4hpe10d_all($view = null, $data = null, $type = 'views') {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_all($view, $data, $type);
}

/** @internal */
function hleb_all($view = null, $data = null, $type = 'views') {
    if (func_num_args() === 0) {
        return [null, null, $type];
    }

    return [$view, $data, $type];
}

/**
 * @see data()
 * @deprecated
 */
function hleb_to0me1cd6vo7gd_data() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_data();
}

/**
 * @see data()
 * @internal
 */
function hleb_data() {
    return \Hleb\Constructor\Routes\Data::returnData();
}

/**
 * @see render()
 * @deprecated
 */
function hleb_v10s20hdp8nm7c_render($render, $data = null) {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_render($render, $data);
}

/**
 * @see render()
 * @internal
 */
function hleb_render($render, $data = null) {

    if (is_string($render)) {
        $render = [$render];
    }

    /*
     *  Replacement by string key in a named array (first element).
     *
     *  Замена по строковому ключу в именованном массиве (первый элемент).
     *
     *  $map = ['header' => 'headerMap', 'content' => 'origin', 'footer' => 'footerMap'];
     *  render([$map, 'content' => 'replace']);
     */
    if (count($render) >= 2 && is_array($render[0])) {
        $list = [];
        $replace = $render;
        $origin = array_shift($replace);
        foreach ($origin as $key => $value) {
            if (!is_string($key)) {
                $list = [];
                break;
            }
            $list[] = isset($replace[$key]) ? $replace[$key] : $origin[$key];
        }
        $render = $list;
    }

    return hleb_all($render, $data, 'render');
}

/**
 * @param string $dir
 * @return array|bool
 *
 * @internal
 */
function hleb_search_filenames($dir) {
    $handle = opendir($dir);
    if(!$handle) {
        error_log("Can't open directory $dir");
        return false;
    }

    $files = [];

    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            if (is_dir($dir . '/' . $file)) {
                $subfiles = hleb_search_filenames($dir . '/' . $file);
                $files = array_merge($files, $subfiles);
            } else {
                $files[] = $dir . '/' . $file;
            }
        }
    }

    closedir($handle);

    return $files;
}

/**
 * @see csrf_token()
 * @deprecated
 */
function hleb_c3dccfa0da1a3e_csrf_token() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_csrf_token();
}

/**
 * @see csrf_token()
 * @internal
 */
function hleb_csrf_token() {
    return \Hleb\Constructor\Handlers\ProtectedCSRF::key();
}

/**
 * @see csrf_field()
 * @deprecated
 */
function hleb_ds5bol10m0bep2_csrf_field() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_csrf_field();
}

/**
 * @see csrf_field()
 * @internal
 */
function hleb_csrf_field() {
    return '<input type="hidden" name="_token" value="' . hleb_csrf_token() . '">';
}

/**
 * @see redirectToSite()
 * @deprecated
 */
function hleb_ba5c9de48cba78c_redirectToSite($url) {
    hleb_deprecated_info(__FUNCTION__);
    hleb_redirect_to_site($url);
}

/**
 * @see redirectToSite()
 * @internal
 */
function hleb_redirect_to_site(string $url) {
    \Hleb\Constructor\Handlers\URL::redirectToSite($url);
}

/**
 * @see redirect()
 * @deprecated
 */
function hleb_ad7371873a6ad40_redirect(string $url, int $code = 303) {
    hleb_deprecated_info(__FUNCTION__);
    hleb_redirect($url, $code);
}

/**
 * @see redirect()
 * @internal
 */
function hleb_redirect(string $url, int $code = 303) {
    \Hleb\Constructor\Handlers\URL::redirect($url, $code);
}

/**
 * @see getProtectUrl()
 * @deprecated
 */
function hleb_ba5c9de48cba78c_getProtectUrl($url) {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_get_protect_url($url);
}

/**
 * @see getProtectUrl()
 * @internal
 */
function hleb_get_protect_url(string $url) {
    return \Hleb\Constructor\Handlers\URL::getProtectUrl($url);
}

/**
 * @see getFullUrl()
 * @deprecated
 */
function hleb_e0b1036cd5b501_getFullUrl($url) {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_get_full_url($url);
}

/**
 * @see getFullUrl()
 * @internal
 */
function hleb_get_full_url(string $url) {
    return \Hleb\Constructor\Handlers\URL::getFullUrl($url);
}

/**
 * @see getMainUrl()
 * @deprecated
 */
function hleb_e2d3aeb0253b7_getMainUrl() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_get_main_url();
}

/**
 * @see getMainUrl()
 * @internal
 */
function hleb_get_main_url() {
    return \Hleb\Constructor\Handlers\URL::getMainUrl();
}

/**
 * @see getMainClearUrl()
 * @deprecated
 */
function hleb_daa581cdd6323_getMainClearUrl() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_get_main_clear_url();
}

/**
 * @see getMainClearUrl()
 * @internal
 */
function hleb_get_main_clear_url() {
    return explode('?', hleb_get_main_url())[0];
}

/**
 * @see getByName()
 * @deprecated
 */
function hleb_i245eaa1a3b6d_getByName(string $name, array $params = []) {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_get_by_name($name, $params);
}

/**
 * @see getByName()
 * @internal
 */
function hleb_get_by_name(string $name, array $params = []) {
    return \Hleb\Constructor\Handlers\URL::getByName($name, $params);
}

/**
 * @see getStandardUrl()
 * @deprecated
 */
function hleb_a1a3b6di245ea_getStandardUrl(string $name) {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_get_standard_url($name);
}

/**
 * @see getStandardUrl()
 * @internal
 */
function hleb_get_standard_url(string $name) {
    return \Hleb\Constructor\Handlers\URL::getStandardUrl($name);
}

/**
 * @see includeTemplate()
 * @deprecated
 */
function hleb_e0b1036c1070101_template(string $template, array $params = [], bool $return = false) {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_include_template($template, $params, $return);
}

/**
 * @see includeTemplate()
 * @internal
 */
function hleb_include_template(string $template, array $params = [], bool $return = false) {
    return (new \Hleb\Main\MainTemplate($template, $params, $return))->getContent();
}

/**
 * @see includeCachedTemplate()
 * @deprecated
 */
function hleb_e0b1036c1070102_template(string $template, array $params = []) {
    hleb_deprecated_info(__FUNCTION__);
    hleb_include_cached_template($template, $params);
}

/**
 * @see includeCachedTemplate()
 * @internal
 */
function hleb_include_cached_template(string $template, array $params = []) {
    new \Hleb\Constructor\Cache\CachedTemplate($template, $params);
}

/**
 * @see includeOwnCachedTemplate()
 * @deprecated
 */
function hleb_ade9e72e1018c6_template(string $template, array $params = []) {
    hleb_deprecated_info(__FUNCTION__);
    hleb_include_own_cached_template($template, $params);
}

/**
 * @see includeOwnCachedTemplate()
 * @internal
 */
function hleb_include_own_cached_template(string $template, array $params = []) {
    new \Hleb\Constructor\Cache\OwnCachedTemplate($template, $params);
}

/**
 * @see print_r2()
 * @deprecated
 */
function hleb_a581cdd66c107015_print_r2($data, $desc = null) {
    hleb_deprecated_info(__FUNCTION__);
    hleb_print_r2($data, $desc);
}

/**
 * @see print_r2()
 * @internal
 */
function hleb_print_r2($data, $desc = null) {
    \Hleb\Main\WorkDebug::add($data, $desc);
}

/**
 * @see getRequestResources()
 * @deprecated
 */
function hleb_ra3le00te0m01n_request_resources() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_get_request_resources();
}

/**
 * @see getRequestResources()
 * @internal
 */
function hleb_get_request_resources() {
    return \Hleb\Constructor\Handlers\Request::getResources();
}

/**
 * @see getRequestHead()
 * @deprecated
 */
function hleb_t0ulb902e69thp_request_head() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_request_head();
}

/**
 * @see getRequestHead()
 * @internal
 */
function hleb_request_head() {
    return \Hleb\Constructor\Handlers\Request::getHead();
}

/**
 * @see getRequest()
 * @deprecated
 */
function hleb_e70c10c1057hn11cc8il2_get_request() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_get_request();
}

/**
 * @see getRequest()
 * @internal
 */
function hleb_get_request() {
    return \Hleb\Constructor\Handlers\Request::getInstance();
}

/**
 * @see storage_path()
 * @deprecated
 */
function hleb_6iopl942e103te6i10600l_storage_path() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_storage_public_path();
}

/**
 * @see storage_path()
 * @internal
 */
function hleb_storage_public_path() {
    return hleb_system_storage_path('public');
}

/**
 * @see public_path()
 * @deprecated
 */
function hleb_10p134l66o0il0e0t92e6i_public_path() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_public_path();
}

/**
 * @see public_path()
 * @internal
 */
function hleb_public_path() {
    return HLEB_PUBLIC_DIR;
}

/**
 * @see view_path()
 * @deprecated
 */
function hleb_601e30l60p2ii1e0o469tl_view_path() {
    hleb_deprecated_info(__FUNCTION__);
    return hleb_view_path();
}

/**
 * @see view_path()
 * @internal
 */
function hleb_view_path() {
    return HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'view';
}

/**
 * @deprecated
 */
function hleb_bt3e3gl60pg8h71e00jep901_error_404() {
    hleb_deprecated_info(__FUNCTION__);
    hleb_page_404();
}

/**
 * @internal
 */
function hleb_page_404() {
    if (file_exists(hleb_view_path() . DIRECTORY_SEPARATOR . '404.php')) {
        include hleb_view_path() . DIRECTORY_SEPARATOR . '404.php';
    } else {
        include HLEB_GLOBAL_DIRECTORY . '/app/Optional/404.php';
    }
    // End of script execution before starting the main project.
    hl_preliminary_exit();
}

/**
 * @see insertTemplate()
 * @internal
 */
function hleb_insert_template(string $hlTemplatePath, array $hlTemplateData = []) {
    extract($hlTemplateData);
    !HLEB_PROJECT_DEBUG_ON or $hlCacheTime = microtime(true);
    unset($hlTemplateData);
    $hlTemplatePath = trim($hlTemplatePath, '/\\') . '.php';
    if (defined('HLEB_OPTIONAL_MODULE_SELECTION') && HLEB_OPTIONAL_MODULE_SELECTION) {
        require HLEB_GLOBAL_DIRECTORY . '/modules/' . (file_exists(HLEB_GLOBAL_DIRECTORY . '/modules/' . $hlTemplatePath) ? '' : HLEB_MODULE_NAME . '/') . $hlTemplatePath;
    } else {
        require HLEB_GLOBAL_DIRECTORY . '/resources/views/' . $hlTemplatePath;
    }
    !HLEB_PROJECT_DEBUG_ON or Hleb\Main\Info::insert('Templates', (defined('HLEB_MODULE_NAME') ? ' module `' . HLEB_MODULE_NAME . '` ' : '') . trim($hlTemplatePath, '/') . hleb_debug_bugtrace(2) . ' (insertTemplate)' . ' load: ' . (round(microtime(true) - $hlCacheTime, 4) * 1000) . ' ms');
}

/**
 * Attempt to define a line in the content, which includes a template for output in the debug panel.
 *
 * Попытка определения строки в контенте, в которой подключен шаблон для вывода в отладочной панели.
 *
 * @internal
 */
function hleb_debug_bugtrace(int $level) {
    $trace = debug_backtrace(2, $level + 1);
    if (isset($trace[$level])) {
        $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[$level]['file'] ?? ''));
        return ' (' . end($path) . " : " . ($trace[$level]['line'] ?? '') . ')';
    }
    return '';
}

/** @internal */
function hleb_deprecated_info(string $name) {
    if (HLEB_PROJECT_DEBUG_ON) {
        trigger_error("Warning about using deprecated function `{$name}`. It is necessary to remove this function in the `/app/Optional/shell.php` file according to the latest version of the framework.", E_USER_NOTICE);
    }
}


$GLOBALS['HLEB_PROJECT_UPDATES'] = ['phphleb/hleb' => HLEB_FRAME_VERSION, 'phphleb/framework' => HLEB_PROJECT_FULL_VERSION];

if (HLEB_PROJECT_DEBUG_ON && (new Hleb\Main\TryClass('XdORM\XD'))->is_connect() &&
    file_exists(HLEB_VENDOR_DIRECTORY . '/phphleb/xdorm')) {

    $GLOBALS['HLEB_PROJECT_UPDATES']['phphleb/xdorm'] = 'dev';
}
if (HLEB_PROJECT_DEBUG_ON && (file_exists(HLEB_VENDOR_DIRECTORY . '/phphleb/adminpan'))) {
    $GLOBALS['HLEB_PROJECT_UPDATES']['phphleb/adminpan'] = 'dev';
}


