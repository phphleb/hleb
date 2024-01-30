<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\RequestInterface;
use Hleb\Static\Request;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class RequestForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(RequestInterface $mock): void
   {
       Request::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Request::replaceWithMock(null);
    }
}
