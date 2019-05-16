<?php

namespace Hleb\Main;

use Hleb\Constructor\Handlers\Key;

use Hleb\Constructor\TCreator;

class CachedTemplate
{
    private $casheTime = 0;

    private $templateParams = [];

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
        $backtrace = $this->hl_debug_backtrace();
        $this->templateParams = $template_params;
        $time = microtime(true);

        $path_to_file = $this->hl_search_cache_file($template);
        $this->tempfile = HLEB_GLOBAL_DIRECTORY . "/resources/views/" . trim($template, "/") . ".php";
        if ($path_to_file == null) {
            ob_start();
            $this->hl_create_content();
            $this->hl_cache_template(ob_get_contents());
            ob_end_clean();
        } else {
            $this->content = file_get_contents($path_to_file, true);
        }
        $this->tempfile = $this->content;
        $this->hl_add_content();
        $time = microtime(true) - $time;
        Info::insert("Templates", trim($template, "/") . $backtrace . $this->info_cache() . " load: " .
            (round($time, 4) * 1000) . " ms" .  ", includeCachedTemplate(...)");
    }


    private function hl_debug_backtrace()
    {
        if (!HLEB_PROJECT_DEBUG) return "";
        $trace = debug_backtrace(2, 4);
        if (isset($trace[3])) {
            $path = explode(HLEB_GLOBAL_DIRECTORY, ($trace[3]["file"] ?? ""));
            return " (" . end($path) . " : " . ($trace[3]["line"] ?? "") . ")";
        }
        return "";
    }

    private function hl_search_cache_file($template)
    {
        $path = HLEB_GLOBAL_DIRECTORY . "/storage/cache/templates/";

        $template_name = md5($template . Key::get() . session_id());

        $this->hashfile = $path . $template_name;

        $search_all = glob($this->hashfile . "_*.txt");

        if ($search_all && count($search_all)) {

            if (count($search_all) > 1) {
                foreach ($search_all as $key => $search_file) {
                    if ($key > 0) unlink("$search_file");
                }
            }

            $s_file = $search_all[0];
            if (filemtime($s_file) >= time() - $this->getFileTime($s_file)) {
                return $s_file;
            }

            unlink("$s_file");
        }
        return null;
    }

    private function hl_cache_template($content)
    {
        if ($this->casheTime === 0) {
            // Without caching.

            $this->content = $content;
            $this->hl_add_content();
        } else {

            $this->delRandOldFile();
            $this->content = $content;

            $file = $this->hashfile . "_" . $this->casheTime . ".txt";
            file_put_contents($file, $content, LOCK_EX);
        }
        if (rand(0, 50) === 0) $this->delRandOldFile();
    }

    private function delRandOldFile()
    {
        $path = HLEB_GLOBAL_DIRECTORY . "/storage/cache/templates/";
        $files = glob($path . "*.txt");
        if ($files && count($files)) {
            $file = $files[array_rand($files)];
            if (filemtime($file) < strtotime('-' . $this->getFileTime($file) . ' seconds')) {
                unlink("$file");
            }
        }
    }

    private function getFileTime($file)
    {
        return intval(explode('_', $file)[1]);
    }

    private function info_cache()
    {
        return " cache " . $this->casheTime . " s , ";
    }

    private function hl_add_content()
    {
        (new TCreator($this->tempfile, $this->templateParams))->print();
    }

    private function hl_create_content()
    {
        $this->casheTime = (new TCreator($this->tempfile, $this->templateParams))->include();
    }

}


