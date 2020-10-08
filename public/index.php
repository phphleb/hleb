<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

// All calls are sent to this file.
// Все вызовы направляются в этот файл.

define('HLEB_START', microtime(true));
define('HLEB_FRAME_VERSION', "1.5.30");
define('HLEB_PUBLIC_DIR', __DIR__);


// General headers.
// Общие заголовки.

header("Referrer-Policy: no-referrer-when-downgrade");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
// ...

// Additional structural paths
// Дополнительные структурные пути
/*
define('HLEB_GLOBAL_DIRECTORY', realpath(__DIR__ . '/../'));
define('HLEB_SEARCH_START_CONFIG_FILE', HLEB_GLOBAL_DIRECTORY);
define('HLEB_SEARCH_DBASE_CONFIG_FILE', realpath(HLEB_GLOBAL_DIRECTORY . '/database'));
define('HLEB_STORAGE_DIRECTORY', realpath(HLEB_GLOBAL_DIRECTORY . '/storage'));
*/

// Initialization.
// Инициализация.

require __DIR__ . '/../vendor/phphleb/framework/bootstrap.php';

exit();


