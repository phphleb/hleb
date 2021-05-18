<?php

declare(strict_types=1);

/*
 * Using a cached template with caching times in the pasted content.
 *
 * Использование кешируемого шаблона с заданем времени кеширования в подключаемом контенте.
 */

namespace Hleb\Constructor\Cache;

use Hleb\Constructor\Handlers\Key;
use Hleb\Constructor\TCreator;
use Hleb\Main\Info;

class CachedTemplate
{
    protected $templateParams = [];

    private $cacheTime = 0;

    private $content = null;

    private $hashFile = null;

    private $tempFile = null;

    private $dir = null;

    private $data = null;

    /**
     * Creation of a cached template with a set caching time in the connected content.
     * You cannot specify the path to the template engine file (for example, `Twig`),
     * only to the .php executable file without specifying the extension.
     * @param string $path - abbreviated path to the content file in the `view` folder or,
     * if the module exists, the $path must contain `<module_name>/<path_to_content_file>`.
     * @param array $templateParams - an array of parameters, where the keys are the names of the variables
     * given to the content with the corresponding values.
     */
    /**
     * Создание кешируемого шаблона с заданием времени кеширования в подключаемом контенте.
     * Нельзя указать путь до файла шаблонизатора (например, Twig),
     * только до исполняемого файла .php без указания расширения.
     * @param string $path - сокращенный путь к файлу контента в папке `view` или,
     * при существовании модуля, параметр $path должен содержать `<module_name>/<path_to_content_file>`.
     * @param array $templateParams - массив параметров, где ключи представляют собой названия отдаваемых
     * в контент переменных с соответствующими значениями.
     */
    public function __construct(string $path, array $templateParams = []) {
        $backtrace = null;
        $time = 0;
        if (HLEB_PROJECT_DEBUG_ON) {
            $backtrace = $this->debugBacktrace();
            $time = microtime(true);
        }
        $templateName = trim($path, '/\\') . '.php';
        $templateDirectory = $this->getTemplateDirectory($templateName);
        $this->templateParams = $templateParams;
        $pathToFile = $this->searchCacheFile($path);
        $this->tempFile = $templateDirectory;
        if (is_null($pathToFile)) {
            ob_start();
            $this->createContent();
            $this->cacheTemplate(ob_get_contents());
            ob_end_clean();
        } else {
            $this->content = file_get_contents($pathToFile, true);
        }
        if ($this->cacheTime !== 0) {
            $this->data = $this->content;
        }
        $this->addContent();
        if (HLEB_PROJECT_DEBUG_ON) {
            $time = microtime(true) - $time;
            Info::insert('Templates', trim($path, '/') . $backtrace . $this->infoCache() . ' load: ' .
                (round($time, 4) * 1000) . ' ms , ' . $this->infoTemplateName() . '(...)');
        }
    }

    // The name of the action to be displayed in the debug panel
    // Возвращает название действия для вывода в отладочной панели
    protected function infoTemplateName() {
        return 'includeCachedTemplate';
    }

    //Returns an empty string. This means that the current template is not unique to the user.
    // Возвращает пустую строку. Это значит, что текущий шаблон неуникален для пользователя.
    protected function templateAreaKey() {
        return '';
    }

    // Attempt to define a line in the content, which includes a template for output in the debug panel.
    // Попытка определения строки в контенте, в которой подключен шаблон для вывода в отладочной панели.
    private function debugBacktrace() {
        $trace = debug_backtrace(2, 4);
        if (isset($trace[3])) {
            $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[3]['file'] ?? ''));
            return ' (' . end($path) . " : " . ($trace[3]['line'] ?? '') . ')';
        }
        return '';
    }

    // Search for a template in the cache, returning the path to the file if successful, or `null` otherwise.
    // Поиск шаблона в кеше с возвращением пути до файла в случае успеха или `null` в противном случае.
    private function searchCacheFile($template) {
        $path = HLEB_GLOBAL_DIRECTORY . HLEB_TEMPLATE_CACHED_PATH . '/';
        $hashParams = count($this->templateParams) ? $this->avoidingOfCollisionsMd5(json_encode($this->templateParams)) : '';
        $templateName = $this->avoidingOfCollisionsMd5($template . Key::get() . $this->templateAreaKey() . $hashParams);
        $this->dir = substr($templateName, 0, 2);
        $this->hashFile = $path . $this->dir . "/" . $templateName;
        $searchAll = glob($this->hashFile . '_*.cache');

        if (is_array($searchAll) && count($searchAll)) {
            if (count($searchAll) > 1) {
                foreach ($searchAll as $key => $search_file) {
                    if ($key > 0) @unlink("$search_file");
                }
            }
            $searchFile = $searchAll[0];
            $this->cacheTime = $this->getFileTime($searchFile);
            $period = intval(time() - filemtime($searchFile));
            if ($this->cacheTime >= $period) {
                if ($this->cacheTime > 3 &&
                    (($this->cacheTime - 2 == $period && rand(0, 10) === 2) ||
                        ($this->cacheTime - 1 == $period && rand(0, 5) === 2))) {
                    // Pre-warming the cache.
                    // Предварительный прогрев кеша.
                } else {
                    return $searchFile;
                }
            }
            @unlink("$searchFile");
        }
        return null;
    }

    // Reduces the likelihood of a match occurring. Returns the converted string.
    // Уменьшает вероятность возникновения совпадения. Возвращает преобразованную строку.
    private function avoidingOfCollisionsMd5(string $str) {
        return empty($str) ? '' : md5($str) . substr(md5(strrev($str)), 0, 5);
    }

    // Caches content.
    // Кеширует контент.
    private function cacheTemplate($content) {
        if ($this->cacheTime === 0) {
            // Without caching.
            $this->data = $content;
            $this->addContent();
        } else {
            $this->deleteOldFiles();
            @mkdir(HLEB_GLOBAL_DIRECTORY . HLEB_TEMPLATE_CACHED_PATH . '/' . $this->dir, 0775, true);
            $this->content = $content;
            $file = $this->hashFile . '_' . strval($this->cacheTime) . '.cache';
            file_put_contents($file, $content, LOCK_EX);
            @chmod($file, 0775);
        }
        if (rand(0, 1000) === 0) $this->deleteOldFiles();
    }

    // Performs a one-time deletion of obsolete cache files.
    // Выполняет единократное удаление устаревших файлов кеша.
    private function deleteOldFiles() {
        if (!isset($GLOBALS['HLEB_CACHED_TEMPLATES_CLEARED'])) {
            $path = HLEB_GLOBAL_DIRECTORY . HLEB_TEMPLATE_CACHED_PATH;
            $files = glob($path . '/*/*.cache');
            if (is_array($files) && count($files)) {
                foreach ($files as $key => $file) {
                    if (filemtime($file) < strtotime('-' . $this->getFileTime($file) . ' seconds')) {
                        @unlink("$file");
                    }
                }
            }
            $directories = glob($path . '/*', GLOB_NOSORT);
            foreach ($directories as $key => $directory) {
                if (!file_exists($directory)) break;
                $listDirectory = scandir($directory);
                if ([] === (array_diff((is_array($listDirectory)? $listDirectory : []), ['.', '..']))) {
                    @rmdir($directory);
                }
            }
            $GLOBALS['HLEB_CACHED_TEMPLATES_CLEARED'] = true;
        }
    }

    // Returns the file caching time in seconds.
    // Возвращает время кеширования файла в секундах.
    private function getFileTime($file) {
        return intval(explode('_', $file)[1]);
    }

    // Returns a standardized string with template cache times.
    // Возвращает стандартизированную строку с временем кеширования шаблона.
    private function infoCache() {
        return ' cache ' . strval($this->cacheTime) . ' s , ';
    }

    // Display content data.
    // Отображение данных контента.
    private function addContent() {
       print (new TCreator($this->data, $this->templateParams));
    }

    // Displaying data from a cached file.
    // Отображение данных из закешированного файла.
    private function createContent() {
        $this->cacheTime = (new TCreator($this->tempFile, $this->templateParams))->include();
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


