<?php

declare(strict_types=1);

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Data\View;
use Hleb\HlebBootstrap;
use Hleb\Route\Alias;
use Hleb\Route\Any;
use Hleb\Route\Delete;
use Hleb\Route\Fallback;
use Hleb\Route\Get;
use Hleb\Route\Group\EndGroup;
use Hleb\Route\Group\ToGroup;
use Hleb\Route\MatchTypes;
use Hleb\Route\Options;
use Hleb\Route\Patch;
use Hleb\Route\Post;
use Hleb\Route\Put;

#[Accessible]
final class Route
{
    private function __construct()
    {
    }

    /**
     * To assign a URL route to the rendered content for the GET method.
     * Route::get('/address/', 'Content'); - displaying a text string.
     * Route::get('/', view('index')); - template display (file /resources/views/index.php).
     * Route::get('/handling/default/')->controller(DefaultController::class); - addressing to the controller. The second argument is missing.
     *
     * Для назначения URL-маршрута отображаемому контенту для метода GET.
     * Route::get('/address/', 'Content'); - отображение текстовой строки.
     * Route::get('/', view('index')); - отображение шаблона (файл /resources/views/index.php).
     * Route::get('/handling/default/')->controller(DefaultController::class); - обращение к контроллеру. Второй аргумент отсутствует.
     *
     * @see view()
     */
    public static function get(string $route, null|int|float|string|View $view = null): Get
    {
        return new Get($route, $view);
    }

    /**
     * To assign a URL route to the rendered content for the POST method.
     * Route::post('/address/', 'Content'); - displaying a text string.
     * Route::post('/', view('index')); - template display (file /resources/views/index.php).
     * Route::post('/handling/default/')->controller(DefaultController::class); - addressing to the controller. The second argument is missing.
     *
     * Для назначения URL-маршрута отображаемому контенту для метода POST.
     * Route::post('/address/', 'Content'); - отображение текстовой строки.
     *
     * Route::post('/', view('index')); - отображение шаблона (файл /resources/views/index.php).
     * Route::post('/handling/default/')->controller(DefaultController::class); - обращение к контроллеру. Второй аргумент отсутствует.
     *
     * @see view()
     */
    public static function post(string $route, null|int|float|string|View $view = null): Post
    {
        return new Post($route, $view);
    }

    /**
     * To assign a URL route to the rendered content for the PUT method.
     * Route::put('/address/', 'Content'); - displaying a text string.
     * Route::put('/', view('index')); - template display (file /resources/views/index.php).
     * Route::put('/handling/default/')->controller(DefaultController::class); - addressing to the controller. The second argument is missing.
     *
     * Для назначения URL-маршрута отображаемому контенту для метода PUT.
     * Route::put('/address/', 'Content'); - отображение текстовой строки.
     * Route::put('/', view('index')); - отображение шаблона (файл /resources/views/index.php).
     * Route::put('/handling/default/')->controller(DefaultController::class); - обращение к контроллеру. Второй аргумент отсутствует.
     *
     * @see view()
     */
    public static function put(string $route, null|int|float|string|View $view = null): Put
    {
        return new Put($route, $view);
    }

    /**
     * To assign a URL route to the rendered content for the DELETE method.
     * Route::delete('/address/', 'Content'); - displaying a text string.
     * Route::delete('/', view('index')); - template display (file /resources/views/index.php).
     * Route::delete('/handling/default/')->controller(DefaultController::class); - addressing to the controller. The second argument is missing.
     *
     * Для назначения URL-маршрута отображаемому контенту для метода DELETE.
     * Route::delete('/address/', 'Content'); - отображение текстовой строки.
     * Route::delete('/', view('index')); - отображение шаблона (файл /resources/views/index.php).
     * Route::delete('/handling/default/')->controller(DefaultController::class); - обращение к контроллеру. Второй аргумент отсутствует.
     *
     * @see view()
     */
    public static function delete(string $route, null|int|float|string|View $view = null): Delete
    {
        return new Delete($route, $view);
    }

    /**
     * To assign a URL route to the rendered content for the PATCH method.
     * Route::patch('/address/', 'Content'); - displaying a text string.
     * Route::patch('/', view('index')); - template display (file /resources/views/index.php).
     * Route::patch('/handling/default/')->controller(DefaultController::class); - addressing to the controller. The second argument is missing.
     *
     * Для назначения URL-маршрута отображаемому контенту для метода PATCH.
     * Route::patch('/address/', 'Content'); - отображение текстовой строки.
     * Route::patch('/', view('index')); - отображение шаблона (файл /resources/views/index.php).
     * Route::patch('/handling/default/')->controller(DefaultController::class); - обращение к контроллеру. Второй аргумент отсутствует.
     *
     * @see view()
     */
    public static function patch(string $route, null|int|float|string|View $view = null): Patch
    {
        return new Patch($route, $view);
    }

    /**
     * The OPTIONS method is automatically supported by other methods in the framework and returns a standard list.
     * If you need to override it, then you need to call this method before any other with the same address.
     * Route::options('/handling/default/')->controller(DefaultController::class); - addressing to the controller.
     *
     * Метод OPTIONS автоматически поддерживается другими методами во фреймворке и возвращает стандарный перечень.
     * Если необходимо его переопределить, то нужно вызвать этот метод перед любым другим с тем же адресом.
     * Route::options('/handling/default/')->controller(DefaultController::class); - обращение к контроллеру.
     */
    public static function options(string $route): Options
    {
        return new Options($route);
    }

    /**
     * Similar to the 'get' method, but uses all available HTTP methods.
     *
     * Аналогия методу 'get', только использует все доступные HTTP методы.
     *
     * @see self::get()
     */
    public static function any(string $route, null|int|float|string|View $view = null): Any
    {
        return new Any($route, $view);
    }

    /**
     * Similar to the 'get' method, but uses the HTTP methods passed to 'types'.
     *
     * Аналогия методу 'get', только использует переданные в 'types' HTTP методы.
     *
     * @see self::get()
     */
    public static function match(array $types, string $route, null|int|float|string|View $view = null): MatchTypes
    {
        return new MatchTypes($types, $route, $view);
    }

    /**
     * Adds the following data via Route to the group.
     * In this case, the methods following this will be added to the properties of the group.
     *
     * Добавляет следующие маршруты через Route в группу.
     * При этом методы, следующие за этим, будут добавляться в свойства группы.
     *
     * ```php
     *   Route::toGroup()->prefix('/api/');
     *
     *      Route::get('/users/')
     *           ->controller(ApiController::class, 'users'); // GET /api/users
     *
     *      Route::post('/user/')
     *           ->controller(ApiController::class, 'createUser'); // POST /api/user
     *
     *    Route::endGroup();
     * ```
     */
    public static function toGroup(): ToGroup
    {
        return new ToGroup();
    }

    /**
     * Finishes adding methods to the group.
     *
     * Завершает добавление методов в группу.
     *
     * ```php
     *  Route::toGroup()->prefix('/api/');
     *
     *     Route::get('/users/')
     *          ->controller(ApiController::class, 'users'); // GET /api/users
     *
     *     Route::post('/user/')
     *          ->controller(ApiController::class, 'createUser'); // POST /api/user
     *
     *   Route::endGroup();
     *  ```
     */
    public static function endGroup(): EndGroup
    {
        return new EndGroup();
    }

    /**
     * Intercepts all unmatched paths for all HTTP methods (or specified ones).
     * There can only be one `fallback` method in routes for a particular HTTP method.
     * If you need to apply different content to different mismatched HTTP methods,
     * create an additional fallback().
     *
     * Перехватывает все не сопоставленные пути для всех HTTP методов (или для указанных).
     * Может быть только один метод `fallback` в маршрутах для конкретного HTTP-метода.
     * Если нужно применить разный контент к разным не совпавшим HTTP-методам,
     * создайте дополнительный fallback().
     *
     * ```php
     *  Route::fallback(view('404'));
     *  ```
     *
     * @param null|int|float|string|View $view - standard options, string or view(...)
     *                                         - стандартные параметры, строка или view(...)
     *
     * @param array $httpTypes - HTTP-методы, на которые оказывает влияние метод.
     */
    public static function fallback(null|int|float|string|View $view = null, array $httpTypes = HlebBootstrap::HTTP_TYPES): Fallback
    {
        return new Fallback($view, $httpTypes);
    }

    /**
     * Creates a clone of the route named $name with a new name $newName and address.
     * If the target route is dynamic, then the required parameters must be passed in the new address.
     * You cannot assign additional conditions to this action, such as adding a controller,
     * but when in a group, the method accepts actions from the group,
     * adding them on top of the target ones.
     * For example:
     *
     * Создает клон маршрута по имени $name c новым именем $newName и адресом.
     * Если целевой маршрут динамический, то нужно передать необходимые параметры в новом адресе.
     * К этому действию нельзя назначить дополнительные условия, например добавить контроллер,
     * но находясь в группе метод принимает действия от группы, добавляя их поверх целевых.
     * Например:
     *
     * ```php
     * Route::get('/user/{id}/', view('user'))->name('profile');
     * Route::alias('/profile/{id}/', 'main.profile', 'profile');
     *
     * Route::toGroup()->prefix('/demo/');
     *   // ... //
     *   Route::alias('/user/{id}/', 'demo.profile', 'profile');
     * Route::endGroup();
     * ```
     */
    public static function alias(string $route, string $newName, string $name): Alias
    {
        return new Alias($route, $newName, $name);
    }
}
