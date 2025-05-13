<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;


use Hleb\Route\Group\GroupPrefix;

trait GroupPrefixTrait
{
    /**
     * Set a prefix for a route group.
     * For example, for the prefix '/test/', if the group contains the route '/page/',
     * the address '/test/page/' for that route will be checked.
     *
     * Установка префикса к группе маршрутов.
     * Например, для префикса '/test/', если в группе находится маршрут '/page/',
     * будет проверяться адрес '/test/page/' для этого маршрута.
     *
     * ```php
     *  Route::toGroup()->prefix('/test/');
     *    // ... //
     *  Route::endGroup();
     * ```
     */
    public function prefix(string $prefix): GroupPrefix
    {
        return new GroupPrefix($prefix);

    }
}
