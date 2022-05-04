<?php


namespace Hleb\Main\Logger;


/**
 * Describes log levels.
 */
final class LogLevel
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    const ALL = [
        1 => self::EMERGENCY,
        2 => self::ALERT,
        3 => self::CRITICAL,
        4 => self::ERROR,
        5 => self::WARNING,
        6 => self::NOTICE,
        7 => self::INFO,
        8 => self::DEBUG
    ];

    private static $defaultLevel = null;

    public static function getDefault()
    {
        if (is_null(self::$defaultLevel)) {
            self::$defaultLevel = defined('HLEB_MAX_LOG_LEVEL') && in_array(strtolower(HLEB_MAX_LOG_LEVEL), self::ALL) ? strtolower(HLEB_MAX_LOG_LEVEL) : self::INFO;
        }
        return self::$defaultLevel;
    }

}


