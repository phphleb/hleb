<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\CookieInterface;
use Hleb\Static\Cookies;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class CookiesForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(CookieInterface $mock): void
   {
       Cookies::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Cookies::replaceWithMock(null);
    }
}
