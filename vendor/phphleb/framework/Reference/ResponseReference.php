<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\HttpMethods\External\Response as SystemResponse;
use Hleb\Main\Insert\ContainerUniqueItem;

#[Accessible] #[AvailableAsParent]
class ResponseReference extends ContainerUniqueItem implements ResponseInterface, Interface\Response, RollbackInterface
{
    /**
     * Need to maintain compatibility with @see self::rollback()
     *
     * Необходимо поддерживать совместимость с @see self::rollback()
     */
    private static SystemResponse|null $response = null;

    /** @inheritDoc */
    #[\Override]
    public function getInstance(): ?SystemResponse
    {
        return self::$response;
    }

    /** @inheritDoc */
    #[\Override]
    public function getStatus(): int
    {
        return self::$response->getStatus();
    }

    /** @inheritDoc */
    #[\Override]
    public function setStatus(int $status, ?string $reason = null): void
    {
        self::$response->setStatus($status, $reason);
    }

    /** @inheritDoc */
    #[\Override]
    public function getHeaders(): array
    {
        return self::$response->getHeaders();
    }

    /** @inheritDoc */
    #[\Override]
    public function replaceHeaders(array $headers): void
    {
        self::$response->replaceHeaders($headers);
    }

    /** @inheritDoc */
    #[\Override]
    public function addHeaders(array $headers, bool $replace = true): void
    {
        self::$response->addHeaders($headers, $replace);
    }

    /** @inheritDoc */
    #[\Override]
    public function setHeader(string $name, float|int|string $value, bool $replace = true): void
    {
        self::$response->setHeader($name, $value, $replace);
    }

    /** @inheritDoc */
    #[\Override]
    public function hasHeader(string $name): bool
    {
        return self::$response->hasHeader($name);
    }

    /** @inheritDoc */
    #[\Override]
    public function getHeader(string $name): array
    {
        return self::$response->getHeader($name);
    }

    /** @inheritDoc */
    #[\Override]
    public function get(): string
    {
        return $this->getBody();
    }

    /** @inheritDoc */
    #[\Override]
    public function set(string|\Stringable $body, ?int $status = null): void
    {
        if ($status !== null) {
            $this->setStatus($status);
        }
        $this->setBody($body);
    }

    /** @inheritDoc */
    #[\Override]
    public function add(mixed $content): void
    {
        $this->addToBody($content);
    }

    /** @inheritDoc */
    #[\Override]
    public function getBody(): string
    {
        return self::$response->getBody();
    }

    /** @inheritDoc */
    #[\Override]
    public function setBody($body): void
    {
        self::$response->setBody($body);
    }

    /** @inheritDoc */
    #[\Override]
    public function addToBody(mixed $content): void
    {
        self::$response->addToBody($content);
    }

    /** @inheritDoc */
    #[\Override]
    public function clearBody(): void
    {
        self::$response->clearBody();
    }

    /** @inheritDoc */
    #[\Override]
    public function removeFromBody(): mixed
    {
        return self::$response->removeFromBody();
    }

    /** @inheritDoc */
    #[\Override]
    public function getVersion(): string
    {
        return self::$response->getVersion();
    }

    /** @inheritDoc */
    #[\Override]
    public function setVersion(string $version): void
    {
        self::$response->setVersion($version);
    }

    /** @inheritDoc */
    #[\Override]
    public function getReason(): ?string
    {
        return self::$response->getReason();
    }


    /** @internal */
    #[\Override]
    public static function init(SystemResponse $response): void
    {
        self::$response = $response;
    }

    /**
     * @inheritDoc
     *
     * @internal
     */
    #[\Override]
    public static function rollback(): void
    {
        self::$response = new SystemResponse();
    }
}
