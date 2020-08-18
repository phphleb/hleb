<?php

declare(strict_types=1);

/*
 * Using a template.
 *
 * Использование шаблона.
 */

namespace Hleb\Main;

use Hleb\Constructor\TCreator;

class MainTemplate
{
    public function __construct(string $path, array $template = []) {
        if (HLEB_PROJECT_DEBUG) {
            $time = microtime(true);
            $backtrace = $this->debugBacktrace();
        }
        $templateName = trim($path, '/\\') . '.php';
        $templateDirectory = $this->getTemplateDirectory($templateName);
        (new TCreator($templateDirectory, $template))->include();
        if (HLEB_PROJECT_DEBUG) {
            $time = microtime(true) - $time;
            Info::insert('Templates', trim($path, '/') . $backtrace . ' load: ' . (round($time, 4) * 1000) . ' ms');
        }
    }

    // Attempt to define a line in the content, which includes a template for output in the debug panel.
    // Попытка определения строки в контенте, в которой подключен шаблон для вывода в отладочной панели.
    public function debugBacktrace() {
        $trace = debug_backtrace(2, 4);
        if (isset($trace[3])) {
            $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[3]['file'] ?? ''));
            return ' (' . end($path) . " : " . ($trace[3]['line'] ?? '') . ')';
        }
        return '';
    }

    // Finds and returns the directory of the content file. The search depends on the module matching the condition.
    // Ищет и возвращает директорию файла с контентом. Поиск зависит от подходящего под условие модуля.
    private function getTemplateDirectory($templateName) {
        if (defined('HLEB_OPTIONAL_MODULE_SELECTION') && HLEB_OPTIONAL_MODULE_SELECTION) {
            if (file_exists(HLEB_GLOBAL_DIRECTORY . '/modules/' . $templateName)) {
                return HLEB_GLOBAL_DIRECTORY . '/modules/' . $templateName;
            }
            return HLEB_GLOBAL_DIRECTORY . '/modules/' . HLEB_MODULE_NAME . "/" . $templateName;
        }
        return HLEB_GLOBAL_DIRECTORY . '/resources/views/' . $templateName;
    }

}


