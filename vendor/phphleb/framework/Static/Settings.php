<?php

/*declare(strict_types=1);*/

namespace Hleb\Static;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\CoreProcessException;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Reference\SettingInterface;

#[Accessible]
final class Settings extends BaseSingleton
{
    private static SettingInterface|null $replace = null;

    /**
     * Returns a positive value if standard mode is active.
     *
     * Возвращает положительное значение если активен стандартный режим.
     */
    public static function isStandardMode(): bool
    {
        if (self::$replace) {
            return self::$replace->isStandardMode();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->isStandardMode();
    }

    /**
     * Returns a positive value if asynchronous mode is active.
     *
     * Возвращает положительное значение если активен асинхронный режим.
     */
    public static function isAsync(): bool
    {
        if (self::$replace) {
            return self::$replace->isAsync();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->isAsync();
    }

    /**
     * Returns the activity status of the console (CLI) mode in the framework.
     *
     * Возвращает статус активности консольного (CLI) режима во фреймворке.
     */
    public static function isCli(): bool
    {
        if (self::$replace) {
            return self::$replace->isCli();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->isCli();
    }

    /**
     * Returns the activity status of the framework's debug mode.
     *
     * Возвращает статус активности режима отладки фреймворка.
     */
    public static function isDebug(): bool
    {
        if (self::$replace) {
            return self::$replace->isDebug();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->isDebug();
    }

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
     * or file. For example: Settings::getRealPath('@{name}/resources')
     * or Settings::getRealPath('@{name}/favicon.ico'),
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
     * или файлу. Например: Settings::getRealPath('@{name}/resources')
     * или Settings::getRealPath('@{name}/favicon.ico'),
     * где `name` соответственно `global` и `public`.
     */
    public static function getRealPath(string $keyOrPath): false|string
    {
        if (self::$replace) {
            return self::$replace->getRealPath($keyOrPath);
        }

        return BaseContainer::instance()->get(SettingInterface::class)->getRealPath($keyOrPath);
    }

    /**
     * Similar to the getRealPath() method. The difference is that when accessing a non-existent path,
     * returns a string with the target folder of the project and the specified file in it.
     * Checking the existence of a file and the correctness of its name is not checked in this case.
     *
     * Аналогично методу getRealPath(). Отличие в том, что при обращении к несуществующему пути,
     * возвращает строку с целевой папкой проекта и указанным файлом в ней.
     * Проверка существования файла и корректность его имени в этом случае не проверяется.
     */
    public static function getPath(string $keyOrPath): false|string
    {
        if (self::$replace) {
            return self::$replace->getPath($keyOrPath);
        }

        return BaseContainer::instance()->get(SettingInterface::class)->getPath($keyOrPath);
    }

    /**
     * Returns the setting value for the trailing slash in a URL.
     *
     * Возвращает значение настройки для конечного слеша в URL-адресе.
     */
    public static function isEndingUrl(): bool
    {
        if (self::$replace) {
            return self::$replace->isEndingUrl();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->isEndingUrl();
    }

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
    public static function getParam(string $name, string $key): mixed
    {
        if (self::$replace) {
            return self::$replace->getParam($name, $key);
        }
        return BaseContainer::instance()->get(SettingInterface::class)->getParam($name, $key);
    }

    /**
     * Simplified getting a parameter from the 'common' configuration.
     *
     * Упрощённое получение параметра из 'common' конфигурации.
     *
     * @see self::getParam()
     */
    public static function common(string $key): mixed
    {
        if (self::$replace) {
            return self::$replace->common($key);
        }

        return BaseContainer::instance()->get(SettingInterface::class)->common($key);
    }

    /**
     * Simplified getting a parameter from the 'main' configuration.
     *
     * Упрощённое получение параметра из 'main' конфигурации.
     *
     * @see self::getParam()
     */
    public static function main(string $key): mixed
    {
        if (self::$replace) {
            return self::$replace->main($key);
        }

        return BaseContainer::instance()->get(SettingInterface::class)->main($key);
    }

    /**
     * Simplified getting a parameter from the 'database' configuration.
     *
     * Упрощённое получение параметра из 'database' конфигурации.
     *
     * @see self::getParam()
     */
    public static function database(string $key): mixed
    {
        if (self::$replace) {
            return self::$replace->database($key);
        }

        return BaseContainer::instance()->get(SettingInterface::class)->database($key);
    }

    /**
     * Simplified getting a parameter from the 'system' configuration.
     *
     * Упрощённое получение параметра из 'system' конфигурации.
     *
     * @see self::getParam()
     */
    public static function system(string $key): mixed
    {
        if (self::$replace) {
            return self::$replace->system($key);
        }

        return BaseContainer::instance()->get(SettingInterface::class)->system($key);
    }

    /**
     * Returns the name of the currently active module.
     *
     * Возвращает название текущего активного модуля.
     */
    public static function getModuleName(): ?string
    {
        if (self::$replace) {
            return self::$replace->getModuleName();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->getModuleName();
    }

    /**
     * Returns the default language.
     *
     * Возвращает язык по умолчанию.
     */
    public static function getDefaultLang(): string
    {
        if (self::$replace) {
            return self::$replace->getDefaultLang();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->getDefaultLang();
    }

    /**
     * Returns a list of allowed languages for the project.
     *
     * Возвращает список разрешенных языков для проекта.
     */
    public static function getAllowedLanguages(): array
    {
        if (self::$replace) {
            return self::$replace->getAllowedLanguages();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->getAllowedLanguages();
    }


    /**
     * An attempt to determine the current language,
     * otherwise returns the default from the configuration.
     *
     * Попытка определения текущего языка,
     * иначе возвращает дефолтный из конфигурации.
     */
    public static function getAutodetectLang(): string
    {
        if (self::$replace) {
            return self::$replace->getAutodetectLang();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->getAutodetectLang();
    }

    /**
     * Returns the name of the controller method (not middleware)
     * or NULL if there was no call.
     *
     * Возвращает название метода контроллера (не middleware)
     * или NULL, если вызова не было.
     */
    public static function getControllerMethodName(): ?string
    {
        if (self::$replace) {
            return self::$replace->getControllerMethodName();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->getControllerMethodName();
    }

    /**
     * Returns the original PSR-7 object with which the framework was initialized.
     * Since part of the data, in the absence of it, is taken from global server variables,
     * then the data of the object may not be as complete as that of the current one.
     *
     * Возвращает исходный объект PSR-7, с помощью которого был инициализирован фреймворк.
     * Так как часть данных, при отсутствии, берется из глобальных серверных переменных,
     * то данные объекта могут быть не настолько полными, как у текущего.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public static function getInitialRequest(): object
    {
        if (self::$replace) {
            return self::$replace->getInitialRequest();
        }

        return BaseContainer::instance()->get(SettingInterface::class)->getInitialRequest();
    }

    /**
     * @internal
     *
     * @see SettingForTest
     */
    #[ForTestOnly]
    public static function replaceWithMock(SettingInterface|null $mock): void
    {
        if (\defined('HLEB_CONTAINER_MOCK_ON') && !HLEB_CONTAINER_MOCK_ON) {
            throw new CoreProcessException('The action is prohibited in the settings.');
        }
        self::$replace = $mock;
    }
}
