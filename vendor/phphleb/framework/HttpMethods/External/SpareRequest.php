<?php

declare(strict_types=1);

namespace Hleb\HttpMethods\External;

use Hleb\Constructor\Attributes\{Accessible, Autowiring\NoAutowire, AvailableAsParent};

/**
 * Can be useful when initializing the framework (for example, for tests) in asynchronous mode.
 * Contains only those methods that are needed in the framework.
 * This class cannot be used for DI.
 * An example of how to initialize a framework for integration tests:
 *
 * Может пригодиться при инициализации фреймворка (например для тестов) в асинхронном режиме.
 * Содержит только те методы, которые нужны для фреймворка.
 * Этот класс не получиться использовать для DI.
 * Пример того, как можно инициализировать фреймворк для интеграционных тестов:
 *
 * ```php
 * $config = [
 *      'common' => require $globalDir . '/config/common-test.php',
 *      'database' => require $globalDir . '/config/database-test.php',
 *      'main' => require $globalDir . '/config/main-test.php',
 *      'system' => require $globalDir . '/config/system-test.php',
 *      'other' => require $globalDir . '/config/other-test.php',
 *       // An array of module settings for testing.
 *      'modules' => require $globalDir . '/config/modules-test.php',
 * ];
 * $logger = new NullLogger();
 * $request = new SpareRequest($requestUri, 'GET');
 * $framework = (new HlebAsyncBootstrap($publicDir, $config, $logger));
 * $response = $framework->load($request, session: [])->getResponse();
 *
 * ```
 */
#[NoAutowire] #[Accessible] #[AvailableAsParent]
readonly class SpareRequest
{
    public function __construct(
        private RequestUri $uri,
        private string $method = 'GET',
        private array $parsedBody = [],
        private array $headers = [],
        private string $body = '',
        private array $cookieParams = [],
        private array $uploadedFiles = [],
        private string $protocolVersion = '1.1',
    ) {}

    /**
     * Returns cookie parameters.
     *
     * Возвращает параметры cookie.
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * Returns the parsed body of the request (for example, data for the $_POST array).
     *
     * Возвращает распарсенное тело запроса (например, данные для массива $_POST).
     */
    public function getParsedBody(): array
    {
        return $this->parsedBody;
    }

    /**
     * Returns the request body (content).
     *
     * Возвращает тело запроса (контент).
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Returns GET request parameters.
     *
     * Возвращает GET-параметры запроса.
     */
    public function getQueryParams(): array
    {
        $params = $this->getUri() ? \parse_url((string)$this->getUri(), PHP_URL_QUERY) : '';

        $queryParams = [];
        \parse_str((string)$params, $queryParams);

        return $queryParams;
    }

    /**
     * Returns data from downloaded files.
     *
     * Возвращает данные загруженных файлов.
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * Returns the HTTP request method.
     *
     * Возвращает HTTP-метод запроса.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns an array of HTTP headers.
     *
     * Возвращает массив заголовков.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Returns the protocol version.
     *
     * Возвращает версию протокола.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Returns the system object URI.
     *
     * Возвращает системный объект URI.
     */
    public function getUri(): RequestUri
    {
        return $this->uri;
    }

    /**
     * Returns a new object with a modified HTTP method.
     * Due to immutability, an object remains unchanged.
     *
     * Возвращает новый объект с измененным HTTP методом.
     * Благодаря иммутабельности объект остается неизменным.
     */
    public function withMethod(string $newMethod): self
    {
        return new self(
            uri: $this->uri,
            method: $newMethod,
            parsedBody: $this->parsedBody,
            headers: $this->headers,
            body: $this->body,
            cookieParams: $this->cookieParams,
            uploadedFiles: $this->uploadedFiles,
            protocolVersion: $this->protocolVersion,
        );
    }
}
