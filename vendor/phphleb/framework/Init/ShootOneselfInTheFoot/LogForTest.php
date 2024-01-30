<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\LogInterface;
use Hleb\Static\Log;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class LogForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(LogInterface $mock): void
   {
       Log::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Log::replaceWithMock(null);
    }
}
