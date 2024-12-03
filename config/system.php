<?php
/*
 * Framework system settings. It is assumed that these settings are the same
 * for all environments and are changed only when the project is first set up.
 * If you don't know the exact application of these settings,
 * it's best to leave them at the default.
 *
 * Системные настройки фреймворка.
 * Предполагается, что эти настройки одинаковы для всех окружений и изменяются
 * только при стартовой настройке проекта. Если вы не знаете точного применения
 * этих настроек, то лучше оставить их по умолчанию.
 */

return [
    /*
    │-----------------------------------------------------------------------------
    │ Abbreviated paths to project folders
    │-----------------------------------------------------------------------------
    │
    │ Here you can set a label (for example 'logs') for an existing project folder,
    │ which can then be obtained as $this->settings()->getPath('logs') with
    │ inheritance from Hleb\Base\Container. It also allows you to continue
    │ paths like $this->settings()->getPath('@logs/file.log').
    │ It is recommended to name labels, if possible, with the same name as the
    │ folder.
    │ Important! Specifying a directory does not designate it,
    │ but specifies a path to match.
    │ Already existing system directory definitions:
    │
    │ 'global' => ./ (project root folder)
    │ 'public' => (public directory, originally ./public)
    │ 'vendor' => (project libraries, originally ./vendor)
    │ 'modules' => ./modules (modules folder)
    │ 'app' => ./app
    │ 'storage' => ./storage
    │ 'routes' => ./routes
    │ 'resources' => ./resources
    │ 'views' => ./resources/views
    │
    │ Initially: specifying the path to the `logs` folder (used in RotateLogs).
    │
    │
    │-----------------------------------------------------------------------------
    │ Сокращённые пути к папкам проекта
    │-----------------------------------------------------------------------------
    │
    │ Здесь можно задать метку (на примере 'logs') для существующей папки проекта,
    │ которую затем можно получить как $this->settings()->getPath('logs') при
    │ наследовании от Hleb\Base\Container. Также она позволяет получить продолжение
    │ пути, как $this->settings()->getPath('@logs/file.log').
    │ Рекомендуется давать названия меткам, по возможности, одноименные с папкой.
    │ Важно! Указание директории не назначает её, а указывает соответствие пути.
    │ Уже имеющиеся системные определения директорий:
    │
    │ 'global'    => ./ (корневая папка проекта)
    │ 'public'    => (публичная директория, изначально ./public)
    │ 'vendor'    => (библиотеки проекта, изначально ./vendor)
    │ 'modules'   => ./modules (папка с модулями)
    │ 'app'       => ./app
    │ 'storage'   => ./storage
    │ 'routes'    => ./routes
    │ 'resources' => ./resources
    │ 'views'     => ./resources/views
    │
    │ Изначально: указание пути к папке logs (используется в RotateLogs).
    │
    */
    'project.paths' => [
        'logs' => '/storage/logs',
        // ,,, //
    ],

    /*
    │-----------------------------------------------------------------------------
    │ Built-in automatic class loading
    │-----------------------------------------------------------------------------
    │
    │ When classes are called, they are loaded by built-in means, as well as
    │ selected auto-class loader, it is assumed to be provided
    │ Composer (file /vendor/autoload.php).
    │
    │ If the Composer autoloader is used, it will take precedence over
    │ built-in, but new classes like PSR-0, not yet added to Composer,
    │ will be found automatically, this is convenient during development, but
    │ when using in production you need to restart classmap generation in Composer.
    │
    │ Disable if using another way.
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Встроенная автоматическая загрузка классов
    │-----------------------------------------------------------------------------
    │
    │ При вызове классов происходит их загрузка встроенными средствами, а также
    │ выбранным авто-загрузчиком классов, предполагается, что он предоставлен
    │ Composer (файл /vendor/autoload.php).
    │
    │ Если используется авто-загрузчик Composer, то он будет преимущественным перед
    │ встроенным, однако новые классы вида PSR-0, еще не добавленные в Composer,
    │ будут найдены автоматически, это удобно при разработке, но при использовании
    │ в production необходимо перезапустить генерацию карты классов в Composer.
    │
    │ Отключить, если используется другой способ загрузки файлов.
    │ Изначально: true
    │
    */
    'classes.autoload' => true,

    /*
    │-----------------------------------------------------------------------------
    │ Enable original PSR-7 Request in settings
    │-----------------------------------------------------------------------------
    │
    │ If the Request object is used when initializing the framework,
    │ then it will be available in the settings as Setting::getInitialRequest().
    │ Default: false
    │
    │
    │-----------------------------------------------------------------------------
    │ Сделать доступным оригинальный PSR-7 Request в настройках
    │-----------------------------------------------------------------------------
    │
    │ Если при инициализации фреймворка использован объект Request,
    │ то он будет доступен в настройках как Setting::getInitialRequest().
    │
    │ Изначально: false
    │
    */
    'origin.request' => false,

    /*
    │-----------------------------------------------------------------------------
    │ Completing URLs when generating them
    │-----------------------------------------------------------------------------
    │
    │ URLs may have or not have a slash at the end. This option allows setting
    │ the presence of a (true) end as compulsory, otherwise the valid ending will
    │ be without "/".
    │ This option only affects URL generation by special framework functions.
    │ The value must match the server's handling of trailing slashes.
    │ (false - attempt to auto-detect, 0 - without slash, 1 - with slash)
    │ Default: 0
    │
    │
    │-----------------------------------------------------------------------------
    │ Завершение URL-адресов при их генерации
    │-----------------------------------------------------------------------------
    │
    │ Адреса URL могут иметь или не иметь слеш в конце. Данная опция позволяет
    │ установить обязательным наличие (1) окончания, в противном случае (0)
    │ валидное окончание будет без "/".
    │ Эта опция влияет также на генерацию URL специальными функциями фреймворка.
    │ Значение должно соответствовать обработке конечных слешей на сервере.
    │ (false - отключено, 0 - без слеша, 1 - со слешем)
    │ Изначально: 0
    │
    */
    'ending.slash.url' => 0,

    /*
    │-----------------------------------------------------------------------------
    │ Restricts ending.url settings to specified HTTP methods only
    │-----------------------------------------------------------------------------
    │
    │ The mandatory presence or absence of a trailing slash can create problems
    │ when requesting methods other than GET. Therefore, by default, the setting
    │ applies only to this method.
    │ Default: ['get']
    │
    │
    │-----------------------------------------------------------------------------
    │ Ограничивает действие ending.url только на указанные HTTP-методы
    │-----------------------------------------------------------------------------
    │
    │ Обязательное наличие или отсутствие конечного слеша может создать проблемы
    │ при запросе к методам, отличным от GET. Поэтому по умолчанию настройка
    │ распространяется только на этот метод.
    │ Изначально: ['get']
    │
    */
    'ending.url.methods' => ['get'],

    /*
    │-----------------------------------------------------------------------------
    │ URL validity
    │-----------------------------------------------------------------------------
    │
    │ Regular expression that restricts characters when generating a URL.
    │ When a symbol is located outside the specified range, redirection
    │ to the main page occurs.
    │
    │ Latin and Cyrillic characters in lowercase, as well as the `@` sign:
    │ "/^[a-z0-9а-яё\_\-\/\.\@]+$/u"
    │
    │ Latin and Cyrillic characters in any case:
    │ "/^[a-z0-9а-яё\_\-\/\.]+$/ui"
    │
    │ Latin and Cyrillic lowercase characters:
    │ "/^[a-z0-9а-яё\_\-\/\.]+$/u"
    │
    │ Latin lowercase characters without numbers, underscores and hyphens:
    │ "/^[a-z\/\.]+$/"
    │
    │ Default: false (Not defined, must be configured by the server tools)
    │
    │
    │-----------------------------------------------------------------------------
    │ Валидность URL
    │-----------------------------------------------------------------------------
    │
    │ Регулярное выражение, ограничивающее символы при генерации URL.
    │ При нахождении символа вне указанного диапазона происходит редирект
    │ на главную страницу.
    │
    │ Латинские и кириллические символы в нижнем регистре, а также знак `@`:
    │ "/^[a-z0-9а-яё\_\-\/\.\@]+$/u"
    │
    │ Латинские и кириллические символы в любом регистре:
    │ "/^[a-z0-9а-яё\_\-\/\.]+$/ui"
    │
    │ Латинские и кириллические символы в нижнем регистре:
    │ "/^[a-z0-9а-яё\_\-\/\.]+$/u"
    │
    │ Латинские символы в нижнем регистре без цифр, нижнего подчеркивания и дефиса:
    │ "/^[a-z\/\.]+$/"
    │
    │ Изначально: false (Не определено, должно быть настроено серверными средствами)
    │
    */
    'url.validation' => false,

    /*
    │-----------------------------------------------------------------------------
    │ The name of the session cookies.
    │-----------------------------------------------------------------------------
    │
    │ Care must be taken when changing this setting, as previously
    │ established sessions may become inactive.
    │ Default: 'PHPSESSID'
    │
    │
    │-----------------------------------------------------------------------------
    │ Название сессионной Cookie.
    │-----------------------------------------------------------------------------
    │
    │ Необходима осторожность при изменении этого параметра,
    │ так как прежде установленные сессии могут стать неактивными.
    │ Изначально: 'PHPSESSID'
    │
    */
    'session.name' => 'PHPSESSID',

    /*
    │-----------------------------------------------------------------------------
    │ Maximum lifetime of a user session cookie
    │-----------------------------------------------------------------------------
    │
    │ The duration of session Cookies in seconds after the last use.
    │ Default: 0 (standard session until browser closes)
    │
    │
    │-----------------------------------------------------------------------------
    │ Максимальное время жизни пользовательской сессионной Cookie
    │-----------------------------------------------------------------------------
    │
    │ Время действия сессионной Cookies в секундах после последнего использования.
    │ Изначально: 0 (стандартная сессия до закрытия браузера)
    │
    */
    'max.session.lifetime' => 0,

    /*
    │-----------------------------------------------------------------------------
    │ Allowed list of route files
    │-----------------------------------------------------------------------------
    │
    │ Sometimes it may be necessary to control included route files,
    │ such files may not be visible in the version control system, but still
    │ affect the route map. This way you can specify an allowed list of such files,
    │  beyond which route files will not be included.
    │ In an exceptional case, if you need to add middleware for a third-party
    │ library, you can insert it 'main.php' into the main file, wrap it in a group,
    │ and specify other third-party route files for libraries in this list.
    │ Default: [] (Empty array (file tracking disabled))
    │
    │
    │-----------------------------------------------------------------------------
    │ Разрешённый список файлов маршрутов
    │-----------------------------------------------------------------------------
    │
    │ Иногда может быть необходимо контролировать подключаемые файлы маршрутов,
    │ такие файлы могут быть не видны в системе контроля версий, но всё равно
    │ воздействовать на карту маршрутов. Поэтому можно задать белый список таких
    │ файлов, вне которого файлы маршрутов не будут подключены.
    │ В исключительном случае, при необходимости добавить middleware для сторонней
    │ библиотеки, можно вставить её 'main.php' в основной файл, обернув в группу,
    │ а другие сторонние файлы маршрутов для библиотек указать в этом списке.
    │ Изначально: [] (Пустой массив (слежение за маршрутами отключено))
    │
    */
    'allowed.route.paths' => [
        // '/routes/map.php',
        // '/routes/demo-updater-option/main.php',
        // ... //
    ],

    /*
    │-----------------------------------------------------------------------------
    │ Allowed list for types of pluggable partitions
    │-----------------------------------------------------------------------------
    │
    │ To control the configuration of WEB sections (see phphleb/adminpan),
    │ their files may not be visible in the version control system, but still
    │ be displayed in the browser. Therefore, you can set a white list
    │ of such files, outside of which the names of panel types
    │ (in the directory /config/structure/) will not be connected.
    │ Default: [] (Empty array (partition tracking disabled))
    │
    │
    │-----------------------------------------------------------------------------
    │ Разрешённый список для типов подключаемых разделов
    │-----------------------------------------------------------------------------
    │
    │ Для контроля подключаемых конфигураций WEB-разделов (см. phphleb/adminpan),
    │ их файлы могут быть не видны в системе контроля версий, но всё равно
    │ отображаться в браузере. Поэтому можно задать белый список таких файлов,
    │ вне которого названия типов панелей (в директории /config/structure/)
    │ не будут подключены.
    │ Изначально: [] (Пустой массив (слежение за разделами отключено))
    │
    */
    'allowed.structure.parts' => [
        // 'adminpan',
        // 'hlogin',
        // ... //
    ],

    /*
    │-----------------------------------------------------------------------------
    │ Visibility for phphleb/adminpan library pages
    │-----------------------------------------------------------------------------
    │
    │ If there are library pages, it controls external access to them by checking
    │ the presence of the phphleb/hlogin library and administrator rights.
    │ Disabled visibility (false) means access only to administrators or
    │ complete blocking of pages if the hlogin library is not active.
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Видимость для страниц библиотеки phphleb/adminpan
    │-----------------------------------------------------------------------------
    │
    │ При наличии страниц библиотеки осуществляет контроль внешнего доступа к ним,
    │ проверяя наличие библиотеки phphleb/hlogin и права администратора.
    │ Отключенная видимость (false) означает доступ только администраторам или
    │ полную блокировку страниц, если библиотека hlogin не активна.
    │ Изначально: true
    │
    */
    'page.external.access' => true,

    /*
    │-----------------------------------------------------------------------------
    │ Using events
    │-----------------------------------------------------------------------------
    │
    │ If events will not be used, they can be disabled in this setting
    │ and the /app/Bootstrap/Events/ folder can be deleted.
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Использование событий
    │-----------------------------------------------------------------------------
    │
    │ Если события не будут использоваться, то их можно отключить в этом параметре
    │ и удалить папку /app/Bootstrap/Events/.
    │ Изначально: true
    │
    */
    'events.used' => true,

    /*
    │-----------------------------------------------------------------------------
    │ Name of the folder with project modules
    │-----------------------------------------------------------------------------
    │
    │ Setting the name of the folder with project modules.
    │ Change this value with caution if modules have already been created.
    │ The constant HLEB_MODULES_DIR (full path) overrides this setting.
    │ Default: 'modules'
    │
    │
    │-----------------------------------------------------------------------------
    │ Название папки с модулями проекта
    │-----------------------------------------------------------------------------
    │
    │ Установка названия папки с модулями проекта.
    │ Необходимо с осторожностью изменять это значение, если модули уже созданы.
    │ Константа HLEB_MODULES_DIR (полный путь к папке) переписывает эту настройку.
    │ Изначально: 'modules'
    │
    */
    'module.dir.name' => 'modules',


    /*
    │-----------------------------------------------------------------------------
    │ Adding a file with your own functions
    │-----------------------------------------------------------------------------
    │
    │ Sometimes you need to add your own functions that will be used after
    │ the container and autoloader are initialized. You can list function files
    │ in this array.
    │ Default: []
    │
    │
    │-----------------------------------------------------------------------------
    │ Добавление файла с собственными функциями
    │-----------------------------------------------------------------------------
    │
    │ Иногда необходимо добавить собственные функции, которые будут использованы
    │ после инициализации контейнера и автозагрузки. Вы можете перечислить файлы
    │ с функциями в этом массиве.
    │ Изначально: []
    │
    */
    'custom.function.files' => [
        // '/functions.php',
    ],

    /*
    │-----------------------------------------------------------------------------
    │ Additional files with parameters
    │-----------------------------------------------------------------------------
    │
    │ List of configuration names and corresponding files.
    │ The settings will be loaded similarly to other framework settings and will
    │ be available by the specified name.
    │ For the name 'custom', the value of the 'param' setting can be obtained from
    │ the function config('custom', 'param').
    │ Default: []
    │
    │
    │-----------------------------------------------------------------------------
    │ Дополнительные файлы с параметрами
    │-----------------------------------------------------------------------------
    │
    │ Перечень названий конфигурации и соответствующих им файлов.
    │ Настройки будут загружены аналогично другим настройкам фреймворка и будут
    │ доступны по указанному названию.
    │ Для названия 'custom' значение настройки 'param' может быть получено из
    │ функции config('custom', 'param').
    │ Изначально: []
    │
    */
    'custom.setting.files' => [
        // 'custom' => '/config/custom.php',
    ],
];
