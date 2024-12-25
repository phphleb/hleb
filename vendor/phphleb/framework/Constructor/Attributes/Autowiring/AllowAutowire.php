<?php

namespace Hleb\Constructor\Attributes\Autowiring;

/**
 * The #[AllowAutowire] tag for a class indicates that it is AVAILABLE
 * for substitution during dependency injection.
 * Relevant only when a certain DI mode is enabled
 * in the configuration (disabled if this attribute is not present).
 * The condition does not apply to classes of services
 * located in the container.
 *
 * При помощи метки #[AllowAutowire] для класса указывается,
 * что он ДОСТУПЕН для подстановки при внедрении зависимостей.
 * Актуально только при включении определенного режима DI
 * в конфигурации (выключено, если нет этого атрибута).
 * Условие не касается классов находящихся в контейнере сервисов.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class AllowAutowire
{
}
