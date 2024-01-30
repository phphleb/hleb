<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits;


use Hleb\Route\Name;

trait InsertNameTrait
{
    /**
     * Defines the name of the route.
     *
     * Задаёт имя маршрута.
     */
    public function name(string $name): Name
    {
        return new Name($name);
    }
}
