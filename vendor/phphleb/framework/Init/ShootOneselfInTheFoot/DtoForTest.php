<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\DtoInterface;
use Hleb\Static\Dto;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class DtoForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(DtoInterface $mock): void
   {
       Dto::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Dto::replaceWithMock(null);
    }
}
