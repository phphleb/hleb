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

    // Add a message to the queue.
    // Добавление сообщения в очередь.
    /**
     * @param string|array $messages
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
                self::$messages[] = 'ErrorOutput:: Indefinite error.';
                hleb_system_log(' ' . explode('~', $message)[0] . PHP_EOL);
            }
        }
    }

    // Display the collected messages and exit the script.
    // Вывод собранных сообщений и выход из скрипта.
    public static function run() {
        $errors = self::$messages;
        $content = '';
        if (count(self::$messages) > 0) {
            foreach ($errors as $key => $value) {
                if (HLEB_PROJECT_DEBUG_ON) $value = str_replace('~', '<br><br>', $value);
                if ($key == 0 && self::$firstType) {
                    $content .= self::first_content($value);
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

    // Simultaneous display of the message with the exit from the script.
    // Одновременный вывод сообщения с выходом из скрипта.
    public static function get($message, $first_type = true) {
        self::$firstType = $first_type;
        self::add($message);
        self::run();
    }

    // Output the standard message.
    // Вывод стандартного сообщения.
    private static function content(string $message) {
        return "<div style='color:#c17840; margin: 5px; padding: 10px; border: 1px solid #f5f8c9; background-color: #f5f8c9;'><h4>$message</h4></div>";
    }

    // Display the main message.
    // Вывод основного сообщения.
    private static function first_content(string $message) {
        return "<div style='color:#d24116; margin: 5px; padding: 10px; border: 1px solid #f28454; background-color: seashell;'><h4>$message</h4></div>";
    }

}

