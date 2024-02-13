<?php

/*declare(strict_types=1);*/

namespace Hleb\Main\Console\Commands\Deployer;

use Hleb\Constructor\Attributes\Accessible;
use Hleb\Constructor\Data\SystemSettings;

#[Accessible]
final class LibDeployerFinder
{
    /**
     * One easy way to determine if a library can be deployed.
     *
     * Единый простой способ определения, что библиотека может быть развёртываемой.
     */
    public function isExists(string $command): bool
    {
        return (bool)SystemSettings::getRealPath("@vendor/$command/updater.json");
    }
}
