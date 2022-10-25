<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Main\Commands\Features;

use Hleb\Scheme\App\Commands\Feature\MainFeatureInterface;

class FlatKeglingFeature implements MainFeatureInterface
{
    private const TRACK = [' ', '║', ' ', '|', ' ', '|', ' ', '|', ' ', '║', ' ', ' '];

    private const TARGET = [1 => 5, 2 => 4, 3 => 6, 4 => 3, 5 => 5, 6 => 7, 7 => 2, 8 => 4, 9 => 6, 10 => 8];

    private const POSITION = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10];

    private const DAMAGE = [
        'direct' => [1 => [], 1.5 => [7], 2 => [7, 4], 2.5 => [7, 4], 3 => [4, 7, 8], 3.5 => [4, 2, 8], 4 => [1, 2, 4, 5, 8], 4.5 => [1, 2, 5, 8], 5 => [1, 2, 3, 5, 8, 9], 5.5 => [1, 3, 5, 9], 6 => [3, 6, 9], 6.5 => [3, 6, 9], 7 => [6, 9, 10], 7.5 => [6, 10], 8 => [6, 10], 8.5 => [10], 9 => []],
        'touch' => [1 => [[3, 6, 5, 9], [2, 5, 4, 8]], 2 => [[5, 9], [4, 7]], 3 => [[6, 10], [5, 8]], 4 => [[8], [7]], 5 => [[9], [8]], 6 => [[10], [9]], 7 => [[], []], 8 => [[], []], 9 => [[], []], 10 => [[], []]]
    ];

    private const CONFIG_DIR = HLEB_STORAGE_DIRECTORY . '/lib/flat-kegling/';

    private const CONFIG_FILE = 'config.json';

    private array $data = [];

    public function run(array $a = []): void
    {
        if (!isset($a[0], $a[1], $a[2]) || !is_numeric($a[0]) || !is_numeric($a[1]) || !is_numeric($a[2]) || $a[0] > 10 || $a[0] < 1 || $a[1] > 10 || $a[1] < 1 || $a[2] < 1 || $a[2] > 100) {
            exit(PHP_EOL . 'Sample command `php console flat-kegling-feature [strength:1-10] [target:1-10] [factor:1-100]`, for example, `php console flat-kegling-feature 8 1 64`' . PHP_EOL . PHP_EOL);
        }
        $strength = (int)$a[0];
        $target = (int)$a[1];
        $factor = (int)$a[2];

        function track(array $state, int $i): string
        {
            return implode($state) . (($i % 3 == 0) ? '-' . $i / 3 : '');
        }

        $this->data = $data = $this->getConfig();

        echo " strength: {$strength} (Throw power)" . PHP_EOL .
            " target: [{$target}] (Aim at the bowling pin (place) with this number)" . PHP_EOL .
            " factor: {$factor} (Offset left 1-49 and right 51-100 from the target)" . PHP_EOL .
            ($this->data['frame'] === 0 ? '  NEW GAME' . PHP_EOL : '') .
            ($this->data['attempt'] === 1 ? '  NEW FRAME' . PHP_EOL : '') .
            "    GO!" . PHP_EOL;
        $before = null;
        $turn = 0;
        $third = 33;
        if ($this->data['level'] === 1) {
            sleep(2);
        }

        $path = (float)(self::TARGET[$target]) + round(($factor - 50) * 0.01, 2, PHP_ROUND_HALF_UP);
        for ($i = 1; $i < $third; $i++) {
            $path = $this->calc($i, $strength, $factor, $path);
            $p = round($path - 1, 0, PHP_ROUND_HALF_DOWN);
            $l = self::TRACK;
            if ($p >= 0) {
                $l[$p] = '(';
            }
            if ($p >= -1) {
                $l[$p + 1] = ($i % 2 == 0) ? ':' : '.';
            }
            $l[$p + 2] = ')';
            echo track($l, $i) . "\r";
            usleep((int)(1000000 / $strength - 1000 * $i));
            $l = $et = self::TRACK;
            if ($i === $third - 1 && $p > 0 && $p < 9) {
                $l[$p] = $et[$p + 2] = '\\';
                $l[$p + 1] = 'V';
                $l[$p + 2] = $et[$p] = '/';
                $et[$p + 1] = 'Λ';
                echo track($l, $i) . PHP_EOL;
                echo track($et, $i) . PHP_EOL;
            } else {
                echo track($l, $i) . PHP_EOL;
            }

        }
        $s = $this->update($this->data['data'], $strength, $path, $factor);
        echo $this->formation($s) . PHP_EOL;

        $allScore = $this->searchScore($s);

        $actualScore = abs($allScore - $this->searchScore($this->data['data']));
        if ($allScore === 0 && $this->data['attempt'] === 1) {
            echo PHP_EOL . "  STRIKE!" . PHP_EOL;
            $this->data['data'] = self::POSITION;
            $this->data['attempt'] = 1;
            if ($this->data['type'] === 'strike') {
                $this->data['points'] += 10;
            }
            $this->data['type'] = 'strike';
            $this->data['frame']++;
            $this->data['stat'][] = 'X';
            $this->data['count'] = 0;
        } else if ($allScore === 0 && $this->data['attempt'] === 2) {
            echo PHP_EOL . "   Spare!" . PHP_EOL;
            $this->data['data'] = self::POSITION;
            $this->data['attempt'] = 1;
            $this->data['type'] = 'spare';
            $this->data['frame']++;
            $this->data['stat'][] = $this->data['count'] . '/' . $actualScore;
        } else {

            if ($this->data['attempt'] === 1) {
                $this->data['attempt'] = 2;
                $this->data['data'] = $s;
                $this->data['frame']++;
            } else {
                $this->data['attempt'] = 1;
                $this->data['data'] = self::POSITION;
                $this->data['stat'][] = $this->data['count'] . '/' . $actualScore;
            }
        }
        $this->data['points'] += $actualScore;

        $this->data['count'] = $actualScore;
        if ($data['type'] === 'spare' && $data['attempt'] === 1) {
            $this->data['points'] += $actualScore;
            $this->data['type'] = '';
        }
        if ($data['type'] === 'strike') {
            $this->data['points'] += $actualScore;
            if ($this->data['attempt'] === 2) {
                $this->data['type'] = '';
            }
        }

        $stat = $this->data['attempt'] === 1 ? $this->data['stat'] : array_merge($this->data['stat'], [$actualScore]);

        echo PHP_EOL . implode(' | ', $stat) . '  ' . $this->data['points'] . PHP_EOL;

        if ($this->data['frame'] === 10 && ($this->data['attempt'] === 1)) {
            echo PHP_EOL . 'Total: ' . $this->data['points'] . ' New level: ' . ($this->data['level'] + 1) . PHP_EOL;
            $this->data = $this->defaultConfig();
            $this->data['level'] = $data['level'] + 1;
        }
        $this->saveConfig();
        exit(1);
    }

    private function formation(array $s = self::POSITION): string
    {
        return implode(PHP_EOL, [
            str_repeat(' ', 5) . $s[1],
            str_repeat(' ', 4) . $s[2] . ' ' . $s[3],
            str_repeat(' ', 3) . $s[4] . ' ' . $s[5] . ' ' . $s[6],
            str_repeat(' ', 2) . $s[7] . ' ' . $s[8] . ' ' . $s[9] . ' ' . $s[10],
        ]);
    }

    private function calc(int $meters, int $strength, int $factor, float $path): float
    {
        $path = $path + ($factor > 49 ? 1 : -1) / $strength * 0.003 * $meters;
        if ($path > 8) {
            $path = 8;
        }
        if ($path < 0) {
            $path = 0;
        };

        return round($path, 5);
    }

    private function update(array $s, int $strength, float $path, int $factor): array
    {
        $rule = self::DAMAGE['direct'][$this->roundToHalf($path)];
        $damage = [];
        foreach ($rule as $num) {
            if ($s[$num] !== '*') {
                $s[$num] = '*';
                $damage[] = $num;
            }
        }
        foreach ($damage as $n) {
            $s = $this->touch($n, $s, $strength, $factor, 0);
        }

        return $s;
    }

    private function roundToHalf($v)
    {
        return ceil($v / 0.5) * 0.5;
    }

    private function touch(int $parentNum, array $s, int $strength, int $factor, int $nesting = 1): array
    {
        [$scatterLeft, $scatterRight] = self::DAMAGE['touch'][$parentNum];
        $slip = rand(1, 15 - $this->data['level'] > 10 ? 10 : $this->data['level']);
        $nesting++;
        $vector = ($factor - 50) >= 0 ? 1 : 0;
        $chance = (int)((50 + $slip) - (abs(($factor - 50) ? ($factor - 50) : 1) / ($strength / 3) / $nesting) * 100);
        $basicEldh = $vector ? $scatterLeft : $scatterRight;
        foreach ($basicEldh as $num) {
            if (rand(1, $chance > 0 ? $chance : 1) === 1 || rand(1, 6) === 1) {
                $s = $this->cucle($num, $s, $strength, $factor, $nesting);
            }
        }
        $ricochetEldh = $vector ? $scatterRight : $scatterLeft;
        foreach ($ricochetEldh as $num) {
            if (rand(1, ($chance > 0 ? $chance : 1) * 2) === 1 || rand(1, 8) === 1) {
                $s = $this->cucle($num, $s, $strength, $factor, $nesting);
            }
        }

        return $s;
    }

    private function cucle(int $num, array $s, int $strength, int $factor, int $nesting): array
    {
        if ($s[$num] !== '*') {
            $s[$num] = '*';
            $s = $this->touch($num, $s, $strength, $factor, $nesting);
        }
        return $s;
    }

    private function searchScore(array $s): int
    {
        $count = 0;
        foreach ($s as $key) {
            if ($key !== '*') {
                $count++;
            }
        }
        return $count;
    }

    private function defaultConfig(): array
    {
        return [
            'time' => time(),
            'attempt' => 1,
            'points' => 0,
            'data' => self::POSITION,
            'level' => 1,
            'frame' => 0,
            'stat' => [],
            'count' => 0,
            'type' => ''
        ];
    }

    private function getConfig(): array
    {
        $defaultConfig = $this->defaultConfig();
        if (file_exists(self::CONFIG_DIR . self::CONFIG_FILE)) {
            $config = json_decode(file_get_contents(self::CONFIG_DIR . self::CONFIG_FILE), true);
            if ($config['time'] > time() - 3600) {
                return $config;
            }
            $defaultConfig['level'] = $config['level'];
        } else {
            @mkdir(self::CONFIG_DIR, 0777, true);
        }
        return $defaultConfig;
    }

    private function saveConfig(): void
    {
        $this->data['time'] = time();
        @file_put_contents(self::CONFIG_DIR . self::CONFIG_FILE, json_encode($this->data));
    }

}

