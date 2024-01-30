<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb;

use Hleb\Constructor\Data\{DynamicParams, SystemSettings};
use Hleb\Main\{Console\ConsoleHandler, Console\WebConsole, Logger\Log, Logger\LoggerInterface, Logger\LogLevel};
use Exception;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;

#[Accessible] #[AvailableAsParent]
class HlebConsoleBootstrap extends HlebBootstrap
{
    private int $code = 0;

    private ?array $argv = null;

    /**
     * Constructor with initialization.
     * If the path to the public folder is not used in the project, then it may not be specified,
     * in which case the public directory may not exist at all.
     *
     * Конструктор с инициализацией.
     * Если путь до публичной папки не используется в проекте, то он может быть не указан,
     * в этом случае публичной директории вообще может не существовать.
     *
     * @param string|null $publicPath - full path to the public directory of the project.
     *                                - полный путь к публичной директории проекта.
     *
     * @param array $config - an array replacing the configuration data.
     *                      - заменяющий конфигурационные данные массив.
     *
     * @throws Exception
     */
    public function __construct(?string $publicPath = null, array $config = [], ?LoggerInterface $logger = null)
    {
        $this->mode = self::CONSOLE_MODE;

        \defined('HLEB_START') or \define('HLEB_START', \microtime(true));

        parent::__construct($publicPath, $config, $logger);
    }

    /**
     * Setting the arguments passed to the script or imitating them.
     *
     * Установка переданных скрипту аргументов или их имитация.
     */
    public function setArgv(array $argv): self
    {
        $this->argv = $argv;

        return $this;
    }

    /**
     * Returns the command execution code.
     *
     * Возвращает код выполнения команды.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Console loader. Only for working with console commands.
     * Returns the exit code of the script.
     *
     * Консольный загрузчик. Только для работы с консольными командами.
     * Возвращает код завершения скрипта.
     */
    #[\Override]
    public function load(): int
    {
        $this->code = 0;

        try {
            $cli = $this->loadConsoleApp();

        } catch (\AsyncExitException $e) {
            // AsyncExitException needs to be handled similarly since the commands can be used anywhere.
            // Необходимо обработать AsyncExitException аналогично, так как команды могут быть использованы везде.
            $cli = $e->getMessage();
        } catch (CoreException $e) {
            echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
            $this->getLogger()->error($e);
            $this->code = 1;

            return $this->code;
        } catch (\Throwable $t) {
            // Errors should be handled similarly, since the commands can be used anywhere.
            // Необходимо обработать ошибки аналогично, так как команды могут быть использованы везде.
            $pr = $t->getPrevious();
            while ($pr !== null) {
                if (\get_class($pr) === \AsyncExitException::class) {
                    $cli = $pr->getMessage();
                    break;
                }
                $pr = $pr->getPrevious();
            }
            $pr or throw $t;
        }

        if (\is_string($cli)) {
            echo $cli;
        } else if (!$cli) {
            echo ConsoleHandler::DEFAULT_MESSAGE . PHP_EOL;
            $this->code = 1;
        }

        return $this->code;
    }

    /**
     * Loading and running console commands.
     *
     * Загрузка и запуск консольных команд.
     *
     * @throws CoreException
     */
    private function loadConsoleApp(): string|bool
    {
        $this->logger and Log::setLogger($this->logger);
        $argv = $this->argv ?? $GLOBALS['argv'] ?? $_SERVER['argv'] ?? [];
        LogLevel::setDefaultMaxLogLevel(SystemSettings::getCommonValue('max.cli.log.level'));
        DynamicParams::setArgv($argv);
        \date_default_timezone_set($this->config['common']['timezone']);

        $webConsole = new WebConsole();
        if (!empty($_SERVER['REQUEST_METHOD'])) {
            if (!$webConsole->load()) {
                return '';
            }
            $webArgs = $webConsole->getArgs();
        }
        $handler = (new ConsoleHandler($webArgs ?? $argv, $this->config));
        $result = $handler->run();
        $this->code = $handler->getCode();
        if ($result === false) {
            return false;
        }
        isset($webArgs) and $result = $webConsole->addFooter($result);

        return $result;
    }
}
