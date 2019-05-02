<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

// All calls are sent to this file.
// Все вызовы направляются в этот файл.

define('HLEB_START', microtime(true));

define('HLEB_PUBLIC_DIR', __DIR__);

define('HLEB_FRAME_VERSION', "1.0.6");

// This block contains the optional project settings.
// В этом блоке опциональные настройки проекта.

//mb_internal_encoding("UTF-8");

// General headers
// Общие заголовки
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN");
// ...

// Demo redirection from "http" to "https".
// Демонстрационное перенаправление с "http" на "https".
define('HLEB_PROJECT_ONLY_HTTPS', false);

// Demo URL redirection from "www" to without "www" and back 0/1/2.
// Демонстрационное перенаправление URL с "www" на без "www" и обратно 0/1/2.
define('HLEB_PROJECT_GLUE_WITH_WWW', 0);

// Initialization.
// Инициализация.

include __DIR__ . '/../vendor/phphleb/framework/require.php';

if (file_exists(__DIR__ . '/../start.hleb.php')) {
    require __DIR__ . '/../start.hleb.php';
} else {
    require __DIR__ . '/../default.start.hleb.php';
}

exit();


