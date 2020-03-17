<?php

declare(strict_types=1);


class_alias('Hleb\Constructor\Handlers\Request', 'Request');

class_alias('Hleb\Scheme\App\Controllers\MainController', 'MainController');

class_alias('Hleb\Scheme\App\Middleware\MainMiddleware', 'MainMiddleware');

class_alias('Hleb\Scheme\App\Models\MainModel', 'MainModel');

class_alias('Hleb\Constructor\Routes\Route', 'Route');

class_alias('Hleb\Main\WorkDebug', 'WorkDebug');

function view($to, $data = null)
{
    return hleb_v5ds34hop4nm1d_page_view($to, $data);
}

function render($name, $data = null)
{
    return hleb_v10s20hdp8nm7c_render($name, $data);
}

function data()
{
    return hleb_to0me1cd6vo7gd_data();
}

function csrf_field()
{
    echo hleb_ds5bol10m0bep2_csrf_field();
}

function csrf_token()
{
    return hleb_c3dccfa0da1a3e_csrf_token();
}

function storage($to, $data = null)
{
    return hleb_hol6h1d32sm0l1of_storage($to, $data);
}

function redirectToSite($url)
{
    hleb_ba5c9de48cba78c_redirectToSite($url);
}

function redirect(string $url, int $code = 303)
{
    hleb_ad7371873a6ad40_redirect($url, $code);
}

function getProtectUrl($url)
{
    return hleb_ba5c9de48cba78c_getProtectUrl($url);
}

function getFullUrl($url)
{
    return hleb_e0b1036cd5b501_getFullUrl($url);
}

function getMainUrl()
{
    return hleb_e2d3aeb0253b7_getMainUrl();
}

function getMainClearUrl()
{
    return hleb_daa581cdd6323_getMainClearUrl();
}

function getUrlByName($name, $args=[])
{
    return hleb_i245eaa1a3b6d_getByName($name, $args);
}

function getStandardUrl(string $name)
{
    return hleb_a1a3b6di245ea_getStandardUrl($name);
}

function print_r2($data, $desc = null)
{
    hleb_a581cdd66c107015_print_r2($data, $desc);
}

function includeTemplate(string $template, array $params = [])
{
    hleb_e0b1036c1070101_template($template, $params);
}

function includeCachedTemplate(string $template, array $params = [])
{
    hleb_e0b1036c1070102_template($template, $params);
}

function includeOwnCachedTemplate(string $template, array $params = [])
{
    hleb_ade9e72e1018c6_template($template, $params);
}

function getRequestResources()
{
    return hleb_ra3le00te0m01n_request_resources();
}

function getRequestHead()
{
    return hleb_t0ulb902e69thp_request_head();
}

function getRequest()
{
    return hleb_e70c10c1057hn11cc8il2_get_request();
}


