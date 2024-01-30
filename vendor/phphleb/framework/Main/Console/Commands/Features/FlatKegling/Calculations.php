<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\FlatKegling;

use Exception;

/**
 * @internal
 */
final class Calculations
{
    public function track(array $state, int $i): string
    {
        return \implode($state) . (($i % 3 === 0) ? '-' . $i / 3 : '');
    }

    public function path(int $meters, int $strength, int $factor, float $path): float
    {
        $path = $path + ($factor > 49 ? 1 : -1) / $strength * 0.003 * $meters;
        if ($path > 8) {
            $path = 8.0;
        }
        if ($path < 0) {
            $path = 0.0;
        }

        return \round($path, 5);
    }

    public function format(array $s): string
    {
        return \implode(PHP_EOL, [
            \str_repeat(' ', 5) . $s[1],
            \str_repeat(' ', 4) . $s[2] . ' ' . $s[3],
            \str_repeat(' ', 3) . $s[4] . ' ' . $s[5] . ' ' . $s[6],
            \str_repeat(' ', 2) . $s[7] . ' ' . $s[8] . ' ' . $s[9] . ' ' . $s[10],
        ]);
    }

    /**
     * @throws Exception
     */
    public function touch(int $parentNum, array $s, int $strength, int $factor, array $damageTouch, array $data, int $nesting = 1): array
    {
        [$scatterLeft, $scatterRight] = $damageTouch[$parentNum];
        $slip = \random_int(1, 15 - $data['level'] > 10 ? 10 : $data['level']);
        $nesting++;
        $vector = ($factor - 50) >= 0 ? 1 : 0;
        $chance = (int)((50 + $slip) - (\abs(($factor - 50) ?: 1) / ($strength / 3) / $nesting) * 100);
        $basicEldh = $vector ? $scatterLeft : $scatterRight;
        foreach ($basicEldh as $num) {
            if (\random_int(1, $chance > 0 ? $chance : 1) === 1 || \random_int(1, 6) === 1) {
                $s = $this->cycle($num, $s, $strength, $factor, $damageTouch, $data, $nesting);
            }
        }
        $ricochetEldh = $vector ? $scatterRight : $scatterLeft;
        foreach ($ricochetEldh as $num) {
            if (\random_int(1, ($chance > 0 ? $chance : 1) * 2) === 1 || \random_int(1, 8) === 1) {
                $s = $this->cycle($num, $s, $strength, $factor, $damageTouch, $data, $nesting);
            }
        }

        return $s;
    }

    public function cycle(int $num, array $s, int $strength, int $factor, array $damageTouch, array $data, int $nesting): array
    {
        if ($s[$num] !== '*') {
            $s[$num] = '*';
            $s = $this->touch($num, $s, $strength, $factor, $damageTouch, $data, $nesting);
        }
        return $s;
    }

    public function searchScore(array $s): int
    {
        $count = 0;
        foreach ($s as $key) {
            if ($key !== '*') {
                $count++;
            }
        }
        return $count;
    }

    public function update(array $data, int $strength, float $path, int $factor, array $originDamage): array
    {
        $s = $data['data'];
        $rule = $originDamage['direct'][$this->roundToHalf($path)];
        $damage = [];
        foreach ($rule as $num) {
            if ($s[$num] !== '*') {
                $s[$num] = '*';
                $damage[] = $num;
            }
        }
        foreach ($damage as $n) {
            $s = $this->touch($n, $s, $strength, $factor, $originDamage['touch'], $data, 0);
        }

        return $s;
    }

    public function roundToHalf($v): string
    {
        return (string)(\ceil($v / 0.5) * 0.5);
    }

}
