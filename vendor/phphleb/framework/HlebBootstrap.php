<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb;

use AsyncExitException;
use Exception;
use Functions;
use Hleb\Constructor\Data\{DebugAnalytics, DynamicParams, SystemSettings};
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\HttpMethods\External\RequestUri;
use Hleb\Init\{AddressBar, Autoloader, Connectors\HlebConnector, ErrorLog};
use Hleb\Main\Insert\{BaseAsyncSingleton, BaseSingleton};
use Hleb\Static\Response;
use Hleb\Main\Logger\{FileLogger, Log, LoggerInterface, LogLevel};
use Hleb\Main\ProjectLoader;
use Phphleb\Idnaconv\IdnaConvert;
use Hleb\HttpMethods\External\SystemRequest;
use Hleb\HttpMethods\External\Response as SystemResponse;
use RuntimeException;
use Throwable;

/**
 * HLEB2 framework loader in basic mode.
 * To use console or asynchronous modes,
 * use appropriate classes inherited from this.
 * This class contains the necessary methods
 * that are always present when loading the framework.
 *
 * Загрузчик фреймворка HLEB2 в базовом режиме.
 * Чтобы использовать консольный или асинхронный режимы,
 * используйте соответствующие классы, унаследованные от этого.
 * В этом классе собраны необходимые методы,
 * которые всегда присутствуют при загрузке фреймворка.
 *
 */
#[Accessible] #[AvailableAsParent]
class HlebBootstrap
{
    public const HTTP_TYPES = ['GET', 'POST', 'DELETE', 'PUT', 'PATCH', 'OPTIONS', 'HEAD'];

    final public const STANDARD_MODE = 1;

    final public const CONSOLE_MODE = 2;

    final public const ASYNC_MODE = 3;

    final protected const DEFAULT_RE_CLEANING = 100_000;

    protected ?int $mode = null;

    protected array $config = [];

    protected ?array $session = null;

    protected ?array $cookies = null;

    private readonly ?string $globalDirectory;

    private readonly ?string $vendorDirectory;

    private readonly ?string $moduleDirectory;

    private ?string $publicDirectory;

    protected ?LoggerInterface $logger = null;

    protected ?IdnaConvert $hostConvertor = null;

    protected ?SystemResponse $response = null;

    protected static bool $loadResources = false;

    protected ?AddressBar $addressBar = null;

    /**
     * Constructor with initialization.
     *
     * Конструктор с инициализацией.
     *
     * @param string|null $publicPath - full path to the public directory of the project.
     *                                - полный путь к публичной директории проекта.
     *
     * @param array $config - an array replacing the configuration data.
     *                      - заменяющий конфигурационные данные массив.
     *
     * @param LoggerInterface|null $logger - the logging object can be specified before the framework is initialized;
     *                                       it supports the framework's internal logging interface.
     *
     *                                     - объект логирования может быть задан до инициализации фреймворка;
     *                                       он поддерживает внутренний интерфейс логирования фреймворка.
     * @see LoggerAdapter
     *
     * @throws Exception
     */
    public function __construct(?string $publicPath = null, array $config = [], ?LoggerInterface $logger = null)
    {
        $this->mode === null and $this->mode = self::STANDARD_MODE;

        // Some use cases for the framework don't need a public directory.
        // В некоторых вариантах использования фреймворка публичная директория не нужна.
        $this->publicDirectory = $publicPath;

        // The current version of the framework.
        // Текущая версия фреймворка.
        \defined('HLEB_CORE_VERSION') or \define('HLEB_CORE_VERSION', '2.1.1');

        $this->logger = $logger;

        // Register an error handler.
        // Регистрация обработчика ошибок.
        $this->setErrorHandler();

        $this->initialParameters($config);
    }

    /**
     * If you need to install your own logger,
     * then it can be done at initialization.
     * In asynchronous mode can be assigned to a specific request.
     *
     * Если нужно установить собственный способ логирования,
     * то это можно сделать при инициализации.
     * В асинхронном режиме может быть назначен конкретному запросу.
     *
     * @see LoggerAdapter - adapter for PSR-3.
     */
    public function setLogger(LoggerInterface $logger): static
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Returns the current logger.
     *
     * Возвращает текущий способ логирования.
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger ?: Log::instance();
    }

    /**
     * Processing the request and displaying the result.
     * In console mode, returns the execution code.
     *
     * Обработка запроса и вывод результата.
     * В консольном режиме возвращает код выполнения.
     */
    public function load(): int|HlebBootstrap
    {
        try {
            $this->loadProject();

            $this->requestCompletion();

            return $this;

        } catch (AsyncExitException $e) {
            $this->scriptExitEmulation($e);

        } catch (HttpException $e) {
            $this->scriptHttpError($e);

        } catch (Throwable $t) {
            $this->getPreviousErrorControl($t) or $this->scriptErrorHandling($t);
        }

        $this->logsPostProcessing();

        return $this;
    }

    /**
     * Loads configuration.
     *
     * Получение конфигурации фреймворка.
     *
     * @throws Exception
     */
    protected function initConfig(array $config): array
    {
        $c = $config;

        $moduleDirectory = $this->getModuleDirectoryName();
        $dir = $this->globalDirectory;
        $defaultConfig = $this->getDefaultConfig();
        require __DIR__ . '/Init/Review/basic.php';
        if ($c) {
            $c['common'] = \array_merge($defaultConfig['common'], $c['common'] ?? []);
            $c['system'] = \array_merge($defaultConfig['system'], $c['system'] ?? []);
            $c['main'] = \array_merge($defaultConfig['main'], $c['main'] ?? []);
        } else {
            $func = static function (string $path): array {
                return require $path;
            };
            $c['common'] = \array_merge($defaultConfig['common'], $func($dir . '/config/common.php'));
            $c['database'] = $func($dir . '/config/database.php');
            $c['system'] = \array_merge($defaultConfig['system'], $func($dir . '/config/system.php'));
            $c['main'] = \array_merge($defaultConfig['main'], $func($dir . '/config/main.php'));
        }
        $c['system']['mode'] = $this->mode;
        if ($moduleDirectory) {
            $c['system']['module.dir.name'] = $moduleDirectory;
        } else {
            $this->moduleDirectory = $this->globalDirectory . DIRECTORY_SEPARATOR . $c['system']['module.dir.name'];
        }
        $c['system']['module.namespace'] = \ucfirst($c['system']['module.dir.name']);
        $paths = $c['system']['project.paths'];
        unset($c['system']['project.paths']);
        foreach ($paths as &$path) {
            $path = $dir . '/' . \ltrim($path, '/\\');
        }
        $c['path'] = \array_merge($paths, [
            'global' => $dir,
            'public' => $this->publicDirectory,
            'vendor' => $this->vendorDirectory,
            'modules' => $this->moduleDirectory,
            'app' => $dir . '/app',
            'storage' => $dir . '/storage',
            'logs' => $dir . '/storage/logs',
            'routes' => $dir . '/routes',
            'resources' => $dir . '/resources',
            'views' => $dir . '/resources/views',
            'library' => $this->vendorDirectory . '/phphleb',
            'framework' => $this->vendorDirectory . '/phphleb/framework',
        ]);
        $c['default.database'] = $c['database'];
        if ($custom = $c['system']['custom.setting.files']) {
            foreach ($custom as $name => $file) {
                if ($name && !isset($c[$name]) && \is_string($name)) {
                    $c[$name] = $func($dir . '/' . \ltrim($file, '/\\'));
                }
            }
        }
        return ($this->config = $this->checkConfig($c));
    }

    /**
     * Returns default parameters for optional values in the configuration.
     *
     * Возвращает дефолтные параметры для необязательных значений в конфигурации.
     */
    protected function getDefaultConfig(): array
    {
        return [
            'common' => [
                'log.level.in-cli' => false,
                'system.log.level' => 'warning',
                'log.sort' => true,
                'log.stream' => false,
                'log.format' => 'row',
                'log.db.excess' => 0,
                'container.mock.allowed' => false,
                'twig.options' => [
                    'debug' => true,
                    'charset' => 'utf-8',
                    'auto_reload' => true,
                    'strict_variables' => false,
                    'autoescape' => false,
                    'optimizations' => -1,
                    'cache' => true,
                ],
                'twig.cache.inverted' => [],
                'show.request.id' => true,
                'max.log.size' => 0,
                'max.cache.size' => 0,
            ],
            'main' => [
                'default.lang' => 'en',
                'allowed.languages' => ['en', 'ru', 'de', 'es', 'zh'],
                'db.log.enabled' => false,
                'session.options' => [],
            ],
            'system' => [
                'project.paths' => [],
                'origin.request' => false,
                'ending.slash.url' => 0,
                'ending.url.methods' => ['get'],
                'url.validation' => false,
                'session.name' => 'PHPSESSID',
                'max.session.lifetime' => 0,
                'allowed.route.paths' => [],
                'allowed.structure.parts' => [],
                'page.external.access' => true,
                'events.used' => true,
                'autowiring.mode' => 0,
                'module.dir.name' => 'modules',
                'custom.function.files' => [],
                'custom.setting.files' => [],
                'async.clear.state' => true,
            ],
        ];
    }

    /**
     * # №3
     * Installing the main class loader.
     * You can change this method with inheritance.
     * In this method, a third-party class loader is called on demand,
     * that is, its code is loaded when it is needed.
     *
     * Установка основного загрузчика классов.
     * Можно задать собственный вариант, переопределив этот метод наследованием.
     * В этом методе сторонний загрузчик классов вызывается по требованию,
     * то есть код его загружается когда в нём возникла необходимость.
     */
    protected function setDefaultAutoloader(): void
    {
        // One time function for registering third party loaders.
        // Одноразовая функция для регистрации сторонних загрузчиков.
        function agentLoader($class): bool
        {
            \spl_autoload_unregister('Hleb\agentLoader');

            if (\file_exists($vendorDir = \constant('HLEB_VENDOR_DIR') . '/autoload.php')) {
                require_once $vendorDir;
            }
            \spl_autoload_call($class);
            \spl_autoload_unregister('Hleb\reqLoadFunc');
            \spl_autoload_register('Hleb\reqLoadFunc', true, true);

            return \class_exists($class, false);
        }
        \spl_autoload_register('Hleb\agentLoader', true, true);
    }

    /**
     * Detection of a folder with third-party libraries.
     * You can set your own variant using the HLEB_VENDOR_DIR constant.
     *
     * Обнаружение папки со сторонними библиотеками.
     * Можно задать собственный вариант c помощью константы HLEB_VENDOR_DIR.
     */
    protected function searchVendorDirectory(): string
    {
        if (\defined('HLEB_VENDOR_DIR')) {
            return \constant('HLEB_VENDOR_DIR');
        }
        \define('HLEB_VENDOR_DIR', $dir =  \dirname(__DIR__, 2));

        return $dir;
    }

    /**
     * Detection of a folder with a global project directory.
     * You can change this method with inheritance.
     *
     * Обнаружение папки с глобальной директорией проекта.
     * Можно задать собственный вариант, переопределив этот метод наследованием.
     */
    protected function searchGlobalDirectory(): string
    {
        if (\defined('HLEB_GLOBAL_DIR')) {
            return \constant('HLEB_GLOBAL_DIR');
        }
        $dir = \dirname(__DIR__, 3);
        if (\is_dir($dir . '/app') && \is_dir($dir . '/routes')) {
            return $dir;
        }
        require __DIR__ . '/Init/Connectors/Preload/search-functions.php';

        return (string)search_root();
    }

    /**
     * Detecting the name of the folder with modules from a constant.
     *
     * Обнаружение названия папки с модулями из константы.
     */
    protected function getModuleDirectoryName(): ?string
    {
        if (\defined('HLEB_MODULES_DIR')) {
            $this->moduleDirectory = \constant('HLEB_MODULES_DIR');
            return \basename(\constant('HLEB_MODULES_DIR'));
        }
        return null;
    }

    /**
     * Handling non-existent HTTP method. The best way to do this is server-side.
     * You can change this method with inheritance.
     *
     * Обработка несуществующего HTTP-метода. Лучше всего это делать серверными средствами.
     * Можно задать собственный вариант, переопределив этот метод наследованием.
     */
    protected function handlingNonExistentMethod(SystemRequest $request): bool
    {
        if (!\in_array($request->getMethod(), self::HTTP_TYPES)) {
            Response::replaceHeaders([
                'Allow' => \implode(', ', \array_unique(self::HTTP_TYPES)),
                'Content-Length' => '0',
            ]);
            Response::setBody('');
            Response::setStatus(501);
            return true;
        }
        return false;
    }

    /**
     * Standardization of the incoming Request. Project initialization. Returns Response.
     *
     * Стандартизация входящего Request. Инициализация проекта. Возвращает Response.
     *
     * @throws AsyncExitException|Exception|HttpException
     */
    protected function loadProject(?object $originRequest = null): void
    {
        $startTime = \defined('HLEB_START') ? HLEB_START : \microtime(true);
        $this->config['system']['start.unixtime'] = $startTime;
        SystemSettings::setStartTime($startTime);
        $this->response = null;
        $request = $this->buildRequest($originRequest);
        $debug = (string)($request->getGetParam('_debug') ?? $request->getPostParam('_debug'));
        $debug = $debug !== 'off';
        $this->logger and Log::setLogger($this->logger);
        LogLevel::setDefaultMaxLogLevel(SystemSettings::getCommonValue('max.log.level'));
        DynamicParams::initRequest($request, $debug, $startTime);
        DynamicParams::setAlternateSession($this->session);
        DynamicParams::setAlternateCookies($this->cookies);
        $debug = DynamicParams::isDebug();
        $this->config['common']['debug'] = $debug;
        \date_default_timezone_set($this->config['common']['timezone']);
        \ini_set('display_errors', $debug ? '1' : '0');
        if ($this->config['system']['origin.request']) {
            DynamicParams::setDynamicOriginRequest($originRequest);
        }
        Response::init(new SystemResponse());
        if (SystemSettings::getValue('common', 'show.request.id')) {
            if ($this->mode === self::ASYNC_MODE) {
                Response::addHeaders(['X-Request-ID' => DynamicParams::getDynamicRequestId()]);
            } else {
                \header('X-Request-ID: ' . DynamicParams::getDynamicRequestId());
            }
        }
        if ($this->handlingNonExistentMethod($request)) {
            return;
        }
        if ($this->mode === self::ASYNC_MODE && $request->getMethod() === 'GET') {
            Response::addHeaders(['Content-Type' => 'text/html; charset=UTF-8']);
        }

        // Check the incoming URL data for validity.
        // Проверка входящих данных URL на валидность.
        $this->verifiedUrlOrRedirect($request);

        ProjectLoader::init();
    }

    /**
     * Output of arbitrary data.
     *
     * Вывод произвольных данных.
     */
    protected function output(string $message, ?int $httpCode = null, array $headers = []): void
    {
        if (!Response::getInstance()) {
            Response::init(new SystemResponse());
        }
        Response::setBody($message);
        Response::addHeaders($headers);
        if ($httpCode !== null) {
            Response::setStatus($httpCode);
        }
        if ($this->mode !== self::ASYNC_MODE) {
            $this->headerOutput();
            exit($message);
        }
    }

    /**
     * Combining the rendered content and the contents of the Request object into a single output.
     *
     * Соединение выведенного контента и содержимого объекта Request в единый вывод.
     */
    protected function requestCompletion(string $content = ''): void
    {
        if (!Response::getInstance()) {
            Response::init(new SystemResponse());
        }

        if ($this->mode === self::ASYNC_MODE) {
            $this->session === null or $this->session = $_SESSION ?? [];
            $this->cookies === null or $this->cookies = $_COOKIE ?? [];
            $this->output($content . Response::getBody());
        } else {
            $this->headerOutput();
            echo $content;
            echo Response::getInstance()->getBody();
        }
    }

    /**
     * Checking the correct compilation of the configuration.
     * Configuration validation is done before a custom error handler
     * can be assigned and will be handled by the standard one.
     *
     * Проверка правильного составления конфигурации.
     * Валидация конфигурации производится до возможного назначения
     * пользовательского обработчика ошибок и будет обработана стандартным.
     *
     * @throws Exception
     */
    protected function checkConfig(array $config): array
    {
        $map = [
            'common' => [
                'debug' => ['boolean'], //required
                'log.enabled' => ['boolean'], //required
                'max.log.level' => ['string'], //required
                'max.cli.log.level' => ['string'], //required
                'system.log.level' => ['string'],
                'log.level.in-cli' => ['boolean'],
                'error.reporting' => ['integer'], //required
                'log.sort' => ['boolean'],
                'log.stream' => ['boolean', 'string'],
                'log.format' => ['string'],
                'log.db.excess' => ['integer'],
                'timezone' => ['string'], //required
                'routes.auto-update' => ['boolean'], //required
                'container.mock.allowed' => ['boolean'],
                'app.cache.on' => ['boolean'], //required
                'show.request.id' => ['boolean'],
                'max.log.size' => ['integer'],
                'max.cache.size' => ['integer'],
                'twig.options' => ['array'],
                'twig.cache.inverted' => ['array'],
                // 'config.debug' => ['boolean'], //hidden

            ],
            'database' => [
                'base.db.type' => ['string'],
                'db.settings.list' => ['array'],
            ],
            'main' => [
                'session.enabled' => ['boolean'], //required
                'db.log.enabled' => ['boolean'],
                'default.lang' => ['string'],
                'allowed.languages' => ['array'],
                'session.options' => ['array'],
            ],
            'system' => [
                'classes.autoload' => ['boolean'], //required
                'origin.request' => ['boolean'],
                'ending.slash.url' => ['boolean', 'integer'],
                'ending.url.methods' => ['array'],
                'url.validation' => ['boolean', 'string'],
                'session.name' => ['string'],
                'max.session.lifetime' => ['integer'],
                'allowed.route.paths' => ['array'],
                'allowed.structure.parts' => ['array'],
                'page.external.access' => ['boolean'],
                'module.dir.name' => ['string'],
                'custom.setting.files' => ['array'],
                'custom.function.files' => ['array'],
                'events.used' => ['boolean'],
                'autowiring.mode' => ['integer'],
                // 'start.unixtime' => ['integer'], // system
                // 'module.namespace' => ['string'], system
                // 'route.files.checking' => ['boolean'], // hidden
                // 'async.clear.state' => ['boolean'], // hidden
            ],
        ];
        // The following errors that occur at this level can be displayed without taking into account the settings and debug mode.
        // Следующие ошибки, возникающие на данном уровне, могут быть отображены без учёта настроек и режима отладки.
        foreach ($map as $key => $rule) {
            if (empty($config[$key])) {
                throw new \DomainException("Configuration not found for `$key`");
            }
            foreach ($rule as $k => $val) {
                if (!isset($config[$key][$k])) {
                    throw new \DomainException("Configuration parameter `$k` not found for `$key`");
                }
                if (!\in_array(\gettype($config[$key][$k]), $val, true)) {
                    throw new \DomainException("Wrong type of configuration parameter `$k`.");
                }
            }
        }
        if (!\in_array($config['main']['default.lang'], $config['main']['allowed.languages'])) {
            throw new \DomainException("The `default.lang` param must be present in the `allowed.languages`.");
        }
        foreach ($config['main']['allowed.languages'] as $lang) {
            if (\strtolower($lang) !== $lang) {
                throw new \DomainException("Only lowercase is allowed for `allowed.languages`");
            }
        }
        if (!\in_array($config['common']['log.format'], ['row', 'json'])) {
            throw new \DomainException("Wrong `log.format` format");
        }
        // Initial value of debug mode.
        $config['common']['config.debug'] = $config['common']['debug'];

        return $config;
    }

    /**
     * Assembly of the Request system object from global server variables.
     *
     * Сборка системного объекта Request из глобальных серверных переменных.
     *
     * @param object|null $request
     * @return SystemRequest
     * @throws Exception
     */
    protected function buildRequest(?object &$request = null): SystemRequest
    {
        $_SERVER['HTTP_HOST'] = $this->convertHost($_SERVER['HTTP_HOST']);

        $this->standardization();
        $this->convertForcedMethod($_POST, $_SERVER);
        $protocol = \trim(\stristr($_SERVER["SERVER_PROTOCOL"], '/') ?: '', ' /') ?: '1.1';

        return new SystemRequest(
            $_COOKIE,
            null,
            null,
            null,
            $_SERVER['REQUEST_METHOD'],
            \hl_convert_standard_headers(\getallheaders()),
            $protocol,
            new RequestUri(
                $_SERVER['HTTP_HOST'],
                $_SERVER['DOCUMENT_URI'],
                $_SERVER['QUERY_STRING'],
                $_SERVER['SERVER_PORT'],
                $_SERVER['REQUEST_SCHEME'],
                $_SERVER['REMOTE_ADDR'],
            ));
    }

    /**
     * If an adjusted method value is received from the form, then the previous method is replaced.
     * Returns the modified method or null.
     *
     * Если из формы пришло скорректированное значение метода, то текущий метод заменяется.
     * Возвращает измененный метод или null.
     */
    protected function convertForcedMethod(array &$post, array &$server, ?object &$request = null): ?string
    {
        if ($server['REQUEST_METHOD'] === 'POST' && isset($post['_method']) && \is_string($post['_method'])) {
            $forced = \strtoupper($post['_method']);
            if (!$forced || $forced === 'POST') {
                return null;
            }
            if (!\in_array($forced, ['PUT', 'PATCH', 'DELETE'])) {
                throw new RuntimeException('The `_method` value is incorrect.');
            }
            unset($post['_method']);
            $server['REQUEST_METHOD'] = $forced;
            return $forced;
        }
        return null;
    }

    /**
     * Standardization of incoming server variables for use in the framework.
     *
     * Стандартизация входящих серверных переменных для использования во фреймворке.
     */
    protected function standardization(): void
    {
        $reqUri = $_SERVER['REQUEST_URI'];

        /* If the full address came in the value. */
        /* Если в значении пришёл полный адрес. */
        if (\str_starts_with($reqUri, 'http')) {
            $_SERVER['REQUEST_SCHEME'] = \strstr($reqUri, '://', true);
            $addr = \strstr($reqUri, '://');
            $addr = \ltrim($addr ?: $reqUri, ':/');
            $host = \strstr($addr, '/', true);
            $_SERVER['REQUEST_URI'] = \strstr($addr, '/');
            $_SERVER['HTTP_HOST'] = $host ?: $_SERVER['HTTP_HOST'];
        }

        // Standardization of server values for the entire project.
        // Стандартизация серверных значений для всего проекта.
        \str_starts_with($_SERVER['REQUEST_URI'], '/') or $_SERVER['REQUEST_URI'] = '/' . $_SERVER['REQUEST_URI'];
        $_SERVER['DOCUMENT_URI'] = \strstr($_SERVER['REQUEST_URI'], '?', true) ?: $_SERVER['REQUEST_URI'];
        $uri = \strstr($_SERVER['REQUEST_URI'], '?');
        $uri = \ltrim($uri ?: '', '?');
        $uri = $uri ? '?' . $uri : '';
        $_SERVER['QUERY_STRING'] = $uri;
        if (empty($_SERVER['SERVER_PORT'])) {
            $_SERVER['SERVER_PORT'] = \ltrim((string)\strstr($_SERVER['HTTP_HOST'], ':'), ':') ?: null;
        }
        $_SERVER['SERVER_PORT'] = (int)$_SERVER['SERVER_PORT'];
        if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') ||
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443)) {
            $_SERVER['REQUEST_SCHEME'] = 'https';
            $_SERVER['HTTPS'] = 'on';
        } else {
            $_SERVER['REQUEST_SCHEME'] = 'http';
            $_SERVER['HTTPS'] = 'off';
        }
        $_SERVER['REQUEST_METHOD'] = \strtoupper($_SERVER['REQUEST_METHOD']);

        $ip = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? '';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = \explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = \trim(\reset($ips));
        }
        if (!\filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = '';
        }

        $_SERVER['REMOTE_ADDR'] = $ip;

        isset($_SERVER["SERVER_PROTOCOL"]) or $_SERVER["SERVER_PROTOCOL"] = 'HTTP/1.1';
    }

    /**
     * Host encoding conversion if needed.
     *
     * Преобразование кодировки хоста при необходимости.
     *
     * @throws Exception
     */
    protected function convertHost(string $host): string
    {
        if (\str_starts_with('xn--', $host)) {
            if (!SystemSettings::getRealPath('@library/idnaconv')) {
                throw new \InvalidArgumentException("To convert a domain $host, install the phphleb/idnaconv library");
            }
            $this->hostConvertor or $this->hostConvertor = new IdnaConvert();
            $host = (string)$this->hostConvertor->decode($_SERVER['HTTP_HOST']);
            $this->mode === self::ASYNC_MODE or $this->hostConvertor = null;
        }

        return $host;
    }


    /**
     * Exit handling.
     *
     * Обработка выхода из выполнения.
     */
    protected function scriptExitEmulation(AsyncExitException $e): void
    {
        $this->output($e->getMessage(), $e->getStatus(), $e->getHeaders());
    }

    /**
     * Allows you to throw and intercept errors with enable exit from the script.
     *
     * Позволяет выбрасывать и перехватывать ошибки с включением выхода из скрипта.
     */
    protected function getPreviousErrorControl(\Throwable $e): bool
    {
        $pr = $e->getPrevious();
        while ($pr !== null) {
            if (\get_class($pr) === AsyncExitException::class) {
                $this->scriptExitEmulation($pr);
                return true;
            }
            $pr = $pr->getPrevious();
        }
        return false;
    }

    /**
     * Error page output.
     *
     * Вывод страницы ошибки.
     */
    protected function scriptHttpError(HttpException $e): void
    {
        $this->output($e->getMessageContent(), $e->getHttpStatus());
    }

    /**
     * Handling a runtime error.
     *
     * Обработка возникшей ошибки в ходе выполнения.
     */
    protected function scriptErrorHandling(\Throwable $t): void
    {
        $this->getLogger()->error($t);

        if (DynamicParams::isDebug()) {
            $message = PHP_EOL . '<pre>ERROR: ' . $t . '</pre>' . PHP_EOL;
        } else {
            $message = '';
        }
        $this->output($message, 500);
    }

    /**
     * Initialization of the global parameters of the framework.
     * In asynchronous mode, they will be common to all requests.
     *
     * Инициализация глобальных параметров фреймворка.
     * При асинхронном режиме они будут общими для всех запросов.
     *
     * @throws Exception
     */
    private function initialParameters(array $config): void
    {
        if ($this->publicDirectory !== null) {
            $this->publicDirectory = \rtrim($this->publicDirectory, '/\\');
            if (!\is_dir($this->publicDirectory)) {
                $error = 'Wrong path to the project\'s public directory. ' .
                    'Check that the path is correct and the HLEB_PUBLIC_DIR constant is set in index.php and the ./console file.';
                throw new \InvalidArgumentException($error);
            }
            if (!\defined('HLEB_PUBLIC_DIR')) {
                \define('HLEB_PUBLIC_DIR', $this->publicDirectory);
            }
        }
        if (!\function_exists('get_env')) {
            require __DIR__ . '/Init/Review/basic.php';
        }

        $this->globalDirectory = \rtrim($this->searchGlobalDirectory(), '/\\');
        $this->vendorDirectory = \rtrim($this->searchVendorDirectory(), '/\\');

        $this->initConfig($config);
        if ($this->config['common']['config.debug'] ?? null) {
            \defined('HLEB_STRICT_UMASK') or @\umask(0000);
        }
        \error_reporting($this->config['common']['error.reporting'] ?? null);

        $this->loadBaseClasses(); // #1
        SystemSettings::init($this->mode);
        SystemSettings::setData($this->config);

        // Registration of autoload (will be done in reverse order).
        // Регистрация автозагрузки (будет выполнена в обратном порядке).
        Autoloader::init($this->vendorDirectory, $this->globalDirectory);

        if (!\function_exists('Hleb\agentLoader')) {
            $this->loadOtherClasses(); // #4
            $this->setDefaultAutoloader(); // #3
            $this->loadRequiredClasses(); // #2

            // Allows you to disable the container testing mechanism outside of tests in the project.
            // Позволяет запретить вне тестов в проекте механизм тестирования контейнеров.
            \define('HLEB_CONTAINER_MOCK_ON', $this->config['common']['container.mock.allowed']);
        }

        (new Functions())->create();
    }

    /**
     * # №1
     * Loads always necessary classes before own autoloader.
     * Including the autoloader itself.
     *
     * Загружает всегда необходимые классы ранее собственного загрузчика классов.
     * В том числе и сам авто-загрузчик.
     */
    private function loadBaseClasses(): void
    {
        if (self::$loadResources) {
            return;
        }
        $dir = $this->vendorDirectory . '/phphleb/framework/';
        foreach (
            [BaseSingleton::class => 'Main/Insert/BaseSingleton.php',
                RollbackInterface::class => 'Base/RollbackInterface.php',
                SystemSettings::class => 'Constructor/Data/SystemSettings.php',
                BaseAsyncSingleton::class => 'Main/Insert/BaseAsyncSingleton.php',
                AsyncExitException::class => 'Constructor/Exceptions/Exit/AsyncExitException.php',
                Functions::class => 'Init/Functions.php',
                Autoloader::class => 'Init/Autoloader.php',
                DynamicParams::class => 'Constructor/Data/DynamicParams.php',
                HlebConnector::class => 'Init/Connectors/HlebConnector.php',
                AddressBar::class => 'Init/AddressBar.php',
                Response::class => 'HttpMethods/External/Response.php',
            ] as $name => $path) {
            \class_exists($name, false) or require $dir . $path;
        }
        self::$loadResources = true;
        if ($this->config['common']['debug']) {
            \class_exists(DebugAnalytics::class, false) or require $dir . 'Constructor/Data/DebugAnalytics.php';
        }
    }

    /**
     * # №2
     * Preloading framework library classes.
     *
     * Предварительная загрузка классов библиотек фреймворка.
     */
    private function loadRequiredClasses(): void
    {
        if (!\function_exists('Hleb\reqLoadFunc')) {
            /** @internal */
            function reqLoadFunc(string $class): bool
            {
                $load = Autoloader::makeStatic($class);
                if ($load) {
                    require $load;
                }
                if (DynamicParams::isDebug()) {
                    DebugAnalytics::addData(DebugAnalytics::CLASSES_AUTOLOAD, [$class => $load]);
                }
                return (bool)$load;
            }

            \spl_autoload_register('Hleb\reqLoadFunc', true, true);
        }
    }

    /**
     * # №4
     * Preloading all remaining unloaded project classes.
     *
     * Предварительная загрузка всех оставшихся незагруженными классов проекта.
     */
    private function loadOtherClasses(): void
    {
        if (!\function_exists('Hleb\otherLoadFunc') && $this->config['system']['classes.autoload']) {
            /** @internal */
            function otherLoadFunc(string $class): bool
            {
                $load = Autoloader::makeCustom($class);
                if ($load) {
                    require $load;

                    if (DynamicParams::isDebug()) {
                        DebugAnalytics::addData(DebugAnalytics::CLASSES_AUTOLOAD, [$class => $load]);
                    }
                }
                return (bool)$load;
            }
            \spl_autoload_register('Hleb\otherLoadFunc');
        }
    }

    /**
     * Regular expression URL check and redirect if it doesn't match.
     *
     * Проверка URL по регулярному выражению и редирект при несоответствии.
     */
    private function verifiedUrlOrRedirect(SystemRequest $request): void
    {
        if (!$this->config['common']['config.debug'] && isset($this->config['common']['allowed.hosts'])) {
            $allowed = $this->config['common']['allowed.hosts'];
            $current = \explode(':', $request->getUri()->getHost())[0];
            if (!$allowed || !\is_array($allowed)) {
                $this->getLogger()->warning('The `allowed.hosts` parameter of the `common` configuration is not set!');
            } else if (!in_array($current, $allowed)) {
                $isValid = false;
                foreach ($allowed as $pattern) {
                    if (\str_starts_with($pattern, '/') && \preg_match($pattern, $current)) {
                        $isValid = true;
                        break;
                    }
                }
                if (!$isValid) {
                    async_exit('Invalid Host header', 400);
                }
            }
        }
       if ($request->getUri()->getPath() === '/') {
           return;
       }
        $urlValidator = $this->mode === self::ASYNC_MODE ? ($this->addressBar ??= new AddressBar()) : new AddressBar();
        $urlValidator->init(SystemSettings::getData(), $request);
        if ($urlValidator->check()->isUrlCompare()) {
            return;
        }
        async_exit('', 301, \array_merge(Response::getHeaders(), ['Location' => $urlValidator->getResultUrl()]));
    }

    /**
     * Display status and headers for a standard (non-asynchronous) request.
     *
     * Вывод статуса и заголовков для стандартного (не асинхронного) запроса.
     */
    private function headerOutput(): void
    {
        $res = Response::getInstance();
        if (!\headers_sent()) {
            foreach ($res->getHeaders() as $name => $header) {
                if (\is_array($header)) {
                    foreach($header as $h) {
                        \header("$name: $h");
                    }
                } else {
                    \header("$name: $header");
                }
            }
            if (\http_response_code() !== false && \http_response_code() !== 200) {
                return;
            }
            if ($res->getReason()) {
                $pr = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/' . $res->getVersion();
                \header($pr . ' ' . $res->getStatus() . ' ' . $res->getReason(), true, $res->getStatus());
            } else {
                \http_response_code($res->getStatus());
            }
        }
    }

    /**
     * Sets error handlers at the lowest allowed loading level of the framework.
     *
     * Устанавливает обработчики ошибок на допустимо нижнем уровне загрузки фреймворка.
     */
    private function setErrorHandler(): void
    {
        // These constants should only be used for debugging the project.
        // Эти константы должны быть использованы только для отладки проекта.
        if (!\defined('HLEB_CLI_MODE')) {
            \define('HLEB_CLI_MODE', $this->mode === self::CONSOLE_MODE);
        }
        if (!\defined('HLEB_LOAD_MODE')) {
            \define('HLEB_LOAD_MODE', $this->mode);
        }
        if (\function_exists('Hleb\core_user_log')) {
            return;
        }
        $logger = $this->logger;
        /**
         * Outputting errors to the logger even if some of the classes are not loaded.
         *
         * Вывод ошибок в способ логирования, даже если часть классов не загружена.
         *
         * @internal - do not use outside the framework core.
         */
        function core_user_log(int $errno, string $errstr, ?string $errfile = null, ?int $errline = null): bool
        {
            global $logger;

            $level = \error_reporting();
            if ($level >= 0 && ($level === 0 || !($level & $errno))) {
                return true;
            }
            \class_exists(ErrorLog::class, false) or require __DIR__ . '/Init/ErrorLog.php';

            ErrorLog::setLogger($logger);

            return ErrorLog::execute($errno, $errstr, $errfile, $errline);
        }

        \set_error_handler('Hleb\core_user_log');

        /** @internal - do not use outside the framework core. */
        function core_bootstrap_shutdown(): void
        {
            if ($e = \error_get_last() and $e['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR)) {
                core_user_log(E_ERROR, $e['message'] ?? '', $e['file'] ?? null, $e['line'] ?? null);
            }
        }
        /** @internal - do not use outside the framework core. */
        function core_bootstrap_log_finished(): void
        {
            if (\class_exists(FileLogger::class, false)) {
                FileLogger::finished();
            }
        }

        if ($this->mode !== self::ASYNC_MODE) {
            \register_shutdown_function('Hleb\core_bootstrap_shutdown');
        }
        \register_shutdown_function('Hleb\core_bootstrap_log_finished');
    }

    /**
     * Performs actions for final processing of logs.
     *
     * Выполняет действия для конечной обработки логов.
     */
    protected function logsPostProcessing(): void
    {
        if (\class_exists(FileLogger::class, false)) {
            FileLogger::finished();
        }
    }
}
