<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use App\Bootstrap\ContainerInterface;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Reference\SettingInterface;

/**
 * The base class of the model, all Models must be inherited from it.
 *
 * Базовый класс модели, все Модели должны быть унаследованы от него.
 */
#[AvailableAsParent]
abstract class Model
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
        return \Hleb\Static\Container::getContainer();
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
