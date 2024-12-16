<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Containers\ContainerTrait;

/**
 * The base class for embedding and using containers in classes that inherit from it.
 * To inherit readonly classes, use ReadonlyContainer.
 *
 * Базовый класс для внедрения и использования контейнеров в наследуемых от него классах.
 * Для наследования readonly классов используйте ReadonlyContainer.
 */
#[AvailableAsParent]
abstract class Container
{
    use ContainerTrait;
}
