<?php

namespace Hleb\Scheme\App\Commands;

class MainTask
{
    public function createTask($arguments) {
        if(method_exists($this, 'execute')) {
            $this->execute(...$arguments);
        } else {
            hleb_system_log(PHP_EOL . "Method 'execute' not exists in Task!" . PHP_EOL);
        }

    }

}

