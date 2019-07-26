<?php

declare(strict_types=1);

namespace Hleb\Main;

class OwnCachedTemplate extends CachedTemplate
{
    function hl_info_template_name(){
        return  'includeOwnCachedTemplate';
    }

    function hl_template_area_key(){
        return  session_id();
    }
}


