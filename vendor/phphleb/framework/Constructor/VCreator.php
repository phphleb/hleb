<?php

declare(strict_types=1);

namespace Hleb\Constructor;

class VCreator
{
    private $hlTemplateContent = '';

    function __construct(string $include)
    {
        $this->hlTemplateContent = $include;

        $data = hleb_to0me1cd6vo7gd_data();
        foreach ($data as $key => $value) {
            if(!in_array($key ,['hlTemplateContent', 'hlTemplateData', 'hlCacheTime'])) {
                $this->$key = $value;
            }
        }
    }

    public function includeTemplateName()
    {
        return $this->hlTemplateContent;
    }

    public function view()
    {
        extract(hleb_to0me1cd6vo7gd_data());

        require $this->includeTemplateName();

    }
}

