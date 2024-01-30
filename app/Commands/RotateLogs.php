<?php

declare(strict_types=1);

/*
 * Task for cron (~ daily) or a separate run for log rotation (deleting).
 *
 * Задание для cron (~ ежедневно) или запуск вручную для ротации (удаления) логов.
 */

namespace App\Commands;

use Hleb\Base\Task;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RotateLogs extends Task
{
    /** php console rotate-logs [days] **/

    /**
     * Delete old logs.
     *
     * Удаление устаревших логов.
     *
     * @param int $days - delete earlier than this time in days.
     *                  - удаление ранее этого времени в днях.
     */
    protected function run(int $days = 3): int
    {
        $prescriptionForRotation = 60 * 60 * 24 * $days;
        $code = self::SUCCESS_CODE;
        $total = 0;
        $logs = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->settings()->getPath('logs')
            )
        );
        foreach ($logs as $log) {
            $logPath = $log->getRealPath();
            if(!is_writable($logPath)) {
                $user = @exec('whoami');
                echo "Permission denied! It is necessary to assign rights to the directory `sudo chmod -R 766 ./storage` and the current user " . ($user ? "`$user`" : '') . PHP_EOL;
                $code = self::ERROR_CODE;
                break;
            }
            if ($log->isFile() && $log->getFileName() !== ".gitkeep" && filemtime($logPath) < (time() - $prescriptionForRotation)) {
                unlink($log->getRealPath());
                $total++;
            }
        }
        echo "Deleted " . $total . " log files." . PHP_EOL;

        return $code;
    }
}
