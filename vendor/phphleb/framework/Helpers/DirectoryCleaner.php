<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use GlobIterator;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Data\SystemSettings;
use Hleb\Static\Path;

#[Accessible]
final class DirectoryCleaner
{
    final public const PERMISSION_MESSAGE = 'Permission denied! It is necessary to assign rights to the directory ';

    private array $errors = [];

    /**
     * Returns errors if any were caused by an accelerated deletion.
     *
     * Возвращает ошибки, если они возникли в результате ускоренного удаления.
     */
   public function getErrors(): array
   {
       return $this->errors;
   }

    /**
     * Returns the result of an accelerated deletion
     * by renaming the source directory.
     *
     * Возвращает результат ускоренного удаления
     * через переименование исходной директории.
     */
   public function forceRemoveDir(?string $path = null): bool
   {
       if (!\file_exists($path)) {
           return true;
       }
       $viewPath = $this->getBasePath($path);
       if (!\is_writable($path)) {
           $this->errors[] = self::PERMISSION_MESSAGE . $viewPath;
           return false;
       }
       $newPath = \rtrim($path, "/") . "_delete_" . \sha1($path);
       if (\is_dir($newPath)) {
           // If the previous deletion was not completed.
           // Если предыдущее удаление не было выполнено до конца.
           $this->removeDir($newPath);
       }
       \rename($path, $newPath);
       \clearstatcache();

       if (\file_exists($newPath) && !\is_writable($newPath)) {
           $this->errors[] = self::PERMISSION_MESSAGE . $viewPath;
           return false;
       }
       if (!\file_exists($newPath)) {
           $this->errors[] = 'Failed to move directory.';
           return false;
       }
       $this->removeDir($newPath);

       if (\file_exists($newPath)) {
           $this->errors[] = 'Failed to clean directory: ' . $newPath;
           return false;
       }

       return true;
   }

    /**
     * Deleting a folder by first deleting all files and folders in it.
     * Irrational, but easier way.
     * For Linux, an attempt will be made to clear via the command.
     *
     * Удаление папки через предварительное удаление всех файлов и папок в ней.
     * Нерациональный, но более простой способ.
     * Для Linux будет выполнена попытка удалить через команду.
     */
    public function removeDir(?string $path = null): void
    {
        $path = \realpath($path);
        if ($path && \file_exists($path) && \is_dir($path)) {
            $global = \realpath(Path::getReal('global'));
            if (!$global || !\str_starts_with($path, $global)) {
               throw new \RuntimeException('You can only delete a folder within a project.');
            }

            if (!\str_starts_with(\strtoupper(\php_uname('s')), 'WINDOWS')) {
                try {
                    @\shell_exec("rm -rf $path");
                    \clearstatcache();
                    if (!file_exists($path)) {
                        return;
                    }
                } catch (\Throwable) {
                }
            }

            $dir = \opendir($path);
            if (!\is_resource($dir)) {
                return;
            }
            while (false !== ($element = \readdir($dir))) {
                if ($element !== '.' && $element !== '..') {
                    $tmp = $path . '/' . $element;
                    if (\is_dir($tmp)) {
                        $this->removeDir($tmp);
                    } else {
                        try {
                            \set_error_handler(function ($_errno, $errstr) {
                                throw new \RuntimeException($errstr);
                            });
                            @\unlink($tmp);
                        } catch (\RuntimeException) {
                        } finally {
                            \restore_error_handler();
                        }
                    }
                }
            }
            \closedir($dir);
            \clearstatcache();
            if (\is_dir($path) && !(new GlobIterator($path . '/*'))->count()) {
                @\rmdir($path);
            }
        }
    }

    /**
     * Getting the path from the project root.
     *
     * Получение пути от корня проекта.
     */
    private function getBasePath(string $path): string
    {
        $globalPath = SystemSettings::getRealPath('global');
        $path = \str_replace('\\', '/', $path);
        $globalPath = \str_replace('\\', '/', $globalPath);

        return \str_replace($globalPath, '', $path);
    }
}
