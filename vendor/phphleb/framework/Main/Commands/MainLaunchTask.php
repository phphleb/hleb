<?php

namespace Hleb\Main\Commands;

class MainLaunchTask  extends \MainTask
{
    protected function everyMinute($cms = []) // XX:XX:00
    {
        $this->run($cms);
    }

    protected function everyHour($cms = []) // XX:00:00
    {
        if (date("i") == "00") $this->run($cms);

    }

    protected function everyDay($cms = []) // 00:00:00
    {
        if (date("H:i") == "00:00") $this->run($cms);
    }

    private function run($cms = [])
    {
        foreach ($cms as $cmd) {
            print shell_exec($cmd);
        }
    }
}

