<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\DbInterface;
use Hleb\Static\DB;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class DbForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(DbInterface $mock): void
   {
       DB::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        DB::replaceWithMock(null);
    }
}
