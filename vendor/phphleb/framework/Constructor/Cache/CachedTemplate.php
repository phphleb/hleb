<?php

declare(strict_types=1);

namespace Hleb\Constructor\Cache;

use Hleb\Constructor\Handlers\Key;

use Hleb\Constructor\TCreator;

use Hleb\Main\Info;

class CachedTemplate
{
    protected $templateParams = [];

    private $cacheTime = 0;

    private $content = null;

    private $hashfile = null;

    private $tempfile = null;
    
    private $dir = null;

    private $data = null;

    /**
     * CachedTemplate constructor.
     * @param string $path
     * @param array $templateParams
     */
    function __construct(string $path, array $templateParams = [])
    {
        if (HLEB_PROJECT_DEBUG) {
            $backtrace = $this->debugBacktrace();
            $time = microtime(true);
        }
        $templateName = trim($path, '/') . '.php';

        $templateDirectory = $this->getTemplateDirectory($templateName);

        $this->templateParams = $templateParams;
        $pathToFile = $this->searcCacheFile($path);
        $this->tempfile = $templateDirectory;
        if (is_null($pathToFile)) {
            ob_start();
            $this->createContent();
            $this->cacheTemplate(ob_get_contents());
            ob_end_clean();
        } else {
            $this->content = file_get_contents($pathToFile, true);
        }
        $this->data = $this->content;
        $this->addContent();

        if (HLEB_PROJECT_DEBUG) {
            $time = microtime(true) - $time;
            Info::insert('Templates', trim($path, '/') . $backtrace . $this->infoCache() . ' load: ' .
                (round($time, 4) * 1000) . ' ms , ' . $this->infoTemplateName() . '(...)');
        }
    }

    protected function infoTemplateName(){
        return  'includeCachedTemplate';
    }

    protected function templateAreaKey(){
        return  '';
    }

    private function debugBacktrace()
    {
        $trace = debug_backtrace(2, 4);
        if (isset($trace[3])) {
            $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[3]['file'] ?? ''));
            return ' (' . end($path) . " : " . ($trace[3]['line'] ?? '') . ')';
        }
        return '';
    }

    private function searcCacheFile($template)
    {
        $path = HLEB_GLOBAL_DIRECTORY . HLEB_TEMPLATE_CACHED_PATH . '/';

        $hash_params = count($this->templateParams) ? $this->acollmd5(json_encode($this->templateParams)) : '';

        $template_name = $this->acollmd5($template . Key::get() . $this->templateAreaKey() . $hash_params);

        $this->dir =  substr($template_name, 0, 2);

        $this->hashfile = $path . $this->dir . "/" . $template_name;

        $search_all = glob($this->hashfile . '_*.txt');

        if ($search_all && count($search_all)) {

            if (count($search_all) > 1) {
                foreach ($search_all as $key => $search_file) {
                    if ($key > 0) @unlink("$search_file");
                }
            }

            $s_file = $search_all[0];
            $this->cacheTime = $this->getFileTime($s_file);
            if (filemtime($s_file) >= time() - $this->cacheTime) {
                return $s_file;
            }

            @unlink("$s_file");
        }
        return null;
    }

    private function acollmd5( string $str){
        return  empty($str) ? '' : md5($str) .  substr(md5(strrev($str)),0,5);
    }

    private function cacheTemplate($content)
    {          
        if ($this->cacheTime === 0) {

            // Without caching.
            $this->data = $content;
            $this->addContent();

        } else {

            $this->deleteOldFile();
            mkdir(HLEB_GLOBAL_DIRECTORY . HLEB_TEMPLATE_CACHED_PATH . '/' . $this->dir, 0777, true);
            $this->content = $content;                        
            $file = $this-> hashfile . '_' . $this->cacheTime . '.txt';
            file_put_contents($file, $content, LOCK_EX);

        }
        if (rand(0, 1000) === 0) $this->deleteOldFile();
    }

    private function deleteOldFile()
    {
        if (!isset($GLOBALS['HLEB_CACHED_TEMPLATES_CLEARED'])) {
            $path = HLEB_GLOBAL_DIRECTORY . HLEB_TEMPLATE_CACHED_PATH;
            $files = glob($path . '/*/*.txt');
            if ($files && count($files)) {
                foreach ($files as $key => $file) {
                    if (filemtime($file) < strtotime('-' . $this->getFileTime($file) . ' seconds')) {
                        @unlink("$file");
                    }
                }
            }
            $directories = glob($path . '/*', GLOB_NOSORT);
            foreach($directories as $key => $directory) {
                if(!file_exists($directory)) break;
                if ([] === (array_diff(scandir($directory), array('.', '..')))) {
                    @rmdir($directory);
                }
            }
            $GLOBALS['HLEB_CACHED_TEMPLATES_CLEARED'] = true;
        }
    }

    private function getFileTime($file)
    {
        return intval(explode('_', $file)[1]);
    }

    private function infoCache()
    {
        return ' cache ' . $this->cacheTime . ' s , ';
    }

    private function addContent()
    {
        (new TCreator($this->data, $this->templateParams))->print();
    }

    private function createContent()
    {
        $this->cacheTime = (new TCreator($this->tempfile, $this->templateParams))->include();
    }

    private function getTemplateDirectory($templateName)
    {
        if(file_exists(HLEB_GLOBAL_DIRECTORY . '/modules/' . $templateName)){
            return HLEB_GLOBAL_DIRECTORY . '/modules/' . $templateName;
        }
        return HLEB_GLOBAL_DIRECTORY . '/resources/views/' . $templateName;
    }

}


