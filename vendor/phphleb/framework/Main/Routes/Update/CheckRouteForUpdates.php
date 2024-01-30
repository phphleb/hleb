<?php

declare(strict_types=1);

namespace Hleb\Main\Routes\Update;

use FilesystemIterator;
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
    public function hasChanges(): bool
    {
        $fileInfo = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->routeDir, FilesystemIterator::SKIP_DOTS)
        );
        /** @var SplFileInfo $data */
        foreach ($fileInfo as $data) {
            if (!$data->isFile() || $data->getExtension() !== 'php') {
                continue;
            }
            if (\filemtime($data->getRealPath()) > (int)$this->time) {
                return true;
            }
        }

        return false;
    }

}
