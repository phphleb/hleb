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
     * (!) Without the following changes, the ability to change the container contents in real time is not available.
     * If you need to add a service after initialization, you can add it using this method.
     * As a result, it will be possible to use this feature via ContainerFactory::setSingleton().
     * You can use lazy loading of objects in two ways:
     * 1) Use lazy load object loading added in PHP 8.4,
     *  so that objects are initialized only upon request.
     * ```php
     *    ContainerFactory::setSingleton(
     *        CustomServiceInterface::class,
     *        ContainerFactory::getLazyObject(CustomService::class)
     *    );
     *  ```
     * 2) Use an anonymous function for $value that initializes the desired object.
     *
     * For all this, you will need to supplement the ContainerFactory class,
     * and if you want the addition to be via $this->container in the application,
     * then also the BaseContainer class:
     *
     * Добавление/замена сервиса контейнера в реальном времени.
     * (!) Без указанных далее изменений возможность изменять содержимое контейнера в реальном времени недоступна.
     * При необходимости добавить сервис после инициализации можно добавить его при помощи этого метода.
     * В результате можно будет через ContainerFactory::setSingleton() использовать эту возможность.
     * Можно использовать "ленивую" загрузку объектов двумя способами:
     * 1) Используйте lazy load загрузку объектов, добавленную в PHP 8.4,
     *  чтобы объекты инициализировались только по запросу.
     *  ```php
     *   ContainerFactory::setSingleton(
     *         CustomServiceInterface::class,
     *         ContainerFactory::getLazyObject(CustomService::class)
     *     );
     * ```
     * 2) Используйте анонимную функцию для $value инициализирующую нужный объект.
     *
     * Для всего этого нужно будет дополнить класс ContainerFactory, а если нужно,
     * чтобы добавление было и через $this->container в приложении, то и класс BaseContainer:
     *
     * ```php
     * // App\Bootstrap\ContainerFactory
     *
     * public static function getSingleton(string $id): mixed
     * {
     *   ...
     *
     *   if (self::$singletons[$id] instanceof \Closure) {
     *       self::$singletons[$id] = self::$singletons[$id]();
     *   }
     *   return self::$singletons[$id];
     * }
     *
     * #[\Override]
     * public static function setSingleton(string $id, ?object $value): void
     * {
     *    parent::setSingleton($id, $value);
     * }
     * ```
     * ```php
     *  // App\Bootstrap\BaseContainer
     *
     *  public function setSingleton(string $id, ?object $value): void
     *  {
     *     ContainerFactory::setSingleton($id, $value);
     *  }
     * ```
     * ```php
     *   // App\Bootstrap\ContainerInterface
     *
     *   public function setSingleton(string $id, ?object $value): void
     *  ```
     */
    protected static function setSingleton(string $id, object|null $value): void
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
     * Getting a lazy object from a class name and constructor parameters for assignment in a container.
     * It is necessary to take into account the nuances of lazy objects, including that if static methods
     * such as rollback() are called on the object, the fields involved must have default values.
     *
     * Получение "ленивого" объекта из названия класса и параметров конструктора для присвоения в контейнере.
     * Необходимо учитывать нюансы ленивых объектов, в том числе, если у объекта будут вызваны статические методы,
     * например rollback(), задействованные поля должны иметь значения по умолчанию.
     *
     * ```php
     *    CustomServiceInterface => self::getLazyObject(CustomService::class)
     *  ```
     */
    final public static function getLazyObject(string $class, array $params = []): object
    {
        return CoreContainer::getLazyObject($class, $params);
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
