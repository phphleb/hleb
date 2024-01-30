<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Logger;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Constructor\Cache\WebCron;
use Hleb\Database\SystemDB;
use Hleb\Static\Settings;
use Hleb\Static\System;

/**
 * Performs logging to files.
 *
 * Осуществляет логирование в файлы.
 */
#[Accessible] #[AvailableAsParent]
class FileLogger extends BaseLogger implements LoggerInterface
{
    private static ?string $requestId = null;

    private static bool $checkSize = false;

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
     * @inheritDoc
     */
    #[\Override]
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('emergency', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('alert', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('critical', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('error', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('warning', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('notice', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('info', $message, $context));
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->saveFile($this->createLog('debug', $message, $context));
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
     * Output a line with a log to a file.
     *
     * Вывод строки с логом в файл.
     *
     * @param string $row - formed string for logging.
     *                    - сформированная строка для логирования.
     *
     * @param null $level
     * @return void
     */
    private function saveFile(string $row, $level = null): void
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
                self::$checkSize = true;
            }
        }
        $dbPrefix = $level === LogLevel::STATE && \str_contains($row, SystemDB::DB_PREFIX) ? '.db' : '';
        if ($this->isConsoleMode) {
            if (!\file_exists($dir)) {
                \hl_create_directory($dir);
            }
            \file_put_contents($dir . $I . \date('Y_m_d') . $dbPrefix . '.system.log', $row . PHP_EOL, FILE_APPEND|LOCK_EX);
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

        \file_put_contents($dir . $I . \date('Y_m_d_') . $prefix . $dbPrefix . '.log', $row . PHP_EOL, FILE_APPEND);
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
            (new LogCleaner())->run($dir, 'Y_m_d');
        }, 3600);
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
