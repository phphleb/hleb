<?php

namespace Hleb\Reference;

interface DiInterface
{
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
    public function object(string $class, array $params = []): object;

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
    public function method(object $obj, string $method, array $params = []): mixed;
}
