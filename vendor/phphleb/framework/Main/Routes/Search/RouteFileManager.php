<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Routes\Search;

use Hleb\Constructor\Cache\CacheRoutes;
use Hleb\Constructor\Cache\RouteMark;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\AsyncRouteException;
use Hleb\RouteColoredException;
use Hleb\HttpMethods\External\SystemRequest;
use Hleb\Main\Routes\Update\CheckRouteForUpdates;
use Hleb\Main\Routes\Update\RouteData;

/**
 * Performs interaction with the framework's routing.
 *
 * Осуществляет взаимодействие с маршрутизацией фреймворка.
 *
 * @internal
 */
class RouteFileManager
{
    protected bool $isBlocked = false;

    protected array $data = [];

    protected int $fallbackNumber = 0;

    protected array $protected = [];

    protected ?string $method = null;

    protected ?string $routeName = null;

    protected ?string $routeClassName = null;

    protected ?bool $isPlain = null;

    protected ?bool $isNoDebug = null;

    protected static ?array $infoCache = null;

    protected static bool|array $stubData = false;

    /**
     * Get a matching block of route data with the current request, or false if not found.
     *
     * Получение совпавшего блока данных маршрута с текущим запросом или false, если не был найден.
     *
     * @throws RouteColoredException
     */
    public function getBlock(): false|array
    {
        /** @see hl_check() - getBlock start */
        $this->init();
        self::$infoCache = $this->getInfoFromCache();
        /** @see hl_check() - getInfoFromCache received */

        if ($this->checkFromUpdate(self::$infoCache)) {
            // During the check, the state may have changed.
            // Во время проверки состояние могло измениться.
            if ($this->validateInfoFromUpdate(self::$infoCache, $this->getInfoFromCache())) {
                // Direct saving routes to the cache.
                // Непосредственное сохранение маршрутов в кеш.
                $routes = (new RouteData())->dataExtraction();
                if ((new CacheRoutes($routes))->save() === false) {
                    $this->throwSaveError();
                }
                if (\function_exists('opcache_reset')) {
                    \opcache_reset();
                }
            }
            // Check cache persistence.
            // Проверка сохранения кеша.
            self::$infoCache = $this->getInfoFromCache();
            if (!self::$infoCache) {
                $this->throwSaveError();
            }
        }
        /** @see hl_check() - checkFromUpdate completed */

        self::$stubData = $this->siteStubSearch(self::$infoCache);

        if (self::$stubData) {
            $this->isBlocked = true;
            return \is_array(self::$stubData) ? self::$stubData : false;
        }
        self::$infoCache = $this->getInfoFromCache();
        /** @see hl_check() - getInfoFromCache completed */

        return $this->searchBlock();
    }

    /**
     * Returns the blocking status of the site.
     *
     * Возвращает статус блокировки сайта.
     */
    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    /**
     * Returns the security types of the route.
     *
     * Возвращает типы защищенности маршрута.
     */
    public function protected(): array
    {
        return $this->protected;
    }

    /**
     * Indicates simple or standard content delivery.
     *
     * Указывает на простую отдачу контента или стандартную.
     */
    public function getIsPlain(): null|bool
    {
        return $this->isPlain;
    }

    /**
     * Returns the flag for forcing the debug panel to be disabled.
     *
     * Возвращает признак принудительного отключения отладочной панели.
     */
    public function getIsNoDebug(): null|bool
    {
        return $this->isNoDebug;
    }

    /**
     * Returns dynamic route data when matching parts in `/{param}/` as 'param' => `value`.
     *
     * Возвращает данные динамического маршрута при совпадении частей в `/{param}/` как 'param' => `value`.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns the name of the matched route if present.
     *
     * Возвращает название совпавшего маршрута если оно присутствует.
     */
    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    /**
     * Returns the class name for caching the matched route.
     *
     * Возвращает название класса для кеширования совпавшего маршрута.
     */
    public function getRouteClassName(): ?string
    {
        return $this->routeClassName;
    }

    /**
     * Getting a block to be placed in place of mismatched routes.
     *
     * Получение блока размещаемого вместо не совпавших маршрутов.
     *
     * @throws RouteColoredException
     */
    public function getFallbackBlock(): array|false
    {
        if (!$this->fallbackNumber) {
            return false;
        }
        return $this->getBlockById($this->fallbackNumber);
    }

    /**
     * @throws RouteColoredException
     */
    protected function searchBlock(): false|array
    {
        $request = DynamicParams::getRequest();
        // If this is the main page.
        // Если это главная страница.
        $index = $this->searchIndexPage((int)(self::$infoCache['index_page'] ?? 0), $request);
        if ($index) {
            $this->routeName = self::$infoCache['index_page_name'] ?? null;
            $this->isNoDebug = self::$infoCache['no_debug'] ?? null;

            return $index;
        }
        // Search for a list of routes.
        // Поиск списка маршрутов.
        $this->method = \ucfirst(\strtolower($request->getMethod()));
        /** @see hl_check() - request method defined */

        // Search for a specific route.
        // Поиск конкретного маршрута.
        $block = $this->getDataByRequest($request);
        /** @see hl_check() - getDataByRequest completed */

        if ($block === false) {
            return false;
        }

        $blockNumber = $this->createBlockDataNumber($block);
        if (!$blockNumber) {
            return false;
        }
        /** @see hl_check() - createBlockDataNumber completed */

        return $this->getBlockById($blockNumber, $block->getIsCompleteAddress());
    }

    /**
     * Initialization to avoid code duplication.
     *
     * Инициализация для того, чтобы избежать дублирования кода.
     */
    protected function init(): void
    {
        $this->data = [];
        $this->protected = [];
        $this->fallbackNumber = 0;
        $this->method = null;
    }

    /**
     * Search for block data by ID.
     *
     * Поиск данных блока по идентификатору.
     *
     * @throws RouteColoredException
     */
    private function getBlockById($blockNumber, bool $isComplete = true): false|array
    {
        $method = $this->method;
        // Search for a block with route data.
        // Поиск блока с данными маршрута.
        $class = RouteMark::getRouteClassName(RouteMark::DATA_PREFIX . $method . $blockNumber);
        $path = $this->searchPath("@storage/cache/routes/Map/$method/$class.php");
        if (empty($path)) {
            return false;
        }

        $data = $this->getFromCache($path, $class);

        if ($data) {
            $default = $data['data']['default'] ?? [];
            if ($default) {
                $this->data += $this->checkKeysAndUpdateData($default, $data['full-address'] ?? 'undefined', $isComplete);
            }
        }
        return $data;
    }

    /**
     * Returns array-converted data from a file.
     * Returns false if the file was not found or the conversion failed.
     *
     * Возвращает преобразованные в массив данные из файла.
     * Возвращает false если файл не был найден или преобразование не удалось.
     *
     * @throws RouteColoredException
     */
    private function getFromCache(string $path, string $class): array|false
    {
        if (SystemSettings::isAsync()) {
            // First the class's existence is checked, whether it has been modified on disk or not.
            // Сначала проверяется существование класса, неважно, был ли он изменен на диске.
            if (!\class_exists($class, false)) {
                if (!\file_exists($path)) {
                    return false;
                }
                require $path;
            }
        } else {
            if (!\file_exists($path)) {
                return false;
            }
            if (!\class_exists($class, false)) {
                require $path;
            }
        }
        $this->routeClassName = $class;

        /** @var object $class */
        return $class::getData();
    }

    /**
     * An attempt to get data for the main page bypassing the search among other routes.
     *
     * Попытка получения данных для главной страницы минуя поиск среди остальных маршрутов.
     *
     * @throws RouteColoredException
     */
    private function searchIndexPage(int $page, SystemRequest $request): false|array
    {
        if ($page && $request->getMethod() === 'GET' && $request->getUri()->getPath() === '/') {
            $class = RouteMark::getRouteClassName(RouteMark::DATA_PREFIX . 'Get' . $page);
            $path = $this->searchPath("@storage/cache/routes/Map/Get/$class.php");
            if ($path) {
                return $this->getFromCache($path, $class);
            }
        }

        return false;
    }

    /**
     * Returns the path depending on the usage method.
     *
     * Возвращает путь в зависимости от метода использования.
     */
    private function searchPath(string $path): false|string
    {
        return SystemSettings::isAsync() ? SystemSettings::getPath($path) : SystemSettings::getRealPath($path);
    }

    /**
     * Checking the blocking of the site by a special stub page.
     *
     * Проверка блокировки сайта специальной страницей-заглушкой.
     *
     * @throws RouteColoredException
     */
    private function siteStubSearch(array $info): array|bool
    {
        if ($this->checkForBlocking()) {
            if (!empty($info['site_blocked'])) {
                $class = RouteMark::getRouteClassName(RouteMark::DATA_PREFIX . 'Get' . $info['site_blocked']);
                return $this->getFromCache(
                    SystemSettings::getRealPath("@storage/cache/routes/Map/Get/$class.php"),
                    $class
                );
            }
            return true;
        }
        return false;
    }

    /**
     * Project lock check.
     *
     * Проверка на блокировку проекта.
     */
    private function checkForBlocking(): bool
    {
        $path = SystemSettings::getRealPath('@storage/cache/routes/lock-status.info');
        if (!$path) {
            return false;
        }
        return (bool)\file_get_contents($path);
    }

    /**
     * Returns whether the cache needs to be refreshed.
     *
     * Возвращает необходимость обновления кеша.
     */
    private function checkFromUpdate(array $info): bool
    {
        if (!SystemSettings::getCommonValue('routes.auto-update')) {
            // If there is a cache, then only it is taken.
            // Если есть кеш, то берётся только он.
            return false;
        }
        $time = $info['time'] ?? 0;
        if (!$time) {
            // Cache not found.
            // Кеш не обнаружен.
            return true;
        }
        if (!(new CheckRouteForUpdates($time, SystemSettings::getRealPath('routes')))
            ->hasChanges($info['files_hash'] ?? null)
        ) {
            // If there is no forced update and routes do not need to be updated.
            // Если маршруты не нужно обновлять.
            return false;
        }

        return true;
    }

    /**
     * Get configuration from cache.
     *
     * Получить конфигурацию из кеша.
     *
     * @throws RouteColoredException
     */
    private function getInfoFromCache(): array
    {
        $info = [];
        $infoClassName = RouteMark::getRouteClassName(RouteMark::INFO_CLASS_NAME);
        $path = SystemSettings::getRealPath("@storage/cache/routes/$infoClassName.php");
        if ($path) {
            $info = $this->getFromCache($path, $infoClassName);
        }
        return $info ?: [];
    }

    /**
     * Checking readiness to update the configuration file.
     *
     * Проверка готовности к обновлению файла конфигурации.
     *
     * @throws RouteColoredException
     */
    private function validateInfoFromUpdate(array $firstInfo, array $secondInfo): bool
    {
        // The cache file definitely does not exist.
        // Файл кеша однозначно не существует.
        if ((empty($firstInfo) && empty($secondInfo))) {
            return true;
        }

        // The update was not performed by another request.
        // Обновление не было выполнено другим запросом.
        if ($firstInfo['time'] === $secondInfo['time']) {
            return true;
        }

        return $this->updateRounds($secondInfo);
    }

    /**
     * If another process is processing changes, then you need to wait for them.
     *
     * Если другой процесс обрабатывает изменения, то нужно их подождать.
     *
     * @throws RouteColoredException
     */
    private function updateRounds(array $info): bool
    {
        $update = static function ($i) {
            empty($i['update_status']) || $i['update_status'] < \microtime(true) - 1;
        };
        while ($update($info)) {
            \usleep(10000);
            $info = $this->getInfoFromCache();
        }
        return !empty($info['update_status']);
    }

    /**
     * Error saving cache file.
     *
     * Ошибка сохранения файла кеша.
     *
     * @throws RouteColoredException
     */
    private function throwSaveError(): void
    {
       throw (new RouteColoredException(AsyncRouteException::HL01_ERROR))->complete(DynamicParams::isDebug());
    }

    /**
     * Returns the data of the matched block according to the data of the current request.
     *
     * Возвращает данные совпавшего блока согласно данным текущего запроса.
     *
     * @throws RouteColoredException
     */
    private function getDataByRequest(SystemRequest $request): SearchBlock|false
    {
        $class = RouteMark::getRouteClassName(RouteMark::PREVIEW_PREFIX . $this->method);
        $path = $this->searchPath("@storage/cache/routes/Preview/$class.php");
        if (empty($path)) {
            return false;
        }
        return new SearchBlock($request, (array)$this->getFromCache($path, $class));
    }

    /**
     * Parsing data from the found block.
     *
     * Разбор данных из найденного блока.
     */
    private function createBlockDataNumber(SearchBlock $block): false|int
    {
        // All summary data will be available only after receiving the block number.
        // Все итоговые данные будут только после получения номера блока.
        $blockNumber = (int)$block->getNumber();
        $this->routeName = $block->getRouteName();
        $this->fallbackNumber = $block->getFallback();
        if (!$blockNumber) {
            return false;
        }
        $this->protected = $block->protected();
        $this->isPlain = $block->getIsPlain();
        $this->isNoDebug = $block->getIsNoDebug();
        $this->data = $block->getData();

        return $blockNumber;
    }

    /**
     * Returns default values from dynamic routes.
     *
     * Возвращает дефолтные значения из динамических маршрутов.
     *
     * @throws RouteColoredException
     */
    private function checkKeysAndUpdateData(array $data, string $address, bool $isComplete): array
    {
        $defaultList = [];
        foreach ($data as $subarray) {
            $key = $subarray[0];
            if (isset($defaultList[$key]) || isset($this->data[$key])) {
                throw (new RouteColoredException(AsyncRouteException::HL38_ERROR))->complete(DynamicParams::isDebug(), ['key' => $key, 'value' => $subarray[1], 'address' => $address]);
            }
            $defaultList[$key] = !$isComplete && \str_contains($subarray[1], '?') ? null : \rtrim($subarray[1], '?');
        }
        return $defaultList;
    }

}
