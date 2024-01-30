<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\DiInterface;
use Hleb\Static\DI;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class DiForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(DiInterface $mock): void
   {
       DI::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        DI::replaceWithMock(null);
    }
}
