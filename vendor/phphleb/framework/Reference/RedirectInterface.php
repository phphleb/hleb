<?php

namespace Hleb\Reference;

interface RedirectInterface
{
    /**
     * Redirect to internal page or full URL.
     *
     * Редирект на внутреннюю страницу или полный URL.
     */
    public function to(string $location, int $status = 302): void;
}
