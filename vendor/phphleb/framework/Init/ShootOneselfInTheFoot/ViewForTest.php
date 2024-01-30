<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\ViewInterface;
use Hleb\Static\View;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class ViewForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(ViewInterface $mock): void
   {
       View::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        View::replaceWithMock(null);
    }
}
