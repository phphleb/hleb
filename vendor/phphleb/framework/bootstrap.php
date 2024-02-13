<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

use Hleb\HlebBootstrap;

PHP_VERSION_ID < 80200 and exit('Current PHP version is ' . PHP_VERSION . ', required >= 8.2');

\class_exists(HlebBootstrap::class) or require __DIR__ . '/HlebBootstrap.php';

(new HlebBootstrap(HLEB_PUBLIC_DIR))->load();
