<?php

namespace Hleb\Constructor\Cache;

use Hleb\Helpers\DirectoryCleaner;
use Hleb\Helpers\DirectoryHelper;
use Hleb\Reference\CacheReference;
use Hleb\Static\Settings;

/**
 * @internal
 */
final class ClearRandomFileCache
{
    /**
     * @internal
     */
    public function run(string $headlineDir): void
    {
        if (!\file_exists($headlineDir)) {
            return;
        }
        $max = Settings::getParam('common', 'max.cache.size');
        if ($max <= 0) {
            return;
        }
        $startTime = \microtime(true);
        $I = DIRECTORY_SEPARATOR;
        $parentDir = \dirname($headlineDir);
        $size = DirectoryHelper::getMbSize($parentDir);
        if (!$size || $size <= $max) {
            return;
        }

        $excessMb = $size - $max;
        $dirs = (array)\scandir($parentDir);
        \shuffle($dirs);
        $cleaner = (new DirectoryCleaner());
        foreach ($dirs as $dirName) {
            if (\str_starts_with($dirName, '.') || \str_contains($dirName, '_')) {
                continue;
            }
            $selectedDir = $headlineDir . $I . $dirName;
            if (!\file_exists($selectedDir)) {
                continue;
            }
            $targetDir = \str_replace([CacheReference::HEADLINE_PREFIX . DIRECTORY_SEPARATOR, '.txt'], ['', '.php'], $selectedDir);
            $selectedMbSize = DirectoryHelper::getMbSize($targetDir);
            if (!$selectedMbSize) {
                continue;
            }
            $headMbSize = DirectoryHelper::getMbSize($selectedDir);
            $selectedMbSize += $headMbSize;

            $cleaner->forceRemoveDir($selectedDir);
            $cleaner->forceRemoveDir($targetDir);
            $excessMb -= $selectedMbSize;
            if ((int)$excessMb <= 0 || \microtime(true) - $startTime > 3) {
                break;
            }
        }
        \clearstatcache();
    }
}
