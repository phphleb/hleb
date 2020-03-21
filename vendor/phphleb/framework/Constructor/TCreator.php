<?php

declare(strict_types=1);

namespace Hleb\Constructor;

class TCreator
{
    private $hlTemplateContent = '';

    private $hlTemplateData = [];

    private $hlCacheTime = 0;

    function __construct($content, $data = [])
    {
        $this->hlTemplateContent = $content;

        $this->hlTemplateData = $data;
    }

    public function include()
    {
        extract($this->hlTemplateData);

        foreach($this->hlTemplateData as $key => $value){
            if(!in_array($key ,['hlTemplateContent', 'hlTemplateData', 'hlCacheTime'])) {
                $this->$key = $value;
            }
        }

        require $this->includeTemplateName();;

        return $this->hlCacheTime;

    }

    public function includeTemplateName()
    {
        return $this->hlTemplateContent;
    }

    public function print()
    {
        echo $this->hlTemplateContent;

        return null;
    }

    /**
     * To set the caching time inside the template.
     *  ~ ... $this->setCacheTime(60); ...
     * @param int $seconds
     */
    public function setCacheTime(int $seconds)
    {
        $this->hlCacheTime = $seconds;
    }
}

