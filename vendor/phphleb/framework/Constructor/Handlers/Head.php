<?php

declare(strict_types=1);

/*
 * Outputting user data to the <head> ... </head> page.
 *
 * Вывод пользовательских данных в <head>...</head> страницы.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Scheme\Home\Constructor\Handlers\HeadInterface;
use Hleb\Scheme\Home\Constructor\Handlers\ResourceStandard;

class Head extends ResourceStandard implements HeadInterface
{
    private $description = '';

    private $title = '';

    private $scripts = [];

    private $styles = [];

    private $meta = [];

    /**
     * @inheritDoc
     */
    public function addStyles(string $url) {
        $this->styles[$url] = $url;
    }

    /**
     * @inheritDoc
     */
    public function addScript(string $url, string $attr = 'defer', string $charset = 'utf-8') {
        $this->scripts[$url] = ['url' => $url, 'charset' => $charset, 'attribute' => $attr];
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $value) {
        $this->title = $value;
    }

    /**
     * @inheritDoc
     */
    public function addMeta(string $name, $content) {
        $this->meta[$name] = $content;
    }

    /**
     * @inheritDoc
     */
    public function setDescription(string $value) {
        $this->description = $value;
    }

    /**
     * @inheritDoc
     */
    public function output(bool $print = true, int $indents = 2) {
        $result = PHP_EOL;
        $ind = str_repeat(' ', $indents);
        if (!empty($this->title)) {
            $result .= $ind . '<title>' . $this->convertPrivateTags($this->title) . '</title>' . PHP_EOL;
        }
        if (!empty($this->description)) {
            $result .= $ind . '<meta name="description" content="' . $this->convertPrivateTags($this->description) . '" />' . PHP_EOL;
        }
        if (count($this->meta)) {
            foreach ($this->meta as $key => $value) {
                $result .= $ind . "<meta name=\"$key\" content=\"" . $this->convertPrivateTags($value) . "\">" . PHP_EOL;
            }
        }
        if (count($this->styles)) {
            foreach ($this->styles as $style) {
                $result .= $ind . '<link rel="stylesheet" href="' . $this->convertPrivateTags($style) . '" type="text/css" >' . PHP_EOL;
            }
        }
        if (count($this->scripts)) {
            foreach ($this->scripts as $script) {
                $script = $this->convertPrivateTagsInArray($script);
                $result .= $ind . '<script ' . $script["attribute"] . ' src="' . $script["url"] . '" charset="' . $script["charset"] . '"></script>' . PHP_EOL;
            }
        }
        if ($print) echo $result;
        return $result;
    }

}

