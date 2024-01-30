<?php

namespace Hleb;

use Hleb\Constructor\Attributes\NotFinal;

/**
 * Error modifying a file or folder in the framework.
 * Occurs not only when the file data is changed,
 * but also when the path to the file or folder is changed,
 * as well as when they are deleted or when access rights are changed.
 *
 * Ошибка модификации файла или папки во фреймворке.
 * Возникает не только при изменении данных файла,
 * но и изменения пути к файлу или папке,
 * а также их удаления или изменения прав доступа.
 */
#[NotFinal]
class FileResourceModificationException extends CoreProcessException implements FileSystemException
{
}
