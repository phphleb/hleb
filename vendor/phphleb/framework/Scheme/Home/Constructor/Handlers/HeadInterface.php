<?php


namespace Hleb\Scheme\Home\Constructor\Handlers;


interface HeadInterface
{
    /**
     * Loading CSS styles by URL. Pre-made in the controller.
     * @param string $url - direct or relative address of the resource.
     *//**
     * Загрузка стилей CSS по URL. Производится предварительно в контроллере.
     * @param string $url - прямой или относитеьный адрес ресурса.
     */
    public function addStyles(string $url);

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
    public function addScript(string $url, string $attr = 'defer', string $charset = 'utf-8');

    /**
     * Sets the title of the page. Pre-made in the controller.
     * <title>{$value}</title>
     * @param string $value - title text.
     *//**
     * Устанавливает заголовок страницы. Производится предварительно в контроллере.
     * <title>{$value}</title>
     * @param string $value - текст заголовка.
     */
    public function setTitle(string $value);

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
    public function addMeta(string $name, $content);

    /**
     * Sets the page description. Pre-made in the controller.
     * <meta name="description" content="{$value}" />
     * @param string $value - a short description (or annotation) of the page.
     *//**
     * Устанавливает описание страницы. Производится предварительно в контроллере.
     * <meta name="description" content="{$value}" />
     * @param string $value - краткое описание (или аннотация) страницы.
     */
    public function setDescription(string $value);

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
    public function output(bool $print = true, int $indents = 2);




}

