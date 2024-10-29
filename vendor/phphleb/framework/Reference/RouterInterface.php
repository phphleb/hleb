<?php

namespace Hleb\Reference;

/**
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface RouterInterface
{
    /**
     * Returns the current route name, if any.
     *
     * Возвращает текущее название маршрута, если оно присвоено.
     */
    public function name(): ?string;

    /**
     * Returns the standardized address from the route name in the URL.
     * For example, for a route Route::get('/test/{var1}/{var2?}', ...)->name('example');
     * you need to pass the following data to the method ->url('example', ['var1' => 3000, 'var2' => 'pro'], true);
     * to get this string value '/test/3000/pro'. This will set the trailing slash
     * depending on project settings.
     * Returns false if the parameters did not match the route (there is no such name, the route does not support
     * the specified method, the replacement parts of the route did not fit) or an error in obtaining routes.
     *
     * Возвращает стандартизированный адрес из названия маршрута в URL.
     * Например, для маршруте Route::get('/test/{var1}/{var2?}', ...)->name('example');
     * нужно передать следующие данные  ->url('example', ['var1' => 3000, 'var2' => 'pro'], true);
     * чтобы получить такое строковое значение '/test/3000/pro'. При этом конечный слеш будет установлен
     * в зависимости от настроек проекта.
     * Возвращает false если параметры не совпали с маршрутом (нет такого названия, маршрут не поддерживает
     * указанный метод, не подошли заменяемые части маршрута) или ошибка получения маршрутов.
     *
     * @param string $routeName - route name. The name must be used in routes.
     *                            - название маршрута. Название должно быть используемым в маршрутах.
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
     * @return string|false
     */
    public function url(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): false|string;

    /**
     * Returns the full URL given the route name and current domain. For example `https://site.com/test/3000/pro`.
     * In this case, the trailing slash will be set depending on the project settings.
     * Since only the current domain is assigned, use concatenation with ->url() for another domain.
     * Returns false if the parameters did not match the route (there is no such name, the route does not support
     * the specified method, the replacement parts of the route did not fit) or an error in obtaining routes.
     *
     * Возвращает полный URL-адрес по имени маршрута и текущего домена. Например `https://site.com/test/3000/pro`.
     * При этом конечный слеш будет установлен в зависимости от настроек проекта.
     * Так как домен присваивается только текущий, для другого домена используйте конкатенацию с ->url().
     * Возвращает false если параметры не совпали с маршрутом (нет такого названия, маршрут не поддерживает
     * указанный метод, не подошли заменяемые части маршрута) или ошибка получения маршрутов.
     *
     * @see self::url() - more about method arguments.
     *                  - подробнее об аргументах метода.
     */
    public function address(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): false|string;

    /**
     * Returns the data specified in the controller route (middleware).
     *
     * Возвращает данные, указанные в маршруте контроллера (middleware).
     */
    public function data(): array;
}
