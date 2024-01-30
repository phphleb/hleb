<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console\Sections;

use FilesystemIterator;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\CoreProcessException;
use Hleb\Helpers\NameConverter;
use Hleb\InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Transferring the folder with the default module to the final one.
 *
 * Перенос папки с дефолтным модулем в итоговую.
 *
 * @internal
 */
final class ModuleCreator
{
    public function run(string $name): false|string
    {
        $name = \preg_replace('/(\-){2,}/', '$1', $name);
        $moduleName = $this->getModuleClassName($name);
        $baseModulePath = SystemSettings::getRealPath('modules');
        $modulePath = $baseModulePath . DIRECTORY_SEPARATOR . $name;
        if (\is_dir($modulePath)) {
            return "Error! Such a module (/{modules}/$name/) already exists." . PHP_EOL;
        }
        \is_dir($modulePath) or \hl_create_directory($modulePath);
        if (!\is_dir($modulePath)) {
            return 'Failed to create module folder.' . PHP_EOL;
        }
        $path = SystemSettings::getRealPath('@app/Optional/Modules/example');
        if (!$path) {
            $path = SystemSettings::getRealPath('@framework/Optional/Modules/example');
        }
        $files = $this->copyRecursive($path, $modulePath);
        if ($files) {
            foreach ($files as $file) {
                $content = \file_get_contents($file);
                if (\str_contains($content, 'module_class_name_template')) {
                    $content = \str_replace('module_class_name_template', $moduleName, $content);
                }
                if (\str_contains($content, 'modules_template')) {
                    $content = \str_replace('modules_template', SystemSettings::getSystemValue('module.namespace'), $content);
                }
                if (\str_contains($content, 'module_base_name_template')) {
                    $content = \str_replace('module_base_name_template', $name, $content);
                }
                \file_put_contents($file, $content);
            }
        }
        return "New module directory successfully created!" . PHP_EOL .
            'Run `composer dump-autoload` to update the classmap.' . PHP_EOL;
    }

    private function getModuleClassName(string $name): string
    {
        if (!\preg_match('/^[a-z]{1}[a-z0-9\-\/]+[a-z0-9]{1}$/', $name)) {
            throw new InvalidArgumentException("Wrong module name! Only lowercase Latin characters and numbers with a hyphen are allowed. The name must start with a character.");
        }
        return (new NameConverter())->convertStrToClassName($name);
    }

    /**
     * Transfers files from one folder to another, taking into account nesting.
     *
     * Переносит файлы из одной папки в другую с учётом вложенности.
     */
    private function copyRecursive(string $from, string $to): array
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS)
        );
        $result = [];
        /** @var SplFileInfo $item */
        foreach ($files as $item) {
            if ($item->isFile()) {
                $path = $item->getRealPath();
                $dir = \dirname($path);
                $relativePath = \trim(\str_replace($from, '', $dir), '\\/');
                $newPath = \rtrim($to, '\\/') . DIRECTORY_SEPARATOR . $relativePath . DIRECTORY_SEPARATOR;
                \hl_create_directory($newPath);
                $newFile = $newPath . $item->getFilename();
                if (!\copy($item->getRealPath(), $newFile)) {
                    throw new CoreProcessException("Failed to copy to file $newFile");
                }
                $result[] = $newFile;
            }
        }

        return $result;
    }
}
