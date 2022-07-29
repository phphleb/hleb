<?php

namespace Hleb\Scheme\App\Middleware;

use Hleb\Scheme\App\Controllers\BaseController;

class MainMiddleware extends BaseController
{
    /**
     * Parameter to use only for class testing.
     *
     * Параметр использовать только для тестирования класса.
     *
     * @param array|null $data
     */
    function __construct(array $data = null)
    {
        parent::__construct($data);
    }
}

