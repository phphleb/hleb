<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\RedirectInterface;
use Hleb\Static\Redirect;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class RedirectForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(RedirectInterface $mock): void
   {
       Redirect::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Redirect::replaceWithMock(null);
    }
}
