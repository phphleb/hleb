<?php
/**
 * Functions required from the start of preloading the framework.
 *
 * Функции, необходимые с начала предварительной загрузки фреймворка.
 */

namespace {
    if (!function_exists('get_env')) {
        /**
         * Returns the environment variable by name,
         * or $default if it is not set.
         *
         * Возвращает переменную окружения по имени или $default,
         * если она не установлена.
         */
        function get_env(string $name, #[SensitiveParameter] mixed $default): string|int|float|array|bool
        {
            $env = $_ENV[$name] ?? getenv($name);

            if ($env === false) {
                return $default;
            }
            if (is_numeric($env)) {
                return (int)$env == $env ? (int)$env : (float)$env;
            }
            return match ($env) {
                'true', 'TRUE' => true,
                'false', 'FALSE' => false,
                'null', 'NULL' => null,
                default => $env,
            };
        }
    }

    if (!function_exists('_e')) {
        /**
         * Converter of characters to corresponding HTML entities.        *
         * Often used to escape HTML output to protect against
         * XSS attacks.
         * The type of value processed may vary.
         *
         * Конвертер символов в соответствующие HTML-сущности.
         * Часто используется для экранирования вывода HTML
         * для защиты от XSS-атак.
         * Тип обрабатываемого значения может быть разным.
         */
        function _e(#[SensitiveParameter] mixed $value): string
        {
            return htmlentities((string)$value, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('get_constant')) {
        /**
         * Returns the value of a constant;
         * if the constant does not exist, then returns $default.
         *
         * Возвращает значение константы.
         * Если константа не существует, то возвращает $default.
         */
        function get_constant(string $name, mixed $default = null): mixed
        {
            return defined($name) ? constant($name) : $default;
        }
    }
}
