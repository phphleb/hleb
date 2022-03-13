<?php

declare(strict_types=1);


namespace Hleb\Main\Helpers;


class RangeChecker
{
    protected $range;

    /**
     * @param string|array $range - '1,3,4-8,10,100' / ['1','3','4-8','10','100']
     */
    public function __construct($range)
    {
        $this->range = is_array($range) ? $range : explode(',', (string)$range);
    }

    /**
     * Checking if a number is in a range.
     *
     * Проверка нахождения числа в диапазоне.
     *
     * @param int $number
     * @return bool
     */
    public function check(int $number)
    {
        foreach ($this->range as $range) {
            if (is_numeric($range)) {
                if ((int)$range === $number) {
                    return true;
                }
            } else if (is_string($range) && $range) {
                $interval = explode('-', $range);
                if (count($interval) === 2 && is_numeric($interval[0]) && is_numeric($interval[1]) && $number >= (int)$interval[0] && $number <= (int)$interval[1]) {
                    return true;
                }
            } else break;
        }
        return false;
    }
}

