<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use Hleb\Base\RollbackInterface;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Main\Insert\ContainerUniqueItem;

#[Accessible] #[AvailableAsParent]
class CsrfReference extends ContainerUniqueItem implements CsrfInterface, Interface\Csrf, RollbackInterface
{
    /**
     * @inheritDoc
     */
    #[\Override]
    public function token(): string
    {
        return \Hleb\Constructor\Protected\Csrf::key();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function field(): string
    {
        return '<input type="hidden" name="_token" value="' . $this->token() . '">';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function validate(?string $key): bool
    {
        return \Hleb\Constructor\Protected\Csrf::validate($key);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function discover(): string|null
    {
        return \Hleb\Constructor\Protected\Csrf::discover();
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function rollback(): void
    {
        \Hleb\Constructor\Protected\Csrf::rollback();
    }
}
