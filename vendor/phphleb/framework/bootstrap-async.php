<?php

/**
 * For asynchronous code execution.
 *
 * Для асинхронного выполнения кода.
 */

use Hleb\Main\AsyncClearConnector;
use Hleb\Main\Errors\HlebExitException;
use Hleb\Main\ProjectLoader;

// Casting to the original global array.
foreach ($GLOBALS ?? [] as $key => $gv) { unset($GLOBALS[$key], $key, $gv); }

// For asynchronous counting.
$GLOBALS['HLEB_START'] = microtime(true);

//To set a different directory name 'vendor' add HLEB_VENDOR_DIR_NAME to the constants
defined('HLEB_VENDOR_DIRECTORY') or define('HLEB_VENDOR_DIRECTORY', defined('HLEB_VENDOR_DIR_NAME') ? HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIR_NAME : dirname(__DIR__, 2));

// End of script execution (before starting the main project).
if (!function_exists('hl_preliminary_exit')) {
    /**
     * @param string $text - message text.
     *
     * @throws HlebExitException
     * @internal
     */
    function hl_preliminary_exit($text = '') {
        if (!class_exists('Hleb\Main\Errors\HlebExitException', false)) {
            require HLEB_VENDOR_DIRECTORY . '/phphleb/framework/Main/Errors/HlebExitException.php';
        }
        throw new HlebExitException((string)$text);
    }
}

defined('HLEB_ASYNC_MODE') or define('HLEB_ASYNC_MODE', 1);

try {

    require 'preloader.php';

    AsyncClearConnector::clearAll();

    if (empty($radjaxIsActive)) {
        ProjectLoader::start();
    }

} catch (HlebExitException $exit) {
    echo $exit->getMessage();
}


