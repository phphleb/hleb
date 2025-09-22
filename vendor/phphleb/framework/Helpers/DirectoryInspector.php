<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\Constructor\Attributes\Accessible;

/**
 * (!) Works with directories without `/../`.
 *
 * (!) Работает с директориями без `/../`.
 */
#[Accessible]
final class DirectoryInspector
{
    /**
     * Returns the result of a search for an occurrence of the original directory in the search list.
     *
     * Возвращает результат поиска вхождения оригинальной директории в список искомых.
     */
    public function isDirectoryEntry(string $sampleDir, array $checkedDirs): bool
    {
        $sampleDir = $this->formatDirectory($sampleDir);
        foreach ($checkedDirs as $dir) {
            $dir = $this->formatDirectory($dir);
            if (\str_starts_with($dir, $sampleDir)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the remainder of the occurrence of the root directory in the full directory,
     * or false if the root directory is not in the full directory.
     *
     * Возвращает остаток от вхождения корневой директории в полную директорию или false,
     * если корневая директория не входит в полную.
     */
    public function getRelativeDirectory(string $rootDir, string $fullDir): string|false
    {
        $rootDir = $this->formatDirectory($rootDir);
        $fullDir = $this->formatDirectory($fullDir);
        if (!\str_starts_with($fullDir, $rootDir)) {
           return false;
        }
        $search = \str_replace($rootDir, '', $fullDir, $count);
        if ($count > 1) {
            return false;
        }
        return \str_replace('\\', '/', \trim($search, DIRECTORY_SEPARATOR));
    }

    /**
     * Converts the path to the directory to the standard form.
     *
     * Приводит путь к директории в стандартный вид.
     */
    private function formatDirectory(string $dir): string
    {
        return \trim(\str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR);
    }
}
