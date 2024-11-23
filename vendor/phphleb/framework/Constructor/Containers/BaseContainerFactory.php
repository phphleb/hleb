<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Containers;

use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Reference\Interface\{
    Arr,
    Cache,
    Command,
    Converter,
    Cookie,
    Csrf,
    Db,
    Debug,
    DI,
    Dto,
    Log,
    Path,
    Redirect,
    Request,
    Response,
    Router,
    Session,
    Setting,
    System,
    Template,
};
use Hleb\Reference\{ArrInterface,
    CacheInterface,
    CommandInterface,
    ConverterInterface,
    CookieInterface,
    CsrfInterface,
    DbInterface,
    DebugInterface,
    DiInterface,
    DtoInterface,
    LogInterface,
    PathInterface,
    RedirectInterface,
    RequestInterface,
    ResponseInterface,
    RouterInterface,
    SessionInterface,
    SettingInterface,
    SystemInterface,
    TemplateInterface
};

/**
 * A control class for getting or creating a singleton from a container.
 *
 * Управляющий класс для получения или создания singleton из контейнера.
 */
abstract class BaseContainerFactory extends BaseAsyncSingleton
{
    /**
     * Aliases for the single identifier in the value by which the singleton is created.
     *
     * Псевдонимы для единственного идентификатора в значении, по которому создается singleton.
     */
    public const SERVICE_MAP = [
        Arr::class => ArrInterface::class,
        Csrf::class => CsrfInterface::class,
        Converter::class => ConverterInterface::class,
        Request::class => RequestInterface::class,
        Response::class => ResponseInterface::class,
        Db::class => DbInterface::class,
        Cookie::class => CookieInterface::class,
        Session::class => SessionInterface::class,
        Setting::class => SettingInterface::class,
        Router::class => RouterInterface::class,
        Log::class => LogInterface::class,
        Path::class => PathInterface::class,
        Debug::class => DebugInterface::class,
        Dto::class => DtoInterface::class,
        Cache::class => CacheInterface::class,
        Redirect::class => RedirectInterface::class,
        System::class => SystemInterface::class,
        Template::class => TemplateInterface::class,
        Command::class => CommandInterface::class,
        DI::class => DiInterface::class,
    ];

    protected static array $singletons = [];

    /**
     * Registered keys for custom services.
     *
     * Зарегистрированные ключи кастомных сервисов.
     */
    protected static ?array $customServiceKeys = null;

    /**
     * Returns the keys of custom services registered manually.
     *
     * Возвращает ключи кастомных сервисов, зарегистрированных вручную.
     */
    final public static function getCustomKeys(): ?array
    {
        return self::$customServiceKeys;
    }

    /**
     * Add/replace container service in real time.
     * If you need to add a service after initialization, you can add it using this method.
     * As a result, it will be possible to use this feature via ContainerFactory::setSingleton().
     * You can use lazy loading of objects in two ways:
     * 1) Use lazy load object loading added in PHP 8.4,
     *  so that objects are initialized only upon request.
     * 2) Use Callable type for $singleton as an initialization function.
     * To do this you will need to change the ContainerFactory class:
     *
     * Добавление/замена сервиса контейнера в реальном времени.
     * При необходимости добавить сервис после инициализации можно добавить его при помощи этого метода.
     * В результате можно будет через ContainerFactory::setSingleton() использовать эту возможность.
     * Можно использовать "ленивую" загрузку объектов двумя способами:
     * 1) Используйте lazy load загрузку объектов, добавленную в PHP 8.4,
     *  чтобы объекты инициализировались только по запросу.
     * 2) Используйте тип Callable для $singleton в виде инициализирующей функции.
     * Для этого нужно будет изменить класс ContainerFactory:
     *
     * ```php
     * public static function getSingleton(string $id): mixed
     * {
     *   ...
     *
     *   if (is_callable(self::$singletons[$id])) {
     *       self::$singletons[$id] = self::$singletons[$id]();
     *   }
     *   return self::$singletons[$id];
     * }
     * #[\Override]
     * public static function setSingleton(string $id, object|callable|null $value): void
     * {
     *   parent::setSingleton($id, $value);
     * }
     * ```
     */
    protected static function setSingleton(string $id, object|callable|null $value): void
    {
        self::$singletons[$id] = $value;
        if (\is_null(self::$customServiceKeys)) {
            return;
        }
        if (\is_null($value)) {
            unset(self::$customServiceKeys[$id]);
        } else {
            self::$customServiceKeys[] = $id;
        }
    }

    /**
     * To simplify the search for services through Container::has(),
     * all custom services of the singleton type must be registered.
     * If none are registered, by default the framework tries to determine
     *  the existence of the service along with its creation. To avoid this,
     * add ALL custom services using this method.
     * The ContainerFactory::rollback() method must implement a reset
     * (self::$customServices = null) for non-persistent services.
     * For example:
     *
     * Для упрощения поиска сервисов через Container::has(),
     * все кастомные сервисы типа singleton должны быть зарегистрированы.
     * Если не зарегистрирован ни один, по умолчанию фреймворк пытается определить
     * наличие сервиса вместе с его созданием. Чтобы этого избежать,
     * добавьте ВСЕ кастомные сервисы при помощи этого метода.
     * В методе ContainerFactory::rollback() должно быть реализован сброс значения
     * (self::$customServices = null) для непостоянных сервисов.
     * Например:
     *
     * ```php
     *   self::has($id) or self::$singletons[$id] = match ($id) {
     *      RequestIdInterface::class => new RequestIdService(),
     *      default => null
     *   };
     *   self::register(RequestIdInterface::class);
     *
     *   return self::$singletons[$id];
     * ```
     *
     */
    final protected static function register(string $id): void
    {
        if (\is_null(self::$customServiceKeys)) {
            self::$customServiceKeys = [];
        } else if (isset(self::SERVICE_MAP[$id]) || \in_array($id, self::SERVICE_MAP)) {
            return;
        }
        self::$customServiceKeys[$id] ?? self::$customServiceKeys[] = $id;
    }

    /**
     * Returns the result of checking for the existence of a singleton by identifier.
     *
     * Возвращает результат проверки на существование singleton по идентификатору.
     */
    final protected static function has(string &$id): bool
    {
        $id = self::SERVICE_MAP[$id] ?? $id;

        return \array_key_exists($id, self::$singletons);
    }

}
