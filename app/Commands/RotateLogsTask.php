<?php

namespace App\Commands;

class RotateLogsTask extends \MainTask
{
    // php console rotate-logs-task

    const DESCRIPTION = "Delete old logs";

    protected function execute($arg = null)
    {
        // Task for cron (~ daily) or a separate run for log rotation
        // Задание для cron (~ ежедневно) или запуск вручную для ротирования логов

        $temp = 60 * 60 * 12 * 3; // 3 дня
        $total = 0;

        $logs = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(HLEB_GLOBAL_DIRECTORY . "/storage/logs/")
        );
        foreach ($logs as $log) {

            if ($log->isFile() && $log->getFileName() !== ".gitkeep" && filemtime($log->getRealPath()) < (time() - $temp)) {
                @unlink($log->getRealPath());
                $total++;
            }
        }
        echo "Deleted " . $total . " files";

        echo "\n" . __CLASS__ . " done." . "\n";
    }

}


