<?php

declare(strict_types=1);

namespace Hleb\Database;

/**
 * @internal
 */
final class StandardDB extends SystemDB
{
    protected static string $dbName = 'default.database';

    /**
     * Getting a connection with predictable settings
     * for use in project libraries.
     * Thus, if the output type or other parameters are changed
     * in the configuration using this method, you can get
     * a connection with the original minimum parameters.
     * Returns a wrapper over PDO with default settings.
     * According to these settings, options are enabled
     * ATTR_ERRMODE and FETCH_ASSOC for PDO.
     * Connect to the database from the original configuration
     * and does not respect settings
     * project modules, so should not be used in them.
     *
     * Получение подключения с предсказуемыми настройками
     * для использования в библиотеках проекта.
     * Таким образом, если тип вывода или иные параметры будут
     * изменены в конфигурации при помощи этого метода можно
     * получить соединение с исходными минимальными параметрами.
     * Возвращает обёртку над PDO с дефолтными настройками.
     * Согласно этим настройкам включены параметры
     * ATTR_ERRMODE и FETCH_ASSOC для PDO.
     * Подключение к базе данных из исходной конфигурации
     * и не учитывает настройки модулей проекта, поэтому
     * не должно в них использоваться.
     *
     * @internal
     */
    public static function getStandardPdoInstance(#[\SensitiveParameter] ?string $configKey = null): PdoManager
    {
        if (!isset(self::$defaultConnList[$configKey])) {
            $pdo = self::getNewPdoInstance($configKey);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            self::$defaultConnList[$configKey] = $pdo;
        }

        return new PdoManager(self::$defaultConnList[$configKey], self::getConfigKey($configKey));
    }
}
