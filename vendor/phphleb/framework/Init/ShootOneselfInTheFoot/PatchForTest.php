<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\PathInterface;
use Hleb\Static\Path;


/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class PatchForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(PathInterface $mock): void
   {
       Path::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Path::replaceWithMock(null);
    }
}
