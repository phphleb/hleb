<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\ResponseInterface;
use Hleb\Static\Response;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class ResponseForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(ResponseInterface $mock): void
   {
       Response::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Response::replaceWithMock(null);
    }
}
