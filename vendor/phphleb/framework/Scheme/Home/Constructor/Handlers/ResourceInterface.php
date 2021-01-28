<?php


namespace Hleb\Scheme\Home\Constructor\Handlers;


interface ResourceInterface
{
    /**
     * Adds loading JS script.
     * @param string $url - the address of the loaded resource.
     * @param string $charset - encoding.
     *//**
     * Добавляет загрузку скрипта JS.
     * @param string $url - адрес подгружаемого ресурса.
     * @param string $charset - кодировка.
     */
    public function addBottomScript(string $url, string $charset = 'utf-8');

    /**
     * Outputting blocks previously assigned via Request::getResources()->addBottomScript(...).
     * You need to place this output via `print getRequestResources()->getBottomScripts()` at the bottom of the <body> ... </body> block.
     * @param int $indents - number of spaces before inserted blocks.
     * @return string
     *//**
     * Вывод блоков, ранее назначенных через Request::getResources()->addBottomScript(...).
     * Необходимо разместить данный вывод через `print getRequestResources()->getBottomScripts()` в нижней части блока <body>...</body>.
     * @param int $indents - количество пробелов перед вставляемыми блоками.
     * @return string
     */
    public function getBottomScripts(int $indents = 2);

    /**
     * Displays the blocks previously assigned via Request::getResources()->addBottomScript(...).
     * You need to place this output via `print getRequestResources()->getBottomScriptsOnce()` at the bottom of the <body> ... </body> block.
     * @param int $indents - number of spaces before inserted blocks.
     * @return string|null
     *//**
     * Единоразово выводит блоки, ранее назначенные через Request::getResources()->addBottomScript(...).
     * Необходимо разместить данный вывод через `print getRequestResources()->getBottomScriptsOnce()` в нижней части блока <body>...</body>.
     * @param int $indents - количество пробелов перед вставляемыми блоками.
     * @return string|null
     */
    public function getBottomScriptsOnce(int $indents = 2);

    /**
     * Adds loading CSS styles.
     * @param string $url - the address of the loaded resource.
     *//**
     * Добавляет загрузку CSS-стилей.
     * @param string $url - адрес подгружаемого ресурса.
     */
    public function addBottomStyles(string $url);

    /**
     * Outputting blocks previously assigned via Request::getResources()->addBottomStyles(...).
     * You need to place this output via `print getRequestResources()->getBottomStyles()` at the bottom of the <body> ... </body> block.
     * @param int $indents - number of spaces before inserted blocks.
     * @return string
     *//**
     * Вывод блоков, ранее назначенных через Request::getResources()->addBottomStyles(...).
     * Необходимо разместить данный вывод через `print getRequestResources()->getBottomStyles()` в нижней части блока <body>...</body>.
     * @param int $indents - количество пробелов перед вставляемыми блоками.
     * @return string
     */
    public function getBottomStyles(int $indents = 2);

    /**
     * Displays the blocks previously assigned via Request::getResources()->addBottomStyles(...).
     * You need to place this output via `print getRequestResources()->getBottomStylesOnce()` at the bottom of the <body> ... </body> block.
     * @param int $indents - number of spaces before inserted blocks.
     * @return string|null
     *//**
     * Единоразово выводит блоки, ранее назначенные через Request::getResources()->addBottomStyles(...).
     * Необходимо разместить данный вывод через `print getRequestResources()->getBottomStylesOnce()` в нижней части блока <body>...</body>.
     * @param int $indents - количество пробелов перед вставляемыми блоками.
     * @return string|null
     */
    public function getBottomStylesOnce(int $indents = 2);

}