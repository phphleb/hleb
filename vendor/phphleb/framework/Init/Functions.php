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
    /** @internal */
    final public const NEEDED_TAGS = ['<', '>'];

    /** @internal */
    final public const REPLACING_TAGS = ['&lt;', '&gt;'];

    /** @internal */
    public function create(): bool
    {
        if (function_exists('hl_debug')) {
            return false;
        }
        if ($files = SystemSettings::getValue('system', 'custom.function.files')) {
            foreach($files as $file) {
                require Path::get('global') . DIRECTORY_SEPARATOR . ltrim($file, '\\/');
            }
        }

        if (!function_exists('hl_relative_path')) {
            /**
             * Converts the full path to relative to the project's root directory.
             * The result can be used in notifications given to the user.
             * For example:
             *
             * Преобразует полный путь в относительный по отношению к корневой директории проекта.
             * Результат можно использовать в отдаваемых пользователю оповещениях.
             * Например:
             *
             * '/home/user/projects/hleb/public/index.php' => '@/public/index.php'
             *
             * @see PathInfoDoc::special()
             */
            function hl_relative_path(string $path): string
            {
                return Path::relative($path);
            }
        }

        if (!function_exists('hl_create_directory')) {
            /**
             * Recursively creates a directory according to the file path.
             *
             * Создаёт рекурсивно директорию для файлового пути.
             *
             * @see PathInfoDoc::special()
             */
            function hl_create_directory(string $path, int $permissions = 0775): bool
            {
                return Path::createDirectory($path, $permissions);
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
            function hl_clear_tags(mixed $value, array $neededTags = Functions::NEEDED_TAGS, array $replacingTags = Functions::REPLACING_TAGS): mixed
            {
                if (empty($value)) {
                    return $value;
                }
                $type = gettype($value);
                if ($type === 'string') {
                    return str_replace($neededTags, $replacingTags, $value);
                }
                if ($type === 'array') {
                    $preKeys = implode(\array_keys($value));
                    $clearKeys = false;
                    foreach ($neededTags as $tag) {
                        if (str_contains($preKeys, $tag)) {
                            $clearKeys = true;
                            break;
                        }
                    }
                    if ($clearKeys) {
                        $new = [];
                        foreach ($value as $key => $item) {
                            $new[hl_clear_tags($key)] = hl_clear_tags($item);
                        }
                        return $new;
                    } else {
                        foreach ($value as $key => $item) {
                            $value[$key] = hl_clear_tags($item);
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
                    json_decode($json, true, $depth, $decodeFlags);
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
                        $name = str_replace(' ', '-', ucwords(\strtolower(str_replace('_', ' ', str_replace('HTTP_', '', $name)))));
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
                    if (is_string($name)) {
                        $result = [];
                        foreach (explode(',', $header) as $item) {
                            $result[] = trim($item);
                        }
                        $headers[$name] = $result;
                    } else {
                        throw new \InvalidArgumentException('Wrong headers format.');
                    }
                }
                return array_change_key_case($headers, CASE_LOWER);
            }
        }

        if (!function_exists('hl_formatting_debug_info')) {
            /**
             * Converts debugging information to HTML
             *
             * Преобразует отладочную информацию в HTML.
             *
             * @internal
             */
            function hl_formatting_debug_info(mixed $value, mixed ...$values): string
            {
                $result = PHP_EOL . "<!-- DEBUG_INFO -->" . PHP_EOL . '<div style=\'border-bottom: 2px solid #E65F4B;\'>' . PHP_EOL;
                $processValue = function (mixed $val): string {
                    \ob_start();
                    \var_dump($val);
                    $text = (string)\ob_get_clean();
                    $converted = \htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $text = \str_replace(' ', '&nbsp;', $converted);
                    $blockStyle = 'background-color: whitesmoke!important;' .
                        'font-family: Verdana, Geneva, sans-serif;' .
                        'color: #353478!important;' .
                        'padding: 10px!important;' .
                        'margin: 0;' .
                        'font-size: 14px!important;';
                    $prepareContent = PHP_EOL . "<div style='{$blockStyle}'>";
                    $lines = \explode("\n", $text);
                    foreach ($lines as $key => $line) {
                        if ($key === 0) {
                            $prepareContent .= "<div><b>{$line}</b></div>" . PHP_EOL;
                            continue;
                        }
                        $prepareContent .= "<div>{$line}</div>" . PHP_EOL;
                    }
                    return $prepareContent . '</div>' . PHP_EOL;
                };
                $result .= $processValue($value);
                foreach ($values as $val) {
                    $result .= $processValue($val);
                }
                return $result . '</div><!-- END DEBUG_INFO -->' . PHP_EOL;
            }
        }

        require __DIR__ . '/Review/functions.php';

        return true;
    }
}
