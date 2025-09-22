<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Insert;

use Hleb\Constructor\Attributes\AvailableAsParent;

#[AvailableAsParent]
class OpenInstanceSingleton extends BaseSingleton
{
    /**
     * Allows you to get a reference to an instance of a class.
     *
     * Позволяет получить ссылку на экземпляр класса.
     */
    final public static function instance(): static
    {
        return self::getInstance();
    }
}
