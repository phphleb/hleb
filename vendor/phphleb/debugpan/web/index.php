<?php

declare(strict_types=1);

use Hleb\HttpMethods\External\SystemRequest;
use Phphleb\Debugpan\Panel\Resources;

/**
 * @var SystemRequest $request
 */

// Should return true if the resource was found, or false if not found.
// Нужно вернуть true, если ресурс был найден, или false, если не обнаружен.
return (new Resources($request))->get();
