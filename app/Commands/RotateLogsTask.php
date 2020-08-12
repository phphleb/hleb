<?php

/*
 * Task for cron (~ daily) or a separate run for log rotation (deleting).
 *
 * Задание для cron (~ ежедневно) или запуск вручную для ротации (удаления) логов.
 */

namespace App\Commands;

class RotateLogsTask extends \Hleb\Scheme\App\Commands\MainTask
{
    /** php console rotate-logs-task **/

    const DESCRIPTION = "Delete old logs";

    protected function execute() {
        // Delete earlier than this time in seconds.
        // Удаление ранее этого времени в секундах.
        $prescriptionForRotation = 60 * 60 * 24 * 3;

        $total = 0;
        $logs = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(HLEB_GLOBAL_DIRECTORY . "/storage/logs/")
        );
        foreach ($logs as $log) {
            if ($log->isFile() && $log->getFileName() !== ".gitkeep" && filemtime($log->getRealPath()) < (time() - $prescriptionForRotation)) {
                @unlink($log->getRealPath());
                $total++;
            }
        }
        echo "Deleted " . $total . " files";

        echo "\n" . __CLASS__ . " done." . "\n";
    }

}


