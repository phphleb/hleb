<?php

namespace Hleb\Main;

use Hleb\Constructor\TCreator;

class MainTemplate
{
    function __construct(string $path, array $template = [])
    {
        $time = microtime(true);

        $backtrace = $this->hl_debug_backtrace();

        (new TCreator(HLEB_GLOBAL_DIRECTORY . "/resources/views/" . trim($path, "/") . ".php", $template))->include();

        $time = microtime(true) - $time;

        Info::insert("Templates", trim($path, "/") . $backtrace . " load: " . (round($time, 4) * 1000) . " ms");
    }

    function hl_debug_backtrace()
    {
        if(!HLEB_PROJECT_DEBUG) return "";
        $trace = debug_backtrace(2,4);
        if(isset($trace[3])){
            $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[3]["file"] ?? ""));
            return  " (" . end($path) . " : " . ($trace[3]["line"] ?? "") . ")";
        }
        return "";
    }

}


