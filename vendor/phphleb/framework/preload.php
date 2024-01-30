<?php
/**
 * By connecting this script to php.ini, you can achieve an increase
 * in the performance of the framework in opcache mode.
 *
 * Подключив этот скрипт в php.ini можно добиться прироста
 * производительности фреймворка в режиме opcache.
 *
 * ```
 *  opcache.preload=/path/to/project/vendor/phphleb/framework/preload.php
 * ```
 */

use Hleb\Init\Connectors\HlebConnector;

if (!class_exists(HlebConnector::class, false)) {
    $dir = realpath(__DIR__);
    include $dir . '/Init/Connectors/HlebConnector.php';

    $map = HlebConnector::$map;
    array_walk($map, function (&$path) use ($dir): void {
        $path = $dir . $path;
    });

    if (!function_exists('search_root')) {
        include $dir . '/Init/Connectors/Preload/search-functions.php';
    }
    $root = search_root();
    foreach (HlebConnector::$bootstrapMap as $file) {
        $map[] = $root . $file;
    }

    $routeDir = \realpath($root . '/storage/cache/routes');
    if (is_dir($routeDir)) {
        $map = array_merge($map, search_php_files($routeDir));
    }

    if (!function_exists('get_env')) {
        $map[] = $dir . '/Init/Review/basic.php';
    }
    if (!function_exists('hl_debug')) {
        $map[] = $dir . '/Init/Review/functions.php';
    }

    if (function_exists('opcache_compile_file')) {
        foreach ($map as $file) {
            @opcache_compile_file($file);
        }
    }
}
