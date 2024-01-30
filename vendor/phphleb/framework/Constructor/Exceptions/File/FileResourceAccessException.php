<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * Error accessing a file or folder in the framework.
 * Occurs when a file or folder is not found
 * or failed to read the content from the file.
 *
 * Ошибка обращения к файлу или папке во фреймворке.
 * Возникает, когда файл или папка не найдены
 * или не получилось прочитать контент из файла.
 *
 */
#[NotFinal]
class FileResourceAccessException extends CoreProcessException implements FileSystemException
{
}
