<?php

declare(strict_types=1);

namespace Hleb\Main\Insert\Examples;

/**
 * @see ExampleMirrorRequest
 */
class Head
{
    private $head = [];

    /**
     * @param array $list -  Adding test values to be returned in methods.
     *
     *                    -  Добавление тестовых значений, которые будут возвращены в методах.
     */
    public function __construct(array $list)
    {
        $this->head = $list;
    }

    public function addStyles(string $url)
    {
        return $this;
    }

    public function addScript(string $url, string $attr = 'defer', string $charset = '')
    {
        return $this;
    }

    public function setTitle(string $value)
    {
        return $this;
    }

    public function addMeta($data, $content = '')
    {
        return $this;
    }

    public function addMetaFromParts(array $list)
    {
        return $this;
    }

    public function setDescription(string $value)
    {
        return $this;
    }

    public function output(bool $print = true, int $indents = 2)
    {
        return $this->head['output'];
    }

}

