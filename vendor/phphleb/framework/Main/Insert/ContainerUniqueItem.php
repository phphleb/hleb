<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Insert;

use Hleb\Static\DI;

/**
 *
 * A highly specialized class for standard singleton objects in a container.
 * The peculiarity of inherited classes is that they cannot be cloned.
 * Since this class is used in container singleton object classes,
 * then you can apply it to a class and also prohibit the creation
 * of an object of this class using Creator.
 *
 * Узкоспециальный класс для стандартных объектов-singletons в контейнере.
 * Особенностью унаследованных классов является то, что их нельзя клонировать.
 * Так как этот класс используется в классах объектов-singletons контейнера,
 * то можно применяя его к классу также запретить создание объекта этого класса
 * с помощью Creator.
 *
 * @see DI
 *
 * @internal
 */
class ContainerUniqueItem
{
    final protected function __clone()
    {
    }
}
