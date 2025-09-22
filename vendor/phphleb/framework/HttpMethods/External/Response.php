<?php

/*declare(strict_types=1);*/

namespace Hleb\HttpMethods\External;

use Hleb\Constructor\Attributes\NotFinal;

#[NotFinal]
class Response
{
    final public const PHRASES = [
        100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing',
        200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-status', 208 => 'Already Reported',
        300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Switch Proxy', 307 => 'Temporary Redirect',
        400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Time-out', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Large', 415 => 'Unsupported Media Type', 416 => 'Requested range not satisfiable', 417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Unordered Collection', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Time-out', 505 => 'HTTP Version not supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 511 => 'Network Authentication Required',
    ];

    private const UPPERCASE = ['MD5', 'MIME', 'TE', 'URI', 'WWW'];

    private array $body = [];

    private string $version = '1.1';

    private array $headers = [];

    /**
     * @param \Stringable|string|null $body - message body.
     *                                      - тело сообщения.
     *
     * @param int|null $status - HTTP status or null (default 200).
     *                          - HTTP-статус или null (по умолчанию 200).
     *
     * @param array $headers - list of headers key => value.
     *                       - список заголовков ключ => значение.
     *
     * @param string|null $reason - a semantic phrase for the HTTP status or null (autodetection).
     *                             - смысловая фраза для HTTP-статуса или null (автоопределение).
     *
     * @param string|null $version - HTTP protocol version or null (autodetection).
     *                             - версия HTTP-протокола или null (автоопределение).
     */
    public function __construct(
        \Stringable|string|null $body = null,
        private ?int $status = null,
        array $headers = [],
        private ?string $reason = null,
        ?string $version = null,
    ) {
        if ($body !== null) {
            $this->body[] = (string)$body;
        }
        if ($version === null) {
            if (!empty($_SERVER['SERVER_PROTOCOL'])) {
                $this->version = \trim(\strstr((string)$_SERVER['SERVER_PROTOCOL'], '/'), '/ ');
            }
        } else {
            $this->version = $version;
        }
        $this->addHeaders($headers);
        if ($status === null) {
            $this->status = 200;
            $this->reason = null;
        } else {
            $this->setStatus($status, $this->reason);
        }

    }

    /**
     * Returns the HTTP response code, default is 200.
     *
     * Возвращает HTTP-код ответа, по умолчанию равен 200.
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Sets the HTTP response code.
     *
     * Устанавливает HTTP-код ответа.
     *
     * @param int $status
     * @param string|null $reason - if the text of the response code differs from the standard one,
     *                              then it can be specified in this parameter.
     *
     *                            - если текст кода ответа отличается от стандартного,
     *                              то его можно указать в этом параметре.
     */
    public function setStatus(int $status, ?string $reason = null): void
    {
        $this->status = $status;
        if ($reason === null && isset(self::PHRASES[$status])) {
            $this->reason = self::PHRASES[$status];
        } else {
            $this->reason = $reason ?? '';
        }
    }

    /**
     * Returns the specified text describing the HTTP response code.
     *
     * Возвращает заданный текст описания HTTP-кода ответа.
     */
    public function getReason(): ?string
    {
        return $this->reason ?: null;
    }

    /**
     * Returns the set headers of the form ['name' => ['value1', 'value2']].
     *
     * Возвращает установленные заголовки вида ['название' => ['значение1', 'значение2']].
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Returns a set of only associative headers of type
     * ['name1: value1', 'name2: value2'].
     *
     * Возвращает набор только ассоциативных заголовков типа
     * ['название1: значение1', 'название2: значение2'].
     */
    public function getPrepareHeaders(): array
    {
        $result = [];
        foreach ($this->headers as $name => $headers) {
            $result[] = $name . ': ' . $headers;
       }
        return $result;
    }

    /**
     * Sets the headers (replacing the entire set) of the form
     * ['name' => 'value'] or ['name' => ['value1', 'value2']].
     *
     * Устанавливает заголовки (полностью заменяя весь набор) вида
     * ['название' => 'значение'] или ['название' => ['значение1', 'значение2']].
     */
    public function replaceHeaders(array $headers): void
    {
        $items = [];
        foreach ($headers as $name => $value) {
            if (\is_array($value)) {
                $items[$this->normalizeHeaderName($name)] = $value;
            } else {
               $items[$this->normalizeHeaderName($name)] = [$value];
            }
        }
        $this->headers = $items;
    }

    /**
     * Sets a single HTTP header;
     * if $replace is negative and such a header already exists, then it does not replace.
     *
     * Устанавливает единичный HTTP-заголовок.
     * Если $replace отрицателен и такой заголовок уже существует, то не производит замену.
     */
    public function setHeader(string $name, int|float|string $value, bool $replace = true): void
    {
        $name = $this->normalizeHeaderName($name);
        if ($replace) {
            $this->headers[$name] = [$value];
            return;
        }
        $this->headers[$name][] = $value;
        $this->headers[$name] = \array_unique($this->headers[$name]);
    }

    /**
     * Returns the value of the header by title set using the Response object.
     * If the header was set to header(...), then it can be found using headers_list().
     *
     * Возвращает значение заголовка по названию, установленного с помощью объекта Response.
     * Если заголовок был установлен как header(...), то найти его можно при помощи headers_list().
     */
    public function getHeader(string $name): array
    {
        $name = $this->normalizeHeaderName($name);
        if (\array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
        return [];
    }

    /**
     * Returns the result of checking for the existence of a header set for a Response by its name.
     * If the header was set to header(...), then it can be found using headers_list().
     *
     * Возвращает результат проверки на существование установленного для Response заголовка по его названию.
     * Если заголовок был установлен как header(...), то найти его можно при помощи headers_list().
     */
    public function hasHeader(string $name): bool
    {
        return (bool)$this->getHeader($name);
    }

    /**
     * Adds headers to the set, at the same time replacing duplicates,
     * like [`name` => [`value1`, 'value2']], [`name` => 'value'] or ['name: value'].
     * if $replace is negative and such a header
     *
     * Добавляет заголовки к набору, вместе с этим заменяя дубликаты,
     * вида [`название` => [`значение1`, 'значение2']], [`название` => 'значение'] или ['название: значение'].
     * Если $replace отрицателен и такой заголовок уже существует, то не производит замену.
     */
    public function addHeaders(array $headers, bool $replace = true): void
    {
        foreach($headers as $k => $val) {
            // If a value of the form ['name: value'] is received.
            // Если пришло значение вида ['название: значение'].
            if (\is_numeric($k)) {
                $list = \explode(':', $val);
                $name = \array_shift($list);
                $headers[$name][] = \trim(\implode(':', $list));
                $headers[$name] = \array_unique($headers[$name]);
            }
        }
        foreach ($headers as $key => $value) {
            if (!\is_array($value)) {
                $value = [$value];
            }
            $name = $this->normalizeHeaderName($key);
            if (isset($this->headers[$name])) {
                $this->headers[$name] = \array_unique($replace ? $value : \array_merge($this->headers[$name], $value));
            } else {
                $this->headers[$name] = \array_unique($value);
            }
        }
    }

    /**
     * Get added content.
     *
     * Получение добавленного контента.
     */
    public function getBody(): string
    {
        return \implode($this->body);
    }

    /**
     * Replaces the content completely.
     *
     * Заменяет контент полностью.
     */
    public function setBody($body): void
    {
        $this->body = [(string)$body];
    }

    /**
     * Adds new content to the end of existing content.
     *
     * Добавляет новый контент в конец существующего.
     */
    public function addToBody(mixed $content): void
    {
        $this->body[] = (string)$content;
    }

    /**
     * Removes the last added content by returning it.
     * In this case, the previous one after it becomes the last one.
     *
     * Удаляет последний добавленный контент возвращая его.
     * При этом предыдущий за ним становится последним.
     */
    public function removeFromBody(): mixed
    {
        return \array_pop($this->body);
    }

    /**
     * Clears all previously installed content.
     *
     * Очищает весь установленный ранее контент.
     */
    public function clearBody(): void
    {
        $this->body = [];
    }

    /**
     * Get the version of the HTTP data transfer protocol being used.
     *
     * Получение версии используемого HTTP-протокола передачи данных.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @internal
     *
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * Returns a standardized array of arguments to carry into the Psr7 Request.
     *
     * Возвращает стандартизированный массив аргументов для переноса в Psr7 Request.
     */
    public function getArgs(): array
    {
        return [
            $this->getStatus(),
            $this->getHeaders(),
            $this->getBody(),
            $this->getVersion(),
            $this->getReason(),
        ];
    }

    /**
     * Standardizes the title for the heading.
     *
     * Стандартизирует название для заголовка.
     */
    private function normalizeHeaderName(string $name): string
    {
        $name = \trim($name);
        $name = \str_replace('_', '-', \strtoupper($name));
        $parts = \explode('-', $name);
        foreach ($parts as &$part) {
            if (\in_array($part, self::UPPERCASE)) {
                continue;
            }
            $part = \ucwords(\strtolower($part));
        }

        return \implode('-', $parts);
    }
}
