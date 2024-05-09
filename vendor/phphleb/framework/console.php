<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

use Hleb\HlebBootstrap;
use Hleb\HlebConsoleBootstrap;

PHP_VERSION_ID < 80200 and exit('Current PHP version is ' . PHP_VERSION . ', required >= 8.2' . PHP_EOL);

HLEB_PUBLIC_DIR or exit('Error! The public directory in the \'/console\' file is incorrectly specified.');

\class_exists(HlebBootstrap::class) or require __DIR__ . '/HlebBootstrap.php';
\class_exists(HlebConsoleBootstrap::class) or require __DIR__ . '/HlebConsoleBootstrap.php';

exit((new HlebConsoleBootstrap(HLEB_PUBLIC_DIR))->load());
