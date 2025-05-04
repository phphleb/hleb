<?php
/**
 * @author  Foma Tuturov <fomiash@yandex.ru>
 */

declare(strict_types=1);

namespace Hleb\Base;

use App\Bootstrap\BaseContainer;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Reference\SettingInterface;
use Hleb\Main\Console\{Colorizer, Console, Specifiers\ArgType, Specifiers\LightDataType};

/**
 * The base class for the console command, all console commands must be inherited from it.
 *
 * Базовый класс для консольной команды, все консольные команды должны быть унаследованы от него.
 */
#[AvailableAsParent]
abstract class Task extends Console
{
    /**
     * Returns framework settings from the command container.
     *
     * Возвращает настройки фреймворка из контейнера команд.
     *
     * @see BaseContainer
     */
    #[\Override]
    final protected function settings(): SettingInterface
    {
        return parent::settings();
    }

    /**
     * Direct command execution from code as a standard object method.
     *
     * Непосредственное выполнение команды из кода как через обычный метод объекта.
     *
     * @inheritDoc
     */
    #[\Override]
    final public function call(array $arguments = [], ?bool $strictVerbosity = null): bool
    {
        return parent::call($arguments, $strictVerbosity);
    }

    /**
     * Returns the result code for the executed command.
     *
     * Возвращает результирующий код для выполненной команды.
     */
    #[\Override]
    final public function getCode(): int
    {
        return parent::getCode();
    }

    /**
     * Getting the result of the executed command as specified in the command code via setResult().
     *
     * Получение результата выполненной команды как заданное в коде команды через setResult().
     */
    #[\Override]
    final public function getResult(): mixed
    {
        return parent::getResult();
    }

    /**
     * Returns an array of all arguments.
     *
     * Возвращает массив всех аргументов.
     *
     * @return LightDataType[]
     */
    #[\Override]
    final protected function getOptions(): array
    {
        return parent::getOptions();
    }

    /**
     * Returns the DataType object of the specific argument by name.
     *
     * Возвращает объект DataType конкретного аргумента по имени.
     */
    #[\Override]
    final protected function getOption(string $name): ?LightDataType
    {
        return parent::getOption($name);
    }

    /**
     * Returns an array with the named argument signatures assigned, or an empty array
     * if no named arguments are needed for the command.
     *
     * Возвращает массив с назначенными сигнатурами именованных аргументов или пустой массив,
     * если именованные аргументы для команды не нужны.
     *
     * @see Arg() - list of possibilities.
     *            - перечень возможностей.
     *
     * @return ArgType[]
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Returns an object with text coloring methods for the terminal.
     * The terminal you are using must support basic colors.
     *
     * Возвращает объект с методами раскраски текста для терминала.
     * Используемый терминал должен поддерживать базовые цвета.
     *
     * ```php
     * echo $this->color()->green('text') . PHP_EOL;
     * // or
     * $c = $this->color();
     * echo $c->green('text') . PHP_EOL;
     * ```
     *
     * @see Colorizer
     */
    protected function color(): Colorizer
    {
        return parent::color();
    }

    /**
     * Disables automatic processing of process interrupt signals.
     * Active when the 'pcntl' extension is present.
     * After this, you can use the method for manual signal processing.
     *
     * Выключает автоматическую обработку сигналов на прерывание процесса.
     * Активно при наличии расширения 'pcntl'.
     * После этого можно использовать метод для ручной обработки сигналов.
     *
     * @see self::isShutdownSignalReceived()
     */
    protected function disableAutomaticSignalInterrupt(): void
    {
        parent::disableAutomaticSignalInterrupt();
    }

    /**
     * Enables automatic processing of process shutdown signals.
     * Active when the 'pcntl' extension is present.
     * Enabled by default, required to disable manual signal processing.
     *
     * Включает автоматическую обработку сигналов на прерывание процесса.
     * Активно при наличии расширения 'pcntl'.
     * По умолчанию включено, необходимо для отмены ручной обработки сигналов.
     *
     * @see self::disableAutomaticSignalInterrupt()
     */
    protected function enableAutomaticSignalInterrupt(): void
    {
        parent::enableAutomaticSignalInterrupt();
    }

    /**
     * Checks if a process shutdown signal has been received.
     * Active when the 'pcntl' extension is present.
     * The default signal handler must be disabled for this to work.
     * The signal type can be checked by comparing it with the returned value.
     * If there are no shutdown signals, returns 0.
     * Example with a specific signal:
     *
     * Проверяет, был ли получен сигнал на выключение процесса.
     * Активно при наличии расширения 'pcntl'.
     * Для работы необходимо отключить дефолтный обработчик сигналов.
     * Тип сигнала можно проверить сравнив с возвращенным значением.
     * При отсутствии сигналов на выключение возвращает 0.
     * Пример с конкретным сигналом:
     *
     * ```php
     *  $this->disableAutomaticSignalInterrupt();
     *  if ($this->isShutdownSignalReceived() === self::SIGINT) {
     *    // ... //
     *  }
     * ```
     */
    protected function isShutdownSignalReceived(): int
    {
        return parent::isShutdownSignalReceived();
    }
}
