<?php

declare(strict_types=1);

namespace Hleb\Constructor\Cache;

class OwnCachedTemplate extends CachedTemplate
{
    function infoTemplateName(){
       return  'include<b>Own</b>CachedTemplate';
    }

    function templateAreaKey(){
        return  session_id();
    }
}


