<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Containers;

use Hleb\Main\Insert\ExternalSingleton;
use Hleb\Reference\{
    ArrInterface,
    ArrReference,
    CacheInterface,
    CacheReference,
    CommandInterface,
    CommandReference,
    ConverterInterface,
    ConverterReference,
    DiInterface,
    DiReference,
    CsrfInterface,
    CsrfReference,
    DtoInterface,
    DtoReference,
    CookieInterface,
    CookieReference,
    DbReference,
    DbInterface,
    DebugInterface,
    DebugReference,
    LogInterface,
    LogReference,
    PathInterface,
    PathReference,
    RedirectInterface,
    RedirectReference,
    RequestInterface,
    RequestReference,
    ResponseInterface,
    ResponseReference,
    RouterInterface,
    RouterReference,
    SessionInterface,
    SessionReference,
    SettingInterface,
    SettingReference,
    SystemInterface,
    SystemReference,
    TemplateInterface,
    TemplateReference
};
use Throwable;

/**
 * Base class with framework containers.
 *
 * Базовый класс с контейнерами фреймворка.
 */
abstract class CoreContainer extends ExternalSingleton implements CoreContainerInterface
{
    private static array $containers = [];


    /**
     * Registered keys for custom services.
     *
     * Зарегистрированные ключи кастомных сервисов.
     */
    protected static ?array $customServiceKeys = null;

    /**
     * @inheritDoc
     *
     * @template TContainerInterface
     * @param class-string<TContainerInterface> $id
     * @return TContainerInterface|mixed
     */
    #[\Override]
    public function get(string $id): mixed
    {
        return $this->getOriginSingleton($id);
    }

    /** @inheritDoc */
    #[\Override]
    final public function debug(): DebugInterface
    {
        return $this->getOriginSingleton(DebugInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function log(): LogInterface
    {
        return $this->getOriginSingleton(LogInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function arr(): ArrInterface
    {
        return $this->getOriginSingleton(ArrInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function path(): PathInterface
    {
        return $this->getOriginSingleton(PathInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function redirect(): RedirectInterface
    {
        return $this->getOriginSingleton(RedirectInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function csrf(): CsrfInterface
    {
        return $this->getOriginSingleton(CsrfInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function cookies(): CookieInterface
    {
        return $this->getOriginSingleton(CookieInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function session(): SessionInterface
    {
        return $this->getOriginSingleton(SessionInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function request(): RequestInterface
    {
        return $this->getOriginSingleton(RequestInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function response(): ResponseInterface
    {
        return $this->getOriginSingleton(ResponseInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function settings(): SettingInterface
    {
        return $this->getOriginSingleton(SettingInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function route(): RouterInterface
    {
        return $this->getOriginSingleton(RouterInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function dto(): DtoInterface
    {
        return $this->getOriginSingleton(DtoInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function db(): DbInterface
    {
        return $this->get(DbInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function system(): SystemInterface
    {
        return $this->get(SystemInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function converter(): ConverterInterface
    {
        return $this->get(ConverterInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function cache(): CacheInterface
    {
        return $this->get(CacheInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function template(): TemplateInterface
    {
        return $this->get(TemplateInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    final public function command(): CommandInterface
    {
        return $this->get(CommandInterface::class);
    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $id): bool
    {
        $list = BaseContainerFactory::SERVICE_MAP;

        $id = $list[$id] ?? $id;

        if (\in_array($id, $list)) {
            return true;
        }

        $singletonKeys = BaseContainerFactory::getCustomKeys();
        if (!\is_null($singletonKeys) && \in_array($id, $singletonKeys)) {
            return true;
        }
        if (!\is_null(self::$customServiceKeys) && \in_array($id, self::$customServiceKeys)) {
            return true;
        }
        try {
            return static::get($id) !== null;
        } catch (Throwable) {
        }
        return true;
    }


    /**
     * To simplify the search for services through Container::has(),
     * all custom services must be registered.
     * For singleton services, there is a similar method in ContainerFactory.
     * If none are registered, by default the framework tries to determine
     *  the existence of the service along with its creation. To avoid this,
     * add ALL custom services using this method.
     * For example:
     *
     * Для упрощения поиска сервисов через Container::has(),
     * все кастомные сервисы должны быть зарегистрированы.
     * Для сервисов типа singleton существует аналогичный метод в ContainerFactory.
     * Если не зарегистрирован ни один, по умолчанию фреймворк пытается определить
     * наличие сервиса вместе с его созданием. Чтобы этого избежать,
     * добавьте ВСЕ кастомные сервисы при помощи этого метода.
     * Например:
     *
     * ```php
     *   self::register(CustomService::class);
     * ```
     *
     */
    final protected static function register(string $id): void
    {
            self::$customServiceKeys[$id] ?? self::$customServiceKeys[] = $id;
    }

    private function getOriginSingleton(string $id): mixed
    {
        $list = BaseContainerFactory::SERVICE_MAP;

        if (isset($list[$id])) {
            $id = $list[$id];
        }
        if (!\in_array($id, $list)) {
            return null;
        }
        return $this->getObject($id);
    }

    private function getObject(string $class): object|null
    {
        if (\array_key_exists($class, self::$containers)) {
            return self::$containers[$class];
        }
        $container = match ($class) {
            ArrInterface::class => new ArrReference(),
            CsrfInterface::class => new CsrfReference(),
            ConverterInterface::class => new ConverterReference(),
            RequestInterface::class => new RequestReference(),
            ResponseInterface::class => new ResponseReference(),
            DbInterface::class => new DbReference(),
            CookieInterface::class => new CookieReference(),
            SessionInterface::class => new SessionReference(),
            SettingInterface::class => new SettingReference(),
            RouterInterface::class => new RouterReference(),
            LogInterface::class => new LogReference(),
            PathInterface::class => new PathReference(),
            DebugInterface::class => new DebugReference(),
            DtoInterface::class => new DtoReference(),
            CacheInterface::class => new CacheReference(),
            RedirectInterface::class => new RedirectReference(),
            SystemInterface::class => new SystemReference(),
            TemplateInterface::class => new TemplateReference(),
            CommandInterface::class => new CommandReference(),
            DiInterface::class => new DiReference(),
            default => null
        };

        return self::$containers[$class] = $container;
    }

    /**
     * @internal
     */
    public static function rollback(): void
    {
        self::$containers = [];
        self::$customServiceKeys = null;
    }
}
