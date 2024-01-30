<?php

declare(strict_types=1);

namespace Hleb\Main\Logger;

use Hleb\Helpers\DirectoryHelper;
use Hleb\Static\Settings;

/**
 * Clearing log files if the limit is exceeded.
 *
 * Очистка файлов для логов в случае превышения лимита.
 *
 * @internal
 */
final class LogCleaner
{
    /**
     * @internal
     */
    public function run(string $dir, string $pattern): void
    {
        if (!\file_exists($dir)) {
            return;
        }
        $max = Settings::getParam('common', 'max.log.size');
        if ($max <= 0) {
            return;
        }
        $size = DirectoryHelper::getMbSize($dir);
        if (!$size || $size < $max) {
            return;
        }
        if (!\file_exists($dir)) {
            return;
        }
        /**
         * @var \SplFileInfo[] $logs
         */
        $logs = \iterator_to_array(DirectoryHelper::getFileIterator($dir));
        if (!$logs) {
            return;
        }
        $now = \date($pattern);
        foreach ($logs as $key => $log) {
            if (\str_starts_with($log->getFilename(), $now)) {
                unset($logs[$key]);
            }
        }
        $logs = \array_values($logs);
        if (!$logs) {
            return;
        }
        \asort($logs, SORT_STRING);
         $this->unlink(\current($logs)->getRealPath());
    }

    private function unlink(false|string $path): void
    {
        if (!$path) {
            return;
        }
        try {
            @\unlink($path);
        } catch (\Throwable) {
        }
    }
}
