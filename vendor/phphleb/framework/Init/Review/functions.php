<?php

declare(strict_types=1);

use Hleb\Constructor\Data\View;
use Hleb\HttpMethods\External\RequestUri;
use Hleb\HttpMethods\Specifier\DataType;
use Hleb\Main\Console\Specifiers\ArgType;
use Hleb\Reference\LogInterface;
use Hleb\Static\Cache;
use Hleb\Static\Debug;
use Hleb\Static\Once;
use Hleb\Static\Path;
use Hleb\Static\Redirect;
use Hleb\Static\Request;
use Hleb\Static\Script;
use Hleb\Static\Settings;
use Hleb\Static\Template;

if (!function_exists('hl_debug')) {
    /**
     * Returns the debug mode status from the framework settings.
     *
     * Возвращает статус режима отладки из настроек фреймворка.
     *
     * @return bool
     */
    function hl_debug(): bool
    {
        return Settings::isDebug();
    }
}

if (!function_exists('hl_config')) {
    /**
     * Getting any value from the framework configuration by value name.
     *
     * Получение любого значения из конфигурации фреймворка по названию значения.
     */
    function hl_config(string $name): float|bool|array|int|string|null
    {
        return Settings::getParam('common', $name);
    }
}

if (!function_exists('hl_db_config')) {
    /**
     * Getting database settings from the framework configuration by value name.
     *
     * Получение настроек баз данных из конфигурации фреймворка по названию значения.
     */
    function hl_db_config(string $key): float|bool|int|string|null
    {
        return Settings::getParam('database', $key);
    }
}

if (!function_exists('hl_realpath')) {
    /**
     * Returns the full path to the folder according to the project settings.
     * For example:
     * '@' or `@global` - the path to the project`s root directory.
     * '@public' - path to the public folder of the project.
     * '@storage' - path to the project data folder.
     * '@views' - path to folder with file templates.
     * '@modules' - path to the `modules' folder (if it exists).
     * It is also possible to supplement this request by specifying a continuation to an EXISTING folder
     * or file. For example: hl_realpath('@global/resources') or hl_realpath('@public/favicon.ico').
     *
     * Возвращает полный путь до папки согласно настройкам проекта.
     * Например:
     * '@' или `@global` - путь до корневой директории проекта.
     * '@public' - путь до публичной папки проекта.
     * '@storage' - путь до папки с данными проекта.
     * '@views' - путь до папки с шаблонами файлов.
     * '@modules' - путь до папки с модулями (при её существовании).
     * Также можно дополнить этот запрос, указав продолжение к СУЩЕСТВУЮЩЕЙ папке
     * или файлу. Например: hl_realpath('@global/resources') или hl_realpath('@public/favicon.ico').
     *
     * @see PathInfoDoc::special()
     */
    function hl_realpath(string $keyOrPath): string|false
    {
        return Path::getReal($keyOrPath);
    }
}

if (!function_exists('hl_path')) {
    /**
     * Similar to the hl_realpath() function, but returns the path without checking for existence.
     *
     * Аналог функции hl_realpath(), но возвращает путь без проверки на существование.
     *
     * @see hl_realpath()
     * @see PathInfoDoc::special()
     */
    function hl_path(string $keyOrPath): string|false
    {
        return Path::get($keyOrPath);
    }
}

if (!function_exists('is_async')) {
    /**
     * Returns the current mode of using the framework in asynchronous mode.
     *
     * Возвращает соответствие текущему режиму использования фреймворка в асинхронном режиме.
     */
    function is_async(): bool
    {
        return Settings::isAsync();
    }
}

if (!function_exists('async_exit')) {
    /**
     * Simulation with exit from the process (script) for asynchronous mode
     * and normal exit for standard mode.
     * For the function to work correctly in asynchronous mode, it is necessary
     * do not catch the thrown exception AsyncExitException in the code above.
     *
     * Имитация с выходом из процесса (скрипта) для асинхронного режима
     * и обычный выход для стандартного режима.
     * Для корректной работы функции в асинхронном режиме необходимо
     * не перехватывать выбрасываемое исключение AsyncExitException
     * в коде уровнем выше.
     *
     *  ```php
     *   try {
     *       ...
     *   } catch (\AsyncExitException $e) {
     *     throw $e
     *   } catch (\ErrorException $t) {
     *     ...
     *   }
     * ```
     *
     * @throws AsyncExitException
     */
    function async_exit($message = '', ?int $httpStatus = null, array $headers = []): never
    {
        Script::asyncExit($message, $httpStatus, $headers);
    }
}

if (!function_exists('view')) {
    /**
     * The view() function allows you to display the desired template or plain text from the controller or route.
     * Example: return view('default', ['param' => 'value']); to return from the controller.
     * Example: Route::get('/', view('example', ['param' => 'value']); in a router.
     *
     * Функция view() позволяет выводить из контроллера или в маршруте нужный шаблон или простой текст.
     * Пример: return view('default', ['param' => 'value']); для возврата из контроллера.
     * Пример: Route::get('/', view('example', ['param' => 'value']); в маршрутизаторе.
     *
     * @param string $template - path to the template in the resources/views folder (or modules/{module_name}/views
     *                           in a module). If it is a PHP file, then the extension does not need to be specified.
     *                         - путь до шаблона в папке resources/views (или modules/{module_name}/views в модуле).
     *                           Если это PHP-файл, то расширение указывать не нужно.
     *
     * @param array $params - The named parameters will be passed to the assigned template as variables.
     *                      - Именованные параметры будут переданы в назначенный шаблон как переменные.
     *
     * @param int|null $status - For some simple tasks, you can immediately assign an HTTP response code.
     *                         - Для некоторых простых задач,  можно сразу назначить HTTP-код ответа.
     *
     * @return View
     */
    function view(string $template, array $params = [], ?int $status = null): View
    {
        return \Hleb\Static\View::view($template, $params, $status);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * The csrf_token() function returns the protected token for protection against CSRF attacks.
     *
     * Функция csrf_token() возвращает защищённый токен для защиты от CSRF-атак.
     */
    function csrf_token(): string
    {
        return \Hleb\Static\Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * The Csrf::field() method returns HTML content to be inserted
     * into the form to protect against CSRF attacks.
     *
     * Метод Csrf::field() возвращает HTML-контент для вставки
     * в форму для защиты от CSRF-атак.
     */
    function csrf_field(): string
    {
        return \Hleb\Static\Csrf::field();
    }
}

if (!function_exists('template')) {
    /**
     * Returns the content of the initialized template.
     *
     * Возвращает содержимое инициализированного шаблона.
     *
     * @see insertTemplate()
     */
    function template(string $viewPath, array $extractParams = [], array $config = []): string
    {
        return Template::get($viewPath, $extractParams, $config);
    }
}

if (!function_exists('insertTemplate')) {
    /**
     * Inserting a template and assigning variables from its parameters.
     * Example: insertTemplate('test', ['param' => 'value']);
     * Outputs the template /resources/views/test.php where the $param variable is equal to 'value'.
     * Example: insertTemplate('test.twig'); outputs template /resources/views/test.twig similarly
     * Example for a module: for an active module, the path will point to a folder
     * /modules/{module_name}/views/test.php
     *
     * Вставка шаблона и назначение переменных из его параметров.
     * Пример: insertTemplate('test', ['param' => 'value']);
     * Выводит шаблон /resources/views/test.php в котором переменная $param равна 'value'.
     * Пример: insertTemplate('test.twig'); аналогично выводит шаблон /resources/views/test.twig
     * Пример для модуля: для активного модуля путь будет указывать в папку
     * /modules/{module_name}/views/test.php
     *
     */
    function insertTemplate(string $viewPath, array $extractParams = [], array $config = []): void
    {
        /**
         * Additional variables that will be active in the template.
         *
         * Дополнительные переменные, которые будут активны в шаблоне.
         *
         * @var $container - container for templates.
         *                 - контейнер для шаблонов.
         *
         * @var $extractParams - initial data set.
         *                     - первоначальный массив данных.
         */
        Template::insert($viewPath, $extractParams, $config);
    }
}

if (!function_exists('insertCacheTemplate')) {
    /**
     * Allows you to save the template to the cache, for example
     * insertCacheTemplate('template', ['param' => 'value'], sec:60)
     * will save the template to the cache for 1 minute.
     * In this case, if the template parameters ($extractParams) change,
     * then this will be a newly created cache, since the parameters are included
     * in the cache key along with $viewPath.
     *
     * Позволяет сохранить шаблон в кеш, например
     * insertCacheTemplate('template', ['param' => 'value'], sec:60)
     * сохранит в кеш шаблон на 1 минуту.
     * При этом если параметры шаблона ($extractParams) изменятся,
     * то это будет новый созданный кеш,
     * так как параметры входят в ключ кеша вместе с $viewPath.
     *
     * @see insertTemplate() - more about function arguments.
     *                       - подробнее об аргументах функции.
     */
    function insertCacheTemplate(string $viewPath, array $extractParams = [], int $sec = Cache::DEFAULT_TIME, array $config = []): void
    {
        Template::insertCache($viewPath, $extractParams, $sec, $config);
    }
}

if (!function_exists('url')) {
    /**
     * Returns the standardized address from the route name in the URL.
     * For example, for a route: Route::get('/test/{var1}/{var2?}', ...)->name('example');
     * you need to pass the following data to the function url('example', ['var1' => 3000, 'var2' => 'pro'], true);
     * to get this string value '/test/3000/pro'. This will set the trailing slash
     * depending on project settings.
     * Returns error if the parameters did not match the route (there is no such name, the route does not support
     * the specified method, the replacement parts of the route did not fit) or an error in obtaining routes.
     *
     * Возвращает стандартизированный адрес из названия маршрута в URL.
     * Например, для маршрута Route::get('/test/{var1}/{var2?}', ...)->name('example');
     * нужно передать следующие данные в функцию url('example', ['var1' => 3000, 'var2' => 'pro'], true);
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
    function url(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): string
    {
        return \Hleb\Static\Router::url($routeName, $replacements, $endPart, $method);
    }
}

if (!function_exists('address')) {
    /**
     * Returns the full URL given the route name and current domain. For example `https://site.com/test/3000/pro`.
     * In this case, the trailing slash will be set depending on the project settings.
     * Since only the current domain is assigned, use concatenation with url() for another domain.
     * Returns error if the parameters did not match the route (there is no such name, the route does not support
     * the specified method, the replacement parts of the route did not fit) or an error in obtaining routes.
     *
     * Возвращает полный URL-адрес по имени маршрута и текущего домена. Например `https://site.com/test/3000/pro`.
     * При этом конечный слеш будет установлен в зависимости от настроек проекта.
     * Так как домен присваивается только текущий, для другого домена используйте конкатенацию с url().
     * Возвращает ошибку если параметры не совпали с маршрутом (нет такого названия, маршрут не поддерживает
     * указанный метод, не подошли заменяемые части маршрута) или ошибка получения маршрутов.
     *
     * @see url() - more about method arguments.
     *            - подробнее об аргументах метода.
     */
    function address(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): string
    {
        return \Hleb\Static\Router::address($routeName, $replacements, $endPart, $method);
    }
}

if (!function_exists('Arg')) {
    /**
     * Standardization of input data to assign a console command argument.
     *
     * Various combinations of parameters are possible.
     * The first argument in the example supports the two values -N and --Name,
     * and is required. By default --Name is 'Undefined',
     * the input value can only be a string (not an array).
     * The value can be --Name=Fedor or -N=Mark, with --Name being 'Undefined'.
     * The second argument of the form --force, if present, is true.
     * The third argument is an array, the value can be specified multiple times
     * as --Options=1 and --Options=2, which is equivalent to --Options=[1,2], its presence
     * is optional and if not present or called as --Options will be equal to [] (an empty array).
     *
     *
     * Стандартизация введенных данных для назначения аргумента консольной команды.
     *
     * Возможны различные комбинации параметров.
     * Первый аргумент в примере поддерживает два значения -N и --Name, наличие его обязательно.
     * По умолчанию --Name равен 'Undefined', входящее значение может быть только строкой (не массив).
     * Значение может быть типа --Name=Fedor или -N=Mark, при --Name будет равно 'Undefined'.
     * Второй аргумент вида --force, если присутствует, то равен true.
     * Третий аргумент массив, значение может быть задано несколько раз как --Options=1 и --Options=2,
     * что равнозначно --Options=[1,2], наличие его необязательно и при отсутствии значения или вызове
     * как --Options будет равен [] (пустому массиву).
     *
     * ```php
     *
     *  protected function rules(): array
     *  {
     *    return [
     *       Arg(name:'Name')->short(name:'N')->default('Undefined')->required(),
     *       Arg(name:'force'),
     *       Arg(name:'Options')->list()->default([]),
     *    ];
     *  }
     * ```
     *
     * @see ArgType - a detailed list of parameters for the argument.
     *              - подробный перечень параметров для аргумента.
     */
    function Arg(?string $name): ArgType
    {
        return new ArgType($name);
    }
}

if (!function_exists('print_r2')) {
    /**
     * Data output to the debug panel.
     *
     * Вывод данных в панель отладки.
     */
    function print_r2(mixed $data, ?string $name = null): void
    {
        Debug::send($data, $name);
    }
}

if (!function_exists('route_name')) {
    /**
     * Returns the name of the current route, or null if none has been assigned.
     *
     * Возвращает название текущего маршрута или null если оно не назначено.
     */
    function route_name(): null|string
    {
        return \Hleb\Static\Router::name();
    }
}

if (!function_exists('param')) {
    /**
     * Returns an object with dynamic query data by parameter name
     * with a choice of value format.
     *
     * Возвращает объект с данными динамического запроса по имени параметра
     * с возможностью выбора формата значения.
     *
     * @see Request::param() - more about return parameters.
     *                       - подробнее о возвращаемых параметрах.
     */
    function param(string $name): DataType
    {
        return Request::param($name);
    }
}

if (!function_exists('setting')) {
    /**
     * Since custom values are recommended to be added to /config/main.php,
     * a separate function is provided for frequent use of this configuration.
     *
     * Ввиду того, что пользовательские значения рекомендовано добавлять в /config/main.php,
     * то для частого использования этой конфигурации предусмотрена отдельная функция.
     */
    function setting(string $key): mixed
    {
        return Settings::getParam('main', $key);
    }
}

if (!function_exists('config')) {
    /**
     * A wrapper for receiving settings parameters.
     *
     * Обёртка для получения параметров настроек.
     *
     * @see settings()
     */
    function config(string $name, string $key): mixed
    {
        return Settings::getParam($name, $key);
    }
}

if (!function_exists('hl_redirect')) {
    /**
     * Replacing the internal redirect for normal and asynchronous requests.
     *
     * Замена внутреннего редиректа для обычных и асинхронных запросов.
     */
    function hl_redirect(string $location, int $status = 302): void
    {
        Redirect::to($location, $status);
    }
}

if (!function_exists('request_uri')) {
    /**
     * Returns a RequestUri object with the parameters
     * of the current address.
     *
     * Возвращает объект RequestUri с параметрами
     * текущего адреса.
     */
    function request_uri(): RequestUri
    {
        return Request::getUri();
    }
}

if (!function_exists('request_host')) {
    /**
     * Returns the current host (and port if non-default).
     *
     * Возвращает текущий хост (и порт, если он не стандартный).
     */
    function request_host(): string
    {
        return Request::getUri()->getHost();
    }
}

if (!function_exists('request_path')) {
    /**
     * Returns the current request path from a URL
     * with no parameters.
     * Parameters can be obtained as:
     *
     * Возвращает текущий путь запроса из URL
     * без параметров.
     * Параметры можно получить как:
     *
     * app_request_uri()->getQuery();
     */
    function request_path(): string
    {
        return Request::getUri()->getPath();
    }
}

if (!function_exists('request_address')) {
    /**
     * Returns the current request URL without parameters.
     * Parameters can be obtained as:
     *
     * Возвращает текущий URL запроса без параметров.
     * Параметры можно получить как:
     *
     * app_request_uri()->getQuery();
     */
    function request_address(): string
    {
        return Request::getAddress();
    }
}

if (!function_exists('logger')) {
    /**
     * Logging according to the established levels. logger()->error('Message', []);
     *
     * Логирование по установленным уровням. logger()->error('Message', []);
     */
    function logger(): LogInterface
    {
        return new Hleb\Main\Logger\LoggerWrapper();
    }
}

if (!function_exists('hl_file_exists')) {
    /**
     * Similar to the file_exists function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_exists, но дополнительно
     * может принимать специальные пути с '@' в начале.
     */
    function hl_file_exists(string $path): bool
    {
        return Path::exists($path);
    }
}

if (!function_exists('hl_file_get_contents')) {
    /**
     * Similar to the file_get_contents function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_get_contents, но дополнительно
     * может принимать специальные пути с '@' в начале.
     */
    function hl_file_get_contents(string $path, bool $use_include_path = false, $context = null, int $offset = 0, ?int $length = null): false|string
    {
        return Path::contents($path, $use_include_path, $context, $offset, $length);
    }
}

if (!function_exists('hl_file_put_contents')) {
    /**
     * Similar to the file_put_contents function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции file_put_contents, но дополнительно
     * может принимать специальные пути с '@' в начале.
     */
    function hl_file_put_contents(string $path, mixed $data, int $flags = 0, $context = null): false|int
    {
        return Path::put($path, $data, $flags, $context);
    }
}

if (!function_exists('hl_is_dir')) {
    /**
     * Similar to the is_dir function, but can additionally
     * accept special paths with '@' at the beginning.
     *
     * Аналог функции is_dir, но дополнительно
     * может принимать специальные пути с '@' в начале.
     */
    function hl_is_dir(string $path): bool
    {
        return Path::isDir($path);
    }
}

if (!function_exists('is_empty')) {
    /**
     * Checking for empty value, more selective than empty().
     *
     * Проверка на пустое значение, более избирательная, чем empty().
     */
    function is_empty(mixed $value): bool
    {
        return $value === null || $value === [] || $value === '';
    }
}

if (!function_exists('once')) {
    /**
     * The once() function allows you to execute a piece of code only once for one request,
     * and when accessed again, it returns the same result.
     * The execution result is stored in RAM throughout the entire request.
     * In this scenario, the anonymous function passed to once will be executed the first time once is called:
     *
     * Функция once() позволяет выполнять часть кода только единожды для одного запроса,
     * а при повторном обращении возвращает прежний результат.
     * Результат выполнения хранится в оперативной памяти в течении всего запроса.
     * В этом сценарии анонимная функция, переданная в once, будет выполнена при первом вызове once:
     *
     * ```php
     * $value = once(function () {
     *     // An example of a resource-intensive operation.
     *     return ExampleStorage::getData();
     * });
     * ```
     */
    function once(callable $func): mixed
    {
        return Once::get($func);
    }
}
