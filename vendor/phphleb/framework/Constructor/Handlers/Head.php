<?php

declare(strict_types=1);

namespace Hleb\Constructor\Handlers;

use Hleb\Scheme\Home\Constructor\Handlers\ResourceStandard;

class Head extends ResourceStandard
{
    private $description = '';

    private $title = '';

    private $scripts = [];

    private $styles = [];

    private $meta = [];


    // add in controller

    public function addStyles(string $url)
    {
       $this->styles[$url]= $url;
    }

    public function addScript(string $url, string $attr = 'defer', string $charset = 'utf-8')
    {
        $this->scripts[$url] = ['url' => $url, 'charset' => $charset, 'attribute' => $attr];
    }

    public function setTitle(string $value)
    {
        $this->title = $value;
    }

    public function addMeta(string $name, $content)
    {
        $this->meta[$name] = $content;
    }

    public function setDescription(string $value)
    {
        $this->description = $value;
    }

    // add in <head>...</head> HTML tags

    public function output(bool $print = true, int $indents = 2)
    {
        $result = "\n";

        $ind = str_repeat(' ', $indents);

        if(!empty($this->title)){
            $result .= $ind . '<title>' . $this->convertPrivateTags($this->title) . '</title>' . "\n";
        }
        if(!empty($this->description)){
            $result .= $ind . '<meta name="description" content="' . $this->convertPrivateTags($this->description) . '" />' . "\n";
        }

        if(count($this->meta)){
            foreach($this->meta as $key => $value){
                $result .= $ind . "<meta name=\"$key\" content=\"" . $this->convertPrivateTags($value) . "\">" . "\n";
            }
        }

        if(count($this->styles)){
            foreach($this->styles as $style){
                $result .= $ind . '<link rel="stylesheet" href="' . $this->convertPrivateTags($style) . '" type="text/css" >' . "\n";
            }
        }

        if(count($this->scripts)){
            foreach($this->scripts as $script){
                $script = $this->convertPrivateTagsInArray($script);
                $result .= $ind . '<script ' . $script["attribute"] . ' src="' . $script["url"] . '" charset="' . $script["charset"] . '"></script>' . "\n";
            }
        }

        if($print) echo $result;
        return $result;
    }


}

