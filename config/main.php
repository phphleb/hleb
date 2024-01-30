<?php

if (file_exists(__DIR__ . '/main-local.php')) { return (require __DIR__ . '/main-local.php');}

/*
 * This file contains user settings that can be overridden in modules when creating
 * a similar /modules/{module_name}/config/main.php file.
 *
 * В этом файле находятся пользовательские настройки, которые можно переопределить в модулях
 * при создании аналогичного файла /modules/{module_name}/config/main.php.
 */

return [
    /*
    │-----------------------------------------------------------------------------
    │ Default language (if not defined)
    │-----------------------------------------------------------------------------
    │
    │ If the language is not defined, it is assigned from this parameter.
    │ If the current value is not supported, then another implementation-specific
    │ value will be used. Therefore, in order to avoid discrepancies,
    │ it is recommended that all implementations have one common
    │ ('en' - English or 'ru' - Russian).
    │ Default: 'en'
    │
    │
    │-----------------------------------------------------------------------------
    │ Язык по умолчанию (если не определён)
    │-----------------------------------------------------------------------------
    │
    │ В случае, если язык не определён, он назначается из этого параметра.
    │ Если текущее значение не поддерживается, то будет использовано иное из
    │ конкретной реализации. Поэтому, во избежание расхождений, рекомендуется
    │ во всех реализациях иметь один общий ('en' - английский или 'ru' - русский).
    │ Изначально: 'en'
    │
    */
    'default.lang' => 'en',

    /*
    │-----------------------------------------------------------------------------
    │ List of allowed languages
    │-----------------------------------------------------------------------------
    │
    │ Restricting the use of certain languages from the available list.
    │ This list is assumed to be present in all implementations.
    │ Default: ['en', 'ru', 'de', 'es', 'zh']
    │
    │
    │-----------------------------------------------------------------------------
    │ Список допустимых языков
    │-----------------------------------------------------------------------------
    │
    │ Ограничение на использование определённых языков из доступного списка.
    │ Подразумевается, что этот список присутствует во всех реализациях.
    │ Изначально: ['en', 'ru', 'de', 'es', 'zh']
    │
    */
    'allowed.languages' => ['en', 'ru', 'de', 'es', 'zh'],

    /*
    │-----------------------------------------------------------------------------
    │ Default session initialization
    │-----------------------------------------------------------------------------
    │
    │ If the project does not use sessions, then in this case you can not activate
    │ them when loading the framework.
    │ In this case, you can selectively (manually) use their initialization.
    │ The plain(...) method in a route overrides this parameter.
    │ Default: true
    │
    │
    │-----------------------------------------------------------------------------
    │ Инициализация сессий по умолчанию
    │-----------------------------------------------------------------------------
    │
    │ Если в проекте не используются сессии, то в этом случае можно их не
    │ активировать при загрузке фреймворка.
    │ При этом можно выборочно (вручную) использовать их инициализацию.
    │ Метод plain(...) в маршруте переопределяет этот параметр.
    │ Изначально: true
    │
    */
    'session.enabled' => true,

    /*
    │-----------------------------------------------------------------------------
    │ Logging database queries
    │-----------------------------------------------------------------------------
    │
    │ Database queries are saved according to the current logging method.
    │ Default: false
    │
    │
    │-----------------------------------------------------------------------------
    │ Выводить в логи запросы к базе данных
    │-----------------------------------------------------------------------------
    │
    │ Запросы к базам данных сохраняются согласно текущему способу логирования.
    │ Изначально: false
    │
    */
    'db.log.enabled' => false,

    /*
    │-----------------------------------------------------------------------------
    │ User session cookie parameters
    │-----------------------------------------------------------------------------
    │
    │ Setting parameters for session Cookie.
    │ The values are similar to `options` in the PHP function setcookie().
    │ ('expires', 'path', 'domain', 'secure', 'httponly', 'samesite')
    │ The 'expires' parameter takes precedence over the max.session.lifetime value.
    │ Default: [] (standard framework settings)
    │
    │
    │-----------------------------------------------------------------------------
    │ Параметры пользовательской сессионной Cookie
    │-----------------------------------------------------------------------------
    │
    │ Установка параметров для сессионной Cookie.
    │ Значения аналогично `options` в PHP функции setcookie().
    │ ('expires', 'path', 'domain', 'secure', 'httponly', 'samesite')
    │ Параметр 'expires' имеет приоритет выше, чем значение max.session.lifetime.
    │ Изначально: [] (стандартные настройки фреймворка)
    │
    */
    'session.options' => [],

    /*
     * If you need to add your own settings, you can assign them here.
     * In the future, they will be available from the setting(...) function or
     * Hleb\Static\Settings::getParam('main', ...) by setting name.
     *
     * Если необходимо добавить собственные настройки, то их можно назначить здесь.
     * В дальнейшем они будут доступны из функции setting(...) или
     * Hleb\Static\Settings::getParam('main', ...) по названию настройки.
     */

     //...//
];
