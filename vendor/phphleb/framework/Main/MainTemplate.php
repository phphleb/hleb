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
            $backtrace = $this->debugBacktrace();
        }
        $templateName = trim($path, '/') . '.php';

        $templateDirectory = $this->getTemplateDirectory($templateName);

        print $templateName;

        (new TCreator($templateDirectory, $template))->include();

        if(HLEB_PROJECT_DEBUG) {
            $time = microtime(true) - $time;
            Info::insert('Templates', trim($path, '/') . $backtrace . ' load: ' . (round($time, 4) * 1000) . ' ms');
        }
    }

    public function debugBacktrace()
    {
        $trace = debug_backtrace(2,4);
        if(isset($trace[3])){
            $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[3]['file'] ?? ''));
            return  ' (' . end($path) . " : " . ($trace[3]['line'] ?? '') . ')';
        }
        return '';
    }

    private function getTemplateDirectory($templateName)
    {
        if(file_exists(HLEB_GLOBAL_DIRECTORY . '/modules/' . $templateName)){
            return HLEB_GLOBAL_DIRECTORY . '/modules/' . $templateName;
        }
        return HLEB_GLOBAL_DIRECTORY . '/resources/views/' . $templateName;
    }

}


