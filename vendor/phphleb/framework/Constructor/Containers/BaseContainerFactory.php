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
