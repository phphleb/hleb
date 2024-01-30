<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\ScriptInterface;
use Hleb\Static\Script;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class ScriptForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(ScriptInterface $mock): void
   {
       Script::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Script::replaceWithMock(null);
    }
}
