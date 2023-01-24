<?php

use Hleb\Main\ProjectLoader;

//To set a different directory name 'vendor' add HLEB_VENDOR_DIR_NAME to the constants
define('HLEB_VENDOR_DIRECTORY', defined('HLEB_VENDOR_DIR_NAME') ? HLEB_GLOBAL_DIRECTORY . '/' . HLEB_VENDOR_DIR_NAME : dirname(__DIR__, 2));

define('HLEB_ASYNC_MODE', 0);

require 'preloader.php';

if (empty($radjaxIsActive)) {
    ProjectLoader::start();
}



