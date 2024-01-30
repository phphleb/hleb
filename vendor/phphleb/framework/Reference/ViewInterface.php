<?php

namespace Hleb\Reference;

use Hleb\Constructor\Data\View;

interface ViewInterface
{
    /**
     * @see view()
     */
    public function view(string $template, array $params = [], ?int $status = null): View;
}
