<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Data;

use Hleb\DynamicStateException;
use Hleb\HlebBootstrap;
use Hleb\Main\Insert\BaseSingleton;

/**
 * Serves to manage the main settings inside the framework core.
 * Use the Settings class to get values.
 *
 * Служит для управления основными настройками внутри ядра фреймворка.
 * Для получения значений используйте класс Settings.
 *
 * @internal
 */
final class SystemSettings extends BaseSingleton
{
    private static ?array $data = null;

    private static ?int $mode = null;

    private static ?array $argv = null;

    /**
     * Initialize global project settings.
     * For example, class A is tested, depending on the settings:
     *
     * Инициализация глобальных настроек проекта.
     * Например, тестируется класс A, зависящий от настроек:
     *
     * ```php
     * SystemSettings::init(HlebBootstrap::STANDARD_MODE);
     *
     * class A {
     *    function example() {
     *      if (Settings::isCli()) {
     *        return 'cli';
     *      }
     *      return 'not cli';
     *    }
     * }
     *
     * echo (new A)->example(); // not cli
     * ```
     *
     *
     * @param int $mode       - framework execution mode.
     *                        - режим выполнения фреймворка.
     *
     * @internal
     */
    public static function init(int $mode = HlebBootstrap::STANDARD_MODE): void
    {
        self::$mode = $mode;
    }

    /**
     * Setting the framework configuration.
     *
     * Установка конфигурации фреймворка.
     *
     * @internal
     */
    public static function setData(array $data): void
    {
        self::$data = $data;
        if (isset($data['database'])) {
            self::$data['default.database'] = $data['database'];
        }
    }

    /** @internal  */
    public static function getData(): array
    {
        return self::$data ?? [];
    }

    /**
     * For testing settings.
     *
     * Для тестирования настроек.
     *
     * @internal
     */
    public static function setSuite(string $name, array $data): void
    {
        self::$data[$name] = $data;
    }

    /**
     * For testing settings.
     *
     * Для тестирования настроек.
     *
     * @internal
     */
    public static function setValue(string $name, string $key, null|string|float|array|int|bool $value): void
    {
        self::$data[$name][$key] = $value;
    }

    /**
     * Returns an existing setting from the configuration by type and name, eg ->getValue('common', 'debug').
     * (!) It is recommended to use the Settings class instead of this class.
     *
     * Возвращает существующую настройку из конфигурации по типу и имени, например ->getValue('common', 'debug').
     * (!) Вместо этого класса рекомендуется использовать класс Settings.
     */
    public static function getValue(string $name, string $key): null|string|array|float|int|bool
    {
        return self::$data[$name][$key] ?? null;
    }

    /**
     * @see self::getValue()
     */
    public static function getSystemValue(string $key): null|string|array|float|int|bool
    {
        return self::$data['system'][$key] ?? null;
    }

    /**
     * @see self::getValue()
     */
    public static function getMainValue(string $key): null|string|array|float|int|bool
    {
        return self::$data['main'][$key] ?? null;
    }


    /**
     * @see self::getValue()
     */
    public static function getCommonValue(string $key): null|string|array|float|int|bool
    {
        return self::$data['common'][$key] ?? null;
    }

    /** @internal */
    public static function isStandardMode(): bool
    {
        return self::$mode === HlebBootstrap::STANDARD_MODE;
    }

    /** @internal */
    public static function isAsync(): bool
    {
        return self::$mode === HlebBootstrap::ASYNC_MODE;
    }

    /** @internal */
    public static function isCli(): bool
    {
        return self::$mode === HlebBootstrap::CONSOLE_MODE;
    }

    /** @internal */
    public static function getAlias(string $keyOrPath, bool $ifExists = true): false|string
    {
        if (!\str_starts_with($keyOrPath, '@')) {
            if ($result = self::getValue('path', $keyOrPath)) {
                return $result;
            }
            throw new DynamicStateException("The `$keyOrPath` value was not found in the valid file path abbreviations.");
        }
        // Custom paths containing /../ are too unpredictable.
        // Пользовательские пути, содержащие  /../, слишком непредсказуемы.
        if (\str_contains($keyOrPath, '..')) {
            throw new DynamicStateException("You cannot use '...' in file path abbreviations: $keyOrPath");
        }
        $keyOrPath = str_replace('\\', '/', $keyOrPath);
        $dir = \strstr($keyOrPath, '/', true) ?: $keyOrPath;
        $path = \strstr($keyOrPath, '/');
        $path = $path ?: '';
        /** @see PathInfoDoc::special() */
        $path = match ($dir) {
            '@', '@global' => self::getValue('path', 'global') . $path,
            '@public' => self::getValue('path', 'public') . $path,
            '@storage' => self::getValue('path', 'storage') . $path,
            '@resources' => self::getValue('path', 'resources') . $path,
            '@app' => self::getValue('path', 'app') . $path,
            '@views' => self::getValue('path', 'views') . $path,
            '@modules' => self::getValue('path', 'modules') . $path,
            '@vendor' => self::getValue('path', 'vendor') . $path,
            '@library' => self::getValue('path', 'library') . $path,
            '@framework' => self::getValue('path', 'framework') . $path,
            default => false,
        };
        if (!$path) {
            throw new DynamicStateException("The `@$keyOrPath` value was not found in the valid file path abbreviations.");
        }
        // May not be obvious, but realpath may return false.
        // Может быть неочевидным, но realpath может вернуть false.
        return $ifExists ? \realpath($path) . (\str_ends_with($path, '/') ? DIRECTORY_SEPARATOR : '') : $path;
    }

    /** @internal */
    public static function getRealPath(string $keyOrPath): false|string
    {
        return self::getAlias($keyOrPath);
    }

    /** @internal */
    public static function getPath(string $keyOrPath): string
    {
        return (string)self::getAlias($keyOrPath, ifExists:false);
    }

    /** @internal */
    public static function getLogOn(): bool
    {
        return (bool)self::getCommonValue('log.enabled');
    }

    /** @internal */
    public static function getSortLog(): bool
    {
        return (bool)self::getCommonValue( 'log.sort');
    }

    /** @internal */
    public static function getArgv(): array
    {
        return self::$argv ?? [];
    }

    /** @internal */
    public static function updateMainSettings(array $data): void
    {
        self::$data['main'] = \array_merge(self::$data['main'] ?? [], $data);
    }

    /** @internal */
    public static function addModuleType(bool $type): void
    {
        self::$data['main']['module.view.type'] = $type ? 'closed' : 'opened';
    }

    /** @internal */
    public static function updateDatabaseSettings(array $data): void
    {
        $settings = $data['db.settings.list'] ?? [];
        $oldSettings = self::$data['database']['db.settings.list'] ?? [];
        self::$data['database'] = \array_merge(self::$data['database'] ?? [], $data);
        self::$data['database']['db.settings.list'] = \array_merge($oldSettings, $settings);
    }

    /** @internal */
    public static function setStartTime(float $time): void
    {
        self::$data['system']['start.unixtime'] = $time;
    }

}
