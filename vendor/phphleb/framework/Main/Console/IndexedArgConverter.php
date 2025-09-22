<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console;

use Hleb\DynamicStateException;
use Hleb\Helpers\ReflectionMethod;
use Hleb\Main\Console\Specifiers\ArgType;
use Hleb\Main\Console\Specifiers\LightDataType;

/**
 * @internal
 */
final class IndexedArgConverter
{
    private array $errorCells = [];

    private array $convertedTypes = [];

    public function __construct(readonly private ReflectionMethod $method)
    {
    }

    /**
     * Returns a list of argument names that failed validation.
     *
     * Возвращает список названий аргументов которые не прошли проверки.
     */
    public function getErrors(): array
    {
        return $this->errorCells;
    }

    /**
     * Checking an indexed array of arguments.
     *
     * Проверка индексированного массива аргументов.
     */
    public function checkIndexedArgs(array $arguments): false|array
    {
        $this->errorCells = [];
        $arguments = \array_values($arguments);
        $methodArguments = $this->method->getArgTypeList();
        if (!$methodArguments) {
            return $arguments;
        }
        $num = 0;
        // List of default values.
        // Список значений по умолчанию.
        $defaultValues = $this->method->getArgDefaultValueList();
        foreach ($methodArguments as $key => $args) {
            if (!isset($arguments[$num])) {
                if (array_key_exists($key, $defaultValues)) {
                    continue;
                }
                $this->errorCells[] = $key;
                break;
            }
            if (!$this->checkType((string)$arguments[$num], $args)) {
                if (!isset($defaultValues[$key])) {
                    $this->errorCells[] = $key;
                }
            } else {
                $arguments[$num] = $this->getConvertedValue((string)$arguments[$num]);
            }
            $num++;
        }
        return \count($this->errorCells) === 0 ? $arguments : false;
    }

    /**
     * Checking an associative array of arguments.
     *
     * Проверка ассоциативного массива аргументов.
     *
     * @param LightDataType[] $arguments
     * @param array $registerTypes
     */
    public function checkAssocArguments(array &$arguments, array $registerTypes): void
    {
        $result = [];
        /** @var array $rules */
        foreach ($registerTypes as $rules) {
            $searchNames = [];
            $searchLabel = false;
            $isDefault = \array_key_exists('default', $rules);
            $isLabel = \array_key_exists('label', $rules);
            $isRequired = \array_key_exists('required', $rules);
            $names = \array_keys($rules);
            // Iteration of arguments with search for matches in the rules.
            // Перебор аргументов с поиском соответствий в правилах.
            foreach ($arguments as $name => $item) {
                if ($rules['name'] === $name) {
                    $result[$name] = $item;
                    $searchNames[] = $name;
                    // If there is no default value, it is assigned from the rule.
                    // При отсутствии значения по умолчанию, оно назначается из правила.
                    if ($isDefault && ($item->value === null || \is_bool($item->value))) {
                        $result[$name] = new LightDataType($rules['default']);
                    }

                    if ($isLabel) {
                        // If the parameter type is `label`, then it cannot have a value.
                        // Если тип параметра `label`, то у него не может быть значения.
                        if ($item->value !== null && !\is_bool($item->value)) {
                            throw new DynamicStateException('The type parameter `label` must not be set to a value.');
                        }
                        $searchLabel = \is_bool($item->value);
                    }
                }
            }

            // If the names are duplicated.
            // Если названия дублируются.
            if (\count($searchNames) > 1) {
                throw new DynamicStateException('Console parameter names must not be repeated.');
            }

            if ($isLabel) {
                $result[$rules['name']] = new LightDataType($searchLabel);
                break;
            }
            // Required parameters must be present.
            // Обязательные параметры должны присутствовать.
            if (!\count($searchNames) && $isRequired) {
                throw new DynamicStateException('The required parameter `' . $rules['name'] . '` was not specified.');
            }

            foreach ($arguments as $name => $item) {
                if (($rules['name'] === $name) && $item->value !== null && !\is_bool($item->value)) {
                    $list = $item->value;
                    if (\in_array('list', $names, true)) {
                        if (\is_array($list)) {
                            if (isset($rules['minCount']) && $rules['minCount'] > \count($list)) {
                                throw new DynamicStateException('The `' . $name . '` value does not match the minimum allowed array size: ' . $rules['minCount']);
                            }
                            if (isset($rules['maxCount']) && $rules['maxCount'] < \count($list)) {
                                throw new DynamicStateException('The `' . $name . '` value does not match the maximum allowed array size: ' . $rules['maxCount']);
                            }
                            $result[$name] = new LightDataType($list);
                            break;
                        }
                        throw new DynamicStateException('Failed to convert value `' . $name . '` to type: `list`');
                    }
                    $value = $item->asString();
                    if (\in_array('integer', $names, true)) {
                        if (\is_numeric($value) && !\str_contains($value, '.')) {
                            $integer = (int)$value;
                            if (isset($rules['min']) && $rules['min'] > $integer) {
                                throw new DynamicStateException('The `' . $name . '` value is less than allowed: ' . $rules['min']);
                            }
                            if (isset($rules['max']) && $rules['max'] < $integer) {
                                throw new DynamicStateException('The `' . $name . '` value exceeds allowed: ' . $rules['max']);
                            }
                            $result[$name] = new LightDataType($integer);
                            break;
                        }
                        throw new DynamicStateException('Failed to convert value `' . $name . '` to type: `integer`');
                    }
                    if (\in_array('number', $names, true)) {
                        if (\is_numeric($value)) {
                            $number = \str_contains($value, '.') ? (float)$value : (int)$value;
                            if (isset($rules['min']) && $rules['min'] > $number) {
                                throw new DynamicStateException('The `' . $name . '` value is less than the allowed numerical value: ' . $rules['min']);
                            }
                            if (isset($rules['max']) && $rules['max'] < $number) {
                                throw new DynamicStateException('The `' . $name . '` value exceeds the allowed numerical value: ' . $rules['max']);
                            }
                            $result[$name] = new LightDataType($number);
                            break;
                        }
                        throw new DynamicStateException('Failed to convert value `' . $name . '` to type: `number`');
                    }
                    if (\in_array('string', $names, true)) {
                        $length = \strlen($value);
                        if (isset($rules['minLength']) && $rules['minLength'] > $length) {
                            throw new DynamicStateException('The length of the string value `' . $name . '` is less than allowed: ' . $rules['minLength']);
                        }
                        if (isset($rules['maxLength']) && $rules['maxLength'] < $length) {
                            throw new DynamicStateException('The length of the string value `' . $name . '` is greater than allowed:' . $rules['maxLength']);
                        }
                    }
                }
            }
        }
        $arguments = $result;
    }


    /**
     * Checking the correct composition of rules for console arguments.
     *
     * Проверка составления правил для консольных аргументов.
     */
    public function checkRules(array $registerTypes): void
    {
        $this->errorCells = [];
        $names = [];
        foreach ($registerTypes as $check) {
            $r = $check->toArray();
            $errorPrefix = 'Error in `' . $r['name'] . '` console parameter. ';
            if (\in_array($r['name'], $names, true)) {
                throw new DynamicStateException($errorPrefix . 'The parameter name ' . $r['name'] . ' is duplicated in the rules.');
            }
            $names[] = $r['name'];
            foreach ($r['shortName'] ?? [] as $shortName) {
                if (\in_array($shortName, $names, true)) {
                    throw new DynamicStateException($errorPrefix . 'The parameter name ' . $shortName . ' is duplicated in the rules.');
                }
                $names[] = $r['shortName'];
            }
            if (isset($r['label'])) {
                if (isset($r['default'])) {
                    throw new DynamicStateException($errorPrefix . 'The `label` option cannot be used with `default`.');
                }
                if (isset($r['required'])) {
                    throw new DynamicStateException($errorPrefix . 'The `label` option cannot be used with `required`.');
                }
                if (isset($r['shortName'])) {
                    throw new DynamicStateException($errorPrefix . 'The `label` option cannot be used with short name.');
                }
            }
            if (isset($r['minLength'])) {
                if ($r['minLength'] < 0) {
                    throw new DynamicStateException($errorPrefix . 'The `minLength` parameter cannot be a negative value.');
                }
                if (isset($r['maxLength']) && $r['maxLength'] < $r['minLength']) {
                    throw new DynamicStateException($errorPrefix . 'The `minLength` parameter cannot be greater than `maxLength`.');
                }
            }
            if (isset($r['maxLength']) && $r['maxLength'] < 0) {
                throw new DynamicStateException($errorPrefix . 'The `maxLength` parameter cannot be a negative value.');
            }
            if (isset($r['min'])) {
                if ($r['min'] < 0) {
                    throw new DynamicStateException($errorPrefix . 'The `min` parameter cannot be a negative value.');
                }
                if (isset($r['max']) && $r['max'] < $r['min']) {
                    throw new DynamicStateException($errorPrefix . 'The `min` parameter cannot be greater than `max`.');
                }
            }
            if (isset($r['max']) && $r['max'] < 0) {
                throw new DynamicStateException($errorPrefix . 'The `max` parameter cannot be a negative value.');
            }
            if (isset($r['minCount'])) {
                if ($r['minCount'] < 0) {
                    throw new DynamicStateException($errorPrefix . 'The `minCount` parameter cannot be a negative value.');
                }
                if (isset($r['maxCount']) && $r['maxCount'] < $r['minCount']) {
                    throw new DynamicStateException($errorPrefix . 'The `minCount` parameter cannot be greater than `maxCount`.');
                }
            }
            if (isset($r['maxCount']) && $r['maxCount'] < 0) {
                throw new DynamicStateException($errorPrefix . 'The `maxCount` parameter cannot be a negative value.');
            }
            if (isset($r['label']) && \array_diff(\array_keys($r), ArgType::WITH_LABEL)) {
                throw new DynamicStateException($errorPrefix . 'The `label` option is incompatible with other options.');
            }

            // Checking for the intersection of unique types.
            // Проверка на пересечение уникальных типов.
            $searchTypes = [];
            foreach(ArgType::TYPES as $type) {
                if (isset($r[$type])) {
                    $searchTypes[] = $type;
                }
            }
            if (\count($searchTypes) > 1) {
                $type = \array_shift($searchTypes);
                throw new DynamicStateException($errorPrefix . 'The `'. $type . '` parameter cannot be assigned together with: ' . \implode(', ', $searchTypes));
            }
        }
    }

    /**
     * Get a list of set argument names.
     *
     * Получение списка установленных названий аргументов.
     */
    public function getAssigmentNames(array $registerTypes): array
    {
        $result = [];
        foreach ($registerTypes as $type) {
            $r = $type->toArray();
            $result[] = '--' . $r['name'];
            foreach ($r['shortName'] ?? [] as $shortName) {
                $result[] = '-' . $shortName;
            }
        }
        return $result;
    }

    /**
     * Returns a converted array where the data is sorted by name.
     *
     * Возвращает преобразованный массив, где данные рассортированы по названиям.
     */
    public function assignmentOfShortNames(array &$arguments, array $registerTypes): array
    {
        $result = [];
        foreach ($registerTypes as $type) {
            $r = $type->toArray();
            $result[$r['name']] = $r;
            foreach ($r['shortName'] ?? [] as $shortName) {
                if (!isset($arguments[$r['name']]) && isset($arguments[$shortName])) {
                    $arguments[$r['name']] = $arguments[$shortName];
                    unset($arguments[$shortName]);
                }
            }
            if (!isset($arguments[$r['name']])) {
                if (array_key_exists('default', $r)) {
                    $arguments[$r['name']] = new LightDataType($r['default']);
                } else if (!isset($r['required'])) {
                    $arguments[$r['name']] = new LightDataType(null);
                }
            }
            unset($result[$r['name']]['shortName']);

        }
        return $result;
    }

    /**
     * Returns the value in the nearest similar allowed type.
     *
     * Возвращает значение в ближайшем подобном разрешённом типе.
     */
    public function getConvertedValue(string $name): mixed
    {
        return $this->convertedTypes[$name] ?? null;
    }

    /**
     * Due to the specifics of obtaining values, they are all in the string type.
     * Therefore, it is necessary to determine what they are.
     *
     * В силу специфики получения значений - они все в строковом типе.
     * Поэтому нужно определить, что они из себя представляют.
     */
    private function checkType(string $value, array $types): bool
    {
        $this->convertedTypes[$value] = null;
        if (is_numeric($value)) {
            if (str_contains($value, '.')) {
                if (in_array('double', $types, true) || in_array('float', $types, true)) {
                    $this->convertedTypes[$value] = (float)$value;
                    return true;
                }
            } else if (in_array('integer', $types, true) || in_array('int', $types, true)) {
                $this->convertedTypes[$value] = (int)$value;
                return true;
            }
        } else if (str_starts_with($value, '[') && str_ends_with($value, ']') && in_array('array', $types, true)) {
            $this->convertedTypes[$value] = array_map('trim', explode(',', trim($value, '[]')));
            return true;
        }
        if (in_array('mixed', $types, true) || in_array('string', $types, true)) {
            $this->convertedTypes[$value] = $value;
            return true;
        }
        return false;
    }
}
