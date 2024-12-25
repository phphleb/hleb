<?php

namespace Hleb\Constructor\Attributes\Autowiring;

/**
 * The #[NoAutowire] label for a class indicates that
 * it is NOT AVAILABLE for dependency injection substitution.
 * Relevant only when a specific DI mode is enabled
 * in the configuration (enabled if this attribute is not present).
 * The condition does not apply to classes of services
 * located in the container.
 *
 * При помощи метки #[NoAutowire] для класса указывается,
 * что он НЕ ДОСТУПЕН для подстановки при внедрении зависимостей.
 * Актуально только при включении определенного режима DI
 * в конфигурации (включено, если нет этого атрибута).
 * Условие не касается классов находящихся в контейнере сервисов.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class NoAutowire
{
}
