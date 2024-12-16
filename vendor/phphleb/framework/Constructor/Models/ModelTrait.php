<?php

declare(strict_types=1);

namespace Hleb\Constructor\Models;

use App\Bootstrap\ContainerInterface;
use Hleb\Reference\SettingInterface;
use Hleb\Static\Container;

trait ModelTrait
{
    /**
     * Get the model container directly for static methods.
     * Can be called like this:
     *
     * Прямое получение контейнера модели для статических методов.
     * Может быть вызван так:
     *
     * self::container()->db() / self::container()->get(DbInterface::class)
     */
    final protected static function container(): ContainerInterface
    {
        return Container::getContainer();
    }

    /**
     * Returns the framework settings from the container.
     *
     * Возвращает настройки фреймворка из контейнера.
     */
    final protected static function settings(): SettingInterface
    {
        return self::container()->get(SettingInterface::class);
    }
}
