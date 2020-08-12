<?php

namespace App\Commands;

class RotateLogsTask extends \Hleb\Scheme\App\Commands\MainTask
{
    // php console rotate-logs-task

    const DESCRIPTION = "Delete old logs";

    protected function execute()
    {

        // Task for cron (~ daily) or a separate run for log rotation
        // Задание для cron (~ ежедневно) или запуск вручную для ротирования логов

        $temp = 60 * 60 * 24 * 3; // delete all < 3 days

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


