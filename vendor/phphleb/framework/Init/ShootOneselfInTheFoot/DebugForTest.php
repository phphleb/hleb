<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\DebugInterface;
use Hleb\Static\Debug;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class DebugForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(DebugInterface $mock): void
   {
       Debug::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Debug::replaceWithMock(null);
    }
}
