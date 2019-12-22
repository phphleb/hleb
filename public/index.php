<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

// All calls are sent to this file.
// Все вызовы направляются в этот файл.

define('HLEB_START', microtime(true));

define('HLEB_PUBLIC_DIR', __DIR__);

define('HLEB_FRAME_VERSION', "1.3.0");

// Initialization.
// Инициализация.

require __DIR__ . '/../vendor/phphleb/framework/bootstrap.php';

exit();


