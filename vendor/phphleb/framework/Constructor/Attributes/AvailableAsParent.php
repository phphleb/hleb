<?php

namespace Hleb\Constructor\Attributes;

/**
 * Using the #[AvailableAsParent] tag for a framework class,
 * it is indicated that it is accessible from the outside
 * for classes in user code to inherit from it.
 * Inheritance depends on the purpose of a particular class.
 *
 * При помощи метки #[AvailableAsParent] для класса фреймворка
 * указывается, что он доступен извне для наследования от него
 * классов в пользовательском коде.
 * Наследование зависит от предназначения конкретного класса.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AvailableAsParent
{
}
