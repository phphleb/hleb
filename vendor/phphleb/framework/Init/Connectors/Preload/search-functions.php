<?php

if (!function_exists('search_root')) {
    /**
     * Function for searching the root directory outside the framework.
     *
     * Функция поиска корневой директории вне фреймворка.
     *
     * @internal
     */
    function search_root(): string|false
    {
        $base = __DIR__ . '/../../../../../';
        for ($i = 0; $i < 3; $i++) {
            $search = \realpath($base . '/../') . DIRECTORY_SEPARATOR;
            if (\is_dir($search . 'app') && \is_dir($search . 'routes')) {
                return $search;
            }
        }
        return false;
    }
}

if (!function_exists('search_php_files')) {
    /**
     * A universal function that returns a list of paths to files in the specified directory.
     *
     * Универсальная функция, возвращающая список путей к файлам в указанной директории.
     *
     * @internal
     */
    function search_php_files(string $dir): array
    {
        $result = [];
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $result[] = $file->getRealPath();
            }
        }
        return $result;
    }
}
