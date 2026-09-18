<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\HttpMethods\External\RequestUri;
use Hleb\HttpMethods\Specifier\DataType;
use Hleb\Main\Insert\ContainerUniqueItem;

#[Accessible] #[AvailableAsParent]
class RequestReference extends ContainerUniqueItem implements RequestInterface, Interface\Request, RollbackInterface
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
    public function allGet(bool $cleared = true): array
    {
        return $cleared ? \hl_clear_tags($_GET ?? []) : $_GET ?? [];
    }

    /** @inheritDoc */
    #[\Override]
    public function allPost(bool $cleared = true): array
    {
        return $cleared ? \hl_clear_tags($_POST ?? []) : $_POST ?? [];
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
        return DynamicParams::getRequest()->getParsedBody(cleared: true) ?? [];
    }

    /** @inheritDoc */
    #[\Override]
    public function rawData(): array
    {
        return DynamicParams::getDynamicUriParams();
    }

    /** @inheritDoc */
    #[\Override]
    public function getParsedBody(): array
    {
        return DynamicParams::getRequest()->getParsedBody(cleared: false) ?? [];
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
        $xrw = $this->headerValue('X-Requested-With');
        if ($xrw !== null && \in_array(\strtolower($xrw), ['xmlhttprequest', 'fetch'], true)) {
            return true;
        }

        return $this->isPjax();
    }

    /** @inheritDoc */
    #[\Override]
    public function isPjax(): bool
    {
        $pjax = $this->headerValue('X-PJAX');

        return $pjax !== null && $pjax !== '';
    }

    /** @inheritDoc */
    #[\Override]
    public function acceptsJson(): bool
    {
        $accept = $this->headerValue('Accept');

        return $accept !== null && \str_contains($accept, 'application/json');
    }

    /** @inheritDoc */
    #[\Override]
    public function getContentTypeFormat(): ?string
    {
        $contentType = $this->headerValue('Content-Type');
        if ($contentType === null) {
            return null;
        }

        $mime = \strtolower(\trim(\explode(';', $contentType, 2)[0]));

        $map = [
            'application/json'                  => 'json',
            'application/x-json'                => 'json',
            'text/json'                         => 'json',
            'application/xml'                   => 'xml',
            'text/xml'                          => 'xml',
            'application/xhtml+xml'             => 'html',
            'text/html'                         => 'html',
            'application/x-www-form-urlencoded' => 'form',
            'multipart/form-data'               => 'form',
            'text/plain'                        => 'txt',
            'application/javascript'            => 'js',
            'application/x-javascript'          => 'js',
            'text/javascript'                   => 'js',
            'text/css'                          => 'css',
        ];

        return $map[$mime] ?? null;
    }

    public function isSoap(): bool
    {
        $contentType = $this->headerValue('Content-Type');
        if ($contentType !== null
            && \str_contains(\strtolower($contentType), 'application/soap+xml')
        ) {
            return true;
        }

        $soapAction = $this->headerValue('SOAPAction');

        return $soapAction !== null && $soapAction !== '';
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

        $path = $uri->getPath();

        return $uri->getScheme() . '://' . $uri->getHost() . ($path === '/' ? '' : $path);
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
    public function server($name): mixed
    {
        return $_SERVER[$name] ?? null;
    }

    /** @inheritDoc */
    #[\Override]
    public function isCurrent(string $uri): bool
    {
        return \trim($uri, '/') === \trim($this->getUri()->getPath(), '/');
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

    private function headerValue(string $name): ?string
    {
        $key = 'HTTP_' . \strtoupper(\str_replace('-', '_', $name));
        if (isset($_SERVER[$key])) {
            return (string)$_SERVER[$key];
        }

        if ($header = $this->getHeader($name)) {
            return (string)\end($header);
        }

        return null;
    }
}
