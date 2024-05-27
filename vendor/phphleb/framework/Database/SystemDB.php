<?php

declare(strict_types=1);

namespace Hleb\Database;

use Hleb\Constructor\Attributes\NotFinal;
use Hleb\Constructor\Data\DebugAnalytics;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\DatabaseException;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Main\Logger\LogLevel;
use Hleb\Static\Log;
use PDO;
use PDOStatement;

/**
 * A wrapper for working with PDO.
 *
 * Оболочка для работы с PDO.
 *
 * @see DB - Working class to use in the project.
 *         - Рабочий класс для использования в проекте.
 *
 * @internal
 */
#[NotFinal]
class SystemDB extends BaseAsyncSingleton
{
    final public const DB_PREFIX = '[#DB-LOG';

    private static array $connectionList = [];

    protected static array $defaultConnList = [];

    protected static string $dbName = 'database';

    /**
     * Creating a connection according to the configuration.
     *
     * Создание подключения согласно конфигурации.
     *
     * @internal
     */
    final public static function instance(?string $configKey): PDO
    {
        $config = self::getConfigKey($configKey);
        if (!isset(self::$connectionList[$config])) {
            self::$connectionList[$config] = self::createConnection($config);
        }
        return self::$connectionList[$config];
    }

    /**
     * Executing a prepared query with separate arguments.
     * A specific configuration key may be given.
     *
     * Выполнение подготовленного запроса с отдельными аргументами.
     * Может быть задан конкретный ключ конфигурации.
     *
     * @internal
     */
    final public static function run(
        #[\SensitiveParameter] string $sql,
        #[\SensitiveParameter] array  $args = [],
        ?string                       $config = null,
    ): false|PDOStatement
    {
        $time = \microtime(true);
        $stmt = self::instance($config)->prepare($sql);
        $stmt->execute($args);
        self::createLog($time, $sql, $config);

        return $stmt;
    }

    /**
     * Execution of a standard (not prepared) SQL query.
     * A specific configuration key may be given.
     *
     * Выполнение стандартного (не подготовленного) SQL-запроса.
     * Может быть задан конкретный ключ конфигурации.
     *
     * @internal
     */
    final public static function dbQuery(#[\SensitiveParameter] string $sql, $config = null): false|array
    {
        $time = \microtime(true);
        $stmt = self::instance($config)->query($sql);
        if (\is_bool($stmt)) {
            return $stmt;
        }
        self::createLog($time, $sql, $config);

        return $stmt->fetchAll();
    }

    /**
     * Getting a wrapper connection over PDO.
     * A specific configuration key may be given.
     *
     * Получение соединения-обёртки над PDO.
     * Может быть задан конкретный ключ конфигурации.
     *
     * @internal
     */
    final public static function getPdoInstance(?string $configKey = null): PdoManager
    {
        return new PdoManager(self::instance($configKey), self::getConfigKey($configKey));
    }

    /**
     * Obtaining a new instance of a wrapper connection over PDO with standard settings.
     * A specific configuration key may be given.
     *
     * Получение нового экземпляра соединения-обёртки над PDO со стандартными настройками.
     * Может быть задан конкретный ключ конфигурации.
     *
     * @internal
     */
    final public static function getNewStandardInstance(?string $configKey = null): PdoManager
    {
        $config = self::getConfigKey($configKey);

        return new PdoManager(self::createConnection($config, standard: true), $config);
    }

    /**
     * Returns a new PDO object configured according to the framework settings.
     * A specific configuration key may be given.
     *
     * Возвращает новый объект PDO, сконфигурированный согласно настройкам фреймворка.
     * Может быть задан конкретный ключ конфигурации.
     *
     * @internal
     */
    final public static function getNewPdoInstance(?string $configKey = null): PDO
    {
        return self::createConnection(self::getConfigKey($configKey));
    }

    /**
     * Escaping a value.
     *
     * Экранирование значения.
     *
     * @internal
     */
    final public static function quote(#[\SensitiveParameter] string $value, int $type = PDO::PARAM_STR, ?string $config = null): string
    {
        return self::instance($config)->quote($value, $type);
    }

    /**
     * Getting the current or given configuration in a standardized form.
     * A specific configuration key may be given.
     *
     * Получение текущей или заданной конфигурации в стандартизированном виде.
     * Может быть задан конкретный ключ конфигурации.
     *
     * @internal
     */
    final public static function getConfig(?string $configKey = null): ?array
    {
        $config = self::getConfigParams($configKey);
        if ($config === null) {
            return null;
        }
        foreach ($config as $key => $value) {
            if (\is_numeric($key)) {
                foreach (['dbname', 'charset', 'port', 'host'] as $name) {
                    self::updateConfigData($name, $value, $config);
                }
            }
        }

        return $config;
    }

    /**
     * Saving data to the log and debug information.
     *
     * Сохранение данных в лог и отладочную информацию.
     *
     * @internal
     */
    final public static function createLog(
        $time,
        #[\SensitiveParameter] string $sql,
        mixed $config,
        string $status = 'exec',
        bool $previously = false,
        ?string $driver = null,
    ): void {
        $time = \microtime(true) - $time;
        $time = $time > 0 ? (float)\number_format($time, 5, ".", "") : 0;

        if (DynamicParams::isDebug()) {
            self::setAnalyticsLogData(
                $sql,
                $time,
                self::getConfigKey($config),
                $driver ?? self::getPdoInstance()->getAttribute(PDO::ATTR_DRIVER_NAME),
                $status,
                $previously,
            );
        }
        if (SystemSettings::getValue('main', 'db.log.enabled')) {
            Log::log(
                LogLevel::STATE,
                self::DB_PREFIX . ' ' . $time . ' sec] (' . $status . ') ' . \rtrim($sql, ' ;') . ';',
                [
                    'db.name' => self::getConfigKey($config),
                    'db.driver' => $driver ?? self::getPdoInstance()->getAttribute(PDO::ATTR_DRIVER_NAME),
                    \Hleb\Main\Logger\Log::B7E_NAME => \Hleb\Main\Logger\Log::DB_B7E,
                ],
            );
        }
        if (SystemSettings::getCommonValue('log.db.excess') > 0) {
            DbExcessLog::set($time);
        }
    }

    /**
     * Saving data to the log and debug information.
     *
     * Сохранение данных в лог и отладочную информацию.
     *
     * @internal
     */
    final public static function createCustomLog(
        #[\SensitiveParameter] string $sql,
        float $microtime,
        array $params = [],
        ?string $dbname = null,
        ?string $driver = null,
    ): void {
        $microtime = $microtime > 0 ? (float)\number_format($microtime, 5, ".", "") : 0;

        if (DynamicParams::isDebug()) {
            self::setAnalyticsLogData($sql, $microtime, $dbname, $driver, 'exec', false);
        }

        if (SystemSettings::getValue('main', 'db.log.enabled')) {
            Log::log(
                LogLevel::STATE,
                self::DB_PREFIX . ' ' . $microtime . ' sec] (exec) ' . \rtrim($sql, ' ;') . ';',
                array_merge([
                    'db.name' => $dbname,
                    'db.driver' => $driver,
                    \Hleb\Main\Logger\Log::B7E_NAME => \Hleb\Main\Logger\Log::DB_B7E,
                ], $params),
            );
        }
        if (SystemSettings::getCommonValue('log.db.excess') > 0) {
            DbExcessLog::set($microtime);
        }
    }

    /**
     * @inheritDoc
     *
     * @internal
     */
    #[\Override]
    final public static function rollback(): void
    {
        foreach (self::$connectionList as $key => $connection) {
            /** @var PDOStatement $connection */
            self::$connectionList[$key] = null;
            unset(self::$connectionList[$key]);
        }
        self::$connectionList = [];

        foreach (self::$defaultConnList as $key => $connection) {
            /** @var PDOStatement $connection */
            self::$defaultConnList[$key] = null;
            unset(self::$defaultConnList[$key]);
        }
        self::$defaultConnList = [];
    }

    /**
     * Returns the actual connection configuration key.
     *
     * Возвращает актуальный ключ конфигурации подключения.
     *
     * @internal
     */
    final protected static function getConfigKey(#[\SensitiveParameter] ?string $config): string
    {
        return \is_string($config) ? $config : SystemSettings::getValue(self::$dbName, 'base.db.type');
    }

    /**
     * Converting the original configuration settings to a named array by value.
     *
     * Преобразование исходных параметров конфигурации в именованный массив по значениям.
     */
    private static function updateConfigData(
        #[\SensitiveParameter] string $name,
        #[\SensitiveParameter] string $value,
        #[\SensitiveParameter] array  &$config): void
    {
        if (!isset($config[$name]) && \str_contains($value, $name . '=')) {
            $params = \explode(';', $value);
            foreach ($params as $param) {
                $parts = \explode('=', $param);
                if ($parts[0] === $name) {
                    $config[$name] = $parts[1] ?? null;
                }
            }
        }
    }

    /**
     * Create a connection as a PDO object.
     * A specific configuration key may be given.
     *
     * Создание подключения в виде объекта PDO.
     * Может быть задан конкретный ключ конфигурации.
     *
     * @internal
     */
    private static function createConnection(?string $config = null, $standard = false): PDO
    {
        try {
            $param = self::getConfigParams($config) ?? [];
            $opt = $param['options'] ?? [];
            if ($standard) {
                $opt[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
                $opt[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
            } else {
                $opt[PDO::ATTR_ERRMODE] = $opt[PDO::ATTR_ERRMODE] ?? PDO::ERRMODE_EXCEPTION;
                $opt[PDO::ATTR_DEFAULT_FETCH_MODE] = $opt[PDO::ATTR_DEFAULT_FETCH_MODE] ?? PDO::FETCH_ASSOC;
            }
            $opt[PDO::ATTR_EMULATE_PREPARES] = $opt[PDO::ATTR_EMULATE_PREPARES] ?? false;

            $user = $param["user"] ?? '';
            $pass = $param["pass"] ?? $param["password"] ?? '';
            $condition = [];

            foreach ($param as $key => $prm) {
                \is_numeric($key) and $condition[] = \preg_replace('/\s+/', '', $prm);
            }
            return new PDO(\implode(";", $condition), $user, $pass, $opt);
        } catch (\PDOException $e) {
            throw new DatabaseException((string)$e);
        }
    }

    /**
     * Getting the initial connection parameters from the framework configuration.
     * A specific configuration key may be given.
     *
     * Получение исходных параметров подключения из конфигурации фреймворка.
     * Может быть задан конкретный ключ конфигурации.
     */
    private static function getConfigParams(?string $configKey = null): ?array
    {
        $params = SystemSettings::getValue(self::$dbName, 'db.settings.list');
        $config = $params[self::getConfigKey($configKey)] ?? null;
        if ($config === null) {
            return null;
        }
        return $config;
    }

    /**
     * Отправка данных в аналитику.
     */
    private static function setAnalyticsLogData(string $sql, float $time, string $dbname, string $driver, string $status, bool $previously): void
    {
        DebugAnalytics::addData(
            DebugAnalytics::DB_DEBUG,
            [
                'sql' => $sql,
                'time' => $time,
                'dbname' => $dbname,
                'type' => $driver,
                'stat' => $status,
                'previously' => (int)$previously,
            ]
        );
    }
  }
