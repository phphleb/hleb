<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Logger;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Cache\WebCron;
use Hleb\Database\SystemDB;
use Hleb\Static\Settings;
use Hleb\Static\System;
use RuntimeException;

/**
 * Performs logging to files.
 *
 * Осуществляет логирование в файлы.
 */
#[Accessible] #[AvailableAsParent]
class FileLogger extends BaseLogger implements LoggerInterface
{
    protected const CACHE_LOGS_NUM = 100;

    private static ?string $requestId = null;

    private static bool $checkSize = false;

    private static array $memCache = [];

    public function __construct(
        readonly private string $storageDir,
        readonly private string $host,
        readonly private bool   $sortByDomain,
        readonly private bool   $isConsoleMode = false,
        bool   $isDebug = false,
    )
    {
        $this->isDebug = $isDebug;
    }

    /**
     * Remnants of accumulated logs are saved.
     *
     * Сохраняются остатки накопившихся логов.
     *
     * @internal
     */
    public static function finished(): void
    {
        foreach(self::$memCache as $file => $logs) {
            self::saveText($file, \implode($logs));
        }
        self::$memCache = [];
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('emergency', $message, $context), 'emergency');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('alert', $message, $context), 'alert');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('critical', $message, $context), 'critical');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('error', $message, $context), 'error');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('warning', $message, $context), 'warning');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('notice', $message, $context), 'notice');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('info', $message, $context), 'info');
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('debug', $message, $context), 'debug');
    }

    /**
     * The $level variable can contain the value 'state' - logging requests to the database.
     *
     * В переменной $level может быть значение 'state' - логирование запросов к БД.
     *
     * @inheritDoc
     */
    #[\Override]
    public function log(mixed $level, string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog($level, $message, $context), $level);
    }


    /**
     * Delayed saving of logs in parts depending on the level.
     * For console commands and errors, logs are saved directly.
     *
     * Отложенное сохранение логов частями в зависимости от уровня.
     * Для консольных команд и ошибок логи сохраняются напрямую.
     */
    protected function delayedSave(?string $level, string $file, string $row): void
    {
        self::$memCache[$file][] = $row;

        if (\in_array($level, ['emergency', 'alert', 'critical', 'error']) ||
            \count(self::$memCache) >= self::CACHE_LOGS_NUM
        ) {
            self::finished();
        }
    }

    /**
     * Output a line with a log to a file.
     *
     * Вывод строки с логом в файл.
     *
     * @param string $row - formed string for logging.
     *                    - сформированная строка для логирования.
     *
     * @param string|null $level
     * @return void
     */
    private function saveFile(string $row, ?string $level = null): void
    {
        $this->init();
        $I = DIRECTORY_SEPARATOR;
        $dir = $this->storageDir . $I . 'logs';
        try {
            $maxSize = Settings::getParam('common', 'max.log.size');
        } catch (\Throwable) {
            $maxSize = 0;
        }
        if ($maxSize > 0) {
            // If the action has not yet been performed or a lot of logs have been sent.
            // Если ещё не производилось действие или много отправлено логов.
            if (!self::$checkSize) {
                $this->clear($dir);
                $this->clearOut($dir);
                self::$checkSize = true;
            }
        }
        $dbPrefix = $level === LogLevel::STATE && \str_contains($row, SystemDB::DB_PREFIX) ? '.db' : '';
        if (!\file_exists($dir)) {
            try {
                \set_error_handler(function ($_errno, $errstr) {
                    throw new RuntimeException($errstr);
                });
                \mkdir($dir, 0775, true);
            } catch (RuntimeException) {
            } finally {
                \restore_error_handler();
            }
        }
        if ($this->isConsoleMode) {
            $file = $dir . $I . \date('Y_m_d') . $dbPrefix . '.system.log';
            \file_put_contents($file, $row . PHP_EOL, FILE_APPEND|LOCK_EX);
            @\chmod($file, 0664);
            return;
        }
        $prefix = $this->sortByDomain ?
            (\str_replace(['\\', '//', '@', '<', '>'], '',
                \str_replace('127.0.0.1', 'localhost',
                    \str_replace('.', '_',
                        \explode(':', $this->host)[0]
                    )
                )
            ) ?: 'handler') : 'project';

        $file = $dir . $I . \date('Y_m_d_') . $prefix . $dbPrefix . '.log';
        $this->delayedSave($level, $file, $row . PHP_EOL);
    }

    /**
     * Deletes one old log every hour if the size of the logs
     * exceeds the one specified in the settings.
     *
     * Удаляет по одному старому логу каждый час, если размер
     * логов превышает заданный в настройках.
     */
    private function clear(string $dir): void
    {
        WebCron::offer('hl_file_logger_cache', function() use ($dir) {
            (new LogCleaner())->clearInitialLogIfExceeded($dir, 'Y_m_d');
        }, 3600);
    }

    /**
     * Every 10 seconds it checks whether the logs exceed the permissible size and if so, it deletes the logs.
     *
     * Каждые 10 секунд проверяет превышение допустимого размера логами и при превышении удаляет логи.
     */
    private function clearOut(string $dir): void
    {
        WebCron::offer('hl_file_log_exceeded_cache', function() use ($dir) {
            (new LogCleaner())->clearAllLogsIfExceeded($dir, 'Y_m_d');
        }, 10);
    }

    /**
     * Saving logs to a file.
     *
     * Сохранение логов в файл.
     */
    private static function saveText(string $file, string $text): void
    {
        @\file_put_contents($file, $text, FILE_APPEND);
        @\chmod($file, 0664);
    }

    private function init(): void
    {
        try {
            // If there is a low-level error, this class will not be loaded.
            // При низкоуровневой ошибке этот класс будет не загружен.
            $requestId = System::getRequestId();
        } catch (\Throwable) {
            self::$requestId = \sha1(\rand());
            self::$checkSize = true;
            return;
        }

        if (self::$requestId !== $requestId) {
            self::$requestId = $requestId;
            self::$checkSize = false;
        }
    }
}
