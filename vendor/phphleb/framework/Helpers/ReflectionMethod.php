<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\ReflectionProcessException;
use ReflectionException;
use ReflectionParameter;

/**
 * A set of methods for working with the parameters of a specific class method.
 *
 * Набор методов для работы с параметрами конкретного метода класса.
 */
#[Accessible]
final class ReflectionMethod
{
    private \ReflectionMethod $method;

    private array $params;

    private ?array $returnTypes = null;

    private ?array $defaultValuesList = null;

    private ?array $typeList = null;

    private ?array $nameList = null;

    public function __construct(
        private readonly string $className,
        private readonly string $methodName,
    )
    {
        try {
            $this->method = (new \ReflectionClass($className))->getMethod($methodName);
        } catch (\ReflectionException $e) {
            throw new ReflectionProcessException($e);
        }
        $this->params = $this->method->getParameters();
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * Returns a list of argument names.
     *
     * Возвращает список названий аргументов.
     */
    public function getArgNameList(): array
    {
        if ($this->nameList !== null) {
            return $this->nameList;
        }
        $names = [];
        foreach ($this->params as $value) {
            $names[] = $value->getName();
        }

        return $this->nameList = $names;
    }

    /**
     * Returns a list of types for arguments.
     *
     * Возвращает список типов для аргументов.
     */
    public function getArgTypeList(): array
    {
        if ($this->typeList !== null) {
            return $this->typeList;
        }
        $methodList = [];
        /** @var ReflectionParameter|null $value */
        foreach ($this->params as $value) {
            if (!$value) {
                continue;
            }

            $types = $value->getType();
            if (!$types) {
                continue;
            }
            $name = $value->getName();
            if (\method_exists($types, 'getTypes')) {
                foreach ($types->getTypes() as $type) {
                    $methodList[$name][] = $type->getName();
                }
            } else if (\method_exists($types, 'getType')) {
                $methodList[$name][] = $types->getType()->getName();
            } else {
                $methodList[$name][] = $types->getName();
            }
            if ($types->allowsNull()) {
                $methodList[$name][] = 'null';
            }
            $methodList[$name] = \array_unique($methodList[$name]);
        }
        return $this->typeList = $methodList;
    }

    /**
     * Checking the existence of parameters.
     *
     * Возвращает количество аргументов метода.
     */
    public function countArgs(): int
    {
        return \count($this->params);
    }

    /**
     * Returns the text of the block's phpDoc, or false if there is none.
     *
     * Возвращает текст phpDoc блока или false при его отсутствии.
     */
    public function getDocComment(): string
    {
        $result = [];
        foreach(\preg_split("/\r\n|\r|\n/", (string)$this->method->getDocComment()) as $str) {
            $result[]= \trim(\ltrim($str, '/* '));
        }
        return \implode(PHP_EOL, $result);
    }

    /**
     * Returns the first line from the phpDoc method, or false if it is not present.
     *
     * Возвращает первую строчку из phpDoc метода или false при его отсутствии.
     */
    public function getFirstLineInDocComment(): string
    {
        $doc = \explode(PHP_EOL, $this->getDocComment());

        return \trim($doc[0] ?: $doc[1] ?? '') . PHP_EOL;
    }

    /**
     * Returns a list of default values (if any) for the arguments.
     *
     * Возвращает список значений по умолчанию (если они установлены) для аргументов.
     */
    public function getArgDefaultValueList(): array
    {
        if ($this->defaultValuesList !== null) {
            return $this->defaultValuesList;
        }
        $methodList = [];
        foreach ($this->params as $param) {
            if ($param->isOptional()) {
                try {
                    $methodList[$param->getName()] = $param->getDefaultValue();
                } catch(ReflectionException $e) {
                    throw new ReflectionProcessException($e);
                }
            }
        }

        return $this->defaultValuesList = $methodList;
    }

    /**
     * Returns a list of method return types.
     *
     * Возвращает перечень возвращаемых типов метода.
     */
    public function getArgReturnTypesList(): array
    {
        if ($this->returnTypes !== null) {
            return $this->returnTypes;
        }
        $returnTypes = [];
        $types = $this->method->getReturnType();
        if (!$types) {
            return $this->returnTypes = [];
        }
        if (\method_exists($types, 'getTypes')) {
            $listTypes = $types->getTypes();
            foreach ($listTypes as $type) {
                $returnTypes[] = $type->getName();
            }
        } else {
            $returnTypes = [$types->getName()];
        }
        if ($types->allowsNull()) {
            $returnTypes[] = 'null';
        }

        return $this->returnTypes = \array_unique($returnTypes);
    }

    /**
     * Returns the result of error checking for arguments.
     * If there are no errors, it returns false, otherwise an array of names in which errors occurred.
     *
     * Возвращает результат проверки на ошибки для аргументов.
     * Если ошибок нет возвращает false, иначе массив названий, в которых произошли ошибки.
     *
     * @param array $data - array of substituted arguments name => value.
     *                     - массив подставляемых аргументов название => значение.
     *
     * @param array $favorites - when specifying a list of names, it checks only for them.     *
     *                         - при указании списка названий производит проверку только для них.
     */
    public function getErrorInArguments(array $data, array $favorites = []): false|array
    {
        $cells = [];
        $arguments = $this->getArgNameList();
        $default = $this->getArgDefaultValueList();
        foreach ($arguments as $arg) {
            $value = $data[$arg] ?? null;
            if ($value === null) {
                if (\array_key_exists($arg, $default)) {
                    continue;
                }
                if ($favorites && !in_array($arg, $favorites)) {
                    continue;
                }
                $cells[] = $arg;
            }
        }

        return \count($cells) ? $cells : false;
    }

    /**
     * Converts the named array data to the appropriate set of arguments
     * for the given class method.
     *
     * Преобразует данные именованного массива в подходящий набор аргументов
     * для данного метода класса.
     *
     * @param array $data - array of substituted arguments name => value.
     *                    - массив подставляемых аргументов название => значение.
     *
     * @param array $favorites - when specifying a list of names, leaves only them
     *                           and the default argument data in the resulting array.
     *
     *                         - при указании списка названий оставляет в результирующем
     *                           массиве только их и дефолтные данные аргументов.
     *
     * @return array|false - false if the data does not match, otherwise an array
     *                       with the final arguments name => value.
     *
     *                     - false при не совпадении данных иначе массив
     *                       с итоговыми аргументами название => значение.
     */
    public function convertArguments(array $data, array $favorites = []): array|false
    {
        $result = [];
        $arguments = $this->getArgNameList();
        $types = $this->getArgTypeList();
        $default = $this->getArgDefaultValueList();
        foreach ($arguments as $arg) {
            $value = $data[$arg] ?? null;
            // If the value of the argument is missing.
            // Если отсутствует значение аргумента.
            if ($value === null) {
                // If the argument has a default value.
                // Если у аргумента есть значение по умолчанию.
                if (\array_key_exists($arg, $default)) {
                    $result[$arg] = $default[$arg];
                    continue;
                }
                if ($favorites && !in_array($arg, $favorites)) {
                    continue;
                }
                return false;
            }
            $value = (string)$value;

            // If the type of the argument is defined.
            // Если тип аргумента определён.
            if (isset($types[$arg])) {
                $t = $types[$arg];
                if (\is_numeric($value)) {
                    if (\str_contains($value, '.')) {
                        if (\in_array('double', $t, true) || \in_array('float', $t, true)) {
                            $result[$arg] = (float)$value;
                            continue;
                        }
                    } else if (\in_array('int', $t, true) || \in_array('integer', $t, true)) {
                        $result[$arg] = (int)$value;
                        continue;
                    }
                }
                if (\in_array('mixed', $t, true) || \in_array('string', $t, true)) {
                    $result[$arg] = $value;
                    continue;
                }
                return false;
            }

            if (\is_numeric($value)) {
                $result[$arg] = \str_contains($value, '.') ? (float)$value : (int)$value;
            } else {
                $result[$arg] = $value;
            }
        }
        return $result;
    }

    /**
     * For method attributes, returns objects of these attributes by method name in an array.
     * If there are identical attributes, then only the first object will be returned.
     *
     * Возвращает для атрибутов метода объекты этих атрибутов по названию метода в массиве.
     * Если есть одинаковые атрибуты, то будет возвращён только первый объект.
     *
     * @template T
     * @param class-string<T> $class - name of the attribute class to search for.
     *                               - название класса атрибута для поиска.
     *
     * @return array<string, T> - an array of instances of the specified attribute.
     *                          - массив экземпляров указанного атрибута.
     */
    public function searchAttributes(string $class): array
    {
        $result = [];

        foreach ($this->params as $parameter) {
            $attribute = \current($parameter->getAttributes($class));
            if ($attribute) {
                $result[$parameter->getName()] = $attribute->newInstance();
            }
        }
        return $result;
    }

    /**
     * For method attributes, returns objects of these attributes by method name in an array.
     * If there are identical attributes, then all options will be present.
     *
     * Возвращает для атрибутов метода объекты этих атрибутов по названию метода в массиве.
     * Если есть одинаковые атрибуты, то будут присутствовать все варианты.
     *
     * @template T
     * @param class-string<T> $class - name of the attribute class to search for.
     *                               - название класса атрибута для поиска.
     *
     * @return array<string, array<T> - an array of instances of the specified attribute.
     *                                - массив экземпляров указанного атрибута.
     */
    public function searchAttributesWithDuplicates(string $class): array
    {
        $result = [];

        foreach ($this->params as $parameter) {
            foreach ($parameter->getAttributes($class) as $attribute) {
                $result[$parameter->getName()][] = $attribute->newInstance();
            }
        }
        return $result;
    }
}
