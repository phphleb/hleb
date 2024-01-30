<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\CacheInterface;
use Hleb\Static\Cache;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class CacheForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(CacheInterface $mock): void
   {
       Cache::replaceWithMock($mock);
   }

   /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Cache::replaceWithMock(null);
    }
}
