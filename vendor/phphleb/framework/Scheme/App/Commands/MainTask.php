<?php

namespace Hleb\Scheme\App\Commands;

class MainTask
{
    public function __construct(){ }

    public function create_task($arguments)
    {
        $this->execute(...$arguments);
    }

}

