<?php

/*declare(strict_types=1);*/

namespace Hleb\HttpMethods\External;

use Hleb\ParseException;

/**
 * An internal framework object to store the state of the current HTTP request.
 *
 * Внутренний объект фреймворка для хранения состояния текущего HTTP-запроса.
 */
final class SystemRequest
{
    public function __construct(
        private readonly array       $cookieParams,
        private null|string          $rawBody,
        private null|array           $parsedBody,
        private readonly ?object     $streamBody,
        private readonly string      $method,
        private readonly array       $getParams,
        private readonly array       $postParams,
        private readonly array       $headers,
        private readonly string      $protocol,
        private readonly RequestUri  $uri,
    )
    {
    }

    /**
     * Returns a list of Cookies ($_COOKIE) set when the framework was initialized.
     *
     * Возвращает список Cookies ($_COOKIE), установленных при инициализации фреймворка.
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * Returns the converted request body, for example if it is in JSON format.
     * Does not work with `multipart/form-data`.
     * (!) The data is returned in its original form,
     * so you need to check it for vulnerabilities yourself.
     *
     * Возвращает преобразованное тело запроса, например, если оно в формате JSON.
     * Не работает с `multipart/form-data`.
     * (!) Данные возвращаются в исходном виде, поэтому нужно
     * самостоятельно проверить их на уязвимости.
     */
    public function getParsedBody(): null|array
    {
        if (!empty($_POST)) {
            return \hl_clear_tags($_POST);
        }
        if ($this->parsedBody === null) {
            $rawBody = $this->getRawBody();
            $body = \trim($rawBody);
            // Deferred attempt to parse the JSON body of the request.
            // Отложенная попытка получения JSON-тела запроса.
            if ((str_starts_with($body, '{') && str_ends_with($body, '}')) ||
                (str_starts_with($body, '[') && str_ends_with($body, ']'))
            ) {
                try {
                    $this->parsedBody = \json_decode($body, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
                } catch(\JsonException $e) {
                   throw new ParseException($e);
                }
            } else if (\str_contains($body, '=')) {
                \parse_str($body, $this->parsedBody);
            }
            (\is_array($this->parsedBody) || \is_object($this->parsedBody)) or $this->parsedBody = null;
        }
        if (is_object($this->parsedBody)) {
            return \hl_clear_tags((array)$this->parsedBody);
        }

        return $this->parsedBody;
    }

    /**
     * Returns the request body in its original form.
     * Does not work with `multipart/form-data`.
     * (!) The data is returned in its original form,
     * so you need to check it for vulnerabilities yourself.
     *
     * Возвращает тело запроса в исходном виде.
     * Не работает с `multipart/form-data`.
     * (!) Данные возвращаются в исходном виде, поэтому нужно
     * самостоятельно проверить их на уязвимости.
     */
    public function getRawBody(): string
    {
        if ($this->rawBody !== null) {
            return $this->rawBody;
        }
        if ($this->streamBody !== null) {
            return (string)$this->streamBody;
        }
        return $this->rawBody = (string)\file_get_contents('php://input');
    }

    /**
     * Returns the current HTTP request method, such as 'GET' (from $_SERVER['REQUEST_METHOD']).
     *
     * Возвращает текущий метод HTTP-запроса, например 'GET' (из $_SERVER['REQUEST_METHOD']).
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns an object containing the URL data from the request.
     *
     * Возвращает объект с содержанием данных URL из запроса.
     */
    public function getUri(): RequestUri
    {
        return $this->uri;
    }

    /**
     * Returns the GET request parameters ($_GET) set when the framework was initialized.
     *
     * Возвращает GET-параметры запроса ($_GET), установленные при инициализации фреймворка.
     */
    public function getGetParams(): array
    {
        return $this->getParams;
    }

    /**
     * Returns the specific GET parameter by name, or NULL if it does not exist.
     *
     * Возвращает конкретный GET-параметр по названию или NULL в случае его отсутствия.
     */
    public function getGetParam(string|int|float $name): null|array|string|int|float
    {
        return $this->getParams[$name] ?? null;
    }

    /**
     * Returns the POST request parameters ($_POST) set when the framework was initialized.
     *
     * Возвращает POST-параметры запроса ($_POST), установленные при инициализации фреймворка.
     */
    public function getPostParams(): array
    {
        return $this->postParams;
    }

    /**
     * Returns the specific POST parameter by name, or NULL if it does not exist.
     *
     * Возвращает конкретный POST-параметр по названию или NULL в случае его отсутствия.
     */
    public function getPostParam(string|int|float $name): null|array|string|int|float
    {
        return $this->postParams[$name] ?? null;
    }

    /**
     * Returns a list of headers set prior to framework initialization in the Request object.
     *
     * Возвращает список заголовков, установленных до инициализации фреймворка в объекте Request.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Checking for the existence of a header by name.
     *
     * Проверка существования заголовка по названию.
     */
    public function hasHeader($name): bool
    {
        return \array_key_exists(
            \strtr($name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'),
            $this->headers
        );
    }

    /**
     * Get an array of matching headers by name.
     *
     * Получение массива соответствующих заголовков по названию.
     */
    public function getHeader($name): array
    {
        $name = \strtr($name, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
        if (!$this->hasHeader($name)) {
            return [];
        }

        return $this->headers[$name];
    }

    /**
     * Get the corresponding headers as a string.
     *
     * Получение соответствующих названию заголовков в виде строки.
     */
    public function getHeaderLine($name): string
    {
        return \implode(', ', $this->getHeader($name));
    }

    /**
     * Returns the HTTP protocol version, for example `1.1`.
     *
     * Возвращает версию HTTP-протокола, например `1.1`.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    /**
     * In exceptional cases, it is necessary to process the download stream.
     *
     * В исключительных случаях необходимо для обработки загружаемого потока.
     *
     * @see \Psr\Http\Message\StreamInterface;
     */
    public function getStreamBody(): ?object
    {
        return $this->streamBody;
    }
}