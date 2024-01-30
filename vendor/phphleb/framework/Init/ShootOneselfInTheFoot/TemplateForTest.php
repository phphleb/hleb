<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\TemplateInterface;
use Hleb\Static\Template;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class TemplateForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(TemplateInterface $mock): void
   {
       Template::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Template::replaceWithMock(null);
    }
}
