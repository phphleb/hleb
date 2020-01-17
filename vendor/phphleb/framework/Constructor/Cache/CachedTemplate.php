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

    /**
     * CachedTemplate constructor.
     * @param string $template
     * @param array $template_params
     */
    function __construct(string $template, array $template_params = [])
    {
        if (HLEB_PROJECT_DEBUG) {
            $backtrace = $this->hl_debug_backtrace();
            $time = microtime(true);
        }

        $this->templateParams = $template_params;
        $path_to_file = $this->hl_search_cache_file($template);
        $this->tempfile = HLEB_GLOBAL_DIRECTORY . '/resources/views/' . trim($template, '/') . '.php';
        if (is_null($path_to_file)) {
            ob_start();
            $this->hl_create_content();
            $this->hl_cache_template(ob_get_contents());
            ob_end_clean();
        } else {
            $this->content = file_get_contents($path_to_file, true);
        }
        $this->tempfile = $this->content;
        $this->hl_add_content();

        if (HLEB_PROJECT_DEBUG) {
            $time = microtime(true) - $time;
            Info::insert('Templates', trim($template, '/') . $backtrace . $this->info_cache() . ' load: ' .
                (round($time, 4) * 1000) . ' ms , ' . $this->hl_info_template_name() . '(...)');
        }
    }

    protected function hl_info_template_name(){
        return  'includeCachedTemplate';
    }

    protected function hl_template_area_key(){
        return  '';
    }

    private function hl_debug_backtrace()
    {
        $trace = debug_backtrace(2, 4);
        if (isset($trace[3])) {
            $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[3]['file'] ?? ''));
            return ' (' . end($path) . " : " . ($trace[3]['line'] ?? '') . ')';
        }
        return '';
    }

    private function hl_search_cache_file($template)
    {
        $path = HLEB_GLOBAL_DIRECTORY . '/storage/cache/templates/';

        $hash_params = count($this->templateParams) ? $this->acollmd5(json_encode($this->templateParams)) : '';

        $template_name = $this->acollmd5($template . Key::get() . $this->hl_template_area_key() . $hash_params);

        $dir =  substr($template_name, 0, 2);

        if(!file_exists($path . $dir)) mkdir($path . $dir);

        $this->hashfile = $path . $dir . "/" . $template_name;

        $search_all = glob($this->hashfile . '_*.txt');

        if ($search_all && count($search_all)) {

            if (count($search_all) > 1) {
                foreach ($search_all as $key => $search_file) {
                    if ($key > 0) unlink("$search_file");
                }
            }

            $s_file = $search_all[0];
            $this->cacheTime = $this->getFileTime($s_file);
            if (filemtime($s_file) >= time() - $this->cacheTime) {
                return $s_file;
            }

            unlink("$s_file");
        }
        return null;
    }

    private function acollmd5( string $str){
        return  empty($str) ? '' : md5($str) .  substr(md5(strrev($str)),0,5);
    }

    private function hl_cache_template($content)
    {
        if ($this->cacheTime === 0) {
            // Without caching.

            $this->content = $content;
            $this->hl_add_content();
        } else {

            $this->delOldFile();
            $this->content = $content;

            $file = $this->hashfile . '_' . $this->cacheTime . '.txt';
            file_put_contents($file, $content, LOCK_EX);
        }
        if (rand(0, 1000) === 0) $this->delOldFile();
    }

    private function delOldFile()
    {
        if (!isset($GLOBALS['HLEB_CACHED_TEMPLATES_CLEARED'])) {
            $path = HLEB_GLOBAL_DIRECTORY . '/storage/cache/templates/';
            $files = glob($path . '/*/*.txt');
            if ($files && count($files)) {
                foreach ($files as $key => $file) {
                    if (filemtime($file) < strtotime('-' . $this->getFileTime($file) . ' seconds')) {
                        unlink("$file");
                    }
                }
            }
            $directories = glob($path . '/*', GLOB_NOSORT);
            foreach($directories as $key => $directory) {
                if ([] === (array_diff(scandir($directory), array('.', '..')))) {
                    rmdir($directory);
                }
            }
            $GLOBALS['HLEB_CACHED_TEMPLATES_CLEARED'] = true;
        }
    }

    private function getFileTime($file)
    {
        return intval(explode('_', $file)[1]);
    }

    private function info_cache()
    {
        return ' cache ' . $this->cacheTime . ' s , ';
    }

    private function hl_add_content()
    {
        (new TCreator($this->tempfile, $this->templateParams))->print();
    }

    private function hl_create_content()
    {
        $this->cacheTime = (new TCreator($this->tempfile, $this->templateParams))->include();
    }

}


