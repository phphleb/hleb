<?php


namespace Hleb\Main\Logger;


use Hleb\Constructor\Handlers\Request;
use Hleb\Scheme\Home\Main\LoggerInterface;

class FileLogger implements LoggerInterface
{

    /**
     * @inheritDoc
     */
    public function emergency(string $message, array $context = [])
    {
        $this->saveFile($this->createLog('emergency', $message, $context));
    }

    /**
     * @inheritDoc
     */
    public function alert(string $message, array $context = [])
    {
        $this->saveFile($this->createLog('alert', $message, $context));
    }

    /**
     * @inheritDoc
     */
    public function critical(string $message, array $context = [])
    {
        $this->saveFile($this->createLog('critical', $message, $context));
    }

    /**
     * @inheritDoc
     */
    public function error($message, array $context = [])
    {
        $this->saveFile($this->createLog('error', $message, $context));
    }

    /**
     * @inheritDoc
     */
    public function warning(string $message, array $context = [])
    {
        $this->saveFile($this->createLog('warning', $message, $context));
    }

    /**
     * @inheritDoc
     */
    public function notice(string $message, array $context = [])
    {
        $this->saveFile($this->createLog('notice', $message, $context));
    }

    /**
     * @inheritDoc
     */
    public function info(string $message, array $context = [])
    {
        $this->saveFile($this->createLog('info', $message, $context));
    }

    /**
     * @inheritDoc
     */
    public function debug(string $message, array $context = [])
    {
        $this->saveFile($this->createLog('debug', $message, $context));
    }

    /**
     * @inheritDoc
     */
    public function log($level, string $message, array $context = [])
    {
       $this->saveFile($this->createLog($level, $message, $context));
    }

    private function createLog(string $level, string $message, array $context) {
        $log = ['[' . date("H:i:s d.m.Y e") . str_replace(['+00:00', ':00'], '', date("P")) . ']', (Request::isConsoleMode() ? 'System:' : 'Web:') . strtoupper($level)];
        $log[] = $message;
        if (isset($context['file'], $context['line'])) {
            $log[]= '{' . $context['file'] . ' on line ' . $context['line'] . '}';
        }
        if (isset($context['class'], $context['function'])) {
            $log[]= '{' .  $context['class'] . ($context['method'] ?? ':') . $context['function'] . '}';
        }
        if (isset($context['domain'], $context['url'])) {
            $log[]= $context['domain'] . ($context['url'] !== '/' ? $context['url'] : '');
        }
        if (isset($context['ip'])) {
            $log[]= $context['ip'];
        }
        $replace = [];
        foreach ($context as $key => $val) {
            if ((is_string($val) || is_numeric($val)) && strpos($message, '{' . $key . '}') !== false) {
                $replace['{' . $key . '}'] = $val;
                unset($context[$key]);
            }
        }
        $log[2] = strtr($message, $replace);
        unset($context['class'], $context['function'], $context['type'], $context['method'], $context['ip'], $context['file'], $context['line'], $context['domain'], $context['url']);

        return implode(' ' , $log) . ' ' . json_encode($context);
    }

    private function saveFile(string $row) {
        if (!HLEB_PROJECT_LOG_ON) {
            return false;
        }
        if (Request::isConsoleMode()) {
           return file_put_contents(HLEB_STORAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . date('Y_m_d_') . 'errors.system.log', $row . PHP_EOL, FILE_APPEND);
        }
        $prefix = defined('HLEB_PROJECT_LOG_SORT_BY_DOMAIN') && HLEB_PROJECT_LOG_SORT_BY_DOMAIN ?
            str_replace(['\\', '//', '@', '<', '>'], '',
                str_replace('127.0.0.1', 'localhost' ,
                    str_replace( '.', '_',
                        explode(':', $_SERVER['HTTP_HOST'])[0]
                )
                )
            ) . '_' : '';
        return  file_put_contents(HLEB_STORAGE_DIRECTORY . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . date('Y_m_d_') . $prefix . 'errors.log', $row . PHP_EOL, FILE_APPEND);
    }

}

