<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\ArrInterface;
use Hleb\Static\Arr;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class ArrForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(ArrInterface $mock): void
   {
       Arr::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Arr::replaceWithMock(null);
    }

}
