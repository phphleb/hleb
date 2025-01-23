<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Data;

use Hleb\Base\RollbackInterface;
use Hleb\HttpMethods\External\SystemRequest;
use Hleb\Main\Insert\BaseAsyncSingleton;

/**
 *  Serves to manage the main settings inside the framework core.
 *
 * Служит для управления основными настройками внутри ядра фреймворка.
 *
 * @internal
 */
final class DynamicParams extends BaseAsyncSingleton implements RollbackInterface
{
    /** @see self::rollback() */
    private static bool $dynamicDebug = true;

    /** @see self::rollback() */
    private static ?object $dynamicOriginRequest = null;

    /** @see self::rollback() */
    private static ?string $dynamicHost = null;

    /** @see self::rollback() */
    private static array $dynamicUriParams = [];

    /** @see self::rollback() */
    private static ?string $dynamicRequestId = null;

    /** @see self::rollback() */
    private static ?SystemRequest $baseRequest = null;

    /** @see self::rollback() */
    private static ?array $argv = null;

    /** @see self::rollback() */
    private static bool $dynamicEndingUrl = false;

    /** @see self::rollback() */
    private static ?float $startTime = null;

    /** @see self::rollback() */
    private static ?float $endTime = null;

    /** @see self::rollback() */
    private static ?float $coreEndTime = null;

    /** @see self::rollback() */
    private static ?string $dynamicActiveModuleName = null;

    /** @see self::rollback() */
    private static array $controllerRelatedData = [];

    /** @see self::rollback() */
    private static ?string $dynamicRouteName = null;

    /** @see self::rollback() */
    private static ?string $dynamicRouteClassName = null;

    /** @see self::rollback() */
    private static ?array $alternateSession = null;

    /** @see self::rollback() */
    private static ?array $alternateCookies = null;

    /** @see self::rollback() */
    private static ?string $controllerMethodName = null;

    /**
     * Initialization of dynamic parameters of a single request.
     *
     * Инициализация динамических параметров отдельного запроса.
     *
     * @param SystemRequest $request - initialized request object.
     *                               - инициализированный объект запроса.
     *
     * @param bool $isDebug - mutable debug value.
     *                      - изменяемое значение отладки.
     *
     * @param float|null $startTime
     *
     * @param bool $newId - generate a new request ID.
     *                    - сгенерировать новый ID запроса.
     *
     * @return void
     */
    public static function initRequest(
        SystemRequest $request,
        bool          $isDebug,
        ?float        $startTime = null,
    ): void
    {
        self::$baseRequest = $request;
        self::setDynamicRequest($request);
        self::setDynamicDebug($isDebug);
        self::setDynamicHost($request->getUri()->getHost());
        self::$startTime = $startTime;
    }

    /** @internal */
    public static function setAlternateSession(?array $data): void
    {
        self::$alternateSession = $data;
    }

    /** @internal */
    public static function getAlternateSession(): ?array
    {
        return self::$alternateSession;
    }

    /** @internal */
    public static function setAlternateCookies(?array $data): void
    {
        self::$alternateCookies = $data;
    }

    /** @internal */
    public static function getAlternateCookies(): ?array
    {
        return self::$alternateCookies;
    }

    /** @internal */
    public static function setDynamicDebug(bool $isDebug): void
    {
        self::$dynamicDebug = $isDebug;
    }

    /** @internal */
    public static function setDynamicOriginRequest(?object $psr7Request): void
    {
        self::$dynamicOriginRequest = $psr7Request;
    }

    /**
     * If a Request object was passed with the request, then it can be obtained using this method.
     * You also need to enable this action in the settings.
     *
     * Если с запросом был передан объект Request, то его можно получить при помощи этого метода.
     * Также нужно разрешить это действие в настройках.
     */
    public static function getDynamicOriginRequest(): ?object
    {
        return self::$dynamicOriginRequest;
    }

    /**
     * Returns the UNIX timestamp set at the beginning of the request.
     *
     * Возвращает установленную при начале запроса метку UNIX-времени.
     */
    public static function getStartTime(): ?float
    {
        return self::$startTime;
    }

    /**
     * Returns the UNIX timestamp set when the request ended.
     *
     * Возвращает установленную при завершении запроса метку UNIX-времени.
     */
    public static function getEndTime(): ?float
    {
        return self::$endTime;
    }

    /**
     * Sets the UNIX timestamp after the framework has finished loading.
     *
     * Устанавливает после завершении загрузки фреймворка метку UNIX-времени.
     */
    public static function setEndTime(float $endTime): void
    {
        self::$endTime = $endTime;
    }

    /**
     * Returns the UNIX timestamp set when the framework was loaded.
     *
     * Возвращает установленную при завершении загрузки фреймворка метку UNIX-времени.
     */
    public static function getCoreEndTime(): ?float
    {
        return self::$coreEndTime;
    }

    /**
     * Sets a UNIX timestamp after the project exits and before the debug panel is displayed.
     *
     * Устанавливает после завершения проекта и до вывода панели отладки метку UNIX-времени.
     *
     * @internal
     */
    public static function setCoreEndTime(float $endTime): void
    {
        self::$coreEndTime = $endTime;
    }

    /** @internal */
    public static function setDynamicHost(string $host): void
    {
        self::$dynamicHost = $host;
    }

    /** @internal */
    public static function setActiveModuleName(string $name): void
    {
        self::$dynamicActiveModuleName = $name;
    }

    /** @internal */
    public static function setDynamicUriParams(array $data): void
    {
        self::$dynamicUriParams = $data;
    }

    /** @internal */
    public static function setDynamicRequest(SystemRequest $request): void
    {
        self::$baseRequest = $request;
        $key = SystemSettings::getSystemValue('ending.slash.url');
        $path = $request->getUri()->getPath();
        $default = !($path === '/') && \str_ends_with($path, '/');
        $methods = SystemSettings::getSystemValue('ending.url.methods');
        $method = $request->getMethod();
        if (\in_array($key, [0, 1, '0', '1'], true) &&
            (\in_array(\strtolower($method), $methods, true) ||
                \in_array(\strtoupper($method), $methods, true)
            )) {
            self::$dynamicEndingUrl = (bool)$key;
        } else {
            self::$dynamicEndingUrl = $default;
        }
    }

    /** @internal */
    public static function setNewDynamicRequestId(): void
    {
        // Fast generation of RFC 4211 UUID v4 similarity
        // Быстрая генерация подобия RFC 4211 UUID v4
        try {
            $data = \random_bytes(16);
            $data[6] = \chr(\ord($data[6]) & 0x0f | 0x40);
            $data[8] = \chr(\ord($data[8]) & 0x3f | 0x80);
            $hash = \vsprintf('%s%s-%s-%s-%s-%s%s%s', \str_split(\bin2hex($data), 4));
        } catch (\Exception) {
            $hash = \substr(\sha1(\microtime() . \rand()), 0, 36);
            $hash[8] = '-';
            $hash[13] = '-';
            $hash[18] = '-';
            $hash[23] = '-';
        }

        self::$dynamicRequestId = $hash;
    }

    /** @internal */
    public static function isDebug(): bool
    {
        return SystemSettings::getCommonValue('debug') && self::$dynamicDebug;
    }

    /** @internal */
    public static function isConfigDebug(): bool
    {
        return (bool)(SystemSettings::getCommonValue('config.debug') ?? false);
    }

    /** @internal */
    public static function getHost(): ?string
    {
        return self::$dynamicHost;
    }

    /** @internal */
    public static function getDynamicUriParams(): array
    {
        return self::$dynamicUriParams;
    }

    /** @internal */
    public static function setControllerRelatedData(array $data): void
    {
        self::$controllerRelatedData = $data;
    }

    /** @internal */
    public static function getControllerRelatedData(): array
    {
        return self::$controllerRelatedData;
    }

    /** @internal */
    public static function getDynamicRequestId(): ?string
    {
        if (self::$dynamicRequestId === null) {
            self::setNewDynamicRequestId();
        }
        return self::$dynamicRequestId;
    }

    /** @internal */
    public static function getConsoleCommand(): string
    {
        return \implode(' ', self::$argv ?? []);
    }

    /** @internal */
    public static function getBaseRequest(): ?SystemRequest
    {
        return self::$baseRequest;
    }

    /** @internal */
    public static function getArgv(): array
    {
        return self::$argv ?? [];
    }

    /** @internal */
    public static function setArgv(array $argv): void
    {
        if (SystemSettings::isCli() && self::$argv === null) {
            self::$argv = $argv;
        }
    }

    /** @internal */
    public static function isEndingUrl(): bool
    {
        return self::$dynamicEndingUrl;
    }

    /** @internal */
    public static function getRequest(): SystemRequest
    {
        return self::$baseRequest;
    }

    /** @internal */
    public static function getModuleName(): ?string
    {
        return self::$dynamicActiveModuleName;
    }

    /** @internal */
    public static function setRouteName(?string $name): void
    {
        self::$dynamicRouteName = $name;
    }

    /** @internal */
    public static function getRouteName(): ?string
    {
        return self::$dynamicRouteName;
    }

    /** @internal */
    public static function setRouteClassName(?string $name): void
    {
        self::$dynamicRouteClassName = $name;
    }

    public static function getRouteClassName(): ?string
    {
        return self::$dynamicRouteClassName;
    }

    /** @internal */
    public static function setControllerMethodName(?string $name): void
    {
        self::$controllerMethodName = $name;
    }

    public static function getControllerMethodName(): ?string
    {
        return self::$controllerMethodName;
    }

    public static function addressAsArray(): array
    {
           return [
               'host' => self::getRequest()->getUri()->getHost(),
               'scheme' => self::getRequest()->getUri()->getScheme(),
               'path' => self::getRequest()->getUri()->getPath(),
               'method' => self::getRequest()->getMethod(),
               'port' => self::getRequest()->getUri()->getPort(),
               'query' => self::getRequest()->getUri()->getQuery(),
           ];
    }

    public static function addressAsString(bool $withMethod = false, bool $withQuery = false): string
    {
        $data = self::addressAsArray();
        $method = $withMethod ? $data['method'] . ' ' : '';
        if ($data['port'] && !\str_contains($data['host'], ':')) {
            $data['host'] = $data['host'] . ':' . $data['port'];
        }
        $query = $withQuery ? $data['query'] : '';

        return "{$method}{$data['scheme']}://{$data['host']}{$data['path']}{$query}";
    }

    /**
     * @inheritDoc
     *
     * @internal
     */
    #[\Override]
    public static function rollback(): void
    {
        self::$dynamicDebug = true;

        self::$dynamicOriginRequest = null;

        self::$dynamicHost = null;

        self::$dynamicRequestId = null;

        self::$baseRequest = null;

        self::$argv = null;

        self::$dynamicEndingUrl = false;

        self::$startTime = null;

        self::$endTime = null;

        self::$coreEndTime = null;

        self::$dynamicUriParams = [];

        self::$dynamicActiveModuleName = null;

        self::$dynamicRouteName = null;

        self::$controllerRelatedData = [];

        self::$alternateSession = null;

        self::$dynamicRouteClassName = null;

        self::$alternateCookies = null;

        self::$controllerMethodName = null;
    }

}
