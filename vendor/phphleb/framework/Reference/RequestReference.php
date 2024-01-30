<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\HttpMethods\External\RequestUri;
use Hleb\HttpMethods\Specifier\DataType;
use Hleb\Main\Insert\ContainerUniqueItem;

#[Accessible] #[AvailableAsParent]
class RequestReference extends ContainerUniqueItem implements RequestInterface, Interface\Request
{
    private static array $cachedParams = [];

    /** @inheritDoc */
    #[\Override]
    public function getMethod(): string
    {
        return DynamicParams::getRequest()->getMethod();
    }

    /** @inheritDoc */
    #[\Override]
    public function isMethod(string $name): bool
    {
        return $this->getMethod() === \strtoupper($name);
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string|int $name): ?DataType
    {
        if (\array_key_exists($name, self::$cachedParams['get'] ?? [])) {
            return self::$cachedParams['get'][$name];
        }

        return self::$cachedParams['get'][$name] = new DataType($_GET[$name] ?? null);
    }

    /** @inheritDoc */
    #[\Override]
    public function post(string|int $name): DataType
    {
        if (\array_key_exists($name, self::$cachedParams['post'] ?? [])) {
            return self::$cachedParams['post'][$name];
        }

        return self::$cachedParams['post'][$name] = new DataType($_POST[$name] ?? null);
    }

    /** @inheritDoc */
    #[\Override]
    public function param(string $name): DataType
    {
        if (\array_key_exists($name, self::$cachedParams['params'] ?? [])) {
            return self::$cachedParams['params'][$name];
        }
        $data = DynamicParams::getDynamicUriParams();
        return self::$cachedParams['params'][$name] = (\array_key_exists($name, $data) ? new DataType($data[$name]) : new DataType(null));
    }

    /** @inheritDoc */
    #[\Override]
    public function data(): array
    {
        $data = DynamicParams::getDynamicUriParams();
        if (!isset(self::$cachedParams['params']) || \count(self::$cachedParams['params']) !== \count($data)) {
            self::$cachedParams['params'] = [];
            foreach ($data as $name => $value) {
                self::$cachedParams['params'][$name] = new DataType($value);
            }
        }
        return self::$cachedParams['params'];
    }

    /** @inheritDoc */
    #[\Override]
    public function input(): array
    {
        if (\array_key_exists('input', self::$cachedParams)) {
            return self::$cachedParams['input'];
        }

        $data = \hl_clear_tags((array)self::getParsedBody());

        return self::$cachedParams['input'] = $data;
    }

    /** @inheritDoc */
    #[\Override]
    public function rawData(): array
    {
        return DynamicParams::getDynamicUriParams();
    }

    /** @inheritDoc */
    #[\Override]
    public function getParsedBody(): null|array
    {
        return DynamicParams::getRequest()->getParsedBody();
    }

    /** @inheritDoc */
    #[\Override]
    public function getUri(): RequestUri
    {
        return DynamicParams::getRequest()->getUri();
    }

    /** @inheritDoc */
    #[\Override]
    public function getRawBody(): string
    {
        return DynamicParams::getRequest()->getRawBody();
    }

    /** @inheritDoc */
    #[\Override]
    public function isAjax(): bool
    {
        if (\strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
            return true;
        }
        if ($header = $this->getHeader('X-Requested-With')) {
            return \strtolower((string)\end($header)) === 'xmlhttprequest';
        }
        return false;
    }

    /** @inheritDoc */
    #[\Override]
    public function getFiles(string|int|null $name = null): null|array|object
    {
        return $name === null ? ($_FILES ?? []) : ($_FILES[$name] ?? null);
    }

    /** @inheritDoc */
    #[\Override]
    public function isHttpSecure(): bool
    {
        return DynamicParams::getRequest()->getUri()->getScheme() === 'https';
    }

    /** @inheritDoc */
    #[\Override]
    public function getHost(): string
    {
        return DynamicParams::getRequest()->getUri()->getHost();
    }

    /** @inheritDoc */
    #[\Override]
    public function getHostName(): string
    {
        $host = $this->getHost();

        return \strstr($host, ':', true) ?: $host;
    }

    /** @inheritDoc */
    #[\Override]
    public function getAddress(): string
    {
        $uri = DynamicParams::getRequest()->getUri();

        return $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath();
    }

    /** @inheritDoc */
    #[\Override]
    public function getHttpScheme(): string
    {
        return 'http' . ($this->isHttpSecure() ? 's' : '') . '://';
    }

    /** @inheritDoc */
    #[\Override]
    public function getSchemeAndHost(): string
    {
        return self::getHttpScheme() . $this->getHost();
    }

    /** @inheritDoc */
    #[\Override]
    public function getProtocolVersion(): string
    {
        return DynamicParams::getRequest()->getProtocolVersion();
    }

    /** @inheritDoc */
    #[\Override]
    public function getHeaders(): array
    {
        return DynamicParams::getRequest()->getHeaders();
    }

    /** @inheritDoc */
    #[\Override]
    public function hasHeader($name): bool
    {
        return DynamicParams::getRequest()->hasHeader($name);
    }

    /** @inheritDoc */
    #[\Override]
    public function getHeader($name): array
    {
        return DynamicParams::getRequest()->getHeader($name);
    }

    /** @inheritDoc */
    #[\Override]
    public function getSingleHeader($name): DataType
    {
        $header = $this->getHeader($name);

        return new DataType($header ? \current($header) : null);
    }

    /** @inheritDoc */
    #[\Override]
    public function getHeaderLine($name): string
    {
        return DynamicParams::getRequest()->getHeaderLine($name);
    }

    /** @inheritDoc */
    #[\Override]
    public static function server($name): mixed
    {
        return $_SERVER[$name] ?? null;
    }

    /** @inheritDoc */
    #[\Override]
    public function getStreamBody(): ?object
    {
        return DynamicParams::getRequest()->getStreamBody();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function rollback(): void
    {
        self::$cachedParams = [];
    }
}
