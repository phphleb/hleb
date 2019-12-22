<?php

declare(strict_types=1);

namespace Hleb\Main;

use Hleb\Constructor\TCreator;

class MainTemplate
{
    public function __construct(string $path, array $template = [])
    {
        if(HLEB_PROJECT_DEBUG){
            $time = microtime(true);
            $backtrace = $this->hl_debug_backtrace();
        }

        (new TCreator(HLEB_GLOBAL_DIRECTORY . '/resources/views/' . trim($path, '/') . '.php', $template))->include();

        if(HLEB_PROJECT_DEBUG) {
            $time = microtime(true) - $time;
            Info::insert('Templates', trim($path, '/') . $backtrace . ' load: ' . (round($time, 4) * 1000) . ' ms');
        }
    }

    public function hl_debug_backtrace()
    {
        $trace = debug_backtrace(2,4);
        if(isset($trace[3])){
            $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[3]['file'] ?? ''));
            return  ' (' . end($path) . " : " . ($trace[3]['line'] ?? '') . ')';
        }
        return '';
    }

}


