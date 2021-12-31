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

    private $metaName = [];

    private $uniqueMetaList = [];

    /**
     * Loading CSS styles by URL. Pre-made in the controller.
     * @param string $url - direct or relative address of the resource.
     * @return Head
     *//**
     * Загрузка стилей CSS по URL. Производится предварительно в контроллере.
     * @param string $url - прямой или относитеьный адрес ресурса.
     * @return Head
     */
    public function addStyles(string $url) {
        $this->styles[$url] = $url;
        return $this;
    }

    /**
     * Loading JS scripts by URL. Pre-made in the controller.
     * @param string $url - direct or relative address of the resource.
     * @param string $attr - load type attribute.
     * @param string $charset - encoding.
     * @return Head
     *//**
     * Загрузка скриптов JS по URL. Производится предварительно в контроллере.
     * @param string $url - прямой или относитеьный адрес ресурса.
     * @param string $attr - атрибут типа загрузки.
     * @param  string $charset - кодировка.
     * @return Head
     */
    public function addScript(string $url, string $attr = 'defer', string $charset = '') {
        $this->scripts[$url] = ['url' => $url, 'charset' => $charset, 'attribute' => $attr];
        return $this;
    }

    /**
     * Sets the title of the page. Pre-made in the controller.
     * <title>{$value}</title>
     * @param string $value - title text.
     * @return Head
     *//**
     * Устанавливает заголовок страницы. Производится предварительно в контроллере.
     * <title>{$value}</title>
     * @param string $value - текст заголовка.
     * @return Head
     */
    public function setTitle(string $value) {
        $this->title = $value;
        return $this;
    }

    /**
     * Adds a custom meta post. Pre-made in the controller.
     * <meta name="{$data}" content="{$content}" />
     * or $data = ['key1' => 'value1', 'key2' => 'value2'] -> <meta key1="value1" key2="value2"/>
     * @param string|array $data
     * @param mixed $content
     * @return Head
     *//**
     * Добавляет произвольное мета-сообщение. Производится предварительно в контроллере.
     * <meta name="{$data}" content="{$content}" />
     * или $data = ['key1' => 'value1', 'key2' => 'value2'] -> <meta key1="value1" key2="value2"/>
     * @param string|array $data
     * @param string $content
     * @return Head
     */
    public function addMeta($data, $content = '') {
        if (is_array($data)) {
            return $this->addMetaFromParts($data);
        }
        $this->metaName[$data] = $content;
        return $this;
    }

    /**
     * Adds an arbitrary meta assembled from a variable list.
     * ['key1' => 'value1', 'key2' => 'value2'] -> <meta key1="value1" key2="value2"/>
     * @param array $list - named array 'tag' => 'value'
     * @return Head
     *//**
     * Добавляет произвольное мета-сообщение собранное из вариативного списка.
     * ['key1' => 'value1', 'key2' => 'value2'] -> <meta key1="value1" key2="value2"/>
     * @param array $list - именованный массив  'тег' => 'значение'
     * @return Head
     */
    public function addMetaFromParts(array $list) {
        $this->uniqueMetaList[] = $list;
        return $this;
    }

    /**
     * Sets the page description. Pre-made in the controller.
     * <meta name="description" content="{$value}" />
     * @param string $value - a short description (or annotation) of the page.
     * @return Head
     *//**
     * Устанавливает описание страницы. Производится предварительно в контроллере.
     * <meta name="description" content="{$value}" />
     * @param string $value - краткое описание (или аннотация) страницы.
     * @return Head
     */
    public function setDescription(string $value) {
        $this->description = $value;
        return $this;
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
        foreach ($this->metaName as $key => $value) {
            $result .= $ind . "<meta name=\"" . $this->convertPrivateTags($key) . "\" content=\"" . $this->convertPrivateTags($value) . "\">" . PHP_EOL;
        }
        foreach ($this->uniqueMetaList as $list) {
            $result .= $ind . "<meta";
            foreach ($list as $key => $value) {
                $result .= " " . $this->convertPrivateTags($key) . "=\"" . $this->convertPrivateTags($value) . "\"";
            }
            $result .= ">" . PHP_EOL;
        }
        foreach ($this->styles as $style) {
            $result .= $ind . '<link rel="stylesheet" href="' . $this->convertPrivateTags($style) . '" type="text/css" >' . PHP_EOL;
        }
        foreach ($this->scripts as $script) {
            $script = $this->convertPrivateTagsInArray($script);
            $result .= $ind . '<script ' . $script["attribute"] . ' src="' . $script["url"] . (!empty($script["charset"]) ? '" charset="' . $script["charset"] : '') . '"></script>' . PHP_EOL;
        }
        if ($print) echo $result;
        return $result;
    }

}

