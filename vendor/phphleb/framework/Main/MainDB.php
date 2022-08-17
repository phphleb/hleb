<?php

declare(strict_types=1);

/*
 * A wrapper for working with PDO.
 *
 * Оболочка для работы с PDO.
 */

namespace Hleb\Main;

use PDO;

/**
 * @package Hleb\Main
 * @internal
 */
final class MainDB
{
    use \DeterminantStaticUncreated;

    private static $connectionList = [];

    public static function instance($configKey)
    {
        $config = self::getConfigForce($configKey);
        if (!isset(self::$connectionList[$config])) {
            self::$connectionList[$config] = self::createConnection($config);
        }
        return self::$connectionList[$config];
    }

    public static function run($sql, $args = [], $config = null)
    {
        $time = microtime(true);
        $stmt = self::instance($config)->prepare($sql);
        $stmt->execute($args);
        $time = microtime(true) - $time;
        if (defined('HLEB_PROJECT_DEBUG_ON') && HLEB_PROJECT_DEBUG_ON) {
            \Hleb\Main\DataDebug::add($sql, $time, self::setConfigKey($config), true);
        }
        if (defined('HLEB_DB_LOG_ENABLED') && HLEB_DB_LOG_ENABLED) {
            hleb_system_log('[DB LOG ' . round($time, 4) . ' sec] ' . $sql . ';');
        }

        return $stmt;
    }


    public static function dbQuery(string $sql, $config = null)
    {
        $time = microtime(true);
        $stmt = self::instance($config)->query($sql);
        if (is_bool($stmt)) {
            return $stmt;
        }
        $data = $stmt->fetchAll();
        \Hleb\Main\DataDebug::add(htmlentities($sql), microtime(true) - $time, self::setConfigKey($config), true);
        return $data;
    }

    /**
     * @param string|null $configKey
     * @return PdoManager
     */
    public static function getPdoInstance($configKey = null)
    {
        return new PdoManager(self::instance($configKey));
    }

    /**
     * @param string|null $configKey
     * @return PDO
     */
    public static function getNewPdoInstance($configKey = null)
    {
        return self::createConnection(self::getConfigForce($configKey));
    }

    /**
     * @param string|null $configKey
     * @return array|null
     */
    public static function getConfig(string $configKey = null) {
        $key = self::getConfigForce($configKey);
        $config = HLEB_PARAMETERS_FOR_DB[$key] ?? null;
        if (is_null($config)) {
            return null;
        }
        foreach($config as $key => $value) {
            if (is_numeric($key)) {
                foreach (['dbname', 'charset', 'port', 'host'] as $name) {
                    self::updateConfigData($name, $value, $config);
                }
            }
        }

        return $config;
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $config
     */
    protected static function updateConfigData(string $name, string $value, array &$config): void
    {
        if (!isset($config[$name]) && strpos($value, $name . '=') !== false) {
            $params = explode(';', $value);
            foreach ($params as $param) {
                $parts = explode('=', $param);
                if ($parts[0] === $name) {
                    $config[$name] = $parts[1] ?? null;
                }
            }

        }

    }

    protected static function setConfigKey($config)
    {
        return is_string($config) ? $config : HLEB_TYPE_DB;
    }

    protected static function createConnection(string $config = null)
    {
        $param = defined('HLEB_PARAMETERS_FOR_DB') ? HLEB_PARAMETERS_FOR_DB[$config] : [];
        $opt = $param["options-list"] ?? [];
        $opt[PDO::ATTR_ERRMODE] = $param["errmode"] ?? PDO::ERRMODE_EXCEPTION;
        $opt[PDO::ATTR_DEFAULT_FETCH_MODE] = $param["default_fetch_mode"] ?? PDO::FETCH_ASSOC;
        $opt[PDO::ATTR_EMULATE_PREPARES] = $param["emulate-prepares"] ?? $param["emulate_prepares"] ?? false;

        $user = $param["user"] ?? '';
        $pass = $param["pass"] ?? $param["password"] ?? '';
        $condition = [];

        foreach ($param as $key => $prm) {
            if (is_numeric($key)) {
                $condition [] = preg_replace('/\s+/', '', $prm);
            }
        }
        return new PDO(implode(";", $condition), $user, $pass, $opt);
    }

    private static function getConfigForce($configKey = null)
    {
        if (!defined('HLEB_TYPE_DB')) {
            $configSearchDir = defined('HLEB_SEARCH_DBASE_CONFIG_FILE') ?
                HLEB_SEARCH_DBASE_CONFIG_FILE :
                HLEB_GLOBAL_DIRECTORY . '/database';

            if (file_exists($configSearchDir . "/dbase.config.php")) {
                hleb_require($configSearchDir . "/dbase.config.php");
            } else {
                hleb_require($configSearchDir . "/default.dbase.config.php");
            }
        }
        return self::setConfigKey($configKey);
    }

}


