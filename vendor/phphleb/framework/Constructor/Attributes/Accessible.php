<?php

namespace Hleb\Constructor\Attributes;

/**
 * The #[Accessible] tag for a framework class indicates that
 * its methods or an instance of the class are available
 * for use in user code.
 * Use depends on the purpose of the class.
 *
 * При помощи метки #[Accessible] для класса фреймворка указывается,
 * что его методы или экземпляр класса доступны для использования
 * в пользовательском коде.
 * Использование зависит от предназначения класса.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Accessible
{
}
