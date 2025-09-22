<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console\Specifiers;

use Hleb\Constructor\Attributes\Accessible;

#[Accessible]
final class ArgType
{
    final public const TYPES = ['integer', 'number', 'string', 'list', 'label'];

    final public const WITH_LABEL = ['name', 'label', 'desc'];

    private array $data;

    /**
     * @internal
     * @see arg()
     */
    public function __construct(?string $name = null)
    {
        $this->data = ['name' => $name];
    }

    /**
     * Assigns a default value if the argument has no value or is missing.
     * If the argument is required and no default value is set, then there
     * must be an input value.
     *
     * Назначает значение по умолчанию если у аргумента нет значения или он отсутствует.
     * Если аргумент обязательный, а значение по умолчанию не установлено,
     * то обязательно должно быть входящее значение.
     */
    public function default(mixed $value = null): static
    {
        $this->data['default'] = $value;

        return $this;
    }

    /**
     * Sets the short name for the argument, as opposed to the full name (--Name)
     * it is called with a single hyphen (-N).
     * The value can be either -N="Ernst Neizvestnyi" or just -N=Ernst.
     * Several of these abbreviations can be set for the same argument.
     *
     * Устанавливает короткое имя для аргумента, в отличие от полного (--Name)
     * оно вызывается с одним дефисом (-N).
     * Значение может быть как -N="Ernst Neizvestnyi" так и просто -N=Ernst.
     * Может быть установлено несколько таких сокращений у одного аргумента.
     */
    public function short(string $name): static
    {
        if (!isset($this->data['shortName'])) {
            $this->data['shortName'] = [];
        }
        $this->data['shortName'][] = $name;

        return $this;
    }

    /**
     * With this method, the argument can be received as an array of values.
     * For example, --Name=1 is equivalent to the array [1] and --Name=1 --Name=2
     * is equivalent to [1,2].
     * It is also possible to pass the list in one destination as --Name=[1,2]
     *
     * При наличии этого метода аргумент может быть получен в виде массива значений.
     * Например, --Name=1 равнозначно массиву [1], а --Name=1 --Name=2 соответствует [1,2].
     * Также можно передать список в одном назначении как --Name=[1,2]
     */
    public function list(int $minCount = 0, ?int $maxCount = null): static
    {
        $this->data['list'] = true;
        $this->data['minCount'] = $minCount;
        $this->data['maxCount'] = $maxCount;

        return $this;
    }

    /**
     * With this method, the argument can be received as a numeric value
     * For example, --Name=2 or --Name=-100 and also --Name=3.50
     *
     * При наличии этого метода аргумент может быть получен как числовое значение.
     * Например, --Name=2 или --Name=-100, а также --Name=3.50
     */
    public function number(float|int|null $min = null, float|int|null $max = null): static
    {
        $this->data['number'] = true;
        $this->data['min'] = $min;
        $this->data['max'] = $max;

        return $this;
    }

    /**
     * With this method, the argument can be received as an integer value.
     * For example, --Name=2 or --Name=-100
     *
     * При наличии этого метода аргумент может быть получен как целочисленное значение.
     * Например, --Name=2 или --Name=-100
     */
    public function integer(?int $min = null, ?int $max = null): static
    {
        $this->data['integer'] = true;
        $this->data['min'] = $min;
        $this->data['max'] = $max;

        return $this;
    }

    /**
     * With this method, the argument can be received as a string value.
     * If there are no other types, this is the default value.
     * The value can be either --Name="Ernst Neizvestnyi" or simply --Name=Ernst.
     *
     * При наличии этого метода аргумент может быть получен как строковое значение.
     * При отсутствии других типов является значением по умолчанию.
     * Значение может быть как --Name="Ernst Neizvestnyi" так и просто --Name=Ernst.
     */
    public function string(int $minLength = 0, ?int $maxLength = null): static
    {
        $this->data['string'] = true;
        $this->data['minLength'] = $minLength;
        $this->data['maxLength'] = $maxLength;

        return $this;
    }

    /**
     * The presence of this setting makes the argument mandatory.
     * In this case, its value does not have to be present.
     *
     * Присутствие этой настройки делает аргумент обязательным.
     * При этом значение его не должно обязательно присутствовать.
     */
    public function required(): static
    {
        $this->data['required'] = true;

        return $this;
    }

    /**
     * The presence of this method means that the parameter has no value
     * and its presence/absence is expressed as a boolean value.
     * For example, when --force is added, the 'force' parameter will be true,
     * otherwise it will be false.
     * Cannot be used with the required() and default() methods.
     *
     * Присутствие этого метода означает, что параметр не имеет значения
     * и присутствие/отсутствие его выражается булевым значением.
     * Например, при добавлении --force параметр 'force' будет равен true,
     * в отсутствие - false.
     * Не может быть применено с методами required() и default().
     */
    public function label(): static
    {
        $this->data['label'] = true;

        return $this;
    }

    /**
     * Adds a description to the parameter that can be used by the framework
     * when displaying information about the command.
     *
     * Добавляет описание к параметру, которое может быть использовано
     * фреймворком при выводе информации о команде.
     */
    public function desc(string $text): static
    {
        $this->data['description'] = $text;

        return $this;
    }

    /**
     * Service method.
     *
     * Служебный метод.
     *
     * @internal
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
