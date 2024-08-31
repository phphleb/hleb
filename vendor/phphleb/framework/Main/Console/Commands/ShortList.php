<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\CoreProcessException;
use Hleb\Helpers\ReflectionMethod;
use Hleb\Helpers\TaskHelper;
use Hleb\Main\Console\Colorizer;

/**
 * @internal
 */
final class ShortList
{
    /**
     * Returns a formatted list of existing user commands.
     *
     * Возвращает отформатированный список существующих пользовательских команд.
     */
    public function run(): false|string
    {
        $nameHelper = new TaskHelper();
        $color = new Colorizer();

        $titles = ['COMMAND', 'DESCRIPTION'];
        $interval = 0;
        $defaultInterval = 3;
        $rows = [];
        $list = $nameHelper->getCommands();
        $names = [];
        foreach ($list as $t) {
            $names[] = $t['name'];
            if (isset($t['short']) && $t['short']) {
                $names[] = $t['short'];
            }
        }
        if ($d = $nameHelper->getDuplicateName($names)) {
            throw new CoreProcessException('Duplicate task names found: ' . \implode(', ', $d));
        }
        foreach ($list as $task) {
            if (\method_exists($task['class'], 'run')) {
                $rm = new ReflectionMethod($task['class'], 'run');
                $line = $rm->getFirstLineInDocComment();
                if (!$nameHelper->checkName($task['name'])) {
                    throw new CoreProcessException('Incorrect task name format for ' . $task['class']);
                }
                $command = $task['name'];
                if (!empty($task['short'])) {
                    if (!$nameHelper->checkName($task['name'])) {
                        throw new CoreProcessException('Incorrect task short name format for ' . $task['class']);
                    }
                    $command .= ' (' . $task['short'] . ')';
                }
                $interval = \max(\strlen($command), $interval);
                $rows[$command] = [$command, $line];
            }
        }
        if (!$rows) {
            return false;
        }
        \ksort($rows, SORT_NATURAL);

        $interval < $defaultInterval and $interval = $defaultInterval;
        $result = PHP_EOL;
        $blocks = [];
        $last = false;
        foreach ($rows as $row) {
            $command = \current($row);
            $cp = \str_contains($command, '/');
            $block = $cp ? \strstr($command, '/', true) : false;
            if ($blocks && !$block && $last) {
                $result .= PHP_EOL;
            }
            $last = $cp;
            if (!\in_array($block, $blocks)) {
                if ($cp) {
                    $result .= PHP_EOL . $block . PHP_EOL;
                }
                $blocks[] = $block;
            }
            $row[1] = $color->yellow($row[1]);
            $result .= ' ' . \implode(\str_repeat(' ', $interval - \strlen($command) + $defaultInterval), $row);
        }
        $title = $titles[0] . \str_repeat(' ', $interval - $defaultInterval) . $titles[1];

        return $title . $result;
    }
}
