<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Methods\Traits\Group;

use Hleb\Route\Group\GroupWhere;

trait GroupWhereTrait
{
    /**
     * Checking the dynamic parts of a route in a group
     * using a regular expression.
     *
     * Проверка динамических частей маршрута в группе
     * при помощи регулярного выражения.
     */
    public function where(array $rules): GroupWhere
    {
        return new GroupWhere($rules);
    }
}
