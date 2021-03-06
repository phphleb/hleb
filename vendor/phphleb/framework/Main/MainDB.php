<?php

declare(strict_types=1);

/*
 * A wrapper for working with PDO.
 *
 * Оболочка для работы с PDO.
 */

namespace Hleb\Main;

final class MainDB
{
    use \DeterminantStaticUncreated;

    private static $connectionList = [];

    public static function instance($config_key) {
        if (empty(self::$connectionList) && !defined('HLEB_TYPE_DB')) {
            $configSearchDir = defined('HLEB_SEARCH_DBASE_CONFIG_FILE') ?
                HLEB_SEARCH_DBASE_CONFIG_FILE :
                HLEB_GLOBAL_DIRECTORY . '/database';

            if (file_exists($configSearchDir . "/dbase.config.php")) {
                hl_print_fulfillment_inspector($configSearchDir, "/dbase.config.php");
            } else {
                hl_print_fulfillment_inspector($configSearchDir, "/default.dbase.config.php");
            }
        }
        $config = self::setConfigKey($config_key);
        if (!isset(self::$connectionList[$config])) {
            self::$connectionList[$config] = self::init($config);
        }
        return self::$connectionList[$config];
    }

    public static function run($sql, $args = [], $config = null) {
        $time = microtime(true);
        $stmt = self::instance($config)->prepare($sql);
        $stmt->execute($args);
        if (defined('HLEB_PROJECT_DEBUG_ON') && HLEB_PROJECT_DEBUG_ON) {
            \Hleb\Main\DataDebug::add($sql, microtime(true) - $time, self::setConfigKey($config), true);
        }
        return $stmt;
    }


    public static function db_query($sql, $config = null) {
        $time = microtime(true);
        $stmt = self::instance($config)->query($sql);
        $data = $stmt->fetchAll();
        \Hleb\Main\DataDebug::add(htmlentities($sql), microtime(true) - $time, self::setConfigKey($config), true);
        return $data;
    }

    protected static function init(string $config) {
        $param = HLEB_PARAMETERS_FOR_DB[$config];

        $opt = [
            \PDO::ATTR_ERRMODE => $param["errmode"] ?? \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => $param["default_fetch_mode"] ?? \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => $param["emulate_prepares"] ?? false
        ];

        $user = $param["user"];
        $pass = $param["pass"];
        $condition = [];

        foreach ($param as $key => $prm) {
            if (is_numeric($key)) {
                $condition [] = preg_replace('/\s+/', '', $prm);
            }
        }
        $connection = implode(";", $condition);
        self::$connectionList[$config] = new \PDO($connection, $user, $pass, $opt);
        return self::$connectionList[$config];
    }

    protected static function setConfigKey($config) {
        return is_string($config) ? $config : HLEB_TYPE_DB;
    }

}


