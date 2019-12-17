<?php

namespace Hleb\Constructor\Handlers;

use Hleb\Scheme\Home\Constructor\Handlers\ResourceStandard;

class Resources extends ResourceStandard
{
    protected $bottom_scripts = [];

    protected $bottom_styles = [];

    protected $bottom_scripts_once = false;

    protected $bottom_styles_once = false;


    function addBottomScript(string $url, string $charset = 'utf-8')
    {
        $this->bottom_scripts[$url] = ['url' => $url, 'charset' => $charset];
    }

    // Print to bottom of page
    function getBottomScripts(int $indents = 2)
    {
        $result = "\n";
        foreach($this->bottom_scripts as $script){
            $script = $this->convertPrivateTagsInArray($script);
            $result .= str_repeat(' ', $indents) . '<script src="' . $script["url"] . '" charset="' . $script["charset"] . '"></script>' . "\n";
        }

        return $result;
    }

    // Once displayed at the end of the page
    function getBottomScriptsOnce(int $indents = 2)
    {
        if($this->bottom_scripts_once) return null;

        $this->bottom_scripts_once = true;
        return self::getBottomScripts($indents);
    }

    // Print to bottom of page
    function addBottomStyles(string $url)
    {
        $this->bottom_styles[$url] =  $url;
    }

    // Print to bottom of page
    function getBottomStyles(int $indents = 2)
    {
        $result = "\n";
        foreach($this->bottom_styles as $style){
            $result .= str_repeat(' ', $indents) . '<link rel="stylesheet" href="' . $this->convertPrivateTags($style) . '" type="text/css" media="screen">' . "\n";
        }

        return $result;
    }

    // Once displayed at the end of the page
    function getBottomStylesOnce(int $indents = 2)
    {
        if($this->bottom_styles_once) return null;

        $this->bottom_styles_once = true;
        return self::getBottomStyles($indents);
    }
}

