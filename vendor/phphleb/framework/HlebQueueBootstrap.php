<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb;

use ErrorException;
use Hleb\Base\Task;
use Hleb\Main\Logger\LoggerInterface;
use Exception;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;

/**
 * Framework loader for executing individual commands from a queue.
 * Installed similarly to the console.php file.
 * Can be executed by a worker in various modes specified in the constructor,
 * since commands may have restrictive attributes.
 * Can be used in asynchronous mode.
 *
 * Загрузчик фреймворка для выполнения отдельных команд из очереди.
 * Инсталлируется аналогично файлу console.php
 * Может выполнятся воркером в различных указанных в конструкторе режимах,
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
     * @throws Exception
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

        parent::__construct($publicPath, $config, $logger);
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
     * @throws ErrorException
     */
    #[\Override]
    public function load(?string $commandClass = null, array $arguments = []): int
    {
        \date_default_timezone_set($this->config['common']['timezone']);

        if (!$commandClass) {
            throw new ErrorException('The command must be specified.');
        }
        if (!\is_a($commandClass, Task::class, true)) {
            throw new ErrorException('The command class must be inherited from ' . Task::class);
        }

        $status = true;

        try {
            $command = new $commandClass();

            $status = $command->call($arguments, strictVerbosity: $this->verbosity);

            $this->result = $command->getResult();

        } catch (\AsyncExitException $e) {
            echo $e->getMessage();
        }

        HlebAsyncBootstrap::prepareAsyncRequestData();

        return (int)($status != 0);
    }
}
