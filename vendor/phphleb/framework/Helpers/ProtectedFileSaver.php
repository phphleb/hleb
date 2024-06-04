<?php

/*declare(strict_types=1);*/

namespace Hleb\Helpers;

use Hleb\CoreProcessException;

/**
 * @internal
 */
final class ProtectedFileSaver
{
    /**
     * Saving a file with write protection (does not work in all OS).
     *
     * Сохранение файла с защитой от конкурентной записи (работает не во всех OC).
     */
    public function save(string $path, string $data): void
    {
       \hl_create_directory($path);

       $fp = \fopen($path, 'wb+');
       if ($fp === false || !\flock($fp, LOCK_EX)) {
           // If for some reason the file cannot be written, it is written without blocking.
           // Если по какой-то причине записать файл не удается, то он записывается без блокировки.
           \file_put_contents($path, $data);
           $fp and \fclose($fp);
           @\chmod($path, 0664);
           return;
       }
       \ftruncate($fp, 0);
       if (!\fwrite($fp, $data)) {
           throw new CoreProcessException('Failed to save the file, check the permissions on the directory.');
       }
       \fflush($fp);
       \flock($fp, LOCK_UN);

       \fclose($fp);
        @\chmod($path, 0664);
   }
}
