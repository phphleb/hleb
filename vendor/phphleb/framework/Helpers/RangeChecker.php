<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

/**
 * @internal
 */
final class RangeChecker
{
    protected array $range;

    /**
     * Allows you to set numeric intervals.
     * For example:
     * '0-10' - from 0 to 10 inclusive.
     * '-20--10' - from -20 to -10 inclusive.
     * '12' is equal to 12.
     * '-12' is equal to -12.
     * '10-∞' - greater than or equal to 10, from 10 inclusive to `infinity`.
     * '-∞--10' - minus `infinity` to -10.
     *
     * Позволяет задать числовые интервалы.
     * Например:
     * '0-10' - от 0 до 10 включительно.
     * '-20--10' - от -20 до -10 включительно.
     * '12' - равно 12.
     * '-12' - равно -12.
     * '10-∞' - больше или равно 10, от 10 включительно до `бесконечности`.
     * '-∞--10' - от минус `бесконечности` до -10.
     *
     *
     * @param string|array $range - '1,3,4-8,10,100' / ['1','3','4-8','10','100']
     */
    public function __construct(string|array $range)
    {
        $this->range = \is_array($range) ? $range : \explode(',', $range);
    }

    /**
     * Checking if a number is in a range.
     *
     * Проверка нахождения числа в диапазоне.
     *
     * @param int $number
     * @return bool
     */
    public function check(int $number): bool
    {
        foreach ($this->range as $range) {
            if (\is_numeric($range)) {
                if ((int)$range === $number) {
                    return true;
                }
            } else if (\is_string($range) && $range !== '') {
                $i = \explode('-', $range);
                $count = \count($i);
                if ($count > 2 && $count < 5) {
                    if ($count === 3) {
                        $i = \str_contains($range, '--') ? [$i[0], '-' . $i[2]] : ['-' . $i[1], $i[2]];
                    } else if ($count === 4) {
                        $i = ['-' . $i[1], '-' . $i[3]];
                    }
                }
                if (\count($i) === 2) {
                    if (\is_numeric($i[0]) && $number >= (int)$i[0]) {
                        if (\end($i) === '∞' || (\is_numeric($i[1]) && $number <= (int)$i[1])) {
                            return true;
                        }
                    }
                    if ($i[0] === '-∞' && \is_numeric($i[1]) && $number <= (int)$i[1]) {
                        return true;
                    }
                }
            } else {
                break;
            }
        }
        return false;
    }

    /**
     * Checks the data initiated in the constructor for correctness.
     * This does not use a regular expression, so the calculation should be fast.
     *
     * Проверяет инициированные в конструкторе данные на корректность.
     * Здесь не используется регулярное выражение, поэтому расчет должен происходить быстро.
     *
     * @param bool $onlyPositive - only positive numbers.
     *                           - только положительные числа.
     *
     * @return bool
     */
    public function validation(bool $onlyPositive = false): bool
    {
        $str = \implode(',', $this->range);
        if (\str_contains($str, '∞')) {
            if (\substr_count($str, '∞') > 1 || (!\str_ends_with($str, '∞') && !\str_starts_with($str, '-∞'))) {
                return false;
            }
        }
        foreach ($this->range as $range) {
            if (\is_numeric($range)) {
                continue;
            }
            if ($onlyPositive && \substr_count($range, '-') !== 1) {
                return false;
            }
            if (!\is_numeric(\str_replace(['-', '∞'], '', $range)) || \substr_count($range, '-') > 3) {
                return false;
            }
            if (\str_starts_with($range, '--') || \str_ends_with($range, '-') || \str_ends_with($range, '--')) {
                return false;
            }
            if (!$onlyPositive && \substr_count($range, '-') > 1 && !\substr_count($range, '--')) {
                return false;
            }
        }

        return true;
    }
}
