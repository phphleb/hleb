<?php

function hl_user_log(int $errno, string $errstr, string $errfile = null, int $errline = null)
{
    if (!error_reporting()) {
        return false;
    }
    $path = HLEB_VENDOR_DIRECTORY . '/phphleb/framework';
    require_once $path . '/Main/Insert/BaseSingleton.php';
    if (!class_exists('\Hleb\Main\Logger\Log', false)) {
        require $path . '/Scheme/Home/Main/LoggerInterface.php';
        require $path . '/Main/Logger/LogLevel.php';
        require $path . '/Main/Logger/Log.php';
        require $path . '/Main/Logger/FileLogger.php';
    }
    if (!class_exists('Hleb\Constructor\Handlers\Request')) {
        require $path . '/Constructor/Handlers/Request.php';
    }

    $log = \Hleb\Main\Logger\Log::getInstance();

    $params = [];
    if ($errfile) {
        $params['file'] = $errfile;
    }
    if ($errline) {
        $params['line'] = $errline;
    }
    switch ($errno) {
        case E_ERROR:
        case E_USER_ERROR:
        case E_PARSE:
        case E_COMPILE_ERROR:
        case E_CORE_ERROR:
        case E_RECOVERABLE_ERROR:
            $log->error($errstr, $params);
            headers_sent() or http_response_code (500);
            exit();
        case E_USER_NOTICE:
        case E_NOTICE:
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $log->notice($errstr, $params);
            break;
        default:
            $log->warning($errstr, $params);
            break;
    }

    return true;
}

set_error_handler('hl_user_log');

function hl_shutdown()
{
    if ($error = error_get_last() AND $error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR)) {
        hl_user_log(E_ERROR, $error['message'] ?? '', $error['file'] ?? null, $error['line'] ?? null);
    }
}

register_shutdown_function('hl_shutdown');


if (!function_exists('Logger')) {
    /**
     * Логирование по установленным уровням. Log()->error('Message', []);
     *
     * Logging according to the established levels. Log()->error('Message', []);
     *
     * @return Hleb\Main\Logger\Log
     */
    function Logger() {
        return \Hleb\Main\Logger\Log::getInstance();
    }
}