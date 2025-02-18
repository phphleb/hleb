<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb;

use App\Bootstrap\ContainerFactory;
use App\Middlewares\Hlogin\Registrar;
use AsyncExitException;
use Exception;
use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\HttpMethods\{External\RequestUri, External\SystemRequest};
use Hleb\Constructor\Data\DebugAnalytics;
use Hleb\HttpMethods\External\Response as SystemResponse;
use Hleb\HttpMethods\Intelligence\Cookies\AsyncCookies;
use Hleb\Init\ErrorLog;
use Hleb\Init\Headers\ParsePsrHeaders;
use Hleb\Init\Headers\ParseSwooleHeaders;
use Hleb\Main\Logger\LoggerInterface;
use Hleb\Static\Response;
use Throwable;

#[Accessible] #[AvailableAsParent]
class HlebAsyncBootstrap extends HlebBootstrap
{
    private static int $processNumber = 0;

    /**
     * Constructor with initialization.
     * In asynchronous mode, must be called once outside the loop.
     *
     * Конструктор с инициализацией.
     * В асинхронном режиме должен быть вызван один раз вне цикла.
     *
     * @param string|null $publicPath - full path to the public directory of the project.
     *                                - полный путь к публичной директории проекта.
     *
     * @param array $config - an array replacing the configuration data.
     *                      - заменяющий конфигурационные данные массив.
     *
     * @throws Throwable
     */
    public function __construct(?string $publicPath = null, array $config = [], ?LoggerInterface $logger = null)
    {
        $this->mode = self::ASYNC_MODE;

        // In asynchronous mode, an initialization error should be logged.
        // В асинхронном режиме ошибка инициализации должна быть отправлена в лог.
        try {
            parent::__construct($publicPath, $config, $logger);
        } catch (\Throwable $t) {
            $this->errorLog($t);
            throw $t;
        }
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function setLogger(LoggerInterface $logger): static
    {
        parent::setLogger($logger);

        return $this;
    }

    /**
     * Processing the request and displaying the result. In asynchronous mode, it is called inside the loop.
     * May contain a PSR-7 like object.
     * @param $request - with further initialization.
     * If there is no such object, loading data from global variables.
     *
     * @param $session - parameter allows you to use an external
     * session storage mechanism along with using
     * $_SESSION array to manipulate session data and return it to the session handler.
     * Standard session initialization will be disabled.
     * @param $cookie - parameter works similarly to external sessions for Cookies.
     *
     *
     * Обработка запроса и вывод результата. В асинхронном режиме вызывается внутри цикла.
     * Может содержать PSR-7 подобный объект $request с дальнейшей инициализацией.
     * Если такого объекта нет, загрузка данных из глобальных переменных.
     *
     * Параметр $session позволяет использовать внешний механизм хранения
     * сессий вместе с использованием массива $_SESSION для оперирования данными сессий
     * и возвращения его в обработчик сессий.
     * Стандартная инициализация сессий при этом будет отключена.
     * Параметр $cookies действует аналогично внешним сессиям для Cookies.
     */
    #[\Override]
    public function load(?object $request = null, ?array $session = null, ?array $cookie = null): HlebAsyncBootstrap
    {
        $this->session = $session;
        $this->cookies = $cookie;

        self::$processNumber++;

        \ob_start();
        try {
            try {
                $this->loadProject($request);

                $this->requestCompletion((string)\ob_get_contents());

            } catch (AsyncExitException $e) {
                $this->asyncScriptExitEmulation($e, (string)\ob_get_contents());

            } catch (HttpException $e) {
                $this->scriptHttpError($e);

            } catch (Throwable $t) {
                $this->getPreviousErrorControl($t) or $this->scriptErrorHandling($t);
            }
        } catch (\Throwable) {
            /*
             * There is a possibility of an error in error handling,
             * but an error should not be returned here.
             *
             * Существует вероятность ошибки в обработке ошибок,
             * но ошибки вернуться здесь не должно.
             */
        }
        \ob_end_clean();

        $this->logsPostProcessing();

        $this->afterRequest();

        return $this;
    }

    /**
     * Ending an asynchronous request.
     *
     * Завершение асинхронного запроса.
     */
    public function afterRequest(): void
    {
        try {
            if (\class_exists(Response::class, false)) {
                $this->response = Response::getInstance() ?? new SystemResponse();
            }
            if (\session_status() === PHP_SESSION_ACTIVE) {
                AsyncCookies::setSessionName(\session_name());
                \session_write_close();
                \session_abort();
            }
            AsyncCookies::output();

        } catch (\Throwable) {
            if (\session_status() === PHP_SESSION_ACTIVE) {
                \session_abort();
            }
        }

        $_GET = $_POST = $_SERVER = $_SESSION = $_COOKIE = $_REQUEST = $_FILES = [];

        self::prepareAsyncRequestData($this->config, self::$processNumber);
    }

    /**
     * Returns the result of request processing in the load() method as an object with data,
     * which can then be converted (substituted) into any PSR-7 object.
     *
     * Возвращает результат обработки запроса в методе load() в виде объекта с данными,
     * которые могут быть затем преобразованы (подставлены) в любой объект по PSR-7.
     */
    public function getResponse(): SystemResponse
    {
        return $this->response;
    }

    /**
     * When adding the $session array in the load() method, it returns
     * the result of using the $_SESSION variable.
     * An alternative way to work with sessions.
     *
     * При добавлении массива $session в методе load() возвращает
     * результат использования переменной $_SESSION.
     * Альтернативный вариант работы с сессиями.
     */
    public function getSession(): ?array
    {
        return $this->session;
    }

    /**
     * When adding the $cookies array in the load() method, it returns
     * the result of using the $_COOKIE variable.
     * An alternative way to work with cookies.
     *
     * При добавлении массива $cookies в методе load() возвращает
     * результат использования переменной $_COOKIE.
     * Альтернативный вариант работы с Cookies.
     */
    public function getCookies(): ?array
    {
        return $this->cookies;
    }

    /**
     * Saving the error in prepared form if it occurred at the top level.
     *
     * Сохранение ошибки в подготовленном виде, если она возникла на самом верхнем уровне.
     */
    public function errorLog(\Throwable $e): void
    {
        // The error may be in the error handler itself.
        // Ошибка может быть в самом обработчике ошибок.
        try {
            \class_exists(ErrorLog::class, false) or require __DIR__ . '/Init/ErrorLog.php';
            ErrorLog::log($e);
        } catch (\Throwable $t) {
            \error_log((string)$e);
            \error_log((string)$t);
        }
    }

    /**
     * Preparing data to use an asynchronous request.
     *
     * Подготовка данных к использованию асинхронного запроса.
     *
     * @see HlebQueueBootstrap::prepareAsyncRequestData()
     *
     * @internal
     */
    protected static function prepareAsyncRequestData(array $config, int $processNumber): void
    {
        // If your application does not use state storage, you can disable state cleanup.
        // Если в приложении не используется хранение состояния, то можно отключить его очистку.
        if ($config['system']['async.clear.state'] ?? true) {
            foreach (\get_declared_classes() as $class) {
                \is_a($class, RollbackInterface::class, true) and $class::rollback();
            }
        }
        foreach ([ContainerFactory::class, Registrar::class, DebugAnalytics::class, ErrorLog::class] as $class) {
            \class_exists($class, false) and $class::rollback();
        }

        /*
         * Periodically clean up used memory and call GC. Will be applied to every $rate request.
         * For example, if you pass HLEB_ASYNC_RE_CLEANING=3, then after every third request.
         *
         * Периодическая очистка используемой памяти и вызов GC. Будет применено к каждому $rate запросу.
         * Например, если передать HLEB_ASYNC_RE_CLEANING=3, то после каждого третьего запроса.
         */
        $rate = (int)get_env('HLEB_ASYNC_RE_CLEANING', get_constant('HLEB_ASYNC_RE_CLEANING', self::DEFAULT_RE_CLEANING));
        if ($rate >= 0 && ($rate === 0 || $processNumber % $rate == 0)) {
            \gc_collect_cycles();
            \gc_mem_caches();
        }
        \memory_reset_peak_usage();
    }

    /**
     * The framework can work with any incoming request, where the
     * (or set before executing the load()) method  the corresponding values of $_SERVER.
     * In extreme cases, this data can be taken from a PSR-7 like object
     * (Psr\Http\Message\ServerRequestInterface).
     *
     * Фреймворк может работать с любым входящим запросом, где определены
     * (или заданы до выполнения метода load())  соответствующие значения $_SERVER.
     * В крайнем случае эти данные могут быть взяты из объекта по подобию PSR-7
     * (Psr\Http\Message\ServerRequestInterface).
     *
     * @param object|null $request
     * @return SystemRequest
     * @throws Exception
     * @see self::load()
     */
    #[\Override]
    protected function buildRequest(?object &$request = null): SystemRequest
    {
        $headers = [];
        if ($request !== null) {
            if (\method_exists($request, 'getCookieParams')) {
                [$body, $headers] = $this->parsePsr7Request($request);
                $headers = $this->parseHeaders($headers, ParsePsrHeaders::class);
            } else if (\method_exists($request, 'rawContent') ||
                \method_exists($request, 'getContent')
            ) {
                [$body, $headers] = $this->parseSwooleRequest($request);
                $headers = $this->parseHeaders($headers, ParseSwooleHeaders::class);
            } else if (\str_starts_with($request::class, "Workerman\\")) {
                [$body, $headers] = $this->parseWorkermanRequest($request);
            } else {
                // Data will be received from $_SERVER.
                // Данные будут получены из $_SERVER.
                $body = null;
            }
        }

        $_SERVER['HTTP_HOST'] = $this->convertHost($_SERVER['HTTP_HOST']);

        $this->standardization();
        $this->convertForcedMethod($_POST, $_SERVER, $request);
        $protocol = \trim(\stristr($_SERVER["SERVER_PROTOCOL"] ?? '','/') ?: '', ' /') ?: '1.1';

        $streamBody = isset($body) && \is_object($body) ? $body : null;
        $rawBody    = isset($body) && \is_string($body) ? $body : null;
        $parsedBody = isset($body) && \is_array($body)  ? $body : null;

        $_SERVER['REMOTE_ADDR'] = \strip_tags((string)($_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null));

        return new SystemRequest(
            (array)$_COOKIE,
            $rawBody,
            $parsedBody,
            $streamBody,
            $_SERVER['REQUEST_METHOD'],
            $headers,
            $protocol,
            new RequestUri(
                (string)$_SERVER['HTTP_HOST'],
                (string)$_SERVER['DOCUMENT_URI'],
                $_SERVER['QUERY_STRING'],
                $_SERVER['SERVER_PORT'],
                $_SERVER['REQUEST_SCHEME'],
                $_SERVER['REMOTE_ADDR'],
            ));
    }

    /**
     * @inheritDoc
     */
    protected function convertForcedMethod(array &$post, array &$server, ?object &$request = null): ?string
    {
        $forced = parent::convertForcedMethod($post, $server);
        if ($forced && $request) {
            try {
                if (\method_exists($request, 'withMethod')) {
                    // PSR7
                    $request = $request->withMethod($forced);
                } else if (\property_exists($request, 'server') && \is_array($request->server)) {
                    // Swoole
                    $request->server['request_method'] = \strtolower($forced);
                } else if (\method_exists($request, 'method')) {
                    // Workerman
                    $request = new ($request::class)(\strtolower($forced), $request->uri(), $request->header(), $request->rawBody());
                }
            } catch (\Throwable $t) {
                $this->getLogger()->warning($t);
            }
        }
        return $forced;
    }

    /**
     * An attempt to parse a PSR-7 object.
     *
     * Попытка разбора PSR-7 объекта.
     *
     * @see \Psr\Http\Message\ServerRequestInterface
     */
    protected function parsePsr7Request(object $req): array
    {
        empty($_COOKIE) and $_COOKIE = $req->getCookieParams();
        empty($_POST) and $_POST = (array)$req->getParsedBody();
        $body = method_exists($req, 'getBody') ? (string)$req->getBody() : '';
        empty($_GET) and $_GET = (array)$req->getQueryParams();
        empty($_FILES) and $_FILES = $req->getUploadedFiles();
        isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] = \strtoupper((string)$req->getMethod());
        $headers = $req->getHeaders();
        if (\method_exists($req, 'getProtocolVersion')) {
            $_SERVER["SERVER_PROTOCOL"] = 'HTTP/' . $req->getProtocolVersion();
        }

        if (\method_exists($req, 'getUri') && \is_object($req->getUri())) {
            /** @var object $uri */
            $uri = $req->getUri();
            isset($_SERVER['HTTP_HOST']) or $_SERVER['HTTP_HOST'] = $uri->getHost();
            isset($_SERVER['DOCUMENT_URI']) or $_SERVER['DOCUMENT_URI'] = $uri->getPath();
            isset($_SERVER['SERVER_NAME']) or $_SERVER['SERVER_NAME'] = $uri->getHost();
            isset($_SERVER['QUERY_STRING']) or $_SERVER['QUERY_STRING'] = $uri->getQuery();
            isset($_SERVER['SERVER_PORT']) or $_SERVER['SERVER_PORT'] = $uri->getPort();
            isset($_SERVER['REQUEST_URI']) or $_SERVER['REQUEST_URI'] = $uri->getPath() . '?' .
                \ltrim($uri->getQuery(), '?/');
            if (empty($_SERVER['REMOTE_ADDR'])) {
                if (\method_exists($req, 'getServerParams')) {
                    $params = $uri->getServerParams();
                    $_SERVER['REMOTE_ADDR'] = (string)($params['REMOTE_ADDR'] ?? $params['HTTP_CLIENT_IP'] ?? $params['HTTP_X_FORWARDED_FOR'] ?? null);
                } else if (\filter_var($_SERVER['HTTP_HOST'], FILTER_VALIDATE_IP)) {
                    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_HOST'];
                }
            }
        }
        return [$body, $headers];
    }

    /**
     * An attempt to parse an object according to the PHP Swoole specification.
     *
     * Попытка разбора объекта по спецификации PHP Swoole.
     *
     * @see \Swoole\Http\Request
     */
    protected function parseSwooleRequest(object $req): array
    {
        $headers = $req->header;
        $server = $req->server;
        $_COOKIE = $req->cookie ?? [];
        $_POST = $req->post ?? [];
        $_GET = $req->get ?? [];
        $_FILES = $req->files ?? [];
        $_SERVER['HTTP_HOST'] = $server['remote_addr'] ?? $headers['host'];
        $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
        $_SERVER['REMOTE_ADDR'] = $server['remote_addr'] ?? '';
        $_SERVER['REQUEST_METHOD'] = \strtoupper((string)$server['request_method']);
        $_SERVER['DOCUMENT_URI'] = $server['path_info'] ?? '';
        $_SERVER['SERVER_PORT'] = $server['server_port'] ?? null;
        $_SERVER['QUERY_STRING'] = $server['query_string'] ?? '';
        $_SERVER['REQUEST_URI'] = $server['request_uri'] ?? '';
        $_SERVER["SERVER_PROTOCOL"] = $server['server_protocol'] ?? 'HTTP/1.1';

        $_SERVER['HTTPS'] = $_SERVER['SERVER_PORT'] == 443 ? 'on' : 'off';
        // An additional field by which you can get the type of HTTP connection scheme.
        // Дополнительное поле по которому можно получить тип HTTP-схемы подключения.
        if (isset($server['https'])) {
            $_SERVER['HTTPS'] = $server['https'] === 'on' ? 'on' : 'off';
        }
        $body = method_exists($req, 'rawContent') ? $req->rawContent() : $req->getContent();

        return [(string)$body, $headers];
    }

    /**
     * An attempt to parse an object according to the PHP Workerman specification.
     *
     * Попытка разбора объекта по спецификации PHP Workerman.
     *
     * @param \Workerman\Protocols\Http\Request $req
     */
    protected function parseWorkermanRequest(object $req): array
    {
        // Override $_SERVER['HTTP_HOST'] on initialization if it's different.
        // Переопределите $_SERVER['HTTP_HOST'] при инициализации, если он отличается.
        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? $req->host(true);
        $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
        $_SERVER['REMOTE_ADDR'] = $req->connection?->getRemoteIp();
        $get = $req->get() ?: [];
        $_GET = $get;
        $_POST = $req->post() ?: [];
        $_SERVER['QUERY_STRING'] = $get ? '?' . \http_build_query($get) : '';
        $_SERVER['REQUEST_METHOD'] = \strtoupper((string)$req->method());
        $_SERVER['DOCUMENT_URI'] = $req->uri() ?: '';
        $_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? $req->connection?->getLocalPort();
        $_SERVER['REQUEST_URI'] = $_SERVER['DOCUMENT_URI'] . $_SERVER['QUERY_STRING'];
        $_SERVER['HTTPS'] = $_SERVER['HTTPS'] ?? ($_SERVER['SERVER_PORT'] == 443 ? 'on' : 'off');
        $_SERVER["SERVER_PROTOCOL"] = 'HTTP/' . $req->protocolVersion();

        $body = $req->rawBody();
        $headers = $req->header() ?: [];
        $_SESSION = $req->session() ? $req->session()->all() : [];
        $_COOKIE = $req->cookie() ?: [];
        $_FILES = $req->file() ?: [];

        return [$body, $headers];
    }

    /**
     * Converts headings to standard format.
     *
     * Преобразует заголовки в стандартный вид.
     */
    protected function parseHeaders(mixed $headers, string $class): array
    {
        $headers = (new $class())->update($headers);

        return \array_change_key_case($headers, CASE_LOWER);
    }

    /**
     * Execution exit handling for asynchronous mode.
     *
     * Обработка выхода из выполнения для асинхронного режима.
     */
    private function asyncScriptExitEmulation(AsyncExitException $e, string $content): void
    {
        $this->output($content . $e->getMessage(), $e->getStatus(), $e->getHeaders());
    }
}
