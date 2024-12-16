<?php

/*declare(strict_types=1);*/

namespace Hleb\Init;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Data\DynamicParams;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Constructor\Data\MainLogLevel;
use Hleb\Init\Connectors\HlebConnector;
use Hleb\Main\Insert\BaseAsyncSingleton;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Main\Insert\OpenInstanceSingleton;
use Hleb\Main\Logger\BaseLogger;
use Hleb\Main\Logger\FileLogger;
use Hleb\Main\Logger\Log;
use Hleb\Main\Logger\LoggerInterface;
use Hleb\Main\Logger\LogLevel;
use Hleb\Main\Logger\NullLogger;
use Hleb\Main\Logger\StreamLogger;
use Hleb\Reference\LogInterface;
use Hleb\Static\Script;

/**
 * Allows logging even if the framework itself is not running.
 * The framework configuration files can be additionally loaded.
 * Serves only resources that are loaded in the loader class constructor.
 *
 * Позволяет выполнить логирование даже если сам фреймворк не работает.
 * Могут быть дополнительно загружены файлы конфигурации фреймворка.
 * Обслуживает только ресурсы, загружаемые в конструкторе класса-загрузчика.
 *
 * @internal
 */
final class ErrorLog
{
    /**
     * A third-party logging method with an interface.
     *
     * Сторонний способ логирования с интерфейсом.
     */
    protected static ?LoggerInterface $logger = null;

    private static array $config = [];

    private static array $notices = [];

    /**
     * If the logging method differs from the standard one,
     * then you can specify it here.
     *
     * Если способ логирования отличается от стандартного,
     * то можно указать его здесь.
     */
    public static function setLogger(?LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    /** @internal */
    public static function getLogger(): ?LoggerInterface
    {
        return self::$logger;
    }

    /**
     * Convert system error to standard output.
     *
     * Преобразование системной ошибки в стандартный вывод.
     */
    public static function handle(\Throwable $t): void
    {
        self::throw(self::make($t));
    }

    /**
     * Logging a system error to standard output.
     *
     * Логирование системной ошибки в стандартный вывод.
     */
    public static function log(\Throwable $throwable): void
    {
        self::make($throwable);
    }

    /**
     * Outputting errors to the logger even if some of the classes are not loaded.
     *
     * Вывод ошибок в механизм логирования даже если часть классов не загружена.
     */
    public static function execute(int $errno, string $errstr, ?string $errfile = null, ?int $errline = null): bool
    {
        try {
            self::loadBaseClasses();

            $params = [];
            if ($errfile) {
                $params['file'] = $errfile;
            }
            if ($errline) {
                $params['line'] = $errline;
            }
            $params['request-id'] = DynamicParams::getDynamicRequestId();
            $log = self::$logger ?? Log::instance();

            $debug = DynamicParams::isDebug();
            switch ($errno) {
                case E_CORE_ERROR:
                    self::outputNotice();
                    $log->critical($errstr, $params);
                    \async_exit($debug ? (SystemSettings::isCli() ?  '' : self::format($errstr)) : '', 500);
                    break;
                case E_ERROR:
                case E_USER_ERROR:
                case E_PARSE:
                case E_COMPILE_ERROR:
                case E_RECOVERABLE_ERROR:
                    self::outputNotice();
                    $log->error($errstr, $params);
                    \async_exit($debug ? (SystemSettings::isCli() ?  '' : self::format($errstr)) : '', 500);
                    break;
                case E_USER_WARNING:
                case E_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                    self::outputNotice();
                    $log->warning($errstr, $params);
                    $debug and print self::format( "Warning: $errstr in $errfile:$errline");
                    break;
                case E_USER_NOTICE:
                case E_NOTICE:
                // case E_STRICT: // deprecated in PHP 8.4
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                    $log->notice($errstr, $params);
                    $debug and self::$notices[] = self::format("Notice: $errstr in $errfile:$errline");
                    break;
                default:
                    self::outputNotice();
                    $log->error($errstr, $params);
                    \async_exit($debug ? (SystemSettings::isCli() ?  '' : self::format($errstr)) : '', 500);
                    break;
            }
            self::$config and SystemSettings::setData(self::$config);
        } catch (\Throwable $t) {
            \error_log((string)$t);
            self::$config = [];
            return false;
        }

        return true;
    }

    /**
     * @internal
     */
    public static function rollback(): void
    {
        self::$config = [];
        self::$notices = [];
    }

    /**
     * Parsing and logging errors.
     *
     * Разбор и логирование ошибки.
     */
    private static function make(\Throwable $t, int $error = E_USER_WARNING): \Throwable
    {
        try {
            self::execute($error, $t->getMessage() . ' ' . $t->getTraceAsString(), $t->getFile(), $t->getLine());
        } catch (\Throwable) {
        }
        return $t;
    }

    /**
     * Throws an error.
     *
     * Выбрасывает ошибку.
     */
    private static function throw($t): void
    {
        throw $t;
    }

    /**
     * Loading the necessary framework classes for logging to work.
     *
     * Загрузка необходимых классов фреймворка для работы логирования.
     */
    private static function loadBaseClasses(): void
    {
        try {
            $dir = \dirname(__DIR__);
            if (!\interface_exists(RollbackInterface::class, false)) {
                require_once $dir . '/Base/RollbackInterface.php';
            }
            foreach ([
                HlebConnector::class => '/Init/Connectors/HlebConnector.php',
                BaseAsyncSingleton::class => '/Main/Insert/BaseAsyncSingleton.php',
                DynamicParams::class => '/Constructor/Data/DynamicParams.php',
                ] as $name => $path) {
                if (!\class_exists($name, false)) {
                    require_once $dir . $path;
                }
            }
            foreach (HlebConnector::$exceptionMap as $excClass => $excName) {
                if (!\class_exists($dir . DIRECTORY_SEPARATOR . $excClass, false)) {
                    require_once $dir . DIRECTORY_SEPARATOR . $excName;
                }
            }
            $map = array_merge(HlebConnector::$map, HlebConnector::$formattedMap);
            $load = static function(array $classes) use ($map, $dir) {
                foreach ($classes as $class) {
                    if (!\class_exists($class, false)) {
                        require_once $dir . $map[$class];
                    }
                }
            };

            $beforeSettingsClasses = [
                BaseSingleton::class,
                OpenInstanceSingleton::class,
                LogLevel::class,
                SystemSettings::class,
                DynamicParams::class,
            ];
            $load($beforeSettingsClasses);

            self::loadSettings();

            $afterSettingsClasses = [
                LoggerInterface::class,
                LogInterface::class,
                \Hleb\Reference\Interface\Log::class,
                MainLogLevel::class,
                Script::class,
                Log::class,
                BaseLogger::class,
                NullLogger::class,
                FileLogger::class,
                StreamLogger::class,
                \Functions::class,
            ];
            $load($afterSettingsClasses);

            (new \Functions())->create();
        } catch (\Throwable $t) {
            \error_log((string)$t);
        }
    }

    /**
     * Loading settings for logging, if they have not been loaded before.
     * The method is the smallest possible loader
     * of the framework's capabilities to display an error.
     *
     * Загрузка настроек для логирования, если они не были загружены ранее.
     * Метод представляет собой минимально возможный загрузчик
     * возможностей фреймворка для вывода ошибки.
     */
    private static function loadSettings(): void
    {
        if (!\function_exists('get_env')) {
            require __DIR__ . '/../Init/Review/basic.php';
        }
        self::$config = SystemSettings::getData();
        SystemSettings::init(HLEB_LOAD_MODE);
        $config = self::getMinConfig();
        $common = $config['common'];
        isset($common['timezone']) && \date_default_timezone_set($common['timezone']);
        $debug = ($common['debug'] ?? '0') ? '1' : '0';
        \ini_set('display_errors', $debug);
        LogLevel::setDefaultMaxLogLevel($common[HLEB_CLI_MODE ? 'max.cli.log.level' : 'max.log.level']);
        SystemSettings::setData($config);
        DynamicParams::setArgv($GLOBALS['argv'] ?? []);
    }

    /**
     * Loading a minimal framework configuration or setting default options.
     *
     * Загрузка минимальной конфигурации фреймворка или установка параметров по умолчанию.
     */
    private static function getMinConfig(): array
    {
        $dir = \defined('HLEB_GLOBAL_DIR') ? HLEB_GLOBAL_DIR : \dirname(__DIR__, 4);
        $c = (static function () use ($dir): array {
            try {
                if (\file_exists($directory = $dir . '/config/common.php')) {
                    return include $directory;
                }
            } catch (\Throwable) {
            }
            return [];
        })();
        !\is_bool($c['debug'] ?? null) and $c['debug'] = false;
        isset($c['log.enabled']) or $c['log.enabled'] = true;
        isset($c['max.log.level']) or $c['max.log.level'] = 'info';
        isset($c['max.cli.log.level']) or $c['max.log.level'] = 'info';
        isset($c['log.level.in-cli']) or $c['log.level.in-cli'] = false;
        isset($c['log.stream']) or $c['log.stream'] = false;
        isset($c['log.format']) or $c['log.format'] = 'row';
        return [
            'path' => [
                'global' => $dir,
                'storage' => $dir . '/storage',
                'public' => \defined("HLEB_PUBLIC_DIR") ? HLEB_PUBLIC_DIR : $dir . '/public',
            ],
            'common' => $c,
        ];
    }

    /**
     * Notification output is close to natural output.
     *
     * Вывод notice, приближённый к естественному отображению.
     */
    private static function outputNotice(): void
    {
        if (self::$notices) {
            print \implode(PHP_EOL, self::$notices);
            self::$notices = [];
        }
    }

    /**
     * Formatting the error output.
     *
     * Форматирование выводимой ошибки.
     */
    private static function format(string $message): string
    {
        if (SystemSettings::isCli()) {
            return $message . PHP_EOL . PHP_EOL;
        }

        return PHP_EOL . "<pre>$message</pre>" . PHP_EOL;
    }
}
