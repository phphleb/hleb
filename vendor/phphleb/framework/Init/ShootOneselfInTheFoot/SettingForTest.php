<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\SettingInterface;
use Hleb\Static\Settings;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class SettingForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(SettingInterface $mock): void
   {
       Settings::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Settings::replaceWithMock(null);
    }
}
