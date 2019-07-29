<?php

declare(strict_types=1);

namespace Hleb\Constructor\Cache;

class OwnCachedTemplate extends CachedTemplate
{
    function hl_info_template_name(){
       return  'include<b>Own</b>CachedTemplate';
    }

    function hl_template_area_key(){
        return  session_id();
    }
}


