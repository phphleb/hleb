<?php

/*declare(strict_types=1);*/

namespace Hleb\Init;

use Hleb\Init\Connectors\HlebConnector;
use Hleb\Init\Connectors\PhphlebConnector;

/**
 * @internal
 */
final class Autoloader
{
    private const LIBRARY_NAMES = ['XdORM'];

    private static string $vendorPath;

    private static string $frameworkPath;

    private static ?string $globalPath = null;

    private static bool $singleCall = true;

    /**
     * When starting the framework, you need to pass a values.
     *
     * При старте фреймворка нужно передать значения.
     *
     * @internal
     */
    public static function init(
        string $vendorPath,
        string $globalPath,
        bool   $singleCall = true,
    ): void
    {
        self::$vendorPath = $vendorPath;
        self::$globalPath = $globalPath;
        self::$frameworkPath = $vendorPath . '/phphleb/framework';
        self::$singleCall = $singleCall;
    }

    /**
     * Loads native framework classes.
     *
     * Загружает собственные классы фреймворка.
     */
    public static function makeStatic(string $class): string|false
    {
        if (isset(HlebConnector::$formattedMap[$class])) {
            return self::searchFile($class, HlebConnector::$formattedMap, self::$frameworkPath);
        }
        $element = \strstr($class, '\\', true);
        if ($element === 'Hleb') {
            if (\str_ends_with($class, 'Exception')) {
                return self::searchFile($class, HlebConnector::$exceptionMap, self::$frameworkPath);
            }
            return self::searchFile($class, HlebConnector::$map, self::$frameworkPath);
        }
        if ($element === 'Phphleb') {
            return self::searchFile($class, PhphlebConnector::$map);
        }
        if (\in_array($element, self::LIBRARY_NAMES, true)) {
            return self::searchFile($class, HlebConnector::$libraryMap);
        }
        if ($element === 'App') {
            if (\str_starts_with($class, 'App\Bootstrap')) {
                return self::searchFile($class, HlebConnector::$bootstrapMap, self::$globalPath);
            }
            // There is already an isset check inside the method
            // Внутри метода уже есть проверка isset
            return self::searchFile($class, HlebConnector::$anyMap, self::$globalPath);
        }
        return false;
    }

    /**
     * Template method for loading arbitrary connectors.
     * A non-standard way, thanks to which you can search for a match
     * only in a specific group of classes and reduce the scope of the search.
     * Returns the path to the file, or false if there is no match.
     *
     * Шаблонный метод для загрузки произвольных коннекторов.
     * Нестандартный способ, благодаря которому можно искать соответствие
     * только в конкретной группе классов и сокращать область поиска.
     * Возвращает путь к файлу или false при отсутствии совпадения.
     */
    public static function searchFile(string $class, array &$data, ?string $path = null): string|false
    {
        if (isset($data[$class])) {
            $classSubPath = $data[$class];
            // In a real project, the class will only be needed once.
            // В реальном проекте класс потребуется только один раз.
            if (self::$singleCall) {
                unset($data[$class]);
            }
            return ($path ?? self::$vendorPath) . $classSubPath;
        }

        return false;
    }

    /**
     * Attempt to load classes by matching namespace with file path.
     * This is slow and should only be used for development
     *
     * Попытка загрузить классы по совпадению namespace с файловым путём.
     * Это медленный способ и может быть использован только для разработки.
     */
    public static function makeCustom(string $class): string|false
    {
        return CustomAutoloader::make($class, self::$globalPath, self::$vendorPath);
    }
}
