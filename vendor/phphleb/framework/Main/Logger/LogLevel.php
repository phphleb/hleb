<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Logger;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\CoreProcessException;

/**
 * Describes log levels.
 *
 * Уровни логирования.
 */
#[Accessible]
final class LogLevel
{
    // If the log should always be present.
    // Если лог должен присутствовать постоянно.
    final public const STATE = 'state';
    // By levels.
    // По уровням.
    final public const EMERGENCY = 'emergency';
    final public const ALERT = 'alert';
    final public const CRITICAL = 'critical';
    final public const ERROR = 'error';
    final public const WARNING = 'warning';
    final public const NOTICE = 'notice';
    final public const INFO = 'info';
    final public const DEBUG = 'debug';

    final public const REQUIRED = [
        0 => self::STATE,
        1 => self::EMERGENCY,
        2 => self::ALERT,
        3 => self::CRITICAL,
        4 => self::ERROR,
        5 => self::WARNING,
        6 => self::NOTICE,
        7 => self::INFO,
        8 => self::DEBUG
    ];

    final public const ALL = [
        1 => self::EMERGENCY,
        2 => self::ALERT,
        3 => self::CRITICAL,
        4 => self::ERROR,
        5 => self::WARNING,
        6 => self::NOTICE,
        7 => self::INFO,
        8 => self::DEBUG
    ];

    private static string $defaultLevel = self::WARNING;

    private static ?string $maxLogLevel = null;

    /**
     * @internal
     */
    public static function setDefaultMaxLogLevel(string $level): void
    {
        if (self::$maxLogLevel !== null) {
            return;
        }

        $level = \strtolower($level);
        if (!\in_array($level, self::REQUIRED)) {
            $list = \implode(', ', self::ALL);
            throw new CoreProcessException("Specified logging level `$level` is not supported, use: " . $list);
        }
        self::$maxLogLevel = $level;
    }

    /**
     * Returns the previously set or default logging level.
     *
     * Возвращает ранее установленный или дефолтный уровень логирования.
     *
     * @see MainLogLevel::get() - getting an active level.
     *                                - получение активного уровня.
     * @internal
     */
    public static function getDefault(): string
    {
        return self::$maxLogLevel ?? self::$defaultLevel;
    }

}
