<?php

define('HLEB_PROJECT_FULL_VERSION', '1.5.65');

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

require HLEB_PROJECT_DIRECTORY . '/Constructor/Handlers/Request.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/VCreator.php';

require HLEB_PROJECT_DIRECTORY . '/Constructor/Routes/Data.php';


define('HL_TWIG_CONNECTED', file_exists(HLEB_VENDOR_DIRECTORY . '/twig'));

if (HL_TWIG_CONNECTED) {

    if (!defined('HL_TWIG_LOADER_FILESYSTEM')) {
        //Folder with .twig files
        define('HL_TWIG_LOADER_FILESYSTEM', HLEB_GLOBAL_DIRECTORY . '/resources/views');
    }

    if (!defined('HL_TWIG_CHARSET')) {
        //Twig template encoding
        define('HL_TWIG_CHARSET', 'utf-8');
    }

    //Turn on/off Twig caching. Set HL_TWIG_CACHED_ON (Twig) or HLEB_TEMPLATE_CACHE (All)
    define('HL_TWIG_CACHED', (defined('HL_TWIG_CACHED_ON') && HL_TWIG_CACHED_ON) ||
    (defined('HLEB_TEMPLATE_CACHE') && HLEB_TEMPLATE_CACHE) ? HLEB_GLOBAL_DIRECTORY . "/storage/cache/twig/compilation" : false);

    if (!defined('HL_TWIG_AUTO_RELOAD')) {
        //Recompilation of Twig templates
        define('HL_TWIG_AUTO_RELOAD', HLEB_PROJECT_DEBUG_ON);
    }

    if (!defined('HL_TWIG_STRICT_VARIABLES')) {
        //Ignoring non-existent Twig variables
        define('HL_TWIG_STRICT_VARIABLES', false);
    }

    if (!defined('HL_TWIG_AUTOESCAPE')) {
        // Automatic screening of Twig data
        define('HL_TWIG_AUTOESCAPE', false);
    }

    if (!defined('HL_TWIG_OPTIMIZATIONS')) {
        // Optimize data with Twig
        define('HL_TWIG_OPTIMIZATIONS', -1);
    }
}

/*
 * The view( ... ) function enables to specify a content template in the get( ... ) function of the router or when returning from the controller.
 *
 * Функция view( ... ) позволяет назначить шаблон контента в функции get( ... ) маршрутизатора или при возвращении из контроллера.
 */
function hleb_v5ds34hop4nm1d_page_view($view = null, $data = null) {
    if (func_num_args() === 0) {
        return [null, null, 'views'];
    }

    return [$view, $data, 'views'];
}

function hleb_gop0m3f4hpe10d_all($view = null, $data = null, $type = 'views') {
    if (func_num_args() === 0) {
        return [null, null, $type];
    }

    return [$view, $data, $type];
}

/*
 * The data() function returns the $data parameters from the view( ..., $data ) function into the content.
 *
 * Функция data() возвращает в шаблон контента параметры $data из функции view( ..., $data ).
 */
function hleb_to0me1cd6vo7gd_data() {
    return \Hleb\Constructor\Routes\Data::returnData();
}

/*
 * The render( ... ) function is used as an equivalent of view( ... ) for a page designer, referring to the name of a set of templates.
 *
 * Функция render( ... ) используется как аналог view( ... ) для конструктора страниц, указывая на название для комплекта шаблонов.
 */
function hleb_v10s20hdp8nm7c_render($render, $data = null) {

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

    return hleb_gop0m3f4hpe10d_all($render, $data, 'render');
}

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

/*
 * The csrf_token() function returns the protected token for protection against CSRF attacks.
 *
 * Функция csrf_token() возвращает защищённый токен для защиты от CSRF-атак.
 */
function hleb_c3dccfa0da1a3e_csrf_token() {
    return \Hleb\Constructor\Handlers\ProtectedCSRF::key();
}

/*
 * The csrf_field() function returns the HTML content for protection against CSRF attacks.
 *
 * Функция csrf_field() возвращает HTML-контент для вставки в форму для защиты от CSRF-атак.
 */
function hleb_ds5bol10m0bep2_csrf_field() {
    return '<input type="hidden" name="_token" value="' . hleb_c3dccfa0da1a3e_csrf_token() . '">';
}

/*
 * The redirectToSite( ... ) function redirects to an external site.
 *
 * Функция redirectToSite( ... ) осуществляет перенаправление на сторонний сайт.
 */
function hleb_ba5c9de48cba78c_redirectToSite($url) {
    \Hleb\Constructor\Handlers\URL::redirectToSite($url);
}

/*
 * The redirect( ... ) function performs internal redirection with an option to specify the redirection code.
 *
 * Функция redirect( ... ) производит внутренний редирект с возможным указанием кода перенаправления.
 */
function hleb_ad7371873a6ad40_redirect(string $url, int $code = 303) {
    \Hleb\Constructor\Handlers\URL::redirect($url, $code);
}

/*
 * The getProtectUrl( ... ) function returns the specified URL address with an added token for protection against CSRF attacks.
 * To protect the route referred to by the URL address in full, one of the protect() methods shall be applied to it.
 *
 * Функция getProtectUrl( ... ) возвращает указанный URL-адрес c добавлением токена для защиты от CSRF-атак.
 * Для полноценной защиты маршрута, на который указывает URL-адрес, к нему должен быть применён один из методов protect().
 */
function hleb_ba5c9de48cba78c_getProtectUrl($url) {
    return \Hleb\Constructor\Handlers\URL::getProtectUrl($url);
}

/*
 * The getFullUrl( ... ) function converts a relative URL address to the full one.
 *
 * Функция getFullUrl( ... ) преобразует относительный URL-адрес в полный.
 */
function hleb_e0b1036cd5b501_getFullUrl($url) {
    return \Hleb\Constructor\Handlers\URL::getFullUrl($url);
}

/*
 * Using the getMainUrl() function, the current URL address can be obtained.
 *
 * При помощи функции getMainUrl() можно получить текущий URL-адрес.
 */
function hleb_e2d3aeb0253b7_getMainUrl() {
    return \Hleb\Constructor\Handlers\URL::getMainUrl();
}

/*
 * The getMainClearUrl() function returns the current URL address without GET parameters.
 *
 * Функция getMainClearUrl() возвращает текущий URL-адрес без GET-параметров.
 */
function hleb_daa581cdd6323_getMainClearUrl() {
    return explode('?', hleb_e2d3aeb0253b7_getMainUrl())[0];
}

/*
 * The getByName( ... ) function enables to access the route address by the name of the route (if it was assigned).
 *
 * Функция getByName( ... ) позволяет обратиться к адресу маршрута по имени маршрута (если оно было присвоено).
 */
function hleb_i245eaa1a3b6d_getByName(string $name, array $params = []) {
    return \Hleb\Constructor\Handlers\URL::getByName($name, $params);
}

/*
 * The getStandardUrl() function converts the URL address to its conventional form.
 *
 * Функция getStandardUrl() приводит URL-адрес к стандартному виду.
 */
function hleb_a1a3b6di245ea_getStandardUrl(string $name) {
    return \Hleb\Constructor\Handlers\URL::getStandardUrl($name);
}

/*
 * The includeTemplate( ... ) function enables to include the content of another template into the template and transfer parameters (variables).
 *
 * Функция includeTemplate( ... ) позволяет включить в шаблон контент из другого шаблона с передачей параметров (переменных).
 */
function hleb_e0b1036c1070101_template(string $template, array $params = [], bool $return = false) {
    return (new \Hleb\Main\MainTemplate($template, $params, $return))->getContent();
}

/*
 * The includeCachedTemplate( ... ) function enables to include the cashed content of another template into the template
 * and transfer parameters (variables).
 *
 * Функция includeCachedTemplate( ... ) позволяет включить в шаблон кешируемый контент из другого шаблона
 * с передачей параметров (переменных).
 */
function hleb_e0b1036c1070102_template(string $template, array $params = []) {
    new \Hleb\Constructor\Cache\CachedTemplate($template, $params);
}

/*
 * The includeOwnCachedTemplate( ... ) function enables to include the cashed content of another template
 * into the template and transfer cashed parameters (variables).
 *
 * Функция includeOwnCachedTemplate( ... ) позволяет включить в шаблон кешируемый контент из другого шаблона
 * с передачей кешируемых параметров (переменных).
 */
function hleb_ade9e72e1018c6_template(string $template, array $params = []) {
    new \Hleb\Constructor\Cache\OwnCachedTemplate($template, $params);
}

/*
 * The print_r2( ... ) function in DEBUG mode outputs the debugging data on top of the content.
 *
 * Функция print_r2( ... ) в DEBUG-режиме выводит отладочные данные поверх контента.
 */
function hleb_a581cdd66c107015_print_r2($data, $desc = null) {
    \Hleb\Main\WorkDebug::add($data, $desc);
}

/*
 * The getRequestResources() function enables to get style data for output of them on the page (in its lower part).
 *
 * Функция getRequestResources() для получения данных стилей для вывода на странице (в нижней её части).
 */
function hleb_ra3le00te0m01n_request_resources() {
    return \Hleb\Constructor\Handlers\Request::getResources();
}

/*
 * The <head><?php getRequestHead()->output(); ?></head> displays all previous set Request::getHead() data.
 *
 * <head><?php getRequestHead()->output(); ?></head> отображает все предыдущие установленные данные Request::getHead().
 */
function hleb_t0ulb902e69thp_request_head() {
    return \Hleb\Constructor\Handlers\Request::getHead();
}

/*
 * The getRequest( ... ) function outputs the class Request.
 *
 *  Через функцию getRequest( ... ) можно обращаться к классу Request. Например, getRequest()::getGet();
 */
function hleb_e70c10c1057hn11cc8il2_get_request() {
    return \Hleb\Constructor\Handlers\Request::class;
}

/*
 * Full path to folder '/storage/public'
 *
 * Полный путь к папке '/storage/public'
 */
function hleb_6iopl942e103te6i10600l_storage_path() {
    return hleb_system_storage_path('public');
}

/*
 * Full path to folder '/public'
 *
 * Полный путь к папке '/public'
 */
function hleb_10p134l66o0il0e0t92e6i_public_path() {
    return HLEB_PUBLIC_DIR;
}

/*
 * Full path to folder '/view'
 *
 * Полный путь к папке '/view'
 */
function hleb_601e30l60p2ii1e0o469tl_view_path() {
    return HLEB_GLOBAL_DIRECTORY . DIRECTORY_SEPARATOR . 'view';
}

// 404
function hleb_bt3e3gl60pg8h71e00jep901_error_404() {
    if (file_exists(hleb_601e30l60p2ii1e0o469tl_view_path() . DIRECTORY_SEPARATOR . '404.php')) {
        include hleb_601e30l60p2ii1e0o469tl_view_path() . DIRECTORY_SEPARATOR . '404.php';
    } else {
        include HLEB_GLOBAL_DIRECTORY . '/app/Optional/404.php';
    }
    // End of script execution before starting the main project.
    hl_preliminary_exit();
}


$GLOBALS['HLEB_PROJECT_UPDATES'] = ['phphleb/hleb' => HLEB_FRAME_VERSION, 'phphleb/framework' => HLEB_PROJECT_FULL_VERSION];

if (HLEB_PROJECT_DEBUG_ON && (new Hleb\Main\TryClass('XdORM\XD'))->is_connect() &&
    file_exists(HLEB_VENDOR_DIRECTORY . '/phphleb/xdorm')) {

    $GLOBALS['HLEB_PROJECT_UPDATES']['phphleb/xdorm'] = 'dev';
}
if (HLEB_PROJECT_DEBUG_ON && (file_exists(HLEB_VENDOR_DIRECTORY . '/phphleb/adminpan'))) {
    $GLOBALS['HLEB_PROJECT_UPDATES']['phphleb/adminpan'] = 'dev';
}


