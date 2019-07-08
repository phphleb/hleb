<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

// All calls are sent to this file.
// Все вызовы направляются в этот файл.

define('HLEB_START', microtime(true));

define('HLEB_PUBLIC_DIR', __DIR__);

define('HLEB_FRAME_VERSION', "1.1.5");

// This block contains the optional project settings.
// В этом блоке опциональные настройки проекта.

//mb_internal_encoding("UTF-8");

// General headers
// Общие заголовки
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN");
// ...


// Initialization.
// Инициализация.

include __DIR__ . '/../vendor/phphleb/framework/require.php';

if (file_exists(__DIR__ . '/../start.hleb.php')) {
    require __DIR__ . '/../start.hleb.php';
} else {
    require __DIR__ . '/../default.start.hleb.php';
}

exit();


