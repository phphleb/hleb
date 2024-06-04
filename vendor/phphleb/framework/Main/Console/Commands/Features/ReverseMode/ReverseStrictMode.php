<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands\Features\ReverseMode;

use FilesystemIterator;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Main\Console\Commands\Features\FeatureInterface;
use Hleb\Static\Path;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * * For tests, you need to open commented strict_types,
 * otherwise it will work a little faster when there is no check.
 * So this class solves this problem by "enabling"
 * and "disabling" this mode for phphleb/framework.
 *
 * Для тестов нужно открывать закомментированные strict_types,
 * в остальном будет работать немного быстрее, когда проверки нет.
 * Таким образом, этот класс решает эту проблему, "включая"
 * и "отключая" этот режим для phphleb/framework.
 *
 * @internal
 */
final class ReverseStrictMode implements FeatureInterface
{
    private const DESCRIPTION = '(!) Reverse strict mode in framework';

    private const SET_REPLACEMENT = ['/*declare(strict_types=1);*/', 'declare(strict_types=1);/*[0]*/'];

    private const UNDO_REPLACEMENT = ['declare(strict_types=1);/*[0]*/', '/*declare(strict_types=1);*/'];

    private string $path = '@framework';

    #[\Override]
    public function run(array $argv): string
    {
        if (!\in_array($argv[1] ?? null, ['--set', '--undo'])) {
            return '[HELP] The action --set or --undo must be specified.' . PHP_EOL;
        }
        if (!empty($argv[2])) {
            $this->path = $argv[2];
            if (!Path::getReal($this->path)) {
                throw new \RuntimeException('Wrong path to process!');
            }
        }
        if ($argv[1] === '--set') {
            $this->changeMode(self::SET_REPLACEMENT);
        }
        if ($argv[1] === '--undo') {
            $this->changeMode(self::UNDO_REPLACEMENT);
        }

        return 'Mode change completed.' . PHP_EOL;
    }

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

    private function changeMode(array $replacement): void
    {
        $dir = SystemSettings::getRealPath($this->path);
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );
        $result = [];
        /** @var SplFileInfo $item */
        foreach ($files as $item) {
            if ($item->isFile() && $item->getExtension() === 'php') {
                $this->searchAndUpdateFile($item->getRealPath(), $replacement);
            }
        }
    }

    private function searchAndUpdateFile(string $file, array $replacement): void
    {
       $rows = \file($file);
       foreach($rows as &$row) {
           if (\trim($row) === $replacement[0]) {
               $row = \str_replace($replacement[0], $replacement[1], $row);
               \file_put_contents($file, \implode($rows));
               @\chmod($file, 0664);
               return;
           }
       }
    }
}
