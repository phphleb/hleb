<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Cache;

use Hleb\Constructor\Data\SystemSettings;
use Hleb\Main\Routes\Prepare\Defender;
use Hleb\RouteColoredException;
use Hleb\Main\Routes\Prepare\FileChecker;
use Hleb\Main\Routes\Prepare\Handler;
use Hleb\Main\Routes\Prepare\Optimizer;
use Hleb\Main\Routes\Prepare\Verifier;
use Hleb\Main\Routes\StandardRoute;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * @internal
 */
final class CacheRoutes
{
    private const DIR = '@storage/cache/routes';

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
            \is_dir($include) ? $this->recursiveRemoveDir($include) : @\unlink($include);
        }
        \clearstatcache();
        \is_dir($dir) and \rmdir($dir);
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

    public function updateInfo(array $data, ?string $className = null): void
    {
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
