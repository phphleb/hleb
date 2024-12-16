<?php

if (file_exists(__DIR__ . '/common-local.php')) { return (require __DIR__ . '/common-local.php');}

/*
 * This file contains settings common to the entire project (including modules).
 *
 * В этом файле находятся общие для всего проекта настройки (включая модули).
 */

return [
    /*
    │-----------------------------------------------------------------------------
    │ Project Debug Mode
    │-----------------------------------------------------------------------------
    │
    │ Allows you to enable (true) / disable (false) debug mode.
    │ “False” value is compulsory on a public server!
    │ To hide the debug mode, add the '_debug=off' parameter to the url.
    │ Default: false
    │
    │
    │-----------------------------------------------------------------------------
    │ Режим отладки проекта
    │-----------------------------------------------------------------------------
    │
    │ Позволяет включить (true) / выключить (false) режим отладки.
    │ На публичном сервере обязательно значение false!
    │ Чтобы скрыть режим отладки для отдельного url необходимо добавить к нему
    │ GET-параметр '_debug=off'.
    │ Изначально: true
    │
    */
    'debug' => env_bool('APP_DEBUG', true),

    /*
    │-----------------------------------------------------------------------------
    │ Transfer of the error log to the specified file
    │-----------------------------------------------------------------------------
    │
    │ Logs with php errors and exceptions when creating routes are directed into
    │ the folder '/storage/logs/'. Files will be numbered by calendar numbers.
    │ When you disable a debug version, this is a convenient way to track errors.
    │ The method is supported in the file logger.
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Вывод лога ошибок в указанный файл
    │-----------------------------------------------------------------------------
    │
    │ Направление логов с ошибками php и исключениями при создании маршрутов в
    │ папку /storage/logs/*. Файлы будут пронумерованы по календарным числам.
    │ При отключении debug-версии это удобный способ отслеживания ошибок.
    │ Способ поддерживается в стандартном файловом сохранении.
    │ Изначально: true
    │
    */
    'log.enabled' => env_bool('APP_LOG_ENABLED', true),

    /*
    │-----------------------------------------------------------------------------
    │ The maximum value of the logging level
    │-----------------------------------------------------------------------------
    │
    │ Allows you to determine the upper allowed logging knowledge from the list
    │ in ascending order: emergency, alert, critical, error, warning, notice,
    │ info, debug
    │ Default: info
    │
    │
    │-----------------------------------------------------------------------------
    │ Максимальное значение уровня логирования
    │-----------------------------------------------------------------------------
    │
    │ Позволяет определить верхнее допустимое значение логирования из списка
    │ по возрастанию: emergency, alert, critical, error, warning, notice,
    │ info, debug
    │ Изначально: info
    │
    */
    'max.log.level' => env('APP_LOG_LEVEL','info'),

    /*
    │-----------------------------------------------------------------------------
    │ Maximum logging level for CLI mode
    │-----------------------------------------------------------------------------
    │
    │ Only for CLI mode.
    │ Allows you to determine the upper allowed logging knowledge from the list
    │ in ascending order: emergency, alert, critical, error, warning, notice,
    │ info, debug
    │ Default: info
    │
    │
    │-----------------------------------------------------------------------------
    │ Максимальное значение уровня логирования для CLI-режима
    │-----------------------------------------------------------------------------
    │
    │ Только для CLI-режима.
    │ Позволяет определить верхнее допустимое значение логирования из списка
    │ по возрастанию: emergency, alert, critical, error, warning, notice,
    │ info, debug
    │ Изначально: info
    │
    */
    'max.cli.log.level' => env('CLI_LOG_LEVEL','info'),

    /*
    │-----------------------------------------------------------------------------
    │ Changing the logging level via a console command
    │-----------------------------------------------------------------------------
    │
    │ In order not to reload the project when the logging level is changed, this
    │ can be will make it a special console command, but only if enabled
    │ this option.
    │ Command info: php console --log-level --help
    │ Default: false
    │
    │
    │-----------------------------------------------------------------------------
    │ Изменение уровня логирования посредством консольной команды
    │-----------------------------------------------------------------------------
    │
    │ Чтобы не перезагружать проект при изменении уровня логирования, это можно
    │ будет сделать специальной консольной командой, но только если включена
    │ данная опция.
    │ Информация о команде: php console --log-level --help
    │ Изначально: false
    │
    */
    'log.level.in-cli' => false,

    /*
    │-----------------------------------------------------------------------------
    │ PHP error reporting level
    │-----------------------------------------------------------------------------
    │
    │ There are many levels of errors in PHP, and it will be determined here,
    │ which of them will be displayed.
    │ Add all PHP errors to the report: 'error.reporting' => -1
    │ Disable error logging: 'error.reporting' => 0
    │ Value from php.ini: 'error.reporting' => error_reporting()
    │ Default: E_ALL
    │
    │
    │-----------------------------------------------------------------------------
    │ Уровень включения в отчёт ошибок PHP
    │-----------------------------------------------------------------------------
    │
    │ В PHP много уровней ошибок, здесь определяется какие из них будут выведены.
    │ Добавлять в отчет все ошибки PHP: 'error.reporting' => -1
    │ Выключение протоколирования ошибок: 'error.reporting' => 0
    │ Значение из php.ini: 'error.reporting' => error_reporting()
    │ Изначально: E_ALL (Все ошибки)
    │
    */
    'error.reporting' => E_ALL,

    /*
    │-----------------------------------------------------------------------------
    │ Adds the current domain to the name of the log file
    │-----------------------------------------------------------------------------
    │
    │ Allows you to divide logging into its sources, including local ones.
    │ The method is supported in the file logger.
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Добавляет в название файла логов текущий домен
    │-----------------------------------------------------------------------------
    │
    │ Позволяет разделить логирование на его источники, в том числе локальные.
    │ Способ поддерживается в стандартном файловом сохранении.
    │ Изначально: true
    │
    */
    'log.sort' => true,

    /*
    │-----------------------------------------------------------------------------
    │ Send standard logging to STDOUT
    │-----------------------------------------------------------------------------
    │
    │ Outputs logs to the specified event stream, for example '/dev/stdout'.
    │ Default: (false|string) false - not used.
    │
    │
    │-----------------------------------------------------------------------------
    │ Направить стандартное логирование в STDOUT
    │-----------------------------------------------------------------------------
    │
    │ Выводит стандартные логи в указанный поток событий, например '/dev/stdout'.
    │ Изначально: (false|string) false - не используется.
    │
    */
    'log.stream' => get_env('APP_LOG_STREAM',false),

    /*
    │-----------------------------------------------------------------------------
    │ Log output format
    │-----------------------------------------------------------------------------
    │
    │ Outputs standard logs as string ('row') or JSON ('json').
    │ Default: row
    │
    │
    │-----------------------------------------------------------------------------
    │ Формат вывода логов
    │-----------------------------------------------------------------------------
    │
    │ Выводит стандартные логи как строку ('row') или JSON ('json').
    │ Изначально: row.
    │
    */
    'log.format' => env('APP_LOG_FORMAT','row'),

    /*
    │-----------------------------------------------------------------------------
    │ Logging exceeding a specified time for the sum of requests to the database
    │-----------------------------------------------------------------------------
    │
    │ If the set time (in seconds) is exceeded within one user request for queries
    │ to the database, it sends a warning message to the log.
    │ You can track all SQL queries in a specific query only if the
    │ 'db.log.enabled' option in the framework settings is enabled.
    │ In the logs, the message can be found using the #db_total_time_exceeded tag
    │ in the text.
    │ For example, if you set it to 1 (one second), then as soon as the total time
    │ of queries to the database exceeds 1 second, a message will be saved
    │ to the log.
    │ Due to the nature of console commands, they are not subject to this rule.
    │ Default: 0 - disabled.
    │
    │
    │-----------------------------------------------------------------------------
    │ Логирование факта превышения заданного времени для суммы запросов к БД
    │-----------------------------------------------------------------------------
    │
    │ При превышении установленного времени (в секундах) в рамках одного запроса
    │ пользователя для запросов к БД отправляет в лог сообщение-предупреждение.
    │ Отследить все запросы SQL в конкретном запросе можно только при включенной
    │ опции 'db.log.enabled' настроек фреймворка.
    │ В логах сообщение можно найти по тегу #db_total_time_exceeded в тексте.
    │ Например, если установить 1, то, как только общее время запросов к БД
    │ превысит 1 секунду будет сохранено сообщение в лог. В силу специфики
    │ консольных команд, они не попадают под действие этого правила.
    │ Изначально: 0 - отключено.
    │
    */
    'log.db.excess' => 0,

    /*
    │-----------------------------------------------------------------------------
    │ Sets the default timezone used by all date/time functions in a script
    │-----------------------------------------------------------------------------
    │
    │ Setting the timezone for a project based on a standard PHP function.
    │ Overrides the `date.timezone` value in the INI file.
    │ Default: 'Europe/Moscow'
    │
    │
    │-----------------------------------------------------------------------------
    │ Устанавливает дефолтный часовой пояс для всех функций даты/времени в скрипте
    │-----------------------------------------------------------------------------
    │
    │ Установка временной зоны для проекта на основе стандартной PHP-функции.
    │ Переопределяет значение `date.timezone` в INI-файле.
    │ Изначально: 'Europe/Moscow'
    │
    */
    'timezone' => env('APP_TIMEZONE','Europe/Moscow'),

    /*
    │-----------------------------------------------------------------------------
    │ Update the route cache when they change
    │-----------------------------------------------------------------------------
    │
    │ Sets the auto-updating of the routes cache based on changes in the `routes`
    │ folder, otherwise you will need to restart the update with a special
    │ console command.
    │ If you have many routes, you can reduce the time it takes to check them
    │ on the production server by setting this parameter to `false`and updating
    │ the routes using the console command only when updating.
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Обновление кеша маршрутов при их изменении
    │-----------------------------------------------------------------------------
    │
    │ Устанавливает авто-обновление кеша маршрутов по изменениям в папке `routes`,
    │ иначе нужно будет перезапускать обновление специальной консольной командой.
    │ При большом количестве файлов маршрутов можно уменьшить время их проверки
    │ на production сервере, выставив этот параметр в `false` и обновляя данные
    │ консольной командой только при обновлении.
    │ Изначально: true
    │
    */
    'routes.auto-update' => true,

    /*
    │-----------------------------------------------------------------------------
    │ Permission to replace container objects with test objects
    │-----------------------------------------------------------------------------
    │
    │ In the framework, the contents of the container can be obtained through
    │ static methods of classes located in the Static folder.
    │ This setting allows/prohibits replacing these methods when executing
    │ a request using special tests of objects that have the same interface in
    │ the container.
    │ Default: false - prohibited (recommended)
    │
    │
    │-----------------------------------------------------------------------------
    │ Разрешение для замены объектов из контейнера на тестовые объекты
    │-----------------------------------------------------------------------------
    │
    │ Во фреймворке содержимое контейнера можно получить через статические методы
    │ классов, находящихся в папке Static.
    │ Данная настройка разрешает/запрещает подменять при выполнении запроса эти
    │ методы с помощью специальных тестов объектов, имеющих тот-же интерфейс в
    │ контейнере.
    │ Изначально: false - запрещено (рекомендовано)
    │
    */
    'container.mock.allowed' => false,

    /*
    │-----------------------------------------------------------------------------
    │ Enable/disable cache usage by the framework
    │-----------------------------------------------------------------------------
    │
    │ Globally controls caching activity in the framework; in addition to caching
    │ TWIG templates, they need to be configured separately.
    │ (!) Use this setting with caution for large amounts of cache.
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Включает/выключает использование кеша фреймворком
    │-----------------------------------------------------------------------------
    │
    │ Глобально управляет активностью кеширования во фреймворке, кроме кеширования
    │ шаблонов TWIG, их нужно настроить отдельно.
    │ (!) С осторожностью используйте эту настройку для большого количества кеша.
    │ Изначально: true
    │
    */
    'app.cache.on' => true,

    /*
    │-----------------------------------------------------------------------------
    │ TWIG template engine settings
    │-----------------------------------------------------------------------------
    │
    │ The settings for connecting the twig/twig library are similar
    │ to its official instructions:
    │ https://twig.symfony.com/doc/3.x/api.html#environment-options
    │
    │ In the local version, when using the DEBUG mode, it is recommended
    │ to completely disable caching.
    │ This is an optional parameter, remove it if TWIG will not be used.
    │
    │ P.S. To use the twig library, you must first install it.
    │
    │
    │-----------------------------------------------------------------------------
    │ Настройки механизма шаблонов TWIG
    │-----------------------------------------------------------------------------
    │
    │ Настройки подключения библиотеки twig/twig аналогичны её официальной
    │ инструкции:
    │ https://twig.symfony.com/doc/3.x/api.html#environment-options
    │
    │ В локальной версии при использовании режима DEBUG, рекомендуется полностью
    │ отключить кеширование.
    │ Это необязательный параметр, удалите его, если TWIG не будет использован.
    │
    │ P.S. Для использования библиотеки Twig её необходимо сначала установить.
    │
    */
    'twig.options' => [
        'debug' => true,
        'charset' => 'utf-8',
        'auto_reload' => true,
        'strict_variables' => false,
        'autoescape' => false,
        'optimizations' => -1,
        'cache' => true,
    ],

    /*
    │-----------------------------------------------------------------------------
    │ TWIG template caching settings
    │-----------------------------------------------------------------------------
    │
    │ If twig.options[cache] = true, then excludes the listed directories
    │ (from the project root) from the cache, otherwise it includes them.
    │ This is an optional parameter, remove it if TWIG will not be used.
    │ In the local version, when using the DEBUG mode, it is recommended
    │ to completely disable caching.
    │ Default: [] (auto-detection)
    │
    │
    │-----------------------------------------------------------------------------
    │ Настройка кеширования шаблонов TWIG
    │-----------------------------------------------------------------------------
    │
    │ Если twig.options[cache] = true, то исключает перечисленные директории
    │ (из корня проекта) из кеша, иначе - включает.
    │ Это необязательный параметр, удалите его, если TWIG не будет использован.
    │ В локальной версии при использовании режима DEBUG, рекомендуется полностью
    │ отключить кеширование.
    │ Изначально: [] (автоопределение)
    │
    */
    'twig.cache.inverted' => [
        // 'resources/views/tmpl',
        // 'modules/example/views/tmpl',
    ],

    /*
    │-----------------------------------------------------------------------------
    │ Display Request ID in request headers
    │-----------------------------------------------------------------------------
    │
    │ Shows the Request ID value in the X-Request-Id header.
    │
    │ This value may be needed when searching the logs for a specific "request-id"
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Отображать Request ID в заголовках запроса
    │-----------------------------------------------------------------------------
    │
    │ Выводит значение Request ID в заголовок X-Request-Id
    │
    │ Это значение может понадобиться при поиске в логах конкретного "request-id"
    │ Изначально: true
    │
    */
    'show.request.id' => true,

    /*
    │-----------------------------------------------------------------------------
    │ Maximum total size of log files
    │-----------------------------------------------------------------------------
    │
    │ Control over the size of the framework's file logs. Value in megabytes.
    │ If this value is exceeded, some older files will be deleted.
    │ To prevent data loss, the action will not affect logs with the current date.
    │ Default: 0 (no restrictions)
    │
    │
    │-----------------------------------------------------------------------------
    │ Максимальный общий размер файлов с логами
    │-----------------------------------------------------------------------------
    │
    │ Контроль за размерами файловых логов фреймворка. Значение в мегабайтах.
    │ При превышении этого значения будут удалены некоторые более старые файлы.
    │ Для предотвращения потерь данных, действие не затронет логи с текущей датой.
    │ Изначально: 0 (без ограничений)
    │
    */
    'max.log.size' => 0,

    /*
    │-----------------------------------------------------------------------------
    │ Maximum total size of cached files
    │-----------------------------------------------------------------------------
    │
    │ Controlling the size of the framework cache in megabytes.
    │ The option affects only the framework's template cache (not TWIG) and
    │ its built-in cache, installed through the Cache service.
    │ If the value is exceeded, a certain amount of cache will be randomly deleted.
    │ Due to the uneven distribution of data, the triggered will be approximately
    │ close to this value. 0 - no restrictions.
    │ Default: 0
    │
    │
    │-----------------------------------------------------------------------------
    │ Максимальный общий размер файлов с кешем
    │-----------------------------------------------------------------------------
    │
    │ Контроль за размерами кеша фреймворка в мегабайтах.
    │ Опция затрагивает только кеш шаблонов фреймворка (не TWIG) и его встроенный
    │ кеш, устанавливаемый через сервис Cache.
    │ При превышении значения будет рандомно удалено некоторое количество кеша.
    │ Из-за неравномерности распределения данных, срабатывание будет приблизительно
    │ близким относительно этого значения. 0 - без ограничений.
    │ Изначально: 0
    │
    */
    'max.cache.size' => 0,
];
