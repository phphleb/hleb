<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\RouterInterface;
use Hleb\Static\Router;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class RouterForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(RouterInterface $mock): void
   {
       Router::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Router::replaceWithMock(null);
    }
}
