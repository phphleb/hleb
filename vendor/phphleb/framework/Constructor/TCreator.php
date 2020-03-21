<?php

declare(strict_types=1);

namespace Hleb\Constructor;

class TCreator
{
    private $HLEBTCreatorContentData = '';

    private $HLEBTCreatorTemplateData = [];

    private $HLEBTCreatorCacheTime = 0;

    function __construct($content, $data = [])
    {
        $this->HLEBTCreatorContentData = $content;

        $this->HLEBTCreatorTemplateData = $data;
    }

    public function include()
    {
        extract($this->HLEBTCreatorTemplateData);

        foreach($this->HLEBTCreatorTemplateData as $key => $value){
            if(!in_array($key ,['HLEBTCreatorContentData','HLEBTCreatorCacheTime', 'HLEBTCreatorTemplateData'])) {
                $this->$key = $value;
            }
        }

        require $this->HLEBTCreatorContentData;

        return $this->HLEBTCreatorCacheTime;

    }

    public function print()
    {
        echo $this->HLEBTCreatorContentData;

        return null;
    }

    /**
     * To set the caching time inside the template.
     *  ~ ... $this->setCacheTime(60); ...
     * @param int $seconds
     */
    public function setCacheTime(int $seconds)
    {
        $this->HLEBTCreatorCacheTime = $seconds;
    }
}

