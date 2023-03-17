<?php

declare(strict_types=1);

/*
 * Adding data from ORM for output to the debug panel.
 * 
 * Добавление данных из ORM для вывода в панель отладки.
 */

namespace Hleb\Main;

use Hleb\Main\Insert\BaseSingleton;

final class DataDebug extends BaseSingleton
{
    protected static $data = [];

    public static function add(string $sql, $time, string $dbname, bool $exec = false) {
        if (HLEB_PROJECT_DEBUG_ON) {
            $timeAbout = $exec ? self::time_about($sql) : '';
            self::$data[] = [$sql, $time, $dbname, $timeAbout];
        }
    }

    public static function get(): array {
        return self::$data;
    }

    public static function createHtmlPart($part, $driver = 'mysql'): string {
        return self::create_html_part($part, $driver = 'mysql');
    }

    public static function create_html_part($part, $driver = 'mysql'): string {
        $reverseQuotes = defined('HLEB_DB_DISABLE_REVERSE_QUOTES') && HLEB_DB_DISABLE_REVERSE_QUOTES === true;
        $pattern = $driver !== "mysql" || $reverseQuotes ? '/`([^`]+)`/' : '/(`[^`]+`)/';
        return preg_replace($pattern, '<span style="color: #a5432d">$1</span>', $part);
    }

    public static function createHtmlParam($param): string {
        return self::create_html_param($param);
    }

    public static function create_html_param($param): string {
        if (is_null($param)) return "";
        switch (gettype($param)) {
            case 'double':
                return "<span style='color: #4e759d'>" . strval($param) . "</span>";
                break;
            case 'integer':
                return "<span style='color: #51519d'>" . strval($param) . "</span>";
                break;
            case 'string':
            default:
                return "<span style='color: #4c8442'>" . htmlentities($param) . "</span>";
        }
    }

    private static function time_about($sql): string {
        return stripos(trim($sql), 'select') == 0 ? '&asymp;' : '';
    }
}


