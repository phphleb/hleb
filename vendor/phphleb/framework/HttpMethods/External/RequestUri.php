<?php

declare(strict_types=1);

namespace Hleb\HttpMethods\External;

use Hleb\Constructor\Attributes\Autowiring\NoAutowire;

// Prior to php 8.2, the `readonly` status may cause an error.
// До версии php 8.2 статус `readonly` может вызывать ошибку.
#[NoAutowire]
final readonly class RequestUri
{
    public function __construct(
        private string $host,
        private string $path,
        private string $query,
        private int|null $port,
        private string $scheme,
        private string $ip,
    )
    {
    }

    /**
     * Returns the domain of the current request.
     * Maybe along with the port. For example `example.com` or `example.com:8080`
     *
     * Возвращает домен текущего запроса.
     * Может быть вместе с портом. Например, `example.com или` `example.com:8080`
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Parts of a resource name (URN), such as `/en/example/page` or `/en/example/page/`.
     *
     * Части имени ресурса (URN), например `/ru/example/page` или `/ru/example/page/`.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Query parameters, for example `?param1=value1&param2=value2`.
     *
     * Параметры запроса, например `?param1=value1&param2=value2`.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Port from $_SERVER['SERVER_PORT'].
     *
     * Порт из $_SERVER['SERVER_PORT'].
     */
    public function getPort(): int|null
    {
        return $this->port ?: null;
    }

    /**
     * The HTTP scheme of the request, `http` or `https`.
     *
     * HTTP-схема запроса, `http` или `https`.
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Returns the value from $_SERVER['REMOTE_ADDR'].
     *
     * Возвращает значение из $_SERVER['REMOTE_ADDR'].
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    public function __toString(): string
    {
        return $this->getScheme() . '://' . $this->getHost() . $this->getPath() . $this->getQuery();
    }
}
