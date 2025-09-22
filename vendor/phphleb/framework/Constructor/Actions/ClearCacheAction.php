<?php

/*declare(strict_types=1);*/

namespace Hleb\Constructor\Actions;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\CoreProcessException;
use Hleb\Reference\CacheReference;

#[Accessible]
final class ClearCacheAction implements ActionInterface
{
    /**
     * Clearing the entire framework cache.
     *
     * Очистка всего кеша фреймворка.
     */
    #[\Override]
   public function run(): void
   {
       if (!(new CacheReference())->clear()) {
           throw new CoreProcessException('Failed to clear cache.');
       }
   }
}
