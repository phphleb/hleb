<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\Constructor\Data\SystemSettings;
use Hleb\Static\Log;

/**
 * @internal
 */
final class LockProject
{
    /**
     * Blocking / unblocking the work of routes (project).
     *
     * Блокировка/разблокировка работы маршрутов (проекта).
     */
   public function run(bool $status): string
   {
       $dir = SystemSettings::getRealPath('storage');
       $file = $dir . '/cache/routes/lock-status.info';
       \hl_create_directory($file);
       \file_put_contents($file, (int)$status);
       @\chmod($file, 0664);
       $message = $status ? 'PROJECT LOCKED!' : 'Project unlocked.';

       Log::info($message);

       return $message . PHP_EOL;
   }
}
