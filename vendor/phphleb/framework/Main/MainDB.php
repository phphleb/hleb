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

    public static function instance() {
        if (is_null(self::$instance)) {
            $configSearchDir = defined('HLEB_SEARCH_DBASE_CONFIG_FILE') ?
                HLEB_SEARCH_DBASE_CONFIG_FILE :
                HLEB_GLOBAL_DIRECTORY . '/database';

            if (file_exists($configSearchDir . "/dbase.config.php")) {
                hl_print_fulfillment_inspector($configSearchDir, "/dbase.config.php");
            } else {
                hl_print_fulfillment_inspector($configSearchDir, "/default.dbase.config.php");
            }
            self::$instance = self::init();
        }
        return self::$instance;
    }

    public static function run($sql, $args = []) {
        $time = microtime(true);
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        if(defined('HLEB_PROJECT_DEBUG_ON') && HLEB_PROJECT_DEBUG_ON) {
            \Hleb\Main\DataDebug::add($sql, microtime(true) - $time, HLEB_TYPE_DB, true);
        }
        return $stmt;
    }


    public static function db_query($sql) {
        $time = microtime(true);
        $stmt = self::instance()->query($sql);
        $data = $stmt->fetchAll();
        \Hleb\Main\DataDebug::add(htmlentities($sql), microtime(true) - $time, HLEB_TYPE_DB, true);
        return $data;
    }

    protected static function init() {
        $prms = HLEB_PARAMETERS_FOR_DB[HLEB_TYPE_DB];

        $opt = [
            \PDO::ATTR_ERRMODE => $prms["errmode"] ?? \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => $prms["default_fetch_mode"] ?? \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => $prms["emulate_prepares"] ?? false
        ];

        $user = $prms["user"];
        $pass = $prms["pass"];
        $condition = [];

        foreach ($prms as $key => $prm) {
            if (is_numeric($key)) {
                $condition [] = preg_replace('/\s+/', '', $prm);
            }
        }
        $connection = implode(";", $condition);
        return new \PDO($connection, $user, $pass, $opt);
    }

}


