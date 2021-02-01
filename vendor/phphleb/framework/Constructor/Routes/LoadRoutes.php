<?php

declare(strict_types=1);

/*
 * Working with cached routes file.
 *
 * Работа с кешируемым файлом маршрутов.
 */

namespace Hleb\Constructor\Routes;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class LoadRoutes
{
    private $cacheRoutes = HLEB_STORAGE_CACHE_ROUTES_DIRECTORY . '/routes.txt';

    private $routesDirectory = HLEB_LOAD_ROUTES_DIRECTORY . '/';

    // Save the route cache to a file.
    // Сохраняет кеш маршрутов в файл.
    public function update($data) {
        @file_put_contents($this->cacheRoutes, json_encode($data), LOCK_EX);
        @chmod($this->cacheRoutes, 0775);
        return $data;
    }

    // Loads the route cache from a file, converting it to an array.
    // Загружает кеш маршрутов из файла с преобразованием в массив.
    public function loadCache() {
        $content = is_writable($this->cacheRoutes) ? file_get_contents($this->cacheRoutes) : null;
        if (empty($content)) {
            return false;
        }
        return json_decode($content, true);
    }

    // Returns the result of checking for changes in route data.
    // Возвращает результат проверки на изменения в данных роутов.
    public function comparison() {
        if (file_exists($this->cacheRoutes)) {
            $time = filemtime($this->cacheRoutes);
            $fileInfo = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->routesDirectory)
            );
            foreach ($fileInfo as $pathname => $info) {
                if (!$info->isFile()) continue;
                if (filemtime($info->getRealPath()) > $time) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}

