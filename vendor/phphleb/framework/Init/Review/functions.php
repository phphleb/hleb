<?php

declare(strict_types=1);

use Hleb\Constructor\Data\View;
use Hleb\Static\View as StaticView;
use Hleb\HttpMethods\External\RequestUri;
use Hleb\HttpMethods\Specifier\DataType;
use Hleb\Main\Console\Specifiers\ArgType;
use Hleb\Main\Logger\LoggerWrapper;
use Hleb\Reference\LogInterface;
use Hleb\Static\Cache;
use Hleb\Static\Csrf;
use Hleb\Static\Debug;
use Hleb\Static\Once;
use Hleb\Static\Path;
use Hleb\Static\Redirect;
use Hleb\Static\Request;
use Hleb\Static\Router;
use Hleb\Static\Script;
use Hleb\Static\Settings;
use Hleb\Static\Template;
use JetBrains\PhpStorm\NoReturn;

if (!function_exists('hl_debug')) {
    /**
     * Returns the debug mode status from the framework settings.
     *
     * Возвращает статус режима отладки из настроек фреймворка.
     */
    function hl_debug(): bool
    {
        return Settings::isDebug();
    }
}

if (!function_exists('hl_db_config')) {
    /**
     * Getting database settings from the framework configuration by value name.
     *
     * Получение настроек баз данных из конфигурации фреймворка по названию значения.
     */
    function hl_db_config(string $key): mixed
    {
        return Settings::getParam('database', $key);
    }
}

if (!function_exists('hl_db_connection')) {
    /**
     * Obtaining database connection data from the framework configuration by connection name.
     *
     * Получение данных соединения к базе данных из конфигурации фреймворка по названию подключения.
     */
    function hl_db_connection(string $name): array
    {
        $connection = hl_db_config('db.settings.list')[$name] ?? null;
        if (!$connection || !\is_array($connection)) {
            throw new InvalidArgumentException('Connection not found: ' . $name);
        }
        return $connection;
    }
}

if (!function_exists('hl_db_active_connection')) {
    /**
     * Retrieving active database connection data from the framework configuration.
     *
     * Получение данных активного соединения к базе данных из конфигурации фреймворка.
     */
    function hl_db_active_connection(): array
    {
        return hl_db_connection(hl_db_config('base.db.type'));
    }
}

if (!function_exists('hl_realpath')) {
    /**
     * Returns the full path to the folder according to the project settings.
     * Can accept special paths with '@' at the beginning.
     *
     * Возвращает полный путь до папки согласно настройкам проекта.
     * Может принимать специальные пути с '@' в начале.
     *
     * @see PathInfoDoc::special()
     */
    /*
     * For example:
     * @ or @global - the path to the project`s root directory.
     * @public - path to the public folder of the project.
     * @storage - path to the project data folder.
     * @views - path to folder with file templates.
     * @modules - path to the `modules' folder (if it exists).
     * It is also possible to supplement this request by specifying a continuation to an EXISTING folder
     * or file. For example: hl_realpath('@global/resources') or hl_realpath('@public/favicon.ico').
     *
     * Например:
     * @ или @global - путь до корневой директории проекта.
     * @public - путь до публичной папки проекта.
     * @storage - путь до папки с данными проекта.
     * @views - путь до папки с шаблонами файлов.
     * @modules - путь до папки с модулями (при её существовании).
     * Также можно дополнить этот запрос, указав продолжение к СУЩЕСТВУЮЩЕЙ папке
     * или файлу. Например: hl_realpath('@global/resources') или hl_realpath('@public/favicon.ico').
     */
    function hl_realpath(string $keyOrPath): string|false
    {
        return Path::getReal($keyOrPath);
    }
}

if (!function_exists('hl_path')) {
    /**
     * Similar to the hl_realpath() function, but returns the path without checking for existence.
     * Can accept special paths with '@' at the beginning.
     *
     * Аналог функции hl_realpath(), но возвращает путь без проверки на существование.
     * Может принимать специальные пути с '@' в начале.
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
     * Returns true if the framework is used in asynchronous mode.
     *
     * Возвращает true, если фреймворк используется в асинхронном режиме.
     *
     * @alias hl_is_async()
     */
    function is_async(): bool
    {
        return Settings::isAsync();
    }
}

if (!function_exists('hl_is_async')) {
    /**
     * Returns true if the framework is used in asynchronous mode.
     *
     * Возвращает true, если фреймворк используется в асинхронном режиме.
     *
     * @see is_async() - alias with short name.
     */
    function hl_is_async(): bool
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
     * @throws AsyncExitException - in asynchronous mode.
     *                             - в асинхронном режиме.
     * @alias hl_async_exit()
     */
    #[NoReturn]
    function async_exit($message = '', ?int $httpStatus = null, array $headers = []): never
    {
        Script::asyncExit($message, $httpStatus, $headers);
    }
}

if (!function_exists('hl_async_exit')) {
    /**
     * Simulation with exit from the process (script) for asynchronous mode
     * and normal exit for standard mode.
     *
     * Имитация с выходом из процесса (скрипта) для асинхронного режима
     * и обычный выход для стандартного режима.
     *
     * @throws AsyncExitException - in asynchronous mode.
     *                             - в асинхронном режиме.
     *
     * @see async_exit() - learn more about the capabilities of the function in the main version.     *
     *                   - подробно о возможностях функции в основном варианте.
     */
    #[NoReturn]
    function hl_async_exit($message = '', ?int $httpStatus = null, array $headers = []): never
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
        return StaticView::view($template, $params, $status);
    }
}

if (!function_exists('hl_view')) {
    /**
     * @internal
     *
     * @see view() - current version of the function.
     *             - актуальная версия функции.
     */
    function hl_view(string $template, array $params = [], ?int $status = null): View
    {
        return StaticView::view($template, $params, $status);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * The csrf_token() function returns the protected token for protection against CSRF attacks.
     *
     * Функция csrf_token() возвращает защищённый токен для защиты от CSRF-атак.
     *
     * @alias hl_csrf_token()
     */
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('hl_csrf_token')) {
    /**
     * The csrf_token() function returns the protected token for protection against CSRF attacks.
     *
     * Функция csrf_token() возвращает защищённый токен для защиты от CSRF-атак.
     *
     * @see csrf_token() - alias with short name.
     */
    function hl_csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * The csrf_field function returns HTML content to be inserted
     * into the form to protect against CSRF attacks.
     *
     * Функция csrf_field возвращает HTML-контент для вставки
     * в форму для защиты от CSRF-атак.
     *
     * @alias hl_csrf_field()
     */
    function csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('hl_csrf_field')) {
    /**
     * The csrf_field function returns HTML content to be inserted
     * into the form to protect against CSRF attacks.
     *
     * Функция csrf_field возвращает HTML-контент для вставки
     * в форму для защиты от CSRF-атак.
     *
     * @see hl_csrf_token() - alias with short name.
     */
    function hl_csrf_field(): string
    {
        return Csrf::field();
    }
}

if (!function_exists('template')) {
    /**
     * Returns the content of the initialized template.
     *
     * Возвращает содержимое инициализированного шаблона.
     *
     * @param string $viewPath - special path to the template file.
     *                         - специальный путь к файлу шаблона.
     *
     * @param array $extractParams - a named array of values converted into variables inside the template.
     *                             - именованный массив значений преобразуемых в переменные внутри шаблона.
     *
     * @param array $config - config for replacing data in the transferred container when testing the template.
     *                      - конфиг для замены данных в передаваемом контейнере при тестировании шаблона.
     *
     * @see insertTemplate()
     * @alias hl_template()
     */
    function template(string $viewPath, array $extractParams = [], array $config = []): string
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
        return Template::get($viewPath, $extractParams, $config);
    }
}

if (!function_exists('hl_template')) {
    /**
     * Returns the content of the initialized template.
     *
     * Возвращает содержимое инициализированного шаблона.
     *
     * @see insertTemplate()
     * @see template() - alias with short name.
     */
    function hl_template(string $viewPath, array $extractParams = [], array $config = []): string
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
     * @param string $viewPath - special path to the template file.
     *                         - специальный путь к файлу шаблона.
     *
     * @param array $extractParams - a named array of values converted into variables inside the template.
     *                             - именованный массив значений преобразуемых в переменные внутри шаблона.
     *
     * @param array $config - config for replacing data in the transferred container when testing the template.
     *                      - конфиг для замены данных в передаваемом контейнере при тестировании шаблона.
     *
     * @alias hl_insert_template()
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

if (!function_exists('hl_insert_template')) {
    /**
     * Inserting a template and assigning variables from its parameters.
     * Example: hl_insert_template('test', ['param' => 'value']);
     *
     * Вставка шаблона и назначение переменных из его параметров.
     * Пример: hl_insert_template('test', ['param' => 'value']);
     *
     * @see insertTemplate() - current version of the function.
     *                       - актуальная версия функции.
     */
    function hl_insert_template(string $viewPath, array $extractParams = [], array $config = []): void
    {
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
     * @param string $viewPath - special path to the template file.
     *                         - специальный путь к файлу шаблона.
     *
     * @param array $extractParams - a named array of values converted into variables inside the template.
     *                             - именованный массив значений преобразуемых в переменные внутри шаблона.
     *
     * @param int $sec - number of seconds for caching.
     *                 - количество секунд для кеширования.
     *
     * @param array $config - config for replacing data in the transferred container when testing the template.
     *                      - конфиг для замены данных в передаваемом контейнере при тестировании шаблона.
     *
     * @alias hl_insert_cache_template()
     */
    function insertCacheTemplate(string $viewPath, array $extractParams = [], int $sec = Cache::DEFAULT_TIME, array $config = []): void
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
        Template::insertCache($viewPath, $extractParams, $sec, $config);
    }
}

if (!function_exists('hl_insert_cache_template')) {
    /**
     * Allows you to save the template to the cache, for example
     * hl_insert_cache_template('template', ['param' => 'value'], sec:60)
     * will save the template to the cache for 1 minute.
     *
     * Позволяет сохранить шаблон в кеш, например
     * hl_insert_cache_template('template', ['param' => 'value'], sec:60)
     * сохранит в кеш шаблон на 1 минуту.
     *
     * @see insertCacheTemplate() - current version of the function.
     *                            - актуальная версия функции.
     */
    function hl_insert_cache_template(string $viewPath, array $extractParams = [], int $sec = Cache::DEFAULT_TIME, array $config = []): void
    {
        Template::insertCache($viewPath, $extractParams, $sec, $config);
    }
}

if (!function_exists('url')) {
    /**
     * Returns the standardized relative address (URI) from the route name in the URL.
     * For example, for a route: Route::get('/test/{var1}/{var2?}', ...)->name('example');
     * you need to pass the following data to the function url('example', ['var1' => 3000, 'var2' => 'pro'], true);
     * to get this string value '/test/3000/pro'. This will set the trailing slash
     * depending on project settings.
     * Returns error if the parameters did not match the route (there is no such name, the route does not support
     * the specified method, the replacement parts of the route did not fit) or an error in obtaining routes.
     *
     * Возвращает стандартизированный относительный адрес (URI) из названия маршрута в URL.
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
     *
     * @alias hl_url()
     */
    function url(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): string
    {
        return Router::url($routeName, $replacements, $endPart, $method);
    }
}

if (!function_exists('hl_url')) {
    /**
     * Returns the standardized relative address (URI) from the route name in the URL.
     *
     * Возвращает стандартизированный относительный адрес (URI) из названия маршрута в URL.
     *
     * @see url() - current version of the function.
     *            - актуальная версия функции.
     */
    function hl_url(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): string
    {
        return Router::url($routeName, $replacements, $endPart, $method);
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
     *
     * @alias hl_address()
     */
    function address(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): string
    {
        return Router::address($routeName, $replacements, $endPart, $method);
    }
}

if (!function_exists('hl_address')) {
    /**
     * Returns the full URL given the route name and current domain. For example `https://site.com/test/3000/pro`.
     *
     * Возвращает полный URL-адрес по имени маршрута и текущего домена. Например `https://site.com/test/3000/pro`.
     *
     * @see address() - current version of the function.
     *                - актуальная версия функции.
     */
    function hl_address(string $routeName, array $replacements = [], bool $endPart = true, string $method = 'get'): string
    {
        return Router::address($routeName, $replacements, $endPart, $method);
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
     *
     * @alias hl_print_r2()
     */
    function print_r2(mixed $data, ?string $name = null): void
    {
        Debug::send($data, $name);
    }
}

if (!function_exists('hl_print_r2')) {
    /**
     * Data output to the debug panel.
     *
     * Вывод данных в панель отладки.
     *
     * @see print_r2() - alias with short name.
     */
    function hl_print_r2(mixed $data, ?string $name = null): void
    {
        Debug::send($data, $name);
    }
}

if (!function_exists('var_dump2')) {
    /**
     * Improved var_dump() output.
     *
     * Улучшенный вывод var_dump().
     *
     * @alias hl_var_dump2()
     */
    function var_dump2(mixed $value, mixed ...$values): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
            echo '<pre>' . PHP_EOL;
            \var_dump($value, ...$values);
            echo PHP_EOL . '</pre>';
            return;
        }
        \var_dump($value, ...$values);
    }
}

if (!function_exists('hl_var_dump2')) {
    /**
     * Improved var_dump() output.
     *
     * Улучшенный вывод var_dump().
     *
     * @see var_dump2() - alias with short name.
     */
    function hl_var_dump2(mixed $value, mixed ...$values): void
    {
        var_dump2($value, ...$values);
    }
}

if (!function_exists('dump')) {
    /**
     * Improved formatted output of var_dump().
     *
     * Улучшенный форматированный вывод var_dump().
     *
     * @alias hl_dump()
     */
    function dump(mixed $value, mixed ...$values): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {

            echo PHP_EOL, \core_formatting_debug_info($value, ...$values), PHP_EOL;
        } else {
            \var_dump($value, ...$values);
        }
    }
}

if (!function_exists('hl_dump')) {
    /**
     * Improved formatted output of var_dump().
     *
     * Улучшенный форматированный вывод var_dump().
     *
     * @see dump() - alias with short name.
     */
    function hl_dump(mixed $value, mixed ...$values): void
    {
        dump($value, ...$values);
    }
}

if (!function_exists('dd')) {
    /**
     * Improved formatted output of var_dump() with script termination.
     *
     * Улучшенный форматированный вывод var_dump() c завершением работы скрипта.
     *
     * @throws AsyncExitException - in asynchronous mode.
     *                            - в асинхронном режиме.
     * @alias hl_dd()
     */
    #[NoReturn]
    function dd(mixed $value, mixed ...$values): never
    {
        \dump($value, ...$values);
        /** @see async_exit() - для асинхронных запросов. */
        \async_exit();
    }
}

if (!function_exists('hl_dd')) {
    /**
     * Improved formatted output of var_dump() with script termination.
     *
     * Улучшенный форматированный вывод var_dump() c завершением работы скрипта.
     *
     * @throws AsyncExitException - in asynchronous mode.
     *                            - в асинхронном режиме.
     *
     * @see dd() - alias with short name.
     */
    #[NoReturn]
    function hl_dd(mixed $value, mixed ...$values): never
    {
        \dd($value, ...$values);
    }
}

if (!function_exists('route_name')) {
    /**
     * Returns the name of the current route, or null if none has been assigned.
     *
     * Возвращает название текущего маршрута или null если оно не назначено.
     *
     * @alias hl_route_name()
     */
    function route_name(): null|string
    {
        return Router::name();
    }
}

if (!function_exists('hl_route_name')) {
    /**
     * Returns the name of the current route, or null if none has been assigned.
     *
     * Возвращает название текущего маршрута или null если оно не назначено.
     *
     * @see route_name() - alias with short name.
     */
    function hl_route_name(): null|string
    {
        return Router::name();
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
     * @alias hl_param()
     */
    function param(string $name): DataType
    {
        return Request::param($name);
    }
}

if (!function_exists('hl_param')) {
    /**
     * Returns an object with dynamic query data by parameter name
     * with a choice of value format.
     *
     * Возвращает объект с данными динамического запроса по имени параметра
     * с возможностью выбора формата значения.
     *
     * @see Request::param() - more about return parameters.
     *                       - подробнее о возвращаемых параметрах.
     *
     * @see param() - alias with short name.
     */
    function hl_param(string $name): DataType
    {
        return Request::param($name);
    }
}

if (!function_exists('setting')) {
    /**
     * Since custom values are recommended to be added to /config/main.php,
     * a separate function is provided for frequent use of this configuration.
     * Returns the value of the parameter by key from the 'main' settings group.
     *
     * Ввиду того, что пользовательские значения рекомендовано добавлять в /config/main.php,
     * то для частого использования этой конфигурации предусмотрена отдельная функция.
     * Возвращает значение параметра по ключу из группы настроек 'main'.
     *
     * @alias hl_setting()
     */
    function setting(string $key): mixed
    {
        return Settings::getParam('main', $key);
    }
}

if (!function_exists('hl_setting')) {
    /**
     * Since custom values are recommended to be added to /config/main.php,
     * a separate function is provided for frequent use of this configuration.
     * Returns the value of the parameter by key from the 'main' settings group.
     *
     * Ввиду того, что пользовательские значения рекомендовано добавлять в /config/main.php,
     * то для частого использования этой конфигурации предусмотрена отдельная функция.
     * Возвращает значение параметра по ключу из группы настроек 'main'.
     *
     * @see setting() - alias with short name.
     */
    function hl_setting(string $key): mixed
    {
        return Settings::getParam('main', $key);
    }
}

if (!function_exists('config')) {
    /**
     * Getting any value from the framework configuration
     * by configuration type and value name.
     *
     * Получение любого значения из конфигурации фреймворка
     * по типу конфигурации и названию значения.
     *
     * @alias hl_config()
     */
    function config(string $name, string $key): mixed
    {
        return Settings::getParam($name, $key);
    }
}

if (!function_exists('hl_config')) {
    /**
     * Getting any value from the framework configuration
     * by configuration type and value name.
     *
     * Получение любого значения из конфигурации фреймворка
     * по типу конфигурации и названию значения.
     *
     * @see config() - alias with short name.
     */
    function hl_config(string $name, string $key): mixed
    {
        return Settings::getParam($name, $key);
    }
}

if (!function_exists('get_config_or_fail')) {
    /**
     * A wrapper for receiving settings parameters. If not present or equal to null, throws an error.
     *
     * Обёртка для получения параметров настроек. При отсутствии или равном null выбрасывает ошибку.
     *
     * @throws InvalidArgumentException
     * @see config()
     * @alias hl_get_config_or_fail()
     */
    function get_config_or_fail(string $name, string $key): mixed
    {
        return config($name, $key) ?? throw new InvalidArgumentException("Failed to get `{$key}` parameter from `{$name}` configuration");
    }
}

if (!function_exists('hl_get_config_or_fail')) {
    /**
     * A wrapper for receiving settings parameters. If not present or equal to null, throws an error.
     *
     * Обёртка для получения параметров настроек. При отсутствии или равном null выбрасывает ошибку.
     *
     * @throws InvalidArgumentException
     * @see config()
     * @see get_config_or_fail() - alias with short name.
     */
    function hl_get_config_or_fail(string $name, string $key): mixed
    {
        return get_config_or_fail($name, $key);
    }
}

if (!function_exists('hl_redirect')) {
    /**
     * Replacing the internal redirect for normal and asynchronous requests.
     *
     * Замена внутреннего редиректа для обычных и асинхронных запросов.
     *
     * @param string $location - redirect target, full or relative URL.
     *                         - цель редиректа, полный или относительный URL.
     *
     * @param int $status - response code of the current HTTP request for the redirect.
     *                    - код ответа текущего HTTP-запроса для редиректа.
     */
    #[NoReturn]
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
     * Returns the current host (and port if passed in the URL).
     *
     * Возвращает текущий хост (и порт, если он передан в URL).
     *
     * @alias hl_request_host()
     */
    function request_host(): string
    {
        return Request::getUri()->getHost();
    }
}

if (!function_exists('hl_request_host')) {
    /**
     * Returns the current host (and port if passed in the URL).
     *
     * Возвращает текущий хост (и порт, если он передан в URL).
     *
     * @see request_host() - alias with short name.
     */
    function hl_request_host(): string
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
     * request_uri()->getQuery();
     *
     * @alias hl_request_path()
     */
    function request_path(): string
    {
        return Request::getUri()->getPath();
    }
}

if (!function_exists('hl_request_path')) {
    /**
     * Returns the current request path from a URL
     * with no parameters.
     * Parameters can be obtained as:
     *
     * Возвращает текущий путь запроса из URL
     * без параметров.
     * Параметры можно получить как:
     *
     * hl_request_uri()->getQuery();
     *
     * @see request_path() - alias with short name.
     */
    function hl_request_path(): string
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
     * request_uri()->getQuery();
     *
     * @alias hl_request_address()
     */
    function request_address(): string
    {
        return Request::getAddress();
    }
}

if (!function_exists('hl_request_address')) {
    /**
     * Returns the current request URL without parameters.
     * Parameters can be obtained as:
     *
     * Возвращает текущий URL запроса без параметров.
     * Параметры можно получить как:
     *
     * hl_request_uri()->getQuery();
     *
     * @see request_address() - alias with short name.
     */
    function hl_request_address(): string
    {
        return Request::getAddress();
    }
}

if (!function_exists('logger')) {
    /**
     * Logging according to the established levels. logger()->error('Message', []);
     *
     * Логирование по установленным уровням. logger()->error('Message', []);
     *
     * @alias hl_logger()
     */
    function logger(): LogInterface
    {
        return new LoggerWrapper();
    }
}

if (!function_exists('hl_logger')) {
    /**
     * Logging according to the established levels. hl_logger()->error('Message', []);
     *
     * Логирование по установленным уровням. hl_logger()->error('Message', []);
     *
     * @see logger() - alias with short name.
     */
    function hl_logger(): LogInterface
    {
        return new LoggerWrapper();
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

if (!function_exists('hl_relative_path')) {
    /**
     * Converts the full path to relative to the project's root directory.
     * The result can be used in notifications given to the user.
     * For example:
     *
     * Преобразует полный путь в относительный по отношению к корневой директории проекта.
     * Результат можно использовать в отдаваемых пользователю оповещениях.
     * Например:
     *
     * '/home/user/projects/hleb/public/index.php' -> '@/public/index.php'
     *
     * @see PathInfoDoc::special()
     */
    function hl_relative_path(string $path): string
    {
        return Path::relative($path);
    }
}

if (!function_exists('hl_create_directory')) {
    /**
     * Recursively creates a directory according to the file path.
     *
     * Создаёт рекурсивно директорию для файлового пути.
     *
     * @see PathInfoDoc::special()
     */
    function hl_create_directory(string $path, int $permissions = 0775): bool
    {
        return Path::createDirectory($path, $permissions);
    }
}

if (!function_exists('is_empty')) {
    /**
     * Checking for empty value, more selective than empty().
     * There will be an error when passing an undeclared variable,
     * so in an analogy with the original function, you can suppress
     * this error by adding an '@' before the function.
     *
     * Проверка на пустое значение, более избирательная, чем empty().
     * При передаче не объявленной переменной будет ошибка, поэтому для аналогии
     * с оригинальной функцией можно подавить эту ошибку добавлением '@'
     * перед функцией.
     *
     * ```php
     * unset($var);
     *
     * if (@is_empty($var) || @is_empty($var[1])) {
     *     // Code if the variable is empty.
     * }
     *
     * ```
     */
    function is_empty(mixed $value): bool
    {
        return $value === null || $value === [] || $value === '' || $value === false;
    }
}

if (!function_exists('hl_is_empty')) {
    /**
     * @internal
     * @see is_empty() - current version of the function.
     *             - актуальная версия функции.
     */
    function hl_is_empty(mixed $value): bool
    {
        return is_empty($value);
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

if (!function_exists('hl_once')) {
    /**
     * The hl_once() function allows you to execute a piece of code only once for one request,
     * and when accessed again, it returns the same result.
     *
     * Функция hl_once() позволяет выполнять часть кода только единожды для одного запроса,
     * а при повторном обращении возвращает прежний результат.
     *
     * @see once() - current version of the function.
     *             - актуальная версия функции.
     */
    function hl_once(callable $func): mixed
    {
        return Once::get($func);
    }
}


if (!function_exists('preview')) {
    /**
     * Allows you to display the current request data in the route text field.
     * Also, automatically adds the appropriate Content-Type header.
     * For example:
     *
     * Позволяет вывести в текстовом поле маршрута текущие данные запроса.
     * Также автоматически добавляет соответствующий заголовок Content-Type.
     * Например:
     *
     * ```php
     *
     * Route::any(
     *     '/address/{name}',
     *     preview('Current route {{route}}, request parameter {%name%}, request method {{method}}')
     * );
     * ```
     */
    function preview(string $value): string
    {
        return Functions::PREVIEW_TAG . $value;
    }
}
