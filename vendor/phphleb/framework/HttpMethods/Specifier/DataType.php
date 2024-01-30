<?php

declare(strict_types=1);

namespace Hleb\HttpMethods\Specifier;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Helpers\DefaultValueHelper;

/**
 * Class for data transfer.
 * Data can be obtained from a class object in various formats.
 *
 * Класс для передачи данных.
 * Данные можно получить из объекта класса в различных форматах.
 */
#[Accessible]
final class DataType
{
    private ?string $clearValue = null;

    private ?array $clearList = null;

    /**
     * @param $value - the original, raw value.
     *               - оригинальное, необработанное значение.
     *
     * @param bool $clear - if false, return all values in their original (not processed!) form.
     *                     - при false возвращать все значения в исходном (не обработанном!) виде.
     */
    public function __construct(readonly public mixed $value, readonly private bool $clear = true)
    {
    }

    /**
     * Returns the value cast to `integer`
     *
     * Возвращает значение приведенное к типу `integer`.
     */
    public function toInt(): int
    {
        return \is_numeric($this->value) ? (int)$this->value : 0;
    }

    /**
     * Returns the value cast to `string`
     *
     * Возвращает значение приведенное к типу `string`.
     */
    public function toString(): string
    {
        return \is_string($this->value) ? $this->clear($this->value) : (string)$this->value;
    }

    /**
     * Returns a value cast to type `integer`; if the value does not exist, returns $default.
     * If the value exists, it will be limited by $min and $max if the value is less than or exceeded.
     * If the value is not a number, for example containing letters, $default will also be returned.
     * If there is no value, you can also set the exception class $exc, which is called
     * instead of the default value; if true, the standard error class will be called.
     *
     * Возвращает значение приведенное к типу `integer`, если значение не существует - возвращает $default.
     * Если значение существует, то будет ограничено $min и $max при меньшем значении или превышении.
     * При значении не являющимся числом, например, содержащим буквы, также будет возвращено $default.
     * При отсутствии значения также можно задать класс исключения $exc, вызываемый вместо
     * дефолтного значения; при true будет вызван стандартный класс ошибки.
     */
    public function limitInt(int $min = 0, int $max = PHP_INT_MAX, int $default = 0, bool|string $exc = false): int
    {
        if (!\is_numeric($this->value)) {
            DefaultValueHelper::err($exc);
            return $default;
        }
        $value = $this->toInt();
        if ($value > $max) {
            return $max;
        }
        if ($value < $min) {
            return $min;
        }
        return $value;
    }

    /**
     * Returns the value cast to `integer`, if the value does not exist, returns $default.
     * If the value is not a number, such as containing letters, $default will also be returned.
     * If there is no value, you can also set the exception class $exc, which is called
     * instead of the default value; if true, the standard error class will be called.
     *
     * Возвращает значение приведенное к типу `integer`, если значение не существует - возвращает $default.
     * При значении не являющимся числом, например, содержащим буквы, также будет возвращено $default.
     * При отсутствии значения также можно задать класс исключения $exc, вызываемый вместо
     * дефолтного значения; при true будет вызван стандартный класс ошибки.
     */
    public function asInt(int $default = 0, bool|string $exc = false): int
    {
        if (!\is_numeric($this->value)) {
            DefaultValueHelper::err($exc);
            return $default;
        }
        return (int)$this->value;
    }

    /**
     * Returns a value cast to type `integer`; if the value does not exist or is less than 0, returns 0.
     * If the value is not a number, such as containing letters, 0 will also be returned.
     * If there is no value, you can also set the exception class $exc, which is called
     * instead of the default value; if true, the standard error class will be called.
     *
     * Возвращает значение приведенное к типу `integer`, если значение не существует или меньше 0 - возвращает 0.
     * При значении не являющимся числом, например, содержащим буквы, также будет возвращено 0.
     * При отсутствии значения также можно задать класс исключения $exc, вызываемый вместо
     * дефолтного значения; при true будет вызван стандартный класс ошибки.
     */
    public function asPositiveInt(bool|string $exc = false): int
    {
        if (!\is_numeric($this->value) || (int)$this->value < 0) {
            DefaultValueHelper::err($exc);
            return 0;
        }
        return (int)$this->value;
    }

    /**
     * Returns the value cast to the `float` type, if the value does not exist, returns $default.
     * If the value is not a number, such as containing letters, $default will also be returned.
     * If there is no value, you can also set the exception class $exc, which is called
     * instead of the default value; if true, the standard error class will be called.
     *
     * Возвращает значение приведенное к типу `float`, если значение не существует - возвращает $default.
     * При значении не являющимся числом, например, содержащим буквы, также будет возвращено $default.
     * При отсутствии значения также можно задать класс исключения $exc, вызываемый вместо
     * дефолтного значения; при true будет вызван стандартный класс ошибки.
     */
    public function asFloat(float $default = 0.0, int $precision = 5, int $mode = PHP_ROUND_HALF_UP, bool|string $exc = false): float
    {
        if (!\is_numeric($this->value)) {
            DefaultValueHelper::err($exc);
            return $default;
        }
        return \round((float)$this->value, $precision, $mode);
    }

    /**
     * Returns a value converted to the `float` type; if the value does not exist or is less than 0, returns 0.0.
     * If the value is not a number, such as containing letters, 0.0 will also be returned.
     * If there is no value, you can also set the exception class $exc, which is called
     * instead of the default value; if true, the standard error class will be called.
     *
     * Возвращает значение приведенное к типу `float`, если значение не существует или меньше 0 - возвращает 0.0.
     * При значении не являющимся числом, например, содержащим буквы, также будет возвращено 0.0.
     * При отсутствии значения также можно задать класс исключения $exc, вызываемый вместо
     * дефолтного значения; при true будет вызван стандартный класс ошибки.
     */
    public function asPositiveFloat(int $precision = 5, int $mode = PHP_ROUND_HALF_UP, bool|string $exc = false): float
    {
        if (!\is_numeric($this->value) || (float)$this->value < 0) {
            DefaultValueHelper::err($exc);
            return 0.0;
        }
        return \round((float)$this->value, $precision, $mode);
    }

    /**
     * Returns the value cast to the `string` type, if the value does not exist, returns $default.
     * The value of $default is assumed to be a safe value.
     * If there is no value, you can also set the exception class $exc, which is called
     * instead of the default value; if true, the standard error class will be called.
     *
     * Возвращает значение приведенное к типу `string`, если значение не существует - возвращает $default.
     * Предполагается, что значением $default является безопасное значение.
     * При отсутствии значения также можно задать класс исключения $exc, вызываемый вместо
     * дефолтного значения; при true будет вызван стандартный класс ошибки.
     */
    public function asString(string|null $default = null, bool|string $exc = false): string|null
    {
        if ($this->value === null) {
            DefaultValueHelper::err($exc);
            return $default;
        }
        $type = \gettype($this->value);
        if (!in_array($type, ['boolean', 'integer', 'string', 'double', 'float'])) {
            DefaultValueHelper::err($exc, "The value cast to a string cannot be processed due to the type: $type");
            return $default;
        }
        if (!$this->clear) {
            return (string)$this->value;
        }
        if ($this->clearValue === null) {
            $this->clearValue = $this->clear((string)$this->value);
        }
        return $this->clearValue;
    }

    /**
     * Returns the value defined as `boolean`, if the value does not exist, returns $default.
     * If there is no value, you can also set the exception class $exc, which is called
     * instead of the default value; if true, the standard error class will be called.
     *
     * Возвращает значение определенное как `boolean`, если значение не существует - возвращает $default.
     * При отсутствии значения также можно задать класс исключения $exc, вызываемый вместо
     * дефолтного значения; при true будет вызван стандартный класс ошибки.
     *
     * @param array $correct - comparison options to be identified as a positive value.
     *                         For example, if only 'on' can be positive, then ['on'].
     *
     *                       - варианты сравнения, которые будут идентифицированы как положительное значение.
     *                         Например, если положительным может быть только значение 'on', то ['on'].
     */
    public function asBool(bool $default = false, array $correct = [true, 'true', 'TRUE', '1', 1], bool|string $exc = false): bool
    {
        if ($this->value === null) {
            DefaultValueHelper::err($exc);
            return $default;
        }
        return \in_array($this->value, $correct, true);
    }

    /**
     * Returns the value specified as `array`, if the value does not exist, returns $default.
     * The value of $default is assumed to be a safe value.
     * If there is no value, you can also set the exception class $exc, which is called
     * instead of the default value; if true, the standard error class will be called.
     *
     * Возвращает значение определенное как `array`, если значение не существует - возвращает $default.
     * Предполагается, что значением $default является безопасное значение.
     * При отсутствии значения также можно задать класс исключения $exc, вызываемый вместо
     * дефолтного значения; при true будет вызван стандартный класс ошибки.
     */
    public function asArray(array $default = [], bool|string $exc = false): array
    {
        if ($this->value === null) {
            DefaultValueHelper::err($exc);
            return $default;
        }
        $value = $this->value;
        if (\is_string($this->value) &&
            (\str_starts_with(\ltrim($this->value), '{') || \str_starts_with(\ltrim($this->value), '['))
        ) {
            try {
                $value = \json_decode(\trim($value), true, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
            }
            if (!\is_array($value)) {
                DefaultValueHelper::err($exc, 'Failed to convert string value to array.');
                return $default;
            }
        }
        if (!\is_array($value)) {
            DefaultValueHelper::err($exc,'The value is not an array.');
            return $default;
        }
        if (!$this->clear) {
            return $value;
        }
        if ($this->clearList === null) {
            $this->clearList = $this->clear($value);
        }

        return $this->clearList;
    }

    /**
     * Returns the value in its original form.
     * ATTENTION! You need to carry out the procedure for clearing the value yourself
     * from potentially unsafe characters.
     *
     * Возвращает значение в исходном виде.
     * ВНИМАНИЕ! Необходимо самостоятельно провести процедуру очистки значения
     * от потенциально небезопасных символов.
     */
    public function value(): mixed
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Basic conversion of unsafe HTML characters to HTML entities.
     *
     * Базовое преобразование небезопасных HTML-символов в HTML-сущности.
     */
    private function clear(mixed $value): mixed
    {
        return \hl_clear_tags($value);
    }
}
