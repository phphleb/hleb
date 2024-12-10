<?php

declare(strict_types=1);

namespace Phphleb\Debugpan;

use Hleb\Static\Request;
use Hleb\Static\Response;
use Hleb\Static\Settings;
use Hleb\Static\System;
use Phphleb\Nicejson\JsonConverter;

final class InitPanel
{
    public const API_VERSION = 'v2';

    public const SCRIPT_ID = 'hl-debugpan-init-script';

    private static ?string $requestId = null;

    private bool $open = false;

    /**
     * Stores class loading statistics for previous processes in asynchronous mode.
     *
     * Хранит статистику загрузки классов по предыдущим процессам в асинхронном режиме.
     */
    private static array $autoload = [];

    /**
     * The output of the debug panel of the framework.
     *
     * Вывод отладочной панели фреймворка.
     */
    public function createPanel(bool $force = false, bool $open = false): string
    {
        if (!$force && (!Settings::isDebug() || Request::getMethod() !== 'GET')) {
            return '';
        }
        // For one request, the panel is displayed once.
        // За один запрос панель выводится один раз.
        if (self::$requestId === System::getRequestId()) {
            return '';
        }
        // Value initialization.
        // Инициализация значения.
        self::$requestId = System::getRequestId();

        $this->open = $open;
        $v = self::API_VERSION;
        $id = self::SCRIPT_ID;
        $key = System::getLibraryKey();
        $data = $this->getProcessData();
        return PHP_EOL . "<script id=\"$id\" src=\"/$key/debugpan/$v/js/debugpanscript\" " . PHP_EOL . " data-list='$data' async ></script>" . PHP_EOL;
    }

    /**
     * Generates request data for output to the debug panel.
     *
     * Формирует данные запроса для вывода в панель отладки.
     */
    private function getProcessData(): false|string
    {
        $startTime = \microtime(true);
        $opcache = \function_exists('opcache_get_status') ? \opcache_get_status() : [];
        $autoload = $this->getAutoload();
        $data = [
            'tag' => System::getLibraryKey(),
            'version' => self::API_VERSION,
            'system' => [
                'load' => [
                    'core' => $this->getTime(System::getCoreEndTime(), $startTime),
                    'middleware' => $this->getMiddleware($startTime),
                ],
                'php' => [
                    'version' => \phpversion(),
                    'opcache' => (int)($opcache && ($opcache['opcache_enabled'] ?? false)),
                    'jit' => (int)($opcache && ($opcache['jit']['enabled'] ?? false)),
                ],
                'time' => $this->getTime(System::getEndTime(), $startTime), // sec
                'memory' => \round(\memory_get_peak_usage() / 1024 / 1024, 2), // MB
                'storage' => \round(\memory_get_peak_usage(true) / 1024 / 1024, 2), // MB
                'code' => [
                    'status' => $code = Response::getStatus(),
                    'type' => (int)\substr((string)$code, 0, 1),
                ],
                'async' => (int)Settings::isAsync(),
                'logs' => [
                    'enabled' => (int)Settings::getParam('common', 'log.enabled'),
                    'level' => Settings::getParam('common', 'max.log.level'),
                ],
                'timezone' => Settings::getParam('common', 'timezone'),
            ],
            'autoload' => [
                'previous' => $this->getAsyncAutoload($autoload),
                'process' => $autoload,
            ],
            'request' => [
                'id' => self::$requestId,
            ],
            'template' => $this->getTemplate(),
            'route' => $this->getRouteData(),
            'debug' => $this->getDebugData(),
            'database' => $this->getDbData(),
            'closed' => (int)!$this->open,
        ];

        return $this->convertToJson($data);
    }

    /**
     * Returns the classes loaded by the current script over previous executions.
     *
     * Возвращает классы, загруженные текущим скриптом по предыдущим процессам выполнения.
     */
    public function getAsyncAutoload(array $process): array
    {
        if (Settings::isStandardMode()) {
            return [];
        }
        $asyncLoaded = [];
        foreach (self::$autoload as $key => $item) {
            if (!isset($process[$key])) {
                $asyncLoaded[$key] = $item;
            }
        }
        self::$autoload = \array_merge(self::$autoload, $process);

        return $asyncLoaded;
    }

    /**
     * Returns the classes loaded by the current execution process.
     *
     * Возвращает классы, загруженные текущим процессом выполнения.
     */
    public function getAutoload(): array
    {
        $classes = System::getClassesAutoloadDataFromDA();
        $autoload = [];
        foreach($classes as $data) {
            $path = \end($data);
            $name = \array_search($path, $data);
            $autoload[$name] = $path ? 1 : 0;
        }
        return $autoload;
    }

    public function getTemplate(): array
    {
        $templates = System::getInsertTemplateDataFromDA();
        $result = [];
        foreach ($templates as $template) {
            if (isset($template['path'])) {
                $result[] = $template;
            }
        }
        return $result;
    }

    /**
     * Formatting the JSON output to the page.
     *
     * Форматирование выводимого на страницу JSON.
     */
    private function convertToJson(array $data): false|string
    {
        return \str_replace(["\/", "'"], ["/", "&apos;"], (new JsonConverter())->get($data));
    }

    /**
     * Total time calculation.
     *
     * Подсчёт итогового времени.
     */
    private function getTime(?float $time, float $startTime): float
    {
        return \round(($time ?? $startTime) - System::getStartTime(), 5);
    }

    /**
     * Standardization for middleware classes.
     *
     * Стандартизация для классов-посредников.
     */
    private function getMiddleware(float $startTime): array
    {
        $result = [];
        foreach (System::getMiddlewareDataFromDA() as $item) {
            $result[] = ['name' => \ltrim($item['name'], '\\'), 'sec' => $this->getTime($item['time'], $startTime)];
        }
        return $result;
    }

    /**
     * Returns the converted route data.
     *
     * Возвращает преобразованные данные маршрута.
     */
    private function getRouteData(): ?array
    {
        /** @var object $class */
        $data = System::getRouteCacheData();
        if (!$data) {
            return null;
        }
        $info = System::getRouteCacheInfo();
        $time = $info['time'] ? \date('Y-m-d H:i:s', $info['time']) : '-';

        $result = [];
        $result['time'] = $time;
        $result['name'] = System::getRouteName();
        $result['address'] = $data['full-address'] ?? null;
        $result['method'] = $data['name'] ?? null;
        if (isset($data['data']['view'])) {
            $result['params'] = $data['data']['view']['params'] ?? null;
        }
        $class = $data['controller'] ?? $data['module'] ?? $data['page'] ?? null;
        if ($class) {
            $result['controller']['class'] = $class['class'];
            $result['controller']['method'] = $class['class-method'];
            $result['controller']['type'] = $class['method'] ?? null;
            $result['controller']['initiator'] = System::getInitiatorDataFromDA()[0] ?? '-';
        }

        return $result;
    }

    /**
     * Returns user debug data.
     *
     * Возвращает пользовательские отладочные данные.
     */
    private function getDebugData(): array
    {
        $debug = System::getDebugDataFromDA();
        $hlCheck = System::getHlCheckDataFromDA();
        if (!$debug && !$hlCheck) {
            return [];
        }
        $result = [];
        foreach ($debug as $item) foreach ($item as $key => $value) {
            $result[] = [$key => $this->convertData($value)];
        }
        foreach ($hlCheck as $item) foreach ($item as $key => $value) {
            $result[] = [$key => $this->convertToJson((array)$value)];
        }

        return $result;
    }

    /**
     * Returns database queries.
     *
     * Возвращает запросы к базе данных.
     */
    private function getDbData(): array
    {
        return System::getDbDebugDataFromDA();
    }

    /**
     * Returns data in the format of the var_dump() function.
     *
     * Возвращает данные в формате функции var_dump()
     */
    private function convertData(mixed $data): string
    {
        \ob_start();
        \var_dump($data);

        $data = (string)\ob_get_clean();

        return \htmlspecialchars(\htmlentities($data));
    }
}