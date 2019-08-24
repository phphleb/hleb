<?php

namespace App\Commands;

class RotateLogsTask extends \MainTask
{
    // php console rotate-logs-task

    const DESCRIPTION = "Delete old logs in storage/logs/";

    protected function execute($arg = null)
    {
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
        print "Deleted " . $total . " files";

        print "\n" . __CLASS__ . " done." . "\n";
    }

}


