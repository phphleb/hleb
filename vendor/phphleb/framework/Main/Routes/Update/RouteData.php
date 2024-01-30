<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Update;

use FilesystemIterator;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Main\Routes\BaseRoute;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @internal
 */
final readonly class RouteData
{
    /**
     * Extracting data from a route map.
     *
     * Извлечение данных из карты маршрутов.
     */
    public function dataExtraction(): array
    {
        (static function () {
            $dir = SystemSettings::getRealPath('routes');
            require $dir . DIRECTORY_SEPARATOR . 'map.php';

            $fileInfo = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
            );
            $allowedPaths = \array_map(static function ($val) {
                return \ltrim($val, '\\/ ');
            }, SystemSettings::getValue('system', 'allowed.route.paths'));
            $globalPath = \str_replace('\\', '/', SystemSettings::getRealPath('global'));

            foreach ($fileInfo as $pathname => $data) {
                if (!$data->isFile()) {
                    continue;
                }
                $file = $data->getRealPath();
                if ($allowedPaths) {
                    // The probability that the file path will be /routes/<name>/routes/<name>... is ignored.
                    // Вероятность, что путь к файлу будет /routes/<name>/routes/<name>... не учитывается.
                    $shortPath = \ltrim(\str_replace($globalPath, '', \str_replace('\\', '/', $file)), '/');
                    if (!\in_array($shortPath, $allowedPaths)) {
                        continue;
                    }
                }
                if (\basename($file) === 'main.php') {
                    require $data->getRealPath();
                }
            }
        })();

        return BaseRoute::completion();
    }
}
