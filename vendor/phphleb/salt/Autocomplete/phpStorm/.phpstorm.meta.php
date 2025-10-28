<?php

namespace PHPSTORM_META {

    registerArgumentsSet('phphleb_config_schemes',
        'common', 'database', 'main', 'system',
    );
    expectedArguments(\hl_config(), 0, argumentsSet('phphleb_config_schemes'));
    expectedArguments(\config(), 0, argumentsSet('phphleb_config_schemes'));
    expectedArguments(\Hleb\Static\Settings::getParam(), 0, argumentsSet('phphleb_config_schemes'));
    expectedArguments(\Hleb\Reference\SettingInterface::getParam(), 0, argumentsSet('phphleb_config_schemes'));
    expectedArguments(\Hleb\Reference\Interface\Setting::getParam(), 0, argumentsSet('phphleb_config_schemes'));

    registerArgumentsSet('phphleb_http_code_schemes',
        100, 101, 102,
        200, 201, 202, 203, 204, 205, 206, 207, 208,
        300, 301, 302, 303, 304, 305, 306, 307,
        400,401, 402, 403, 404, 405, 406, 407, 408, 409,
        410, 411, 412, 413, 414, 415, 416, 417, 418,
        422, 423, 424, 425, 426, 428, 429, 431, 451,
        500, 501, 502, 503, 504, 505, 506, 507, 508, 511
    );
    expectedArguments(\hl_redirect(), 1, argumentsSet('phphleb_http_code_schemes'));
    expectedArguments(\Hleb\Static\Redirect::to() , 1, argumentsSet('phphleb_http_code_schemes'));
    expectedArguments(\Hleb\Reference\RedirectInterface::to() , 1, argumentsSet('phphleb_http_code_schemes'));
    expectedArguments(\Hleb\Reference\Interface\Redirect::to() , 1, argumentsSet('phphleb_http_code_schemes'));

    expectedArguments(\view(), 2, argumentsSet('phphleb_http_code_schemes'));
    expectedArguments(\Hleb\Static\View::view() , 2, argumentsSet('phphleb_http_code_schemes'));
    expectedArguments(\Hleb\Reference\ViewInterface::view() , 1, argumentsSet('phphleb_http_code_schemes'));

    registerArgumentsSet('phphleb_special_path_schemes',
        '@global/',
            '@public/',
            '@storage/',
            '@resources/',
            '@app/',
            '@views/',
            '@modules/',
            '@vendor/',
            '@library/',
            '@framework/',
    );
    expectedArguments(\hl_file_exists(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Static\Path::exists(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\PathInterface::exists(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\PathInterface::createDirectory(), 0, argumentsSet('phphleb_special_path_schemes'));

    expectedArguments(\hl_file_get_contents(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Static\Path::contents(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\PathInterface::contents(), 0, argumentsSet('phphleb_special_path_schemes'));


    expectedArguments(\hl_file_put_contents(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Static\Path::put(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\PathInterface::put(), 0, argumentsSet('phphleb_special_path_schemes'));


    expectedArguments(\hl_is_dir(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Static\Path::isDir(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\PathInterface::isDir(), 0, argumentsSet('phphleb_special_path_schemes'));

    expectedArguments(\hl_realpath(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Static\Path::getReal(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\PathInterface::getReal(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Static\Settings::getRealPath(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\SettingInterface::getRealPath(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\SettingReference::getRealPath(), 0, argumentsSet('phphleb_special_path_schemes'));

    expectedArguments(\hl_path(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Static\Path::get(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\PathInterface::get(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Static\Settings::getPath(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\SettingInterface::getPath(), 0, argumentsSet('phphleb_special_path_schemes'));
    expectedArguments(\Hleb\Reference\SettingReference::getPath(), 0, argumentsSet('phphleb_special_path_schemes'));

    registerArgumentsSet('phphleb_log_schemes',
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug',
        'state',
    );
    expectedArguments(\Hleb\Main\Logger\LoggerInterface::log(), 0, argumentsSet('phphleb_log_schemes'));
    expectedArguments(\Hleb\Static\Log::log(), 0, argumentsSet('phphleb_log_schemes'));
    expectedArguments(\Hleb\Reference\LogInterface::log(), 0, argumentsSet('phphleb_log_schemes'));
    expectedArguments(\Hleb\Reference\LogReference::log(), 0, argumentsSet('phphleb_log_schemes'));

    registerArgumentsSet('phphleb_http_method_schemes',
        'GET', 'POST', 'DELETE', 'PUT', 'PATCH', 'OPTIONS', 'HEAD',
        'get', 'post', 'delete', 'put', 'patch', 'options', 'head',
    );
    expectedArguments(\Hleb\Static\Request::isMethod(), 0, argumentsSet('phphleb_http_method_schemes'));
    expectedArguments(\Hleb\Reference\RequestInterface::isMethod(), 0, argumentsSet('phphleb_http_method_schemes'));
    expectedArguments(\Hleb\Reference\RequestReference::isMethod(), 0, argumentsSet('phphleb_http_method_schemes'));

    expectedArguments(\url(), 3, argumentsSet('phphleb_http_method_schemes'));
    expectedArguments(\Hleb\Static\Route::url(), 3, argumentsSet('phphleb_http_method_schemes'));
    expectedArguments(\Hleb\Reference\RouteInterface::url(), 3, argumentsSet('phphleb_http_method_schemes'));
    expectedArguments(\Hleb\Reference\RouteReference::url(), 3, argumentsSet('phphleb_http_method_schemes'));

    expectedArguments(\address(), 3, argumentsSet('phphleb_http_method_schemes'));
    expectedArguments(\Hleb\Static\Route::address(), 3, argumentsSet('phphleb_http_method_schemes'));
    expectedArguments(\Hleb\Reference\RouteInterface::address(), 3, argumentsSet('phphleb_http_method_schemes'));
    expectedArguments(\Hleb\Reference\RouteReference::address(), 3, argumentsSet('phphleb_http_method_schemes'));
}
