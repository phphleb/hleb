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
         * The type is determined automatically.
         * If you need to cast to a specific type,
         * use env(), env_bool(), env_array() or env_int().
         *
         * Возвращает переменную окружения по имени или $default,
         * если она не установлена.
         * Определение типа производится автоматически.
         * Если необходимо приведение к конкретному типу,
         * используйте env(), env_bool(), env_array() или env_int().
         *
         *  | value               | default     | result               |
         *  |---------------------|-------------|----------------------|
         *  | 'example string'    | 'default'   | 'example string'     |
         *  | '', not defined     | 'default'   | 'default'            |
         *  | '0'                 | 'default'   | 0                    |
         *  | '1.23'              | 'default'   | 1.23                 |
         *  | '1'                 | 'default'   | 1                    |
         *  | '123'               | 'default'   | 123                  |
         *  | 'true', 'TRUE'      | 'default'   | true                 |
         *  | 'false', 'FALSE'    | 'default'   | false                |
         *  | 'null', 'NULL'      | 'default'   | null                 |
         */
        function get_env(string $name, #[SensitiveParameter] mixed $default): string|int|float|bool|null
        {
            $env = $_ENV[$name] ?? getenv($name);

            if ($env === false || $env === '') {
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

    if (!function_exists('hl_get_env')) {
        /**
         * @see get_env()
         */
        function hl_get_env(string $name, #[SensitiveParameter] mixed $default): string|int|float|bool|null
        {
            return get_env($name, $default);
        }
    }

    if (!function_exists('env')) {
        /**
         * Returns the original value of the environment variable, or $default if it is not found.
         *
         * Возвращает исходное значение переменной окружения или $default, если она не найдена.
         *
         *   | value               | default     | result               |
         *   |---------------------|-------------|----------------------|
         *   | 'example string'    | 'default'   | 'example string'     |
         *   | '', not defined     | 'default'   | 'default'            |
         *   | '0'                 | 'default'   | '0'                  |
         *   | '1.23'              | 'default'   | '1.23'               |
         *   | '1'                 | 'default'   | '1'                  |
         *   | '123'               | 'default'   | '123'                |
         */
        function env(string $name, #[SensitiveParameter] string $default): string
        {
            $env = $_ENV[$name] ?? getenv($name);

            return  $env === false || $env === '' ? $default : (string)$env;
        }
    }

    if (!function_exists('hl_env')) {
        /**
         * @see env()
         */
        function hl_env(string $name, #[SensitiveParameter] string $default): string
        {
            return env($name, $default);
        }
    }

    if (!function_exists('env_bool')) {
        /**
         * Converts to boolean and returns the environment variable
         * by name or $default if it is not set.
         *
         * Преобразует в boolean и возвращает переменную окружения
         * по имени или $default, если она не установлена.
         *
         *   | value               | default     | result    |
         *   |---------------------|-------------|-----------|
         *   | 'example string'    | false       | true      |
         *   | 'example string'    | true        | true      |
         *   | '', not defined     | false       | false     |
         *   | '', not defined     | true        | true      |
         *   | '0'                 | false       | false     |
         *   | '0'                 | true        | false     |
         *   | '1'                 | false       | true      |
         *   | '1'                 | true        | true      |
         *   | '-1'                | false       | true      |
         *   | '-1'                | true        | true      |
         *   | '123'               | false       | true      |
         *   | '123'               | true        | true      |
         *   | 'true', 'TRUE'      | false       | true      |
         *   | 'true', 'TRUE'      | true        | true      |
         *   | 'false', 'FALSE'    | false       | false     |
         *   | 'false', 'FALSE'    | true        | false     |
         */
        function env_bool(string $name, #[SensitiveParameter] bool $default): bool
        {
            $env = $_ENV[$name] ?? getenv($name);
            return match ($env) {
                'true', 'TRUE', '1' => true,
                'false', 'FALSE', '0' => false,
                false, '' => $default,
                default => (bool)$env,
            };
        }
    }

    if (!function_exists('hl_env_bool')) {
        /**
         * @see env_bool()
         */
        function hl_env_bool(string $name, #[SensitiveParameter] bool $default): bool
        {
            return env_bool($name, $default);
        }
    }

    if (!function_exists('env_int')) {
        /**
         * Converts to integer and returns the environment variable
         * by name or $default if it is not set.
         *
         * Преобразует в целое число и возвращает переменную окружения
         * по имени или $default, если она не установлена.
         *
         *    | value               | default     | result  |
         *    |---------------------|-------------|---------|
         *    | 'example string'    | 42          | error   |
         *    | '', not defined     | 42          | 42      |
         *    | '0'                 | 42          | 0       |
         *    | '1.23'              | 42          | 1       |
         *    | '1'                 | 42          | 1       |
         *    | '123'               | 42          | 123     |
         */
        function env_int(string $name, #[SensitiveParameter] int $default): int
        {
            $env = $_ENV[$name] ?? getenv($name);
            $env = match ($env) {
                'true', 'TRUE', '1' => 1,
                'false', 'FALSE', '0' => 0,
                default => $env,
            };
            if ($env && !is_numeric($env)) {
                throw new RuntimeException("The value of the environment variable `{$name}` is expected to be an integer!");
            }
            return $env === false || $env === '' ? $default : (int)$env;
        }
    }

    if (!function_exists('hl_env_int')) {
        /**
         * @see env_int()
         */
        function hl_env_int(string $name, #[SensitiveParameter] int $default): int
        {
            return env_int($name, $default);
        }
    }

    if (!function_exists('env_array')) {
        /**
         * Converts to an array from a JSON string and returns the environment
         * variable by name or $default if it is not set.
         *
         * Преобразует в массив из JSON-строки и возвращает переменную окружения
         * по имени или $default, если она не установлена.
         *
         *    | value                             | default     | result                             |
         *    |-----------------------------------|-------------|------------------------------------|
         *    | '["en", "ru"]'                    | ['zh']      | ['en', 'ru']                       |
         *    | '{"lang1": "en", "lang2": "ru"}'  | ['zh']      | ['lang1' => 'en', 'lang2' => 'ru'] |
         *    | '', not defined                   | ['zh']      | ['zh']                             |
         *    | 'other string'                    | ['zh']      | error                              |
         */
        function env_array(string $name, #[SensitiveParameter] array $default): array
        {
            $env = $_ENV[$name] ?? getenv($name);
            if ($env === false || $env === '') {
                return $default;
            }
            if (str_starts_with($env, '{') && str_ends_with($env,'}')) {
                return json_decode($env, true, JSON_THROW_ON_ERROR);
            }
            throw new RuntimeException("The value of the environment variable `{$name}` is expected to be an JSON string!");
        }
    }

    if (!function_exists('hl_env_array')) {
        /**
         * @see env_array()
         */
        function hl_env_array(string $name, #[SensitiveParameter] array $default): array
        {
            return env_array($name, $default);
        }
    }

    if (!function_exists('_e')) {
        /**
         * Converter of characters to corresponding HTML entities.
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

    if (!function_exists('redefine')) {
        /**
         * Sets a global constant if it is not defined.
         *
         * Устанавливает глобальную константу, если она не определена.
         */
        function redefine(string $name, mixed $value = null): void
        {
            defined($name) or define($name, $value);
        }
    }
}
