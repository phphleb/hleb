<?php

declare(strict_types=1);

/*
 * Loading the assigned resources at the bottom of the <body>...</body> block.
 *
 * Загрузка назначенных ресурсов в нижней части блока <body>...</body>.
 */

namespace Hleb\Constructor\Handlers;

use Hleb\Scheme\Home\Constructor\Handlers\ResourceStandard;

class Resources extends ResourceStandard
{
    protected $bottomScripts = [];

    protected $bottomStyles = [];

    protected $bottomScriptsOnce = false;

    protected $bottomStylesOnce = false;

    /**
     * Adds loading JS script.
     * @param string $url - the address of the loaded resource.
     * @param string $charset - encoding.
     *//**
     * Добавляет загрузку скрипта JS.
     * @param string $url - адрес подгружаемого ресурса.
     * @param string $charset - кодировка.
     */
    public function addBottomScript(string $url, string $charset = '') {
        $this->bottomScripts[$url] = ['url' => $url, 'charset' => $charset];
    }

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
    public function getBottomScripts(int $indents = 2) {
        $result = PHP_EOL;
        $this->bottomScriptsOnce = true;
        foreach ($this->bottomScripts as $script) {
            $script = $this->convertPrivateTagsInArray($script);
            $result .= str_repeat(' ', $indents) . '<script src="' . $script['url'] . (!empty($script['charset']) ? '" charset="' . $script['charset']  : '') . '"></script>' . PHP_EOL;
        }
        return $result;
    }

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
    public function getBottomScriptsOnce(int $indents = 2) {
        if ($this->bottomScriptsOnce) return null;
        $this->bottomScriptsOnce = true;
        return $this->getBottomScripts($indents);
    }

    /**
     * Adds loading CSS styles.
     * @param string $url - the address of the loaded resource.
     * @param string $media - media attribute that applies the linked resource.
     *//**
     * Добавляет загрузку CSS-стилей.
     * @param string $url - адрес подгружаемого ресурса.
     * @param string $media - медиа-атрибут, который применяет связываемый ресурс.
     */
    public function addBottomStyles(string $url, string $media = '') {
        $this->bottomStyles[$url] = ['url' => $url, 'media' => $media];
    }

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
    public function getBottomStyles(int $indents = 2) {
        $result = PHP_EOL;
        foreach ($this->bottomStyles as $style) {
            $style = $this->convertPrivateTagsInArray($style);
            $result .= str_repeat(' ', $indents) . '<link rel="stylesheet" href="' . $style['url'] . '" type="text/css"'  . (!empty($style['media']) ? ' media="' . $style['media'] . '"' : '') . '>' . PHP_EOL;
        }
        return $result;
    }

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
    public function getBottomStylesOnce(int $indents = 2) {
        if ($this->bottomStylesOnce) return null;
        $this->bottomStylesOnce = true;
        return $this->getBottomStyles($indents);
    }
}

