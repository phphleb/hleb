<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\DI;

use App\Bootstrap\BaseContainer;
use App\Bootstrap\ContainerInterface;
use Hleb\Helpers\ArrayHelper;
use Hleb\Helpers\ReflectionMethod;
use Hleb\ReflectionProcessException;

/**
 * @internal
 */
final class DependencyInjection
{
    private const EXCLUDED = ['bool', 'boolean', 'int', 'integer', 'float', 'double', 'string', 'array', 'object', 'callable', 'mixed', 'resource', 'null'];

    /**
     * Returns a named array of parameters with values, if any can be created from the container
     * or substituted from existing $arguments parameters.
     *
     * Возвращает именованный массив параметров со значениями, если таковые можно создать из контейнера
     * или подставить из существующих параметров $arguments.
     */
    public static function prepare(ReflectionMethod $reflector, array $arguments = [], ?ContainerInterface $container = null): array
    {
        $result = [];
        if ($arguments && !ArrayHelper::isAssoc($arguments)) {
            throw new ReflectionProcessException("The array of wildcard elements must be associative.");
        }
        $defaults = $reflector->getArgDefaultValueList();
        $list = $reflector->getArgTypeList();

        foreach ($list as $name => $types) {
            if (\array_key_exists($name, $arguments)) {
                $result[$name] = $arguments[$name];
                continue;
            }
            foreach ($types as $type) {
                if (\in_array(\strtolower($type), self::EXCLUDED)) {
                    continue;
                }
                $result[$name] = $container ? $container->get($type) : BaseContainer::instance()->get($type);
                if ($result[$name] !== null) {
                    continue 2;
                }
                // Search for a resource through the class loader.
                // Поиск ресурса через загрузчик классов.
                try {
                    if (\class_exists($type)) {
                        if (\method_exists($type, '__construct')) {
                            $ref = new ReflectionMethod($type, '__construct');
                            $result[$name] = new $type(...($ref->countArgs() ? self::prepare($ref) : []));
                        } else {
                            $result[$name] = new $type();
                        }
                    }
                } catch (\Throwable) {
                }
                if (!empty($result[$name])) {
                    continue 2;
                }

                if (\array_key_exists($name, $defaults)) {
                    $result[$name] = $defaults[$name];
                    continue 2;
                }
                $target = $reflector->getClassName() . ':' . $reflector->getMethodName();

                throw new ReflectionProcessException("No wildcard element found for $target parameter `$name`.");
            }
        }
        return $result;
    }
}
