<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

// All calls are sent to this file.
// Все вызовы направляются в этот файл.

define('HLEB_START', microtime(true));
define('HLEB_FRAME_VERSION', "1.6.69");
define('HLEB_PUBLIC_DIR', __DIR__);


// General headers.
// Общие заголовки.

header("Referrer-Policy: no-referrer-when-downgrade");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
// ...

// Initialization.
// Инициализация.

require __DIR__ . '/../vendor/phphleb/framework/bootstrap.php';

exit();


