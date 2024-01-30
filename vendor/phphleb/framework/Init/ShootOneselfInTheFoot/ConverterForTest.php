<?php

declare(strict_types=1);

namespace Hleb\Init\ShootOneselfInTheFoot;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\ForTestOnly;
use Hleb\Reference\ConverterInterface;
use Hleb\Static\Converter;

/** @inheritDoc */
#[ForTestOnly] #[Accessible]
final class ConverterForTest extends BaseMockAddOn
{
    #[ForTestOnly]
   public static function set(ConverterInterface $mock): void
   {
       Converter::replaceWithMock($mock);
   }

    /** @inheritDoc */
    #[ForTestOnly]
    #[\Override]
    public static function cancel(): void
    {
        Converter::replaceWithMock(null);
    }
}
