<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\DI;

use App\Bootstrap\BaseContainer;
use App\Bootstrap\ContainerInterface;
use Hleb\Constructor\Attributes\Autowiring\AllowAutowire;
use Hleb\Constructor\Attributes\Autowiring\Config;
use Hleb\Constructor\Attributes\Autowiring\NoAutowire;
use Hleb\Helpers\ArrayHelper;
use Hleb\Helpers\AttributeHelper;
use Hleb\Helpers\ReflectionMethod;
use Hleb\ReflectionProcessException;
use Hleb\Constructor\Attributes\Autowiring\DI;
use Hleb\Static\Settings;
use Hleb\UnexpectedValueException;

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
        $diAttributes = $reflector->searchAttributes(DI::class);
        $configAttributes = $reflector->searchAttributes(Config::class);

        foreach ($list as $name => $types) {
            // Search the passed arguments for replacement.
            // Поиск в переданных аргументах для замены.
            if (\array_key_exists($name, $arguments)) {
                $result[$name] = $arguments[$name];
                continue;
            }

            // Search through a predefined attribute.
            // Поиск через предопределяющий атрибут.
            /** @var DI $attribute */
            $attribute = $diAttributes[$name] ?? null;
            if ($attribute) {
                $item = $attribute->classNameOrObject;
                if (\is_string($item)) {
                    /** @var string $item */
                    $container = $container ?? BaseContainer::instance();
                    $item = $container->get($item);
                    if ($item === null){
                        $item = self::create($attribute->classNameOrObject, $container);
                    }
                }
                $result[$name] = $item;
                continue;
            } else if ($configAttributes) {
                /** @var Config $attribute */
                $attribute = $configAttributes[$name] ?? null;
                if ($attribute) {
                    $result[$name] = Settings::getParam($attribute->name, $attribute->key);
                    continue;
                }
            }

            foreach ($types as $type) {
                if (\in_array(\strtolower($type), self::EXCLUDED)) {
                    continue;
                }
                $container = $container ?? BaseContainer::instance();
                $result[$name] = $container->get($type);
                if ($result[$name] !== null) {
                    continue 2;
                }
                // Search for a resource through the class loader.
                // Поиск ресурса через загрузчик классов.
                try {
                    if (\class_exists($type)) {
                        $result[$name] = self::create($type, $container);
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

    /**
     * Checking the class for the possibility of automatic substitution.
     *  0, null - attempt to resolve all dependencies automatically.
     *  1 - do not try to resolve dependencies automatically.
     *  2 - similar to item 0, except for classes with the #[NoAutowire].
     *  3 - similar to step 1, except for classes with the #[AllowAutowire].
     *
     * Проверка класса на возможность автоматической подстановки.
     *  0, null - попытка разрешить все зависимости автоматически.
     *  1 - не пытаться разрешать зависимости автоматически.
     *  2 - аналогично п.0, кроме классов с атрибутом #[NoAutowire].
     *  3 - аналогично п.1, кроме классов с атрибутом #[AllowAutowire].
     */
    private static function checkAutowired(string|object $class, ?int $mode): bool
    {
        \is_object($class) and $class = $class::class;

        $isAllow = match ($mode) {
            1, 3 => false,
            0, 2, null => true,
            default => throw new UnexpectedValueException('Unsupported mode number'),
        };

        if ($mode === 2 || $mode === 3) {
            $attribute = $mode === 2 ? NoAutowire::class : AllowAutowire::class;
            if ((new AttributeHelper($class))->hasClassAttribute($attribute)) {
                $isAllow = !$isAllow;
            }
        }
        return $isAllow;
    }

    /**
     * Creating a class object with dependency injection.
     *
     * Создание объекта класса с внедрением зависимостей.
     */
    private static function create(string|object $class, ContainerInterface $container): ?object
    {
        if (self::checkAutowired($class, $container->settings()->getParam('system', 'autowiring.mode'))) {
            if (\method_exists($class, '__construct')) {
                $ref = new ReflectionMethod($class, '__construct');
                return new $class(...($ref->countArgs() ? self::prepare($ref) : []));
            } else {
                return new $class();
            }
        }
        return null;
    }
}
