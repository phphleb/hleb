<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use CallbackFilterIterator;
use FilesystemIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class DirectoryHelper
{
    /**
     * Returns the size of the directory in megabytes.
     *
     * Возвращает размер директории в мегабайтах.
     */
    public static function getMbSize(string $dir): float|false
    {
        if (!\is_dir($dir)) {
            return false;
        }

        if (!\str_starts_with(\strtoupper(\php_uname('s')), 'WINDOWS')) {
            try {
                $result = (string)@\shell_exec("du -s $dir");
                $parts = \explode("\t", \trim($result));
                if (\count($parts) === 2 && \is_numeric($parts[0])) {
                    return \round((int)$parts[0] / 1024, 2);
                }
            } catch (\Throwable) {
            }
        }
        try {
            $iterator = self::getFileIterator($dir);
        } catch (\Throwable) {
            return false;
        }

        $size = 0;
        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            $c = $item->getSize();
            if ($c === false) {
                return false;
            }
            $size += (float)$c;
        }
        if (!$size) {
            return 0;
        }
        return \round($size / 1024 / 1024, 2);
    }

    /**
     * Returns an iterator for the files in the folder matching a mask.
     *
     * Возвращает итератор для файлов в папке, соответствующих маске.
     */
    public static function getFileIterator(string $path, string $mask = '*'): CallbackFilterIterator
    {
        return new CallbackFilterIterator(
            new RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
            ),
            function (SplFileInfo $current) use ($mask) {
                if ($mask !== '*') {
                    return $current->isFile() && \fnmatch($mask, $current->getFilename());
                }
                return $current->isFile();
            }
        );
    }

}
