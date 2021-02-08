<?php

/**
 * Creating an empty new command from a template.
 *
 * Создание пустой новой команды из шаблона.
 */

namespace Hleb\Main\Console;


class CreateTask
{
    public function __construct(string $name, string $description) {
        $name = strtolower($name);
        if (empty($name) || empty($description) || !preg_match("/^[\w\-\/]+$/i", $name)) {
            hl_preliminary_exit(PHP_EOL . 'Invalid command format. ' . PHP_EOL . 'Example: php console --new-task directory/new-name-task "Command short description"' . PHP_EOL);
        }
        $pathParts = explode('/', $name);
        $pathFile = '';
        foreach ($pathParts as $pathPart) {
            $parts = explode('-', $pathPart);
            foreach ($parts as $value) {
                $pathFile .= ucfirst($value);
            }
            $pathFile .= '/';
        }
        $pathFile = trim($pathFile, "\\/ ");
        $classList = explode('/', trim($pathFile, "\\/ "));
        $className = end($classList);
        $fullPathToFile = str_replace('\\', '/', HLEB_GLOBAL_DIRECTORY . '/app/Commands/' . $pathFile);
        if (file_exists($fullPathToFile . '.php')) {
            hl_preliminary_exit(PHP_EOL . 'A task with the same name already exists!' . PHP_EOL);
        }
        if (strpos($fullPathToFile . '.php', 'Task.php') === false) {
            hl_preliminary_exit(PHP_EOL . 'The command request must contain \'task\' at the end.' . PHP_EOL);
        }
        $classList = explode('/', $pathFile);

        array_pop($classList);

        $directoryPath = HLEB_GLOBAL_DIRECTORY . '/app/Commands/' . implode('/', $classList);

        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0750, true);
        }

        if (!is_dir($directoryPath)) {
            hl_preliminary_exit(PHP_EOL . 'Failed to create folder in `/app/Commands/`' . PHP_EOL);
        }

        $namespace = rtrim(('App\\Commands\\' . str_replace('/', '\\', implode('\\', $classList))), '\\');

        $result = $this->createTask($fullPathToFile . '.php', $name, $className, $namespace, $description);

        if ($result) {
            hl_preliminary_exit(PHP_EOL . "Command `$name` successfully created!" . PHP_EOL . "File in `/app/Commands/$pathFile.php`" . PHP_EOL);
        } else {
            hl_preliminary_exit(PHP_EOL . "Command not created." . PHP_EOL);
        }
    }

    private function createTask(string $fileName, string $taskName, string $class, string $namespace, string $description) {
        $content = "<?php

namespace $namespace;

class $class extends \Hleb\Scheme\App\Commands\MainTask
{
    /** php console $taskName [arg] **/

    const DESCRIPTION = \"$description\";

    protected function execute(\$arg = null) {

        // ... //

        echo PHP_EOL . __CLASS__ . \" done.\" . PHP_EOL;
    }

}
";
        return file_put_contents($fileName, $content);
    }
}

