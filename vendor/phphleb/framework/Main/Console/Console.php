<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console;

use App\Bootstrap\BaseContainer;
use App\Bootstrap\ContainerInterface;
use App\Bootstrap\Events\TaskEvent;
use Hleb\Constructor\Attributes\Disabled;
use Hleb\Constructor\Attributes\Task\Purpose;
use Hleb\Constructor\DI\DependencyInjection;
use Hleb\DomainException;
use Hleb\Helpers\AttributeHelper;
use Hleb\InvalidArgumentException;
use Hleb\Reference\SettingInterface;
use Hleb\DynamicStateException;
use Hleb\Helpers\ReflectionMethod;
use Hleb\Main\Console\Specifiers\LightDataType;
use Hleb\Static\Settings;

abstract class Console
{
    /**
     * Exit code (program failed) for use in
     * command line programming.
     * Exit code values greater than 0 are not standardized
     * and depend on the scripts handling the execution.
     * When `script1` this code is returned from the expression
     * `script1 && script2` will not be executed by `script2`.
     *
     * Код выхода (программа не выполнилась) для использования в
     * программировании для командной строки.
     * Значения кодов выхода больше 0 не стандартизированы
     * и зависят от сценариев, обрабатывающих выполнение.
     * При возврате `script1` этого кода из выражения
     * `script1 && script2` не будет выполнен `script2`.
     */
    protected const ERROR_CODE = 1;

    /**
     * Exit code (program executed) for use in
     * command line programming.
     * When `script1` this code is returned from the expression
     * `script1 && script2` will also be executed by `script2`.
     *
     * Код выхода (программа выполнилась) для использования в
     * программировании для командной строки.
     * При возврате `script1` этого кода из выражения
     * `script1 && script2` также  будет выполнен `script2`.
     */
    protected const SUCCESS_CODE = 0;

    /**
     * The standard signal for graceful process termination.
     * Typically sent by the system or administrator to request termination,
     * giving the application a chance to gracefully release resources.
     *
     * Стандартный сигнал для корректного завершения процесса.
     * Обычно отправляется системой или администратором для запроса на завершение работы,
     * предоставляя приложению возможность корректно освободить ресурсы.
     *
     * @see self::isShutdownSignalReceived()
     */
    protected const SIGTERM = 15;

    /**
     * The user's abort signal, typically sent by pressing Ctrl+C in the terminal.
     * Used to stop the application at the user's initiative
     * with the ability to perform finishing actions.
     *
     * Сигнал прерывания процесса пользователем, как правило,
     * отправляется при нажатии Ctrl+C в терминале.
     * Используется для остановки приложения по инициативе пользователя
     * с возможностью выполнить завершающие действия.
     *
     * @see self::isShutdownSignalReceived()
     */
    protected const SIGINT = 2;

    private const RUN_ERROR = 'Parameters for `run` method arguments are incorrect: ';

    private const RULE_ERROR = 'Parameters passed incorrectly according to the rules from the `rules` method: ';

    private ?int $signalReceived = null;

    private ?bool $isAsyncSignal = null;

    private int $code = 0;

    private mixed $result = null;

    private mixed $execResult = null;

    private bool $verbosity = true;

    private ?bool $strictVerbosity = null;

    /**
     * @var LightDataType[]
     */
    private array $arguments = [];

    private array $basicArguments = [];

    private array $unnamedArguments = [];

    private bool $fromCli;

    private ?Colorizer $colorizer = null;

    private AttributeHelper $attributeHelper;

    /**
     * Required to call a container-based class inside another container-based class.
     * For example, the value can be passed as $this->config to a new command object
     * called inside the container class.
     *
     * Необходим для вызова класса на основе контейнера внутри другого такого класса.
     * Например, значение можно передать как $this->config в новый объект команды
     * вызываемой внутри класса с контейнером.
     */
    protected readonly array $config;

    /**
     * Contains a container with assigned services.
     * Usage example:
     *
     * Содержит контейнер с назначенными сервисами.
     * Пример использования:
     *
     * $this->container->db() / $this->container->get(DbInterface::class)
     *
     * @see BaseContainer
     */
    protected readonly ContainerInterface $container;

    /**
     * Constructor with container loading from configuration.
     *
     * Конструктор с загрузкой контейнера из конфигурации.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->container = $config['container'] ?? BaseContainer::instance();

        // System definition of the request as executed from the console.
        // Системное определение запроса как выполненного из консоли.
        $this->fromCli = $this->settings()->isCli();

        $this->registerSignalHandlers();
        $this->enableAutomaticSignalInterrupt();

        $this->attributeHelper = new AttributeHelper(static::class);
    }

    /**
     * Direct execution of a command from the code as through a regular object method;
     * in this case, the output of the results of the command execution is not performed.
     * For example (similar to default-task `php console default-task 1 --Name=Admin --quiet`):
     *
     * Непосредственное выполнение команды из кода как через обычный метод объекта, в этом
     * случае   вывод результатов выполнения команды не производится.
     * Например (аналогично для default-task `php console default-task 1 --Name=Admin --quiet`):
     *
     * ```php
     * if ((new DefaultTask)->call([1, 'Name' => 'Admin', '--quiet'])) {
     *    // Completed successfully.
     * }
     * ```
     *
     * @var array $arguments - an array with arguments for the command.
     *                       - массив с аргументами для команды.
     *
     * @var bool|null $strictVerbosity - forced output of the result of the command.
     *                                 - принудительный вывод результата работы команды.
     */
    public function call(array $arguments = [], ?bool $strictVerbosity = null): bool
    {
        $this->code = 0;
        $this->verbosity = true;
        $this->strictVerbosity = $strictVerbosity;
        $this->result = null;
        $this->execResult = null;
        $this->unnamedArguments = [];
        $this->arguments = [];
        $this->basicArguments = $arguments;

        if (!\method_exists(static::class, 'run')) {
            throw new DynamicStateException('Missing required `run` method for ' . static::class);
        }
        if ($this->attributeHelper->hasClassAttribute(Disabled::class)) {
            throw new DomainException('Execution is disabled by the presence of the #[Disabled] attribute.');
        }
        if (!$this->checkAttributes()) {
            throw new DynamicStateException('Forbidden by an attribute for a class ' . static::class);
        }

        if (Settings::system('events.used') !== false) {
            $eventMethod = new ReflectionMethod(TaskEvent::class, '__construct');
            $event = new TaskEvent(...($eventMethod->countArgs() > 1 ? DependencyInjection::prepare($eventMethod) : []));
            if (\method_exists($event, 'before')) {
                $this->unnamedArguments = $event->before(static::class, $this->fromCli ? 'run' : 'call', $this->unnamedArguments);
            }
        }

        $result = $this->fromCli ? $this->runCli() : $this->runOthers();
        if (\method_exists($event, 'after')) {
            $event->after(static::class, $this->fromCli ? 'run' : 'call', $this->result);
        }
        if (\method_exists($event, 'statusCode')) {
            $this->code = $event->statusCode(static::class, $this->fromCli ? 'run' : 'call', $this->code);
        }

        return $result;
    }

    /**
     * Checking the availability of the called task according to the attributes set in the class.
     *
     * Проверка доступности вызываемой задачи согласно установленным в классе атрибутам.
     */
    final public function isAllowed(): bool
    {
        if ($this->attributeHelper->hasClassAttribute(Disabled::class)) {
            return false;
        }
        return $this->checkAttributes();
    }

    /**
     * Returns the result code for the executed command.
     *
     * Возвращает результирующий код для выполненной команды.
     */
    public function getCode(): int
    {
        return $this->code;
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
     * Getting the result of the executed command.
     * Returns the data from the run() method of the command.
     * Since it is a good rule to return the execution status
     * in integer format, it is better to use the getResult()
     * method with pre-setting the desired value.
     *
     * Получение результата выполненной команды.
     * Возвращает данные метода run() команды.
     * Так как хорошим правилом будет возвращать статус выполнения
     * в формате integer, то лучше использовать метод getResult()
     * c предварительной установкой нужного значения.
     */
    final public function getExecResult(): mixed
    {
        return $this->execResult;
    }

    /**
     * Returns an array of custom rules (no validation).
     * (!) This is an internal system method of the framework.
     *
     * Возвращает массив пользовательских правил (без проверки на корректность).
     * (!) Это внутренний системный метод фреймворка.
     *
     * @internal
     */
    final public function getRules(): array
    {
        $hasRules = \method_exists(static::class, 'rules');
        if (!$hasRules) {
            return [];
        }
        return $this->rules();
    }

    /**
     * Returns an array of all arguments.
     *
     * Возвращает массив всех аргументов.
     *
     * @return LightDataType[]
     */
    protected function getOptions(): array
    {
        return $this->arguments;
    }

    /**
     * Returns the DataType object of the specific argument by name.
     *
     * Возвращает объект DataType конкретного аргумента по имени.
     */
    protected function getOption(string $name): ?LightDataType
    {
        return $this->arguments[$name] ?? null;
    }

    /**
     * Through this method, you can set the result of the running command,
     * and then get it in the code using getResult().
     *
     * Через этот метод можно установить результат запущенной команды,
     * а затем получить в коде через getResult().
     */
    final protected function setResult(mixed $data): void
    {
        $this->result = $data;
    }

    /**
     * Returns the framework settings from the container.
     *
     * Возвращает настройки фреймворка из контейнера.
     */
    protected function settings(): SettingInterface
    {
        return $this->container->settings();
    }

    /**
     * Returns an object with text coloring methods for the terminal.
     *
     * Возвращает объект с методами раскраски текста для терминала.
     */
    protected function color(): Colorizer
    {
        if (!$this->colorizer) {
            $this->colorizer = \in_array('--no-ansi', $this->basicArguments) ? new ReplacingColorizer() : new Colorizer();
        }
        return $this->colorizer;
    }

    /**
     * @see self::enableAutomaticSignalInterrupt()
     */
    protected function disableAutomaticSignalInterrupt(): void
    {
        if (\is_int($this->signalReceived) && $this->isAsyncSignal !== false) {
            \pcntl_async_signals(false);
            $this->isAsyncSignal = false;
        }
    }

    /**
     * @see self::disableAutomaticSignalInterrupt()
     */
    protected function enableAutomaticSignalInterrupt(): void
    {
        if (\is_int($this->signalReceived) && $this->isAsyncSignal !== true) {
            \pcntl_async_signals(true);
            $this->isAsyncSignal = true;
        }
    }

    /**
     * @see self::disableAutomaticSignalInterrupt()
     */
    protected function isShutdownSignalReceived(): int
    {
        if (\is_int($this->signalReceived)) {
            \pcntl_signal_dispatch();
        }
        return (int)$this->signalReceived;
    }

    /**
     * Monitors for system interrupt signals.
     *
     * Отслеживает наличие системных сигналов на прерывание работы.
     */
    private function registerSignalHandlers(): void
    {
        if (\is_int($this->signalReceived)) {
            return;
        }
        if (\function_exists('pcntl_signal')) {
            $this->signalReceived = 0;
            \pcntl_signal(self::SIGTERM, function() {
                $this->signalReceived = self::SIGTERM;
                    if ($this->isAsyncSignal) {
                        echo " SIGTERM detected, exiting..." . PHP_EOL;
                        exit(self::ERROR_CODE);
                }
            });
            \pcntl_signal(self::SIGINT, function() {
                $this->signalReceived = self::SIGINT;
                if ($this->isAsyncSignal) {
                    echo " Ctrl+C detected, exiting..." . PHP_EOL;
                    exit(self::ERROR_CODE);
                }
            });
        }
    }


    private function convertArguments(array $arguments): array
    {
        $result = [];
        foreach ($arguments as $name => $argument) {
            if (!\is_int($name)) {
                \is_string($argument) and $argument = \trim($argument, '"');
                $result[$name] = new LightDataType($argument);
                continue;
            }
            if (\str_starts_with($argument, '-')) {
                $param = \str_contains($argument, '=') ? \strstr($argument, '=', true) : $argument;
                $name = \ltrim($param, '-');
                $value = \ltrim((\strstr($argument, '=') ?: ''), '=');
                $value = \trim($value, '"');
                // `--Name=` or `-N`
                // `--Name=` или `-N`
                if ($param !== '--' . $name && $param !== '-' . $name) {
                    continue;
                }
                if ($value === '') {
                    $result[$name] = new LightDataType(true);
                    continue;
                }
                // An array passed to a string separated by commas.
                // Массив, переданный в строку через запятые.
                if (\str_contains($argument, '[') && \str_ends_with($argument, ']')) {
                    $list = \array_map('trim', \explode(',', \trim($value, '[]')));
                    $result[$name] = new LightDataType($list);
                    continue;
                }
                $result[$name] = new LightDataType($value);
            }
        }
        return $result;
    }

    private function runOthers(): bool
    {
        $this->verbosity = (bool)$this->strictVerbosity;

        return $this->runCli();
    }

    private function runCli(): bool
    {
        $this->code = 1;
        $rules = [];
        $this->searchQuietAndReplace($this->basicArguments);

        $this->verbosity or \ob_start();
        $reflectionMethod = new ReflectionMethod(static::class, 'run');
        $hasRules = \method_exists(static::class, 'rules');
        $handler = new IndexedArgConverter($reflectionMethod);
        if ($hasRules) {
            $rules = $this->rules();
            try {
                // Check user rules for validity.
                // Проверяются пользовательские правила на валидность.
                $handler->checkRules($rules);
            } catch (DynamicStateException $e) {
                throw new DynamicStateException(self::RULE_ERROR . $e->getMessage());
            }
        }
        // Sorting of named and unnamed parameters.
        // Сортировка именованных и неименованных параметров.
        $this->arguments = $this->convertArguments($this->searchSystemParams($this->basicArguments, $rules, $handler));

        // Check for standard arguments.
        // Проверка стандартных аргументов.
        $indexedArguments = $handler->checkIndexedArgs($this->unnamedArguments);
        if ($indexedArguments === false) {
            throw new InvalidArgumentException(self::RUN_ERROR . $this->getTypeErrors($handler->getErrors()));
        }
        // Check named arguments if rules for them are registered.
        // Проверка именованных аргументов, если зарегистрированы правила для них.
        if ($hasRules) {
            // Adding short names to the full names array.
            // Добавление коротких названий к массиву полных.
            $rules = $handler->assignmentOfShortNames($this->arguments, $rules);

            // Validate console options based on custom rules.
            // Проверка консольных параметров на основе пользовательских правил.
            $handler->checkAssocArguments($this->arguments, $rules);
        }
        $this->execResult = $this->run(...$indexedArguments);

        $this->verbosity or \ob_get_clean();

        $this->code = \is_int($this->execResult) ? $this->execResult : 0;

        return $this->code === 0;
    }

    private function searchQuietAndReplace(array &$arguments): void
    {
        foreach ($arguments as $key => $arg) {
            if (\is_string($arg) && \trim($arg) === '--quiet') {
                $this->verbosity = false;
                unset($arguments[$key]);
            }
        }
    }

    /**
     * Sorting of incoming parameters.
     * When you add the `--quiet` option, the output of the command execution is not produced.
     *
     * Сортировка входящих параметров.
     * При добавлении параметра `--quiet`, вывод результатов выполнения команды не производится.
     */
    private function searchSystemParams(array $arguments, array $rules, IndexedArgConverter $handler): array
    {
        $num = 0;
        $names = $handler->getAssigmentNames($rules);
        while (isset($arguments[$num]) && $this->checkIsUnnamed($arguments[$num], $names)) {
            $this->unnamedArguments[] = $arguments[$num];
            unset($arguments[$num]);
            $num++;
        }
        return $arguments;
    }

    /**
     * The presence of an argument in the rules is determined to distinguish between the value '-10' and '-Name'.
     *
     * Определяется присутствие аргумента в правилах, чтобы различать значение '-10' и '-Name=1'.
     */
    private function checkIsUnnamed(string $value, $names): bool
    {
        if (\str_starts_with($value, '-')) {
            $target = \explode('=', $value)[0];
            if (\in_array($target, $names, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checking class accessibility according to attributes.
     *
     * Проверка доступности класса согласно атрибутам.
     */
    private function checkAttributes(): bool
    {
        if ($this->attributeHelper->hasClassAttribute(Purpose::class)) {
            $status = $this->attributeHelper->getClassValue(Purpose::class, 'status');
            if ($this->fromCli && $status === Purpose::EXTERNAL) {
                return false;
            }
            if (!$this->fromCli && $status === Purpose::CONSOLE) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns a string list of method arguments for which parameters did not match.
     *
     * Возвращает строковой список аргументов метода, для которых не подошли параметры.
     */
    private function getTypeErrors(array $errors): string
    {
        return \implode(', ', \array_unique($errors));
    }


}
