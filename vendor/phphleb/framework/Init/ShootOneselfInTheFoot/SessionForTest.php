<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\SessionInterface;
use Hleb\Static\Session;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class SessionForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(SessionInterface $mock): void
   {
       Session::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Session::replaceWithMock(null);
    }
}
