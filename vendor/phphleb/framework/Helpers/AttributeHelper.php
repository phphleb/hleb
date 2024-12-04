<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\DynamicStateException;

#[Accessible]
final class AttributeHelper
{
    private static array $cacheRefClass = [];

    private static array $cacheRefMethod = [];

    public function __construct(private readonly string $className)
    {
    }

    /**
     * Retrieving class attribute data (like #[\ExampleAttribute]).
     * Attribute classes must be accessible to the class loader
     * because the attribute array name => attribute object is returned.
     *
     * Получение данных атрибутов класса (вида #[\ExampleAttribute]).
     * Классы атрибутов должны быть доступны для загрузчика классов,
     * так как возвращается массив атрибутов название => объект атрибута.
     */
    public function getFromClass(): array
    {
        if (\array_key_exists($this->className, self::$cacheRefClass)) {
            return self::$cacheRefClass[$this->className];
        }
        $result = [];
        foreach ($this->getRefClass()->getAttributes() as $attribute) {
            $result[$attribute->getName()] = $attribute->newInstance();
        }

        return self::$cacheRefClass[$this->className] = $result;
    }

    /**
     * Getting attribute data for a class method.
     * Attribute classes must be accessible to the class loader
     * because the attribute array name => attribute object is returned.
     *
     * Получение данных аттрибутов для метода класса.
     * Классы атрибутов должны быть доступны для загрузчика классов,
     * так как возвращается массив атрибутов название => объект атрибута.
     */
    public function getFromMethod(string $methodName): array
    {
        $tag = $this->className . ':' . $methodName;
        if (array_key_exists($tag, self::$cacheRefMethod)) {
            return self::$cacheRefMethod[$tag];
        }
        $refMethod = $this->getRefMethod($methodName);
        foreach ($refMethod->getAttributes() as $attribute) {
            if ($attribute->getName() !== 'Override') {
                self::$cacheRefMethod[$tag][$attribute->getName()] = $attribute->newInstance();
            }
        }

        return self::$cacheRefMethod[$tag];
    }

    /**
     * Checking if an attribute is present on a class.
     * Attribute classes must be accessible to the class loader.
     *
     * Проверка наличия атрибута у класса.
     * Классы атрибутов должны быть доступны для загрузчика классов.
     */
    public function hasClassAttribute(string $name): bool
    {
        return \array_key_exists($name, $this->getFromClass());
    }

    /**
     * Getting the value of a specific argument from a method attribute by name.
     * Attribute classes must be accessible to the class loader.
     *
     * Получение значения конкретного аргумента у атрибута класса по названию.
     * Классы атрибутов должны быть доступны для загрузчика классов.
     */
    public function getClassValue(string $attribute, string $name): mixed
    {
        $attributes = $this->getFromClass();
        if (\array_key_exists($attribute, $attributes) && isset($attributes[$attribute]?->$name)) {
            return $attributes[$attribute]->$name;
        }
        return null;
    }

    /**
     * Checking if an attribute is present on a class.
     *
     * Проверка наличия атрибута у метода.
     */
    public function hasMethodAttribute(string $method, string $name): bool
    {
        return \array_key_exists($name, $this->getFromMethod($method));
    }

    /**
     * Getting the value of a specific argument from a method attribute by name.
     *
     * Получение значения конкретного аргумента у атрибута метода по названию.
     */
    public function getMethodValue(string $method, string $attribute, string $name): mixed
    {
        $attributes = $this->getFromMethod($method);
        if (\array_key_exists($attribute, $attributes) && isset($attributes[$attribute]?->$name)) {
            return $attributes[$attribute]->$name;
        }
        return null;
    }

    /**
     * Getting reflection for a class.
     *
     * Получение рефлексии для класса.
     */
    private function getRefClass(): \ReflectionClass
    {
        try {
            return new \ReflectionClass($this->className);
        } catch (\ReflectionException $e) {
            throw new DynamicStateException($e);
        }
    }

    /**
     * Getting reflection for a class method.
     *
     * Получение рефлексии для метода класса.
     */
    private function getRefMethod(string $methodName): \ReflectionMethod
    {
        try {
            return $this->getRefClass()->getMethod($methodName);
        } catch (\ReflectionException $e) {
            throw new DynamicStateException($e);
        }
    }
}
