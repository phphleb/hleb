<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\DI\DependencyInjection;
use Hleb\CoreErrorException;
use Hleb\Helpers\ReflectionMethod;
use Hleb\Main\Insert\ContainerUniqueItem;

#[Accessible] #[AvailableAsParent]
class DiReference extends ContainerUniqueItem implements DiInterface, Interface\DI
{
    private const MAX_CACHED_EVENTS = 1000;

    private static array $cachedEvents = [];

    /**
     * @inheritDoc
     */
    #[\Override]
    public function object(string $class, array $params = []): object
    {
        if (\is_subclass_of($class, ContainerUniqueItem::class)) {
            throw new CoreErrorException('You cannot create a class accessible from a container.');
        }

        $eventMethod = self::getEvent($class, '__construct');

        return new $class(...($eventMethod->countArgs() ? DependencyInjection::prepare($eventMethod, $params) : []));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function method(object $obj, string $method, array $params = []): mixed
    {
        $eventMethod = self::getEvent($obj::class, $method);

        return $obj->$method(...($eventMethod->countArgs() ? DependencyInjection::prepare($eventMethod, $params) : []));
    }

    private static function getEvent(string $class, string $method): ReflectionMethod
    {
        $tag = $class . ':' . $method;
        if (isset(self::$cachedEvents[$tag])) {
            return self::$cachedEvents[$tag];
        }
        if (\count(self::$cachedEvents) > self::MAX_CACHED_EVENTS) {
            \array_shift(self::$cachedEvents);
        }

        return self::$cachedEvents[$tag] = new ReflectionMethod($class, $method);
    }
}
