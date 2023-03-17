<?php

declare(strict_types=1);


namespace Hleb\Main\Insert\Examples;

/**
 * @see ExampleMirrorRequest
 */
class Resources
{
    private $resources = [];

    /**
     * @param array $list -  Adding test values to be returned in methods.
     *
     *                    -  Добавление тестовых значений, которые будут возвращены в методах.
     */
    public function __construct(array $list)
    {
        $this->resources = $list;
    }

    public function addBottomScript(string $url, string $charset = '')
    {
        return;
    }

    public function getBottomScripts(int $indents = 2)
    {
        return $this->resources['addBottomScript'];
    }

    public function getBottomScriptsOnce(int $indents = 2)
    {
        return $this->resources['getBottomScriptsOnce'];
    }

    public function addBottomStyles(string $url, string $media = '')
    {
        return;
    }

    public function getBottomStyles(int $indents = 2)
    {
        return $this->resources['getBottomStyles'];
    }

    public function getBottomStylesOnce(int $indents = 2)
    {
        return $this->resources['getBottomStylesOnce'];
    }
}

