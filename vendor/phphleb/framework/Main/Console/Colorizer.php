<?php

declare(strict_types=1);

namespace Hleb\Main\Console;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Static\Settings;

/**
 * Colors the text in the console in basic colors.
 * The colors are basic and depend on the type of specific terminal.
 *
 * Раскрашивает в базовые цвета текст в консоли.
 * Цвета являются базовыми и зависят от типа конкретного терминала.
 *
 * ```php
 * echo Colorizer::green('text') . PHP_EOL;
 * // or
 * $c = new Colorizer();
 * echo $c->green('text') . PHP_EOL;
 * ```
 *
 * ```php
 * class DefaultTask extends \Hleb\Base\Task
 * {
 *     protected function run(): int
 *    {
 *       echo $this->color()->green('text') . PHP_EOL;
 *       return self::SUCCESS_CODE;
 *    }
 * }
 * ```
 *
 * @method standard
 * @method red
 * @method green
 * @method cyan
 * @method yellow
 * @method error
 * @method errorMessage
 * @method success
 * @method successMessage
 * @method blue
 */
#[Accessible]
class Colorizer
{
    /**
     * A class constructor must not be overridden.
     *
     * Конструктор класса не должен быть переопределяемым.
     */
    final public function __construct() {
    }

    /**
     *  Returns terminal text with standard color (reset color).
     *
     *  Возвращает текст для терминала с установленным стандартным цветом (сброс цвета).
     */
    public static function standard(string $text): string
    {
        return self::checkAndColorize("\e[0m", "\e[0m", $text);
    }

    /**
     *  Returns terminal text colored bright red.
     *
     *  Возвращает текст для терминала, окрашенный в ярко-красный цвет.
     */
    public static function red(string $text): string
    {
        return self::checkAndColorize("\e[31;1m", "\e[0m", $text);
    }

    /**
     *  Returns terminal text colored bright green.
     *
     *  Возвращает текст для терминала, окрашенный в ярко-зеленый цвет.
     */
    public static function green(string $text): string
    {
        return self::checkAndColorize("\e[32;1m", "\e[0m", $text);
    }

    /**
     *  Returns terminal text colored bright cyan.
     *
     *  Возвращает текст для терминала, окрашенный в ярко-голубой цвет.
     */
    public static function cyan(string $text): string
    {
        return self::checkAndColorize("\e[36;1m", "\e[0m", $text);
    }

    /**
     *  Returns terminal text colored yellow.
     *
     *  Возвращает текст для терминала, окрашенный в желтый цвет.
     */
    public static function yellow(string $text): string
    {
        return self::checkAndColorize("\e[33m", "\e[0m", $text);
    }

    /**
     *  Returns terminal text displayed in white with a bright red background.
     *
     *  Возвращает текст для терминала, отображаемый белым цветом на ярко-красном фоне.
     */
    public static function error(string $text): string
    {
        return self::checkAndColorize("\e[41;37;1m", "\e[0m", $text);
    }

    /**
     * Returns a message to the terminal, displayed in white on a bright red background.
     *
     * Возвращает сообщение для терминала, отображаемое белым цветом на ярко-красном фоне.
     */
    public static function errorMessage(string $text): string
    {
        return PHP_EOL . self::checkAndColorize("\e[41;37;1m", "\e[0m", " " . \trim($text) . " ") . PHP_EOL;
    }

    /**
     *  Returns terminal text displayed in white on a bright red background.
     *
     *  Возвращает текст для терминала, отображаемый белым цветом на зелёном фоне.
     */
    public static function success(string $text): string
    {
        return self::checkAndColorize("\e[37;42;1m", "\e[0m", $text);
    }

    /**
     * Returns a message for the terminal, displayed in white on a green background.
     *
     * Возвращает сообщение для терминала, отображаемое белым цветом на зелёном фоне.
     */
    public static function successMessage(string $text): string
    {
        return PHP_EOL . self::checkAndColorize("\e[37;42;1m", "\e[0m", " " . \trim($text) . " ") . PHP_EOL;
    }

    /**
     *  Returns terminal text colored bright blue.
     *
     *  Возвращает текст для терминала, окрашенный в ярко-синий цвет.
     */
    public static function blue(string $text): string
    {
        return self::checkAndColorize("\e[34;1m", "\e[0m", $text);
    }

    /**
     * Checks the basic color support for the terminal and returns the result.
     *
     * Проверяет базовую поддержку цвета для терминала и возвращает результат.
     */
    protected static function checkAndColorize(string $start, string $end, string $baseText): string
    {
       if (self::isColorSupported() && Settings::isCli()) {
           if (\str_ends_with($baseText, ' ')) {
               $endSpaces = '';
               if (\preg_match('/^(.*?)(\s*)$/', $baseText, $matches)) {
                   $baseText = $matches[1];
                   $endSpaces = $matches[2];
               }
               return $start . $baseText . $end . $start . $endSpaces . $end;
           }
            return $start . $baseText . $end;
       }
       return $baseText;
    }

    /**
     * Checks whether the current terminal supports color.
     *
     * Проверяет, поддерживает ли текущий терминал цвет.
     */
    protected static function isColorSupported(): bool
    {
        if (self::isWindows()) {
            if (self::isWindows10OrHigher()) {
                return true;
            }
            return false;
        } else {
            $term = getenv('TERM');
            return $term && (
                    \str_contains($term, 'xterm') ||
                    \str_contains($term, 'color') ||
                    $term === 'linux'
                );
        }
    }

    /**
     * Checks whether the operating system is Windows.
     *
     * Проверяет, является ли операционная система Windows.
     */
    private static function isWindows(): bool
    {
        return \strtoupper(\substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Checks if the operating system is Windows 10 or newer.
     *
     * Проверяет, является ли операционная система Windows 10 и более новой версии.
     */
    private static function isWindows10OrHigher(): bool|int
    {
        if (!self::isWindows()) {
            return false;
        }

        $ver = \explode('.', \php_uname('r'));
        while (\count($ver) < 3) {
            $ver[] = '0';
        }
        $ver = \implode('.', \array_slice($ver, 0, 3));

        return \version_compare($ver, '10.0.0', '>=');
    }
}
