<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use FilesystemIterator;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\Disabled;
use Hleb\Constructor\Attributes\Hidden;
use Hleb\Constructor\Attributes\Task\Purpose;
use Hleb\CoreProcessException;
use Hleb\Static\Settings;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * General rules for naming commands and obtaining a list of commands.
 *
 * Общие правила для именования команд и получение списка команд.
 *
 * @internal
 */
#[Accessible]
final class TaskHelper
{
    /**
     * Checking the command name for valid characters.
     *
     * Проверка названия команды на допустимые символы.
     */
    public function checkName(string $str): bool
    {
        return \strlen($str) && \preg_match('/^[a-zA-Z0-9\/\-\:\.\_]+$/', $str);
    }

    /**
     * Search for duplicate names using command naming rules in the command list.
     *
     * Поиск дубликатов названий по правилам именования команд в списке команд.
     */
    public function getDuplicateName(array $list): array
    {
        $countValues = \array_count_values($list);
        $duplicates = [];
        foreach ($countValues as $key => $value) {
            if ($value > 1) {
                $duplicates[] = $key;
            }
        }
        return $duplicates;
    }

    /**
     * Returns a list of commands with data for each command.
     * The commands in the list can only be executed from the CLI.
     *
     * Возвращает перечень команд с данными каждой команды.
     * Команды в списке могут быть выполнены только из CLI.
     *
     * @param bool $withHidden - include those marked hidden in the list.
     *                         - включить в список помеченные скрытыми.
     * @return array
     */
    public function getCommands(bool $withHidden = false): array
    {
        $dir = Settings::getRealPath('@app/Commands');
        $tasks = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );
        $list = [];
        /**
         * @var \SplFileInfo $task
         */
        foreach ($tasks as $key => $task) {
            if (!$task->isFile() || !str_ends_with($task->getRealPath(), '.php')) {
                continue;
            }
            $path = \strstr(\ltrim(\str_replace($dir, '', $task->getRealPath(), $count), '\\/'), '.php', true);

            if ($count > 1) {
                continue;
            }
            $class = 'App\Commands\\' . \str_replace(DIRECTORY_SEPARATOR, '\\', $path);
            if (!$this->isVisibility($class)) {
                continue;
            }
            if (!$withHidden && $this->isHidden($class)) {
                continue;
            }
            $list[$key]['class'] = $class;
            $list[$key]['path'] = $path;

            $data = (new ClassDataInFile($task->getRealPath()));
            $class = $data->getClass();
            $constants = (new ReflectionConstant($class))->all();
            $name = $constants['TASK_NAME'] ?? null;
            if ($name) {
                $list[$key]['name'] = (string)$name;
            }
            $short = $constants['TASK_SHORT_NAME'] ?? null;
            if ($short) {
                $list[$key]['short'] = (string)$short;
            }
            if (!$name) {
                $parts = \explode(DIRECTORY_SEPARATOR, $path);
                $converter = new NameConverter();
                foreach ($parts as $k => $part) {
                    $parts[$k] = $converter->convertClassNameToStr($part);
                }
                $command = \implode('/', $parts);
                $list[$key]['name'] = $command;
            }
        }

        return $list;
    }

    /**
     * Checking the command visibility indicators in the general list.
     *
     * Проверка указателей видимости команд в общем списке.
     */
    public function isVisibility(string $class): bool
    {
        $helper = new AttributeHelper($class);
        // Check if the command is available for execution in console mode.
        // Проверка доступности команды для выполнения в консольном режиме.
        if ($helper->hasClassAttribute(Disabled::class) ||
            ($helper->hasClassAttribute(Purpose::class) &&
                $helper->getClassValue(Purpose::class, 'status') === Purpose::EXTERNAL
            )
        ) {
            return false;
        }
        return true;
    }

    /**
     * A command can be specified hidden from the list
     * using the #[Hidden] attribute on the class.
     *
     * Команду можно указать скрытой из списка команд
     * с помощью атрибута #[Hidden] для класса.
     */
    public function isHidden(string $class): bool
    {
        return (new AttributeHelper($class))->hasClassAttribute(Hidden::class);
    }
}
