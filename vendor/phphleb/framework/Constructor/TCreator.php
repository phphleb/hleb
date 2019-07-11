<?php

declare(strict_types=1);

namespace Hleb\Constructor;

class TCreator
{
    private $contentData = "";

    private $teplateData = [];

    private $casheTime = 0;

    function __construct( $content, $data = [])
    {
        $this->contentData = $content;

        $this->teplateData = $data;
    }

    public function include()
    {
        extract($this->teplateData);

        require $this->contentData;

        return $this->casheTime;

    }

    public function print()
    {
        extract($this->teplateData);

        print $this->contentData;

        return null;
    }

    /**
     * To set the caching time inside the template.
     *  ~ ... $this->setCacheTime(60); ...
     * @param int $seconds
     */
    public function setCacheTime(int $seconds)
    {
        $this->casheTime = $seconds;
    }
}

