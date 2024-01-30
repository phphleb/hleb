<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\CommandInterface;
use Hleb\Static\Command;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class CommandForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(CommandInterface $mock): void
   {
       Command::replaceWithMock($mock);
   }

   /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Command::replaceWithMock(null);
    }
}
