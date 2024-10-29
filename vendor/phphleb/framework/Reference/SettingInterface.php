<?php

namespace Hleb\Reference;

/**
 * Provides methods for accessing the framework's system settings.
 * For backward compatibility with custom containers,
 * this interface can only be extended.
 *
 * Предоставляет методы для обращения к системным параметрам фреймворка.
 * Для обратной совместимостью с пользовательскими контейнерами
 * этот интерфейс может только расширяться.
 */
interface SettingInterface
{
    /**
     * Returns a positive value if standard mode is active.
     *
     * Возвращает положительное значение если активен стандартный режим.
     */
    public function isStandardMode(): bool;

    /**
     * Returns a positive value if asynchronous mode is active.
     *
     * Возвращает положительное значение если активен асинхронный режим.
     */
    public function isAsync(): bool;

    /**
     * Returns the activity status of the console (CLI) mode in the framework.
     *
     * Возвращает статус активности консольного (CLI) режима во фреймворке.
     */
    public function isCli(): bool;

    /**
     * Returns the activity status of the framework's debug mode.
     *
     * Возвращает статус активности режима отладки фреймворка.
     */
    public function isDebug(): bool;

    /**
     * Returns the full path to the specified project file or folder according to the project settings.
     * Returns `false` if the file does not exist.
     * For example:
     * '@' or 'global' - the path to the project's root directory.
     * 'public' - path to the public folder of the project.
     * 'storage' - path to the project data folder.
     * 'views' - path to folder with file templates.
     * 'modules' - path to the `modules` folder (if it exists).
     * It is also possible to supplement this request by specifying a continuation to an EXISTING folder
     * or file. For example: path('@<name>/resources') or path('@<name>/favicon.ico'),
     * where `name` is `global` and `public` respectively.
     *
     * Возвращает полный путь до указанного файла или папки проекта согласно настройкам проекта.
     * Вернёт `false` если файл не существует.
     * Например:
     * '@' или 'global' - путь до корневой директории проекта.
     * 'public' - путь до публичной папки проекта.
     * 'storage' - путь до папки с данными проекта.
     * 'views' - путь до папки с шаблонами файлов.
     * 'modules' - путь до папки с модулями (при её существовании).
     * Также можно дополнить этот запрос, указав продолжение к СУЩЕСТВУЮЩЕЙ папке
     * или файлу. Например: path('@<name>/resources') или path('@<name>/favicon.ico'),.
     * где `name` соответственно `global` и `public`.
     */
    public function getRealPath(string $keyOrPath): false|string;

    /**
     * Similar to the getRealPath() method. The difference is that when accessing a non-existent path,
     * returns a string with the target folder of the project and the specified file in it.
     * Checking the existence of a file and the correctness of its name is not checked in this case.
     *
     * Аналогично методу getRealPath(). Отличие в том, что при обращении к несуществующему пути,
     * возвращает строку с целевой папкой проекта и указанным файлом в ней.
     * Проверка существования файла и корректность его имени в этом случае не проверяется.
     */
    public function getPath(string $keyOrPath): false|string;

    /**
     * Returns the setting value for the trailing slash in a URL.
     *
     * Возвращает значение настройки для конечного слеша в URL-адресе.
     */
    public function isEndingUrl(): bool;

    /**
     * Getting any value from the framework configuration ('common', 'main', etc.)
     * by configuration name and value name.
     *
     * Получение любого значения из конфигурации фреймворка ('common', 'main' и тд)
     * по названию конфигурации и названию значения.
     *
     * @param string $name - the name of the configuration file.
     *                     - название файла конфигурации.
     *
     * @param string $key - value name.
     *                    - название значения.
     */
    public function getParam(string $name, string $key): mixed;

    /**
     * Simplified getting a parameter from the 'common' configuration.
     *
     * Упрощённое получение параметра из 'common' конфигурации.
     *
     * @see self::getParam()
     */
    public function common(string $key): mixed;

    /**
     * Simplified getting a parameter from the 'main' configuration.
     *
     * Упрощённое получение параметра из 'main' конфигурации.
     *
     * @see self::getParam()
     */
    public function main(string $key): mixed;

    /**
     * Simplified getting a parameter from the 'database' configuration.
     *
     * Упрощённое получение параметра из 'database' конфигурации.
     *
     * @see self::getParam()
     */
    public function database(string $key): mixed;

    /**
     * Simplified getting a parameter from the 'system' configuration.
     *
     * Упрощённое получение параметра из 'system' конфигурации.
     *
     * @see self::getParam()
     */
    public function system(string $key): mixed;


    /**
     * Returns the name of the currently active module.
     *
     * Возвращает название текущего активного модуля.
     */
    public function getModuleName(): ?string;

    /**
     * Returns the name of the controller method (not middleware)
     * or NULL if there was no call.
     *
     * Возвращает название метода контроллера (не middleware)
     * или NULL, если вызова не было.
     */
    public function getControllerMethodName(): ?string;

    /**
     * Returns the default language.
     *
     * Возвращает язык по умолчанию.
     */
    public function getDefaultLang(): string;

    /**
     * An attempt to determine the current language,
     * otherwise returns the default from the configuration.
     *
     * Попытка определения текущего языка,
     * иначе возвращает дефолтный из конфигурации.
     */
    public function getAutodetectLang(): string;

    /**
     * Returns a list of allowed languages for the project.
     *
     * Возвращает список разрешенных языков для проекта.
     */
    public function getAllowedLanguages(): array;

    /**
     * Returns the original PSR-7 object with which the framework was initialized.
     * Since part of the data, in the absence of it, is taken from global server variables,
     * then the data of the object may not be as complete as that of the current one.
     *
     * Возвращает исходный объект PSR-7, с помощью которого был инициализирован фреймворк.
     * Так как часть данных, при отсутствии, берется из глобальных серверных переменных,
     * то данные объекта могут быть не настолько полными, как у текущего.
     */
    public function getInitialRequest(): object;
}
