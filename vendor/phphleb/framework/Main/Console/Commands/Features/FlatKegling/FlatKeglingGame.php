<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\FlatKegling;

use Hleb\Main\Console\Commands\Features\FeatureInterface;
use Hleb\Main\Console\WebConsole;

/**
 * Console game Bowling.
 * The throw setting is made by the following arguments:
 * {strength} (1-10) - strength of the throw, the stronger, the smaller the deviation from the trajectory,
 * but the smaller the spread.
 * {target} (1-10) - the target in the form of the number of pins on which the throw is made.
 * {factor} (1-100) - accuracy, 50 exactly on target, up to 50 deviation to the left and more than 50 to the right,
 * within the size of the pin space.
 *
 * Консольная игра Боулинг.
 * Настройка броска производится следующими аргументами:
 * {strength} (1-10) - сила броска, чем сильнее, тем меньше отклонение от траектории, но меньше разброс.
 * {target} (1-10) - цель в виде номера кегли, по которой производится бросок.
 * {factor} (1-100) - точность, 50 ровно в цель, до 50 отклонение влево и более 50 вправо,
 * в пределах размера места для кегли.
 *
 * @internal
 */
final class FlatKeglingGame extends StartData implements FeatureInterface
{
    use ExpressionTrait;

    private const DESCRIPTION = 'Bowling console game. ' . self::RULES;

    private const RULES = 'Sample command `php console flat-kegling-feature [strength:1-10] [target:1-10] [factor:1-100]`, for example, `php console flat-kegling-feature 8 1 50`';

    private Calculations $calc;

    private WinStorage $storage;

    public function __construct()
    {
        $this->storage = new WinStorage(self::POSITION);
        $this->calc = new Calculations();
    }

    #[\Override]
    public function run(array $argv = []): string
    {
        \array_shift($argv);
        if (!isset($argv[0], $argv[1], $argv[2]) ||
            (!\is_numeric($argv[0]) || !\is_numeric($argv[1]) || !\is_numeric($argv[2])) ||
            ($argv[0] > 10 || $argv[0] < 1 || $argv[1] > 10 || $argv[1] < 1 || $argv[2] < 1 || $argv[2] > 100)
        ) {
            return self::RULES . PHP_EOL;
        }
        $strength = (int)$argv[0];
        $target = (int)$argv[1];
        $factor = (int)$argv[2];

        if (!$this->storage->isConfig()) {
            echo self::LOGO . PHP_EOL;
        }
        $data = $this->storage->getConfig();

        echo " strength: {$strength} (Throw power)" . PHP_EOL .
            " target: [{$target}] (Aim at the bowling pin (place) with this number)" . PHP_EOL .
            " factor: {$factor} (Offset left 1-49 and right 51-100 from the target)" . PHP_EOL .
            ($data['frame'] === 0 ? '  NEW GAME' . PHP_EOL : '') .
            ($data['attempt'] === 1 ? '  NEW FRAME' . PHP_EOL : '') .
            "    GO!" . PHP_EOL;

        $third = WebConsole::isUsed() ? 3 : 33;
        if ($data['level'] === 1) {
           WebConsole::isUsed() or \sleep(2);
        }

        $path = (float)(self::TARGET[$target]) + \round(($factor - 50) * 0.01, 2);
        for ($i = 1; $i < $third; $i++) {
            $path = $this->calc->path($i, $strength, $factor, $path);
            $p = \round($path - 1, 0, PHP_ROUND_HALF_DOWN);
            $l = self::TRACK;
            if ($p >= 0) {
                $l[$p] = '(';
            }
            if ($p >= -1) {
                $l[$p + 1] = ($i % 2 === 0) ? ':' : '.';
            }
            $l[$p + 2] = ')';
            echo $this->calc->track($l, $i) . "\r";
            WebConsole::isUsed() or \usleep((int)(1000000 / $strength - 1000 * $i));
            $l = $et = self::TRACK;
            if ($i === $third - 1 && $p > 0 && $p < 9) {
                $l[$p] = $et[$p + 2] = '\\';
                $l[$p + 1] = 'V';
                $l[$p + 2] = $et[$p] = '/';
                $et[$p + 1] = 'Λ';
                echo $this->calc->track($l, $i) . PHP_EOL;
                echo $this->calc->track($et, $i) . PHP_EOL;
            } else {
                echo $this->calc->track($l, $i) . PHP_EOL;
            }

        }
        $s = $this->calc->update($data, $strength, $path, $factor, self::DAMAGE);
        echo $this->calc->format($s) . PHP_EOL;

        $allScore = $this->calc->searchScore($s);

        $actualScore = \abs($allScore - $this->calc->searchScore($data['data']));
        if ($allScore === 0 && $data['attempt'] === 1) {
            echo PHP_EOL . "  STRIKE!" . PHP_EOL;
            $data['data'] = self::POSITION;
            $data['attempt'] = 1;
            if ($data['type'] === 'strike') {
                $data['points'] += 10;
            }
            $data['type'] = 'strike';
            $data['frame']++;
            $data['stat'][] = 'X';
            //$data['count'] = 0;
        } else if ($allScore === 0 && $data['attempt'] === 2) {
            echo PHP_EOL . "   Spare!" . PHP_EOL;
            $data['data'] = self::POSITION;
            $data['attempt'] = 1;
            $data['type'] = 'spare';
            $data['frame']++;
            $data['stat'][] = $data['count'] . '/' . $actualScore;

        } else if ($data['attempt'] === 1) {
            $data['attempt'] = 2;
            $data['data'] = $s;
        } else {
            $data['frame']++;
            $data['attempt'] = 1;
            $data['data'] = self::POSITION;
            $data['stat'][] = $data['count'] . '/' . $actualScore;
        }
        $data['points'] += $actualScore;

        $data['count'] = $actualScore;
        if ($data['type'] === 'spare' && $data['attempt'] === 1) {
            $data['points'] += $actualScore;
            $data['type'] = '';
        }
        if ($data['type'] === 'strike') {
            $data['points'] += $actualScore;
            if ($data['attempt'] === 2) {
                $data['type'] = '';
            }
        }

        $stat = $data['attempt'] === 1 ? $data['stat'] : \array_merge($data['stat'], [$actualScore]);

        echo PHP_EOL . \implode(' | ', $stat) . '  ' . $data['points'] . PHP_EOL;

        if ($data['frame'] === 10 && ($data['attempt'] === 1)) {
            echo PHP_EOL . 'Total: ' . $data['points'] . ' New level: ' . ($data['level'] + 1) . PHP_EOL;
            $data = $this->storage->defaultConfig();
            ++$data['level'];
        }
        $data['time'] = \time();
        $this->storage->saveConfig($data);

        return \str_repeat('_', 13) . PHP_EOL;
    }

    /** @inheritDoc */
    #[\Override]
    public static function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    /** @inheritDoc */
    #[\Override]
    public function getCode(): int
    {
        return 0;
    }
}
