<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Update;

use FilesystemIterator;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Main\Routes\Search\RouteFileManager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * @internal
 */
final readonly class CheckRouteForUpdates
{
    public function __construct(
        private int|float $time,
        private string $routeDir,
    )
    {
    }

    /**
     * Returns the result of checking route files for changes
     * since the last update.
     * An affirmative answer means that an update is needed.
     *
     * Возвращает результат проверки файлов маршрутов на наличие
     * изменений с последнего обновления.
     * Утвердительный ответ означает, что необходимо обновление.
     */
    public function hasChanges(?string $hash = null): bool
    {
        $fileInfo = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->routeDir, FilesystemIterator::SKIP_DOTS)
        );
        $map = [];
        /** @var SplFileInfo $data */
        foreach ($fileInfo as $data) {
            if (!$data->isFile() || $data->getExtension() !== 'php') {
                continue;
            }
            $path = $data->getRealPath();
            if (\filemtime($path) > (int)$this->time) {
                return true;
            }
            /** @see RouteFileManager::checkFromUpdate() */
            $map[] = $path;
        }
        if (SystemSettings::getSystemValue('route.files.checking')) {
            // Checking the file structure of routes for changes.
            // Проверка файловой структуры маршрутов на изменение.
            if ($hash !== \sha1(\json_encode($map))) {
                return true;
            }
        }

        return false;
    }

}
