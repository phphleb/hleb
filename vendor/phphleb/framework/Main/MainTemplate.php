<?php

declare(strict_types=1);

/*
 * Using a template.
 *
 * Использование шаблона.
 */

namespace Hleb\Main;

use Hleb\Constructor\TCreator;

final class MainTemplate
{
    private $content = null;

    public function __construct(string $path, array $template = [], bool $return = false ) {
        if (HLEB_PROJECT_DEBUG_ON) {
            $time = microtime(true);
            $backtrace = $this->debugBacktrace();
        }
        $templateName = trim($path, '/\\') . '.php';
        $templateDirectory = $this->getTemplateDirectory($templateName);
        if($return) {
            $this->content = (new TCreator($templateDirectory, $template))->getString();
        } else {
            (new TCreator($templateDirectory, $template))->include();
        }
        if (HLEB_PROJECT_DEBUG_ON) {
            $time = microtime(true) - $time;
            Info::insert('Templates', (defined('HLEB_MODULE_NAME') ? ' module `' . HLEB_MODULE_NAME . '` ' : '') . $templateName . $backtrace . ' (includeTemplate) load: ' . (round($time, 4) * 1000) . ' ms');
        }
    }

    // Attempt to define a line in the content, which includes a template for output in the debug panel.
    // Попытка определения строки в контенте, в которой подключен шаблон для вывода в отладочной панели.
    public function debugBacktrace() {
        return hleb_debug_bugtrace(4);
    }

    // Return content.
    // Возвращает контент.
    public function getContent() {
        return $this->content;
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


