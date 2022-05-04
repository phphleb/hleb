<?php
/**
 * Intermediary class for connecting a logger.
 */

namespace Hleb\Main\Logger;


use Hleb\Constructor\Handlers\Request;
use Hleb\Main\Insert\BaseSingleton;
use Hleb\Scheme\Home\Main\LoggerInterface;

/**
 * The logger performs only logging, but the output of headers and exit from execution for levels below 'warning'
 * is not in its area of responsibility.
 *
 * Логгер осуществляет только логирование, а вот вывод заголовков и выход из выполнения для уровней ниже 'warning'
 * не входит в его область ответственности.
 */
class Log extends BaseSingleton implements LoggerInterface
{
    /**
     * To connect your own logger, create the file `app/Optional/MainLogger.php`
     */
    private static $defaultLogger = 'App\Optional\MainLogger';

    /** @var LoggerInterface $logger */
    private static $logger = null;

    /**
     * @inheritDoc
     */
    public function emergency(string $message, array $context = [])
    {
        if (self::checkLevel('emergency')) {
            $this->init();
            self::$logger->emergency($message, self::prepareContext('emergency', $context));
        }
    }

    /**
     * @inheritDoc
     */
    public function alert(string $message, array $context = [])
    {
        if (self::checkLevel('alert')) {
            $this->init();
            self::$logger->alert($message, self::prepareContext('alert', $context));
        }
    }

    /**
     * @inheritDoc
     */
    public function critical(string $message, array $context = [])
    {
        if (self::checkLevel('critical')) {
            $this->init();
            self::$logger->critical($message, self::prepareContext('critical', $context));
        }
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = [])
    {
        if (self::checkLevel('error')) {
            $this->init();
            self::$logger->error($message, self::prepareContext('error', $context));
        }
    }

    /**
     * @inheritDoc
     */
    public function warning(string $message, array $context = [])
    {
        if (self::checkLevel('warning')) {
            $this->init();
            self::$logger->warning($message, self::prepareContext('warning', $context));
        }
    }

    /**
     * @inheritDoc
     */
    public function notice(string $message, array $context = [])
    {
        if (self::checkLevel('notice')) {
            $this->init();
            self::$logger->notice($message, self::prepareContext('notice', $context));
        }
    }

    /**
     * @inheritDoc
     */
    public function info(string $message, array $context = [])
    {
        if (self::checkLevel('info')) {
            $this->init();
            self::$logger->info($message, self::prepareContext('info', $context));
        }
    }

    /**
     * @inheritDoc
     */
    public function debug(string $message, array $context = [])
    {
        if (self::checkLevel('debug')) {
            $this->init();
            self::$logger->debug($message, self::prepareContext('debug', $context));
        }
    }

    /**
     * @inheritDoc
     */
    public function log($level, string $message, array $context = [])
    {
        if (self::checkLevel($level)) {
            $this->init();
            self::$logger->log($level, $message, self::prepareContext($level, $context));
        }
    }

    private static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    private static function prepareContext(string $level, array $context = [])
    {
        if (Request::isConsoleMode()) {
            $context['command'] = Request::getConsoleCommand();
        } else {
            $context['domain'] = Request::getDomain();
            $context['url'] = Request::getMainUrl();
            if (Request::getRemoteAddress()) {
                $context['ip'] = Request::getRemoteAddress();
            }
        }

        if (isset($context['file'], $context['line'])) {
            return $context;
        }
        $response = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $cells = [];
        $searchList = ['line' => 1, 'file' => 1, 'function' => 2, 'class' => 2, 'type' => 2];
        if ($response && isset($response[1])) {
            foreach ($searchList as $name => $num) {
                if (isset($response[$num][$name])) {
                    $cells[$name] = $response[$num][$name];
                }
            }
        }
        if (isset($cells['type'])) {
            $cells['method'] = $cells['type'];
        }
        $cells['type'] = $level;

        return array_merge($context, $cells);
    }

    private static function checkLevel(string $level)
    {
        return array_search($level, LogLevel::ALL) <= array_search(LogLevel::getDefault(), LogLevel::ALL);
    }

    private static function init()
    {
        if (is_null(self::$logger)) {
            class_exists(self::$defaultLogger) ? self::setLogger(new self::$defaultLogger()) : self::setLogger(new FileLogger());
        }
    }

}

