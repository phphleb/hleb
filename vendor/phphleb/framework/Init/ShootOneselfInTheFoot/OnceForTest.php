<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\OnceInterface;
use Hleb\Static\Once;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class OnceForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(OnceInterface $mock): void
   {
       Once::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Once::replaceWithMock(null);
    }
}
