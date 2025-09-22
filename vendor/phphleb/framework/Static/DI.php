<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\DiInterface;

class DI extends BaseSingleton
{
    private static DiInterface|null $replace = null;

    /**
     * Creates a custom class with arguments from the container
     * substituted into the constructor.
     * If not present in the container, it can also substitute a suitable
     * class object into the argument, which can be created without using
     * unknown arguments (only default values or from container).
     *
     * Создает пользовательский класс с подстановкой
     * в конструктор аргументов из контейнера.
     * При отсутствии в контейнере может также подставить в аргумент подходящий объект
     * класса, который можно создать не используя неизвестные аргументы
     * (только значения по умолчанию или из контейнера).
     *
     * @template TCreatorInterface
     * @param class-string<TCreatorInterface> $class - the name of the created class.
     *                                               - название создаваемого класса.
     *
     * @param array $params - additional constructor parameters [name => value].
     *                      - дополнительные параметры конструктора [название => значение].
     *
     * @return TCreatorInterface
     */
    public static function object(string $class, array $params = []): object
    {
        if (self::$replace) {
            return self::$replace->object($class, $params);
        }

        return BaseContainer::instance()->get(DiInterface::class)->object($class, $params);
    }

    /**
     * Executes a method on an object, substituting arguments from the container.
     * If not present in the container, it can also substitute a suitable
     * class object into the argument, which can be created without using
     * unknown arguments (only default values or from container).
     *
     * Выполняет метод объекта с подстановкой аргументов из контейнера.
     * При отсутствии в контейнере может также подставить в аргумент подходящий объект
     * класса, который можно создать не используя неизвестные аргументы
     * (только значения по умолчанию или из контейнера).
     *
     * @param object $obj - the object on which the method is called.
     *                    - объект, у которого вызывается метод.
     *
     * @param string $method - name of the method.
     *                       - название метода.
     *
     * @param array $params - additional parameters of the method [name => value].
     *                      - дополнительные параметры метода [название => значение].
     *
     * @return mixed
     */
    public static function method(object $obj, string $method, array $params = []): mixed
    {
        if (self::$replace) {
            return self::$replace->method($obj, $method, $params);
        }

        return BaseContainer::instance()->get(DiInterface::class)->method($obj, $method, $params);
    }

    /**
     * @internal
     *
     * @see DiForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(DiInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
