<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb;

use App\Bootstrap\ContainerFactory;
use ErrorException;
use Hleb\Base\RollbackInterface;
use Hleb\Base\Task;
use Hleb\Constructor\Data\DebugAnalytics;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Init\ErrorLog;
use Hleb\Main\Logger\Log;
use Hleb\Main\Logger\LoggerInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Main\Logger\LogLevel;
use Throwable;

/**
 * Framework loader for executing individual commands from a queue.
 * Installed similarly to the console.php file.
 * Can be executed by a worker in various modes specified in the constructor,
 * since commands may have restrictive attributes.
 * Can be used in asynchronous mode.
 *
 * Загрузчик фреймворка для выполнения отдельных команд из очереди.
 * Инсталлируется аналогично файлу console.php
 * Может выполняться воркером в различных указанных в конструкторе режимах,
 * так как у команд могут быть ограничительные атрибуты.
 * Может использоваться в асинхронном режиме.
 *
 * ```php
 *
 * (new Hleb\HlebQueueBootstrap(__DIR__))->load(\App\Commands\DefaultTask::class, ['value']);
 *
 * ```
 */
#[Accessible] #[AvailableAsParent]
class HlebQueueBootstrap extends HlebBootstrap
{
    private static int $processNumber = 0;

    protected const SUPPORTED_MODES = [self::ASYNC_MODE, self::CONSOLE_MODE, self::STANDARD_MODE];

    protected mixed $result = null;

    protected bool $verbosity = true;

    /**
     * A constructor for executing commands from a queue by the framework.
     * If the path to the public folder is not used in the project, then it may not be specified,
     * in which case the public directory may not exist at all.
     *
     * Конструктор для выполнения команд из очереди фреймворком.
     * Если путь до публичной папки не используется в проекте, то он может быть не указан,
     * в этом случае публичной директории вообще может не существовать.
     *
     * @param string|null $publicPath - full path to the public directory of the project.
     *                                - полный путь к публичной директории проекта.
     *
     * @param array $config - an array replacing the configuration data.
     *                      - заменяющий конфигурационные данные массив.
     *
     * @param int $mode - in what mode the commands are executed.
     *                  - в каком режиме выполняются команды.
     *
     * @throws Throwable
     */
    public function __construct(
        ?string          $publicPath = null,
        array            $config = [],
        ?LoggerInterface $logger = null,
        int              $mode = self::STANDARD_MODE,
    ) {
        \defined('HLEB_IS_QUEUE') or \define('HLEB_IS_QUEUE', 'on');

        if (!\in_array($mode, self::SUPPORTED_MODES)) {
            throw new \ErrorException('Unsupported mode');
        }
        $this->mode = $mode;

        \defined('HLEB_START') or \define('HLEB_START', \microtime(true));

        // Initialization errors should be sent to the log.
        // Ошибки инициализации должны быть отправлены в лог.
        try {
            parent::__construct($publicPath, $config, $logger);
        } catch (Throwable $t) {
            $this->errorLog($t);
            throw $t;
        }
    }

    /**
     * Getting the result of the executed command as specified in the command code via setResult().
     *
     * Получение результата выполненной команды как заданное в коде команды через setResult().
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * Sets whether data should be output as content when executing a task.
     *
     * Устанавливает, нужно ли выводить данные при выполнении задачи как контент.
     */
    public function setVerbosity(bool $value): void
    {
        $this->verbosity = $value;
    }

    /**
     * Execute a command from a queue.
     * Returns the exit code of the script.
     *
     * Выполнение команды из очереди.
     * Возвращает код завершения скрипта.
     *
     * @param string|null $commandClass - class of command to be executed. The name is used here rather than the
     *                                    class object, so as not to initialize a third-party autoloader for it.
     *
     *                                  - класс команды, который должен быть выполнен. Здесь используется название, а не
     *                                    объект класса, чтобы не инициализировать для него сторонний автозагрузчик.
     *
     *
     * @param array $arguments - an array with arguments for the command.
     *                         - массив с аргументами для команды.
     *
     * @throws Throwable
     */
    #[\Override]
    public function load(?string $commandClass = null, array $arguments = []): int
    {
        self::$processNumber++;

        \date_default_timezone_set($this->config['common']['timezone']);

        if (!$commandClass) {
            throw new ErrorException('The command must be specified.');
        }
        if (!\is_a($commandClass, Task::class, true)) {
            throw new ErrorException('The command class must be inherited from ' . Task::class);
        }

        $status = true;

        try {
            $this->loadSettings();

            $command = new $commandClass();

            $status = $command->call($arguments, strictVerbosity: $this->verbosity);

            $this->result = $command->getResult();

        } catch (\AsyncExitException $e) {
            echo $e->getMessage();

        } catch (Throwable $t) {
            $this->logsPostProcessing();
            $this->errorLog($t);
            if ($this->mode !== self::ASYNC_MODE) {
                throw $t;
            }
            $status = false;

        } finally {
            $this->logsPostProcessing();
            if ($this->mode === self::ASYNC_MODE) {
                self::prepareAsyncRequestData($this->config, self::$processNumber);
            }
        }

        return (int)($status != false);
    }

    /**
     * Preparing data to use an asynchronous request.
     *
     * Подготовка данных к использованию асинхронного запроса.
     *
     * @see HlebAsyncBootstrap::prepareAsyncRequestData()
     *
     * @internal
     */
    protected static function prepareAsyncRequestData(array $config, int $processNumber): void
    {
        // If your application does not use state storage, you can disable state cleanup.
        // Если в приложении не используется хранение состояния, то можно отключить его очистку.
        if ($config['system']['async.clear.state'] ?? true) {
            foreach (\get_declared_classes() as $class) {
                \is_a($class, RollbackInterface::class, true) and $class::rollback();
            }
        }
        foreach ([ContainerFactory::class, Registrar::class, DebugAnalytics::class, ErrorLog::class] as $class) {
            \class_exists($class, false) and $class::rollback();
        }

        /*
         * Periodically clean up used memory and call GC. Will be applied to every $rate request.
         * For example, if you pass HLEB_ASYNC_RE_CLEANING=3, then after every third request.
         *
         * Периодическая очистка используемой памяти и вызов GC. Будет применено к каждому $rate запросу.
         * Например, если передать HLEB_ASYNC_RE_CLEANING=3, то после каждого третьего запроса.
         */
        $rate = (int)get_env('HLEB_ASYNC_RE_CLEANING', get_constant('HLEB_ASYNC_RE_CLEANING', self::DEFAULT_RE_CLEANING));
        if ($rate >= 0 && ($rate === 0 || $processNumber % $rate == 0)) {
            \gc_collect_cycles();
            \gc_mem_caches();
        }
        \memory_reset_peak_usage();
    }

    /**
     * Saving the error in prepared form if it occurred at the top level.
     *
     * Сохранение ошибки в подготовленном виде, если она возникла на самом верхнем уровне.
     */
    public function errorLog(Throwable $e): void
    {
        // The error may be in the error handler itself.
        // Ошибка может быть в самом обработчике ошибок.
        try {
            \class_exists(ErrorLog::class, false) or require __DIR__ . '/Init/ErrorLog.php';
            ErrorLog::log($e);
        } catch (Throwable $t) {
            \error_log((string)$e);
            \error_log((string)$t);
        }
    }

    /**
     * Simplified project assembly.
     *
     * Упрощённая сборка проекта.
     */
    protected function loadSettings(): void
    {
        $startTime = \defined('HLEB_START') ? HLEB_START : \microtime(true);
        $this->config['system']['start.unixtime'] = $startTime;
        SystemSettings::setStartTime($startTime);
        $this->logger and Log::setLogger($this->logger);
        LogLevel::setDefaultMaxLogLevel(SystemSettings::getCommonValue('max.log.level'));
        \date_default_timezone_set($this->config['common']['timezone']);
        \ini_set('display_errors', $this->config['common']['debug'] ? '1' : '0');
    }
}
