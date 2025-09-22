<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\RouterInterface;

#[Accessible]
final class Router extends BaseSingleton
{
    private static RouterInterface|null $replace = null;

    /**
     * Returns the name of the current route, or null if none has been assigned.
     *
     * Возвращает название текущего маршрута или null если оно не назначено.
     */
    public static function name(): ?string
    {
        if (self::$replace) {
            return self::$replace->name();
        }

        return BaseContainer::instance()->get(RouterInterface::class)->name();
    }

    /**
     * Returns the standardized address from the route name in the URL.
     * For example, for a route: Route::get('/test/{var1}/{var2?}', ...)->name('example');
     * you need to pass the following data to the method Route::url('example', ['var1' => 3000, 'var2' => 'pro'], true);
     * to get this string value '/test/3000/pro'. This will set the trailing slash
     * depending on project settings.
     * Returns error if the parameters did not match the route (there is no such name, the route does not support
     * the specified method, the replacement parts of the route did not fit) or an error in obtaining routes.
     *
     * Возвращает стандартизированный адрес из названия маршрута в URL.
     * Например, для маршрута Route::get('/test/{var1}/{var2?}', ...)->name('example');
     * нужно передать следующие данные в метод Route::url('example', ['var1' => 3000, 'var2' => 'pro'], true);
     * чтобы получить такое строковое значение '/test/3000/pro'. При этом конечный слеш будет установлен
     * в зависимости от настроек проекта.
     * Возвращает ошибку если параметры не совпали с маршрутом (нет такого названия, маршрут не поддерживает
     * указанный метод, не подошли заменяемые части маршрута) или ошибка получения маршрутов.
     *
     * @param string $routeName - route name. The name must be used in routes.
     *                          - название маршрута. Название должно быть используемым в маршрутах.
     *
     * @param array $replacements - an array of substitutions for substitution in a dynamic route.
     *                            - массив замен для подстановки в динамический маршрут.
     *
     * @param bool $endPart - whether it is necessary to leave the final part in the route, where it may be optional.
     *                      - нужно ли оставлять конечную часть в маршруте, где она может быть необязательна.
     *
     * @param string $method - HTTP method for which you want to generate a URL.
     *                         Such a method must be supported by the route.
     *                       - HTTP-метод для которого нужно сгенерировать URL.
     *                         Такой метод должен поддерживаться маршрутом.
     *
     * @return string
     */
    public static function url(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): string
    {
        if (self::$replace) {
            return self::$replace->url($routeName, $replacements, $endPart, $method);
        }

        return BaseContainer::instance()->get(RouterInterface::class)->url($routeName, $replacements, $endPart, $method);
    }

    /**
     * Returns the full URL given the route name and current domain. For example `https://site.com/test/3000/pro`.
     * In this case, the trailing slash will be set depending on the project settings.
     * Since only the current domain is assigned, use concatenation with Route::url() for another domain.
     * Returns error if the parameters did not match the route (there is no such name, the route does not support
     * the specified method, the replacement parts of the route did not fit) or an error in obtaining routes.
     *
     * Возвращает полный URL-адрес по имени маршрута и текущего домена. Например `https://site.com/test/3000/pro`.
     * При этом конечный слеш будет установлен в зависимости от настроек проекта.
     * Так как домен присваивается только текущий, для другого домена используйте конкатенацию с Route::url().
     * Возвращает ошибку если параметры не совпали с маршрутом (нет такого названия, маршрут не поддерживает
     * указанный метод, не подошли заменяемые части маршрута) или ошибка получения маршрутов.
     *
     * @see self::url() - more about method arguments.
     *                  - подробнее об аргументах метода.
     */
    public static function address(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): false|string
    {
        if (self::$replace) {
            return self::$replace->address($routeName, $replacements, $endPart, $method);
        }

        return BaseContainer::instance()->get(RouterInterface::class)->address($routeName, $replacements, $endPart, $method);
    }

    /**
     * Returns the data specified in the controller route (middleware).
     *
     * Возвращает данные, указанные в маршруте контроллера (middleware).
     */
    public static function data(): array
    {
        if (self::$replace) {
            return self::$replace->data();
        }

        return BaseContainer::instance()->get(RouterInterface::class)->data();
    }

    /**
     * @internal
     *
     * @see RouterForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(RouterInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
