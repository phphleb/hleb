<?php

declare(strict_types=1);

/*
 * Custom route assignment through methods.
 *
 * Пользовательское назначение роутов через методы.
 */

namespace Hleb\Constructor\Routes;

use Hleb\Scheme\Home\Constructor\Routes\{
    StandardRoute
};
use Hleb\Constructor\Routes\Methods\{
    RouteMethodGet,
    RouteMethodType,
    RouteMethodName,
    RouteMethodController,
    RouteMethodGetGroup,
    RouteMethodEndGroup,
    RouteMethodBefore,
    RouteMethodAfter,
    RouteMethodWhere,
    RouteMethodGetType,
    RouteMethodEndType,
    RouteMethodPrefix,
    RouteMethodProtect,
    RouteMethodGetProtect,
    RouteMethodEndProtect,
    RouteMethodRenderMap,
    RouteMethodDomain,
    RouteMethodAdminPanController,
    RouteMethodModule
};

final class Route extends MainRoute implements StandardRoute {

    /**
     * The main way to assign URL-route for the content to be displayed.
     * Route::get('/address/', 'Content'); - displaying a text string.
     * Route::get('/', view('index')); - displaying a template.
     * Route::get('/default/')->controller('DefaultController'); - addressing to the controller. The second argument is missing.
     * The $params argument is necessary for optional transfer of variables and their values to the template.
     * The parameters form an array, the keys of which are the names of the variables.
     *
     * Основной метод для назначения URL-маршрута отображаемому контенту.
     * Route::get('/address/', 'Content'); - отображение текстовой строки.
     * Route::get('/', view('index')); - отображение шаблона.
     * Route::get('/handling/default/')->controller('DefaultController'); - обращение к контроллеру. Второй аргумент отсутствует.
     * Аргумент $params необходим для необязательной передачи переменных и их значений в шаблон.
     * Параметры представляют из себя массив, в котором ключи - названия переменных.
     *
     * @param string $route
     * @param string|array $params
     * @return static|null
     */
    public static function get(string $route, $params = []) {
        return self::add(new RouteMethodGet(self::instance(), $route, $params));
    }

    /**
     * Group beginning designation. It forms a named group by specifying its name.
     *
     * Обозначение начала группы. При указании названия группы образует именованную группу.
     *
     * @param string|null $name
     * @return static|null
     */
    public static function getGroup($name = null) {
        return self::add(new RouteMethodGetGroup(self::instance(), $name));
    }

    /**
     * Group closing designation. It closes the related named group by specifying its name.
     *
     * Обозначение закрытия группы. При указании названия закрывает соответствующую именованную группу.
     *
     * @param string|null $name
     * @return static|null
     */
    public static function endGroup($name = null) {
        return self::add(new RouteMethodEndGroup(self::instance(), $name));
    }

    /**
     * Execution of the pre-controller.
     * The class method can be specfied via '@', for example, 'DefaultMiddlewareBefore@method'. If it is not specified, 'index' will be used.
     * The parameters will be transferred to the arguments of the controller by mapping the key-value of the array.
     *
     * Выполнение предварительного контроллера.
     * Указать метод класса можно через '@', например, 'DefaultMiddlewareBefore@method'. Если не указан, будет использован 'index'.
     * Параметры будут преданы в аргументы контроллера по сопоставлению ключ-значение массива.
     *
     * @param string $class_name
     * @param array $params
     * @return static|null
     */
    public static function before(string $class_name, array $params = []) {
        return self::add(new RouteMethodBefore(self::instance(), $class_name, $params));
    }

    /**
     * Execution of the subsequent controller.
     * The class method can be specfied via '@', for example, 'DefaultMiddlewareAfter@method'. If it is not specified, 'index' will be used.
     * The parameters will be transferred to the arguments of the controller by mapping the key-value of the array.
     *
     * Выполнение последущего контроллера.
     * Указать метод класса можно через '@', например, 'DefaultMiddlewareAfter@method'. Если не указан, будет использован 'index'.
     * Параметры будут преданы в аргументы контроллера по сопоставлению ключ-значение массива.
     *
     * @param string $class_name
     * @param array $params
     * @return static|null
     */
    public static function after(string $class_name, array $params = []) {
        return self::add(new RouteMethodAfter(self::instance(), $class_name, $params));
    }

    /**
     * When where( ... ) is added after of the method get( ... ), it sets a check of dynamic parts of the route using regular expressions.
     * Route::get('/ru/{version}/{page}/', 'Content')->where(['version' => '[a-z0-9]+', 'page' => '[a-z]+']);
     *
     * При добавлении после метода get( ... ) устанавливает проверку динамических частей маршрута с помощью регулярных выражений.
     * Route::get('/ru/{version}/{page}/', 'Content')->where(['version' => '[a-z0-9]+', 'page' => '[a-z]+']);
     *
     * @param array $params
     * @return static|null
     */
    public static function where(array $params) {
        return self::add(new RouteMethodWhere(self::instance(), $params));
    }

    /**
     * It adds a supported query type or types in the array for the next running route or group.
     * If the query type differs, the route will be omitted.
     * For example, Route::type(['get', 'post'])->get( ... );
     *
     * Добавляет поддерживаемый тип запроса или типы в массиве для следом идущего маршрута или группы.
     * Если тип запроса будет отличаться, маршрут будет пропущен.
     * Например, Route::type(['get', 'post'])->get( ... );
     *
     * @param string|array $types
     * @return static|null
     */
    public static function type($types) {
        return self::add(new RouteMethodType(self::instance(), $types));
    }

    /**
     * It adds the query type or types supported in the array to the nested routes.
     * The routes are considered as nested before the endType() method.
     * For example, Route::getType(['get', 'post']);
     *
     * Добавляет вложенным маршрутам поддерживаемый тип запроса или типы в массиве.
     * Вложенными маршруты считаются до метода endType().
     * Например, Route::getType(['get', 'post']);
     *
     * @param string|array $types
     * @return static|null
     */
    public static function getType($types) {
        return self::add(new RouteMethodGetType(self::instance(), $types));
    }

    /**
     * It ends using of the gT() method.
     *
     * Завершает применение метода getType().
     *
     * @return static|null
     */
    public static function endType() {
        return self::add(new RouteMethodEndType(self::instance()));
    }

    /**
     * Assignment of a name for the list of templates to be connected.
     *
     * Назначение названия для перечня соединяемых шаблонов.
     *
     * @param string $name
     * @param string|array $map
     * @return static|null
     */
    public static function renderMap(string $name, $map) {
        return self::add(new RouteMethodRenderMap(self::instance(), $name, $map));
    }

    /**
     * It applies the ourte protection to the subsequent get( ... ) method.
     *
     * Применяет защиту маршрута к последующему методу get( ... ).
     *
     * @param string $validate
     * @return static|null
     */
    public static function protect(string $validate = 'CSRF') {
        return self::add(new RouteMethodProtect(self::instance(), $validate));
    }

    /**
     * It adds protection to the nested routes.
     * The routes are considered as nested before the endProtect() method.
     *
     * Добавляет защиту вложенным маршрутам.
     * Вложенными маршруты считаются до метода endProtect().
     *
     * @param string $validate
     * @return static|null
     */
    public static function getProtect(string $validate = 'CSRF') {
        return self::add(new RouteMethodGetProtect(self::instance(), $validate));
    }

    /**
     * End of applying protection to the getProtect() routes.
     *
     * Завершение применения защиты маршрутов getProtect().
     *
     * @return static|null
     */
    public static function endProtect() {
        return self::add(new RouteMethodEndProtect(self::instance()));
    }

    /**
     * Defining of a subdomain, starting from the required level.
     * The subsequent route or group can be executed only if the conditions match.
     * The first argument can be the list of subdomains in the array.
     *
     * Задание поддомена, начиная с необходимого уровня.
     * Выполнение последующего маршрута или группы возможно лишь при совпадении условия.
     * Первым аргументом может быть перечень поддоменов в массиве.
     *
     * @param array|string $name
     * @param int $level
     * @return static|null
     */
    public static function domain($name, $level = 3) {
        return self::add(new RouteMethodDomain(self::instance(), $name, $level, false));
    }

    /**
     * Defining a subdomain pattern, starting from the required regular expression level.
     * The subsequent route or group can be executed only if the conditions match.
     *
     * Задание паттерна поддомена, начиная с необходимого уровня по регулярному выражению.
     * Выполнение последующего маршрута или группы возможно лишь при совпадении условия.
     *
     * @param array|string $name
     * @param int $level
     * @return static|null
     */
    public static function domainPattern($name, $level = 3) {
        return self::add(new RouteMethodDomain(self::instance(), $name, $level, true));
    }

    /**
     * Similar to domainPattern( ... ). Defining a subdomain template, starting from the required regular expression level.
     * The subsequent route or group can be executed only if the conditions match.
     *
     * Аналог domainPattern( ... ). Задание шаблона поддомена, начиная с необходимого уровня  по регулярному выражению.
     * Выполнение последующего маршрута или группы возможно лишь при совпадении условия.
     *
     * @param array|string $name
     * @param int $level
     * @return static|null
     */
    public static function domainTemplate($name, $level = 3) {
        return self::add(new RouteMethodDomain(self::instance(), $name, $level, true));
    }

    /**
     * Difines the name of the route.
     *
     * Задаёт имя маршрута.
     *
     * @param string $name
     * @return static|null
     */
    public static function name(string $name) {
        return self::add(new RouteMethodName(self::instance(), $name));
    }

    /**
     * Assignment of the controller.
     * The class method can be specfied via '@', for example, 'DefaultСontroller@method'. If it is not specified, 'index' will be used.
     * The parameters will be transferred to the arguments of the controller by mapping the key-value of the array.
     *
     * Назначение контроллера.
     * Указать метод класса можно через '@', например, 'DefaultСontroller@method'. Если не указан, будет использован 'index'.
     * Параметры будут переданы в аргументы метода контроллера по сопоставлению ключ-значение массива.
     *
     * @param string $class_name
     * @param array $params
     * @return static|null
     */
    public static function controller(string $class_name, array $params = []) {
        return self::add(new RouteMethodController(self::instance(), $class_name, $params));
    }

    /**
     * Version of controller connection for operation in a modular style.
     * $module_name refers to the /modules/ folder with the module, where the controller and content are located.
     *
     * Вариация подключения контроллера для работы в модульном стиле.
     * $module_name указывает на папку /modules/ с модулем, где находятся контроллер и контент.
     *
     * @param string $module_name
     * @param string $class_name
     * @param array $params
     * @return static|null
     */
    public static function module(string $module_name, string $class_name = "Controller", array $params = []) {
        return self::add(new RouteMethodModule(self::instance(), $module_name, $class_name, $params));
    }

    /**
     * Version of controller connection for implementation of the content into the administration panel.
     *
     * Вариация подключения контроллера для включения контента в административную панель.
     *
     * @param string $class_name
     * @param string|array $block_name
     * @param array $params
     * @return static|null
     */
    public static function adminPanController(string $class_name, $block_name, array $params = []) {
        return self::add(new RouteMethodAdminPanController(self::instance(), $class_name, $block_name, $params));
    }

    /**
     * Setting a prefix for a route or group of routes.
     *
     * Установка префикса маршруту или группе маршрутов.
     *
     * @param string $add
     * @return static|null
     */
    public static function prefix(string $add) {
        return self::add(new RouteMethodPrefix(self::instance(), $add));
    }

}

