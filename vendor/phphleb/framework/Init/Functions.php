<?php

declare(strict_types=1);

use Hleb\Constructor\Data\SystemSettings;
use Hleb\Static\Debug;
use Hleb\Static\Path;
use Hleb\Static\Settings;

/**
 * @internal
 */
final class Functions
{
    final public const PREVIEW_TAG = '~{/preview/}';

    /** @internal */
    public function create(): bool
    {
        if (\function_exists('hl_debug')) {
            return false;
        }
        if ($files = SystemSettings::getValue('system', 'custom.function.files')) {
            foreach($files as $file) {
                require Path::get('global') . DIRECTORY_SEPARATOR . \ltrim($file, '\\/');
            }
        }

        if (!function_exists('hl_clear_tags')) {
            /**
             * Recursive initial data cleaning.
             * The value type remains unchanged.
             *
             * Рекурсивная предварительная очистка данных.
             * Тип значений остаётся неизменным.
             */
            function hl_clear_tags(mixed $value): mixed
            {
                if (empty($value)) {
                    return $value;
                }
                if (\is_string($value)) {
                    return \htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                } else if (\is_array($value)) {
                    foreach ($value as $key => $item) {
                        if (\is_string($key)) {
                            unset($value[$key]);
                            $value[\hl_clear_tags($key)] = \hl_clear_tags($item);
                        } else {
                            $value[$key] = \hl_clear_tags($item);
                        }
                    }
                }
                return $value;
            }
        }

        /**
         * Allows you to use the string validation function as JSON for PHP 8.3 in PHP 8.2
         * As can be seen from the implementation - for PHP 8.2 resource consumption and speed
         * at the level of complete parsing.
         * In any case, it is recommended to use this function only if there is a very high probability
         * that invalid JSON exists in the value being checked.
         *
         * Позволяет использовать функцию по валидации строки как JSON для PHP 8.3 в PHP 8.2
         * Как видно из реализации - для PHP 8.2 ресурсоемкость и скорость на уровне полного преобразования.
         * В любом случае рекомендуется использовать эту функцию только в том случае,
         * если вероятность существования невалидного JSON в проверяемом значении очень высока.
         */
        if (!\function_exists('json_validate')) {
            function json_validate(string $json, int $depth = 512, int $flags = 0): bool
            {
                $decodeFlags = JSON_THROW_ON_ERROR;
                // Currently there is only one flag for this function.
                // На данный момент существует только один флаг у этой функции.
                if ($flags === JSON_INVALID_UTF8_IGNORE) {
                    $decodeFlags += JSON_INVALID_UTF8_IGNORE;
                }
                try {
                    \json_decode($json, true, $depth, $decodeFlags);
                } catch (\JsonException) {
                    return false;
                }
                return true;
            }
        }

        /**
         * When using Nginx instead of Apache, the function may not be defined.
         *
         * При использовании Nginx вместо Apache функция может быть не определена.
         */
        if (!\function_exists('getallheaders')) {
            function getallheaders(): array
            {
                $headers = [];
                foreach ($_SERVER as $name => $item) {
                    if ($name != 'HTTP_MOD_REWRITE' && (\str_starts_with($name, 'HTTP_') || $name == 'CONTENT_TYPE' || $name == 'CONTENT_LENGTH')) {
                        $name = \str_replace(' ', '-', \ucwords(\strtolower(\str_replace('_', ' ', \str_replace('HTTP_', '', $name)))));
                        if ($name == 'Content-Type') $name = 'Content-type';
                        $headers[$name] = $item;
                    }
                }
                return $headers;
            }
        }

        if (!function_exists('hl_check')) {
            /**
             * Allows you to set a mark in the code with performance output in the debug panel.
             * For example:
             *
             * Позволяет установить метку в коде с выводом производительности в панель отладки.
             * Например:
             *
             * hl_check('Start performance test №1');
             * // ... //
             * hl_check('End performance test №1');
             *
             * @see ReverseHlCheckMode::run() - converting a function from a comment.
             *                                - преобразование функции из комментария.
             */
            function hl_check(string $message, ?string $file = null, ?int $line = null): void
            {
                if (Settings::isCli() || !Settings::isDebug()) {
                    return;
                }
                Debug::setHlCheck($message, $file, $line);
            }
        }

        if (!function_exists('hl_convert_standard_headers')) {
            /**
             * Converts non-asynchronous request headers to standard form.
             *
             * Преобразует заголовки не асинхронного запроса в стандартный вид.
             *
             * @internal
             */
            function hl_convert_standard_headers(mixed $headers): array
            {
                if (empty($headers)) {
                    return [];
                }
                foreach ((array)$headers as $name => $header) {
                    if (\is_string($name)) {
                        $result = [];
                        foreach (\explode(',', $header) as $item) {
                            $result[] = \trim($item);
                        }
                        $headers[$name] = $result;
                    } else {
                        throw new \InvalidArgumentException('Wrong headers format.');
                    }
                }
                return \array_change_key_case($headers, CASE_LOWER);
            }
        }

        if (!function_exists('core_formatting_debug_info')) {
            /**
             * Converts debugging information to HTML.
             * A feature of this HTML is that it displays normally
             * when inline styles are disabled.
             *
             * Преобразует отладочную информацию в HTML.
             * Особенностью этого HTML является нормальное отображение
             * при отключенных встроенных стилях.
             *
             * @internal - do not use outside the framework core.
             */
            function core_formatting_debug_info(mixed $value, mixed ...$values): string
            {
                $backtrace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
                $level = $backtrace[2] ?? [];
                $function = $level['function'] ?? null;
                if ($function !== 'dd') {
                    $level = $backtrace[1] ?? [];
                    $function = 'dump';
                }
                $file = $level['file'] ?? null;
                $line = $level['line'] ?? null;
                $path = "";
                if ($file) {
                    $file = Path::relative($file);
                    if ($line) {
                        $pathStyle = 'color: #d3d3d3!important;' .
                            'background-color: #28396c!important;' .
                            'padding: 10px!important;' .
                            'font-size: 13px!important;' .
                            'font-family: Verdana, Geneva, sans-serif!important;';
                        $path = "<tr bgcolor='#28396c'><td style='{$pathStyle}'>" .
                            "<font color='#d3d3d3'>[<b>{$function}</b>] $file:$line</font></td></tr>" . PHP_EOL;
                    }
                }
                $tableStyle = 'background-color: #f9f9f910px!important; border-bottom: 3px solid #E65F4B';
                $result = PHP_EOL . "<!-- DEBUG_INFO -->" . PHP_EOL . "<font color='#353478'>" .
                    "<table width='100%' border='0' cellspacing='0' cellpadding='0' bgcolor='whitesmoke' style='$tableStyle'>" .
                    "<tbody>" . PHP_EOL;
                $result .= $path;
                $processValue = function (mixed $val): string {
                    \ob_start();
                    \var_dump($val);
                    $text = (string)\ob_get_clean();
                    $converted = \htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $text = \str_replace(' ', '&nbsp;', $converted);
                    $blockStyle = 'background-color: #f9f9f910px!important;' .
                        'font-family: Verdana, Geneva, sans-serif10px!important;' .
                        'color: #353478!important;' .
                        'padding: 0 10px!important;' .
                        'margin: 0;' .
                        'font-size: 14px!important;';
                    $prepareContent = PHP_EOL;
                    $lines = \explode("\n", $text);
                    foreach ($lines as $key => $line) {
                        if ($key === 0) {
                            $prepareContent .= "<tr bgcolor='#f9f9f9'><td>&nbsp;</td></tr>";
                            $prepareContent .= "<tr bgcolor='#f9f9f9'><td style='{$blockStyle}'><b>{$line}</b></td></tr>" . PHP_EOL;
                            continue;
                        }
                        $prepareContent .= "<tr bgcolor='#f9f9f9'><td style='{$blockStyle}'>{$line}</td></tr>" . PHP_EOL;
                    }
                    return $prepareContent . PHP_EOL;
                };
                $result .= $processValue($value);
                foreach ($values as $val) {
                    $result .= $processValue($val);
                }
                $result .= "<tr bgcolor='#f9f9f9'><td>&nbsp;</td></tr>";
                return $result . "</tbody></table></font><!-- END DEBUG_INFO -->" . PHP_EOL;
            }
        }

        if (!function_exists('array_find')) {
            /**
             * Returns the first array element that matches a condition.
             * Similar to array_find() in PHP 8.4
             *
             * Возвращает первый элемент массива, соответствующий условию.
             * Аналогично array_find() в PHP 8.4
             */
            function array_find(array $array, callable $callback): mixed
            {
                foreach ($array as $value) {
                    if ($callback($value)) {
                        return $value;
                    }
                }
                return null;
            }
        }

        if (!function_exists('array_find_key')) {
            /**
             * Returns the key of the first element of an array that matches a condition.
             * Similar to array_find_key() in PHP 8.4
             *
             * Возвращает ключ первого элемента массива, соответствующего условию.
             * Аналогично array_find_key() в PHP 8.4
             */
            function array_find_key(array $array, callable $callback): int|string|null
            {
                foreach ($array as $key => $value) {
                    if ($callback($value, $key)) {
                        return $key;
                    }
                }
                return null;
            }
        }

        if (!function_exists('array_all')) {
            /**
             * Checks whether each element of the array matches a condition.
             * Similar to array_all() in PHP 8.4
             *
             * Проверяет, соответствует ли каждый элемент массива условию.
             * Аналогично array_all() в PHP 8.4
             */
            function array_all(array $array, callable $callback): bool
            {
                foreach ($array as $value) {
                    if (!$callback($value)) {
                        return false;
                    }
                }
                return true;
            }
        }

        if (!function_exists('array_any')) {
            /**
             * Checks whether at least one array element matches a condition.
             * Similar to array_any() in PHP 8.4
             *
             * Проверяет, соответствует ли условию хотя бы один элемент массива.
             * Аналогично array_any() в PHP 8.4
             */
            function array_any(array $array, callable $callback): bool
            {
                foreach ($array as $value) {
                    if ($callback($value)) {
                        return true;
                    }
                }
                return false;
            }
        }
        require __DIR__ . '/Review/functions.php';

        return true;
    }
}
