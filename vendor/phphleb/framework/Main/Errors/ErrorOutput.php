<?php

declare(strict_types=1);

/*
 * Global collection and output of errors.
 * Глобальный сбор и вывод ошибок.
 */

namespace Hleb\Main\Errors;

use Hleb\Main\Insert\BaseSingleton;

final class ErrorOutput extends BaseSingleton
{
    protected static $messages = [];

    protected static $firstType = true;

    private const ERROR_CONTENT = "<div style='color:%s; margin: 5px; font-family: \"PT Sans\", \"Arial\", serif!important; box-shadow: 6px 3px 2px #f0f0f0; padding: 10px 15px; border: 2px solid %s; background-color: %s;'><h4><span style='font-size: 20px'>&#8855;</span> %s</h4></div>";

    // Display the collected messages and exit the script.
    // Вывод собранных сообщений и выход из скрипта.
    /** @internal  */
    public static function run() {
        $errors = self::$messages;
        $content = '';
        if (count(self::$messages) > 0) {
            foreach ($errors as $key => $value) {
                if (HLEB_PROJECT_DEBUG_ON) $value = str_replace('~', '<br><br>', $value);
                if ($key == 0 && self::$firstType) {
                    $content .= self::firstContent($value);
                } else {
                    $content .= self::content($value);
                }
            }
            if (HLEB_PROJECT_DEBUG) {
                // End of script execution before starting the main project.
                hl_preliminary_exit($content);
            }
        }
    }

    // Add a messages to the queue.
    // Добавление сообщений в очередь.
    /**
     * @param string|array $messages
     * @internal
     */
    public static function add($messages) {
        if (is_string($messages)) $messages = [$messages];
        if (!headers_sent()) {
            http_response_code (500);
        }
        foreach ($messages as $key => $message) {
            if (isset($message)) {
                self::$messages[] = $message;
                hleb_system_log(" " . explode('~', $message)[0]);
                // End of script execution before starting the main project.
                if (!HLEB_PROJECT_DEBUG) hl_preliminary_exit();
            } else {
                self::$messages[] = 'ErrorOutput:: Unidentified error.';
                hleb_system_log(' ' . explode('~', $message)[0] . PHP_EOL);
            }
        }
    }

    // Simultaneous display of the message with the exit from the script.
    // Одновременный вывод сообщения с выходом из скрипта.
    /** @internal  */
    public static function get($message, $first_type = true) {
        self::$firstType = $first_type;
        self::add($message);
        self::run();
    }

    // Output the standard message.
    // Вывод стандартного сообщения.
    private static function content(string $message) {

        return sprintf(self::ERROR_CONTENT, '#c17840','#f5f8c9', '#f5f8c9', $message);
    }

    // Display the main message.
    // Вывод основного сообщения.
    private static function firstContent(string $message) {
        return sprintf(self::ERROR_CONTENT, '#d24116','#f28454', 'seashell', $message);
    }

}

