<?php

declare(strict_types=1);

namespace App\Commands;

use Hleb\Base\Task;
use Hleb\Constructor\Data\Key;
use Hleb\CoreProcessException;
use Hleb\Helpers\PhpCommentHelper;

/**
 * The command allows you to set up a new project based on the framework.
 * It is recommended to use only when installing the framework
 * and then remove current command file.
 * This command does not return an execution code.
 *
 * Команда позволяет настроить новый проект на основе фреймворка.
 * Рекомендуется использовать только при установке фреймворка
 * и после этого удалить текущий файл с командой.
 * Эта команда не возвращает код выполнения.
 */
class ProjectSetupTask extends Task
{
    /** php console project-setup-task [arg] **/

    private int $numAction = 0;

    /**
     * Initial project setup.
     *
     * Первоначальная настройка проекта.
     *
     * @param string|null $arg - if set to "--clear", deletes the command after execution.
     *                         - при значении "--clear" удаляет команду после выполнения.
     */
    protected function run(?string $arg = null): void
    {
        try {
            $this->generateKey();
            $this->localConfiguration();
            $this->deleteRequirementsFile();
        } catch (\Throwable){
            exit('Failed to set up project...' . PHP_EOL);
        }
        if ($arg === '--clear') {
            $this->clearThisFile();
        }
    }

    /**
     * Generate a secret key.
     *
     * Генерация секретного ключа.
     */
    private function generateKey(): void
    {
        $this->indicate();
        try {
            Key::get();
            echo 'The secret key has been generated.' . PHP_EOL;
        } catch (CoreProcessException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * Create a configuration for local development.
     *
     * Создание конфигурации для локальной разработки.
     */
    private function localConfiguration(): void
    {
        $this->indicate();
        foreach (['database', 'common', 'main'] as $name) {
            $dir = $this->settings()->getPath("@/config");
            if (!file_exists("$dir/$name-local.php")) {
                $data = (array)file("$dir/$name.php");
                foreach($data as $key => $row) {
                    if (str_contains($row, "'/$name-local.php'")) {
                        unset($data[$key]);
                    }
                }
                $content = implode($data);
                if ($name === 'common') {
                    $content = str_replace("'debug' => false,", "'debug' => true,", $content);
                }
                if (!file_put_contents("$dir/$name-local.php", $content)) {
                    echo "Failed to copy config/$name.php. ";
                }
                $this->clearComments("$dir/$name-local.php");
            }
        }
        echo 'Creation of configuration files for local development.' . PHP_EOL;
    }

    /**
     * Removing the verification file due to its uselessness.
     *
     * Удаление проверочного файла из-за его ненадобности.
     */
    private function deleteRequirementsFile(): void
    {
        $this->indicate();
        $file = $this->settings()->getPath('public') . '/requirements.php';
        if (file_exists($file)) {
            unlink($file);
        }
        echo 'Removing the verification file: requirements.php' . PHP_EOL;
    }

    /**
     * Deleting the current command (file).
     *
     * Удаление текущей команды (файла).
     */
    private function clearThisFile(): void
    {
        $this->indicate();
        echo 'Delete setup command.' . PHP_EOL;
        unlink(__FILE__);
    }

    private function indicate(): void
    {
        echo '[' . ++$this->numAction . '] ';
    }

    /**
     * Removing comments in the working configuration file.
     *
     * Удаление комментариев в рабочем файле конфигурации.
     */
    private function clearComments(string $path): void
    {
        $content = file_get_contents($path);
        $content = (new PhpCommentHelper())->clearMultiLine($content);
        $content = trim(preg_replace('/(\r\n|\n)+(?=(\r\n|\n)+)/u', '', $content));

        file_put_contents($path, $content);
    }
}
