<?php

namespace App\Commands;

class RotateLogsTask extends \MainTask
{
    // php console rotate-logs-task

    const DESCRIPTION = "Delete old logs";

    protected function execute($arg = null)
    {
        // Задание для cron (~ ежедневно) или запуск вручную для ротирования логов

        $temp = 60 * 60 * 12 * 3; // 3 дня
        $count = 0;

        $logs = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(HLEB_GLOBAL_DIRECTORY . "/storage/logs/")
        );
        foreach ($logs as $pathname => $log) {
            if (!$log->isFile()) continue;

            if (!in_array(".gitkeep", explode(DIRECTORY_SEPARATOR, $log->getRealPath())) && filemtime($log->getRealPath()) < (time() - $temp)) {
                @unlink($log->getRealPath());
                $count++;
            }
        }
        print "Deleted " . $count . " files";

        print "\n" . __CLASS__ . " done." . "\n";
    }

}


