<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

/*
Все вызовы направляются в этот файл
*/

define('HLEB_START', microtime(true));

define('HLEB_PUBLIC_DIR', __DIR__);

define('HLEB_FRAME_VERSION', "1.0.2");


/*
В этом блоке опциональные настройки проекта
*/

//mb_internal_encoding("UTF-8");

// Общие заголовки
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: SAMEORIGIN");
// ...

// Демонстрационное перенаправление с "http" на "https"
define('HLEB_PROJECT_ONLY_HTTPS', false);

// Демонстрационное перенаправление URL с "www" на без "www" и обратно 0/1/2
define('HLEB_PROJECT_GLUE_WITH_WWW', 0);

// ...

/*
Инициализация
*/

include __DIR__ . '/../vendor/phphleb/framework/require.php';

if (file_exists(__DIR__ . '/../start.hleb.php')) {
    require __DIR__ . '/../start.hleb.php';
} else {
    require __DIR__ . '/../default.start.hleb.php';
}

exit();
