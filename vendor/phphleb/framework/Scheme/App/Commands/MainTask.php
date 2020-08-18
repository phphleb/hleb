<?php

namespace Hleb\Scheme\App\Commands;

class MainTask
{
    public function __construct() {}

    public function createTask($arguments) {
        $this->execute(...$arguments);
    }

}

