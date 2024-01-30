<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\Constructor\Data\SystemSettings;

/**
 * @internal
 */
final class LatestLogs
{
    /**
     * Returns the latest file log data.
     *
     * Возвращает данные последних файловых логов.
     */
   public function run(null|string $linesArg, null|string $fileArg): string
   {
       $lines = 3;
       $files = 5;
       if ($linesArg !== null) {
           $lines = ((int)$linesArg) ?: $lines;
       }
       if ($fileArg !== null) {
           $files = ((int)$fileArg) ?: $files;
       }
       $dir = SystemSettings::getPath('@storage/logs');
       $logs = [];
       foreach (\glob($dir . '/*') as $log) {
           if (!\is_dir($log) && !\str_ends_with($log, '_mail.log')) {
               $logs[$log] = \filemtime($log);
           }
       }
       \asort($logs);
       $files = \array_slice(\array_keys($logs), -$files, $files);
       if (!$files) {
           return 'Logs not found.' . PHP_EOL;
       }
       $data = [];
       foreach ($files as $file) {
           $data[] = \basename($file) . ':' . PHP_EOL;
           $rows = \array_slice(\file($file) ?: [], -$lines, $lines);
           \array_push($data, ...$rows);
       }
       return \implode(PHP_EOL, $data) . PHP_EOL;
   }
}
