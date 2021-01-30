<?php

declare(strict_types=1);

/*
 * Outputting user data to the <head> ... </head> page.
 *
 * Вывод пользовательских данных в <head>...</head> страницы.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Scheme\Home\Constructor\Handlers\ResourceStandard;

class Head extends ResourceStandard
{
    private $description = '';

    private $title = '';

    private $scripts = [];

    private $styles = [];

    private $meta = [];

    /**
     * Loading CSS styles by URL. Pre-made in the controller.
     * @param string $url - direct or relative address of the resource.
     *//**
     * Загрузка стилей CSS по URL. Производится предварительно в контроллере.
     * @param string $url - прямой или относитеьный адрес ресурса.
     */
    public function addStyles(string $url) {
        $this->styles[$url] = $url;
    }

    /**
     * Loading JS scripts by URL. Pre-made in the controller.
     * @param string $url - direct or relative address of the resource.
     * @param string $attr - load type attribute.
     * @param string $charset - encoding.
     *//**
     * Загрузка скриптов JS по URL. Производится предварительно в контроллере.
     * @param string $url - прямой или относитеьный адрес ресурса.
     * @param string $attr - атрибут типа загрузки.
     * @param string $charset - кодировка.
     */
    public function addScript(string $url, string $attr = 'defer', string $charset = 'utf-8') {
        $this->scripts[$url] = ['url' => $url, 'charset' => $charset, 'attribute' => $attr];
    }

    /**
     * Sets the title of the page. Pre-made in the controller.
     * <title>{$value}</title>
     * @param string $value - title text.
     *//**
     * Устанавливает заголовок страницы. Производится предварительно в контроллере.
     * <title>{$value}</title>
     * @param string $value - текст заголовка.
     */
    public function setTitle(string $value) {
        $this->title = $value;
    }

    /**
     * Adds a custom meta post. Pre-made in the controller.
     * <meta name="{$name}" content="{$content}" />
     * @param string $name
     * @param mixed $content
     *//**
     * Добавляет произвольное мета-сообщение. Производится предварительно в контроллере.
     * <meta name="{$name}" content="{$content}" />
     * @param string $name
     * @param mixed $content
     */
    public function addMeta(string $name, $content) {
        $this->meta[$name] = $content;
    }

    /**
     * Sets the page description. Pre-made in the controller.
     * <meta name="description" content="{$value}" />
     * @param string $value - a short description (or annotation) of the page.
     *//**
     * Устанавливает описание страницы. Производится предварительно в контроллере.
     * <meta name="description" content="{$value}" />
     * @param string $value - краткое описание (или аннотация) страницы.
     */
    public function setDescription(string $value) {
        $this->description = $value;
    }

    /**
     * Displays data when installed in the page <head>...</head>.
     * @param bool $print - whether to display the result.
     * @param int $indents - the number of spaces before the inserted blocks.
     * @return string
     *//**
     * Выводит данные при установке в <head>...</head> страницы.
     * @param bool $print - нужно ли отобразить результат.
     * @param int $indents - количество пробелов перед вставляемыми блоками.
     * @return string
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

