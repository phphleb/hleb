<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Cache;

use Hleb\Helpers\DirectoryHelper;
use Hleb\Static\Path;
use Hleb\Static\Settings;

/**
 * A specialized function for counting intervals
 * and executing tasks when they are exceeded.
 * The launched tasks should not block each other,
 * that is, they can be executed simultaneously.
 * Also, since it runs over the Web, if the task
 * is interrupted, it will run again to complete.
 *
 * Узкоспециальная функция для отсчёта интервалов
 * и выполнения задач при превышении их.
 * Запускаемые задачи не должны блокировать друг друга,
 * то есть могут выполнятся одновременно.
 * Также, так как запускается через Веб, при прерывании
 * задачи она запустится снова для завершения.
 *
 * @internal
 */
final class WebCron
{
  private const DIR = '@storage/cache/source/0_webcron';

    /**
     * Returns whether the function was executed.
     * The function can be executed if the key interval
     * exceeds the period in seconds.
     * A new waiting period is then established.
     *
     * Возвращает результат была ли выполнена функция.
     * Функция может быть выполнена, если интервал
     * по ключу превысил период в секундах.
     * Затем устанавливается новый период ожидания.
     *
     * @internal
     */
  public static function offer(string $key, \Closure $func, int $period = 1): bool
  {
      $dir = Path::get(self::DIR);
      if (!\file_exists($dir)) {
          @\mkdir($dir, 0777, true);
      }
      $files = \iterator_to_array(DirectoryHelper::getFileIterator($dir, $key . '_*.txt'));
      $file = null;
      $name = $key . '_' . $period . '.txt';
      /**
       * @var \SplFileInfo[] $files
       */
      foreach($files as $file) {
          if ($file->getFilename() === $name) {
              $file = $file->getRealPath();
          } else {
              try {
                  \set_error_handler(function ($_errno, $errstr) {
                      throw new \RuntimeException($errstr);
                  });
                  @\unlink($file->getRealPath());
              } catch (\RuntimeException) {
              } finally {
                  \restore_error_handler();
              }
          }
      }
      $time = Settings::getParam('system', 'start.unixtime');
      if ($file) {
          $previous = @\file_get_contents($file);
          if (!$previous || $previous == $time || (float)$previous >= $time - $period) {
              return false;
          }
      }
      $path = $dir . DIRECTORY_SEPARATOR . $name;
      @\file_put_contents($path, $time) and $func();
      @\chmod($path, 0777);

      return true;
  }
}
