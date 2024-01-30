<?php

declare(strict_types=1);

namespace Hleb\Main\Console\Commands;

use Hleb\Base\Task;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Constructor\DI\DependencyInjection;
use Hleb\CoreException;
use Hleb\DynamicStateException;
use Hleb\Helpers\ReflectionMethod;
use Hleb\Helpers\TaskHelper;
use Hleb\Main\Console\Commands\Help\HelpGenerator;

/**
 * @internal
 */
final class CustomTask
{
    private int $code = 0;

    /**
     * Returns the code of the executed command or a default value.
     *
     * Возвращает код выполненной команды или значение по умолчанию.
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Returns the result of executing the custom command,
     * or false if the command was not found.
     *
     * Возвращает результат выполнения пользовательской команды
     * или false, если команда не была найдена.
     *
     * @throws CoreException
     */
    public function searchAndRun(array $args): false|string
    {
        $command = $args[0] ?? null;
        $secondArg = $args[1] ?? null;

        $verb = \in_array('--strict-verbosity', $args) ? true : null;
        unset($args[(string)\array_search('--strict-verbosity', $args)]);

        if ($command === null) {
            return false;
        }
        $helper = new TaskHelper();
        if (!$helper->checkName((string)$command)) {
            $this->code = 1;
            return 'Wrong command name';
        }

        $path = SystemSettings::getRealPath('@app/Commands');
        if (!$path) {
            return false;
        }

        $allCommands = $helper->getCommands(true);
        $searchData = [];
        $names = 0;
        foreach ($allCommands as $data) {
            if ($data['name'] === $command || (isset($data['short']) && $data['short'] === $command)) {
                $searchData = $data;
                $names++;
            }
        }
        if (!$searchData) {
            return false;
        }
        if ($names > 1) {
            throw new DynamicStateException("Duplicate task name for {$searchData['class']}");
        }

        $class = $searchData['class'];
        if (!\class_exists($class)) {
            return false;
        }
        if (!\is_subclass_of($class, Task::class)) {
            throw new DynamicStateException("$class must be inherited from Hleb\Base\Task.");
        }
        if (!\method_exists($class, 'run')) {
            throw new DynamicStateException("The $class command class must have an `run` method.");
        }
        if ($secondArg === '--desc') {
            return (new ReflectionMethod($class, 'run'))->getDocComment() . PHP_EOL;
        }
        if ($secondArg === '--help') {
            return (new HelpGenerator())->get($class) . PHP_EOL;
        }
        $args = \array_slice($args, 1);

        $refConstruct = new ReflectionMethod($class, '__construct');
        $task = new $class(...($refConstruct->countArgs() > 1 ? DependencyInjection::prepare($refConstruct) : []));

        /** @var Task $class */
        $task->call($args, $verb);

        $this->code = $task->getCode();

        return '';
    }
}
