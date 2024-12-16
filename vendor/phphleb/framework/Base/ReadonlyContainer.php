<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Containers\ContainerTrait;

/**
 * The base readonly class for embedding and using containers in readonly classes that inherit from it.
 * To inherit non-readonly classes, use Container.
 *
 * Базовый readonly класс для внедрения и использования контейнеров в наследуемых от него readonly классах.
 * Для наследования не-readonly классов используйте Container.
 */
#[AvailableAsParent]
abstract readonly class ReadonlyContainer
{
   use ContainerTrait;
}
