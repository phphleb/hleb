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
    public function clearInitialLogIfExceeded(string $dir, string $pattern): void
    {
        $this->clear($dir, $pattern);
    }

    /**
     * @internal
     */
    public function clearAllLogsIfExceeded(string $dir, string $pattern): void
    {
        $this->clear($dir, $pattern, true);
    }

    private function unlink(false|string $path): void
    {
        if (!$path) {
            return;
        }
        try {
            \set_error_handler(function ($_errno, $errstr) {
                throw new \RuntimeException($errstr);
            });
            @\unlink($path);
        } catch (\RuntimeException) {
        } finally {
            \restore_error_handler();
        }
    }

    private function clear(string $dir, string $pattern, bool $all = false): void
    {
        if (!\file_exists($dir)) {
            return;
        }
        $max = Settings::getParam('common', 'max.log.size');
        if ($max <= 0) {
            return;
        }
        if ($all) {
            // If the limit is exceeded by a factor of two, all logs are deleted. This is a last resort.
            // При двукратном превышении удаляются все логи. Это крайняя мера.
            $max *= 2;
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
        if (!$all) {
            foreach ($logs as $key => $log) {
                if (\str_starts_with($log->getFilename(), $now)) {
                    unset($logs[$key]);
                }
            }
        }
        $logs = \array_values($logs);
        if (!$logs) {
            return;
        }
        if ($all) {
            foreach ($logs as $log) {
                $this->unlink($log->getRealPath());
            }
        } else {
            \asort($logs, SORT_STRING);
            $this->unlink(\current($logs)->getRealPath());
        }
    }
}
