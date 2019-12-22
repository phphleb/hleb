<?php

class MainTask
{
    public function __construct(){ }

    public function create_task($arguments)
    {
        $this->execute(...$arguments);
    }

}

