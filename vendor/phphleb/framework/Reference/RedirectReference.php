<?php

/*declare(strict_types=1);*/

namespace Hleb\Reference;

use AsyncExitException;
use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Attributes\AvailableAsParent;
use Hleb\Main\Insert\ContainerUniqueItem;
use Hleb\Static\Response;
use Hleb\Static\Script;

#[Accessible] #[AvailableAsParent]
class RedirectReference extends ContainerUniqueItem implements RedirectInterface, Interface\Redirect
{
    /**
     * @inheritDoc
     *
     * @throws AsyncExitException
     */
    #[\Override]
    public function to(string $location, int $status = 302): void
    {
        Script::asyncExit('', $status, \array_merge(Response::getHeaders(), ['Location' => $location]));
    }
}
