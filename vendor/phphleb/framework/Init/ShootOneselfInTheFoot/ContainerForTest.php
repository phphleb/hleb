<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Constructor\Containers\TestContainerInterface;
use Hleb\Static\Container;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class ContainerForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(TestContainerInterface $mock): void
   {
       Container::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Container::replaceWithMock(null);
    }
}
