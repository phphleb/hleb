<?php

declare(strict_types=1);

namespace Hleb\Main\Errors;

use DeterminantStaticUncreated;

class ErrorOutput
{
    use DeterminantStaticUncreated;

    protected static $messages = [];

    /**
     * @param string|array $messages
     */
    public static function add($messages)
    {
        if (is_string($messages)) $messages = [$messages];

        if (!headers_sent()) {
            header($_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
        }

        foreach ($messages as $key => $message) {

            if (isset($message)) {

                self::$messages[] = $message;

                error_log(" " . explode('~', $message)[0] . PHP_EOL);

                if (!HLEB_PROJECT_DEBUG) exit();

            } else {

                self::$messages[] = 'ErrorOutput:: Indefinite error.';

                error_log(' ' . explode('~', $message)[0] . PHP_EOL);

            }
        }
    }

    public static function run()
    {
        $errors = self::$messages;

        $content = '';

        if (count(self::$messages) > 0) {

            foreach ($errors as $key => $value) {

                if (HLEB_PROJECT_DEBUG) $value = str_replace('~', '<br><br>', $value);

                if ($key == 0) {

                    $content .= self::first_content($value);

                } else {

                    $content .= self::content($value);
                }
            }

            if (HLEB_PROJECT_DEBUG) {

                die($content);
            }

        }

    }

    public static function get($message)
    {
        self::add($message);
        self::run();
    }


    private static function content(string $message)
    {
        return "<div style='color:#c17840; margin: 5px; padding: 10px; border: 1px solid #f5f8c9; background-color: #f5f8c9;'><h4>$message</h4></div>";
    }

    private static function first_content(string $message)
    {
        return "<div style='color:#d24116; margin: 5px; padding: 10px; border: 1px solid #f28454; background-color: seashell;'><h4>$message</h4></div>";
    }

}

