<?php

/*
 * Task for cron (~ daily) or a separate run for log rotation (deleting).
 *
 * Задание для cron (~ ежедневно) или запуск вручную для ротации (удаления) логов.
 */

namespace App\Commands;

class RotateLogsTask extends \Hleb\Scheme\App\Commands\MainTask
{
    /** php console rotate-logs-task [arg] **/

    const DESCRIPTION = "Delete old logs";

    /**
     * Delete earlier than this time in days.
     * Удаление ранее этого времени в днях.
     * @param string|int $days
     */
    protected function execute($days = 3) {
        $prescriptionForRotation = 60 * 60 * 24 * (int)$days;

        $total = 0;
        $logs = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                realpath(hleb_system_storage_path('logs'))
            )
        );
        foreach ($logs as $log) {
            $logPath = $log->getRealPath();
            if(!is_writable($logPath)) {
                $user = @exec('whoami');
                echo "Permission denied! It is necessary to assign rights to the directory `sudo chmod -R 775 ./storage` and the current user " . ($user ? "`{$user}`" : '') . PHP_EOL;
                break;
            }
            if ($log->isFile() && $log->getFileName() !== ".gitkeep" && filemtime($logPath) < (time() - $prescriptionForRotation)) {
                unlink($log->getRealPath());
                $total++;
            }
        }
        echo "Deleted " . $total . " files";

        echo PHP_EOL . __CLASS__ . " done." . PHP_EOL;
    }

}


