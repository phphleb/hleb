<?php

declare(strict_types=1);

namespace Hleb\Constructor;

class VCreator
{
    function __construct()
    {
        $data = hleb_to0me1cd6vo7gd_data();
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function view(string $include)
    {
        extract(hleb_to0me1cd6vo7gd_data());

        require $include;

    }
}

