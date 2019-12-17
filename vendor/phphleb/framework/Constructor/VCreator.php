<?php

declare(strict_types=1);

namespace Hleb\Constructor;

class VCreator
{
    private $includeFile = '';

    function __construct(string $include)
   {
       $this->includeFile = $include;
   }

   public function view()
   {
       extract (hleb_to0me1cd6vo7gd_data());

      require $this->includeFile;

   }
}

