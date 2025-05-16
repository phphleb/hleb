<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Cache;

use FilesystemIterator;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Main\Routes\Prepare\Defender;
use Hleb\Main\Routes\Update\CheckRouteForUpdates;
use Hleb\RouteColoredException;
use Hleb\Main\Routes\Prepare\FileChecker;
use Hleb\Main\Routes\Prepare\Handler;
use Hleb\Main\Routes\Prepare\Optimizer;
use Hleb\Main\Routes\Prepare\Verifier;
use Hleb\Main\Routes\StandardRoute;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @internal
 */
final class CacheRoutes
{
    private const DIR = '@storage/cache/routes';

    private const FILES = '@global/routes';

    private string $dir;

    private ClassWithDataCreator $creator;

    public function __construct(private readonly array $rawData)
    {
        $this->dir = SystemSettings::getPath(self::DIR);
        $this->creator = new ClassWithDataCreator();
    }

    /**
     * @throws RouteColoredException
     */
    public function save(): bool
    {
        // Saving the original configuration.
        // Сохранение первоначальной конфигурации.
        if (!$this->updateDefaultInfo()) {
            return false;
        }

        $this->recursiveRemoveDir(SystemSettings::getPath(self::DIR . '/Map'));
        $this->recursiveRemoveDir(SystemSettings::getPath(self::DIR . '/Preview'));

        (new FileChecker($this->rawData))->isCheckedOrError();

        $sortRoutes = (new Handler($this->rawData))->sort();
        $optimizer = (new Optimizer($sortRoutes))->update();

        $list = $optimizer->getRoutesList();
        // Save the preliminary list of routes.
        // Сохранение предварительного списка маршрутов.
        $this->savePreviewData($list);

        $routes = $optimizer->getRoutesByMethod();

        (new Verifier($routes))->isCheckedOrError();
        // Save data for each route.
        // Сохранение данных для каждого маршрута.
        $this->saveSortData($routes);

        $info = $optimizer->getRoutesInfo();
        $info['time'] = \time();
        $info['update_status'] = 0;
        // Save global route information.
        // Сохранение глобальной информации о маршрутах.
        $this->updateInfo($info);

        return true;
    }

    /**
     * Saving minimized preliminary route data.
     *
     * Сохранение минимизированных предварительных данных маршрутов.
     */
    private function savePreviewData(array $data): void
    {
        (new Defender())->handle($data);

        foreach ($data as $method => $items) {
            if ($method === 'head') {
                continue;
            }
            $method = \ucfirst($method);
            $class = RouteMark::getRouteClassName(RouteMark::PREVIEW_PREFIX . $method);
            $this->creator->saveContent(
                $class,
                $this->dir . "/Preview/$class.php",
                $items,
            );
        }
    }

    /**
     * Deleting files in a directory.
     * The files may have been deleted by another process.
     *
     * Удаление файлов в директории.
     * Файлы могут быть удалены другим процессом.
     */
    private function recursiveRemoveDir(string $dir): void
    {
        $includes = \glob($dir . '/*');
        foreach ($includes as $include) {
            if (\is_dir($include)) {
                $this->recursiveRemoveDir($include);
            } else {
                try {
                    \set_error_handler(function ($_errno, $errstr) {
                        throw new \RuntimeException($errstr);
                    });
                    @\unlink($include);
                } catch (\RuntimeException) {
                } finally {
                    \restore_error_handler();
                }
            }
        }
        \clearstatcache();
        if (\is_dir($dir)) {
            try {
                \set_error_handler(function ($_errno, $errstr) {
                    throw new \RuntimeException($errstr);
                });
                @\rmdir($dir);
            } catch (\RuntimeException) {
            } finally {
                \restore_error_handler();
            }
        }
    }

    public function updateDefaultInfo(): bool
    {
        if (!RouteMark::generateHash($this->rawData)) {
            return false;
        }
        $className = RouteMark::getRouteClassName(RouteMark::INFO_CLASS_NAME);
        return $this->creator->saveContent(
            $className,
            $this->dir . "/$className.php",
            [
                'time' => 0,
                'index_page' => 0,
                'update_status' => \microtime(true),
            ],
        );
    }

    /**
     * Updating a file with general information about routes.
     *
     * Обновление файла с общей информацией о маршрутах.
     */
    public function updateInfo(array $data, ?string $className = null): void
    {
        if (SystemSettings::getSystemValue('route.files.checking')) {
            $fileInfo = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(SystemSettings::getPath(self::FILES),
                    FilesystemIterator::SKIP_DOTS
                ),
            );
            $map = [];
            /** @see CheckRouteForUpdates::hasChanges() */
            foreach ($fileInfo as $info) {
                if (!$info->isFile() || $info->getExtension() !== 'php') {
                    continue;
                }
                $map[] = $info->getRealPath();
            }
            $data['files_hash'] = \sha1(\json_encode($map));
        }


        $className = $className ?? RouteMark::getRouteClassName(RouteMark::INFO_CLASS_NAME);
        $this->creator->saveContent(
            $className,
            $this->dir . "/$className.php",
            $data,
        );
    }

    /**
     * Saving a cache with data of specific routes.
     *
     * Сохранение кеша с данными конкретных маршрутов.
     */
    private function saveSortData(array $list): void
    {
        (new Defender())->handle($list);
        foreach ($list as $method => $items) {
            if ($method === 'head') {
                continue;
            }
            foreach ($items as $key => $data) {
                $method = \ucfirst($method);
                $class = RouteMark::getRouteClassName(RouteMark::DATA_PREFIX . $method . $key);
                $this->creator->saveContent(
                    $class,
                    $this->dir . "/Map/$method/$class.php",
                    $this->prepare($data),
                );
            }
        }
    }

    /**
     * Returns a list of all files in a directory and subdirectories relative to the root of the directory.
     *
     * Возвращает список всех файлов в директории и поддиректориях относительно корня директории.
     */
    function getFileList(string $directory): array {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $baseDirLength = \strlen(\realpath($directory)) + 1;

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $filePath = \realpath($fileInfo->getPathname());
                $relativePath = \substr($filePath, $baseDirLength);
                $relativePath = \str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
                $files[] = $relativePath;
            }
        }

        return $files;
    }

    /**
     * Cleaning up unnecessary data.
     *
     * Очистка ненужных данных.
     */
    private function prepare(array $data): array
    {
        foreach($data['actions'] ?? [] as $key => $value) {
            if ($value['method'] === StandardRoute::CONTROLLER_TYPE) {
                $data['controller'] = $value;
            }
            if ($value['method'] === StandardRoute::PAGE_TYPE) {
                $data['page'] = $value;
            }
            if ($value['method'] === StandardRoute::REDIRECT_TYPE) {
                $data['redirect'] = $value;
            }
            if ($value['method'] === StandardRoute::MODULE_TYPE) {
                $data['module'] = $value;
            }
            if ($value['method'] === StandardRoute::MIDDLEWARE_TYPE) {
                $data['middlewares'][]= $value;
            }
            if ($value['method'] === StandardRoute::AFTER_TYPE) {
                $data['middleware-after'][]= $value;
            }
            unset($data['actions'][$key]);
        }
        return $data;
    }
}
